<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class KelasController extends Controller
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
        $cek_code = DB::table('kelas')
            ->where('kode', '=', $request->input('kode'))
            ->first();
        if($cek_code){
            $cek_siswa = DB::table('kelas')
                    ->where('kode', '=', $request->input('kode'))
                    ->where('id_siswa','=', $request->input('id_siswa'))
                    ->first();    
            if(!$cek_siswa)
                {
                  $param = array(
                    'id_pengawas' => $cek_code->id_pengawas,
                    'id_siswa' => $request->input('id_siswa'),
                    'id_materi' => $cek_code->id_materi,
                    'nama_kelas' => $cek_code->nama_kelas,
                    'kode' => $cek_code->kode,
                    'created_at' => $dt->format('Y-m-d H:i:s'),
                    'updated_at' => $dt->format('Y-m-d H:i:s')
                  );

                $store = DB::table('kelas')->insert($param);    
                if($store){
                    return response()->json(
                    array('status' => true,
                          'msg' => 'data berhasil insert!'
                         ), 200);
                }    
                else{
                    return response()->json(
                    array('status' => false,
                          'msg' => 'Gagal insert data'), 200);
                }
            }else{
                 return response()->json(
                    array('status' => false,
                          'msg' => 'Sudah terdaftar'), 200);
            }
        }else{
             return response()->json(
                array('status' => false,
                      'msg' => 'Kelas tidak ditemukan'), 200);
        }
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_user)
    {
        $result = DB::table('kelas')->where('id_siswa', '=', $id_user)->get();
         if ($result) {
               return response()->json(
                array(
                    'kelas' => $result,
                    'status' => true,
                    'msg' => 'berhasil'
                ), 200);
            }
            else{
                return response()->json(
                array(
                    'status' => false,
                    'msg' => 'tidak ditemukan kelas'
                ), 200);
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
