<?php
namespace App\Controllers;
use App\Models\MyPullOutTrxModel;

class MyPullOutTrx extends BaseController
{
	public function __construct()
	{
		$this->mypulloutrx =  new MyPullOutTrxModel();
		$this->db_erp = $this->mypulloutrx->db_erp;

	}
	
	public function index() { 
		echo view('templates/meheader01');
		echo view('transactions/pullout/pullout-trx');
		echo view('templates/mefooter01');
	} //end index
	
	public function man_recs_po_sv() { 
		$trxno = $this->request->getVar('trxno_id');
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$fld_ptyp = $this->request->getVar('fld_ptyp');
		$str_tag ='';
		
		$aua_branch = $this->myusermod->ua_brnch($this->db_erp,$cuser);
		$str_branch = "aa.`branch_id` = '__MEBRNCH__' ";

		if(count($aua_branch) > 0) { 
			$str_branch = "";
			for($xx = 0; $xx < count($aua_branch); $xx++) { 
				$mbranch = $aua_branch[$xx];
				$str_branch .= "aa.`branch_id` = '$mbranch' or ";
            } //end for 
            $str_branch = "(" . substr($str_branch,0,strlen($str_branch) - 3) . ")";
        }
		if(!empty($trxno)) { 
			//EDIT ACCESS
			$result = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='16'","myua_acct");
			if($result == 1){
				//IF USER IS NOT A SUPERADMIN WILL FALL THIS VALIDATION
				if($this->myusermod->mysys_userlvl() != 'S') {
					
					//USER ONLY CAN EDIT THEIR ENTRY WHEN TAG IS DRAFT ELSE NO ACCESS --RCVNG EDITDRAFT
					$result_drft = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='32'","myua_acct");
					
					//USER CAN EDIT ENTRY WHEN TAG IS FINAL ELSE NO ACCESS --RCVNG EDITDRAFT
					$result_fnal = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='33'","myua_acct");

					//USER CAN EDIT ENTRY WHEN TAG IS FINAL ELSE NO ACCESS --RCVNG EDIT BRNCH 
					$result_brnch = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='34'","myua_acct");
					
					if(!($result_fnal == 1) && ($result_drft == 1) && !($result_brnch == 1)){ //DRAFT
						$str_tag ="and aa.muser ='$cuser' and aa.df_tag ='D'";
					}//endif
					elseif(!($result_drft == 1) && ($result_fnal == 1) && !($result_brnch == 1)){ //FINAL
						$str_tag ="and aa.df_tag ='F'";
					}//endif
					elseif(($result_drft == 1) && ($result_brnch == 1) && !($result_fnal == 1)){  //DRAFT WITH BRANCH DAPAT NAKAON ANG DRAFT at BRCNH
						$str_tag ="and {$str_branch} and aa.df_tag ='D'";
					}//endif
					elseif(($result_fnal == 1) && ($result_drft == 1) && !($result_brnch == 1)){ //DRAFT and FINAL DAPAT NAKAON ANG DRAFT at FINAL
						$str_tag ="";
					}//endif
					else{
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You don't authorized to edit this data!!!</div>";
						die();
					}
					
					$str = "select aa.muser,aa.potrx_no from {$this->db_erp}.`trx_manrecs_po_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' and aa.flag = 'R' {$str_tag}";
					//var_dump($str);
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() == 0){
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You don't authorized to edit this data!!!</br>Note:Only the Administrative User can edit the Final Tagging.</div>";
						die();
					}//endif
				} //endif
				//WHEN TRANSACTIONS IS POSTED IT IS UNEDITABLE
				$str = "select aa.post_tag from {$this->db_erp}.`trx_manrecs_po_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' and aa.post_tag ='Y'";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() > 0){
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Transactions already posted!!!</br>Note: Posted Transactions is uneditable.</div>";
					die();
				}
				if($fld_ptyp == 'N'){
					$this->mypulloutrx->save_nontrade();
				}
				else{
					$this->mypulloutrx->save_trade();
				}
			}
			else{ //IF EDIT DATA AND NO PERMISSION
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
				die();
			}
		}else{
			//ADD SAVE ACCESS
			$result = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='15'","myua_acct");
			if($result != 1){
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
				die();
			}
			if($fld_ptyp == 'N'){
				$this->mypulloutrx->save_nontrade();
			}
			else{
				$this->mypulloutrx->save_trade();
			}
		}
	}  //end man_recs_po_sv
	
	public function pout_post_vw(){
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
				
		$result = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='17'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-pout-post');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}  //end pout_post_vw
	
	public function poutrec_vw() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 1: ($mpages + 0));
		$data = $this->mypulloutrx->view_recs($mpages,20,$txtsearchedrec);
        echo view('transactions/pullout/pullout-recs-encd',$data);
	}  //end poutrec_vw

    public function printing(){
        echo view('transactions/pullout/pullout-printing');
    }
} //end main class 