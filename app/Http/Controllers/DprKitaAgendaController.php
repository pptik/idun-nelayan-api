<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use TymonJWTAuthExceptionsJWTException;
use DB;
use Carbon\Carbon;
use Hash;

class DprKitaAgendaController extends Controller
{
    public function store(Request $request)
    {
        $dt = Carbon::now();

        $param = array(
            'program' => $request->input('program'),
            'tgl_program' => $request->input('tgl_program'),
            'id_user' => $request->input('id_user'),
            'id_komisi' => $request->input('id_komisi'),
            'time' => $request->input('time')
        );
            $result = DB::table('program')->insert($param);
            if($result){
                return response()->json(
                    array(
                        'status' => true,
                        'msg' => 'data berhasil insert!'
                    ), 200);
            }else{
                return response()->json(
                    array('status' => false,
                        'msg' => 'terjadi kesalahan saat insert!'), 200);
            }
    }
    public function showallagenda()
    {
        $result = DB::table('program')

            ->get();
        if ($result) {
            return response()->json(
                array(
                    'aspirasi' => $result,
                    'status' => true,
                    'msg' => 'berhasil'
                ), 200);
        }
        else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'gagal'
                ), 200);
        }
    }
    public function update(Request $request, $id_program)
    {
        $dt = Carbon::now();
        $param = array(
            'program' => $request->input('program'),
            'tgl_program' => $request->input('tgl_program'),
            'id_komisi' => $request->input('id_komisi'),
            'time' => $request->input('time')
        );

        $result = DB::table('program')
            ->where('id_program', $id_program)
            ->update($param);

        if($result){
            return response()->json(
                array('status' => true,
                    'msg' => 'data berhasil terupdate!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan saat update!'), 200);
        }
    }
    public function delete($id_program)
    {
        $dt = Carbon::now();


        $result = DB::table('program')
            ->where('id_program', $id_program)
            ->delete();

        if($result){
            return response()->json(
                array('status' => true,
                    'msg' => 'data berhasil dihapus!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan saat menghapus!'), 200);
        }
    }
    public function showbyidprogram($id_program)
    {
        $dt = Carbon::now();


        $result = DB::table('program')
            ->where('id_program', $id_program)
            ->get();

        if($result){
            return response()->json(
                array('status' => true,
                    'result' => $result,
                    'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan!'), 200);
        }
    }

    public function showbyiduser($id_user)
    {
        $dt = Carbon::now();


        $result = DB::table('program')
            ->where('id_user', $id_user)
            ->get();

        if($result){
            return response()->json(
                array('status' => true,
                    'result' => $result,
                    'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan!'), 200);
        }
    }
   
}
