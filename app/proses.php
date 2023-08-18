<?php
// print_r($_POST);
// print_r($_FILES);
// die;

include_once "session.php";

class proses extends fb{
	protected
		$file		= 'db/database.json',
		$database	= [];
	public function __construct($id){
		// $this->getDatabase();
		if($id=='login'||$id=='logout'){
			$this->registered=false;
			$this->$id();
		}
		else if($id=='changeDbCheck'){
			$this->getDatabase();
			$this->$id();
		}
		else if($this->verification($id)){
			$this->getDatabase();
			$this->$id();
		}
    }

	private function logout(){
        $_SESSION = array();
        session_destroy();
		$this->registered=false;
		$this->retSuccess();
	}
	
	private function login(){
		$user	= isset($_POST['dt']['user'])?$_POST['dt']['user']:false;
		$pass	= isset($_POST['dt']['pass'])?$_POST['dt']['pass']:false;
		$this->getDatabase();
		$db		= $this->database;
		
		if(!$user||!$pass)$this->retError("Data tidak valid...");
		else if($user!=$db['akses']['user'] || $pass!=$db['akses']['pass'])$this->retError("Anda tidak memiliki akses...");
		else{
			$_SESSION["user_id"]			= $user;
			$this->registered=true;
			$this->retSuccess();
		}
	}
	private function resetDevice(){
		if($this->dt=='KONFIRMASI'){
			$file	= $this->file;
			if (file_exists($file)){
				unlink($file);
				$this->getDatabase();
				$this->logout();
			}
			else $this->retError('Database not found...');
		}
		else $this->retError('Not confirm...');
	}
	private function shutdown(){
		
		if($this->dt=='s')
			exec ("sudo shutdown -h now");
		else 
			exec ("sudo reboot");
		$this->retError('Gagal...');//jika nggak restart/shutdown ngirim ini
	}
	private function updateClock(){
		//update jam rtc
		$update = exec ('sudo hwclock --set --date="'.$this->dt.'" --localtime');
		//raspi update dari rtc
		exec ('sudo hwclock -s');
		if($update)
			$this->retError('Error : '.$update);
		else
			$this->retSuccess();
	}
	
	private function getDatabase(){
		$file			= $this->file;
		//database belum ada? bikin baru standard
		if (!file_exists($file)){
			$arr	= [
				'akses'	=> [
					'user'	=> 'admin',
					'pass'	=> 'admin'
				],
				'setting'	=> [
					'nama'		=> 'Musholla Ad-Din', // yang ikhlas bikin software nya ya.... ^___^
					'lokasi'	=> 'Bekasi',
					'latitude'	=> -6.14,
					'longitude'	=> 106.59,
					'timeZone'	=> 7,
					'dst'		=> '0'
				],
				'prayTimesMethod'	=> '0',//manual pilih parameter ajust.
				'prayTimesAdjust'	=> [
					'fajr'	=> 20,
					'dhuhr'	=> '',
					'asr'	=> 'Standard',
					'maghrib'	=> '',
					'isha'	=> 18
				],
				'prayTimesTune'	=> [	// geser jadwal sholat (menit)
					'fajr'		=> 0,
					'dhuhr'		=> 0,
					'asr'		=> 0,
					'maghrib'	=> 0,
					'isha'		=> 0
				],
				'prayName'	=> [
					'fajr'		=> 'Subuh',
					'dhuhr'		=> 'Dzuhur',
					'asr'		=> 'Ashar',
					'maghrib'	=> 'Maghrib',
					'isha'		=> 'Isya\''
				],
				'timeName'	=> [
					'Hours'		=> 'Jam',
					'Minutes'	=> 'Menit',
					'Seconds'	=> 'Detik',
				],
				'dayName'	=> [
					'Sunday'	=> 'Minggu',
					'Monday'	=> 'Senin',
					'Tuesday'	=> 'Selasa',
					'Wednesday'	=> 'Rabu',
					'Thursday'	=> 'Kamis',
					'Friday'	=> 'Jum\'at',
					'Saturday'	=> 'Sabtu'
				],
				'monthName'	=> [
					'January'		=> 'Januari',
					'February'		=> 'Februari',
					'March'			=> 'Maret',
					'April'			=> 'April',
					'May'			=> 'Mei',
					'June'			=> 'Juni',
					'July'			=> 'Juli',
					'August'		=> 'Agustus',
					'September'		=> 'September',
					'October'		=> 'Oktober',
					'November'		=> 'November',
					'December'		=> 'Desember'
				],
				'timer'	=> [
					'info'		=> 5,
					'wallpaper'	=> 15,
					'wait_adzan'=> 1,
					'adzan'		=> 3,
					'sholat'	=> 20
				],
				'iqomah'	=> [
					'fajr'		=> 10,
					'dhuhr'		=> 10,
					'asr'		=> 10,
					'maghrib'	=> 10,
					'isha'		=> 10
				],
				'jumat'	=> [	// buat sholat jumat, bisa dipilih aktif atau nggak
					'active'	=> true,
					'duration'	=> 60,	//durasi khutbah --> urutan : adzan-khutbah-sholat
					'text'		=> 'Harap diam saat khotib khutbah'
				],
				'tarawih'	=> [ // buat sholat tarawih, bisa dipilih aktif atau nggak
					'active'	=> true,
					'duration'	=> 180	//180 menit = 3 jam --> muncul display sholat --> urutan : adzan-iqomah-sholat(isya')-tarawih
				],
				'info'	=> [
					[
						'Aplikasi Display-Masjid',
						'Selamat datang di aplikasi Display Masjid, aplikasi baru saja diinstal dan belum ada data, silakan masuk ke menu admin untuk mengganti data',
						'Display|Masjid V.1.0.0',
						true	// active?
					],
					[
						'Info non-active',
						'Ini contoh info tidak aktif',
						'active = false',
						false
					],
					[
						'سَوُّوا صُفُوفَكُمْ , فَإِنَّ تَسْوِيَةَ الصَّفِّ مِنْ تَمَامِ الصَّلاةِ',
						'Luruskanlah shaf-shaf kalian, karena lurusnya shaf adalah kesempurnaan shalat',
						'HR. Bukhari no.690, Muslim no.433',
						true
					]
					
				],
				'running_text' => [
					'Selamat datang di aplikasi Display-Masjid',
					'Silakan masuk ke menu admin untuk mengubah data'
				]
			];
			$myfile = fopen($file, "w") or $this->retError("Error Create File...");
			fwrite($myfile, json_encode($arr));
			fclose($myfile);
		}
		
		$json 	= file_get_contents($file);
		$this->database	= json_decode($json, true);
		// echo '<pre>'.print_r($this->database,1).'</pre>';
	}
	
	
	private function readDatabase(){
		// echo '<pre>'.print_r($this->database,1).'</pre>';
		// $this->info();
		$db		= $this->database;
		unset($dt['akses']);
		$this->data = $db;
		$this->retSuccess();
	}
	
