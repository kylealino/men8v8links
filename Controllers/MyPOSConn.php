<?php namespace App\Controllers;
  
use CodeIgniter\Controller;

class MyPOSConn extends BaseController
{

    public function __construct()
    {
        $this->myposconn = model('App\Models\MyPOSConnModel');
        $this->myposdbconn = $this->myposconn->connectdb();
    }

    public function reprint_logs() { 
		
		return view('mypos/reprint-logs/reprint-logs');
	}  //end reprint_logs
	
    public function reprint_recs_logs() { 
		$data = array();
		$data['myposdbconn'] = $this->myposdbconn;
		return view('mypos/reprint-logs/reprint-logs-recs',$data);
	}  //end reprint_logs
	

}  //end main Md_article
