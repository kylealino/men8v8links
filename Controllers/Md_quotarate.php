<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
use App\Models\MyMDQuotaRateModel;

class Md_quotarate extends BaseController
{

    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mymdqpr =  new MyMDQuotaRateModel();
    }

    public function index()
    {
        echo view('templates/meheader01');
        echo view('masterdata/md-quota-rate');
        echo view('templates/mefooter01');
    } 

	public function recs() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdqpr->view_recs($mpages,20,$txtsearchedrec);
		return view('masterdata/md-quota-rate-recs',$data);
	}    

    public function profile() {
        return view('masterdata/md-quota-rate-profile');
    }
    
    public function qpr_save() { 
		$this->mymdqpr->qpr_save();
	}  //end qpr_save
	
	
    

}  //end main Md_article
