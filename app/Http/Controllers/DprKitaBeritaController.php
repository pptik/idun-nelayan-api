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

class DprKitaBeritaController extends Controller
{
    public function store(Request $request)
    {
        $dt = Carbon::now();

        $param = array(
            'id_person' => $request->input('id_person'),
            'id_user' => $request->input('id_user'),
            'des_kegiatan' => $request->input('des_kegiatan'),
            'tgl_berita' => $request->input('tgl_berita'),
            'source' => $request->input('source'),
            'dukungan' => "0"
        );
            $result = DB::table('berita')->insert($param);
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
    public function showallberita()
    {
        $result = DB::table('berita')
            ->orderby('tgl_berita','desc')
            ->get();

        $result2 = DB::table('dk_berita')
            ->get();        
        
        if ($result) {
            return response()->json(
                array(
                    'berita' => $result,
                    'dukungan' => $result2,
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
    public function update(Request $request, $id_berita)
    {
        $dt = Carbon::now();
        $param = array(
            'id_person' => $request->input('id_person'),
            'des_kegiatan' => $request->input('des_kegiatan'),
            'source' => $request->input('source'),
            'tgl_berita' => $request->input('tgl_berita')
        );

        $result = DB::table('berita')
            ->where('id_berita', $id_berita)
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
    public function delete($id_berita)
    {
        $dt = Carbon::now();


        $result = DB::table('berita')
            ->where('id_berita', $id_berita)
            ->delete();
        $result2 = DB::table('dk_berita')
            ->where('id_berita', $id_berita)
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
    public function showbyidberita($id_berita)
    {
        $dt = Carbon::now();


        $result = DB::table('berita')
            ->where('id_berita', $id_berita)
            ->orderby('tgl_berita','desc')
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


        $result = DB::table('berita')
            ->where('id_user', $id_user)
            ->orderby('tgl_berita','desc')
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

    public function ubahstatusdukungan($id_user,$id_berita)
    {
        $dt = Carbon::now();

        $hasilcheck=$this->checkstatusdukungan($id_user,$id_berita);

        if ($hasilcheck==1){

            $param = array(
                'status' => 0
            );

            $result = DB::table('dk_berita')
                ->where('id_berita', $id_berita)
                ->where('id_user', $id_user)
                ->update($param);
            $this->kurangidukungan($id_berita);
        }elseif ($hasilcheck==0){
            $param = array(
                'status' => 1
            );

            $result = DB::table('dk_berita')
                ->where('id_berita', $id_berita)
                ->where('id_user', $id_user)
                ->update($param);
            $this->tambahkandukungan($id_berita);

        }else{
            $param = array(
                'id_berita' => $id_berita,
                'id_user' => $id_user,
                'status' => 1,
            );
            $result = DB::table('dk_berita')->insert($param);
            $this->tambahkandukungan($id_berita);

        }
    }
    public function checkstatusdukungan($id_user,$id_berita)
    {
        $dt = Carbon::now();

        $result=DB::table('dk_berita')
            ->where('id_user', $id_user)
            ->where('id_berita', $id_berita)
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
    public function tambahkandukungan($id_berita){
        $result=DB::table('berita')
            ->where('id_berita', $id_berita)
            ->select('dukungan');
        $result=$result+1;
        $param = array(
            'dukungan' => $result
        );

        $result = DB::table('berita')
            ->where('id_berita', $id_berita)
            ->update($param);
    }
    public function kurangidukungan($id_berita){
        $result=DB::table('berita')
            ->where('id_berita', $id_berita)
            ->select('dukungan');
        $result=$result-1;
        $param = array(
            'dukungan' => $result
        );

        $result = DB::table('berita')
            ->where('id_berita', $id_berita)
            ->update($param);
    }
}
