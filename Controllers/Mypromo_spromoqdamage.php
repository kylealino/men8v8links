<?php
namespace App\Controllers;

class Mypromo_spromoqdamage extends BaseController
{
	public function __construct()
	{
		$this->mypromospdp =  model('App\Models\Mypromo_spromoqdamageModel');
		$this->db_erp = $this->mypromospdp->db_erp;
		
	}
	
	public function index() {
		echo view('templates/meheader01');
		echo view('transactions/promotions/spqd/promotion-spqd');
		echo view('templates/mefooter01');
	} 
	public function save_spqd(){
		$this->mypromospdp->save_promo_spqd();

	}

	public function spqd_promo_search(){
		$this->mypromospdp->_search_code();
	}
	//start record viewing
    public function spqd_vw()
    {
        $data = $this->mypromospdp->spqd_view_rec(1, 10);
        return view('transactions/promotions/spqd/spqd_view_rec', $data);
    } //end record viewing

	public function spqd_recs()
    {
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mypromospdp->spqd_view_rec($mpages,10,$txtsearchedrec);
        return view('transactions/promotions/spqd/spqd_view_rec', $data);
    } //end record pagination


	    //show view approval record
		public function spqd_vw_appr() { 
			$data = $this->mypromospdp->spqd_post_view(1,10);
			return view('transactions/promotions/spqd/spqd_view_appr',$data);
		} //end view approval record
	
	    //view approval pagination
	
		public function spqd_save_appr() { 
			$this->mypromospdp->spqd_for_approval();
		}//end for approval
		
		public function spqd_recs_appr() { 
			$txtsearchedrec = $this->request->getVar('txtsearchedrec');
			$mpages = $this->request->getVar('mpages');
			$mpages = (empty($mpages) ? 0: $mpages);
			$data = $this->mypromospdp->spqd_post_view($mpages,10,$txtsearchedrec);
			return view('transactions/promotions/spqd/spqd_view_appr',$data);
		} //end approval pagination

		//show view approval record
		public function spqd_vw_dashboard() { 
			$data = $this->mypromospdp->spqd_dashboard_view(1,10);
			return view('transactions/promotions/spqd/spqd_view_dashboard',$data);
		} //end view approval record

		public function dashboard_recs()
		{
			
			$txtsearchedrec = $this->request->getVar('txtsearchedrec');
			$fromspromo = $this->request->getVar('fromspromo');
			$tospromo = $this->request->getVar('tospromo');
			$ifcheckvalue = $this->request->getVar('ifcheckvalue');
			$mpages = $this->request->getVar('mpages');
			$mpages = (empty($mpages) ? 0: $mpages);
			$data = $this->mypromospdp->spqd_dashboard_view($mpages,10,$txtsearchedrec,$fromspromo,$tospromo,$ifcheckvalue);
			return view('transactions/promotions/spqd/spqd_view_dashboard', $data);
			return view('transactions/promotions/spqd/promotion-spqd', $data);
		} //end record pagination

		   //generate barcode
		 public function spqd_dl_proc() { 
			$spqd_trx_no = $this->request->getVar('spqd_trx_no');
			$this->mypromospdp->download_spqd_barcode($spqd_trx_no);
		}//end generate barcode
		public function spqd_tracing_c(){
			$this->mypromospdp->spqd_audit();
	
		}

	

		
  
} //end main class 
	