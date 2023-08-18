<?php

/* *****************************************************************************************************************
 * *** SESSION START
 * *****************************************************************************************************************/
 
$sessName	= md5("displayMasjid");
if(session_status()==PHP_SESSION_NONE){
	//echo "no session on this server";
    session_name($sessName);
    session_start();
}
elseif(session_name()!=$sessName){
	//echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';
	session_name($sessName);
    session_start();
}



/* *****************************************************************************************************************
 * *** FEEDBACK
 * *****************************************************************************************************************/
class fb{ //feedback
	protected
		$success	= true,//jika proses sukses
		$registered	= true,//jika teregister/ udah login
		$data		= NULL;//callback data--> jika ada --> jika banyak dibuat array (convert to json)
	
	//Write feedback dalam JSON________________________________________________________________________
	public function writeFeedBack(){
		$feedBack=array(
			"success"	=> $this->success,
			"registered"=> $this->registered,
			"data"		=> $this->data
		);
        echo json_encode($feedBack);
    }
	public function retSuccess(){
		$this->writeFeedBack();
    }
	public function retError($err){
		$this->success	= false;
		$this->data 	= $err;
		$this->writeFeedBack();
		exit;
	}
	public function verification($id){
		//Cek sudah login/ belom_____________________________________________________________________
		if(!isset($_SESSION["user_id"])){
			$this->registered=false;
			$this->writeFeedBack();
		}
		//Jika request proses ditemukan______________________________________________________________
		else if(method_exists($this,$id)){
			$this->id	= $id;
			$this->dt	= isset($_POST['dt'])?$_POST['dt']:"";// data
			return true; //gak perlu callback
        }
		//Jika request tidak ditemukan________________________________________________________________
        else{
			$this->success	= false;
			$this->data		= "Request tidak ditemukan...";
			$this->writeFeedBack();
		}
	}
	
	
}


?>