	private function changeDbCheck(){
		$db		= $this->database;
		$wp		= $this->getWallpaper();
		$logo	= 'display/logo/'.$this->getLogo();
		
		$combine	= json_encode($db).json_encode($wp).filesize($logo);
		$this->data = sha1($combine).strlen($combine);//hemat ram... hahaha....
		$this->retSuccess();
	}
	
	private function saveDatabase(){
		$file	= $this->file;
		$myfile = fopen($file, "w") or $this->retError("Error Create File...");
		fwrite($myfile, json_encode($this->database));
		fclose($myfile);
	}
	
	
	private function formSave(){
		$dt		= $this->dt;
		$db		= $this->database;
		
		$id		= $dt['formId'];
		$index	= $dt['index'];
		unset($dt['formId']);
		unset($dt['index']);
		
		if($id=='info')	$dt	= [$dt['r1'],$dt['r2'],$dt['r3'],$dt['active']];
		else if($id=='running_text')	$dt	= $dt['text'];
		else if($id=='prayTimesAdjust'){
			$db['prayTimesMethod']	= $dt['prayTimesMethod'];
			unset($dt['prayTimesMethod']);
		}
		else if($id=='gantiPass'){
			if($dt['password_lama']!=$db['akses']['pass'])
				$this->retError('Password lama salah...');
			else if	($dt['password_baru']!=$dt['ulangi_password_baru'])
				$this->retError('Password baru tidak sama...');
			else if	(strlen($dt['password_baru'])<8)
				$this->retError('Password terlalu pendek, minimal 8 karakter...');
			else {
				$dt		= $dt['password_baru'];
				$id		= 'akses';
				$index	= 'pass';
			}
		}
		
		if($index=='no-index')
			// $db[$id]	= $dt;
			$db[$id] = array_merge($db[$id],$dt);
		else if($index=='new')
			$db[$id][]	= $dt;
		else 
			$db[$id][$index]	= $dt;
		
		// unset($db['akses']);//proteksi biar user/password gak kebaca 
		// $this->data = $db;
		$this->database = array_merge($this->database,$db);
		$this->saveDatabase();
		$this->retSuccess();
	}
	
	private function saveWallpaper(){
		// $this->data	= $_FILES;
		
		if(isset($_FILES)){
			$allowed_ext =  array('jpg');
			$i=0;
			foreach($_FILES as $file){
				if($file['size']>0){
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					if(!in_array($ext,$allowed_ext) ) {
						$this->retError($file['name']." tidak didukung\nExt yang diperbolehkan : ".implode(", ",$allowed_ext));
					}
					else {
						// move_uploaded_file($file['tmp_name'], "display/wallpaper/".time().'__'.$file['name']);
						// move_uploaded_file($file['tmp_name'], "display/wallpaper/".time().'.'.$ext);
						move_uploaded_file($file['tmp_name'], "display/wallpaper/".time().$i.'.'.$ext);
						// $this->writeFeedBackError('upload ok');
					}
				}
				$i++;
			}
			// $this->writeFeedBackError('upload ok');
		}
		
		
		$this->retSuccess();
	}
	
	private function saveLogo(){
		if(isset($_FILES)){
			$allowed_ext =  array('png');
			$i=0;
			foreach($_FILES as $file){
				if($file['size']>0){
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					if(!in_array($ext,$allowed_ext) ) {
						$this->retError($file['name']." tidak didukung\nExt yang diperbolehkan : ".implode(", ",$allowed_ext));
					}
					else {
						// tricky ==> kalo replace file logo.png ==> file di browser masih kesimpen di cache ==> ngeselin	
						$oldLogo	= 'display/logo/'.$this->getLogo();
						if(file_exists($oldLogo)) unlink($oldLogo);
						move_uploaded_file($file['tmp_name'], "display/logo/".time().'.'.$ext);
						// move_uploaded_file($file['tmp_name'], "display/img/logo.".$ext);//ganti logo masih kesimpen  di cache browser
					}
				}
				$i++;
			}
		}
		$this->retSuccess();
	}
	
	private function getWallpaper(){
		$dir	= 'display/wallpaper/';
		$files	= array_diff(scandir($dir),array('.','..','Thumbs.db'));
		return $files;
		
	}
	private function getLogo(){
		$dir	= 'display/logo/';
		$files	= array_diff(scandir($dir),array('.','..','Thumbs.db'));
		$files	= array_values($files);//re index
		return $files[0];
		
	}
	private function wallpaperDelete(){
		if(count($this->getWallpaper())<2){
			$this->retError('minimal harus ada 1 wallpaper');
		}
		else{
			$dir	= 'display/wallpaper/';
			$file	= $this->dt;
			// $this->retError($file);die;
			if(file_exists($dir.$file)) unlink($dir.$file);
			$this->retSuccess();
		}
	}
	private function formDelete(){
		$dt		= $this->dt;
		$db		= $this->database;
		$id		= $dt['formId'];
		$index	= $dt['index'];
		if(count($db[$id])<2){
			$this->retError("Minimal harus ada 1 data...");
		}
		else{
			unset($db[$id][$index]);
			$db[$id] = array_values($db[$id]);//re-index
			$this->database = $db;
			$this->saveDatabase();
			$this->retSuccess();
		}
	}
	
	
		
