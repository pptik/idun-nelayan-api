<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class KomentarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $dt = Carbon::now();
        
        $param = array(
            'id_materi' => $request->input('id_materi'),
            'id_top_komentar' => $request->input('id_top_komentar'),
            'isi_komentar' => $request->input('isi_komentar'),
            'id_user' => $request->input('id_user'),
            'created_at' => $dt->format('Y-m-d H:i:s'),
            'updated_at' => $dt->format('Y-m-d H:i:s')
        );
        
        $result = DB::table('komentar')->insert($param);
        if($result){
            return response()->json(
			array('msg' => 'data berhasil insert!'), 200);
        }else{
            return response()->json(
			array('msg' => 'terjadi kesalahan saat insert!'), 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_materi)
    {
        $komentar = DB::table('komentar')
            ->where('id_materi','=',$id_materi)->get();
                    
                
       if($komentar){
            return response()->json(
			$komentar, 200);
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
    public function update(Request $request)
    {
        $dt = Carbon::now();
        //echo $dt->format('Y-m-d H:i:s');
        
        $param = array(
            'isi_komentar' => $request->input('isi_komentar'),
            'updated_at' => $dt->format('Y-m-d H:i:s')
        );
        
        $result = DB::table('komentar')
            ->where('id_komentar', '=', $request->input('id_komentar'))
            ->update($param);
        
        if($result){
            return response()->json(
			array('msg' => 'berhasil update'), 200);
        }else{
            return response()->json(
			array('msg' => 'gagal update'), 200);
        }
    }

    public function destroy($id)
    {
        //
    }
    
    public function delete($id_komentar)
    {
           $result = DB::table('komentar')
           ->where('id_komentar','=',$id_komentar)->delete();
        
        if($result){
            return response()->json(
			array('msg' => 'data terhapus'), 200);
        }else{
            return response()->json(
			array('msg' => 'data gagal terhapus'), 200);
        }
    }
}
