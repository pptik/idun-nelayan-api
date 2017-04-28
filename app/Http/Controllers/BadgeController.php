<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class BadgeController extends Controller
{
    public function all(){
        $badge = DB::table('badge')->get();
        if($badge){
            return response()->json(
			$badge, 200);
        }else{
            return response()->json(
			array('msg' => "doesnt exist!"), 200);
        }
    }
    
    
    public function byIdBadge($id_badge){
        $badge = DB::table('badge')->where('id_badge','=', $id_badge)->get();
        if($badge){
            return response()->json(
			$badge, 200);
        }else{
            return response()->json(
			array('msg' => "doesnt exist!"), 200);
        }
    }
    
    public function byIdLevel($id_level){
        $badge = DB::table('badge')->where('id_level','=', $id_level)->get();
        if($badge){
            return response()->json(
			$badge, 200);
        }else{
            return response()->json(
			array('msg' => "doesnt exist!"), 200);
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $materi = DB::table('materi')->get();
        
        if($materi){
            return response()->json(
			$materi, 200);
        }else{
            return response()->json(
			"doesnt exist!", 200);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $materi = DB::table('materi')
            ->where('id_materi','=',$id)->get();
                    
                
       if($materi){
            return response()->json(
			$materi, 200);
        }else{
            return response()->json(
			array('msg' => 'data tidak ditemukan!'), 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
