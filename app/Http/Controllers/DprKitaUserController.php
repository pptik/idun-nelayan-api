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

    public function authenticate(Request $request){
        $user = DB::table('user')
            ->where('user', '=', $request->input('username'))
            ->first();

        if($user){
            if (Hash::check($request->input('password'), $user->password)) {
                return response()->json(
                    array(
                        'user' => $user,
                        'totalaspirasi' => $this->countaspirasi($user->id_user),
                        'totalberita' => $this->countberita($user->id_user),
                        'beritafavorit' => $this->beritafavorit(),
                        'aspirasifavorit' => $this->aspirasifavorit(),
                        'status' => true,
                        'msg' => 'login berhasil'
                    ), 200);
            }
            else{
                return response()->json(
                    array(
                        'status' => false,
                        'msg' => 'gagal login, password tidak sama'
                    ), 200);
            }
        }else{
            return response()->json(
                array(
                    'status' => false,
                    'msg' => 'gagal login, username tidak ditemukan'
                ), 200);
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
    public function  getrandomcode($len = 5){
        $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $base = strlen($charset);
        $result = '';

        $now = explode(' ', microtime())[1];
        while ($now >= $base){
            $i = $now % $base;
            $result = $charset[$i] . $result;
            $now /= $base;
        }
        return substr($result, -5);
    }
    public function store(Request $request)
    {
        $dt = Carbon::now();

        while ($i=true){
            $kode=null;
            $kode=$this->getrandomcode();

            $ambilkode = DB::table('user')
                ->select('id_user')
                ->where('id_user', '=', $kode)
                ->get();

            if ($ambilkode!=NULL){
                $i=true;
            }
            else{
                $i=false;
                break;
            }
        }

        $param = array(
            'id_user' => $kode,
            'user' => $request->input('user'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'id_level' => "2",
            'id_source' => "3"
        );

        $cek_availability = DB::table('user')->where('user', '=', $request->input('user'))->get();
        if(!$cek_availability){
            $result = DB::table('user')->insert($param);
            if($result){
                $user = DB::table('user')
                    ->where('user', '=', $request->input('user'))
                    ->first();

                return response()->json(
                    array(
                        'user' => $user,
                        'totalaspirasi' => $this->countaspirasi($user->id_user),
                        'totalberita' => $this->countberita($user->id_user),
                        'beritafavorit' => $this->beritafavorit(),
                        'aspirasifavorit' => $this->aspirasifavorit(),
                        'status' => true,
                        'msg' => 'data berhasil insert!'
                    ), 200);
            }else{
                return response()->json(
                    array('status' => false,
                        'msg' => 'terjadi kesalahan saat insert!'), 200);
            }
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'username sudah digunakan'), 200);
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
        $result = DB::table('user')->where('id_user', '=', $id_user)->first();
        if ($result) {
            return response()->json(
                array(
                    'user' => $result,
                    'status' => true,
                    'msg' => 'login berhasil'
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
    public function update(Request $request, $id_user)
    {
        $dt = Carbon::now();
        $param = array(
            'first_name' => $request->input('firstname'),
            'last_name' => $request->input('lastname'),
            'username' => $request->input('username'),
            'lokasi' => $request->input('lokasi'),
            'bio' => $request->input('bio'),
            'updated_at' => $dt->format('Y-m-d H:i:s')
        );

        $result = DB::table('user')
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
    public function showall()
    {
        $result = DB::table('user')->get();
        if ($result) {
            return response()->json(
                array(
                    'user' => $result,
                    'status' => true,
                    'msg' => 'login berhasil'
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
    public function getprofile($id_user)
    {
        $dt = Carbon::now();

        $result = DB::table('user')
            ->where('id_user', $id_user)
            ->get();

        if($result){
            return response()->json(
                array(
                    'profile' => $result,
                    'status' => true,
                    'msg' => 'data berhasil diambil'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan!'), 200);
        }
    }
    public function getprofileanggota($id_user)
    {
        $dt = Carbon::now();

        $result = DB::table('detail_person')
            ->where('id_user', $id_user)
            ->get();

        if($result){
            return response()->json(
                array(
                    'profile' => $result,
                    'status' => true,
                    'msg' => 'data berhasil diambil'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan!'), 200);
        }
    }

    public function updateprofile(Request $request, $id_user)
    {
        $dt = Carbon::now();
        $param = array(
            'user' => $request->input('user'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'picture' => $request->input('picture')
        );

        $result = DB::table('user')
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

    public function updateprofileanggota(Request $request, $id_user)
    {
        $dt = Carbon::now();
        $param = array(
            'person' => $request->input('person'),
            'alamat' => $request->input('alamat'),
            'tempat_lahir' => $request->input('tempat_lahir'),
            'no_telepon' => $request->input('no_telepon'),
            'tgl_lahir' => $request->input('tgl_lahir'),
            'img_person' => $request->input('img_person'),
            'email' => $request->input('email'),
            'id_fraksi' => $request->input('id_fraksi'),
            'id_dapil' => $request->input('id_dapil'),
            'id_komisi' => $request->input('id_komisi')
        );

        $result = DB::table('detail_person')
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
    public function countaspirasi($id_user)
    {
        $result=DB::table('aspirasi')
            ->where('id_user',$id_user)
            ->count();
        return $result;
    }
    public function countberita($id_user)
    {
        $result=DB::table('berita')
            ->where('id_user',$id_user)
            ->count();
        return $result;
    }
    public function beritafavorit(){
        $result=DB::table('berita')
            ->orderby('dukungan','desc')
            ->take(3)
            ->get();
        return $result;
    }
    public function aspirasifavorit(){
        $result=DB::table('aspirasi')
            ->orderby('dukungan','desc')
            ->take(3)
            ->get();
        return $result;
    }
    public function showallanggota()
    {
        $result = DB::table('detail_person')

            ->get();
        $result2 = DB::table('dk_anggota')
            ->get();
        if ($result) {
            return response()->json(
                array(
                    'anggota' => $result,
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
    public function ubahstatusdukungan($id_user,$id_person)
    {
        $dt = Carbon::now();

        $hasilcheck=$this->checkstatusdukungan($id_user,$id_person);

        if ($hasilcheck==1){

            $param = array(
                'status' => 0
            );

            $result = DB::table('dk_anggota')
                ->where('id_person', $id_person)
                ->where('id_user', $id_user)
                ->update($param);
            $this->kurangidukungan($id_person);
        }elseif ($hasilcheck==0){
            $param = array(
                'status' => 1
            );

            $result = DB::table('dk_anggota')
                ->where('id_person', $id_person)
                ->where('id_user', $id_user)
                ->update($param);
            $this->tambahkandukungan($id_person);

        }else{
            $param = array(
                'id_person' => $id_person,
                'id_user' => $id_user,
                'status' => 1,
            );
            $result = DB::table('dk_anggota')->insert($param);
            $this->tambahkandukungan($id_person);

        }
    }
    public function checkstatusdukungan($id_user,$id_person)
    {
        $dt = Carbon::now();

        $result=DB::table('dk_anggota')
            ->where('id_user', $id_user)
            ->where('id_person', $id_person)
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
    public function tambahkandukungan($id_person){
        $result=DB::table('detail_person')
            ->where('id_person', $id_person)
            ->select('dukungan');
        $result=$result+1;
        $param = array(
            'dukungan' => $result
        );

        $result = DB::table('detail_person')
            ->where('id_person', $id_person)
            ->update($param);
    }
    public function kurangidukungan($id_person){
        $result=DB::table('detail_person')
            ->where('id_person', $id_person)
            ->select('dukungan');
        $result=$result-1;
        $param = array(
            'dukungan' => $result
        );

        $result = DB::table('detail_person')
            ->where('id_person', $id_person)
            ->update($param);
    }
}
