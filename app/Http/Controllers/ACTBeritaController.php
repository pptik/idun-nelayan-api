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

class ACTBeritaController extends Controller
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
	public function deleteberitabyid($id_program)
    {
		 $dt = Carbon::now();
      
        $result = DB::table('act_program')
            ->where('id_program', $id_program)
            ->delete();

        if($result){
            return response()->json(
                array('status' => true,
                    'msg' => 'data berhasil dihapus!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan!'), 200);
        }
    }
	public function approveberita($id_program,$no)
    {
		 $dt = Carbon::now();
      
        $param = array(
			'status'=>$no
        );

        $result = DB::table('act_program')
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
	public function beritabyid($id_berita){
        $result=DB::table('act_program')
            ->where('id_program','=',$id_berita)
            ->get();
        if ($result){
            return response()->json(
                array(
                    'berita' => $result,
                    'status' => true,
                    'msg' => 'data berhasil disimpan!'
                ), 200);
        }else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'data gagal diambil!'
                ), 200);
        }

    }
	public function beritabyiduser($id_user){
        $result=DB::table('act_program')
            ->where('id_user','=',$id_user)
            ->get();
        if ($result){
            return response()->json(
                array(
                    'berita' => $result,
                    'status' => true,
                    'msg' => 'data berhasil diambil!'
                ), 200);
        }else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'data gagal diambil!'
                ), 200);
        }

    }
	public function listsudahapprove()
    {
        $result = DB::table('act_program')
				->where('status','=',1)
				->select('id_program','nama_program','lokasi_program')
				->get();
			
        if ($result) {
            return response()->json(
                array(
                    'status' => true,
					'listprogram'=>$result,
                    'msg' => 'Data Berhasil Diambil'
                ), 200);
        }
        else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'Data Gagal Diambil'
                ), 200);
        }
    }
	
	public function listbelumapprove()
    {
        $result = DB::table('act_program')
				->where('status','=',2)
				->select('id_program','nama_program','lokasi_program')
				->get();
			
        if ($result) {
            return response()->json(
                array(
                    'status' => true,
					'listprogram'=>$result,
                    'msg' => 'Data Berhasil Diambil'
                ), 200);
        }
        else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'Data Gagal Diambil'
                ), 200);
        }
    }
	
	public function store(Request $request)
    {
        $dt = Carbon::now();

        $param = array(
			'id_user' => $request->input('id_user'),
            'nama_program' => $request->input('nama_program'),
            'lokasi_program' => $request->input('lokasi_program'),
            'mulai' => $request->input('mulai'),
			'akhir' => $request->input('akhir'),
			'supervisor' => $request->input('supervisor'),
			'deskripsi' => $request->input('deskripsi'),
			'keterangan' => $request->input('keterangan'),
			'latitude' => $request->input('latitude'),
			'longitude' => $request->input('longitude')
        );
            $result = DB::table('act_program')->insert($param);
			$greetMsg = "hai";
			$respJson = '{"greetMsg":"'.$greetMsg.'","for":"2","judul":"'.$request->input('nama_program').'"}';
			$message = array("m" => $respJson);
            if($result){
                return response()->json(
                    array(
                        'status' => true,
						'notif' => $this->notiftoadmin($message),
                        'msg' => 'data berhasil insert!'
                    ), 200);
            }else{
                return response()->json(
                    array('status' => false,
                        'msg' => 'terjadi kesalahan saat insert!'), 200);
            }
    }
	public function notiftoadmin($message){
		$result = DB::table('act_user')
				->where('level','=',1)
				->select('gcm_id')
				->get();
		$gcm_id=null;
		$regid=array();
		foreach($result as $result1){
			array_push($regid, $result1->gcm_id);	
		}
		
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $regid,
            'data' => $message,
        );
		// Update your Google Cloud Messaging API Key
		if (!defined('GOOGLE_API_KEY')) {
			define("GOOGLE_API_KEY", "AIzaSyDFVG5x8zo1blEqSGq95fAt80-uOTuonPQ"); 		
		}
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
		
        return $result;
	}
    public function showbagianberita($i){
        $result=DB::table('act_program')
            ->orderby('mulai','desc')
            ->skip($i)
            ->take(5)
            ->get();
        $i=$i+5;
        if ($result){
            return response()->json(
                array(
                    'berita' => $result,
                    'next'=>'/beritaact/showbagianberita/'.$i.'',
                    'status' => true,
                    'msg' => 'data berhasil disimpan!'
                ), 200);
        }else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'data gagal diambil!'
                ), 200);
        }

    }
	public function showstart(){
        $result=DB::table('act_program')
            ->orderby('mulai','desc')
            ->where('status', 1)
            ->get();
        if ($result){
            return response()->json(
                array(
                    'berita' => $result,
                    'next'=>'/beritaact/showbagianberita/5',
                    'status' => true,
                    'msg' => 'data berhasil diambil!'
                ), 200);
        }else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'data gagal diambil!'
                ), 200);
        }

    }
	public function showallverified(){
        $result=DB::table('act_program')
            ->orderby('mulai','desc')
            ->where('status', 1)
            ->get();
        if ($result){
            return response()->json(
                array(
                    'berita' => $result,
                    'status' => true,
                    'msg' => 'data berhasil diambil!'
                ), 200);
        }else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'data gagal diambil!'
                ), 200);
        }

    }
}
