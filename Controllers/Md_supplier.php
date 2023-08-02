<?php

namespace App\Controllers;
use App\Models\MyMDSupplierModel;
class Md_supplier extends BaseController
{
	public function __construct()
	{
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mymdsupplier =  new MyMDSupplierModel();
	}
	
	public function index()
	{
		echo view('templates/meheader01');
		echo view('masterdata/md-supplier');
		echo view('templates/mefooter01');
	}
	
	public function recs() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdsupplier->view_recs($mpages,20,$txtsearchedrec);
		return view('masterdata/md-supplier-recs',$data);
	} //end recs
		
	public function profile() {
		return view('masterdata/md-supplier-profile');
	}  //end profile
	
	public function profile_save() { 
		$this->mymdsupplier->profile_save();
	} //end profile_save
}  //end main class Md_customer
