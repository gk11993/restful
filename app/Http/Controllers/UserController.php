<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\User_bind;
use App\Models\User_union;


class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        //
       
        
        return DB::table('categories')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // $data = [
        //     'first_id' => 'wxid_2222',
        //     'first_unit' => 1,
        //     'secondary_id' => '22222',
        //     'secondary_unit' => 2,
        // ];
        $data = $request->all();
       
        $first = $this->getUser($data['first_unit'], $data['first_id']);
        $secondary = $this->getUser($data['secondary_unit'], $data['secondary_id']);
       
        if ( @$secondary->union_id && @$first->union_id ) {
            return $this->create(['secondary_id' => $data['secondary_id']], 'secondary had been bind');
        }
        $unionArr = User_union::whereIn("id", [ @$first['union_id'], @$secondary['union_id'] ])->first();
        //$unionArr = DB::table('user_unions')->

        if ( $unionArr ) {
            $unit = $this->getUnitName();
            $first_unit_name = $unit[$data['first_unit']];
            $secondary_unit_name = $unit[$data['secondary_unit']];

            if ( 
                @$first['union_id'] &&
                json_decode(json_encode($unionArr), true)[$secondary_unit_name.'_id']||
                @$secondary['union_id'] &&
                json_decode(json_encode($unionArr), true)[$first_unit_name.'_id']
            ) {
                return $this->create([], 'exist');
            }
        }

        return $this->create(User_bind::create([
                'first_id' => $first['user_id'],
                'first_unit' => $data['first_unit'],
                'first_table_id' => $first['id'],
                'secondary_id' => $secondary['user_id'],
                'secondary_unit' => $data['secondary_unit'],
                'secondary_table_id' => $secondary['id'],
                'key_code' => $this->createKeyCode(),
                'union_id' => $unionArr?$unionArr->id:0,
            ]), 'waiting. the max wait time for check is 24h');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //misbind
        // $data = [
        //     'user_id' => 'wxid_2222',
        //     'unit' => 1,
        // ];
        $data = $request->all();

        $user = User::where('user_id', $data['user_id'])
        ->where('unit_id', $data['unit'])
        ->where('union_id', '>', 0)
        ->first();
      
        if ( $user ) {
            $unit = $this->getUnitName();
            $unit_name = $unit[$data['unit']];
            User_union::find($user->union_id)
            ->update([$unit_name.'_id' => 0]);
            
            return $this->create(User::find($user->id)->update(['union_id' => 0, 'status' => 1]), 'success');
        } else {
            return $this->create([], 'error');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

     /**
     * post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkBind(Request $request)
    {
        //
       
        // $data = [
        //     'secondary_id' => '22222',
        //     'secondary_unit' => 2,
        //     'key_code' => 'e9f91e14c4bbeae01bf2e155ef14b5bc',
        // ];
        $data = $request->all();
        $bind = User_bind::where([
            'key_code' => $data['key_code'],
            'secondary_id' => $data['secondary_id'],
            'secondary_unit' => $data['secondary_unit'],
            'status' => 0,
        ])
        ->where('created_at', '>', date('Y-m-d H:i:s', time()-60*60*24))
        ->first();
        
        if ( $bind ) {
            $unit = $this->getUnitName();
            $first_unit_name = $unit[$bind->first_unit];
            $secondary_unit_name = $unit[$bind->secondary_unit];
           
             $union_id = User_union::updateOrCreate(
                ['id' => @$bind->union_id],
                [
                    $secondary_unit_name.'_id' => $bind->secondary_table_id,
                    $first_unit_name.'_id' => $bind->first_table_id,
                ],
            )->id;

            User::whereIn('id', [$bind->first_table_id, $bind->secondary_table_id])
            ->update(['union_id' => $union_id, 'status' => 1]);

            $bind->status = 1;
            $bind->union_id = $union_id;
            $bind->deleted_at = date('Y-m-d H:i:s');
            $bind->save();

            return $this->create(user::where("union_id", $union_id)->select('user_id')->pluck("user_id"), 'success');
        } else {
            return $this->create([], 'key invalid or key timeout or secondary mismatch');
        }
        
    }

    /**
     * post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addMoney(Request $request)
    {
        //
        // $data = [
        //     'user_id' => 'wxid_2222',
        //     'unit' => 1,
        //     'money' => 0.01,
        // ];
        $data = $request->all();

        $user = $this->getUser($data['unit'], $data['user_id']);
        $user->money += $data['money'];
        $user->save();

        DB::table('user_pay_records')->insert([
            'money' => $data['money'],
            'user_id' => $user->id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return $this->create($user, 'okay');
    }
     /**
     * post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function queryMoney(Request $request)
    {
        //
        // $data = [
        //     'user_id' => 'wxid_2222',
        //     'unit' => 1,
        // ];
        $data = $request->all();
        $user = $this->getUser($data['unit'], $data['user_id']);
        
        if ( @$user->union_id ) {
            $users = User::where('union_id', @$user->union_id)->get();
            $current_money = 0;
            $all_money = 0;
            foreach ( $users as $u  ) {
                if ( $u->unit_id == 1 ) {
                    $current_money += $u->money;
                }
                $all_money += $u->money;
            }
           
            return  $this->create(['money' => $current_money, 
                    "union_money" => $all_money
            ], 'okay');
        } else {
            return $this->create(['money' => $user->money, "union_money" => $user->money], 'okay');
        }

    }
    /**
     * post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function FreePlay(Request $request)
    {
        //
        // $data = [
        //     'user_id' => 'wxid_2222',
        //     'unit' => 1,
        //     'type' => [],
        // ];
        $data = $request->all();
        $config = DB::table('configs')->first();
        
        if ( $config->stop_wx_send_file &&  $data['unit'] == 1 ) {
           return $this->create(['unit' => 'wx'], 'stop send file');
        }

        $days = 60*60*24+strtotime(date('Y-m-d 00:00:00'))-60*60*24*$config->free_space;
        $user = $this->getUser($data['unit'], $data['user_id']);
        $user_ids[] = $user->id;
        if ( @$user->union_id ) {
            $user_ids = $this->getUserIds($user->union_id, $user->id);

            if ( $config->free_union ) {
                if ( 
                    User::where('union_id', @$user->union_id)
                    ->groupBy('union_id')
                    ->selectRaw('MAX(free_at) AS time, union_id')
                    ->where('free_at', '>', date('Y-m-d 00:00:00', $days))
                    ->first()
                 ) {
                    return $this->create([
                        "timed_at" => $user->free_at, 
                        "okay_timed_at" => date('Y-m-d 00:00:00', 60*60*24*$config->free_space+strtotime($user->free_at))
                    ], 'timeout');
                }
            }
        } else {
            if (
                $user->where([
                    'user_id' => $data['user_id'],
                    'unit_id' => $data['unit'],
                ])
                ->where('free_at', '>', date('Y-m-d 00:00:00', $days))->first() 
             ) {
                return $this->create([
                    "timed_at" => $user->free_at, 
                    "okay_timed_at" => date('Y-m-d 00:00:00', 60*60*24*$config->free_space+strtotime($user->free_at))
                ], 'timeout');
            }
        }

        $user->free_at = date('Y-m-d H:i:s');
        $user->status = 1;
        $user->save();
     
        return $this->create(['names' => DB::table('plays')->whereIn('id', $this->getPlay($config->free_count, $user_ids, $data['unit'], $data['type']))->pluck('file_name')
        ], 'okay');

    }
    /**
     * post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function play(Request $request)
    {
        //
        // $data = [
        //     'user_id' => 'wxid_2222',
        //     'unit' => 1,
        //     'type' => [],
        //     'count' => 2,
        //     'money' => 0,
        // ];
        $data = $request->all();
        $config = DB::table('configs')->first();
        if ( $config->stop_wx_send_file &&  $data['unit'] == 1 ) {
           return $this->create(['unit' => 'wx'], 'stop send file');
        }
        $user = $this->getUser($data['unit'], $data['user_id']);
        $user_ids[] = $user->id;
        $user_money = @$user->money;
        if ( @$user->union_id ) {
            $user_ids = $this->getUserIds($user->union_id, $user->id, $user_money);
        }
      
        if ( $user_money < $data['money'] ) {
            return $this->create(["money" => (string)$user_money], 'money less than');
        }

        if ( $this->subMoney($user_ids, $data['money']) ) {
            return $this->create(['names' => DB::table('plays')->whereIn('id', $this->getPlay($data['count'], $user_ids, $data['unit'], $data['type']))->pluck('file_name')], 'okay');
        } else {
            return $this->create(["money" => (string)$user_money], 'money problem');
        }

    }


}
