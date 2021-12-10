<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Play;
use Illuminate\Support\Str;


class PlayController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return Str::plural('user');
        //DB::table('table2')->union(DB::table('table1'))
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
        //     'str' => '1----hello----6-5-13'
        // ];
        $data = $request;
        $arr = explode("----",$data['str']);
      
        $play = new Play;
        $play->name = $arr[1];
        $play->file_name = $arr[0];
        $play->save();

        $types = explode("-", $arr[2]);
        $insertDatas = [];
      
        foreach ($types as $key => $value)
            $insertDatas[] = ['play_id' => $play->id, 'category_id' => (int)$value];
       
        DB::table('play_category')->insert($insertDatas);
        return $this->create($play, 'success', 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //get categories
        $list = DB::table('plays')
            ->select(['categories.title', 'plays.name', 'plays.file_name'])
            ->join('play_category', 'play_category.play_id', '=','plays.id')
            ->join('categories', 'categories.id', '=','play_category.category_id')
            //->where('play_category.play_id', '=', $id)
            ->where('play_category.category_id', '=', $id)
            ->get();
        return $this->create($list);
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
        //
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
}
