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

class DprKitaUserController extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        //$this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function showbyid($id_user)
    {
        $dt = Carbon::now();


        $result = DB::table('act_user')
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
    public function update(Request $request, $id_user)
    {
        $dt = Carbon::now();
        $param = array(
            'nama'=> $request->input('nama'),
            'no_identitas'=> $request->input('no_identitas'),
            'level'=> 2,
            'image_path'=> $request->input('image_path'),
            'tempat_lahir'=> $request->input('tempat_lahir'),
            'tgl_lahir'=> $request->input('tgl_lahir'),
            'jumlah_jam_volunteer'=> $request->input('jumlah_jam_volunteer'),
            'keahlian'=> $request->input('keahlian'),
            'bahasa'=> $request->input('bahasa'),
            'hp'=> $request->input('hp'),
            'jk'=> $request->input('jk'),
            'alamat'=> $request->input('alamat'),
            'agama'=> $request->input('agama'),
            'pekerjaan'=> $request->input('pekerjaan'),
            'status_perkawinan'=> $request->input('status_perkawinan'),
            'pendidikan_terakhir'=> $request->input('pendidikan_terakihr'),
            'kewarganegaraan'=> $request->input('kewarganegaraan'),
            'updated_at'=> $dt->toDateTimeString()
        );

        $result = DB::table('act_user')
            ->where('id_user', $id_user)
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

}
