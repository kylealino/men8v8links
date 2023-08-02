<?php

namespace App\Controllers;

class MySalesDeposit extends BaseController
{
	public function __construct()
	{
		
		$this->mysalesdepo = model('App\Models\MySalesDepositModel');
	}
	
	public function index() { 
		echo view('transactions/sales/mysalesdeposit');
	} //end index

	public function entry() { 
		echo view('transactions/sales/mysalesdeposit-entry');
	} //end entry

	public function me_save() { 
		$this->mysalesdepo->me_save();
	} //end save

	public function me_delrec() { 
		$this->mysalesdepo->me_delrec();
	} //end me_delrec

	
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
	
	public function getdepositGroup() { 
		$this->db_erp = $this->mydbname->medb(0);
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		$str = "
		SELECT recid,trim(description) __mdata
		FROM {$this->db_erp}.`mst_despositGroup`";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
				array_push($autoCompleteResult,array(
					"mtkn_rid" => $mtkn_rid,
					"value" => $row['__mdata']
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	} //end getdepositGroup
	
	public function getDeposit_BrcnhAcct(){ 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$term = $this->request->getVar('term');
		$this->db_erp = $this->mydbname->medb(0);
		$compName = $this->request->getVar('compName');
		$branchName = $this->request->getVar('branchName');
		$autoCompleteResult = array();
		$rrw       = $this->mysalesdepo->getCompany_data($compName);
		$compRID   = $rrw['recid'];
		$rrow      = $this->mysalesdepo->getCompanyBranch_data($compRID,$branchName);
		$branchRID = $rrow['recid'];
		$str = "
		SELECT recid,bankName,CONCAT(bankName,'-****',SUBSTR( acctNO,LENGTH(acctNO)-4)) __mdata,acctNO FROM {$this->db_erp}.`mst_depositBranchAcct` WHERE `brnchID` = '$branchRID' and `compID` = '$compRID'";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
				array_push($autoCompleteResult,array(
					"mtkn_rid" => $mtkn_rid,
					"value" => $row['__mdata'],
					"bankName" => $row['bankName'],
					"acctNo" => $row['acctNO']
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end getDeposit_BrcnhAcct
	
	public function deposit_recs_branch() {
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$mpages = (empty($this->request->getVar('mpages')) ? 0 : ($this->request->getVar('mpages') + 0));
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$data = $this->mysalesdepo->deposit_recs_branch($mpages,25,$txtsearchedrec);
		echo view('transactions/sales/mysalesdeposit-recs',$data);
	} //end deposit_recs_branch
	
	public function deposit_download_zip_file() { 
		$cseqn = $this->request->getVar('data_01');
		$this->mysalesdepo->download_zip_file($cseqn);
	} //end deposit_download_zip_file
	
} //end main class
?>
