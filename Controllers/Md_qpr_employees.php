<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
use App\Models\MyMDQprEmployeesModel;

class Md_qpr_employees extends BaseController
{

    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mymdqpermp =  new MyMDQprEmployeesModel();
    }

    public function index()
    {
        echo view('templates/meheader01');
        echo view('masterdata/md-qpr-employees');
        echo view('templates/mefooter01');
    } 

	public function recs() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdqpermp->view_recs($mpages,20,$txtsearchedrec);
		return view('masterdata/md-qpr-employees-recs',$data);
	}    

    public function profile() {
        echo view('masterdata/md-qpr-employees-profile');
    }
    
    public function profile_save() { 
		$this->mymdqpermp->profile_save();
    }
    

}  //end main Md_article
