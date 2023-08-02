<?php

namespace App\Controllers;
use App\Models\MyMDCustomerModel;
class Md_customer extends BaseController
{
	public function __construct()
	{
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mymdcustomer =  new MyMDCustomerModel();
	}
	
	public function index()
	{
		echo view('templates/meheader01');
		echo view('masterdata/md-customer');
		echo view('templates/mefooter01');
	}
	
	public function recs() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdcustomer->view_recs($mpages,20,$txtsearchedrec);
		return view('masterdata/md-customer-recs',$data);
	} //end recs
		
	public function profile() {
		return view('masterdata/md-customer-profile');
	}  //end profile
	
	public function profile_save() { 
		$this->mymdcustomer->profile_save();
	} //end profile_save
}  //end main class Md_customer
