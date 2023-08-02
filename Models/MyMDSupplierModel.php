<?php
namespace App\Models;
use CodeIgniter\Model;
class MyMDSupplierModel extends Model
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
		if(!isset($cuser)) {
			//die();
		}

		$str_optn = "";
		if(!empty($msearchrec)) { 
			$msearchrec = $this->dbx->escapeString($msearchrec);
			$str_optn = " where (SPLR_CODE like '%$msearchrec%' or SPLR_NAME like '%$msearchrec%') ";
		}
		
		$strqry = "
		select aa.*,
		sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`mst_supplier` aa {$str_optn} 
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
    
    public function profile_save() { 
		$cuser = $this->mylibzdb->mysys_user();
		$mpw_tkn = $this->mylibzdb->mpw_tkn();
		
		$mtkn_etr = $this->request->getVar('mtkn_etr');
		$maction = $this->request->getVar('maction');
		
		$mesplrcode = $this->dbx->escapeString($this->request->getVar('mesplrcode'));
		$mesplrname = $this->dbx->escapeString($this->request->getVar('mesplrname'));
		$merecflag = $this->request->getVar('merecflag');
		$mesplrtinno = $this->request->getVar('mesplrtinno');
		$mesplraddr1 = $this->dbx->escapeString($this->request->getVar('mesplraddr1'));
		$mesplraddr2 = $this->dbx->escapeString($this->request->getVar('mesplraddr2'));
		$mesplraddr3 = $this->dbx->escapeString($this->request->getVar('mesplraddr3'));
		$mesplrtelno = $this->request->getVar('mesplrtelno');
		$mesplrfaxno = $this->request->getVar('mesplrfaxno');
		$mesplremail = $this->request->getVar('mesplremail');
		$mesplrcpname = $this->dbx->escapeString($this->request->getVar('mesplrcpname'));
		$mesplrcpdesgn = $this->dbx->escapeString($this->request->getVar('mesplrcpdesgn'));
		$mesplrcpcontno = $this->request->getVar('mesplrcpcontno');
		$mesplrcpemail = $this->request->getVar('mesplrcpemail');
		$mesplrwsite = $this->request->getVar('mesplrwsite');
		$mesplrocontinfo = $this->dbx->escapeString($this->request->getVar('mesplrocontinfo'));
		
		//updating of records
		if(!empty($mtkn_etr)) { 
			$str = "select recid,SPLR_CODE from {$this->db_erp}.`mst_supplier` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				$rid = $rw['recid'];
				if($mesplrcode == $rw['SPLR_CODE']) { 
					$adataz = array();
					$adataz[] = "SPLR_NAMExOx'{$mesplrname}'";
					$adataz[] = "SPLR_ADDR1xOx'{$mesplraddr1}'";
					$adataz[] = "SPLR_ADDR2xOx'{$mesplraddr2}'";
					$adataz[] = "SPLR_ADDR3xOx'{$mesplraddr3}'";
					$adataz[] = "SPLR_TINNOxOx'{$mesplrtinno}'";
					$adataz[] = "SPLR_EMAILxOx'{$mesplremail}'";
					$adataz[] = "SPLR_WEBSITExOx'{$mesplrwsite}'";
					$adataz[] = "SPLR_OCONTINFOxOx'{$mesplrocontinfo}'";
					$adataz[] = "SPLR_RFLAGxOx'{$merecflag}'";
					$adataz[] = "SPLR_TELNOxOx'{$mesplrtelno}'";
					$adataz[] = "SPLR_FAXNOxOx'{$mesplrfaxno}'";
					$adataz[] = "SPLR_CPRSNxOx'{$mesplrcpname}'";
					$adataz[] = "SPLR_CPRSN_DESGNxOx'{$mesplrcpdesgn}'";
					$adataz[] = "SPLR_CPRSN_TELNOxOx'{$mesplrcpcontno}'";
					$adataz[] = "SPLR_CPRSN_EMAILxOx'{$mesplrcpemail}'";
					$adataz[] = "SPLR_CPRSN_MOBNOxOx'{$mesplrcpcontno}'";
					$str = " recid = {$rw['recid']} ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_supplier`','MSUPPLIER_UREC',$mesplrcode,$str);
					$str = "update {$this->db_erp}.`mst_supplier` set SPLR_NAME = '$mesplrname',
					`SPLR_ADDR1` = '$mesplraddr1',
					`SPLR_ADDR2` = '$mesplraddr2',
					`SPLR_ADDR3` = '$mesplraddr3',
					`SPLR_TINNO` = '$mesplrtinno',
					`SPLR_RFLAG` = '$merecflag',
					`SPLR_TELNO` = '$mesplrtelno',
					`SPLR_FAXNO` = '$mesplrfaxno',
					`SPLR_EMAIL` = '$mesplremail',
					`SPLR_WEBSITE` = '$mesplrwsite',
					`SPLR_OCONTINFO` = '$mesplrocontinfo',
					`SPLR_CPRSN_MOBNO` = '$mesplrcpcontno',
					`SPLR_CPRSN` = '$mesplrcpname',
					`SPLR_CPRSN_DESGN` = '$mesplrcpdesgn',
					`SPLR_CPRSN_TELNO` = '$mesplrcpcontno',
					`SPLR_CPRSN_EMAIL` = '$mesplrcpemail' 
					where recid = {$rw['recid']} ";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MSUPPLIER_UREC',$mesplrcode,$rw['recid'],$str,'');
					echo "Changes successfuly done!!!";
				} else { 
					$str = "select recid,SPLR_CODE from {$this->db_erp}.`mst_supplier` aa where SPLR_CODE = '$mesplrcode'";
					$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					if($qq->getNumRows() > 0) { 
						$rrw = $qq->getRowArray();
						if($rrw['recid'] !== $rid) {
							echo "Customer Code conflict for update!!!";
						} 
					} else { 
						$adataz = array();
						$adataz[] = "SPLR_CODExOx'{$mesplrcode}'";
						$adataz[] = "SPLR_NAMExOx'{$mesplrname}'";
						$adataz[] = "SPLR_ADDR1xOx'{$mesplraddr1}'";
						$adataz[] = "SPLR_ADDR2xOx'{$mesplraddr2}'";
						$adataz[] = "SPLR_ADDR3xOx'{$mesplraddr3}'";
						$adataz[] = "SPLR_TINNOxOx'{$mesplrtinno}'";
						$adataz[] = "SPLR_EMAILxOx'{$mesplremail}'";
						$adataz[] = "SPLR_WEBSITExOx'{$mesplrwsite}'";
						$adataz[] = "SPLR_OCONTINFOxOx'{$mesplrocontinfo}'";
						$adataz[] = "SPLR_RFLAGxOx'{$merecflag}'";
						$adataz[] = "SPLR_TELNOxOx'{$mesplrtelno}'";
						$adataz[] = "SPLR_FAXNOxOx'{$mesplrfaxno}'";
						$adataz[] = "SPLR_CPRSNxOx'{$mesplrcpname}'";
						$adataz[] = "SPLR_CPRSN_DESGNxOx'{$mesplrcpdesgn}'";
						$adataz[] = "SPLR_CPRSN_TELNOxOx'{$mesplrcpcontno}'";
						$adataz[] = "SPLR_CPRSN_EMAILxOx'{$mesplrcpemail}'";
						$adataz[] = "SPLR_CPRSN_MOBNOxOx'{$mesplrcpcontno}'";
						$str = " recid = {$rw['recid']} ";
						$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_supplier`','MSUPPLIER_UREC',$mesplrcode,$str);
						$str = "update {$this->db_erp}.`mst_supplier` set 
						`SPLR_CODE` = '$mesplrcode',
						`SPLR_NAME` = '$mesplrname',
						`SPLR_ADDR1` = '$mesplraddr1',
						`SPLR_ADDR2` = '$mesplraddr2',
						`SPLR_ADDR3` = '$mesplraddr3',
						`SPLR_TINNO` = '$mesplrtinno',
						`SPLR_RFLAG` = '$merecflag',
						`SPLR_TELNO` = '$mesplrtelno',
						`SPLR_EMAIL` = '$mesplremail',
						`SPLR_WEBSITE` = '$mesplrwsite',
						`SPLR_OCONTINFO` = '$mesplrocontinfo',
						`SPLR_FAXNO` = '$mesplrfaxno',
						`SPLR_CPRSN_MOBNO` = '$mesplrcpcontno',
						`SPLR_CPRSN` = '$mesplrcpname',
						`SPLR_CPRSN_DESGN` = '$mesplrcpdesgn',
						`SPLR_CPRSN_TELNO` = '$mesplrcpcontno',
						`SPLR_CPRSN_EMAIL` = '$mesplrcpemail' 
						where recid = {$rw['recid']} ";
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
						$this->mylibzdb->user_logs_activity_module($this->db_erp,'MSUPPLIER_UREC',$mesplrcode,$rw['recid'],$str,'');
						echo "Updates successfuly done!!!";
					}
					$qq->freeResult();
				}
			} 
			$q->freeResult();
		} else { 
			//adding of records
			$str = "select recid,SPLR_CODE from {$this->db_erp}.`mst_supplier` aa where `SPLR_CODE` = '$mesplrcode'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				echo "Customer Code already EXISTS!!!";
				die();
			} else { 
				if($maction == 'A_REC') { 
					$adataz = array();
					$adataz[] = "SPLR_CODExOx'{$mesplrcode}'";
					$adataz[] = "SPLR_NAMExOx'{$mesplrname}'";
					$adataz[] = "SPLR_ADDR1xOx'{$mesplraddr1}'";
					$adataz[] = "SPLR_ADDR2xOx'{$mesplraddr2}'";
					$adataz[] = "SPLR_ADDR3xOx'{$mesplraddr3}'";
					$adataz[] = "SPLR_TINNOxOx'{$mesplrtinno}'";
					$adataz[] = "SPLR_EMAILxOx'{$mesplremail}'";
					$adataz[] = "SPLR_WEBSITExOx'{$mesplrwsite}'";
					$adataz[] = "SPLR_OCONTINFOxOx'{$mesplrocontinfo}'";
					$adataz[] = "SPLR_RFLAGxOx'{$merecflag}'";
					$adataz[] = "SPLR_TELNOxOx'{$mesplrtelno}'";
					$adataz[] = "SPLR_FAXNOxOx'{$mesplrfaxno}'";
					$adataz[] = "SPLR_CPRSNxOx'{$mesplrcpname}'";
					$adataz[] = "SPLR_CPRSN_DESGNxOx'{$mesplrcpdesgn}'";
					$adataz[] = "SPLR_CPRSN_TELNOxOx'{$mesplrcpcontno}'";
					$adataz[] = "SPLR_CPRSN_EMAILxOx'{$mesplrcpemail}'";
					$adataz[] = "SPLR_CPRSN_MOBNOxOx'{$mesplrcpcontno}'";
					
					$str = " SPLR_CODE = '$mesplrcode' ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_supplier`','MSUPPLIER_AREC',$mesplrcode,$str);
					$str = "
					insert into {$this->db_erp}.`mst_supplier` (
					`SPLR_CODE`,`SPLR_NAME`,`SPLR_ADDR1`,`SPLR_ADDR2`,`SPLR_ADDR3`,`SPLR_TINNO`,`SPLR_EMAIL`,
					`SPLR_RFLAG`,`SPLR_TELNO`,`SPLR_FAXNO`,`SPLR_CPRSN`,`SPLR_CPRSN_DESGN`,`SPLR_CPRSN_TELNO`,`SPLR_WEBSITE`,`SPLR_OCONTINFO`,
					`SPLR_CPRSN_MOBNO`,`SPLR_CPRSN_EMAIL`,`MUSER`,`ENCD`
					) values (
					'$mesplrcode','$mesplrname','$mesplraddr1','$mesplraddr2','$mesplraddr3','$mesplrtinno','$mesplremail',
					'$merecflag','$mesplrtelno','$mesplrfaxno','$mesplrcpname','$mesplrcpdesgn','$mesplrcpcontno','$mesplrwsite','$mesplrocontinfo',
					'$mesplrcpcontno','$mesplrcpemail','$cuser',now()
					) 
					";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MSUPPLIER_AREC',$mesplrcode,$mesplrcode,$str,'');
					echo "Records successfuly added!!!";
				} else { //end A_REC validation 
					echo "INVALID OPERATION!!!";
				}
			}
		} //end mtkn_etr validation 
		
	} //end profile_save
	
} //end main class
