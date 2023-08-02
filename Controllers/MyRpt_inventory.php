<?php namespace App\Controllers;
  
use CodeIgniter\Controller;

class MyRpt_inventory extends BaseController
{

    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->myrptivtybrnch = model('App\Models\MyRptInventoryModel');
        $this->myrptivtyho = model('App\Models\MyRptHOInventoryModel');
        $this->myivtyho = model('App\Models\MyInventoryModel');
    }

    public function index()
    {
        echo view('templates/meheader01');
        echo view('reports/inventory/myrpt-inventory');
        echo view('templates/mefooter01');
    } 
    
    public function stockcard() { 
		echo view('reports/inventory/myrpt-stockcard');
	} //end stockcard
	
    public function stockcard_recs() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$fld_stinqbr_dtefrom = $this->request->getVar('fld_stinqbr_dtefrom');
		$fld_stinqbr_dteto = $this->request->getVar('fld_stinqbr_dteto');
		if(!empty($fld_stinqbr_dtefrom)){
			$fld_stinqbr_dtefrom = $this->mylibz->mydate_yyyymmdd($fld_stinqbr_dtefrom);
		}
		if(!empty($fld_stinqbr_dteto)){
			$fld_stinqbr_dteto = $this->mylibz->mydate_yyyymmdd($fld_stinqbr_dteto);
		}
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->myrptivtybrnch->stockcard($mpages,20,$txtsearchedrec,$fld_stinqbr_dtefrom,$fld_stinqbr_dteto);
		echo view('reports/inventory/myrpt-stockcard-recs',$data);
	} //end stockcard
	
	public function ho() { 
		echo view('reports/inventory/ho/myrptho-inventory');
	}

	public function ho_detailed() { 
		echo view('reports/inventory/ho/myrptho-inventory-detailed');
	}  //end ho_detailed
	
	
	public function ho_detailed_gen() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$metkntmp = $this->request->getVar('metkntmp');
		$mpages = $this->request->getVar('mpages');
		$ltodate = ($this->request->getVar('ltodate') == 1 ? 1 : 0);
		$mpages = (empty($mpages) ? 0: $mpages);
		$mevalfilter = $this->request->getVar('mevalfilter');
		if ($mevalfilter == 'M_PREV'):
			$mdateinq = $this->request->getVar('mdateinq');
			
			$str = "select (DATE('$mdateinq') >= CURRENT_DATE()) me_to_validate ";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->getRowArray();
			if ($rw['me_to_validate'] > 0): 
				echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\">Date Should be less than to Current Date!!!</div>";
				die();
			else:
				if(empty($metkntmp)):
					//processing will be done if previous days are triggered by the user 
					//allow processing and skip user access validation only for displaying report 
					$medata = $this->myivtyho->proc_balance(TRUE);
					if(count($medata) > 0):
						$metkntmp = $medata['metkntmp'];
					endif;
				endif;
			endif;
		endif;
		$data = $this->myrptivtyho->detailed_gen($mpages,20,$txtsearchedrec,$ltodate,$metkntmp);
		echo view('reports/inventory/ho/myrptho-inventory-detailed-gen',$data);
	}  //end ho_detailed
	
	public function ho_detailed_download() { 
		$this->myrptivtyho->detailed_download();
	} //end ho_detailed_download
	
	public function ho_summary() { 
		$this->myrptivtyho->ivtysummary();
	} //end ho_summary
	
	public function live_inventory_balance() { 
		echo view('templates/meheader-online');
		$this->myrptivtyho->live_inventory_balance();
		echo view('templates/mefooter-online');
		
	} // end live_inventory_balance
	
	public function me_itemized() { 
		//$this->myrptivtyho->itemized_ivty_abrach();
		echo view('reports/inventory/ho/myrptho-inventory-itemized');
	} //end me_itemized
	
	public function me_itemized_proc() { 
		$data = $this->myrptivtyho->itemized_ivty_abrach();
		echo view('reports/inventory/ho/myrptho-inventory-itemized-proc',$data);
	} //end me_itemized

}  //end main class
