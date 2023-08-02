<?php

namespace App\Controllers;

class MyInventory extends BaseController
{
	
	public function __construct()
	{
		
		$this->myivty = model('App\Models\MyInventoryModel');
		$this->myivtyrecadj = model('App\Models\MyInventoryReconAdjModel');
	}
		
	public function dr_in()
	{
		echo view('templates/meheader01');
		echo view('inventory/dr-in/dr-in');
		echo view('templates/mefooter01');
	}
	
	public function cycle_count() { 
		echo view('transactions/ho/inventory/cycle-count/mycycle-count');
	} //end cycle_count
	
	public function cycle_count_proc_uploaded_files() { 
		$this->myivty->cycle_count_proc_uploaded_files();
	} //end cycle_count_proc_uploaded_files
	
	public function cycle_count_posting_uploaded() { 
		echo view('transactions/ho/inventory/cycle-count/mycycle-count-posting-uploaded');
	} //end cycle_count_posting_uploaded

	public function cycle_count_posting_uploaded_recs() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$mpages = (empty($this->request->getVar('mpages')) ? 0 : ($this->request->getVar('mpages') + 0));
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$data = $this->myivty->cyc_upldpost_hd_view_recs($mpages,30,$txtsearchedrec);
		echo view('transactions/ho/inventory/cycle-count/mycycle-count-posting-uploaded-recs',$data);
	} //end cycle_count_posting_uploaded_recs
	
	public function cycle_count_post_uploaded() { 
		$mtknattr = $this->request->getVar('mtknattr');
		$this->myivty->cyc_simpleupld_post($mtknattr);
	} //end cycle_count_post_uploaded
	
	public function cycle_count_uploaded_view() { 
	} //end cycle_count_uploaded_view
	
	public function cycle_count_uploaded_editing() { 
		$mmonths = $this->request->getVar('fld_months');
		$myear = $this->request->getVar('fld_years');
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = (empty($this->request->getVar('mpages')) ? 0 : ($this->request->getVar('mpages') + 0));
		$data = $this->myivty->uploaded_view_recs($mpages,30,$txtsearchedrec,$myear,$mmonths);
		echo view('transactions/ho/inventory/cycle-count/mycycle-count-uploaded-view-editing',$data);
	} //end cycle_count_uploaded_editing
	
	public function recon_adj() { 
		echo view('transactions/ho/inventory/recon-adj/ivty-recon-adj');
	} //end recon_adj
	
	public function recon_adj_entry() { 
		echo view('transactions/ho/inventory/recon-adj/ivty-recon-adj-ent');
	} //end recon_adj_entry
	
	public function recon_adj_search_mat() { 
		$this->myivtyrecadj->search_artmaster();
	} //end recon_adj_search_mat
	
	public function recon_adj_entry_sv() { 
		$this->myivtyrecadj->save_entry();
	} //end recon_adj_entry_sv
	
	public function recon_adj_recs() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = (empty($this->request->getVar('mpages')) ? 0 : ($this->request->getVar('mpages') + 0));
		$data = $this->myivtyrecadj->vw_recs($mpages,30,$txtsearchedrec);
		echo view('transactions/ho/inventory/recon-adj/ivty-recon-adj-recs',$data);
		
	} //end recon_adj_recs
	
	public function recon_adj_delrec() { 
		$this->myivtyrecadj->me_delrec();
	} //end recon_adj_delrec
	
	public function recon_adj_postrec() { 
		$this->myivtyrecadj->me_postrec();
	} //end recon_adj_postrec
	
	public function proc_balance() {
		$this->myivty->proc_balance();
	} //end proc_balance
	
}  //end main class MyInventory
