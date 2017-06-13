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

class NelayanUserV2Controller extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        //$this->middleware('jwt.auth', ['except' => ['authenticate']]);
		
		$this->filterpostkejahatan 	= 0b00000001;// 1
        $this->filterpostcuaca 	= 0b00000010;// 2
        $this->filterpostkeadaanlaut 	= 0b00000100;// 4
        $this->filterpostpanicbutton = 0b00001000;// 8
        $this->filterposthasiltangkapan 	= 0b00010000;// 16
		
		
		
		
        $this->otherpost 	= 0b00100000;// 32
		$this->commutertrain 	= 0b01000000;//64
		$this->angkot	 	= 0b10000000;//128
		
		

        //constanta places item
        $this->foodplace 		= 0b0000000001;// 1
        $this->hotelplace 		= 0b0000000010;// 2
        $this->fashionplace		= 0b0000000100;// 4
        $this->gasplace 		= 0b0000001000;// 8
        $this->schoolplace 		= 0b0000010000;// 16
        $this->univplace 		= 0b0000100000;// 32
        $this->hospitalplace	= 0b0001000000;// 64
        $this->bankplace 		= 0b0010000000;// 128
        $this->stationplace		= 0b0100000000;// 256
        $this->deptstoreplace 	= 0b1000000000;// 512
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
	
	//TPI input ketersediaan ikan
	public function inputKetersediaan(Request $request)
    {
        $dt = Carbon::now();
		$keterangan=$request->input('keterangan');
		//
	
		$app_url = env('APP_URL', 'http://167.205.7.228:8088/v1');
		$fotosourcename="default.png";
        if ($request->hasFile('foto_source')){
           if ($request->file('foto_source')->isValid()) {
		   
				$fotosourcename = $request->file('foto_source')->getClientOriginalName();
				$request->file('foto_source')->move(
					base_path() . '/resources/assets/image/', $fotosourcename
				);
          }
        }
		$foto_source=$app_url . '/resources/assets/image/'. $fotosourcename;
	
		$videosourcename="";
		 if ($request->hasFile('video_source')){
           if ($request->file('video_source')->isValid()) {
		   
				$videosourcename = $request->file('video_source')->getClientOriginalName();
				$request->file('video_source')->move(
					base_path() . '/resources/assets/video/', $videosourcename
				);
          }
        }
		$video_source=$app_url . '/resources/assets/video/'. $videosourcename;
		
		$nama_ikan=$request->input('nama_ikan');
		$id_jenisikan=DB::table('jenis_ikan')
			->select('id_jenisikan')
			->where('nama_ikan', '=', $nama_ikan)
			->first();
		$berat=$request->input('berat');
		$id_tpi=DB::table('tpi_detail')
			->select('id_tpi')
			->where('nama_tpi', '=', $request->input('nama_tpi'))
			->where('lokasi', '=', $request->input('lokasi_tpi'))
			->first();

		$param = array(
			'id_tpi' => $id_tpi->id_tpi, 
			'jenis_ikan' => $id_jenisikan->id_jenisikan,
			'total_berat' => $request->input('berat'),
			'harga_perkg'	=> $request->input('harga'),
            'created_at' => $dt,
			'keterangan'	=> $request->input('keterangan'),
			'harga_perkg'	=> $request->input('harga_perkg'),
			'foto_source' => $foto_source,
			'video_source' => $video_source
        );
        
		$result = DB::table('tpi_ketersediaan')->insert($param);
		if($result){
			return response()->json(
					array(
						'status' => true,
						'msg' => 'data berhasil disimpan!'
					), 200);	
		}else{
			return response()->json(
				array('status' => false,
					'msg' => 'terjadi kesalahan!'), 200);
		}
    }
	
	//PEMBELI input kebutuhan ikan
	public function inputkebutuhan(Request $request){
		$dt = Carbon::now();	

		$nama_ikan=$request->input('nama_ikan');
		$id_jenisikan=DB::table('jenis_ikan')
			->select('id_jenisikan')
			->where('nama_ikan', '=', $nama_ikan)
			->first();
		$berat=$request->input('berat');
		$id_tpi=DB::table('tpi_detail')
			->select('id_tpi')
			->where('nama_tpi', '=', $request->input('nama_tpi'))
			->where('lokasi', '=', $request->input('lokasi_tpi'))
			->first();

		$param = array(
			'id_tpi' => $id_tpi->id_tpi,
			'id_user' => $request->input('id_user'),
			'jenis_ikan' => $id_jenisikan->id_jenisikan,
			'total_berat' => $request->input('berat'),
            'created_at' => $dt
        );
        
		$result = DB::table('tpi_kebutuhan')->insert($param);
		if($result){
			return response()->json(
					array(
						'status' => true,
						'msg' => 'data berhasil disimpan!'
					), 200);	
		}else{
			return response()->json(
				array('status' => false,
					'msg' => 'terjadi kesalahan!'), 200);
		}
	}
	
	public function getkebutuhan($id_tpi){
        $result = DB::table('tpi_kebutuhan')
			->select('tpi_kebutuhan.*'
						, DB::raw('(SELECT jenis_ikan.nama_ikan FROM jenis_ikan WHERE jenis_ikan.id_jenisikan = tpi_kebutuhan.jenis_ikan) as nama_ikan')
						, DB::raw('(SELECT users.username FROM users WHERE users.id_user = tpi_kebutuhan.id_user) as username')
						, DB::raw('(SELECT detail_users.firstname FROM detail_users WHERE detail_users.id_user = tpi_kebutuhan.id_user) as firstname')
						, DB::raw('(SELECT detail_users.lastname FROM detail_users WHERE detail_users.id_user = tpi_kebutuhan.id_user) as lastname'))
			->where('tpi_kebutuhan.id_tpi', $id_tpi)
            ->get();
		
        if($result){
            return response()->json(
                array(
				'data' => $result,
				'status' => true,
                'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }
	}

	public function getketersediaan($id_tpi){
        $result = DB::table('tpi_ketersediaan')
			->join('tpi_detail', 'tpi_ketersediaan.id_tpi', '=', 'tpi_detail.id_tpi')
			->join('jenis_ikan', 'tpi_ketersediaan.jenis_ikan', '=', 'jenis_ikan.id_jenisikan')
			->select('tpi_detail.nama_tpi', 'jenis_ikan.nama_ikan','tpi_ketersediaan.total_berat','tpi_ketersediaan.harga_perkg'
			, 'tpi_ketersediaan.created_at', 'tpi_ketersediaan.foto_source', 'tpi_ketersediaan.video_source')
			->where('tpi_ketersediaan.id_tpi', $id_tpi)
            ->get();
		
        if($result){
            return response()->json(
                array(
				'data' => $result,
				'status' => true,
                'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }
	}
	
	public function panicbuttonstore(Request $request)
    {
        $dt = Carbon::now();
		
		$id_user1=$request->input('id_user1');
		$jenis_laporan=4;
		$latitude=$request->input('latitude');
		$longitude=$request->input('longitude');
	
        $param = array(
					'id_user' => $id_user1,
					'id_jenislaporan' => $jenis_laporan,
					'longitude' => $longitude,
					'latitude' => $latitude,
					'detailwaktu' => $dt
						);
				$result = DB::table('laporan_panicbutton')->insert($param);
			
					if($result){
					   return response()->json(
							array('status' => true,
								'msg' => 'laporan berhasil di posting'), 200);
					}else{
						return response()->json(
							array('status' => false,
								'msg' => 'terjadi kesalahan pada memasukan data!'), 200);
					}
    }
	
	public function getjenisikan(){
		$jenisikan=false;
		$jenisikan=DB::table('jenis_ikan')
					->get();
		if($jenisikan){
			return response()->json(
                array('jenisikan' => $jenisikan,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
			
		}else{
			return response()->json(
                array(
				'status' => false,
                    'msg' => 'data gagal diambil!'), 200);
		}
	}

	public function getjenislog(){
		$jenislog=false;
		$jenislog=DB::table('laporan_penangkapanlogbook')
					->get();
		if($jenislog){
			return response()->json(
                array('jenislog' => $jenislog,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
			
		}else{
			return response()->json(
                array(
				'status' => false,
                    'msg' => 'data gagal diambil!'), 200);
		}
	}

	public function getwilayah(){
		$wilayah=false;
		$wilayah=DB::table('laporan_penangkapanwilayah')
					->get();
		if($wilayah){
			return response()->json(
                array('wilayah' => $wilayah,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
			
		}else{
			return response()->json(
                array(
				'status' => false,
                    'msg' => 'data gagal diambil!'), 200);
		}
	}

	public function getgerombolanikan(){
		$gerombolan=false;
		$gerombolan=DB::table('laporan_penangkapangerombolan')
					->get();
		if($gerombolan){
			return response()->json(
                array('gerombolan_ikan' => $gerombolan,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
			
		}else{
			return response()->json(
                array(
				'status' => false,
                    'msg' => 'data gagal diambil!'), 200);
		}
	}

	public function getaktivitas(){
		$aktivitas=false;
		$aktivitas=DB::table('laporan_penangkapanaktivitas')
					->get();
		if($aktivitas){
			return response()->json(
                array('aktivitas' => $aktivitas,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
			
		}else{
			return response()->json(
                array(
				'status' => false,
                    'msg' => 'data gagal diambil!'), 200);
		}
	}
	
	public function getdatatpinama(Request $request)
    {
		$dt = Carbon::now();
        $result = DB::table('tpi_detail')
			->select('id_tpi','nama_tpi')
			->groupBy('nama_tpi')
            ->get();
		
        if($result){
            return response()->json(
                array('datatpi' => $result,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }
    }
	
	public function getdatatpilokasi(Request $request)
    {
		$dt = Carbon::now();
        $result = DB::table('tpi_detail')
			->select('id_tpi','lokasi','kota')
			->groupBy('lokasi')
            ->get();
		
        if($result){
            return response()->json(
                array('datatpi' => $result,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }
    }
	
	public function getdatatpikota(Request $request)
    {
		$dt = Carbon::now();
        $result = DB::table('tpi_detail')
			->select('id_tpi','kota','provinsi')
			->groupBy('kota')
            ->get();
		
        if($result){
            return response()->json(
                array('datatpi' => $result,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }
    }
	
	public function getdatatpiprovinsi()
    {
		$dt = Carbon::now();
        $result = DB::table('tpi_detail')
			->select('id_tpi','provinsi')
			->groupBy('provinsi')
            ->get();
		
        if($result){
            return response()->json(
                array('datatpi' => $result,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }
    }
	
	 public function editprofile(Request $request, $id_user)
    {
        $dt = Carbon::now();  
        $param = array(
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'tempat_lahir' => $request->input('tempat_lahir'),
            'tanggal_lahir' => $request->input('tanggal_lahir'),
            'foto_source' => $request->input('foto_source'),
            'nomor_identitas' => $request->input('nomor_identitas')
        );
        
        $result = DB::table('detail_users')
            ->where('id_user', $id_user)
            ->update($param);
        
        if($result){
			$result2=DB::table('detail_users')
			->select('*')
            ->where('id_user', $id_user)
            ->get();
                return response()->json(
                array(
					'profile'=>$result2,
						'status' => true,
                      'msg' => 'data berhasil terupdate!'), 200);
            }else{
                return response()->json(
                array('status' => false, 
                      'msg' => 'terjadi kesalahan saat update!'), 200);
            }
    }
	 public function editfirstname(Request $request, $id_user)
    {
        $dt = Carbon::now();  
        $param = array(
            'nama_depan' => $request->input('firstname')
        );

		$role = DB::table('user_')->where('id_user', $id_user)->first()->role;
		$fixdetailtabel = "";
		if($role == '1')
			$fixdetailtabel = 'user_nelayandetail';
		else if($role == '3')
			$fixdetailtabel = 'user_pembelidetail';	
		
        $result = DB::table($fixdetailtabel)
            ->where('id_user', $id_user)
            ->update($param);
        if($result){
                return response()->json(
                array(
						'status' => true,
                      'msg' => 'data berhasil terupdate!'), 200);
            }else{
                return response()->json(
                array('status' => false, 
                      'msg' => 'terjadi kesalahan saat update!'), 200);
            }
    }
	
	public function editlastname(Request $request, $id_user)
    {
        $dt = Carbon::now();  
        $param = array(
            'nama_belakang' => $request->input('lastname')
		);

		$role = DB::table('user_')->where('id_user', $id_user)->first()->role;
		$fixdetailtabel = "";
		if($role == '1')
			$fixdetailtabel = 'user_nelayandetail';
		else if($role == '3')
			$fixdetailtabel = 'user_pembelidetail';	

        $result = DB::table($fixdetailtabel)
            ->where('id_user', $id_user)
            ->update($param);
        if($result){
                return response()->json(
                array(
						'status' => true,
                      'msg' => 'data berhasil terupdate!'), 200);
            }else{
                return response()->json(
                array('status' => false, 
                      'msg' => 'terjadi kesalahan saat update!'), 200);
            }
    }

	public function editaddress(Request $request, $id_user)
    {
        $dt = Carbon::now();  
        $param = array(
            'alamat' => $request->input('address')
		);

		$role = DB::table('user_')->where('id_user', $id_user)->first()->role;
		$fixdetailtabel = "";
		if($role == '1')
			$fixdetailtabel = 'user_nelayandetail';
		else if($role == '3')
			$fixdetailtabel = 'user_pembelidetail';	

        $result = DB::table($fixdetailtabel)
            ->where('id_user', $id_user)
            ->update($param);
        if($result){
                return response()->json(
                array(
						'status' => true,
                      'msg' => 'data berhasil terupdate!'), 200);
            }else{
                return response()->json(
                array('status' => false, 
                      'msg' => 'terjadi kesalahan saat update!'), 200);
            }
    }

	public function edittempatlahir(Request $request, $id_user)
    {
        $dt = Carbon::now();  
        $param = array(
            'tempat_lahir' => $request->input('tempat_lahir')
        );
        
        $result = DB::table('detail_users')
            ->where('id_user', $id_user)
            ->update($param);
        
        if($result){
                return response()->json(
                array(
						'status' => true,
                      'msg' => 'data berhasil terupdate!'), 200);
            }else{
                return response()->json(
                array('status' => false, 
                      'msg' => 'terjadi kesalahan saat update!'), 200);
            }
    }
	 public function edittanggallahir(Request $request, $id_user)
    {
        $dt = Carbon::now();  
        $param = array(
            'tanggal_lahir' => $request->input('tanggal_lahir')
        );
        
        $result = DB::table('detail_users')
            ->where('id_user', $id_user)
            ->update($param);
        
        if($result){
                return response()->json(
                array(
						'status' => true,
                      'msg' => 'data berhasil terupdate!'), 200);
            }else{
                return response()->json(
                array('status' => true,
                      'msg' => 'data berhasil terupdate!'), 200);
            }
    }
	 public function editfoto(Request $request, $id_user)
    {
        $dt = Carbon::now();  
        $param = array(
            
            'foto_source' => $request->input('foto_source')
        );
        
        $result = DB::table('detail_users')
            ->where('id_user', $id_user)
            ->update($param);
        
        if($result){
                return response()->json(
                array(
						'status' => true,
                      'msg' => 'data berhasil terupdate!'), 200);
            }else{
                return response()->json(
                array('status' => false, 
                      'msg' => 'terjadi kesalahan saat update!'), 200);
            }
    }
	 public function editnoidentitas(Request $request, $id_user)
    {
        $dt = Carbon::now();  
        $param = array(
            'nomor_identitas' => $request->input('nomor_identitas')
        );
        
        $result = DB::table('detail_users')
            ->where('id_user', $id_user)
            ->update($param);
        
        if($result){
                return response()->json(
                array(
						'status' => true,
                      'msg' => 'data berhasil terupdate!'), 200);
            }else{
                return response()->json(
                array('status' => false, 
                      'msg' => 'terjadi kesalahan saat update!'), 200);
            }
    }
	public function getrequestdatatpi()
    {
		$dt = Carbon::now();
        $result = DB::table('tpi_request')
            ->get();
		
        if($result){
            return response()->json(
                array('datarequesttpi' => $result,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }
    }
	public function showriwayatbyid($id_user)
    {
		//kategori,nama,timestamp,longitude,latitude
		$dt = Carbon::now();
        /*$result = DB::table('nelayan_postlaporan')
			->orderBy('nelayan_postlaporan.detail_waktu','desc')
			->where('nelayan_postlaporan.id_user', $id_user)
            ->get();*/
		
        //if($result){
			$cuaca = DB::table('laporan_cuaca')
				//->orderBy('laporan_cuaca.datailwaktu', 'desc')
				->where('laporan_cuaca.id_user', '=', $id_user)
				->get();
		
			$keadaanlaut = DB::table('laporan_keadaanlaut')
				//->orderBy('laporan_keadaanlaut.datailwaktu', 'desc')
				->where('laporan_keadaanlaut.id_user', '=', $id_user)
				->get();
		
			 $kejahatan = DB::table('laporan_kejahatanlaut')
				//->orderBy('laporan_kejahatanlaut.datailwaktu', 'desc')
				->where('laporan_kejahatanlaut.id_user', '=', $id_user)
				->get();
		
			 /*$tangkapan = DB::table('laporan_penangkapan')
				->orderBy('laporan_kejahatanlaut.datailwaktu', 'desc')
				->where('laporan_kejahatanlaut.id_user', '=', $id_user)
				->get();*/
           
			return response()->json(
                array(
					'cuaca' => $cuaca,	
					'keadaanlaut' => $keadaanlaut,
					'kejahatan' => $kejahatan,
					//'tangkapan' => $tangkapan,
					'status' => true,
					'msg' => 'data berhasil diambil!'), 200);
        /*}else{
            return response()->json(
                array(
				'status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }*/
    }
	
	public function infotambahan()
    {
		$dt = Carbon::now();
        $result = DB::table('tpi_detail')
			->select('id_tpi','nama_tpi')
            ->get();
		$result2=DB::table('jenis_ikan')
            ->get();
        if($result && $result2){
            return response()->json(
                array('datatpi' => $result,
				'datajenisikan' => $result2,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }
    }
	
	public function kejahatanstore(Request $request)
    {
        $dt = Carbon::now();
		
		$id_user1=$request->input('id_user1');
		$jenis_laporan=1;
		$latitude=$request->input('latitude');
		$longitude=$request->input('longitude');
		
		//$id_laporan=null;
		$keterangan=$request->input('keterangan');
		//
	
		$app_url = env('APP_URL', 'http://167.205.7.228:8088/v1');
		$fotosourcename="default.png";
        if ($request->hasFile('foto_source')){
           if ($request->file('foto_source')->isValid()) {
		   
				$fotosourcename = $request->file('foto_source')->getClientOriginalName();
				$request->file('foto_source')->move(
					base_path() . '/resources/assets/image/', $fotosourcename
				);
          }
        }
		$foto_source=$app_url . '/resources/assets/image/'. $fotosourcename;
	
		$videosourcename="";
		 if ($request->hasFile('video_source')){
           if ($request->file('video_source')->isValid()) {
		   
				$videosourcename = $request->file('video_source')->getClientOriginalName();
				$request->file('video_source')->move(
					base_path() . '/resources/assets/video/', $videosourcename
				);
          }
        }
		$video_source=$app_url . '/resources/assets/video/'. $videosourcename;
		
		//
		
		$param = array(
					'id_user' => $id_user1,
					'id_jenislaporan' => $jenis_laporan,
					'keterangan' => $keterangan,
					'foto_source' => $foto_source,
					'video_source' => $video_source,
					'longitude' => $longitude,
					'latitude' => $latitude,
					'detailwaktu' => $dt
				);
			$result = DB::table('laporan_kejahatanlaut')->insert($param);
		
				if($result){
					return response()->json(
						array('status' => true,
							'msg' => 'laporan berhasil di posting'), 200);
				}else{
					return response()->json(
						array('status' => false,
							'msg' => 'terjadi kesalahan pada memasukan data!'), 200);
				}
    }
	
	public function cuacastore(Request $request)
    {
        $dt = Carbon::now();
		
		$id_user1=$request->input('id_user1');
		$jenis_laporan=2;
		$latitude=$request->input('latitude');
		$longitude=$request->input('longitude');
		
		//$id_laporan=null;
		$jenis_cuaca=$request->input('jenis_cuaca');
		$keterangan=$request->input('keterangan');
		//
	
		$app_url = env('APP_URL', 'http://167.205.7.228:8088/v1');
		$fotosourcename="default.png";
        if ($request->hasFile('foto_source')){
           if ($request->file('foto_source')->isValid()) {
		   
				$fotosourcename = $request->file('foto_source')->getClientOriginalName();
				$request->file('foto_source')->move(
					base_path() . '/resources/assets/image/', $fotosourcename
				);
          }
        }
		$foto_source=$app_url . '/resources/assets/image/'. $fotosourcename;
	
		$videosourcename="";
		 if ($request->hasFile('video_source')){
           if ($request->file('video_source')->isValid()) {
		   
				$videosourcename = $request->file('video_source')->getClientOriginalName();
				$request->file('video_source')->move(
					base_path() . '/resources/assets/video/', $videosourcename
				);
          }
        }
		$video_source=$app_url . '/resources/assets/video/'. $videosourcename;
		
		//
		
        $param = array(
					'id_user' => $id_user1,
					'id_jenislaporan' => $jenis_laporan,
					'jenis_cuaca' => $jenis_cuaca,
					'keterangan' => $keterangan,
					'foto_source' => $foto_source,
					'video_source' => $video_source,
					'longitude' => $longitude,
					'latitude' => $latitude,
					'detailwaktu' => $dt
						);
				$result = DB::table('laporan_cuaca')->insert($param);
			
					if($result){
					   return response()->json(
							array('status' => true,
								'msg' => 'laporan berhasil di posting'), 200);
					}else{
						return response()->json(
							array('status' => false,
								'msg' => 'terjadi kesalahan pada memasukan data!'), 200);
					}
    }
	
	public function keadaanlautstore(Request $request)
    {
        $dt = Carbon::now();
		
		$id_user1=$request->input('id_user1');
		$jenis_laporan=3;
		$latitude=$request->input('latitude');
		$longitude=$request->input('longitude');
		
		//$id_laporan=null;
		$jenis_ombak=$request->input('jenis_ombak');
		$keterangan=$request->input('keterangan');
		//
	
		$app_url = env('APP_URL', 'http://167.205.7.228:8088/v1');
		$fotosourcename="default.png";
        if ($request->hasFile('foto_source')){
           if ($request->file('foto_source')->isValid()) {
		   
				$fotosourcename = $request->file('foto_source')->getClientOriginalName();
				$request->file('foto_source')->move(
					base_path() . '/resources/assets/image/', $fotosourcename
				);
          }
        }
		$foto_source=$app_url . '/resources/assets/image/'. $fotosourcename;
	
		$videosourcename="";
		 if ($request->hasFile('video_source')){
           if ($request->file('video_source')->isValid()) {
		   
				$videosourcename = $request->file('video_source')->getClientOriginalName();
				$request->file('video_source')->move(
					base_path() . '/resources/assets/video/', $videosourcename
				);
          }
        }
		$video_source=$app_url . '/resources/assets/video/'. $videosourcename;
		
		//
		$param = array(
				'id_user' => $id_user1,
				'id_jenislaporan' => $jenis_laporan,
				'jenis_keadaanlaut' => $jenis_ombak,
				'keterangan' => $keterangan,
				'foto_source' => $foto_source,
				'video_source' => $video_source,
				'longitude' => $longitude,
				'latitude' => $latitude,
				'detailwaktu' => $dt
			);
			$result = DB::table('laporan_keadaanlaut')->insert($param);
		
				if($result){
					return response()->json(
						array('status' => true,
							'msg' => 'laporan berhasil di posting'), 200);
				}else{
					return response()->json(
						array('status' => false,
							'msg' => 'terjadi kesalahan pada memasukan data!'), 200);
				}
    }
	
	public function hasiltangkapanstore(Request $request)
    {
        $dt = Carbon::now();
		//
	
		$app_url = env('APP_URL', 'http://167.205.7.228:8088/v1');
		$fotosourcename="default.png";
        if ($request->hasFile('foto_source')){
           if ($request->file('foto_source')->isValid()) {
		   
				$fotosourcename = $request->file('foto_source')->getClientOriginalName();
				$request->file('foto_source')->move(
					base_path() . '/resources/assets/image/', $fotosourcename
				);
          }
        }
		$foto_source=$app_url . '/resources/assets/image/'. $fotosourcename;
	
		$videosourcename="";
		 if ($request->hasFile('video_source')){
           if ($request->file('video_source')->isValid()) {
		   
				$videosourcename = $request->file('video_source')->getClientOriginalName();
				$request->file('video_source')->move(
					base_path() . '/resources/assets/video/', $videosourcename
				);
          }
        }
		$video_source=$app_url . '/resources/assets/video/'. $videosourcename;
		
		//
		$id_user1=$request->input('id_user1');
		$jenis_laporan=5;
		$latitude=$request->input('latitude');
		$longitude=$request->input('longitude');
		$nama_ikan=$request->input('nama_ikan');
		$id_jenisikan=DB::table('jenis_ikan')
			->select('id_jenisikan')
			->where('nama_ikan', '=', $nama_ikan)
			->first();
		$berat=$request->input('berat');
		$id_tpifinal=null;
		$id_tpi=DB::table('tpi_detail')
			->select('id_tpi')
			->where('nama_tpi', '=', $request->input('nama_tpi'))
			->where('lokasi', '=', $request->input('lokasi_tpi'))
			->first();
		$harga_perkg=$request->input('harga_perkg');
		$keterangan=$request->input('keterangan');
		
        if($this->postlaporan($request->input('id_user1'),$jenis_laporan,$latitude,$longitude)==true){
			$id_laporanobj=DB::table('nelayan_postlaporan')
			->select('id_laporan')
			->where('id_user', '=', $id_user1)
			->orderBy('detail_waktu', 'desc')
			->first();
			if($id_laporanobj){
				
				$id_laporan=$id_laporanobj->id_laporan;
				 $param = array(
					'id_laporan' => $id_laporan,
					'id_jenisikan' => $id_jenisikan->id_jenisikan,
					'berat' => $berat,
					'id_tpi' => $id_tpi->id_tpi,
					'foto_source' => $foto_source,
					'video_source' => $video_source,
					'harga_perkg' => $harga_perkg,
					'keterangan' => $keterangan
						);
				$result = DB::table('nelayan_detailtangkapan')->insert($param);
			
					if($result){
					   return response()->json(
							array('status' => true,
								'msg' => 'laporan berhasil di posting'), 200);
					}else{
						return response()->json(
							array('status' => false,
								'msg' => 'terjadi kesalahan pada memasukan data!'), 200);
					}
			}else{
				return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan pada detail laporan!'), 200);
			}
		}else{
			 return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan!'), 200);
		}
    }
	
	private function postlaporan($id_user1,$jenis_laporan,$latitude,$longitude){
		 $param = array(
            'id_user' => $id_user1,
            'jenis_laporan' => $jenis_laporan,
			'latitude' => $latitude,
			'longitude' => $longitude
        );
		$result = DB::table('nelayan_postlaporan')->insert($param);
			
            if($result){
               return true;
            }else{
                return false;
            }
	}
	
	public function getdatatpi()
    {
		$dt = Carbon::now();
        $result = DB::table('tpi_detail')
            ->get();
		
        if($result){
            return response()->json(
                array('datatpi' => $result,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
        }
    }
	
	public function userjointpi($id_user,$id_tpi)
    {
		$dt = Carbon::now();
       
		$param = array(
            'id_tpi' => $id_tpi,
            'id_user' => $id_user,
			'status'=> 2
        );
		
		$cek_availability = DB::table('tpi_member')
		->where('id_tpi', '=', $id_tpi)
		->where('id_user', '=', $id_user)
		->first();
        
		if(!$cek_availability){
            $result = DB::table('tpi_member')->insert($param);
			
            if($result){
				
                return response()->json(
                array(
				'status' => true,
                    'msg' => 'anda berhasil mendaftar!'), 200);
            }else{
                return response()->json(
                    array('status' => false,
                        'msg' => 'terjadi kesalahan!'), 200);
            }
        }else{
			$message1="";
			if($cek_availability->status ==1 ){
				$message="anda sudah terdaftar";
			}
			else{
				$message="silahkan tunggu verifikasi";
			}
            return response()->json(
                array('status' => false,
                    'msg' => $message), 200);
        }
    }
	
	public function profileshowbyid($id_user)
    {
		$dt = Carbon::now();
		$result = DB::table('user_')
			->where('user_.id_user', $id_user)
			->first();
		
		if($result){
			$role = $result->role;
			if($role == '1'){
				 $result2 = DB::table('user_')
				->join('user_nelayandetail', 'user_.id_user', '=', 'user_nelayandetail.id_user')
				->where('user_.id_user', $id_user)
				->get();
			}
			else if($role == '3'){
				 $result2 = DB::table('user_')
				->join('user_pembelidetail', 'user_.id_user', '=', 'user_pembelidetail.id_user')
				->where('user_.id_user', $id_user)
				->get();
			}
			
			$resultTPI=DB::table('tpi_detail')
					->get();

			return response()->json(
                array('profileuser' => $result2,
				'tpi' => $resultTPI,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
		}
		else{
				return response()->json(
					array('status' => false,
						'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
			}
    }
	
	public function mapview(Request $request){
       $postkejahatanresult=array();
	   $postcuacaresult=array();
	   $postkeadaanlautresult=array();
	   $postpanicbuttonresult=array();
	   $posthasiltangkapanresult=array();
	   
	   $totalfilter=$request->input('totalfilter');
	   
	   	if ($totalfilter & $this->filterpostkejahatan) {
				$postkejahatanresult = $this->getpostdetail(1);
			}
		if ($totalfilter & $this->filterpostcuaca) {
				$postcuacaresult = $this->getpostdetail(2);
			}
		if ($totalfilter & $this->filterpostkeadaanlaut) {
				$postkeadaanlautresult = $this->getpostdetail(3);
			}
		if ($totalfilter & $this->filterpostpanicbutton) {
				$postpanicbuttonresult = $this->getpostdetail(4);
			}
		if ($totalfilter & $this->filterposthasiltangkapan) {
				$posthasiltangkapanresult = $this->getpostdetail(5);
			}
				return response()->json(
							array(
								'kejahatan' => $postkejahatanresult,
								'cuaca' => $postcuacaresult,
								'keadaanlaut' => $postkeadaanlautresult,
								'panicbutton' => $postpanicbuttonresult,
								'hasiltangkapan' => $posthasiltangkapanresult,
								'status' => true,
								'msg' => 'data berhasil diambil'
							), 200);
    }
	
	private function getpostdetail($jenislaporan){
		$getpostdetail=false;
		$tabeldetailkejahatan="laporan_kejahatanlaut";
		$tabeldetailcuaca="laporan_cuaca";
		$tabeldetailkeadaanlaut="laporan_keadaanlaut";
		$tabelhasiltangkapan="laporan_penangkapan";
		$tabelpanicbutton="laporan_panicbutton";
		$fixdetailtabel="";
		switch ($jenislaporan) {
				case 1:
					$fixdetailtabel=$tabeldetailkejahatan;
					break;
				case 2:
					$fixdetailtabel=$tabeldetailcuaca;
					break;
				case 3:
					$fixdetailtabel=$tabeldetailkeadaanlaut;
					break;
				case 4:
					$fixdetailtabel=$tabelpanicbutton;
					break;
				case 5:
					$fixdetailtabel=$tabelhasiltangkapan;
					break;
				default:
					$fixdetailtabel=$tabeldetailkejahatan;
			}

		/*if($jenislaporan==4){
		$getpostdetail=DB::table('nelayan_postlaporan')
					->where('jenis_laporan','4')
					->get();
			
		}else{
		$getpostdetail=DB::table('nelayan_postlaporan')
					->join('users', 'nelayan_postlaporan.id_user', '=', 'users.id_user')
					->join($fixdetailtabel, 'nelayan_postlaporan.id_laporan', '=', $fixdetailtabel.'.id_laporan')
					->select('nelayan_postlaporan.*','users.username',$fixdetailtabel.'.*')
					->get();
			
		}*/

		$getpostdetail=DB::table($fixdetailtabel)
					->join('user_', $fixdetailtabel.'.id_user', '=', 'user_.id_user')
					->select($fixdetailtabel.'.*','user_.username')
					->get();

		if($getpostdetail){
			return $getpostdetail;
		}else{
			return array();
		}
	}
	
	public function authenticate(Request $request){
        $user = DB::table('user_')
            ->where('username', '=', $request->input('username'))
            ->get();
		$iduser=null;
		$password=null;
		if($user){
		foreach($user as $user1){
			$iduser=$user1->id_user;
			$password=$user1->password;
		}
            if (Hash::check($request->input('password'), $password)) {
				 $param = array(
						'id_user' => $iduser,
						'longitude' => $request->input('longitude'),
						'latitude' => $request->input('latitude')
					);
					$result = DB::table('status_online')->insert($param);
					 
					if($result){
						 /*$detailuser = DB::table('users')
						 ->join('detail_users', 'users.id_user', '=', 'detail_users.id_user')
						 ->join('status_online', 'users.id_user', '=', 'status_online.id_user')
						->where('users.id_user', '=', $iduser)
						->first();*/

						$detailuser = DB::table('user_')
						 ->join('status_online', 'user_.id_user', '=', 'status_online.id_user')
						->where('user_.id_user', '=', $iduser)
						->first();
						
						return response()->json(
							array(
								'detailuser' => $detailuser,
								'kejahatan' => $this->getpostdetail(1),
								'cuaca' => $this->getpostdetail(2),
								'keadaanlaut' => $this->getpostdetail(3),
								//'panicbutton' => $this->getpostdetail(4),
								'hasiltangkapan' => $this->getpostdetail(5),
								'status' => true,
								'msg' => 'data berhasil diambil!'
							), 200);
							
					 }else{
						return response()->json(
							array(
								'status' => false,
								'msg' => 'gagal login, silahkan cek koneksi anda'
							), 200);
					 }
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
	
	public function store(Request $request)
    {
        $dt = Carbon::now();
        $param = array(
            'username' => $request->input('username'),
            'password' => bcrypt($request->input('password')),
            'role' => $request->input('role'),
			'status' => '1'
        );

        $cek_availability = DB::table('user_')->where('username', '=', $request->input('username'))->get();
        
		if(!$cek_availability){
            $result = DB::table('user_')->insert($param);
			
            if($result){
				
                $user = DB::table('user_')
                    ->where('username', '=', $request->input('username'))
                    ->get();
					
					$iduser=null;
							foreach($user as $user1){
								$iduser=$user1->id_user;
							}
					$param2=array(
						'id_user' => $iduser
					);

					/*
						user role 1 => nelayan
						user role 3 => pembeli
					*/
					$role = $request->input('role');
					if($role == '1')
						$result2 = DB::table('user_nelayandetail')->insert($param2);
					else if($role == '3')
						$result2 = DB::table('user_pembelidetail')->insert($param2);
				if($result2){
					return response()->json(
						array(
							'user' => $user,
							'status' => true,
							'msg' => 'data berhasil disimpan!'
						), 200);	
				}else{
					DB::table('user_')->where('username', '=', $request->input('username'))->delete();
					return response()->json(
					array('status' => false,
							'msg' => 'terjadi kesalahan!'), 200);
				}
            }else{
                return response()->json(
                    array('status' => false,
                        'msg' => 'terjadi kesalahan!'), 200);
            }
        }else{
            return response()->json(
                array('status' => false,
                    'msg' => 'username sudah digunakan'), 200);
        }
    }

	public function storePenangkapan(Request $request)
    {
        $param = array(
            'id_laporanjenis' => '5',
            'id_user' => $request->input('id_user'),
            'id_kapal' => $request->input('id_kapal'),
			'id_wilayah' => $request->input('id_wilayah'),
			'DPI' => $request->input('daerah_tangkapan_ikan'),
			'id_log' => $request->input('jenis_log'),
			'trip_ke' => $request->input('trip_ke'),
			'tanggal_berangkat' => Carbon::parse($request->input('tanggal_keberangkatan')),
			'pelabuhan_keberangkatan' => $request->input('pelabuhan_keberangkatan')
        );

        $result = DB::table('laporan_penangkapan')->insert($param);
			
		if($result){
			return response()->json(
				array('status' => true,
					'msg' => 'laporan berhasil di posting'), 200);
		}else{
			return response()->json(
				array('status' => false,
					'msg' => 'terjadi kesalahan pada memasukan data!'), 200);
		}
    }

	public function storeAktivitasPenangkapan(Request $request)
    {
        $param = array(
            'id_laporanpenangkapan' => $request->input('id_laporanpenangkapan'),
			'id_aktivitas' => $request->input('id_aktivitas'),
			'id_gerombolan' => $request->input('id_gerombolan'),
			'tanggal' => $request->input('tanggal'),
			'longitude' => $request->input('longitude'),
			'latitude' => $request->input('latitude'),
			'waktu_mulai' => $request->input('waktu_mulai_aktivitas'),
			'waktu_selesai' => $request->input('waktu_selesai_aktivitas'),
			'lama_penggunaanalat' => $request->input('lama_penggunaanalatpancing'),
			'jumlah_matapancing' => $request->input('jumlah_matapancing'),
			'jarak_antarmatapancing' => $request->input('jarak_antarmatapancing')
        );

        $result = DB::table('laporan_penangkapanaktivitasdetail')->insert($param);
			
		if($result){
			return response()->json(
				array('status' => true,
					'msg' => 'laporan berhasil di posting'), 200);
		}else{
			return response()->json(
				array('status' => false,
					'msg' => 'terjadi kesalahan pada memasukan data!'), 200);
		}
    }

	public function storeKomposisiPenangkapan(Request $request)
    {
        $param = array(
            'id_aktivitasdetail' => $request->input('id_aktivitasdetail'),
			'id_jenisikans' => $request->input('id_jenisikan'),
			'Ekor' => $request->input('ekor'),
			'KG' => $request->input('kg'),
        );

        $result = DB::table('laporan_penangkapankomposisi')->insert($param);
			
		if($result){
			return response()->json(
				array('status' => true,
					'msg' => 'laporan berhasil di posting'), 200);
		}else{
			return response()->json(
				array('status' => false,
					'msg' => 'terjadi kesalahan pada memasukan data!'), 200);
		}
    }

	public function searchkapal($kapal)
    {
		$result = DB::table('detail_kapal')
				->where('detail_kapal.nama_kapal', 'LIKE', '%' . $kapal . '%')
				->get();

		if($result){
			return response()->json(
                array('results' => $result,
				'status' => true,
                    'msg' => 'data berhasil diambil!'), 200);
		}
		else{
				return response()->json(
					array('status' => false,
						'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
			}
    }

	public function getUserAktivitas($userId)
    {
		$penangkapan_jenislog1 = DB::table('laporan_penangkapan')
						->where('laporan_penangkapan.id_user', '=', $userId)
						->where('laporan_penangkapan.id_log', '=', '1')
						->get();

		$penangkapan_jenislog2 = DB::table('laporan_penangkapan')
						->where('laporan_penangkapan.id_user', '=', $userId)
						->where('laporan_penangkapan.id_log', '=', '2')
						->get();
		
		$hasil = array();
		$log = array();
		foreach($penangkapan_jenislog1 as $p){
			$aktivitas = DB::select(
					DB::raw("SELECT
								CONCAT('Aktivitas tgl. ', tanggal) AS nama_aktivitas,
								B.*
							FROM
								laporan_penangkapan A
							RIGHT JOIN laporan_penangkapanaktivitasdetail B ON B.id_laporanpenangkapan = A.id_laporanpenangkapan
							WHERE
								A.id_user = ? AND 
								A.id_log = ?"),array($p->id_user, $p->id_log));
			

			array_push($log, array('detail' => $p, 'aktivitas' => $aktivitas));

		}

		foreach($penangkapan_jenislog2 as $p){
			$iduser=$p->id_user;

			$aktivitas = DB::select(
					DB::raw("SELECT
								CONCAT('Aktivitas tgl. ', tanggal) AS nama_aktivitas,
								B.*
							FROM
								laporan_penangkapan A
							RIGHT JOIN laporan_penangkapanaktivitasdetail B ON B.id_laporanpenangkapan = A.id_laporanpenangkapan
							WHERE
								A.id_user = ? AND
								A.id_log = ?"),array($p->id_user, $p->id_log));
			
			array_push($log, array('detail' => $p, 'aktivitas' => $aktivitas));
		}

		array_push($hasil, $log);
					
		if($penangkapan_jenislog1){
			return response()->json(
                array('log' => $hasil[0],
				'status' => true,
                'msg' => 'data berhasil diambil!'), 200);
		}
		else{
				return response()->json(
					array('status' => false,
						'msg' => 'terjadi kesalahan silahkan cek koneksi!'), 200);
			}
    }

}