	/* *****************************************************************************************************************
	 * *** VIEW
	 * *****************************************************************************************************************/
	
	private function info(){
		$db	= $this->database;
		$id	= 'info';
		ob_start();
		$arrActive			= ['Ya'	=> 1, 'Tidak' => 0];
		$db[$id]['new']		= ['','','',true];
		// print_r( $this->getLogo());
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		foreach($db[$id] as $k => $v){
			$optActive	= '';
			foreach($arrActive as $ka => $va){
				$selected	= $va==$v[3]?'selected':'';
				$optActive	.= '<option '.$selected.' value="'.$va.'">'.$ka.'</option>';
			}
			$title	= is_int($k)?'Info '.($k+1):'Info Baru';
			$delBtn	= is_int($k)?'<button type="button" class="btn btn-danger delete"><i class="fa fa-trash" aria-hidden="true"></i> hapus</button>':'';
			?>
			<form method="post" class="form">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title"><?=$title?></h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="input-group">
					  <span class="input-group-addon">Header</span>
					  <input name="r1" type="text" maxlength="100" class="form-control" value="<?=$v[0]?>" required>
					</div>
					<div class="input">
					  <textarea name="r2" maxlength="255" rows="3" class="form-control" ><?=$v[1]?></textarea>
					</div>
					<div class="input-group">
					  <span class="input-group-addon">Footer</span>
					  <input name="r3" type="text" maxlength="100" class="form-control" value="<?=$v[2]?>" placeholder="boleh dikosongkan">
					</div>
					<div class="input-group">
					  <span class="input-group-addon">Aktif</span>
					  <select name="active" class="form-control  input-sm" required><?=$optActive?></select>
					</div>
					<div class="form-group">
						<input type="hidden" name="formId" value="<?=$id?>">
						<input type="hidden" name="index" value="<?=$k?>">
					</div>
				</div>
				<div class="box-footer">
					<?=$delBtn?>
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> simpan</button>
				</div>
			</div>
			</form>
			<?php
		}
		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
		// echo $my_var;
	}
	private function wallpaper(){
		$db	= $this->database;
		$id	= 'wallpaper';
		$wp	= $this->getWallpaper();
		ob_start();
		echo '
			<section class="content-header content-dynamic section-wallpaper">
			<div class="row">
		';
		// echo '<pre>'.print_r($wp,1).'</pre>';
		?>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<form method="post" class="form-file" enctype="multipart/form-data">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Tambah wallpaper</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="input-group">
					  <span class="input-group-addon">File wallpaper</span>
					  <input type="file" multiple="" class="form-control input-sm" placeholder="" data-proses="saveWallpaper">
					</div>
					<div class="input">
						<small>
						- Ext file yang didukung :  <b>.jpg</b><br>
						- Ukuran maksimal <b>2Mb</b><br>
						- Maksimal 5 file dalam sekali upload<br>
						- Tips : Jika ukuran gambar > 2Mb, cara cepat kompres gambar ⇒ kirim ke whatsapp :P
						</small>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-upload" aria-hidden="true"></i> upload</button>
				</div>
			</div>
			</form>
		</div>
		<?php
		foreach($wp as $v):
		?>
		<div class="col-md-4 col-sm-6 col-xs-12">
          <div class="small-box" style="background-image: url(display/wallpaper/<?=$v?>);">
            <div class="inner"></div>
            <a href="javascript:void(0)" data-file="<?=$v?>" class="small-box-footer"><i class="fa fa-trash"></i> delete</a>
          </div>
        </div>
		<?php 
		endforeach;
		echo '</div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
		// echo $my_var;
	}
	
	private function running_text(){
		$db	= $this->database;
		$id	= 'running_text';
		ob_start();
		$db[$id]['new']	= '';  
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		foreach($db[$id] as $k => $v){
			$title	= is_int($k)?'Teks '.($k+1):'Teks Baru';
			$delBtn	= is_int($k)?'<button type="button" class="btn btn-danger delete"><i class="fa fa-trash" aria-hidden="true"></i> hapus</button>':'';
			?>
			<form method="post" class="form">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title"><?=$title?></h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="input">
					  <textarea name="text" maxlength="255" rows="3" class="form-control" required><?=$v?></textarea>
					</div>
					<div class="input">
						<input type="hidden" name="formId" value="<?=$id?>">
						<input type="hidden" name="index" value="<?=$k?>">
					</div>
				</div>
				<div class="box-footer">
					<?=$delBtn?>
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> simpan</button>
				</div>
			</div>
			</form>
			<?php
		}
		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
		// echo $my_var;
	}
	
