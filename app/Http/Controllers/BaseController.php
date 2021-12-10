<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class BaseController extends Controller
{
    protected function create($data=[], $msg='', $code ="200")
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        return Response($result);
    }
    protected function getUser($unit, $user_id)
    {
        $user = User::where([
            'unit_id' => $unit,
            'user_id' => $user_id,
        ])->first();
        
        return $user ? $user : User::create([
            'unit_id' => $unit, 
            'user_id' => $user_id
        ]);
    }
    protected function createKeyCode()
    {
        return md5(mt_rand());
    }
    protected function getUnitName()
    {
        return DB::table('user_unit')->pluck('name', 'id');
    }
    protected function getPlay($count, $user_ids, $unit, $types=[])
    {
        if ( !count($types) ) { //all for types
            $types = DB::table('categories')->pluck('id')->toArray();
        }
    
        if ($unit == 1) {
            $result = DB::table('user_play_records')
            ->whereNull('user_play_records.play_id')
            ->rightJoinSub(
                DB::table('plays')
                ->groupBy('play_id')
                ->selectRaw('play_id as p_id')
    //            ->where('plays.status', 0)
                ->whereIn('category_id', $types)
                ->join('play_category', 'play_id', '=', 'plays.id')
                , 'new_plays', function ($join) use ($user_ids) {
                $join->whereIn('user_id',  $user_ids);
                $join->on('new_plays.p_id', '=', 'user_play_records.play_id');
            })
            ->inRandomOrder()
            ->limit($count)
            ->pluck('p_id')->toArray();
           
            $this->addPlayRecord($user_ids[0], $result);
            return $result;
        } else {
            $result = DB::table('user_play_records')
            ->whereNull('user_play_records.play_id')
            ->rightJoinSub(
                DB::table('plays')
                ->groupBy('play_id')
                ->selectRaw('play_id as p_id')
                ->where('plays.status', 0)
                ->whereIn('category_id', $types)
                ->join('play_category', 'play_id', '=', 'plays.id')
                , 'new_plays', function ($join) use ($user_ids) {
                $join->whereIn('user_id',  $user_ids);
                $join->on('new_plays.p_id', '=', 'user_play_records.play_id');
            })
            ->inRandomOrder()
            ->limit($count)
            ->pluck('p_id')->toArray();
           
            $this->addPlayRecord($user_ids[0], $result);
            return $result;
        }


       
    }
    private function addPlayRecord($user_id, $data)
    {
        $insertData = [];
        foreach ($data as $key => $value) {
            $insertData[] = [
                'user_id' => $user_id,
                'play_id' => $value,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        DB::table('user_play_records')->insert($insertData);
    }
    protected function subMoney($user_ids, $money)
    {
        foreach ($user_ids as $key => $value) {
            $user = User::find($value);
            if ( $user->money >= $money ) {
                $user->money -= $money;
                $user->save();
                $money = 0;
                break;
            } else {
                $user->money = 0;
                $user->save();
                $money -= $user->money;
            }
        }
        
        return $money == 0;
    }
    protected function getUserIds($union_id, $user_id, &$allmoney=0)
    {
        $user = User::where('union_id', $union_id);
        $user_ids =  $u->pluck('id')->toArray();
        array_splice($user_ids, array_search($user_id, $user_ids), 1);
        array_unshift($user_ids, $user_id);
        $allmoney = $user->selectRaw('SUM(money) AS money')
        ->first()->money;
        return $user_ids;
    }
}
