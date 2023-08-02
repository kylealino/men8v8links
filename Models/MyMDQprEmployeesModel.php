<?php
namespace App\Models;
use CodeIgniter\Model;
class MyMDQprEmployeesModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->request = \Config\Services::request();
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibzdb = model('App\Models\MyLibzDBModel');
		$this->dbx = $this->mylibzdb->dbx;
		$this->mylibzsys = model('App\Models\MyLibzSysModel');
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
			$str_optn = " where (EMPNUMB like '%$msearchrec%' or EMPLNAME like '%$msearchrec%' or 
			EMPFNAME like '%$msearchrec%') ";
		}
		
		$strqry = "
		select aa.*,
		IF(aa.`EMPRSTAT` = 'A', 'Active','In-Active') _MRECSTAT,
		sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`mst_employee` aa {$str_optn} 
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
		
		
		$meeaddr1 = $this->mylibzdb->me_escapeString($this->request->getVar('meeaddr1'));
		$meeaddr2 = $this->mylibzdb->me_escapeString($this->request->getVar('meeaddr2'));
		$meeaddr3 = $this->mylibzdb->me_escapeString($this->request->getVar('meeaddr3'));
		
		
		$meenumb = $this->request->getVar('meenumb');
		$meelname = $this->request->getVar('meelname');
		$meefname = $this->request->getVar('meefname');
		$meemname = $this->request->getVar('meemname');
		$meegend = $this->request->getVar('meegend');
		$meebdte = $this->mylibzsys->mydate_yyyymmdd($this->request->getVar('meebdte'));
		$meebplace = $this->mylibzdb->me_escapeString($this->request->getVar('meebplace'));
		$medateh = $this->mylibzsys->mydate_yyyymmdd($this->request->getVar('medateh'));
		$meectzns = $this->request->getVar('meectzns');
		$meerelgn = $this->request->getVar('meerelgn');
		$meecs = $this->request->getVar('meecs');
		$merecflag = $this->request->getVar('merecflag');
		$meecpno = $this->request->getVar('meecpno');
		$meetelno = $this->request->getVar('meetelno');
		$meeemail = $this->request->getVar('meeemail');
		$meecontinfo = $this->request->getVar('meecontinfo');
		$meecpname = $this->request->getVar('meecpname');
		$meecpdesgn = $this->mylibzdb->me_escapeString($this->request->getVar('meecpdesgn'));
		$meecpcontno = $this->request->getVar('meecpcontno');
		$meecpemail = $this->request->getVar('meecpemail');
		$meecprela = $this->request->getVar('meecprela');
		$meetinno = $this->request->getVar('meetinno');
		$meesssno = $this->request->getVar('meesssno');
		$meehdmfno = $this->request->getVar('meehdmfno');
		$meephilhno = $this->request->getVar('meephilhno');
		
		//updating of records
		if(!empty($mtkn_etr)) { 
			$str = "select recid,EMPNUMB from {$this->db_erp}.`mst_employee` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				if($meenumb == $rw['EMPNUMB']) { 
					$adataz = array();
					$adataz[] = "EMPNUMBxOx'{$meenumb}'";
					$adataz[] = "EMPIDNOxOx'{$meenumb}'";
					$adataz[] = "EMPLNAMExOx'{$meelname}'";
					$adataz[] = "EMPFNAMExOx'{$meefname}'";
					$adataz[] = "EMPMNAMExOx'{$meemname}'";
					$adataz[] = "EMPBDTExOxdate('{$meebdte}')";
					$adataz[] = "EMPHDTExOxdate('{$medateh}')";
					$adataz[] = "EMPBPLACExOx'{$meebplace}'";
					$adataz[] = "EMPCTZNxOx'{$meectzns}'";
					$adataz[] = "EMPGNDRxOx'{$meegend}'";
					$adataz[] = "EMPRLGNxOx'{$meerelgn}'";
					$adataz[] = "EMPCVLSxOx'{$meecs}'";
					$adataz[] = "EMPADDR1xOx'{$meeaddr1}'";
					$adataz[] = "EMPADDR2xOx'{$meeaddr2}'";
					$adataz[] = "EMPADDR3xOx'{$meeaddr3}'";
					$adataz[] = "EMPMOBNxOx'{$meecpno}'";
					$adataz[] = "EMPTELNxOx'{$meetelno}'";
					$adataz[] = "EMPEMAILxOx'{$meeemail}'";
					$adataz[] = "EMPOCNUMxOx'{$meecontinfo}'";
					$adataz[] = "EMPCPNAMExOx'{$meecpname}'";
					$adataz[] = "EMPCPDESGNxOx'{$meecpdesgn}'";
					$adataz[] = "EMPCPCONTNxOx'{$meecpcontno}'";
					$adataz[] = "EMPCPEMAILxOx'{$meecpemail}'";
					$adataz[] = "EMPCPRELAxOx'{$meecprela}'";
					$adataz[] = "EMPTINNOxOx'{$meetinno}'";
					$adataz[] = "EMPHDMFIDxOx'{$meehdmfno}'";
					$adataz[] = "EMPPHILHIDxOx'{$meephilhno}'";
					$adataz[] = "EMPSSSNOxOx'{$meesssno}'";
					$adataz[] = "EMPRSTATxOx'{$merecflag}'";
					$str = " recid = {$rw['recid']} ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_employee`','MDQPR_EMP_UREC',$meenumb,$str);
					
					$str = "update {$this->db_erp}.`mst_employee` set EMPLNAME = '$meelname',
					`EMPFNAME` = '$meefname',
					`EMPMNAME` = '$meemname',
					`EMPBDTE` = date('$meebdte'),
					`EMPHDTE` = date('$medateh'),
					`EMPBPLACE` = '$meebplace',
					`EMPCTZN` = '$meectzns',
					`EMPGNDR` = '$meegend',
					`EMPRSTAT` = '$merecflag',
					`EMPRLGN` = '$meerelgn',
					`EMPCVLS` = '$meecs',
					`EMPADDR1` = '$meeaddr1',
					`EMPADDR2` = '$meeaddr2',
					`EMPADDR3` = '$meeaddr3',
					`EMPADDR3` = '$meeaddr3',
					`EMPMOBN` = '$meecpno',
					`EMPTELN` = '$meetelno',
					`EMPEMAIL` = '$meeemail',
					`EMPOCNUM` = '$meecontinfo',
					`EMPCPNAME` = '$meecpname',
					`EMPCPDESGN` = '$meecpdesgn',
					`EMPCPCONTN` = '$meecpcontno',
					`EMPCPEMAIL` = '$meecpemail',
					`EMPCPRELA` = '$meecprela',
					`EMPTINNO` = '$meetinno',
					`EMPHDMFID` = '$meehdmfno',
					`EMPPHILHID` = '$meephilhno',
					`EMPSSSNO` = '$meesssno' 
					where recid = {$rw['recid']} ";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MDQPR_EMP_UREC',$meenumb,$meenumb,$str,'');
					echo "Changes successfuly done!!!";
				} else { 
					echo "Material Code conflict for update!!!";
				}
			} 
		} else { 
			//adding of records
			$str = "select recid,EMPNUMB from {$this->db_erp}.`mst_employee` aa where `EMPNUMB` = '$meenumb'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				echo "Employee ID already EXISTS!!!";
				die();
			} else { 
				if($maction == 'A_REC') { 
					$adataz = array();
					$adataz[] = "EMPNUMBxOx'{$meenumb}'";
					$adataz[] = "EMPIDNOxOx'{$meenumb}'";
					$adataz[] = "EMPLNAMExOx'{$meelname}'";
					$adataz[] = "EMPFNAMExOx'{$meefname}'";
					$adataz[] = "EMPMNAMExOx'{$meemname}'";
					$adataz[] = "EMPBDTExOxdate('{$meebdte}')";
					$adataz[] = "EMPHDTExOxdate('{$medateh}')";
					$adataz[] = "EMPBPLACExOx'{$meebplace}'";
					$adataz[] = "EMPCTZNxOx'{$meectzns}'";
					$adataz[] = "EMPGNDRxOx'{$meegend}'";
					$adataz[] = "EMPRLGNxOx'{$meerelgn}'";
					$adataz[] = "EMPCVLSxOx'{$meecs}'";
					$adataz[] = "EMPADDR1xOx'{$meeaddr1}'";
					$adataz[] = "EMPADDR2xOx'{$meeaddr2}'";
					$adataz[] = "EMPADDR3xOx'{$meeaddr3}'";
					$adataz[] = "EMPMOBNxOx'{$meecpno}'";
					$adataz[] = "EMPTELNxOx'{$meetelno}'";
					$adataz[] = "EMPEMAILxOx'{$meeemail}'";
					$adataz[] = "EMPOCNUMxOx'{$meecontinfo}'";
					$adataz[] = "EMPCPNAMExOx'{$meecpname}'";
					$adataz[] = "EMPCPDESGNxOx'{$meecpdesgn}'";
					$adataz[] = "EMPCPCONTNxOx'{$meecpcontno}'";
					$adataz[] = "EMPCPEMAILxOx'{$meecpemail}'";
					$adataz[] = "EMPCPRELAxOx'{$meecprela}'";
					$adataz[] = "EMPTINNOxOx'{$meetinno}'";
					$adataz[] = "EMPHDMFIDxOx'{$meehdmfno}'";
					$adataz[] = "EMPPHILHIDxOx'{$meephilhno}'";
					$adataz[] = "EMPSSSNOxOx'{$meesssno}'";
					$adataz[] = "EMPRSTATxOx'{$merecflag}'";
					$str = " EMPNUMB = '$meenumb' ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_employee`','MDQPR_EMP_AREC',$meenumb,$str);
					$str = "
					insert into {$this->db_erp}.`mst_employee` (
					`EMPNUMB`,
					`EMPIDNO`,
					`EMPLNAME`,
					`EMPFNAME`,
					`EMPMNAME`,
					`EMPBDTE`,
					`EMPBPLACE`,
					`EMPGNDR`,
					`EMPCTZN`,
					`EMPRLGN`,
					`EMPCVLS`,
					`EMPADDR1`,
					`EMPADDR2`,
					`EMPADDR3`,
					`EMPMOBN`,
					`EMPTELN`,
					`EMPEMAIL`,
					`EMPOCNUM`,
					`EMPCPNAME`,
					`EMPCPDESGN`,
					`EMPCPCONTN`,
					`EMPCPEMAIL`,
					`EMPCPRELA`,
					`EMPTINNO`,
					`EMPHDMFID`,
					`EMPPHILHID`,
					`EMPSSSNO`,
					`EMPHDTE`,
					`EMPRSTAT`,
					`MUSER`
					) values (
					'$meenumb','$meenumb','$meelname','$meefname','$meemname',date('$meebdte'),'$meebplace','$meegend',
					'$meectzns','$meerelgn','$meecs','$meeaddr1','$meeaddr2','$meeaddr3','$meecpno','$meetelno',
					'$meeemail','$meecontinfo','$meecpname','$meecpdesgn','$meecpcontno','$meecpemail','$meecprela',
					'$meetinno','$meehdmfno','$meephilhno','$meesssno',date('$medateh'),'$merecflag','$cuser'
					)
					";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MDQPR_EMP_AREC',$meenumb,$meenumb,$str,'');
					echo "Records successfuly added!!!";
				} else { //end A_REC validation 
					echo "INVALID OPERATION!!!";
				}
			}
		} //end mtkn_etr validation 
		
	} //end profile_save
	
} //end main class