	private function timer(){
		$db	= $this->database;
		$id	= 'timer';
		
		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		
		//timer
		$timer		= $db['timer'];
		$formTimer	= [];
		foreach($timer as $k => $v){
			$formTimer[$k]	= [
				'type'	=> 'number',
				'min'	=> 1,
				'max'	=> 180,
				'step'	=> 1,
				'value'	=> $v,
				'placeholder'	=> '1-180',
				'required'	=> true,
				'addon'	=> 'menit'
			];
			if($k=='info'||$k=='wallpaper'){
				$formTimer[$k]['max']			= 86400;
				$formTimer[$k]['addon']			= 'detik';
				$formTimer[$k]['placeholder']	= '1-86400';
			}
		}
		$setTimer	= [
			'id'=>'timer',
			'title'=>'Timer'
		];
		echo $this->generateCompleteForm($formTimer,$setTimer);
		
		//form iqomah
		$iqomah		= $db['iqomah'];
		$formIqomah	= [];
		foreach($iqomah as $k => $v){
			$formIqomah[$k]	= [
				'type'	=> 'number',
				'min'	=> 1,
				'max'	=> 180,
				'step'	=> 1,
				'value'	=> $v,
				'required'	=> true,
				'placeholder'	=> '1-180',
				'addon'	=> 'menit'
			];
		}
		$setIqomah	= [
			'id'=>'iqomah',
			'title'=>'Timer Iqomah'
		];
		echo $this->generateCompleteForm($formIqomah,$setIqomah);
		
		// form Sholat jum'at
		$jumat		= $db['jumat'];
		$arrActive	= ['Ya'	=> 1, 'Tidak' => 0];
		$formJumat	= [
			'aktif' => [
				'name'	=> 'active',
				'type'	=> 'select',
				'arr'	=> $arrActive,
				'value'	=> $jumat['active']
			],
			'khutbah'	=>[
				'name'	=> 'duration',
				'type'	=> 'number',
				'min'	=> 1,
				'max'	=> 180,
				'step'	=> 1,
				'value'	=> $jumat['duration'],
				'required'	=> true,
				'addon'	=> 'menit'
			],
			'text'	=>[
				'type'	=> 'text',
				'maxlength'	=> 100,
				'value'	=> $jumat['text'],
				'required'	=> true
			],
		];
		$setJumat	= [
			'id'	=> 'jumat',
			'title'	=> 'Sholat jum\'at (opsional)'
		];
		echo $this->generateCompleteForm($formJumat,$setJumat);
		
		// form Sholat tarawih
		$tarawih	= $db['tarawih'];
		$formTarawih= [
			'aktif' => [
				'name'	=> 'active',
				'type'	=> 'select',
				'arr'	=> $arrActive,
				'value'	=> $tarawih['active']
			],
			'durasi'	=>[
				'name'	=> 'duration',
				'type'	=> 'number',
				'min'	=> 1,
				'max'	=> 180,
				'step'	=> 1,
				'value'	=> $tarawih['duration'],
				'required'	=> true,
				'addon'	=> 'menit'
			]
		];
		$setTarawih	= [
			'id'	=> 'tarawih',
			'title'	=> 'Sholat tarawih (opsional)',
			'info'	=> 'Jika diperlukan, aktifkan hanya di bulan ramadhan'
		];
		echo $this->generateCompleteForm($formTarawih,$setTarawih);
		
		echo '</div></div></section>';
		$this->data .= ob_get_clean();
		$this->retSuccess();
		
	}
	
