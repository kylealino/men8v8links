<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;
use App\Models\MyDatumModel;
use App\Models\MyUserModel;
class MyRptHOInventoryModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->request = \Config\Services::request();
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->db_erp_br = $this->mydbname->medb(1);
		$this->db_temp = $this->mydbname->medb(2);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
		$this->mydatum =  new MyDatumModel();
		$this->myusermod =  new MyUserModel();
		$this->cusergrp = $this->myusermod->mysys_usergrp();
		$this->cuser = $this->myusermod->mysys_user();
		$this->mpw_tkn = $this->myusermod->mpw_tkn();
	}	
	
	public function detailed_gen($npages = 1,$npagelimit = 30,$msearchrec='',$lArtmU=0,$metkntmp='') { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$bid_mtknattr = $this->request->getVar('bid_mtknattr');
		$fld_branch = $this->mylibzdb->me_escapeString($this->request->getVar('fld_branch'));
		
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'02','0004','00070701')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		}  //end if
		
		$data = array();
		$str_optn = "";
		if (!empty($msearchrec)): 
			$msearchrec = $this->mylibzdb->me_escapeString($msearchrec);
			$str_optn = " where (`ITEMC` = '$msearchrec'  or `ITEM_BARCODE` = '$msearchrec' or  `ITEM_DESC` like '%{$msearchrec}%') ";
		endif;
		
		if(!empty($fld_branch) && !empty($bid_mtknattr)) { 
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_branch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$bid_mtknattr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_DTL_GEN','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$br_ocode2 = $rw['B_OCODE2'];
			$tblivty = "{$this->db_erp_br}.`trx_E{$br_ocode2}_myivty_lb_dtl`";
			if(!empty($metkntmp)):
				$tblivty = "{$this->db_temp}.`meivtytmp_{$metkntmp}`";
			endif;
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
			//END BRANCH
		} else { 
			echo "Branch is INVALID!!!";
			die();
		} // end if
		
		//this should be process only once by trigerring generate or process button the form entry
		if($lArtmU) { 
			if($lperbr) { 
				$tbltemp = $this->db_temp . ".`artm_gro_" . $this->mylibz->random_string(15) . "`";
				$str = "drop table if exists {$tbltemp}";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$str = "CREATE TABLE IF NOT EXISTS {$tbltemp} ( 
				  `recid` int(25) NOT NULL AUTO_INCREMENT,
				  `ITEMC` varchar(35) NOT NULL,
				  `ITEM_BARCODE` varchar(18) DEFAULT '',
				  `ITEM_DESC` varchar(150) DEFAULT '',
				  `ITEM_COST` double(15,4) DEFAULT 0.0000,
				  `ITEM_PRICE` double(15,4) DEFAULT 0.0000,
				  PRIMARY KEY (`recid`),
				  KEY `idx01` (`ITEMC`) 
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
				
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$str = "insert into {$tbltemp} (
				`ITEMC`,`ITEM_BARCODE`,`ITEM_DESC`,`ITEM_COST`,`ITEM_PRICE`
				) 
				select itm.ART_CODE,itm.ART_BARCODE1,itm.ART_DESC,kk.art_cost,kk.art_uprice 
				from {$this->db_erp}.mst_article itm join {$this->db_erp}.`mst_article_per_branch` kk ON (itm.`recid` = kk.`artID`) 
				where itm.`ART_HIERC1` = '0600' and kk.`brnchID` = {$br_id}  
				";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				$str = "update {$tblivty} aa join {$tbltemp} bb on(aa.`ITEMC` = bb.`ITEMC`) 
				SET aa.ITEM_DESC = bb.ITEM_DESC,
				aa.ITEM_BARCODE = bb.ITEM_BARCODE,
				aa.MARTM_COST = bb.ITEM_COST,
				aa.MARTM_PRICE = bb.ITEM_PRICE ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				if ($cuser == '181-1'):  //for debugging purposes 
					//echo "{$tbltemp}";
					$str = "drop table if exists {$tbltemp}";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				else: 
					$str = "drop table if exists {$tbltemp}";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				endif;
				
			} else { 
				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				SET aa.ITEM_DESC = itm.ART_DESC,
				aa.ITEM_BARCODE = itm.ART_BARCODE1,
				aa.MARTM_COST = itm.ART_UCOST,
				aa.MARTM_PRICE = itm.ART_UPRICE";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
		} //end if update the latest pricing and costing
		
		$strqryxx = "
		select `ITEMC`,`ITEM_DESC`,`MARTM_COST`,`ITEM_BARCODE`,`MARTM_PRICE`,
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `BEG_QTY`, -- Beginning QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `GEN_IVTYC`, -- General Inventory thru Physical Count QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `GEN_IVTYC_DIFF`, -- General Inventory Discrepancy QTY 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `CYC-ADJ_QTY`, -- Adjusted Cycle Counting 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `RCV_QTY`, -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `CLM_QTY`,  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `RCV-S_QTY`, -- received from Store Use 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `RCV-M_QTY`, -- received from Membership 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `RCV-C_QTY`,  -- received from Change Price 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `RCV-R_QTY`, -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `SALES_QTY`, -- Sales  
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `B1T1_QTY`, -- PO Buy 1 Take 1  
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `DSP_QTY`, -- PO Dispose 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `BRG_QTY`, -- PO Bargain  
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `GVA_QTY`, -- PO Give Aways  
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `TO_QTY`, -- PO Transfer Out 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `TOB_QTY`, -- PO Transfer Out to Other Branch 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `RTML_QTY`, -- PO Return to Mapulang Lupa WSHE 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `POSU_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `POOTH_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'GEN-IVTYC',1,0)), -- items have general inventory count 
		(sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) `END_BAL_QTY` 
		 from " . $tblivty . " {$str_optn} group by `ITEMC` 
		";
		
		$strqry = "
		select `ITEMC`,`ITEM_DESC`,`MARTM_COST`,`ITEM_BARCODE`,`MARTM_PRICE`,
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `BEG_QTY`, -- Beginning QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `GEN_IVTYC`, -- General Inventory thru Physical Count QTY 
		(case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		else 0 end ) `GEN_IVTYC_DIFF`, -- General Inventory Discrepancy QTY 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `CYC-ADJ_QTY`, -- Adjusted Cycle Counting 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `RCV_QTY`, -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `CLM_QTY`,  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `RCV-S_QTY`, -- received from Store Use 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `RCV-M_QTY`, -- received from Membership 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `RCV-C_QTY`,  -- received from Change Price 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `RCV-R_QTY`, -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `SALES_QTY`, -- Sales  
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `B1T1_QTY`, -- PO Buy 1 Take 1  
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `DSP_QTY`, -- PO Dispose 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `BRG_QTY`, -- PO Bargain  
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `GVA_QTY`, -- PO Give Aways  
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `TO_QTY`, -- PO Transfer Out 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `TOB_QTY`, -- PO Transfer Out to Other Branch 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `RTML_QTY`, -- PO Return to Mapulang Lupa WSHE 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `POSU_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `POOTH_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'GEN-IVTYC',1,0)), -- items have general inventory count 
		((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) `END_BAL_QTY` 
		 from " . $tblivty . " {$str_optn} group by `ITEMC` 
		";
		
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = (($npagelimit * ($npages - 1)) > 0 ? ($npagelimit * ($npages - 1)) : 0);
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		
		$str = "
		SELECT oa.*,`ITEM_DESC` `ART_DESC`,MARTM_COST `ITEM_COST`,MARTM_PRICE `ITEM_PRICE`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * `END_BAL_QTY`) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * oa. `END_BAL_QTY`) end ) ITEM_AMT_PRICE 
		from ({$strqry}) oa limit {$nstart},{$npagelimit} ";
		
		$strg = "
		SELECT oa.*,`ITEM_DESC` `ART_DESC`,MARTM_COST `ITEM_COST`,MARTM_PRICE `ITEM_PRICE`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * `END_BAL_QTY`) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * oa. `END_BAL_QTY`) end ) ITEM_AMT_PRICE 
		from ({$strqry}) oa 	
		";
		
		$strnobal = "
		SELECT count(*) nrecs 
		from ({$strqry}) oa where `END_BAL_QTY` < 0";
		$qnb = $this->mylibzdb->myoa_sql_exec($strnobal,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rwnb = $qnb->getRowArray();
		$G_NOBAL_ITEMS = (empty($rwnb['nrecs']) ? 0 : $rwnb['nrecs']);
		$qnb->freeResult();
		
		
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->getNumRows() > 0) { 
			$data['rlist'] = $qry->getResultArray();
			$data['fld_branch'] = $fld_branch;
			$data['mtknbrid'] = $bid_mtknattr;
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['fld_branch'] = $fld_branch;
			$data['mtknbrid'] = $bid_mtknattr;
		}
		
		$qry->freeResult();	
		$str = "select sum(`BEG_QTY`) `G_BEG_QTY`,
		sum(`GEN_IVTYC`) `G_GEN_IVTYC`,
		sum(`GEN_IVTYC_DIFF`) `G_GEN_IVTYC_DIFF`,
		sum(`CYC-ADJ_QTY`) `G_CYC_ADJ_QTY`,
		sum(`RCV_QTY`) `G_RCV_QTY`,
		sum(`CLM_QTY`) `G_CLM_QTY`,
		sum(`RCV-S_QTY`) `G_RCV_S_QTY`,
		sum(`RCV-M_QTY`) `G_RCV_M_QTY`,
		sum(`RCV-C_QTY`) `G_RCV_C_QTY`,
		sum(`RCV-R_QTY`) `G_RCV_R_QTY`,
		sum(`SALES_QTY`) `G_SALES_QTY`,
		sum(`B1T1_QTY`) `G_B1T1_QTY`,
		sum(`DSP_QTY`) `G_DSP_QTY`,
		sum(`BRG_QTY`) `G_BRG_QTY`,
		sum(`GVA_QTY`) `G_GVA_QTY`,
		sum(`TO_QTY`) `G_TO_QTY`,
		sum(`TOB_QTY`) `G_TOB_QTY`,
		sum(`RTML_QTY`) `G_RTML_QTY`,
		sum(`POSU_QTY`) `G_POSU_QTY`,
		sum(`POOTH_QTY`) `G_POOTH_QTY`,
		sum(`END_BAL_QTY`) `G_END_BAL_QTY`,
		sum(`ITEM_AMT_COST`) `G_ITEM_AMT_COST`,
		sum(`ITEM_AMT_PRICE`) `G_ITEM_AMT_PRICE` 
		 from 
		 ({$strg}) me";
		$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qq->getRowArray();
		$data['G_BEG_QTY'] = $rw['G_BEG_QTY']; 
		$data['G_GEN_IVTYC'] = $rw['G_GEN_IVTYC']; 
		$data['G_GEN_IVTYC_DIFF'] = $rw['G_GEN_IVTYC_DIFF']; 
		$data['G_CYC_ADJ_QTY'] = $rw['G_CYC_ADJ_QTY']; 
		$data['G_RCV_QTY'] = $rw['G_RCV_QTY']; 
		$data['G_CLM_QTY'] = $rw['G_CLM_QTY']; 
		$data['G_RCV_S_QTY'] = $rw['G_RCV_S_QTY']; 
		$data['G_RCV_M_QTY'] = $rw['G_RCV_M_QTY']; 
		$data['G_RCV_C_QTY'] = $rw['G_RCV_C_QTY']; 
		$data['G_RCV_R_QTY'] = $rw['G_RCV_R_QTY']; 
		$data['G_SALES_QTY'] = $rw['G_SALES_QTY']; 
		$data['G_B1T1_QTY'] = $rw['G_B1T1_QTY']; 
		$data['G_DSP_QTY'] = $rw['G_DSP_QTY']; 
		$data['G_BRG_QTY'] = $rw['G_BRG_QTY']; 
		$data['G_GVA_QTY'] = $rw['G_GVA_QTY']; 
		$data['G_TO_QTY'] = $rw['G_TO_QTY']; 
		$data['G_TOB_QTY'] = $rw['G_TOB_QTY']; 
		$data['G_RTML_QTY'] = $rw['G_RTML_QTY']; 
		$data['G_POSU_QTY'] = $rw['G_POSU_QTY']; 
		$data['G_POOTH_QTY'] = $rw['G_POOTH_QTY']; 
		$data['G_END_BAL_QTY'] = $rw['G_END_BAL_QTY']; 
		$data['G_ITEM_AMT_COST'] = $rw['G_ITEM_AMT_COST']; 
		$data['G_ITEM_AMT_PRICE'] = $rw['G_ITEM_AMT_PRICE']; 
		$data['G_NOBAL_ITEMS'] = $G_NOBAL_ITEMS; 
		$data['metkntmp'] = $metkntmp;
		$qq->freeResult();
		return $data;
	} //end detailed_gen
	
	public function detailed_download() {
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		$mdl_me_branch = $this->request->getVar('mdl_me_branch');
		$mdl_me_branch_mtkn = $this->request->getVar('mdl_me_branch_mtkn');
		$mdl_metkntmp = $this->request->getVar('mdl_metkntmp');
		if(!empty($mdl_me_branch) && !empty($mdl_me_branch_mtkn)) {
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$mdl_me_branch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mdl_me_branch_mtkn'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_DTL_DWNLD','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$br_ocode2 = $rw['B_OCODE2'];
			$tblivty = "{$this->db_erp_br}.`trx_E{$br_ocode2}_myivty_lb_dtl`";
			if(!empty($mdl_metkntmp)):
				$tblivty = "{$this->db_temp}.`meivtytmp_{$mdl_metkntmp}`";
			endif;			
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
		} //end if
		
		$dloadpath = ROOTPATH . 'public/downloads/me/';
		$mfile = $dloadpath . 'ivty_dtl_dload_' . $this->mylibz->random_string(15) . '.txt';
		if (file_exists($mfile)) { 
			unlink($mfile);
		}
		
		$str_END_BAL_QTY = "
		((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		)
		";
		
		$str = "
		SELECT * INTO OUTFILE '{$mfile}'
		  FIELDS TERMINATED BY '\t' 
		  LINES TERMINATED BY '\n'
		FROM (
		select 'Item Code','Item Barcode','Item Description','Beginning Balance','General Inventory (Physical Count)','General Inventory Discrepancy QTY','Adjusted Cycle Counting','Receiving (Deliveries)',
		'Claims','Receiving (Store Use)','Receiving (Membership)','Receiving (Change Price)','Receiving (Rcv in frm PO)','Sales Out',
		'Pull Out (Buy1Take1)','Pull Out (Dispose)','Pull Out (For Bargain)','Pull Out (Giveaways)','Pull Out (Inventory Transfer Out)',
		'Pull Out (Pull Out to Other Branch)','Pull Out (Return to Mapulang Lupa)','Pull Out (Store-Use)','Pull Out (Others)','Ending Balance',
		'Cost Amount','SRP Amount' 
		union all 
		select `ITEMC`,`ITEM_BARCODE`,`ITEM_DESC`,
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `BEG_QTY`, -- Beginning QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `GEN_IVTYC`, -- General Inventory thru Physical Count QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `GEN_IVTYC_DIFF`, -- General Inventory Discrepancy QTY 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `CYC-ADJ_QTY`, -- Adjusted Cycle Counting 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `RCV_QTY`, -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `CLM_QTY`,  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `RCV-S_QTY`, -- received from Store Use 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `RCV-M_QTY`, -- received from Membership 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `RCV-C_QTY`,  -- received from Change Price 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `RCV-R_QTY`, -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `SALES_QTY`, -- Sales  
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `B1T1_QTY`, -- PO Buy 1 Take 1  
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `DSP_QTY`, -- PO Dispose 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `BRG_QTY`, -- PO Bargain  
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `GVA_QTY`, -- PO Give Aways  
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `TO_QTY`, -- PO Transfer Out 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `TOB_QTY`, -- PO Transfer Out to Other Branch 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `RTML_QTY`, -- PO Return to Mapulang Lupa WSHE 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `POSU_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `POOTH_QTY`, -- PO Store Use 
		{$str_END_BAL_QTY} `END_BAL_QTY`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * {$str_END_BAL_QTY}) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * {$str_END_BAL_QTY}) end ) ITEM_AMT_PRICE 
		 from " . $tblivty . " group by `ITEMC` 
		) oa 
		";
		$str = "
		SELECT * INTO OUTFILE '{$mfile}'
		  FIELDS TERMINATED BY '\t' 
		  LINES TERMINATED BY '\n'
		FROM (
		select 'Item Code','Item Barcode','Item Description','Beginning Balance','General Inventory (Physical Count)','General Inventory Discrepancy QTY','Adjusted Cycle Counting','Receiving (Deliveries)',
		'Claims','Receiving (Store Use)','Receiving (Membership)','Receiving (Change Price)','Receiving (Rcv in frm PO)','Sales Out',
		'Pull Out (Buy1Take1)','Pull Out (Dispose)','Pull Out (For Bargain)','Pull Out (Giveaways)','Pull Out (Inventory Transfer Out)',
		'Pull Out (Pull Out to Other Branch)','Pull Out (Return to Mapulang Lupa)','Pull Out (Store-Use)','Pull Out (Others)','Ending Balance',
		'Cost Amount','SRP Amount' 
		union all 
		select `ITEMC`,`ITEM_BARCODE`,`ITEM_DESC`,
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `BEG_QTY`, -- Beginning QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `GEN_IVTYC`, -- General Inventory thru Physical Count QTY 
		(case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		else 0 end ) `GEN_IVTYC_DIFF`, -- General Inventory Discrepancy QTY 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `CYC-ADJ_QTY`, -- Adjusted Cycle Counting 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `RCV_QTY`, -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'RCV',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `CLM_QTY`,  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `RCV-S_QTY`, -- received from Store Use 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `RCV-M_QTY`, -- received from Membership 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `RCV-C_QTY`,  -- received from Change Price 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `RCV-R_QTY`, -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `SALES_QTY`, -- Sales  
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `B1T1_QTY`, -- PO Buy 1 Take 1  
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `DSP_QTY`, -- PO Dispose 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `BRG_QTY`, -- PO Bargain  
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `GVA_QTY`, -- PO Give Aways  
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `TO_QTY`, -- PO Transfer Out 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `TOB_QTY`, -- PO Transfer Out to Other Branch 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `RTML_QTY`, -- PO Return to Mapulang Lupa WSHE 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `POSU_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `POOTH_QTY`, -- PO Store Use 
		{$str_END_BAL_QTY} `END_BAL_QTY`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * {$str_END_BAL_QTY}) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * {$str_END_BAL_QTY}) end ) ITEM_AMT_PRICE 
		 from " . $tblivty . " group by `ITEMC` 
		) oa 
		";
		
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		//Clear system output buffer
		//flush();
		
		//Define header information
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		//header("Cache-Control: no-cache, must-revalidate");
		header("Expires: 0");
		header('Content-disposition: attachment; filename="ivty_dtl_dload.csv"');
		header('Content-Length: ' . filesize($mfile));
		header("Pragma: no-cache"); 
		//header('Pragma: public');

		//Clear system output buffer
		flush();
		
		//Read the size of the file
		readfile($mfile);
		
	} //end detailed_download
	
	public function ivtysummary() { 
		//$this->cuser;
		//$this->mpw_tkn;
		
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070703')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		}  //end if
		
		$strqry = "
		select `ITEMC`,`ITEM_DESC`,max(`MARTM_COST`) MARTM_COST,`ITEM_BARCODE`,max(`MARTM_PRICE`) MARTM_PRICE,
		((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) `END_BAL_QTY` 
		 from {$this->db_erp_br}.`trx_\",TRIM(Branch_code),\"_myivty_lb_dtl` group by `ITEMC` 
		";
		$strg = "
		SELECT oa.*,`ITEM_DESC` `ART_DESC`,MARTM_COST `ITEM_COST`,MARTM_PRICE `ITEM_PRICE`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * `END_BAL_QTY`) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * `END_BAL_QTY`) end ) ITEM_AMT_PRICE 
		from ({$strqry}) oa 	
		";		
		$str_meivty = "select 
		concat(ifnull(sum(`END_BAL_QTY`),0),'|',
		ifnull(sum(`ITEM_AMT_COST`),0),'|',
		ifnull(sum(`ITEM_AMT_PRICE`),0)) 
		 from 
		 ({$strg}) me";
		 		
		// $strx = "select concat(\"select '\",Branch_code,\"' ME_BRANCH,\",
		// \"(SELECT {$str_END_BAL} from {$this->db_erp_br}.`trx_\",TRIM(Branch_code),\"_myivty_lb_dtl`) END_BAL_QTY union all \") meqry FROM {$this->db_erp}.mst_branch_ivty_tag ORDER BY Branch_code";
		
		// $strx = "select concat(\"select '\",Branch_code,\"' ME_BRANCH,
		// '\",bb.BRNCH_NAME,\"' ME_BRANCH_NAME,\",
		// \"(SELECT {$str_ivty} from {$this->db_erp_br}.`trx_\",TRIM(Branch_code),\"_myivty_lb_dtl`) END_BAL_QTY union all \") meqry FROM {$this->db_erp}.mst_branch_ivty_tag aa 
		// join {$this->db_erp}.mst_companyBranch bb on(aa.Branch_code = concat('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME";

		$str = "select concat(\"select '\",Branch_code,\"' ME_BRANCH,
		'\",bb.BRNCH_NAME,\"' ME_BRANCH_NAME,\",
		\"({$str_meivty}) END_BAL_DATA union all \") meqry FROM {$this->db_erp}.mst_branch_ivty_tag aa 
		join {$this->db_erp}.mst_companyBranch bb on(aa.Branch_code = concat('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME";

		
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "";
		foreach($q->getResultArray() as $rw): 
			$str .= $rw['meqry'];
		endforeach;
		$q->freeResult();
		//die();
		//$rw = $q->getRowArray();
		$memodule = "__merptivtyrecssumma__";
		$str = substr($str,0,strlen($str) - 10);
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		 $chtml = "
		<div class=\"row m-0 p-1 mt-2 mb-2\">
			<div class=\"col-md-12 col-md-12 col-md-12\">
				<div class=\"table-responsive\">
					<table class=\"metblentry-font table-bordered\" id=\"__tbl_{$memodule}\">
						<thead>
							<tr>
								<th></th>
								<th>Branch Code</th>
								<th>Branch Name</th>
								<th>Beg Bal QTY</th>
								<th>Cost Amount</th>
								<th>SRP Amount</th>
							</tr>
		 ";
		 $nn = 1;
		foreach($q->getResultArray() as $rw): 
			$xdata = explode("|",$rw['END_BAL_DATA']);
			list($nBalQty, $nBalCost, $nBalPrice) = $xdata;
			$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
			$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
			
			$chtml .= "
			<tr style=\"background-color: {$bgcolor} !important;\" {$on_mouse}>
				<td>{$nn}</td>
				<td>{$rw['ME_BRANCH']}</td>
				<td>{$rw['ME_BRANCH_NAME']}</td>
				<td class=\"text-end\">" . number_format($nBalQty,4,'.',',') . "</td>
				<td class=\"text-end\">" . number_format($nBalCost,4,'.',',') . "</td>
				<td class=\"text-end\">" . number_format($nBalPrice,4,'.',',') . "</td>
			</tr>
			";
			$nn++;
			$str = "update {$this->db_erp_br}.`trx_branch_bal_summary` set 
			`BAL_QTY` = {$nBalQty},
			`BAL_COST` = {$nBalCost},
			`BAL_SRP_AMT` = {$nBalPrice} 	
			where B_CODE = '{$rw['ME_BRANCH']}'";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		endforeach;
		$q->freeResult();
		$chtml .= "
		</table>
		</div>
		</div>
		</div>
		<script>
		__mysys_apps.meTableSetCellPadding('__tbl_{$memodule}',3,'1px solid #7F7F7F');
		</script>
		";
		echo $chtml;
		
	}  //end ivtysummary
	
	public function live_inventory_balance() { 
		$memodmtkn = 'baa138d483ce20366f3c1270a6abf9f86d58619a8665dd0fcd5b2145feb7c25846056ed4540238d0e60c6fa99a15b80f8e7712af0c846420b59657cceaeca9dd';
		$memtkn = $this->request->getVar('memtkn');
		if ($memtkn == $memodmtkn) { 
			$memodule = "__merptivtybalonline__";
			$str = "select    `B_CODE`,
			bb.BRNCH_NAME,
			ifnull(`BAL_QTY`,0) BAL_QTY,
			ifnull(`BAL_COST`,0) BAL_COST,
			ifnull(`BAL_SRP_AMT`,0) BAL_SRP_AMT,
			`MPROCDATE` from {$this->db_erp_br}.`trx_branch_bal_summary` aa join 
			{$this->db_erp}.mst_companyBranch bb on(aa.B_CODE = concat('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$chtml = "
			<div class=\"row m-0 p-1 mt-2 mb-2\">
				<div class=\"col-md-12 col-md-12 col-md-12\">
					<div class=\"table-responsive\">
						<table class=\"metblentry-font table-bordered\" id=\"__tbl_{$memodule}\">
							<thead>
								<tr>
									<th></th>
									<th>Branch Code</th>
									<th>Branch Name</th>
									<th nowrap>Balance QTY</th>
									<th>Cost Amount</th>
									<th>SRP Amount</th>
									<th>as of Dated</th>
								</tr>
			 ";
			 $nn = 1;
			 $ntotQty = 0; $ntotCost = 0; $ntotSrp = 0;
			foreach($q->getResultArray() as $rw): 
				$nBalQty = $rw['BAL_QTY']; $nBalCost = $rw['BAL_COST']; $nBalPrice = $rw['BAL_SRP_AMT'];
				$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
				$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
				
				$chtml .= "
				<tr style=\"background-color: {$bgcolor} !important;\" {$on_mouse}>
					<td>{$nn}</td>
					<td nowrap>{$rw['B_CODE']}</td>
					<td nowrap>{$rw['BRNCH_NAME']}</td>
					<td class=\"fw-bolder text-danger text-end\" nowrap>" . number_format($nBalQty,4,'.',',') . "</td>
					<td class=\"fw-bolder text-danger text-end\" nowrap>" . number_format($nBalCost,4,'.',',') . "</td>
					<td class=\"fw-bolder text-danger text-end\" nowrap>" . number_format($nBalPrice,4,'.',',') . "</td>
					<td nowrap>{$rw['MPROCDATE']}</td>
				</tr>
				";
				$nn++;
				$ntotQty += $nBalQty; 
				$ntotCost += $nBalCost;
				$ntotSrp += $nBalPrice;
				
			endforeach;
			$q->freeResult();
			$chtml .= "
				<tr>
					<td colspan=\"3\"></td>
					<td class=\"fw-bolder text-end\">" . number_format($ntotQty,4,'.',',') . "</td>
					<td class=\"fw-bolder text-end\">" . number_format($ntotCost,4,'.',',') . "</td>
					<td class=\"fw-bolder text-end\">" . number_format($ntotSrp,4,'.',',') . "</td>
					<td></td>
				</tr>
			</table>
			</div>
			</div>
			</div>
			<script>
			__mysys_apps.meTableSetCellPadding('__tbl_{$memodule}',3,'1px solid #7F7F7F');
			</script>
			";
			echo $chtml;
		} else { 
			echo "...INVALID_TOKEN...";
		}  //end if
		
	} //end live_inventory_balance
	
	public function itemized_ivty_abrach() { 
		$cuser            = $this->myusermod->mysys_user();
		$mpw_tkn          = $this->myusermod->mpw_tkn();
		$adata = array(); 		
		$meitem = $this->request->getVar('meitem');
		$meitemtkn = $this->request->getVar('meitemtkn');
		$str_optn = "";
		if(!empty($meitem) && !empty($meitem)) { 
			$str = "select aa.recid,ART_CODE,ART_DESC,ART_BARCODE1 
			from {$this->db_erp}.`mst_article` aa where `ART_CODE` = '$meitem' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$meitemtkn'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_POS_TALLY_TAXR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Product Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$ART_CODE = $rw['ART_CODE'];
			$ART_DESC = $rw['ART_DESC'];
			$ART_BCODE = $rw['ART_BARCODE1'];
			$str_optn = " `ITEMC` = '$ART_CODE' ";
		} else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong>Product Item is REQUIRED!!!</div>";
			die();
		} //end if
		
		$strqry_fields = "
		concat((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end),'x|x', -- Beginning Balance 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)),'x|x', -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)),'x|x',  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)),'x|x', -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)),'x|x', -- Sales  
		((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		)) -- ending balance  
		";
				
		$str = "
		SELECT
		CONCAT(\"select '\",Branch_code,\"' ME_BRANCH,\",
		\"'\",bb.BRNCH_NAME,\"' ME_BRANCH_NAME,\",
		\"'{$ART_CODE}' ART_CODE,\",
		\"'{$ART_DESC}' ART_DESC,\",
		\"'{$ART_BCODE}' ART_BCODE,\",
		\"(SELECT {$strqry_fields} FROM {$this->db_erp_br}.`trx_\",TRIM(Branch_code),\"_myivty_lb_dtl` where {$str_optn} ) ME_DATA union all \" 
		 ) meqry 
		 FROM {$this->db_erp}.mst_branch_ivty_tag aa JOIN {$this->db_erp}.mst_companyBranch bb ON(aa.Branch_code = CONCAT('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME
		";

		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "";
		foreach($q->getResultArray() as $rw): 
			$str .= $rw['meqry'];
		endforeach;
		$q->freeResult();
		//die();
		//$rw = $q->getRowArray();
		$str = substr($str,0,strlen($str) - 10);
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$adata['rlist'] = $q->getResultArray();
			$adata['rfieldnames'] = $q->getFieldNames();
		} else { 
			$adata['rlist'] = '';
			$adata['rfieldnames'] = '';
		} 
		$q->freeResult();		
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_BRITEMIZED_GEN','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);				
		return $adata;
	 } //itemized_ivty_abrach
	 
	 public function live_balance_branches_conso() { 
		$cuser            = $this->myusermod->mysys_user();
		$mpw_tkn          = $this->myusermod->mpw_tkn();
		
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070707')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		}  //end if

		
		$adata = array(); 		
		$str = "select aa.Branch_code,bb.BRNCH_NAME FROM {$this->db_erp}.mst_branch_ivty_tag aa JOIN {$this->db_erp}.mst_companyBranch bb ON(aa.Branch_code = CONCAT('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "";
		$str_END_BAL_QTY = "
			((case when if(`MTYPE` = 'GEN-IVTYC',1,0) > 0 then 
				if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))  
			else if(`MTYPE` = 'BEG-BAL',`MQTY`,0) 
			end) + 
			if(`MTYPE` = 'RCV',`MQTY`,0) + 
			if(`MTYPE` = 'CYC-ADJ',`MQTY`,0) + 
			if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0) + 
			if(`MTYPE` = 'RCV-S',`MQTY`,0) + 
			if(`MTYPE` = 'RCV-M',`MQTY`,0) + 
			if(`MTYPE` = 'RCV-C',`MQTY`,0) + 
			if(`MTYPE` = 'RCV-R',`MQTY`,0) + 
			if(`MTYPE` = 'SALES',`MQTY`,0) + 
			if(`MTYPE` = 'PO-B1T1',`MQTY`,0) + 
			if(`MTYPE` = 'PO-DSP',`MQTY`,0) + 
			if(`MTYPE` = 'PO-BRG',`MQTY`,0) + 
			if(`MTYPE` = 'PO-GVA',`MQTY`,0) + 
			if(`MTYPE` = 'PO-TO',`MQTY`,0) + 
			if(`MTYPE` = 'PO-TOB',`MQTY`,0) + 
			if(`MTYPE` = 'PO-RTML',`MQTY`,0) + 
			if(`MTYPE` = 'PO-SU',`MQTY`,0) + 
			if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)
			) ";
			
		foreach($q->getResultArray() as $rw): 
			$B_CODE = $rw['Branch_code'];
			$str .= "
			select '{$rw['BRNCH_NAME']}' `Branch`,
			(case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
				sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
			else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
			end) `Beginning Balance`,
			sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `General Inventory (Physical Count)`, -- General Inventory thru Physical Count QTY 
			(case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
				sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
			else 0 end ) `General Inventory Discrepancy QTY`, -- General Inventory Discrepancy QTY 
			sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `Adjusted Cycle Counting`, -- Adjusted Cycle Counting 
			sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `Receiving (Deliveries)`, -- from Receiving Deliveries 
			sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `Claims`,  -- claims adjustment 
			sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `Receiving (Store Use)`, -- received from Store Use 
			sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `Receiving (Membership)`, -- received from Membership 
			sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `Receiving (Change Price)`,  -- received from Change Price 
			sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `Receiving (Rcv in frm Pull Out)`, -- received from Pull Outs 
			sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `Sales Out`, -- Sales  
			sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `Pull Out (Buy1Take1)`, -- PO Buy 1 Take 1  
			sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `Pull Out (Dispose)`, -- PO Dispose 
			sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `Pull Out (For Bargain)`, -- PO Bargain  
			sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `Pull Out (Giveaways)`, -- PO Give Aways  
			sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `Pull Out (Inventory Transfer Out)`, -- PO Transfer Out 
			sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `Pull Out (Pull Out to Other Branch)`, -- PO Transfer Out to Other Branch 
			sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `Pull Out (Return to Mapulang Lupa)`, -- PO Return to Mapulang Lupa WSHE 
			sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `Pull Out (Store-Use)`, -- PO Store Use 
			sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `Pull Out (Others)`, -- PO Store Use 
			((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
				sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
			else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
			end) + 
			sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
			sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
			) `Ending Balance`,
			sum(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * $str_END_BAL_QTY) end ) `Cost Amount`,
			sum(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * $str_END_BAL_QTY) end ) `SRP Amount` 
			 from {$this->db_erp_br}.`trx_{$B_CODE}_myivty_lb_dtl` union all ";
		endforeach;
		$q->freeResult();
		$str = substr($str,0,strlen($str) - 10);
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$adata['rlist'] = $q->getResultArray();
			$adata['rfieldnames'] = $q->getFieldNames();
		} else { 
			$adata['rlist'] = '';
			$adata['rfieldnames'] = '';
		} 
		$q->freeResult();
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_BRCONSO_GEN','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);				
		return $adata;
	 } //end live_balance_arbaches_conso
	 
	
} //end main 
