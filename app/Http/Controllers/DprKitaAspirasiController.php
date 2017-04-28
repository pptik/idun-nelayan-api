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

class DprKitaAspirasiController extends Controller
{
    public function store(Request $request)
    {
        $dt = Carbon::now();

        $param = array(
            'aspirasi' => $request->input('aspirasi'),
            'tgl_aspirasi' => $request->input('tgl_aspirasi'),
            'id_user' => $request->input('id_user'),
            'id_komisi' => $request->input('id_komisi'),
            'img_aspirasi' => $request->input('img_aspirasi'),
            'dukungan' => "0"
        );
            $result = DB::table('aspirasi')->insert($param);
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
    public function showallaspirasi()
    {
        $result = DB::table('aspirasi')

            ->get();
        $result2 = DB::table('dk_aspirasi')
            ->get();
        if ($result) {
            return response()->json(
                array(
                    'aspirasi' => $result,
                    'dukungan' =>$result2,
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
    public function update(Request $request, $id_aspirasi)
    {
        $dt = Carbon::now();
        $param = array(
            'aspirasi' => $request->input('aspirasi'),
            'tgl_aspirasi' => $request->input('tgl_aspirasi'),
            'id_komisi' => $request->input('id_komisi'),
            'img_aspirasi' => $request->input('img_aspirasi')
        );

        $result = DB::table('aspirasi')
            ->where('id_aspirasi', $id_aspirasi)
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
    public function delete($id_aspirasi)
    {
        $dt = Carbon::now();


        $result = DB::table('aspirasi')
            ->where('id_aspirasi', $id_aspirasi)
            ->delete();
        $result2 = DB::table('dk_aspirasi')
            ->where('id_aspirasi', $id_aspirasi)
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
    public function showbyidaspirasi($id_aspirasi)
    {
        $dt = Carbon::now();


        $result = DB::table('berita')
            ->where('id_berita', $id_aspirasi)
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


        $result = DB::table('aspirasi')
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
    public function ubahstatusdukungan($id_user,$id_aspirasi)
    {
        $dt = Carbon::now();

        $hasilcheck=$this->checkstatusdukungan($id_user,$id_aspirasi);

        if ($hasilcheck==1){

            $param = array(
                'status' => 0
            );

            $result = DB::table('dk_aspirasi')
                ->where('id_aspirasi', $id_aspirasi)
                ->where('id_user', $id_user)
                ->update($param);
            $this->kurangidukungan($id_aspirasi);
        }elseif ($hasilcheck==0){
            $param = array(
                'status' => 1
            );

            $result = DB::table('dk_aspirasi')
                ->where('id_aspirasi', $id_aspirasi)
                ->where('id_user', $id_user)
                ->update($param);
            $this->tambahkandukungan($id_aspirasi);

        }else{
            $param = array(
                'id_aspirasi' => $id_aspirasi,
                'id_user' => $id_user,
                'status' => 1,
            );
            $result = DB::table('dk_aspirasi')->insert($param);
            $this->tambahkandukungan($id_aspirasi);

        }
    }
    public function checkstatusdukungan($id_user,$id_aspirasi)
    {
        $dt = Carbon::now();

        $result=DB::table('dk_aspirasi')
            ->where('id_user', $id_user)
            ->where('id_aspirasi', $id_aspirasi)
            ->get();
        if($result){
            if ($result->status == 1){
                return 1;
            }else{
                return 0;
            }

        }else{
            return false;
        }
    }
    public function tambahkandukungan($id_aspirasi){
        $result=DB::table('aspirasi')
            ->where('id_aspirasi', $id_aspirasi)
            ->select('dukungan');
        $result=$result+1;
        $param = array(
            'dukungan' => $result
        );

        $result = DB::table('aspirasi')
            ->where('id_aspirasi', $id_aspirasi)
            ->update($param);
    }
    public function kurangidukungan($id_aspirasi){
        $result=DB::table('aspirasi')
            ->where('id_aspirasi', $id_aspirasi)
            ->select('dukungan');
        $result=$result-1;
        $param = array(
            'dukungan' => $result
        );

        $result = DB::table('aspirasi')
            ->where('id_aspirasi', $id_aspirasi)
            ->update($param);
    }
}
