<?php
namespace App\Models;
use CodeIgniter\Model;
class MyMDCustomerModel extends Model
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
			$str_optn = " where (CUST_CODE like '%$msearchrec%' or CUST_NAME like '%$msearchrec%') ";
		}
		
		$strqry = "
		select aa.*,
		sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`mst_customer` aa {$str_optn} 
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
		
		$mecustcode = $this->dbx->escapeString($this->request->getVar('mecustcode'));
		$mecustname = $this->dbx->escapeString($this->request->getVar('mecustname'));
		$merecflag = $this->request->getVar('merecflag');
		$mecusttinno = $this->request->getVar('mecusttinno');
		$mecustaddr1 = $this->dbx->escapeString($this->request->getVar('mecustaddr1'));
		$mecustaddr2 = $this->dbx->escapeString($this->request->getVar('mecustaddr2'));
		$mecustaddr3 = $this->dbx->escapeString($this->request->getVar('mecustaddr3'));
		$mecusttelno = $this->request->getVar('mecusttelno');
		$mecustfaxno = $this->request->getVar('mecustfaxno');
		$mecustemail = $this->request->getVar('mecustemail');
		$mecustcpname = $this->dbx->escapeString($this->request->getVar('mecustcpname'));
		$mecustcpdesgn = $this->dbx->escapeString($this->request->getVar('mecustcpdesgn'));
		$mecustcpcontno = $this->request->getVar('mecustcpcontno');
		$mecustcpemail = $this->request->getVar('mecustcpemail');
		$mecustwsite = $this->request->getVar('mecustwsite');
		$mecustocontinfo = $this->dbx->escapeString($this->request->getVar('mecustocontinfo'));
		
		//updating of records
		if(!empty($mtkn_etr)) { 
			$str = "select recid,CUST_CODE from {$this->db_erp}.`mst_customer` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				$rid = $rw['recid'];
				if($mecustcode == $rw['CUST_CODE']) { 
					$adataz = array();
					$adataz[] = "CUST_NAMExOx'{$mecustname}'";
					$adataz[] = "CUST_ADDR1xOx'{$mecustaddr1}'";
					$adataz[] = "CUST_ADDR2xOx'{$mecustaddr2}'";
					$adataz[] = "CUST_ADDR3xOx'{$mecustaddr3}'";
					$adataz[] = "CUST_TINNOxOx'{$mecusttinno}'";
					$adataz[] = "CUST_EMAILxOx'{$mecustemail}'";
					$adataz[] = "CUST_WEBSITExOx'{$mecustwsite}'";
					$adataz[] = "CUST_OCONTINFOxOx'{$mecustocontinfo}'";
					$adataz[] = "CUST_RFLAGxOx'{$merecflag}'";
					$adataz[] = "CUST_TELNOxOx'{$mecusttelno}'";
					$adataz[] = "CUST_FAXNOxOx'{$mecustfaxno}'";
					$adataz[] = "CUST_CPRSNxOx'{$mecustcpname}'";
					$adataz[] = "CUST_CPRSN_DESGNxOx'{$mecustcpdesgn}'";
					$adataz[] = "CUST_CPRSN_TELNOxOx'{$mecustcpcontno}'";
					$adataz[] = "CUST_CPRSN_EMAILxOx'{$mecustcpemail}'";
					$adataz[] = "CUST_CPRSN_MOBNOxOx'{$mecustcpcontno}'";
					$str = " recid = {$rw['recid']} ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_customer`','MCUSTOMER_UREC',$mecustcode,$str);
					$str = "update {$this->db_erp}.`mst_customer` set CUST_NAME = '$mecustname',
					`CUST_ADDR1` = '$mecustaddr1',
					`CUST_ADDR2` = '$mecustaddr2',
					`CUST_ADDR3` = '$mecustaddr3',
					`CUST_TINNO` = '$mecusttinno',
					`CUST_RFLAG` = '$merecflag',
					`CUST_TELNO` = '$mecusttelno',
					`CUST_FAXNO` = '$mecustfaxno',
					`CUST_EMAIL` = '$mecustemail',
					`CUST_WEBSITE` = '$mecustwsite',
					`CUST_OCONTINFO` = '$mecustocontinfo',
					`CUST_CPRSN_MOBNO` = '$mecustcpcontno',
					`CUST_CPRSN` = '$mecustcpname',
					`CUST_CPRSN_DESGN` = '$mecustcpdesgn',
					`CUST_CPRSN_TELNO` = '$mecustcpcontno',
					`CUST_CPRSN_EMAIL` = '$mecustcpemail' 
					where recid = {$rw['recid']} ";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MCUSTOMER_UREC',$mecustcode,$rw['recid'],$str,'');
					echo "Changes successfuly done!!!";
				} else { 
					$str = "select recid,CUST_CODE from {$this->db_erp}.`mst_customer` aa where CUST_CODE = '$mecustcode'";
					$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					if($qq->getNumRows() > 0) { 
						$rrw = $qq->getRowArray();
						if($rrw['recid'] !== $rid) {
							echo "Customer Code conflict for update!!!";
						} 
					} else { 
						$adataz = array();
						$adataz[] = "CUST_CODExOx'{$mecustcode}'";
						$adataz[] = "CUST_NAMExOx'{$mecustname}'";
						$adataz[] = "CUST_ADDR1xOx'{$mecustaddr1}'";
						$adataz[] = "CUST_ADDR2xOx'{$mecustaddr2}'";
						$adataz[] = "CUST_ADDR3xOx'{$mecustaddr3}'";
						$adataz[] = "CUST_TINNOxOx'{$mecusttinno}'";
						$adataz[] = "CUST_EMAILxOx'{$mecustemail}'";
						$adataz[] = "CUST_WEBSITExOx'{$mecustwsite}'";
						$adataz[] = "CUST_OCONTINFOxOx'{$mecustocontinfo}'";
						$adataz[] = "CUST_RFLAGxOx'{$merecflag}'";
						$adataz[] = "CUST_TELNOxOx'{$mecusttelno}'";
						$adataz[] = "CUST_FAXNOxOx'{$mecustfaxno}'";
						$adataz[] = "CUST_CPRSNxOx'{$mecustcpname}'";
						$adataz[] = "CUST_CPRSN_DESGNxOx'{$mecustcpdesgn}'";
						$adataz[] = "CUST_CPRSN_TELNOxOx'{$mecustcpcontno}'";
						$adataz[] = "CUST_CPRSN_EMAILxOx'{$mecustcpemail}'";
						$adataz[] = "CUST_CPRSN_MOBNOxOx'{$mecustcpcontno}'";
						$str = " recid = {$rw['recid']} ";
						$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_customer`','MCUSTOMER_UREC',$mecustcode,$str);
						$str = "update {$this->db_erp}.`mst_customer` set 
						`CUST_CODE` = '$mecustcode',
						`CUST_NAME` = '$mecustname',
						`CUST_ADDR1` = '$mecustaddr1',
						`CUST_ADDR2` = '$mecustaddr2',
						`CUST_ADDR3` = '$mecustaddr3',
						`CUST_TINNO` = '$mecusttinno',
						`CUST_RFLAG` = '$merecflag',
						`CUST_TELNO` = '$mecusttelno',
						`CUST_EMAIL` = '$mecustemail',
						`CUST_WEBSITE` = '$mecustwsite',
						`CUST_OCONTINFO` = '$mecustocontinfo',
						`CUST_FAXNO` = '$mecustfaxno',
						`CUST_CPRSN_MOBNO` = '$mecustcpcontno',
						`CUST_CPRSN` = '$mecustcpname',
						`CUST_CPRSN_DESGN` = '$mecustcpdesgn',
						`CUST_CPRSN_TELNO` = '$mecustcpcontno',
						`CUST_CPRSN_EMAIL` = '$mecustcpemail' 
						where recid = {$rw['recid']} ";
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
						$this->mylibzdb->user_logs_activity_module($this->db_erp,'MCUSTOMER_UREC',$mecustcode,$rw['recid'],$str,'');
						echo "Updates successfuly done!!!";
					}
					$qq->freeResult();
				}
			} 
			$q->freeResult();
		} else { 
			//adding of records
			$str = "select recid,CUST_CODE from {$this->db_erp}.`mst_customer` aa where `CUST_CODE` = '$mecustcode'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				echo "Customer Code already EXISTS!!!";
				die();
			} else { 
				if($maction == 'A_REC') { 
					$adataz = array();
					$adataz[] = "CUST_CODExOx'{$mecustcode}'";
					$adataz[] = "CUST_NAMExOx'{$mecustname}'";
					$adataz[] = "CUST_ADDR1xOx'{$mecustaddr1}'";
					$adataz[] = "CUST_ADDR2xOx'{$mecustaddr2}'";
					$adataz[] = "CUST_ADDR3xOx'{$mecustaddr3}'";
					$adataz[] = "CUST_TINNOxOx'{$mecusttinno}'";
					$adataz[] = "CUST_EMAILxOx'{$mecustemail}'";
					$adataz[] = "CUST_WEBSITExOx'{$mecustwsite}'";
					$adataz[] = "CUST_OCONTINFOxOx'{$mecustocontinfo}'";
					$adataz[] = "CUST_RFLAGxOx'{$merecflag}'";
					$adataz[] = "CUST_TELNOxOx'{$mecusttelno}'";
					$adataz[] = "CUST_FAXNOxOx'{$mecustfaxno}'";
					$adataz[] = "CUST_CPRSNxOx'{$mecustcpname}'";
					$adataz[] = "CUST_CPRSN_DESGNxOx'{$mecustcpdesgn}'";
					$adataz[] = "CUST_CPRSN_TELNOxOx'{$mecustcpcontno}'";
					$adataz[] = "CUST_CPRSN_EMAILxOx'{$mecustcpemail}'";
					$adataz[] = "CUST_CPRSN_MOBNOxOx'{$mecustcpcontno}'";
					
					$str = " CUST_CODE = '$mecustcode' ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_customer`','MCUSTOMER_AREC',$mecustcode,$str);
					$str = "
					insert into {$this->db_erp}.`mst_customer` (
					`CUST_CODE`,`CUST_NAME`,`CUST_ADDR1`,`CUST_ADDR2`,`CUST_ADDR3`,`CUST_TINNO`,`CUST_EMAIL`,
					`CUST_RFLAG`,`CUST_TELNO`,`CUST_FAXNO`,`CUST_CPRSN`,`CUST_CPRSN_DESGN`,`CUST_CPRSN_TELNO`,`CUST_WEBSITE`,`CUST_OCONTINFO`,
					`CUST_CPRSN_MOBNO`,`CUST_CPRSN_EMAIL`,`MUSER`,`ENCD`
					) values (
					'$mecustcode','$mecustname','$mecustaddr1','$mecustaddr2','$mecustaddr3','$mecusttinno','$mecustemail',
					'$merecflag','$mecusttelno','$mecustfaxno','$mecustcpname','$mecustcpdesgn','$mecustcpcontno','$mecustwsite','$mecustocontinfo',
					'$mecustcpcontno','$mecustcpemail','$cuser',now()
					) 
					";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MCUSTOMER_AREC',$mecustcode,$mecustcode,$str,'');
					echo "Records successfuly added!!!";
				} else { //end A_REC validation 
					echo "INVALID OPERATION!!!";
				}
			}
		} //end mtkn_etr validation 
		
	} //end profile_save
	
} //end main class