	private function jadwal(){
		$db		= $this->database;
		$method	= $db['prayTimesMethod'];
		$adjust	= $db['prayTimesAdjust'];
		
		$arrMethod	= [
			'0'			=> 'Manual parameter',
			'MWL'		=> 'Muslim World League',
			'ISNA'		=> 'Islamic Society of North America',
			'Egypt'		=> 'Egyptian General Authority of Survey',
			'Makkah'	=> 'Umm al-Qura University, Makkah',
			'Karachi'	=> 'University of Islamic Sciences, Karachi',
			'Tehran'	=> 'Institute of Geophysics, University of Tehran',
			'Jafari'	=> 'Shia Ithna Ashari (Ja`fari)'
		];
		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		?>
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs pull-right">
				  <li><a href="#info" data-toggle="tab"><i class="fa fa-info-circle"></i></a></li>
				  <li><a href="#parameter" data-toggle="tab">Parameter</a></li>
				  <li class="active"><a href="#metode" data-toggle="tab">Metode</a></li>
				  <li class="pull-left header"><i class="fa fa-inbox"></i>Library</li>
				</ul>
				<div class="tab-content">
				  <!-- Morris chart - Sales -->
				  <div class="tab-pane" id="info" >
					Perhitungan waktu sholat menggunakan library dari <a href="http://praytimes.org/" target="_blank">praytimes.org</a>, Untuk manual lebih detail bisa di cek pada halaman situs tersebut.<br>
					Library yang dipakai <b>PrayTimes Version 2.3</b> (versi terbaru pada saat aplikasi ini dibuat)<br><br>
					Untuk mempermudah, setting parameter yang bisa di ganti hanya <b>fajr, dhuhr, asr, maghrib, isha</b> menyesuaikan tampilan pada display. Jika parameter tidak perlu diganti kosongkan saja (diisi default)
					
					<br><br>
					Contoh penggunaan untuk kota bekasi mengkuti metode kemenag bekasi :
					<pre>
latitude	= -6.14
longitude	= 106.59
timeZone	= 7 (GMT +7)
fajr		= 20°
asr		= Standard (Shafii, Maliki, Jafari and Hanbali / shadow factor = 1)
isha		= 18°
					</pre>
					<small>Default aplikasi ini menggunakan setting <b>bekasi - jawa barat - indonesia</b> dengan metode seperti diatas</small>
				  </div>
				  <div class="tab-pane" id="parameter">
					<h4>Parameters</h4>
					<table class="table table-condensed">
						<thead>
							<tr>
								<th>Parameter
								</th>
								<th>Values
								</th>
								<th>Description
								</th>
								<th>Sample Value
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> fajr </td>
								<td> degrees </td>
								<td> twilight angle </td>
								<td> 15
								</td>
							</tr>
							<tr>
								<td> dhuhr </td>
								<td> minutes </td>
								<td> minutes after mid-day </td>
								<td> 1 min
								</td>
							</tr>
							<tr>
								<td rowspan="2"> asr
								</td>
								<td> method </td>
								<td> asr juristic method; see the table below </td>
								<td> Standard
								</td>
							</tr>
							<tr>
								<td> factor </td>
								<td> shadow length factor for realizing asr </td>
								<td> 1.7
								</td>
							</tr>
							<tr>
								<td rowspan="2"> maghrib
								</td>
								<td> degrees </td>
								<td> twilight angle </td>
								<td> 4
								</td>
							</tr>
							<tr>
								<td> minutes </td>
								<td> minutes after sunset </td>
								<td> 15 min
								</td>
							</tr>
							<tr>
								<td rowspan="2"> isha
								</td>
								<td> degrees </td>
								<td> twilight angle </td>
								<td> 18
								</td>
							</tr>
							<tr>
								<td> minutes </td>
								<td> minutes after maghrib </td>
								<td> 90 min
								</td>
							</tr>
						</tbody>
					</table>
					
					<h4>Asr methods</h4>
					<table class="table table-condensed table-striped">
						<thead>
							<tr>
								<th>Method
								</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> Standard </td>
								<td> Shafii, Maliki, Jafari and Hanbali (shadow factor = 1)
								</td>
							</tr>
							<tr>
								<td> Hanafi </td>
								<td> Hanafi school of tought (shadow factor = 2)
								</td>
							</tr>
						</tbody>
					</table>
					
					<b>Contoh penggunaan:</b><br>
					- Asr menggunakan metode <i>Shafii, Maliki, Jafari and Hanbali</i>, maka diisi : <b>Standard</b><br>
					- Asr menggunakan metode <i>Hanafi school of tought</i>, maka diisi : <b>Hanafi</b><br>
					- Asr menggunakan <i>shadow factor 1.5</i>, maka diisi : <b>1.5</b><br>
					- Isha menggunakan <i>twilight angle (18.5 deg)</i>, maka diisi : <b>18.5</b><br>
					- Isha menggunakan <i>85 minutes after maghrib</i>, maka diisi : <b>85 min</b> <a style="color:#00F">(85 spasi min)</a><br>
					- dst...
					
					
				  </div>
				  <div class="tab-pane active" id="metode" >
					<h4>Calculation Methods</h4>
					<table class="table table-condensed table-striped">
						<thead>
							<tr>
								<th>Method
								</th>
								<th>Abbr.
								</th>
								<th>Region Used
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> Muslim World League </td>
								<td> MWL </td>
								<td> Europe, Far East, parts of US
								</td>
							</tr>
							<tr>
								<td> Islamic Society of North America </td>
								<td> ISNA </td>
								<td> North America (US and Canada)
								</td>
							</tr>
							<tr>
								<td> Egyptian General Authority of Survey </td>
								<td> Egypt </td>
								<td> Africa, Syria, Lebanon, Malaysia
								</td>
							</tr>
							<tr>
								<td> Umm al-Qura University, Makkah </td>
								<td> Makkah </td>
								<td> Arabian Peninsula
								</td>
							</tr>
							<tr>
								<td> University of Islamic Sciences, Karachi </td>
								<td> Karachi &nbsp; </td>
								<td> Pakistan, Afganistan, Bangladesh, India
								</td>
							</tr>
							<tr>
								<td> Institute of Geophysics, University of Tehran </td>
								<td> Tehran </td>
								<td> Iran, Some Shia communities
								</td>
							</tr>
							<tr>
								<td> Shia Ithna Ashari, Leva Research Institute, Qum &nbsp; </td>
								<td> Jafari </td>
								<td> Some Shia communities worldwide
								</td>
							</tr>
						</tbody>
					</table>
					
					<h4>Calculating Parameters</h4>
					<table class="table table-condensed table-striped">
						<thead>
							<tr>
								<th>Method &nbsp;
								</th>
								<th>Fajr Angle
								</th>
								<th>Isha
								</th>
								<th>Maghrib
								</th>
								<th>Midnight
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> MWL </td>
								<td> 18° </td>
								<td> 17° </td>
								<td> = Senset </td>
								<td> mid Sunset to Sunrise
								</td>
							</tr>
							<tr>
								<td> ISNA </td>
								<td> 15° </td>
								<td> 15° </td>
								<td> = Senset </td>
								<td> mid Sunset to Sunrise
								</td>
							</tr>
							<tr>
								<td> Egypt </td>
								<td> 19.5° </td>
								<td> 17.5° </td>
								<td> = Senset </td>
								<td> mid Sunset to Sunrise
								</td>
							</tr>
							<tr>
								<td> Makkah </td>
								<td> 18.5° </td>
								<td> 90 min after Maghrib
									<br>120 min during Ramadan </td>
								<td> = Senset </td>
								<td> mid Sunset to Sunrise
								</td>
							</tr>
							<tr>
								<td> Karachi </td>
								<td> 18° </td>
								<td> 18° </td>
								<td> = Senset </td>
								<td> mid Sunset to Sunrise
								</td>
							</tr>
							<tr>
								<td> Tehran </td>
								<td> 17.7° </td>
								<td> 14° </td>
								<td> 4.5° </td>
								<td> mid Sunset to Fajr
								</td>
							</tr>
							<tr>
								<td> Jafari </td>
								<td> 16° </td>
								<td> 14° </td>
								<td> 4° </td>
								<td> mid Sunset to Fajr
								</td>
							</tr>
						</tbody>
					</table>
					
				  </div>
				</div>
			</div>
				
			<form method="post" class="form">
			<div class="box box-warning">
				<div class="box-header with-border">
					<h3 class="box-title">Metode</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="input">
						<select class="form-control" name="prayTimesMethod" id="prayTimesMethod">
							<?=$this->generateOptionSelect($method,$arrMethod,false)?>
						</select>
					</div>
					<div id="prayTimesAdjust" style="display:none">
						<?=$this->formPrayTimesAdjust($adjust) ?>
						<div class="form-group">
							<small>
								- Lihat manual parameter (diatas) untuk cara pengisian.<br>
								- Parameter <b>case sensitive</b> (contoh : <b>Standard</b> tidak sama dengan <b>standard</b>)<br>
								- Jika dikosongkan maka akan diisi default.
							</small>
							<input type="hidden" name="formId" value="prayTimesAdjust">
							<input type="hidden" name="index" value="no-index">
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> simpan</button>
				</div>
			</div>
			</form>
		<?php
		
		$seting	= $db['setting'];
		$location 	= [
			'latitude'	=> [
				'type'	=> 'number',
				'min'	=> -999.0000001,
				'max'	=> 999.9999999,
				'step'	=> 0.0000001,
				'value'	=> $seting['latitude'],
				'required'	=> true,
				'addon'	=> '°'
			],
			'longitude'	=> [
				'type'	=> 'number',
				'min'	=> -999.0000001,
				'max'	=> 999.9999999,
				'step'	=> 0.0000001,
				'value'	=> $seting['longitude'],
				'required'	=> true,
				'addon'	=> '°'
			],
			'timeZone'	=> [
				'type'	=> 'number',
				'min'	=> -11,
				'max'	=> 12,
				'step'	=> 1,
				'value'	=> $seting['timeZone'],
				'required'	=> true,
				'placeholder'	=> 'GMT-11 to GMT+12',	
				'addon'	=> 'GMT'
			],
			'dst'		=> [
				'type'	=> 'select',
				'arr'	=> ['0'=>'0','1'=>'1','Auto'=>'auto'],
				'value'	=> $seting['dst'],
				'required'	=> true
			],
		];
		$set = [
			'id'	=> 'setting',
			'title'	=> 'Lokasi',
			'info'	=> '<b>DST</b> = Daylight Saving Time (Waktu Musim Panas)
						Waktu resmi dimajukan (biasanya) satu jam lebih awal dari zona waktu standar dan diberlakukan selama musim semi dan musim panas (berlaku untuk wilayah eropa)
						Untuk wilayah indonesia isi 0.
			'
		];
		echo $this->generateCompleteForm($location,$set);
		
		$tune	= $db['prayTimesTune'];
		$tune_	= [];
		foreach($tune as $k=>$v){
			$tune_[$k]	= [
				'type'	=> 'number',
				'min'	=> -60,
				'max'	=> 60,
				'step'	=> 1,
				'value'	=> $v,
				'required'	=> true,
				'placeholder'	=> '-60 to 60',	
				'addon'	=> 'menit'
			];
		}
		$set = [
			'id'	=> 'prayTimesTune',
			'title'	=> 'Penyesuaian waktu sholat',
			'info'	=> '- Untuk menyesuaikan waktu sholat -60 sampai +60 menit.
						- Contoh penggunaan : jadwal ditambahkan +2 menit untuk ihtiyati (pengaman)
			'
		];
		echo $this->generateCompleteForm($tune_,$set);
		
		
		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
		
	}
	
	
	private function pengaturan(){
		$db	= $this->database;
		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		?>
		<form method="post" class="form-file" enctype="multipart/form-data">
		<div class="box box-success ">
			<div class="box-header with-border">
				<h3 class="box-title">Logo</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body" style="background-image: url(dist/img/bgTransparent.jpg);">
				<img class="img-responsive pad" src="display/logo/<?=$this->getLogo();?>" style="border:2px dashed #F00;padding:0">
			</div>
			<div class="box-body">
				<div class="input-group">
				  <span class="input-group-addon">File logo</span>
				  <input type="file" class="form-control input-sm" placeholder="" data-proses="saveLogo">
				</div>
				<div class="input">
					<small>
					- Ext file yang didukung :  <b>.png</b><br>
					- Ukuran maksimal <b>2Mb</b><br>
					- Tips : jika logo tampil terlalu besar pada display, edit gambar pada image editor (contoh : photoshop) dan beri jarak kosong pada atas-bawah atau kiri-kanan gambar
					</small>
				</div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-upload" aria-hidden="true"></i> upload</button>
			</div>
		</div>
		</form>
		<?php
		
		$setting 	= $db['setting'];
		unset($setting['latitude']);
		unset($setting['longitude']);
		unset($setting['timeZone']);
		unset($setting['dst']);
		$setSetting = [
			'id'	=> 'setting',
			'title'	=> 'Detail masjid/musholla',
			'color'	=> 'box-success',
			'info'	=> '- Data ini opsional (bisa dikosongkan)
			',
			'open'	=> false
		];
		echo $this->generateTextForm($setting,$setSetting,false);
		
		$dataPass 	= [
			'password lama'	=> [
				'name'		=> 'password_lama',
				'type'		=> 'password',
				'maxlength'	=> 20,
				'required'	=> true
			],
			'password baru'	=> [
				'name'		=> 'password_baru',
				'type'		=> 'password',
				'maxlength'	=> 20,
				'required'	=> true
			],
			'ulangi password'	=> [
				'name'		=> 'ulangi_password_baru',
				'type'		=> 'password',
				'maxlength'	=> 20,
				'required'	=> true
			],
		];
		
		$setPass = [
			'id'	=> 'gantiPass',
			'title'	=> 'Ganti password admin',
			'color'	=> 'box-danger',
			'info'	=> '- Password default : <b>admin</b>
						- Jangan mengganti password dengan \'admin\'
						- Tips : gunakan campuran angka dan huruf untuk memperkuat password.
			',
			'open'	=> false
		];
		echo $this->generateCompleteForm($dataPass,$setPass);
		
		$prayName 	= $db['prayName'];
		$set = [
			'id'	=> 'prayName',
			'title'	=> 'Nama sholat',
			'open'	=> false
		];
		echo $this->generateTextForm($prayName,$set);
		
		$timeName 	= $db['timeName'];
		$set = [
			'id'	=> 'timeName',
			'title'	=> 'Nama waktu',
			'open'	=> false
		];
		echo $this->generateTextForm($timeName,$set);
		
		$dayName 	= $db['dayName'];
		$set = [
			'id'	=> 'dayName',
			'title'	=> 'Nama hari',
			'open'	=> false
		];
		echo $this->generateTextForm($dayName,$set);
		
		$monthName 	= $db['monthName'];
		$set = [
			'id'	=> 'monthName',
			'title'	=> 'Nama bulan',
			'open'	=> false
		];
		echo $this->generateTextForm($monthName,$set);
		
		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
		// echo $my_var;
	}
	
