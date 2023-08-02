<?php
namespace App\Models;
use CodeIgniter\Model;
class MyMDQuotaRateModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->request = \Config\Services::request();
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibzdb = model('App\Models\MyLibzDBModel');
		$this->dbx = $this->mylibzdb->dbx;
	}	
	
	public function view_recs($npages = 1,$npagelimit = 30,$msearchrec='') {
		$cuser = $this->mylibzdb->mysys_user();
		$mpw_tkn = $this->mylibzdb->mpw_tkn();
		
		$_meprodserv = '';
		$_meprodoper = '';
		$_meproddesgnp = '';
		$_meprodsoper = '';
		$_meprodserv = $this->request->getVar('_meprodserv');
		$_meprodoper = $this->request->getVar('_meprodoper');
		$_meproddesgnp = $this->request->getVar('_meproddesgnp');
		$_meprodsoper = $this->request->getVar('_meprodsoper');
		
		if(!empty($_meprodserv)) {
			$_meprodserv = $this->dbx->escapeString($this->request->getVar('_meprodserv'));
		}
		
		if(!empty($_meprodoper)) {
			$_meprodoper = $this->dbx->escapeString($this->request->getVar('_meprodoper'));
		}

		if(!empty($_meproddesgnp)) {
			$_meproddesgnp = $this->dbx->escapeString($this->request->getVar('_meproddesgnp'));
		}

		if(!empty($_meprodsoper)) {
			$_meprodsoper = $this->dbx->escapeString($this->request->getVar('_meprodsoper'));
		}
		
		if(!isset($cuser)) {
			//die();
		}

		$str_optn = "";
		if(!empty($msearchrec)) { 
			$msearchrec = $this->dbx->escapeString($msearchrec);
			$str_optn = " where (PROD_SUB_OPERATION_PROCESS like '%$msearchrec%' or PRODL_SERVICES like '%$msearchrec%' or `PROD_OPERATION` like '%$msearchrec%') ";
		}
		
		if(!empty($_meprodserv) && !empty($_meprodoper) && !empty($_meproddesgnp) && !empty($_meprodsoper)) { 
			$str_optn = " where PRODL_SERVICES = '$_meprodserv' and PROD_OPERATION = '$_meprodoper' and PROD_DESGNT = '$_meproddesgnp' and PROD_SUB_OPERATION = '$_meprodsoper' ";
		}
		
		$strqry = "
		select aa.*,
		sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`mst_process_rate_amnt` aa {$str_optn} 
		";
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->resultID->num_rows > 0) { 
			$data['rlist'] = $qry->getResultArray();
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
		return $data;
	}  //end view_recs	
	
	public function qpr_save() {  
		$cuser = $this->mylibzdb->mysys_user();
		$mpw_tkn = $this->mylibzdb->mpw_tkn();
		
		$mtkn_etr = $this->request->getVar('mtkn_etr');
		$maction = $this->request->getVar('maction');
		$meprodserv = $this->dbx->escapeString($this->request->getVar('meprodserv'));
		$meprodoper = $this->dbx->escapeString($this->request->getVar('meprodoper'));
		$merecflag = $this->request->getVar('merecflag');
		$meproddesgnp = $this->dbx->escapeString($this->request->getVar('meproddesgnp'));
		$meprodsoper = $this->dbx->escapeString($this->request->getVar('meprodsoper'));
		$meprodproc = $this->dbx->escapeString($this->request->getVar('meprodproc'));
		$meqpramt = (empty($this->request->getVar('meqpramt')) ? 0 : ($this->request->getVar('meqpramt') + 0));
		//updating of records
		if(!empty($mtkn_etr)) { 
			$str = "select recid,PRODL_SERVICES,PROD_OPERATION,PROD_DESGNT,PROD_SUB_OPERATION,PROD_SUB_OPERATION_PROCESS,PROD_RFLAG,
			PROD_SOP_RATE_AMT from {$this->db_erp}.`mst_process_rate_amnt` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				$rid = $rw['recid'];
				$_meprodserv = $this->dbx->escapeString($rw['PRODL_SERVICES']);
				$_meprodoper = $this->dbx->escapeString($rw['PROD_OPERATION']);
				$_meproddesgnp = $this->dbx->escapeString($rw['PROD_DESGNT']);
				$_meprodsoper = $this->dbx->escapeString($rw['PROD_SUB_OPERATION']);
				$_meprodproc = $this->dbx->escapeString($rw['PROD_SUB_OPERATION_PROCESS']);
				
				$str = "select recid,PRODL_SERVICES,PROD_OPERATION,PROD_DESGNT,PROD_SUB_OPERATION,PROD_SUB_OPERATION_PROCESS,PROD_RFLAG,
				PROD_SOP_RATE_AMT from {$this->db_erp}.`mst_process_rate_amnt` aa where PRODL_SERVICES = '$meprodserv' and PROD_OPERATION = '$meprodoper' and PROD_DESGNT = '$meproddesgnp' 
				and PROD_SUB_OPERATION = '$meprodsoper' and PROD_SUB_OPERATION_PROCESS = '$meprodproc' ";
				$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
				if($qq->getNumRows() > 0) { 
					$rrw = $qq->getRowArray();
					$rrid = $rrw['recid'];
					if($rid !== $rrid) { 
						echo "Conflict record update... [RECORD ALREADY EXISTS!!!]";
					}  else { 
						$adataz = array();
						$adataz[] = "PROD_SOP_RATE_AMTxOx'{$meqpramt}'";
						$adataz[] = "PROD_RFLAGxOx'{$merecflag}'";
						$str = " `recid` = '$rid' ";
						$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_process_rate_amnt`','MQPR_UREC',$meprodproc,$str);
						$str = "update {$this->db_erp}.`mst_process_rate_amnt` set PROD_SOP_RATE_AMT = '$meqpramt', `PROD_RFLAG` = '$merecflag' 
						where `recid` = '$rid' ";
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
						$this->mylibzdb->user_logs_activity_module($this->db_erp,'MQPR_UREC',$meprodproc,$rid,$str,'');
						echo "Rate Amount updated!!!";
					}
				} else {
					$adataz = array();
					$adataz[] = "PROD_SOP_RATE_AMTxOx'{$meqpramt}'";
					$adataz[] = "PRODL_SERVICESxOx'{$meprodserv}'";
					$adataz[] = "PROD_OPERATIONxOx'{$meprodoper}'";
					$adataz[] = "PROD_DESGNTxOx'{$meproddesgnp}'";
					$adataz[] = "PROD_SUB_OPERATIONxOx'{$meprodsoper}'";
					$adataz[] = "PROD_SUB_OPERATION_PROCESSxOx'{$meprodproc}'";
					$adataz[] = "PROD_RFLAGxOx'{$merecflag}'";
					$str = " `recid` = '$rid' ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_process_rate_amnt`','MQPR_UREC',$_meprodproc,$str);
					$str = "update {$this->db_erp}.`mst_process_rate_amnt` set PROD_SOP_RATE_AMT = '$meqpramt', `PROD_RFLAG` = '$merecflag',
					PRODL_SERVICES = '$meprodserv',
					PROD_OPERATION = '$meprodoper',
					PROD_DESGNT = '$meproddesgnp',
					PROD_SUB_OPERATION = '$meprodsoper',
					PROD_SUB_OPERATION_PROCESS = '$meprodproc' 
					where `recid` = '$rid' ";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MQPR_UREC',$meprodproc,$rid,$str,'');
					echo "Record update successfuly done!!!";
				}
				
			} else { 
				echo "Invalid record token for update";
			}
		} else {
			$str = "select recid,PRODL_SERVICES,PROD_OPERATION,PROD_DESGNT,PROD_SUB_OPERATION,PROD_SUB_OPERATION_PROCESS,PROD_RFLAG,
			PROD_SOP_RATE_AMT from {$this->db_erp}.`mst_process_rate_amnt` aa where PRODL_SERVICES = '$meprodserv' and PROD_OPERATION = '$meprodoper' and PROD_DESGNT = '$meproddesgnp' 
			and PROD_SUB_OPERATION = '$meprodsoper' and PROD_SUB_OPERATION_PROCESS = '$meprodproc' ";
			$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($qq->getNumRows() > 0) { 
				echo "[RECORD ALREADY EXISTS!!!]";
			} else {
				if($maction == 'A_REC') { 
					$adataz = array();
					$adataz[] = "PROD_SOP_RATE_AMTxOx'{$meqpramt}'";
					$adataz[] = "PRODL_SERVICESxOx'{$meprodserv}'";
					$adataz[] = "PROD_OPERATIONxOx'{$meprodoper}'";
					$adataz[] = "PROD_DESGNTxOx'{$meproddesgnp}'";
					$adataz[] = "PROD_SUB_OPERATIONxOx'{$meprodsoper}'";
					$adataz[] = "PROD_SUB_OPERATION_PROCESSxOx'{$meprodproc}'";
					$adataz[] = "PROD_RFLAGxOx'{$merecflag}'";
					$str = " `recid` = 'X' ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_process_rate_amnt`','MQPR_UREC',$meprodproc,$str);
					$str = "insert into {$this->db_erp}.`mst_process_rate_amnt` (PROD_SOP_RATE_AMT,`PROD_RFLAG`,
					PRODL_SERVICES,PROD_OPERATION,PROD_DESGNT,PROD_SUB_OPERATION,PROD_SUB_OPERATION_PROCESS) 
					values ('$meqpramt','$merecflag','$meprodserv','$meprodoper','$meproddesgnp','$meprodsoper','$meprodproc')";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MQPR_AREC',$meprodproc,$meprodproc,$str,'');
					echo "Record successfuly added!!!";
				}
			} //end check if records exists
		} //end mtkn_etr validation 
		
	} //end qpr_save
	
} //end main class
