<?php
namespace App\Models;
use CodeIgniter\Model;
use ZipArchive;

class MySalesDepositModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mylibzdb = model('App\Models\MyLibzDBModel');
        $this->mylibzsys = model('App\Models\MyLibzSysModel');
        $this->myusermod = model('App\Models\MyUserModel');
        $this->mydatum = model('App\Models\MyDatumModel');
        $this->request = \Config\Services::request();
    }
    
	public function lk_Active_BRDF($dbname){ 
		$adata[]="D" . "xOx" . "Draft";
		$adata[]="F" . "xOx" . "Final";	
		return $adata;			
	}
	public function lk_lbc_stats($dbname) { 
		$adata=array();
		$adata[] = "SF" . "xOx" . "SENT FILES";
		$adata[] = "RF" . "xOx" . "RECEIVED FILES";
		$adata[] = "UF" . "xOx" . "UNRECEIVE FILES";
		$adata[] = "TRF" . "xOx" . "TO RECEIVE FILES";
		return $adata;		
	}
	
	public function getCompany_data($_trns_comp = ''){
		$cuser   = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$str = "SELECT `recid`,
		sha2(concat(`recid`,'{$mpw_tkn}'),384) mtkn_attr,
		`COMP_CODE`,`COMP_NAME` FROM {$this->db_erp}.`mst_company` WHERE (`COMP_NAME` = '{$_trns_comp}')";
		$q   = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
		if($q->getNumRows() > 0):
			return $q->getRowArray();
		else:
			echo "<div class=\"alert alert-danger mb-0\"><strong>Info.<br/></strong><strong>Error:</strong> Invalid Company [NOT FOUND]!!!</div>";
			die();
		endif;
		$q->freeResult();
	}  //end getCompany_data
	
	public function getCompanyBranch_data($compRID = '',$_trns_brnch = ''){
		$cuser   = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$str = "SELECT `recid`,
		sha2(concat(`recid`,'{$mpw_tkn}'),384) mtkn_attr,
		sha2(concat(`BRNCH_CODE`,'{$mpw_tkn}'),384) mtkn_attr_bcode,
		`BRNCH_CODE`,`BRNCH_NAME`,`BRNCH_CODEX`,`BRNCH_OCODE3`,`BRNCH_GROUP` FROM {$this->db_erp}.`mst_companyBranch` WHERE `COMP_ID` = '{$compRID}' and `BRNCH_NAME` = '{$_trns_brnch}'";
		$q   = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);

		if($q->getNumRows() > 0):
			return $q->getRowArray();
		else:
			echo "<div class=\"alert alert-danger mb-0\"><strong>Info.<br/></strong><strong>Error:</strong> Invalid Branch [NOT FOUND]!!!</div>";
				die();
		endif; 
		$q->freeResult();

	}  //end getCompanyBranch_data
    
	public function getadddaysMF($date = ''){
		//Mon//Tue//Wed//Thu//Fri//Sat//Sun 
		$interval = 0;
		$day = date('D',strtotime($date));
		switch ($day) { 
		case 'Sat':
		//add 2
			$interval = 2;
		break;
		case 'Fri':
		//ADD 3
			$interval = 3;
		break;
		default:
		//ADD 1
			$interval = 1;
		} 
		$finalDate = date_add(date_create($date),date_interval_create_from_date_string($interval. 'days'));
		return date_format($finalDate,'Y-m-d');
	}  //end getadddaysMF

	public function getadddaysMWF($date = ''){
		//Mon//Tue//Wed//Thu//Fri//Sat//Sun
		$interval = 0;
		$day = date('D',strtotime($date));
		if($day == 'Fri'){
			$interval = 3;
		}
		elseif($day == 'Mon' ||$day == 'Wed' || $day == 'Sat'){
			$interval = 2;

		}
		else{
			$interval = 1;
		}

		$finalDate = date_add(date_create($date),date_interval_create_from_date_string($interval. 'days'));
		return date_format($finalDate,'Y-m-d');
	}  //end getadddaysMWF

	public function getadddaysTTH($date = ''){
		//Mon//Tue//Wed//Thu//Fri//Sat//Sun
		$interval = 0;
		$day = date('D',strtotime($date));
		if($day == 'Thu'){
			$interval = 5;
		}
		elseif($day == 'Fri'){
			$interval = 4;

		}
		elseif($day == 'Sat'){
			$interval = 3;

		}
		elseif($day == 'Tue' || $day == 'Sun' ){
			$interval = 2;

		}
		else{
			$interval = 1;
		}
		$finalDate = date_add(date_create($date),date_interval_create_from_date_string($interval. 'days'));
		return date_format($finalDate,'Y-m-d');
	}  //end getadddaysTTH

	public function getadddaysTTHSA($date = ''){
		//Mon//Tue//Wed//Thu//Fri//Sat//Sun
		$interval = 0;
		$day = date('D',strtotime($date));
		if($day == 'Sat'){
			$interval = 3;
		}
		elseif($day == 'Tue' || $day == 'Thu' ){
			$interval = 2;
		}
		else{
			$interval = 1;
		}

		$finalDate = date_add(date_create($date),date_interval_create_from_date_string($interval. 'days'));
		return date_format($finalDate,'Y-m-d');
	}  //end getadddaysTTHSA

	public function getadddaysTF($date = ''){
		//Mon//Tue//Wed//Thu//Fri//Sat//Sun
		$interval = 0;
		$day = date('D',strtotime($date));
		if($day == 'Fri'){
			$interval = 4;
		}
		elseif($day == 'Tue' || $day == 'Sat' ){
			$interval = 3;
		}
		elseif($day == 'Wed' || $day == 'Sun' ){
			$interval = 2;
		}
		else{
			$interval = 1;
		}

		$finalDate = date_add(date_create($date),date_interval_create_from_date_string($interval. 'days'));
		return date_format($finalDate,'Y-m-d');

	}  //end getadddaysTF

	public function getadddaysMSA($date = ''){
		//Mon//Tue//Wed//Thu//Fri//Sat//Sun
		$interval = 0;
		$day = date('D',strtotime($date));
		if($day == 'Sat'){
			$interval = 2;
		}
		else{
			$interval = 1;
		}

		$finalDate = date_add(date_create($date),date_interval_create_from_date_string($interval. 'days'));
		return date_format($finalDate,'Y-m-d');

	} //end getadddaysMSA

	public function getadddaysT_F($date = ''){
		//Mon//Tue//Wed//Thu//Fri//Sat//Sun
		$interval = 0;
		$day = date('D',strtotime($date));
		if($day == 'Fri'){
			$interval = 4;
		}
		elseif($day == 'Sat'){
			$interval = 3;
		}
		elseif($day == 'Sun'){
			$interval = 2;
		}
		else{
			$interval = 1;
		}

		$finalDate = date_add(date_create($date),date_interval_create_from_date_string($interval. 'days'));
		return date_format($finalDate,'Y-m-d');
	} //end getadddaysT_F

	public function getadddaysSA($date = '') {
		//Mon//Tue//Wed//Thu//Fri//Sat//Sun
		$interval = 0;
		$day = date('D',strtotime($date));
		if($day == 'Mon'){
			$interval = 5;
		}
		elseif($day == 'Tue'){
			$interval = 4;

		}
		elseif($day == 'Wed'){
			$interval = 3;

		}
		elseif($day == 'Thu'){
			$interval = 2;

		}

		elseif($day == 'Fri'){
			$interval = 1;

		}
		elseif($day == 'Sat'){
			$interval = 7;

		}
		else{
			$interval = 6;
		}
		$finalDate = date_add(date_create($date),date_interval_create_from_date_string($interval. 'days'));
		return date_format($finalDate,'Y-m-d');
	} //end getadddaysSA
    
	public function getAddDaysFormatByAcct($mtkn_id,$salesDate){
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$data = [];
		$adata = '';
		$recID = '';
		$dateShoulbe = "";
		if(!empty($mtkn_id) && $salesDate != ''):
			$str = "SELECT `depSchedule` __mdata,`recid` from {$this->db_erp}.`mst_depositBranchAcct` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384)=  '{$mtkn_id}'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw    = $q->getRowArray();
				$adata = $rw['__mdata'];
				$recID = $rw['recid'];
				switch ($adata) {
					case 'M-F':
						$dateShoulbe = $this->getadddaysMF($salesDate);
					break;
					case 'MWF':
						$dateShoulbe = $this->getadddaysMWF($salesDate);
					break;
					case 'T/TH':
						$dateShoulbe = $this->getadddaysTTH($salesDate);
					break;
					case 'TTH':
						$dateShoulbe = $this->getadddaysTTH($salesDate);
					break;
					case 'TF':
						$dateShoulbe = $this->getadddaysTF($salesDate);
					break;
					case 'M-SA':
						$dateShoulbe = $this->getadddaysMSA($salesDate);
					break;
					case 'TTH & SA':
						$dateShoulbe = $this->getadddaysTTHSA($salesDate);
					break;
					case 'SA':
						$dateShoulbe = $this->getadddaysSA($salesDate);
					break;
					case 'T-F':
						$dateShoulbe = $this->getadddaysT_F($salesDate);
					break;
					default:
						echo "ERROR ENCOUNTERED PLEASE CONTACT THE ADMIN --> " . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser;
						break;
				}
			}
			$q->freeResult();
			
			$data = [
				'recID' => $recID,
				'dateShoulbe'=>$dateShoulbe,
				];
		endif;
		return $data;

	}
	// ADD DATE SHOULD BE END
	    
	public function me_save(){ 
		$cuser   = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'04','0006','000102')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		} 
		
		$rcseq   = $this->mylibzdb->me_escapeString($this->request->getVar('_deposit_rid'));
		$_trns_comp     = $this->mylibzdb->me_escapeString($this->request->getVar('_trns_comp'));
		$_trns_brnch    = $this->mylibzdb->me_escapeString($this->request->getVar('_trns_brnch'));
		$_trns_DteRqst  = $this->mylibzdb->me_escapeString($this->request->getVar('_trns_DteRqst'));
		$_trns_reupload = $this->mylibzdb->me_escapeString($this->request->getVar('_trns_reupload'));
		$_trns_dftag    = $this->mylibzdb->me_escapeString($this->request->getVar('_trns_dftag'));
		$_trns_group    = $this->mylibzdb->me_escapeString($this->request->getVar('_trns_group'));
		$mearray =  $this->request->getVar('mearray');
		$rqstDate = date("Y-m-d", strtotime($_trns_DteRqst));
		$salesDate = $rqstDate;
		$mearray = explode(',x|', $mearray);
		$count_uploaded_files   = 0;
		$totalAmount = 0;
		$ccrid = '';
		$bbrid = '';
		$mtkn_etr = ''; //for encryption recid header
		if($_trns_dftag === 'F' || $_trns_reupload === 'true') { 
			$count_uploaded_files   = count( $_FILES['mefiles']['name'] );
		}
		
		$mefiles_path = ROOTPATH . 'public/uploads/medeposit_uploads/';
		$mefiles_upath = 'uploads/medeposit_uploads/';
		
		if ($mefiles = $this->request->getFiles()) {
			foreach ($mefiles['mefiles'] as $mfile) {
				if ($mfile->isValid() && ! $mfile->hasMoved()) { 
					$newName = $mfile->getRandomName();
					$__upld_filename = '';
					if($mfile->getMimeType() == 'application/pdf') { 
						//echo 'yes-pdf ' . ($mfile->getSize() / 1024) . '<br/>';
						$__upld_filename = $cuser . '_' . $mfile->getName();
					} else { 
						if(!$this->mylibzsys->valid_file_type_image($mfile->getMimeType())) { 
							echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please select only <strong>gif/jpg/png </strong> file.</div>";
							die();
						}
						$__upld_filename = $cuser . '_' . $mfile->getName();
					}
					
					if(!empty($__upld_filename)) { 
						//$mfile->move($mefiles_path, $__upld_filename);
					}
				}
			}
		} //end if 
		
		
		//validating Company and its Branches
		$compRID    = '';
		$compCode   = '';
		$branchRID  = '';
		$branchCode = '';
		$rrw      = $this->getCompany_data($_trns_comp);
		$compCode = $rrw['COMP_CODE'];
		$compName = $rrw['COMP_NAME'];
		$compRID  = $rrw['recid'];
		$rrow            = $this->getCompanyBranch_data($compRID,$_trns_brnch);
		$branchRID       = $rrow['recid'];
		$branchCode      = $rrow['BRNCH_CODE'];
		$branchName      = $rrow['BRNCH_NAME'];
		$branchCodex     = $rrow['BRNCH_CODEX'];
		$branchCodexname = $rrow['BRNCH_OCODE3'];
		$branchCodexname = $rrow['BRNCH_OCODE3']; 
		$branch_grp 	 = $rrow['BRNCH_GROUP']; 
		//end validating Company and its Branches

		if($_trns_comp == '' || $_trns_brnch == '' || $_trns_DteRqst == ''):
			echo "<div class=\"alert alert-danger mb-0\"><strong>ERROR</strong><br>Incomplete Field area!</div>";
			die();
		endif;

		if(in_array('', $mearray) || in_array('undefined', $mearray) || in_array('null', $mearray)):
				echo "<div class=\"alert alert-danger mb-0\"><strong>Info.<br/></strong><strong>Error:</strong> Invalid Entries !!!</div>";
			die();
		endif;
		
		if(!empty($rcseq)) { 
			$str = "SELECT `recid`,`sysctrl_seqn` FROM {$this->db_erp}.`trx_ap_trns_deposit_hd` WHERE ((`sysctrl_seqn` = '{$rcseq}') AND (`comprid` = '{$compRID}') AND (`brnchrid` = '{$branchRID}'))";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rrow     = $q->getRow();
				$rcrid    = $rrow->recid;
				$_cseqn  = $rrow->sysctrl_seqn;
				$arrfield = array();
				$arrfield[] = "sysctrl_seqn" . "xOx'" . $rcseq . "'";
				$arrfield[] = "comprid" . "xOx'" . $compRID . "'";
				$arrfield[] = "brnchrid" . "xOx'" . $branchRID . "'";
				$arrfield[] = "salesDate" . "xOx'" . $rqstDate . "'";
				$arrfield[] = "m_user" . "xOx'" . $cuser . "'";
				$arrfield[] = "groupTag" . "xOx'" . $_trns_group . "'";
				$arrfield[] = "df_tag" . "xOx'" . $_trns_dftag . "'";
				$str = " `sysctrl_seqn` = '{$rcseq}' ";
				$this->mylibzdb->logs_modi_audit($arrfield,$this->db_erp,'`trx_ap_trns_deposit_hd`','DEPOSIT_TRANS_HD_UREC',$rcseq,$str);
				$str = "
				UPDATE {$this->db_erp}.`trx_ap_trns_deposit_hd` 
				SET `comprid`  = '{$compRID}', 
					`brnchrid` = '{$branchRID}',
					`groupTag` = '{$_trns_group}',
					`df_tag`   = '{$_trns_dftag}', 
					`salesDate`   = '{$rqstDate}' , 
					`brnch_codex` = '{$branchCodex}',
					`brnch_codexname`  = '{$branchCodexname}' 
				WHERE ((`sysctrl_seqn` = '{$rcseq}') 
				AND (`recid` = '{$rcrid}'));";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEPOSIT_TRANS_HD_UREC','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);

				if($_trns_reupload === 'true'):
					$str_del_files = "DELETE FROM {$this->db_erp}.`trx_ap_trns_deposit_hd_files` WHERE `ctrlno_hd`='$rcseq'";
					$this->mylibzdb->myoa_sql_exec($str_del_files,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				endif;
			}  //end if 
		}
		else{
			$_cseqn = $this->mydatum->get_ctr($this->db_erp,'DEP_CTR');
			$arrfield[] = "sysctrl_seqn" . "xOx'" . $rcseq . "'";
			$arrfield[] = "comprid" . "xOx'" . $compRID . "'";
			$arrfield[] = "brnchrid" . "xOx'" . $branchRID . "'";
			$arrfield[] = "salesDate" . "xOx'" . $rqstDate . "'";
			$arrfield[] = "m_user" . "xOx'" . $cuser . "'";
			$arrfield[] = "groupTag" . "xOx'" . $_trns_group . "'";
			$arrfield[] = "df_tag" . "xOx'" . $_trns_dftag . "'";
			$str = " `sysctrl_seqn` = '{$rcseq}' ";
			$this->mylibzdb->logs_modi_audit($arrfield,$this->db_erp,'`trx_ap_trns_deposit_hd`','ADD_TRNS_HD',$_cseqn,$str);
			
			$str_check = "SELECT `recid`,`sysctrl_seqn` FROM {$this->db_erp}.`trx_ap_trns_deposit_hd` WHERE (`salesDate` = '{$rqstDate}' AND groupTag = 'Sales' AND (`comprid` = '{$compRID}') AND (`brnchrid` = '{$branchRID}') )";
			$q = $this->mylibzdb->myoa_sql_exec($str_check,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'User: ' . $cuser);
				if($q->getNumRows() == 0){ 
					$str = "INSERT INTO {$this->db_erp}.`trx_ap_trns_deposit_hd` (`sysctrl_seqn`, `comprid`, `brnchrid`, `salesDate`, `m_user`,`brnch_codex`,`brnch_codexname`,`df_tag`,`groupTag`) 
					VALUES ('{$_cseqn}', '{$compRID}', '{$branchRID}', '{$rqstDate}', '{$cuser}','{$branchCodex}','{$branchCodexname}','{$_trns_dftag}','{$_trns_group}');";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEPOSIT_TRANS_HD_AREC','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				}
				else{ 
					$rw = $q->getRow();
					$sysctrl_seqn = $rw->sysctrl_seqn;
					
					if($_trns_group =='Sales'):
						echo "<div class=\"alert alert-danger mb-0\"><strong>WARNING</strong><br>Deposit for <strong>[$sysctrl_seqn - $compName - $branchName ]</strong> already exist!</div>";
						die();
					else:
						$str = "INSERT INTO {$this->db_erp}.`trx_ap_trns_deposit_hd` (`sysctrl_seqn`, `comprid`, `brnchrid`, `salesDate`, `m_user`,`brnch_codex`,`brnch_codexname`,`df_tag`,`groupTag`) 
						VALUES ('{$_cseqn}', '{$compRID}', '{$branchRID}', '{$rqstDate}', '{$cuser}','{$branchCodex}','{$branchCodexname}','{$_trns_dftag}','{$_trns_group}');";
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
						$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEPOSIT_TRANS_HD_AREC','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				endif;
				}
				$q->freeResult();

		}
		$remarksre = '';
		$str = "SELECT `recid` FROM {$this->db_erp}.`trx_ap_trns_deposit_hd` WHERE ((`sysctrl_seqn` = '{$_cseqn}') AND (`comprid` = '{$compRID}') AND (`brnchrid` = '{$branchRID}'))";
		$qhd = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'User: ' . $cuser);
		if($qhd->getNumRows() > 0):
			$rrow  = $qhd->getRow();
			$rcridhd = $rrow->recid;
			$mtkn_etr = hash('sha384', $rcridhd . $mpw_tkn); 
			for ($i=0; $i < count($mearray) ; $i++) { 
				$x = explode("x|x",$mearray[$i]);
				$_bank_name_ =  $this->mylibzdb->me_escapeString($x[0]);
				$_acct_name  =  $this->mylibzdb->me_escapeString($x[1]);
				$_rid_sv     =  $this->mylibzdb->me_escapeString($x[3]);
				$_mtkn_dt_sv =  $this->mylibzdb->me_escapeString($x[4]);
				$rems        =  $this->mylibzdb->me_escapeString($x[6]);
				$group       =  $this->mylibzdb->me_escapeString($x[7]);
				//$dateDeposit =  $this->mylibzsys->mydate_yyyymmdd($x[5]);
				$dateDeposit =  $x[5];
				$amountDeposited =  $this->mylibzdb->me_escapeString($x[2]);
				$acct_rid       =  $this->mylibzdb->me_escapeString($x[8]);
				$sales_     =  $this->mylibzdb->me_escapeString($x[9]);
				$shopeepay_ =  $this->mylibzdb->me_escapeString($x[10]);
				$expense_   =  $this->mylibzdb->me_escapeString($x[11]);
				$totalAmount += $amountDeposited; 
				$remarksre.="<p>$rems</p>";
				
				//getAddDaysFormatByAcct returns date should be base in sales of date.
				$acctdata = $this->getAddDaysFormatByAcct($acct_rid,$salesDate);
				$dateShouldBe = $acctdata['dateShoulbe'];
				$acctID       = $acctdata['recID'];
				$str = "SELECT recid FROM {$this->db_erp}.`trx_ap_trns_deposit_dt` WHERE (sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$_mtkn_dt_sv}')";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'User: ' . $cuser);
				if($q->getNumRows() > 0) { 
					$roq_dt   = $q->getRowArray();
					$recid_dt = $roq_dt['recid'];
					$arrfield = array();
					$arrfield[] = "recid" . "xOx'" . $recid_dt . "'";
					$arrfield[] = "dep_group" . "xOx'" . $group . "'";
					$arrfield[] = "bankName" . "xOx'" . $_bank_name_ . "'";
					$arrfield[] = "accountName" . "xOx'" . $_acct_name . "'";
					$arrfield[] = "amountDeposited" . "xOx'" . $amountDeposited . "'";
					$arrfield[] = "expense" . "xOx'" . $expense_ . "'";
					$arrfield[] = "sales" . "xOx'" . $sales_ . "'";
					$arrfield[] = "shopeepay" . "xOx'" . $shopeepay_ . "'";
					$arrfield[] = "m_user" . "xOx'" . $cuser . "'";
					$str = " `recid` = '{$recid_dt}' ";
					$this->mylibzdb->logs_modi_audit($arrfield,$this->db_erp,'`trx_ap_trns_deposit_dt`','UPD_TRNS_RFP',$recid_dt,$str);
					$str = "UPDATE {$this->db_erp}.`trx_ap_trns_deposit_dt` 
					SET  
					`bankName`    = '{$_bank_name_}',
					`dep_group`   = '{$group}',
					`accountName` = '{$_acct_name}',
					`dateDeposit` = '{$dateDeposit}',
					`rems`        = '{$rems}',
					`amountDeposited` = '{$amountDeposited}',
					`expense`   = '{$expense_}',
					`shopeepay` = '{$shopeepay_}',
					`sales`     = '{$sales_}',
					`bankAcctID` = '{$acctID}' 
					WHERE `recid` = '{$recid_dt}';";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEPOSIT_TRANS_DT_UREC','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				}
				else { 
					$str = "select recid from {$this->db_erp}.`trx_ap_trns_deposit_dt` where `trnsrefrid` = '$rcridhd' and `bankName` = '$_bank_name_' and `accountName` = '$_acct_name' and date(`dateDeposit`) = date('{$dateDeposit}') and 
					`expense`   = ('{$expense_}' + 0) and 
					`shopeepay` = ('{$shopeepay_}' + 0) and
					`sales`     = ('{$sales_}' + 0)";
					$qrec = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					if($qrec->getNumRows() > 0):
						$rdt = $qrec->getRow();
						$reciddt = $rdt->recid;
						$str = "UPDATE {$this->db_erp}.`trx_ap_trns_deposit_dt` 
						SET  
						`bankName`    = '{$_bank_name_}',
						`dep_group`   = '{$group}',
						`accountName` = '{$_acct_name}',
						`dateDeposit` = '{$dateDeposit}',
						`rems`        = '{$rems}',
						`amountDeposited` = '{$amountDeposited}',
						`expense`   = '{$expense_}',
						`shopeepay` = '{$shopeepay_}',
						`sales`     = '{$sales_}',
						`bankAcctID` = '{$acctID}' 
						WHERE recid = '$reciddt'";
					else: 
						$str = "INSERT INTO {$this->db_erp}.`trx_ap_trns_deposit_dt` 
						(`trnsrefrid`, `bankName`, `accountName`, `m_user`,`amountDeposited`,`dateDeposit`,`rems`,`dep_group`,`bankAcctID`,`expense`,`sales`,`shopeepay`) 
						VALUES 
						('{$rcridhd}','{$_bank_name_}', '{$_acct_name}', '{$cuser}','{$amountDeposited}','{$dateDeposit}','{$rems}','{$group}','{$acctID}','{$expense_}','{$sales_}','{$shopeepay_}');";
					endif;
					$qrec->freeResult();
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEPOSIT_TRANS_DT_AREC','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				}
				$q->freeResult();
			} //end for loop
		endif;
		$qhd->freeResult();
		
		//update total amoount
		$_trns_DteRqst = date("F j, Y", strtotime($_trns_DteRqst));
		
		$dateInterval = strtotime($dateDeposit)-strtotime($dateShouldBe);
		$dateInterval = $dateInterval/86400;

		$trnsid = $_cseqn;
		$str_uptmt = "
			UPDATE {$this->db_erp}.`trx_ap_trns_deposit_hd`
			SET `totall_amount` = '$totalAmount',`depositShouldbe` ='$dateShouldBe',`lateDays` = '$dateInterval' 
			WHERE `sysctrl_seqn` = '$trnsid';";
		$this->mylibzdb->myoa_sql_exec($str_uptmt,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);

		//file uploading 
		if ($mefiles = $this->request->getFiles()) { 
			foreach ($mefiles['mefiles'] as $mfile) {
				if ($mfile->isValid() && ! $mfile->hasMoved()) { 
					$newName = $mfile->getRandomName();
					$__upld_filename = '';
					if($mfile->getMimeType() == 'application/pdf') { 
						$__upld_filename = $cuser . '_' . $mfile->getName();
					} else { 
						if($this->mylibzsys->valid_file_type_image($mfile->getMimeType())) { 
							$__upld_filename = $cuser . '_' . $mfile->getName();
						}
					}
					
					if(!empty($__upld_filename)) { 
						$mfilext = $this->mylibzdb->me_escapeString($__upld_filename);
						
						if (file_exists($mefiles_path . $__upld_filename)) { 
							unlink($mefiles_path . $__upld_filename);
						}
						$mfile->move($mefiles_path, $__upld_filename);
						$str = "delete from {$this->db_erp}.`trx_ap_trns_deposit_hd_files` where ctrlno_hd = '$trnsid' and `file` = '$mfilext'";
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
						
						$str = "INSERT INTO {$this->db_erp}.`trx_ap_trns_deposit_hd_files` (`ctrlno_hd`,`file`,`muser`,`encd`) VALUES ('{$trnsid}','{$mfilext}','{$cuser}',now())";
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
						
					}
				}
			}  //end foreach 
		} //end if 
		//file uploading end 
		
		if($_trns_dftag == 'F'):
			//get the info of the receiver
			$str_rcpt = "SELECT `URCPT_EMAIL`,`URCPT_ID` FROM {$this->db_erp}.`mst_deposit_wf_urcpt` WHERE URCPT_DEPOSIT ='Y' AND `URCPT_DESG` = '{$branch_grp}' ";
			$q_str_rcpt = $this->mylibzdb->myoa_sql_exec($str_rcpt,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
			$subject = $branchName.'_LBC_'.$trnsid.'_Notification of Sent Files';
			foreach ($q_str_rcpt->getResultArray() as $value ):
				$val_email =  $value['URCPT_EMAIL'];
				$URCPT_ID  = $value['URCPT_ID'];
				$subject = $branchName.$trnsid.'_Notification of Deposit';
				$body  = "This is to inform you that the net sales for the date of {$_trns_DteRqst} was deposited by {$compName} - {$branchCode}.";
				$body.="<p></p>";
				$body.="<strong>NOTE:</strong>";
				$body.=$remarksre;
				$this->sending_emails($val_email,$body,$subject,$trnsid,$URCPT_ID,'DEPOSIT_ENT');
			endforeach;
		endif; 
		//get the info of the receiver end;
		if(!empty($rcseq)){
			echo "<div class=\"alert alert-success mb-0\"><strong>SAVE</strong><br>Deposit successfully Saved!!!</div><script type=\"text/javascript\"> function __salesdepo_refresh_data() { try { jQuery('#_deposit_rid').val('{$trnsid}'); jQuery('#_deposit_rid').attr('data-mtkn_etr','{$mtkn_etr}'); 
			jQuery('#mbtn_Save').prop('disabled',true);} catch(err) { var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } __salesdepo_refresh_data(); </script>";
		}
		else{
		echo "<div class=\"alert alert-success mb-0\"><strong>SAVE</strong><br>Deposit successfully saved!!!</div><script type=\"text/javascript\"> function __salesdepo_refresh_data() { try { jQuery('#_deposit_rid').val('{$trnsid}'); jQuery('#_deposit_rid').attr('data-mtkn_etr','{$mtkn_etr}'); jQuery('#mbtn_Save').prop('disabled',true);} catch(err) { var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } __salesdepo_refresh_data(); </script>";
		}
	} //end me_save
    
	public function me_delrec() { 
		$cuser   = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'04','0006','000104')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted - DEL_SALEDEPO_REC.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		} 
		$data_mtknid =  $this->request->getVar('data_mtknid');  //recid detail record
		$data_rectype =  $this->request->getVar('data_rectype');
		$medeltr =  $this->request->getVar('medeltr');
		$mtkn_etr =  $this->request->getVar('mtkn_etr');  //recid header record
		
		if($data_rectype == 'dt') { 
			if($data_mtknid == 'undefined' || trim($data_mtknid) == '') { 
				if(!empty($mtkn_etr)):
					$mearray =  $this->request->getVar('mearray');
					$x = explode("x|x",$mearray);
					$_bank_name_ =  $this->mylibzdb->me_escapeString($x[0]);
					$_acct_name  =  $this->mylibzdb->me_escapeString($x[1]);
					$_rid_sv     =  $this->mylibzdb->me_escapeString($x[3]);
					$_mtkn_dt_sv =  $this->mylibzdb->me_escapeString($x[4]);
					$rems        =  $this->mylibzdb->me_escapeString($x[6]);
					$group       =  $this->mylibzdb->me_escapeString($x[7]);
					$dateDeposit =  $x[5];
					$amountDeposited =  $this->mylibzdb->me_escapeString($x[2]);
					$acct_rid       =  $this->mylibzdb->me_escapeString($x[8]);
					$sales_     =  $this->mylibzdb->me_escapeString($x[9]);
					$shopeepay_ =  $this->mylibzdb->me_escapeString($x[10]);
					$expense_   =  $this->mylibzdb->me_escapeString($x[11]);
					$str = "select recid from {$this->db_erp}.`trx_ap_trns_deposit_dt` where sha2(concat(`trnsrefrid`,'{$mpw_tkn}'),384)  = '$mtkn_etr' and `bankName` = '$_bank_name_' and `accountName` = '$_acct_name' and date(`dateDeposit`) = date('{$dateDeposit}') and 
					`expense`   = ('{$expense_}' + 0) and 
					`shopeepay` = ('{$shopeepay_}' + 0) and
					`sales`     = ('{$sales_}' + 0) and `rems` = '$rems' ";
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEPOSIT_DT_DEL_BEFORE','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					
					$qrec = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					if($qrec->getNumRows() > 0):
						$rdt = $qrec->getRow();
						$reciddt = $rdt->recid;
						$str = "DELETE FROM {$this->db_erp}.`trx_ap_trns_deposit_dt` WHERE `recid` = '{$reciddt}'";
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
						$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEPOSIT_DT_DEL','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					endif;
					$qrec->freeResult();
				endif;
				$chtml = "<div class=\"alert alert-success mb-0\"><strong>Info.<br/></strong>Data successfully deleted!</div>
				<script type=\"text/javascript\"> 
					salesdepo_delrow('{$medeltr}');
				</script>
				";
				echo $chtml;
				die();
			} else {
				$str = "SELECT `recid` FROM {$this->db_erp}.`trx_ap_trns_deposit_dt` WHERE (sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$data_mtknid}')";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'User: ' . $cuser);
				if($q->getNumRows() > 0) { 
					$rrow = $q->getRow();
					$_rrid = $rrow->recid;
					$str = "DELETE FROM {$this->db_erp}.`trx_ap_trns_deposit_dt` WHERE `recid` = '{$_rrid}'";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEPOSIT_DT_DEL','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					echo "<div class=\"alert alert-success mb-0\"><strong>Info.<br/></strong>Data successfully deleted!</div>
					<script type=\"text/javascript\"> 
						salesdepo_delrow('{$medeltr}');
					</script>
					";
					die();
				}
				else {
					echo "<div class=\"alert alert-danger mb-0\"><strong>Info.<br/></strong><strong>Error:</strong> Problem encountered, cannot be deleted!!</div>";
					die();
				}
			}
		} //end if dt validation
		
		if($data_rectype == 'hd') { 
			$str = "SELECT `recid` FROM {$this->db_erp}.`trx_ap_trns_deposit_hd` WHERE (sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_etr}')";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rrow    = $q->getRow();
				$_hdrrid = $rrow->recid;
				$str = "UPDATE {$this->db_erp}.`trx_ap_trns_deposit_hd` SET `is_cancel` = 'Y' WHERE `recid` = '{$_hdrrid}'";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEPOSIT_TRANS_HD_DEL','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				echo "<div class=\"alert alert-success mb-0\"><strong>Info.<br/></strong><strong>Info:</strong> Data successfully deleted!</div>
				<script type=\"text/javascript\"> 
					salesdepo_delrow('{$medeltr}');
				</script>
				";
				die();
			}
			else {
				echo "<div class=\"alert alert-danger mb-0\"><strong>Info.<br/></strong><strong>Error:</strong> Problem encountered, cannot be deleted!!</div>";
				die();
			}
			$q->freeResult();
		} //end if hd validation 
		
	} //end me_delrec
    
	public function deposit_recs_branch($npages = 1,$npagelimit = 10,$msearchrec='') { 
		$cuser   = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		//new validation 
		$str_optn = '';
		$a_mcomp = $this->myusermod->ua_brnch($this->db_erp,$cuser);
		$str_comp = " AND (`brnchrid` = '__x__' AND `brnchrid` != 0) ";
		if(count($a_mcomp) > 0) { 
			$str_comp = "";
			for($aa = 0; $aa < count($a_mcomp); $aa++) { 
				$str_comp .= " `brnchrid` = '{$a_mcomp[$aa]}' or ";
			}  //end for
			$str_comp = " AND (" . substr($str_comp,0,(strlen($str_comp)-3)) . ") ";
		}

		if(!empty($msearchrec)) {
			$msearchrec = $this->mylibzdb->me_escapeString($msearchrec);
			$str_optn = "AND ((aa.`sysctrl_seqn` LIKE '%$msearchrec%') OR (cc.`BRNCH_NAME` LIKE '%$msearchrec%') OR (aa.`salesDate` LIKE '%$msearchrec%'))";
		} //end if 

		$strqry = "SELECT aa.*,bb.`COMP_NAME`, cc.`BRNCH_NAME` FROM {$this->db_erp}.`trx_ap_trns_deposit_hd` AS aa 
				   JOIN {$this->db_erp}.`mst_company` AS bb ON bb.`recid` = aa.`comprid` 
				   JOIN {$this->db_erp}.`mst_companyBranch` AS cc ON cc.`recid` = aa.`brnchrid` 
				   WHERE aa.`is_cancel`='N' {$str_optn} {$str_comp} ";

		$str = "SELECT count(*) __nrecs FROM ({$strqry}) oa ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = (($npagelimit * ($npages - 1)) > 0 ? ($npagelimit * ($npages - 1)) : 0);
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "SELECT * FROM ({$strqry}) oa  ORDER BY `sysctrl_seqn` desc limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
		
		if($qry->getNumRows() > 0) { 
			$data['rlist'] = $qry->getResultArray();
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
		$qry->freeResult();
		return $data;
	} // end deposit_recs_branch    
    
	public function sending_emails($to,$body,$subject,$mclr_empid,$cuid,$trx_type){
		$cuser = $this->myusermod->mysys_user();
		$from  = $this->getRFPPCF_email();
		$random_hash = md5(date('r', time()));
		$headers = "From: $from\r\nReply-To: noreply@mlocal.com";
		$headers .= "\r\nContent-Type: text/html\r\n";
		$headers .= "X-Priority: 1 (Highest)\n";
		$headers .= "X-MSMail-Priority: High\n";
		$headers .= "Importance: High\n";
		
		$message = "
		<html>
		<body>
		<p>{$body}</p>
		</body>
		</html>
		";
		   
		$str ="SELECT `LOG_RCPT`,
		 `LOG_SENDER`,
		 `LOG_IDENC` 
		 FROM {$this->db_erp}.`tbl_rfppcf_logs`  
		 WHERE `LOG_ENUMB` = '{$mclr_empid}' AND
		 `LOG_RCPT` = '{$to}' AND LOG_TRXTYPE  = '{$trx_type}'
		 ";
		$qrry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);

		if($qrry->getNumRows() == 0) { 
			$mail_sent = @mail( $to, $subject, $message, $headers );
			//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed"
			if($mail_sent){
				$str = "INSERT INTO {$this->db_erp}.`tbl_rfppcf_logs` 
				(`LOG_ENUMB`,
				 `LOG_RCPT`,
				 `LOG_RCPID`,
				 `LOG_SENDER`,
				 `LOG_IDENC`,`LOG_SEND_DATE`,
				 `LOG_TRXTYPE`
				 ) 
				 VALUES(
				 '$mclr_empid','$to','$cuid','$cuser','$random_hash',now(),'{$trx_type}' )";
			  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
			  $this->mylibzdb->user_logs_activity_module($this->db_erp,'RFPPCF_SENDEMAIL','',$message,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				 echo "<div class=\" alert alert-success mb-0\">
				 <strong>Success</strong> 
				 <p> Email sent to $to</p>
				 </div>";
			   
			} else {
				echo "<div class=\" alert alert-danger mb-0\">
				<strong>Success</strong> 
				<p>Email not sent to $to</p>
				</div>";
			}
		} else {
			  echo "<div class=\" alert alert-warning mb-0\">
				<strong>Success</strong> 
				<p>Email already sent to $to</p>
				</div>";
		}
		$qrry->freeResult();	
	}//end sending_emails    

	public function getRFPPCF_email(){
		$cuser = $this->myusermod->mysys_user();
		$uremail = '';
		$str = "SELECT URCPT_EMAIL  FROM {$this->db_erp}.`mst_rfppcf_wf_urcpt` WHERE `URCPT_ID` = '$cuser'";

		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
		if($q->getNumRows() > 0):
			$row = $q->getRowArray();
			$uremail = $row['URCPT_EMAIL'];
		endif;
		$q->freeResult();
		return $uremail;
	} //end getRFPPCF_email

	public function getHORFPPCF_email(){
		$cuser = $this->myusermod->mysys_user();
		$uremail = '';
		$str = "SELECT URCPT_EMAIL  FROM {$this->db_erp}.`mst_rfppcf_wf_urcpt` WHERE URCPT_LBC = '1' ";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
		if($q->getNumRows() > 0):
			$row = $q->getRowArray();
			$uremail = $row['URCPT_EMAIL'];
		endif;
		$q->freeResult();
		return $uremail;

	} //end getRFPPCF_email
	
	public function download_zip_file($cseqn) { 
		//helper("zip");
		$cuser = $this->myusermod->mysys_user();
		if(!empty($cseqn)):
			$zip = new ZipArchive;
			$dloadpath = 'public/downloads/';
			$zipfile = ROOTPATH . $dloadpath .  'salesdepo_file_' . $cseqn . '_' . time() . '.zip';
			$zipname = 'salesdepo_file_' . $cseqn . '_' . time() . '.zip';
			if(true === ($zip->open($zipfile, ZipArchive::CREATE | ZipArchive::OVERWRITE))){
				//$z->setArchiveComment('Interesting!');
				//$z->addFromString('domain.txt', 'wuxiancheng.cn');
				//$folder = './test';
				//!is_dir($folder) && mkdir($folder); // Create an folder for testing
				//if(true === $z->addFile($folder)){
				//echo 'success'; // !!!
				//}
				//rmdir($folder);
				//$zip->close();
				// foo.zip will NOT be saved on disk.
				// If foo.zip already exists before we run this script, the file will remain unchanged.
				
				$str = "SELECT file FROM {$this->db_erp}.`trx_ap_trns_deposit_hd_files` WHERE `ctrlno_hd` = '{$cseqn}'";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				$ldload = 0;
				foreach ($q->getResultArray() as $key ) { 
					$mefiles_upath =  site_url() . 'uploads/medeposit_uploads/' . $key['file'];
					$mefiles_path = ROOTPATH . 'public/uploads/medeposit_uploads/' . $key['file'];
					if (file_exists($mefiles_path)) { 
						$zip->addFile($mefiles_path,$key['file']);
						$ldload = 1;
					}
				}  //end foreach loop 
				$q->freeResult();
				$zip->close();
				if($ldload):
					///Then download the zipped file.
					header('Content-Type: application/zip');
					header('Content-disposition: attachment; filename='.$zipname);
					header('Content-Length: ' . filesize($zipfile));
					header("Pragma: no-cache"); 
					header("Expires: 0");
					readfile($zipfile);	
				else:
					echo "No files attachment found!!!";
				endif;
			} 
		endif;
	} //end download_zip_file
} //end main class
