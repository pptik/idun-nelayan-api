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

class ACTUserController extends Controller
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
	
	public function approverelawan($id_user,$no)
    {
		$dt = Carbon::now();
        $param = array(
			'level'=>$no
        );
        $result = DB::table('act_user')
            ->where('id_user', $id_user)
            ->update($param);
			$greetMsg = "hai";
			$respJson = '{"greetMsg":"'.$greetMsg.'","for":"1"}';
			$message = array("m" => $respJson);
		
        if($result){
            return response()->json(
                array('status' => true,
					'notif' => $this->notiftorelawan($message),
                    'msg' => 'data berhasil terupdate!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan saat update!'), 200);
        }
    }
	public function notiftorelawan($message){
		$result = DB::table('act_user')
				->where('level','=',2)
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
	public function listsudahapprove()
    {
        $result = DB::table('act_user')
				->where('persetujuan','=',1)
				->where('level','=',2)
				->select('id_user','nama_lengkap','email')
				->get();
			
        if ($result) {
            return response()->json(
                array(
                    'status' => true,
					'listuser'=>$result,
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
        $result = DB::table('act_user')
				->where('persetujuan','=',1)
				->where('level','=',3)
				->select('id_user','nama_lengkap','email')
				->get();
			
        if ($result) {
            return response()->json(
                array(
                    'status' => true,
					'listuser'=>$result,
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
	public function statusdaftar($id_user)
    {
        $result = DB::table('act_user')
				->where('id_user','=',$id_user)
				->select('persetujuan')
				->get();
		$persetujuan=0;
		
		foreach($result as $result1){
			$persetujuan=$result1->persetujuan;
		}
				
        if ($persetujuan==1) {
            return response()->json(
                array(
                    'status' => true,
                    'msg' => 'Sudah Mendaftar'
                ), 200);
        }
        else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'Belum Mendaftar'
                ), 200);
        }
    }
	
	public function profileshowbyid($id_user)
    {
        $result = DB::table('act_user')
				->where('id_user',$id_user)
				->get();
        if ($result) {
            return response()->json(
                array(
                    'user' => $result,
                    'status' => true,
                    'msg' => 'Data berhasil ditampilkan'
                ), 200);
        }
        else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'Terjadi Kesalahan'
                ), 200);
        }
    }
	public function storerelawan(Request $request,$email)
    {
	
	
        $dt = Carbon::now();
      
        $param = array(
            'nama_lengkap' => $request->input('nama_lengkap'),
			'nama_panggilan' =>$request->input('nama_panggilan'),
			'jk' =>$request->input('jk'),
			'gol_darah' =>$request->input('gol_darah'),
			'tempat_lahir' =>$request->input('tempat_lahir'),
			'tgl_lahir' =>$request->input('tgl_lahir'),
			'agama' =>$request->input('agama'),
			'status_perkawinan' =>$request->input('status_perkawinan'),
			'jumlah_anak' =>$request->input('jumlah_anak'),
			'jenis_identitas' =>$request->input('jenis_identitas'),
			'no_identitas' =>$request->input('no_identitas'),
			'kewarganegaraan' =>$request->input('kewarganegaraan'),
			'alamat' =>$request->input('alamat'),
			'kota' =>$request->input('kota'),
			'provinsi' =>$request->input('provinsi'),
			'kode_pos' =>$request->input('kode_pos'),
			'telp_rumah' =>$request->input('telp_rumah'),
			'hp' =>$request->input('hp'),
			'pekerjaan' =>$request->input('pekerjaan'),
			'nama_kerabat' =>$request->input('nama_kerabat'),
			'hp_kerabat' =>$request->input('hp_kerabat'),
			'pendidikan_terakhir' =>$request->input('pendidikan_terakhir'),
			'minat' =>$request->input('minat'),
			'keahlian' =>$request->input('keahlian'),
			'pengalaman_organisasi' =>$request->input('pengalaman_organisasi'),
			'motivasi' =>$request->input('motivasi'),
			'level'=>3,
			'persetujuan'=>1,
					
        );
		

        $result = DB::table('act_user')
            ->where('email', $email)
            ->update($param);
			$greetMsg = "hai";
			$respJson = '{"greetMsg":"'.$greetMsg.'","for":"2","judul":"'.$request->input('nama_lengkap').'"}';
			$message = array("m" => $respJson);
			//Google cloud messaging GCM-API url
			
        if($result){
            return response()->json(
                array('status' => true,
				'notif' => $this->notiftoadmin($message),
                    'msg' => 'data berhasil terupdate!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan saat update!'), 200);
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
    public function showall()
    {
        $result = DB::table('act_user')->get();
        if ($result) {
            return response()->json(
                array(
                    'user' => $result,
                    'status' => true,
                    'msg' => 'Data berhasil ditampilkan'
                ), 200);
        }
        else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'Terjadi Kesalahan'
                ), 200);
        }
    }
    public function store(Request $request)
    {
        $dt = Carbon::now();
        $param = array(
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'gcm_id' => $request->input('gcm_id'),
            'created_at'=>$dt->toDateTimeString()
        );

        $cek_availability = DB::table('act_user')->where('email', '=', $request->input('email'))->get();
        if(!$cek_availability){
            $result = DB::table('act_user')->insert($param);
            if($result){
                $user = DB::table('act_user')
                    ->where('email', '=', $request->input('email'))
                    ->first();

                return response()->json(
                    array(
                        'user' => $user,
                        'verifiedprogram' => $this->showverifiedprogram(),
                        'next'=>'/beritaact/showbagianberita/5',
                        'status' => true,
                        'msg' => 'data berhasil disimpan!'
                    ), 200);
            }else{
                return response()->json(
                    array('status' => false,
                        'msg' => 'terjadi kesalahan!'), 200);
            }
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'email sudah digunakan'), 200);
        }
    }
    public function showverifiedprogram()
    {
        $result=DB::table('act_program')
                ->orderby('mulai','desc')
                ->take(5)
                ->get();
        return $result;
    }

    public function authenticate(Request $request){
        $user = DB::table('act_user')
            ->where('email', '=', $request->input('email'))
            ->get();
		
		$iduser=null;
		$password=null;
		foreach($user as $user1){
			$iduser=$user1->id_user;
			$password=$user1->password;
		}
		$hasilcheck=$this->updategcmid($iduser,$request->input('gcm_id'));
        if($user){
            if (Hash::check($request->input('password'), $password)) {
					 $user2 = DB::table('act_user')
						->where('id_user', '=', $iduser)
						->first();
                return response()->json(
                    array(
                        'user' => $user2,
						'updategcmid'=>$hasilcheck,
                        'verifiedprogram' => $this->showverifiedprogram(),
                        'next'=>'/beritaact/showbagianberita/5',
                        'status' => true,
                        'msg' => 'data berhasil disimpan!'
                    ), 200);
            }
            else{
                return response()->json(
                    array(
                        'status' => false,
                        'msg' => 'gagal login, password salah'
                    ), 200);
            }
        }else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'gagal login, email tidak ditemukan'
                ), 200);
        }
    }
	
	public function updategcmid($userid,$gcmid){
		 $dt = Carbon::now();
        $param = array(
            'gcm_id' => $gcmid
        );

        $result = DB::table('act_user')
            ->where('id_user', $userid)
            ->update($param);

        if($result){
            return true;
        }else{
            return false;
        }
	}
}