	private function simulasi(){
		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
		';
		?>
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">Simulasi jadwal sholat</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="row date-navigation month-picker" style="text-align:center">
					<button class="btn btn-info  prev"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Prev</button>
					<input style="width:120px" type="text" class="btn picker btn-info" value="Hari ini" readonly>
					<button class="btn btn-info next">Next <i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
				</div>
				<div class="table-responsive">
				</div>
			</div>
		</div>
		<?php
		echo '</div></div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
	}
	private function getPraySetting(){
		$db	= $this->database;
		$this->data['setting']			= $db['setting'];
		$this->data['prayTimesMethod']	= $db['prayTimesMethod'];
		
		$prayTimesAdjust	= [];
		foreach($db['prayTimesAdjust'] as $k => $v){
			if(strlen(trim($v))>0) $prayTimesAdjust[$k]=$v;
		}
		$this->data['prayTimesAdjust']	= $prayTimesAdjust;
		
		$prayTimesTune		= [];
		foreach($db['prayTimesTune'] as $k => $v){
			if($v<0 || $v>0) $prayTimesTune[$k]=$v;
		}
		
		$this->data['prayTimesTune']	= $prayTimesTune;
		$this->data['items']			= array_keys($db['prayName']);
		$this->data['thead']			= array_values($db['prayName']);
		array_unshift($this->data['thead'], 'Tgl');
		
		
		$this->retSuccess();
	}
	private function about(){
		ob_start();
		?>
		<section class="content-header content-dynamic">
			<div class="row">
				<div class="col-md-6">
				  <!-- Widget: user widget style 1 -->
				  <div class="box box-widget widget-user-2">
					<!-- Add the bg color to the header using any of the bg-* classes -->
					<div class="widget-user-header bg-aqua-active">
					  <div class="widget-user-image">
						<div style="width:65px;height:65px;background:#563eae;position:absolute;border-radius:60px 35px 0 35px;font-size:38px;padding:15px 10px;box-shadow:3px 3px 10px 0 rgba(0,0,0,0.4);overflow:hidden;transform: rotate(-135deg); color:#00a7d0">
						dm
						</div>
					  </div>
					  <!-- /.widget-user-image -->
					  <h3 class="widget-user-username">Display|Masjid</h3>
					  <h5 class="widget-user-desc">Media informasi untuk masjid/musholla</h5>
					</div>
					<div class="box-footer no-padding" style="overflow:hidden">
					  <ul class="nav nav-stacked">
						<li>
							<a class="row">
								<div class="col-xs-5" style="text-align:right">Version</div>
								<div class="col-xs-7"><span class="badge bg-blue">1.0.0</span></div>
							</a>
						</li>
						<li>
							<a class="row">
								<div class="col-xs-5" style="text-align:right">Date</div>
								<div class="col-xs-7"><span class="badge bg-aqua">Feb 2020</span></div>
							</a>
						</li>
						<li>
							<a class="row">
								<div class="col-xs-5" style="text-align:right">Program</div>
								<div class="col-xs-7">fahroni|ganteng</div>
							</a>
						</li>
						<li>
							<a class="row">
								<div class="col-xs-5" style="text-align:right">Display design</div>
								<div class="col-xs-7">Rakel</div>
							</a>
						</li>
						<li>
							<a class="row">
								<div class="col-xs-5" style="text-align:right">License</div>
								<div class="col-xs-7">Berbayar, sangat mahal sekali.... :P</div>
							</a>
						</li>
					  </ul>
					</div>
				  </div>
				  <!-- /.widget-user -->
			</div>
		</div></section>
		<?php
		$this->data = ob_get_clean();
		$this->retSuccess();
	}
	private function sistem(){
		$temp	= exec("/opt/vc/bin/vcgencmd measure_temp | egrep -o '[0-9]*\.[0-9]*'");
		// $temp	= exec("/opt/vc/bin/vcgencmd measure_temp");
		ob_start();
		echo '
			<section class="content-header content-dynamic">
			<div class="row">
		';
		?>
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Update Jam</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label>Jam display</label>
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-clock-o"></i>
						  </div>
						  <input type="text" class="form-control pull-right" value="<?=date('Y-m-d H:i:s')?>" disabled>
						</div>
					</div>
					<div class="form-group">
						<label>Jam lokal (HP/PC)</label>
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-clock-o"></i>
						  </div>
						  <input type="text" class="form-control pull-right" id="jamLokal" disabled>
						</div>
					</div>
					<div class="form-group">
						<small>
							Update jam akan mengganti jam pada display menyesuaikan dengan jam pada HP/PC ini. 
						</small>
					</div>
				</div>
				<div class="box-footer">
					<!--<a class="btn btn-app pull-right"><i class="fa fa-clock-o"></i> Update</a>-->
					<button type="submit" class="btn btn-default" onclick="$('.sidebar-menu .active a').trigger('click')"><i class="fa fa-clock-o" aria-hidden="true"></i> Refresh </button>
					<button type="submit" class="btn btn-primary pull-right" onclick="app.updateClock(this)"><i class="fa fa-clock-o" aria-hidden="true"></i> Update </button>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="box box-default">
				<div class="box-header with-border">
					<h3 class="box-title">Device</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label>Temperature</label>
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-thermometer"></i>
						  </div>
						  <input type="text" class="form-control pull-right" value="<?=$temp?> &#176;C" disabled>
						</div>
					</div>
					<div class="input">
						- Range temperature normal 0 - 70 &#176;C<br>
						- Alarm overheat > 80 &#176;C 
					</div>
				</div>
				<div class="box-footer">
					<button  class="btn btn-app" onclick="$('.sidebar-menu .active a').trigger('click')"><i class="fa fa-thermometer"></i> Refresh </button>
					<button class="btn btn-app pull-right" onclick="app.shutdown(this,'r')"><i class="fa fa-repeat"></i> Restart</button>
					<button class="btn btn-app pull-right" onclick="app.shutdown(this,'s')"><i class="fa fa-power-off"></i> Shutdown</button>
				</div>
			</div>
		</div>		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Reset pengaturan awal</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					- Semua data setting akan di reset ke pengaturan awal.<br>
					- Logo dan wallpaper tidak berubah.<br>
					- Akses login kembali ke awal (user:admin, password:admin)<br>
					- Jika berhasil, akan masuk ke halaman login.
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-danger pull-right" onclick="app.resetDevice(this)"><i class="fa fa-refresh" aria-hidden="true"></i> Reset </button>

				</div>
			</div>
		</div>
		<?php
		echo '</div></section>';
		$this->data = ob_get_clean();
		$this->retSuccess();
	}
	private function generateCompleteForm($arr,$setting=[]){
		$default = [
			'id'	=> '',
			'title'	=> '',
			'color'	=> 'box-info',
			'index'	=> 'no-index',
			'info'	=> false,
			'open'	=> true
		];
		$set	= array_merge($default,$setting);
		$icon	= $set['open']?'fa-minus':'fa-plus';
		$class	= $set['color'].($set['open']?'':' collapsed-box');
		$form	= '
			<form method="post" class="form">
			<div class="box '.$class.'">
				<div class="box-header with-border">
					<h3 class="box-title">'.$set['title'].'</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa '.$icon.'"></i></button>
					</div>
				</div>
				<div class="box-body">';
					foreach($arr as $k => $v){
						$required	= '';
						if(array_key_exists('required', $v)){
							$required	= $v['required']?' required':'';
							unset($v['required']);
						}
						if($v['type']=='select'){
							$reverse	= true;
							if(array_key_exists('rev', $v)){
								$reverse	= $v['rev'];
							}
							$form	.= '
							<div class="input-group">
								<span class="input-group-addon">'.$k.'</span>
								<select class="form-control"';
									$form	.= array_key_exists('name', $v)?' name="'.$v['name'].'"':' name="'.$k.'"';
									$form	.= $required.'>'.$this->generateOptionSelect($v['value'],$v['arr'],$reverse).
								'</select>'.
							'</div>';
						}
						else{
							$form	.= '
							<div class="input-group">
								<span class="input-group-addon">'.$k.'</span>
								<input class="form-control"';
								$addon	= '';
								if(!array_key_exists('name', $v))$form.=' name="'.$k.'"';
								foreach($v as $kr => $vr){
									if($kr=='required')$form	.= " required";
									else if($kr=='addon') $addon = '<span class="input-group-addon">'.$vr.'</span>';
									else $form	.= " $kr=\"$vr\"";
								}
								$form	.= $required.'>'.$addon.
							'</div>';
						}
					}
					$form .='
					<div class="input">
						'.($set['info']?'<small>'.nl2br($set['info']).'</small>':'').'
						<input type="hidden" name="formId" value="'.$set['id'].'">
						<input type="hidden" name="index" value="'.$set['index'].'">
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> simpan</button>
				</div>
			</div>
			</form>
		';
		return $form;
	}
	
