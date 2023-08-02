<?php

namespace App\Controllers;

class MyUser extends BaseController
{
	public function __construct()
	{
		//$this->db_erp = $this->mydbname->medb(0);
		//$this->myusermod = model('App\Models\MyUserModel');
	}
	
	public function user_access() { 
		echo view('myua/myua');
	}  //end user_access
	
	public function user_rec() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->myusermod->view_recs($mpages,30,$txtsearchedrec);
		echo view('myua/myuser-recs',$data);
	} //end user_rec
	
	public function user_module_access_save() { 
		$this->myusermod->ua_mod_access_save();
		
	} //end user_rec
	
} //end main class
?>
