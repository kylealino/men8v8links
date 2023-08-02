<?php namespace App\Controllers;
  
use CodeIgniter\Controller;

class MyDataPOS extends BaseController
{

    public function __construct()
    {
        $this->mydatapos = model('App\Models\MyDataPOSModel');
    }
    
    public function get_token() { 
		$metknkey = $this->request->getVar('metknkey');
		$metkndload = $this->mydatapos->get_token($metknkey);
		echo $metkndload;
		die();
	} //end index
	
	public function mdata_pos_dload() { 
		$mtkn_code = $this->request->getVar('mtkn_code');
		$B_CODE = $this->request->getVar('B_CODE');
		$mdatadload = $this->request->getVar('mdatadload');
		$this->mydatapos->mdata_pos_dload($mtkn_code,$B_CODE,$mdatadload);
	} //end mdata_pos_dload

    public function reprint_logs() { 
		
		return view('mypos/reprint-logs/reprint-logs');
	}  //end reprint_logs
	
    public function reprint_recs_logs() { 
		$data = array();
		$data['myposdbconn'] = $this->myposdbconn;
		return view('mypos/reprint-logs/reprint-logs-recs',$data);
	}  //end reprint_logs
	

}  //end main Md_article
