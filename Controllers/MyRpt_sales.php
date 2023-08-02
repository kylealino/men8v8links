<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
use App\Models\MyLibzSysModel;
use App\Models\MyMDArticleModel;
use App\Models\MySalesOutModel;

class MyRpt_sales extends BaseController
{

    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mymdarticle =  new MyMDArticleModel();
        $this->mysalesout =  new MySalesOutModel();        
        $this->mylibzsys =  new MyLibzSysModel();
    }

    public function index()
    {
        echo view('templates/meheader01');
        echo view('templates/mefooter01');
    } 

    public function sales_out_details_daily() {

    } //end sales_out_details_daily

    public function sales_out_details() { 
        echo view('templates/meheader01');
        echo view('reports/sales/sales-out-details');  
        echo view('templates/mefooter01');

    } //end sales_out_details_daily


    public function sales_out_details_tab_daily() { 
        echo view('reports/sales/sales-out-details-tab-daily');  

    } //end sales_out_details_daily

    public function sales_out_details_tab_daily_proc() { 
        $fld_sc2_dtefrom = $this->mylibzsys->mydate_yyyymmdd($this->request->getVar('fld_sc2_dtefrom'));
        $fld_sc2_dteto = $this->mylibzsys->mydate_yyyymmdd($this->request->getVar('fld_sc2_dteto'));
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mysalesout->sales_out_details_daily_rec($mpages,20,'',$fld_sc2_dtefrom,$fld_sc2_dteto);
        echo view('reports/sales/sales-out-details-tab-daily-rec',$data);  

    } //end sales_out_details_tab_daily_proc

    public function sales_out_details_tab_daily_rec() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $fld_sc2_dtefrom = $this->request->getVar('fld_sc2_dtefrom');
        $fld_sc2_dteto = $this->request->getVar('fld_sc2_dteto');
        
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mysalesout->sales_out_details_daily_rec($mpages,20,$txtsearchedrec,$fld_sc2_dtefrom,$fld_sc2_dteto);
        echo view('reports/sales/sales-out-details-tab-daily-rec',$data);
    }
    
    public function sales_out_tally_daily() { 
		 echo view('reports/sales/sales-out-details-tab-daily-tally');
	} // end sales_out_tally_daily

    public function sales_out_tally_daily_proc() { 
		$data = $this->mysalesout->get_sales_for_tally();
		echo view('reports/sales/sales-out-details-tab-daily-tally-proc',$data);
	} // end sales_out_tally_daily

    public function sales_out_tally_daily_check_proc() { 
		$data = $this->mysalesout->get_sales_branch_per_day_for_tally();
		echo view('reports/sales/sales-out-details-tab-daily-tally-check-proc',$data);
	} // end sales_out_tally_daily_check_proc

	
	public function sales_out_Acct_POS_tally() { 
		 echo view('reports/sales/sales-out-details-AcctPOS-tally');
	} //end sales_out_Acct_POS_tally

	public function sales_out_Acct_POS_tally_proc() { 
		$data = $this->mysalesout->POS_Tally_Summary();
		 echo view('reports/sales/sales-out-details-AcctPOS-tally-proc',$data);
	} //end sales_out_Acct_POS_tally

	public function sales_out_Acct_POS_TAXR_proc() { 
		$data = $this->mysalesout->POS_TAXR_Summary();
		echo view('reports/sales/sales-out-details-AcctPOS-TAXR-proc',$data);
	} // end sales_out_Acct_POS_TAXR_proc

	public function sales_out_itemized_abranch_proc() { 
		$data = $this->mysalesout->sales_out_itemized_abranch_proc();
		echo view('reports/sales/sales-out-details-itemized-abranch-proc',$data);
	} // end sales_out_itemized_abranch_proc
	
	public function sales_out_recon_proc() {
		$this->mysalesout->mesales_recon_reupload();
	}  //end  sales_out_recon_proc
}  //end main Mysales
