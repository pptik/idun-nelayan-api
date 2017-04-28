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

class UserController extends Controller
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
            ->where('email', '=', $request->input('email'))
            ->first();
			
		//$token = JWTAuth::fromUser($user);
		//$credentials = $request->only('email', 'password');
		//$token = JWTAuth::attempt($credentials))
        
//		try {
//			//$customClaims = ['foo' => 'bar', 'baz' => 'bob'];
//		
//            // verify the credentials and create a token for the user
//            if (! $token = JWTAuth::fromUser($user)) {
//                return response()->json(
//				[
//					'error' => 'invalid_credentials'				
//				], 401);
//            }
//        } catch (JWTException $e) {
//            // something went wrong
//            return response()->json(['error' => 'could_not_create_token'], 500);
//        }

        // if no errors are encountered we can return a JWT
        if($user){
            if (Hash::check($request->input('password'), $user->password)) {
               return response()->json(
                array(
                    'user' => $user,
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
                'msg' => 'gagal login, autentikasi tidak ditemukan'
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
    public function store(Request $request)
    {
        $dt = Carbon::now();        
        $param = array(
            'first_name' => $request->input('firstname'),
            'last_name' => $request->input('lastname'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'lokasi' => $request->input('lokasi'),
            'bio' => $request->input('bio'),
            'path_image' => "default.png",
            'role_id' => "2",
            'created_at' => $dt->format('Y-m-d H:i:s'),
            'updated_at' => $dt->format('Y-m-d H:i:s')
        );
        $cek_availability = DB::table('user')->where('email', '=', $request->input('email'))->get();
        if(!$cek_availability){
            $result = DB::table('user')->insert($param);
            if($result){
                $user = DB::table('user')
                        ->where('email', '=', $request->input('email'))
                        ->first();
                    
                return response()->json(
                array(
                      'user' => $user,
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
                      'msg' => 'email sudah digunakan'), 200);
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
}
