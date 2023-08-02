<?php
namespace App\Models;
use CodeIgniter\Model;
use DateTime;

class MyInventoryReconAdjModel extends Model
{
	// .. other member variables
	protected $db;
	public function __construct()
	{
		parent::__construct();
		$this->session = session();
		$this->request = \Config\Services::request();
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->db_br = $this->mydbname->medb(1);
		$this->db_temp = $this->mydbname->medb(2);
		$this->mylibzdb = model('App\Models\MyLibzDBModel');
		$this->mylibzsys = model('App\Models\MyLibzSysModel');
        $this->myusermod = model('App\Models\MyUserModel');
		$this->mydataz = model('App\Models\MyDatumModel');
	}
	
	public function save_entry() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$fld_branch = $this->request->getVar('fld_branch');
		$br_mtknattr = $this->request->getVar('br_mtknattr');
		$mearray = $this->request->getVar('mearray');
		$cseqn_mtknattr = $this->request->getVar('cseqn_mtknattr');
		
		//check user access for saving records  
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'02','0004','00040204')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		}			
		
		$me_recadj_rmk = $this->mylibzdb->me_escapeString($this->request->getVar('me_recadj_rmk'));
		if(!empty($br_mtknattr)): 
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$br_mtknattr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if ($q->getNumRows() > 0): 
				$rw = $q->getRowArray();
				$br_id = $rw['recid'];
				$br_ocode2 = $rw['B_OCODE2'];
			else:
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!</div>";
				die();
			endif;
			$q->freeResult();
		else:
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Branch Data is REQUIRED!!!</div>";
			die();
		endif;
		
		if (count($mearray) > 0):
			foreach($mearray as $xdata):
				$medata = explode("x|x",$xdata);
				$itemc = $medata[0];
				$itemc_ded = $medata[1];
				$itemc_add = $medata[2];
				$itemc_remk = $medata[3];
				$itemc_dtattr = $medata[4];
				$str = "select recid from {$this->db_erp}.mst_article where ART_CODE = '{$itemc}'";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				if($q->getNumRows() > 0):
				else:
					echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Article Code [{$itemc}]!!!</div>";
					die();
				endif;
			endforeach;
		else: 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No valid entries to proceed!!!</div>";
			die();
		endif;
		
		$str = "
		create table if not exists {$this->db_erp}.`trx_ivty_reconadj_hd` ( 
			`recid` int(25) NOT NULL AUTO_INCREMENT,
			`ira_hd_ctrlno` varchar(25) DEFAULT '', -- Inventory Recon Adjustment Header Control Number Sequence
			`ira_branch_id` int(9) DEFAULT NULL, -- Inventory Recon Adjustment Branch ID
			`ira_remk` longtext, -- Remarks 
			`ira_posted` varchar(2) DEFAULT '', -- Posting Remarks Y-Yes or N-Not yet 
			`ira_dateposted` datetime, -- Posting Date 
			`mencd` timestamp default current_timestamp(),  -- Current Date/Time 
			`muser` varchar(25) default '',  -- User 
			PRIMARY KEY (`recid`),
			UNIQUE KEY `idx02` (`ira_hd_ctrlno`),
			KEY `idx03` (`ira_posted`),
			KEY `idx04` (`muser`),
			KEY `idx05` (`mencd`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = "
		create table if not exists {$this->db_erp}.`trx_ivty_reconadj_dt` ( 
			`recid` int(25) NOT NULL AUTO_INCREMENT,
			`ira_hd_id` int(9) DEFAULT NULL, -- Inventory Recon Adjustment Header ID
			`ira_hd_ctrlno` varchar(25) DEFAULT '', -- Inventory Recon Adjustment Header Control Number Sequence
			`ira_branch_id` int(9) DEFAULT NULL, -- Inventory Recon Adjustment Branch ID
			`ira_artcode` varchar(25) default '', -- Item Code
			`ira_artm_rid` int(9) DEFAULT NULL, -- Item Code Record ID
			`ira_ded_qty` double(15,4) default 0.0000, -- Item Quantity Deduct Adjustment
			`ira_add_qty` double(15,4) default 0.0000, -- Item Quantity Add Adjustment
			`ira_item_remk` varchar(150) default '', -- Item Remarks 
			`mencd` timestamp default current_timestamp(),
			`muser` varchar(25) default '',
			PRIMARY KEY (`recid`),
			KEY `idx01` (`ira_hd_id`),
			KEY `idx02` (`ira_hd_ctrlno`),
			KEY `idx03` (`ira_branch_id`),
			KEY `idx04` (`mencd`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		$str = "
		create table if not exists {$this->db_erp}.`trx_ivty_reconadj_upld_files` ( 
			`recid` int(25) NOT NULL AUTO_INCREMENT,
			`ira_hd_ctrlno` varchar(25) DEFAULT '', -- Inventory Recon Adjustment Header Control Number Sequence
			`ira_filename` varchar(150) DEFAULT '', -- Uploaded File 
			`mencd` timestamp default current_timestamp(),  -- Current Date/Time 
			`muser` varchar(25) default '',  -- User 
			PRIMARY KEY (`recid`),
			UNIQUE KEY `idx02` (`ira_hd_ctrlno`),
			KEY `idx04` (`muser`),
			KEY `idx05` (`mencd`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$hd_rid = 0;
		if (empty($cseqn_mtknattr)):
			$cseqn = $this->get_ctr_seqn($this->db_erp,'CTRL_NO01',6);
		else: 
			$str = "select `recid`, `ira_hd_ctrlno` from {$this->db_erp}.`trx_ivty_reconadj_hd` where sha2(concat(`recid`,'{$mpw_tkn}'),384) =  '{$cseqn_mtknattr}'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if ($q->getNumRows() > 0): 
				$rw = $q->getRowArray();
				$hd_rid = $rw['recid'];
				$cseqn = $rw['ira_hd_ctrlno'];
				
				$arrfield = array();
				$arrfield[] = "ira_remk" . "xOx'" . $me_recadj_rmk . "'";
				$arrfield[] = "muser" . "xOx'" . $cuser . "'";
				$str = " `ira_hd_ctrlno` = '{$cseqn}' ";
				$this->mylibzdb->logs_modi_audit($arrfield,$this->db_erp,'`trx_ivty_reconadj_hd`','IVTY_RECONADJ_HD',$cseqn,$str);
				
				$str = "update {$this->db_erp}.`trx_ivty_reconadj_hd` set `ira_remk` = '$me_recadj_rmk'
				where `recid` = {$hd_rid}";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			else:
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Transaction RECORDS CREDENTIALS!!!</div>";
				die();
			endif;
			$q->freeResult();
		endif;
		$str = "select `recid`, `ira_hd_ctrlno` from {$this->db_erp}.`trx_ivty_reconadj_hd` where `ira_hd_ctrlno` =  '{$cseqn}'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if ($q->getNumRows() > 0):
			$rw = $q->getRowArray();
		else: 
			$arrfield = array();
			$arrfield[] = "ira_hd_ctrlno" . "xOx'" . $cseqn . "'";
			$arrfield[] = "ira_branch_id" . "xOx'" . $br_id . "'";
			$arrfield[] = "ira_remk" . "xOx'" . $me_recadj_rmk . "'";
			$arrfield[] = "muser" . "xOx'" . $cuser . "'";
			$str = " `ira_hd_ctrlno` = '{$cseqn}' ";
			$this->mylibzdb->logs_modi_audit($arrfield,$this->db_erp,'`trx_ivty_reconadj_hd`','IVTY_RECONADJ_HD',$cseqn,$str);
		
			$str = "insert into {$this->db_erp}.`trx_ivty_reconadj_hd` (
			`ira_hd_ctrlno`,`ira_branch_id`,`ira_remk`,`muser`
			) values('$cseqn',$br_id,'$me_recadj_rmk','$cuser')";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$str = "select `recid`, `ira_hd_ctrlno` from {$this->db_erp}.`trx_ivty_reconadj_hd` where `ira_hd_ctrlno` =  '{$cseqn}'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if ($q->getNumRows() > 0): 
				$rw = $q->getRowArray();
				$hd_rid = $rw['recid'];
			endif;
			$q->freeResult();
		endif;
		
		foreach($mearray as $xdata):
			$medata = explode("x|x",$xdata);
			$itemc = $this->mylibzdb->me_escapeString($medata[0]);
			$itemc_ded = (!empty($medata[1]) ? ($medata[1] + 0) : 0);
			$itemc_add = (!empty($medata[2]) ? ($medata[2] + 0) : 0);
			$itemc_remk = $medata[3];
			$itemc_dtattr = $medata[4];
			$martc_id = 0;
			
			$arrfield = array();
			$arrfield[] = "ira_hd_id" . "xOx'" . $hd_rid . "'";
			$arrfield[] = "ira_hd_ctrlno" . "xOx'" . $cseqn . "'";
			$arrfield[] = "ira_branch_id" . "xOx'" . $br_id . "'";
			$arrfield[] = "ira_artcode" . "xOx'" . $itemc . "'";
			$arrfield[] = "ira_ded_qty" . "xOx'" . $itemc_ded . "'";
			$arrfield[] = "ira_add_qty" . "xOx'" . $itemc_add . "'";
			$arrfield[] = "ira_item_remk" . "xOx'" . $itemc_remk . "'";
			$arrfield[] = "muser" . "xOx'" . $cuser . "'";
			$str = " `ira_hd_id` = {$hd_rid} and `ira_branch_id` = {$br_id} and `ira_artcode` = '$itemc' ";
			$this->mylibzdb->logs_modi_audit($arrfield,$this->db_erp,'`trx_ivty_reconadj_dt`','IVTY_RECONADJ_DT',$cseqn,$str);
			
			if(empty($itemc_dtattr) || $itemc_dtattr == 'undefined'):
				$str = "select `recid`,`ira_hd_id` from {$this->db_erp}.`trx_ivty_reconadj_dt` where `ira_hd_id` = {$hd_rid} and `ira_branch_id` = {$br_id} 
				and `ira_artcode` = '$itemc'";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				if ($q->getNumRows() > 0): 
					$rw = $q->getRowArray();
					$dt_rid = $rw['recid'];
					$str = "update {$this->db_erp}.`trx_ivty_reconadj_dt` set 
					`ira_artcode` = ifnull((select `ART_CODE` from {$this->db_erp}.mst_article where ART_CODE = '$itemc' limit 1),''),
					`ira_artm_rid` = ifnull((select `recid` from {$this->db_erp}.mst_article where ART_CODE = '$itemc' limit 1),''),
					`ira_ded_qty` = {$itemc_ded},
					`ira_add_qty` = {$itemc_add},
					`ira_item_remk` = '$itemc_remk' 
					where `recid` = {$dt_rid}";
				else:
					$str = "insert into {$this->db_erp}.`trx_ivty_reconadj_dt` (
					`ira_hd_id`,`ira_hd_ctrlno`,`ira_branch_id`,`ira_artcode`,`ira_artm_rid`,`ira_ded_qty`,`ira_add_qty`,`ira_item_remk`,`muser`
					) values('$hd_rid','$cseqn','$br_id',ifnull((select `ART_CODE` from {$this->db_erp}.mst_article where ART_CODE = '$itemc' limit 1),''),
						ifnull((select `recid` from {$this->db_erp}.mst_article where ART_CODE = '$itemc' limit 1),0),{$itemc_ded},{$itemc_add},'$itemc_remk','$cuser')
					";
				endif;
				$q->freeResult();
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			else:
				$str = "select `recid`,`ira_hd_id` from {$this->db_erp}.`trx_ivty_reconadj_dt` where sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$itemc_dtattr'";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				if ($q->getNumRows() > 0): 
					$rw = $q->getRowArray();
					$dt_rid = $rw['recid'];
					$str = "update {$this->db_erp}.`trx_ivty_reconadj_dt` set 
					`ira_artcode` = ifnull((select `ART_CODE` from {$this->db_erp}.mst_article where ART_CODE = '$itemc' limit 1),''),
					`ira_artm_rid` = ifnull((select `recid` from {$this->db_erp}.mst_article where ART_CODE = '$itemc' limit 1),''),
					`ira_ded_qty` = {$itemc_ded},
					`ira_add_qty` = {$itemc_add},
					`ira_item_remk` = '$itemc_remk' 
					where `recid` = {$dt_rid}";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				else:
					echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Detail RECORDS CREDENTIALS!!!</div>";
					die();
				endif;
				$q->freeResult();
				
			endif;
			
			
		endforeach;
		
		$mefiles_path = ROOTPATH . 'public/uploads/mereconadj_uploads/';
		$mefiles_upath = 'uploads/mereconadj_uploads/';
		
		if ($mefiles = $this->request->getFiles()) { 
			if (count($mefiles['mefiles']) > 0): 
				$str = "delete from {$this->db_erp}.trx_ivty_reconadj_upld_files where ira_hd_ctrlno = '$cseqn'";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			endif;
			foreach ($mefiles['mefiles'] as $mfile) {
				if ($mfile->isValid() && ! $mfile->hasMoved()) { 
					$newName = $mfile->getRandomName();
					$__upld_filename = '';
					$itisfilename = $mfile->getName();
					$itisfilename = $this->mylibzdb->me_escapeString(str_replace(' ','_',$itisfilename));
					if($mfile->getMimeType() == 'application/pdf') { 
						$__upld_filename = $cuser . '_' . $itisfilename;
					} else { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please select only <strong>PDF </strong> file.</div>";
						die();
					}
					
					if(!empty($__upld_filename)) { 
						if (file_exists($mefiles_path . $__upld_filename)) { 
							unlink($mefiles_path . $__upld_filename);
						}
						$mfile->move($mefiles_path, $__upld_filename);
						
						$arrfield = array();
						$arrfield[] = "ira_filename" . "xOx'" . $__upld_filename . "'";
						$arrfield[] = "muser" . "xOx'" . $cuser . "'";
						$str = " `ira_hd_ctrlno` = '{$cseqn}' ";
						$this->mylibzdb->logs_modi_audit($arrfield,$this->db_erp,'`trx_ivty_reconadj_upld_files`','IVTY_RECONADJ_FILES',$cseqn,$str);
						
						$str = "insert into {$this->db_erp}.trx_ivty_reconadj_upld_files (
						ira_hd_ctrlno,ira_filename,muser
						) values('$cseqn','$__upld_filename','$cuser')";
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					}
				}
			} //end foreach 
		} //end if 
		
		if (empty($cseqn_mtknattr)):
			$mtknattr = hash('sha384', $hd_rid . $mpw_tkn); 
			echo "<div class=\"alert alert-success mb-0\"><strong>SAVE</strong><br>Transaction successfully Saved [{$cseqn}]!!!</div><script type=\"text/javascript\"> function __metrxnum_refresh_data() { try { jQuery('#me_RecAdjCtrlNo').val('{$cseqn}'); jQuery('#me_RecAdjCtrlNo').attr('data-mtknattr','{$mtknattr}'); 
			jQuery('#mebtn_save_trxent').prop('disabled',true);} catch(err) { var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } }; __metrxnum_refresh_data(); </script>";
		else:
			echo "<div class=\"alert alert-success mb-0\"><strong>System Message</strong><br>Transaction update successfully SAVED [{$cseqn}]!!!</div><script type=\"text/javascript\"> function __metrxnum_refresh_data() { try {
			jQuery('#mebtn_save_trxent').prop('disabled',true);} catch(err) { var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } }; __metrxnum_refresh_data(); 
			</script>";
		endif;
	} //end save_entry
	
	public function me_delrec() { 
		$cuser   = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'02','0004','00040203')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted - DEL_IVTY_RECONADJ_REC.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		} 
		$data_mtknid =  $this->request->getVar('data_mtknid');  //recid detail record
		$data_rectype =  $this->request->getVar('data_rectype');
		$medeltr =  $this->request->getVar('medeltr');
		$mtkn_etr =  $this->request->getVar('mtkn_etr');  //recid header record
		
		if($data_rectype == 'dt') { 
			if($data_mtknid == 'undefined' || trim($data_mtknid) == '') { 
				$mearray =  $this->request->getVar('mearray');
				if(!empty($mtkn_etr) && !empty($mearray)):
					$medata = explode("x|x",$mearray[0]);
					$itemc = $this->mylibzdb->me_escapeString($medata[0]);
					$itemc_ded = (!empty($medata[1]) ? ($medata[1] + 0) : 0);
					$itemc_add = (!empty($medata[2]) ? ($medata[2] + 0) : 0);
					$itemc_remk = $medata[3];
					$itemc_dtattr = $medata[4];
				
					$str = "select recid from {$this->db_erp}.`trx_ivty_reconadj_dt` where sha2(concat(`ira_hd_id`,'{$mpw_tkn}'),384)  = '$mtkn_etr' and `ira_artcode` = '$itemc' 
					and `ira_ded_qty` = {$itemc_ded} and `ira_add_qty` = {$itemc_add} and `ira_item_remk` = '$itemc_remk' ";
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'IVTY_RECSONADJ_DT_DEL_BEFORE','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					
					$qrec = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					if($qrec->getNumRows() > 0):
						$rdt = $qrec->getRow();
						$reciddt = $rdt->recid;
						$str = "DELETE FROM {$this->db_erp}.`trx_ivty_reconadj_dt` WHERE `recid` = '{$reciddt}'";
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
						$this->mylibzdb->user_logs_activity_module($this->db_erp,'IVTY_RECSONADJ_DT_DEL','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					endif;
					$qrec->freeResult();
				endif;
				$chtml = "<div class=\"alert alert-success mb-0\"><strong>Info.<br/></strong>Data successfully deleted!</div>
				<script type=\"text/javascript\"> 
					ivty_reconadj_delrow('{$medeltr}');
				</script>
				";
				echo $chtml;
				die();
			} else {
				$str = "SELECT * FROM {$this->db_erp}.`trx_ivty_reconadj_dt` WHERE (sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$data_mtknid}')";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'User: ' . $cuser);
				if($q->getNumRows() > 0) { 
					$rrow = $q->getRow();
					$_rrid = $rrow->recid;
					$ira_artcode = $rrow->ira_artcode;
					$ira_ded_qty = $rrow->ira_ded_qty;
					$ira_add_qty = $rrow->ira_add_qty;
					$ira_item_remk = $rrow->ira_item_remk;
					$ira_branch_id = $rrow->ira_branch_id;
					$str_rec_val = "
					Item Code: {$ira_artcode}
					Item Ded Qty: {$ira_ded_qty}
					Item Add Qty: {$ira_add_qty}
					Branch Rec ID: {$ira_branch_id}
					Item Remarks: {$ira_item_remk}
					";
					$str = "DELETE FROM {$this->db_erp}.`trx_ivty_reconadj_dt` WHERE `recid` = '{$_rrid}'";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'IVTY_RECSONADJ_DT_DEL','',$cuser,$str . chr(13) . chr(10) . $str_rec_val,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
					echo "<div class=\"alert alert-success mb-0\"><strong>Info.<br/></strong>Selected DATA successfully deleted!</div>
					<script type=\"text/javascript\"> 
						ivty_reconadj_delrow('{$medeltr}');
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
	} // end me_delrec
	
	public function me_postrec() { 
		$cuser   = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'02','0004','00040206')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted - DEL_IVTY_RECONADJ_POSTREC.<br/></strong><strong>Access DENIED!!!</strong></div>
			<script>
				jQuery('#meyn3_ivty_recon_adj_srecs_yes').prop('disabled',true);
				jQuery('#memsgrecme').show();
			</script>
			";
			die();
		} 
		
		
		$mtknattr = $this->request->getVar('mtknattr');
		$chtml = '';
		$str = "select `recid`, `ira_hd_ctrlno`,`ira_posted` from {$this->db_erp}.`trx_ivty_reconadj_hd` where sha2(concat(`recid`,'{$mpw_tkn}'),384) =  '{$mtknattr}'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if ($q->getNumRows() > 0): 
			$rw = $q->getRowArray();
			$hd_rid = $rw['recid'];
			$cseqn = $rw['ira_hd_ctrlno'];
			$ira_posted = $rw['ira_posted'];
			if($ira_posted == 'Y'):
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Transaction Records ALREADY POSTED!!!</div>
				<script>
					jQuery('#meyn3_ivty_recon_adj_srecs_yes').prop('disabled',true);
					jQuery('#memsgrecme').show();
				</script>
				";
				die();
			endif;
			
			$arrfield = array();
			$arrfield[] = "ira_posted" . "xOx'Y'";
			$arrfield[] = "muser" . "xOx'" . $cuser . "'";
			$str = " `ira_hd_ctrlno` = '{$cseqn}' ";
			$this->mylibzdb->logs_modi_audit($arrfield,$this->db_erp,'`trx_ivty_reconadj_hd`','IVTY_RECONADJ_POSTING',$cseqn,$str);
			
			$str = "update {$this->db_erp}.`trx_ivty_reconadj_hd` set `ira_posted` = 'Y',
			`ira_dateposted` = now() 
			where `recid` = {$hd_rid}";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'IVTY_RECSONADJ_POSTING','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
			$chtml = "
			<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>SUCCESS</strong> Posting DONE!!!</div>
			";
		else:
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Transaction RECORDS CREDENTIALS!!!</div>";
			die();
		endif;	
		$chtml .= "
		<script>
			jQuery('#meyn3_ivty_recon_adj_srecs_yes').prop('disabled',true);
			jQuery('#memsgrecme').show();
		</script>
		";
		echo $chtml;
	} //end me_postrec
	
	public function vw_recs($npages = 1,$npagelimit = 10,$msearchrec='') { 
		$cuser   = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$str_optn = '';
		$a_brnch = $this->myusermod->ua_brnch($this->db_erp,$cuser);
		$merecinq = $this->request->getVar('merecinq'); 
		$str_optn2 = '';
		if($merecinq == 'POSTED') { 
			$str_optn2 = " and aa.`ira_posted` = 'Y' " ;
		} elseif ($merecinq == 'NOTPOSTED') { 
			$str_optn2 = " and (trim(aa.`ira_posted`) = '' or  aa.`ira_posted` is null) " ;
		}
			
		$str_brnch = " (aa.`ira_branch_id` = '__x__' AND aa.`ira_branch_id` != 0) ";
		if(count($a_brnch) > 0) { 
			$str_brnch = "";
			for($aa = 0; $aa < count($a_brnch); $aa++) { 
				$str_brnch .= " aa.`ira_branch_id` = '{$a_brnch[$aa]}' or "; 
			}  //end for
			$str_brnch = " (" . substr($str_brnch,0,(strlen($str_brnch)-3)) . ") ";
		} 
		if(!empty($msearchrec)) { 
			$msearchrec = $this->mylibzdb->me_escapeString($msearchrec);
			$str_optn = " ((aa.`ira_hd_ctrlno` = '$msearchrec') OR (cc.`BRNCH_NAME` LIKE '%$msearchrec%'))";
		} //end if 
		
		if(empty($str_optn)) { 
			$str_brnch = " where " . $str_brnch;
		} else {
			$str_brnch = " and " . $str_brnch;
			$str_optn = " where " . $str_optn;
		}
		
		$strqry = "SELECT aa.`recid`,aa.`ira_hd_ctrlno`,sum(bb.`ira_ded_qty`) __ded_qty,sum(bb.`ira_add_qty`) __add_qty,aa.`muser`,cc.`BRNCH_NAME`,
		date_format(aa.`mencd`,'%m/%d/%Y %h:%i:%s') __mencd,date_format(aa.`ira_dateposted`,'%m/%d/%Y %h:%i:%s') __mdateposted,
		if(aa.`ira_posted` = 'Y','YES','NO') __mpostrmk 
		 FROM {$this->db_erp}.`trx_ivty_reconadj_hd` aa 
				   JOIN {$this->db_erp}.`trx_ivty_reconadj_dt` bb ON aa.`recid` = bb.`ira_hd_id` 
				   JOIN {$this->db_erp}.`mst_companyBranch` cc ON cc.`recid` = aa.`ira_branch_id` 
				    {$str_optn} {$str_brnch} {$str_optn2} group by aa.`ira_hd_ctrlno`";

		$str = "SELECT count(*) __nrecs FROM ({$strqry}) oa ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = (($npagelimit * ($npages - 1)) > 0 ? ($npagelimit * ($npages - 1)) : 0);
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "SELECT * FROM ({$strqry}) oa  ORDER BY `ira_hd_ctrlno` desc limit {$nstart},{$npagelimit} ";
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
	} //end vw_recs
	
	
	
	public function search_artmaster() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$data_mtknattr = $this->request->getVar('data-mtknattr');
		$term = $this->request->getVar('term');
		$str_branch = '';
		$BRNCH_MAT_FLAG = '';
		$autoCompleteResult = array();
		
		$str_active_items = " (a.ART_ISDISABLE = 0) ";
		//ALLOW SEARCH DEACTIVATED ITEMS 
		if($this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'02','0004','00040207')) { 
			$str_active_items = " (a.ART_ISDISABLE = 0 or a.ART_ISDISABLE = 1) ";
		} 
				
		if(!empty($data_mtknattr)): 
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$data_mtknattr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_DTL_GEN','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				$br_id = $rw['recid'];
				$BRNCH_MAT_FLAG = $rw['BRNCH_MAT_FLAG'];
				$str_branch = " and kk.`brnchID` = '$br_id' ";
			}
			$q->freeResult();
			
		endif;
		
        if ($BRNCH_MAT_FLAG == 'G'): 
        	$str = "
        	select 
        	a.recid,
        	a.ART_DESC,
        	trim(a.ART_CODE) __mdata,
        	a.ART_SKU,
        	a.ART_SDU,
        	a.ART_IMG,
        	a.ART_NCBM,
        	a.ART_NCONVF,
        	a.ART_UOM,
        	a.ART_BARCODE1,
        	a.ART_HIERC3,
        	a.ART_HIERC4,
        	IFNULL(kk.art_uprice,a.ART_UPRICE) ART_UPRICE,
        	IFNULL(kk.art_cost,a.ART_UCOST) ART_UCOST,
        	sha2(concat(a.recid,'{$mpw_tkn}'),384) mtknattr 
        	from {$this->db_erp}.`mst_article` a 
        	JOIN `mst_article_per_branch` kk 
        	ON (a.`recid` = kk.`artID`) 
        	where {$str_active_items} {$str_branch} AND (a.ART_CODE like '%$term%' or a.ART_DESC like '%$term%' or a.ART_BARCODE1 like '%$term%') order BY a.ART_DESC limit 50 
        	";
        else:
        	$str = "
        	select recid,ART_DESC,trim(ART_CODE) __mdata,
        	ART_SKU,ART_SDU,ART_IMG,ART_NCBM,ART_NCONVF,ART_UPRICE,ART_UCOST,
        	ART_BARCODE1,ART_HIERC3,ART_HIERC4,ART_UOM,
        	sha2(concat(recid,'{$mpw_tkn}'),384) mtknattr 
        	from {$this->db_erp}.`mst_article` a where {$str_active_items} AND (ART_CODE like '%{$term}%' or ART_DESC like '%{$term}%' or ART_BARCODE1 like '%{$term}%') order BY ART_DESC limit 50 
        	";
        endif;        
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
				array_push($autoCompleteResult,array(
					"mtkn_rid" => $mtkn_rid,
					"value" => $row['__mdata'],
					"ART_DESC" => $row['ART_DESC'],  
					"ART_SKU" => $row['ART_SKU'], 
					"ART_SDU" => $row['ART_SDU'], 
					"ART_IMG" => $row['ART_IMG'],
					"ART_UOM"   => $row['ART_UOM'],
					"ART_NCONVF" => $row['ART_NCONVF'],
					"ART_UPRICE" => $row['ART_UPRICE'],
					"ART_UCOST" => $row['ART_UCOST'],  
					"ART_CODE" => $row['__mdata'],
					"ART_NCBM" => $row['ART_NCBM'],
					"ART_MATRID" => $row['recid'],
					"ART_BARCODE1" => $row['ART_BARCODE1'],
					"ART_HIERC3"     => $row['ART_HIERC3'],
					"ART_HIERC4" => $row['ART_HIERC4'],
				));
			endforeach;
		}
		$q->freeResult();
		
		echo json_encode($autoCompleteResult);
        
	} //end search_artmaster
	
	public function get_ctr_seqn($dbname,$mfield,$nlen=10) { 
		$metbl = "{$dbname}.`myctr88`";
		$str = "
		CREATE TABLE if not exists {$metbl} (
		  `CTR_YEAR` varchar(4) DEFAULT '0000',
		  `CTR_MONTH` varchar(2) DEFAULT '00',
		  `CTR_DAY` varchar(2) DEFAULT '00',
		  `CTRL_NO01` varchar(15) DEFAULT '00000000',
		  `CTRL_NO02` varchar(15) DEFAULT '00000000',
		  `CTRL_NO03` varchar(15) DEFAULT '00000000',
		  `CTRL_NO04` varchar(15) DEFAULT '00000000',
		  `CTRL_NO05` varchar(15) DEFAULT '00000000',
		  `CTRL_NO06` varchar(15) DEFAULT '00000000',
		  `CTRL_NO07` varchar(15) DEFAULT '00000000',
		  `CTRL_NO08` varchar(15) DEFAULT '00000000',
		  `CTRL_NO09` varchar(15) DEFAULT '00000000',
		  `CTRL_NO10` varchar(15) DEFAULT '00000000',
		  `CTRL_NO11` varchar(15) DEFAULT '00000000',
		  `SS_CTR` varchar(15) DEFAULT '000000',
		  UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$xfield = (empty($mfld) ? 'CTRL_NO01' : $mfld);
		
		$str = "select date(now()) XSYSDATE,NOW() __medate,SUBSTR(NOW(),12,2) __mehr,SUBSTR(NOW(),15,2) __memn,SUBSTR(NOW(),18,2) __mesc";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rdate = $q->getRowArray();
		$xsysdate = $rdate['XSYSDATE'];
		$xsysdate_exp = explode('-', $xsysdate);
		$xsysyear =  $xsysdate_exp[0];
		$xsysmonth = $xsysdate_exp[1];
		$xsysday = $xsysdate_exp[2];
		$xsysdayhr = $rdate['__mehr'];
		$xsysdaymn = $rdate['__memn'];
		$xsysdaysc = $rdate['__mesc'];
		
		$str = "select {$xfield} from {$metbl} WHERE CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday'  limit 1";
		$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qctr->getNumRows() == 0) {
			$xnumb = '0000000001';
			$str = "insert into {$metbl} (CTR_YEAR,CTR_MONTH,CTR_DAY,{$xfield}) values('$xsysyear','$xsysmonth','$xsysday','$xnumb')";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$qctr->freeResult();
		} else {
			$qctr->freeResult();
			$str = "select {$xfield} MYFIELD from {$metbl} WHERE CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday' limit 1";
			$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rctr = $qctr->getRowArray();
			if(trim($rctr['MYFIELD'],' ') == '') { 
				$xnumb = '0000000001';
			} else {
				$xnumb = $rctr['MYFIELD'];
				$str = "select ('{$xnumb}' + 1) XNUMB";
				$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$rctr = $qctr->getRowArray();
				$xnumb = trim($rctr['XNUMB'],' ');
				$xnumb = str_pad($xnumb + 0,$nlen,"0",STR_PAD_LEFT);
				$str = "update {$metbl} set {$xfield} = '{$xnumb}' where CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday'";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
		}
		return  $xsysyear . $xsysmonth . $xsysday . $xsysdayhr . $xsysdaymn . $xsysdaysc . $xnumb; 
	} //end get_ctr_seqn
	
} //end main 