	private function generateTextForm($arr,$setting=[],$required=true){
		$form	= [];
		foreach($arr as $k => $v){
			$form[$k]	= [
				'type'		=> 'text',
				'maxlength'	=> 100,
				'value'		=> $v,
				'required'	=> $required
			];
		};
		return $this->generateCompleteForm($form,$setting);
	}
	
	private function formPrayTimesAdjust($arr,$required=false){
		$form	= '';
		$req	= $required?'required':'';
		foreach($arr as $k => $v){
			$form	.= '
			<div class="input-group">
				<span class="input-group-addon">'.$k.'</span>
				<input 
					name	="'.$k.'" 
					type	="text" 
					class	="form-control" 
					maxlength	="100" 
					value	="'.$v.'"
					'.$req.'>
			</div>
			';
		}
		return $form;
	}
	
	private function generateOptionSelect($selected,$arr,$reverse=true){
		$opt	= '';
		foreach($arr as $k => $v){
			if($reverse){
				$sel	= $v==$selected?'selected':'';
				$opt	.= "<option value=\"$v\" $sel>$k</option>";
			}
			else{
				$sel	= $k==$selected?'selected':'';
				$opt	.= "<option value=\"$k\" $sel>$v</option>";
			}
		}
		return $opt;
	}
	
	
	
	
	/*
	private function generateForm($arr){
		$form	= '';
		foreach($arr as $k => $v){
			$form	.= '<div class="input-group"><span class="input-group-addon">'.$k.'</span><input class	="form-control" name="'.$k.'"';
			$addon	= '';
			foreach($v as $kr => $vr){
				if($kr=='required')$form	.= " required";
				else if($kr=='addon') $addon = '<span class="input-group-addon">'.$vr.'</span>';
				else $form	.= " $kr=\"$vr\"";
			}
			$form	.= '>'.$addon.'</div>';
		}
		return $form;
	}
	*/
	
	
	
}
$request=isset($_POST['id'])?$_POST['id']:"UNKNOWN_REQUEST_________________________________________";
new proses($request);
?>