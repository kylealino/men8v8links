<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;
use App\Models\MyDatumModel;
use App\Models\MyUserModel;
class MyRptInventoryModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->request = \Config\Services::request();
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->db_erp_br = $this->mydbname->medb(1);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
		$this->mydatum =  new MyDatumModel();
		$this->myusermod =  new MyUserModel();
		$this->cusergrp = $this->myusermod->mysys_usergrp();
	}	
	
	public function stockcard($npages = 1,$npagelimit = 30,$msearchrec='',$fld_stinqbr_dtefrom='',$fld_stinqbr_dteto='',$mtbl='') { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$fld_stinqbrbranch = $this->request->getVar('fld_stinqbrbranch');
		$fld_stinqbrbranch_id = $this->request->getVar('fld_stinqbrbranch_id');
		$fld_stinqbritemcode_s = $this->request->getVar('fld_stinqbritemcode_s');
		
		//FOR TSHIRT AND PANTS ONLY
		// $str_tap = '';
		// $result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='114'","myua_acct");
		// if($result == 1){
		// 	$str_tap = "AND (bb.`ART_HIERC2` = '0301' OR bb.`ART_HIERC2` ='0302' OR bb.`ART_HIERC1` = 'TSHIRT' OR bb.`ART_HIERC1` = 'PANTS')";
		// 	if(($msearchrec == '') || ($fld_stinqbritemcode_s == '')){
		// 		$str_tap = "WHERE (bb.`ART_HIERC2` = '0301' OR bb.`ART_HIERC2` ='0302' OR bb.`ART_HIERC1` = 'TSHIRT' OR bb.`ART_HIERC1` = 'PANTS')";
		// 	}
		// }
		$str_tap = '';
		$fld_tap = $this->request->getVar('fld_tap');
		$fld_tap_code = '';
		if(!empty($fld_tap)){
			if($fld_tap == 'TSHIRT'){
				$fld_tap_code = '0302';
			}
			elseif($fld_tap == 'PANTS'){
				$fld_tap_code = '0301';
			}
			else{ 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Invalid Department Name!!!.</div>";
				die();
			}
			$str_tap = "AND (bb.`ART_HIERC2` = '$fld_tap_code' OR bb.`ART_HIERC1` = '$fld_tap')";
			if(($msearchrec == '') || ($fld_stinqbritemcode_s == '')){
				$str_tap = "WHERE (bb.`ART_HIERC2` = '$fld_tap_code' OR bb.`ART_HIERC1` = '$fld_tap')";
			}
		}
		//VALIDATE IF SAME MONTH
		if($fld_stinqbr_dtefrom != '' || $fld_stinqbr_dteto != ''){
			$str = "SELECT MONTH('$fld_stinqbr_dtefrom') _day1,MONTH('$fld_stinqbr_dteto') _day2,YEAR('$fld_stinqbr_dtefrom') _yr1,YEAR('$fld_stinqbr_dteto') _yr2";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->getRowArray();
			$_day1 = $rw['_day1'];//
			$_day2 = $rw['_day2'];//
			$_yr1 = $rw['_yr1'];//
			$_yr2 = $rw['_yr2'];//
			if(($_day1 != $_day2) && ($_yr1 != $_yr2)){
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Date Range!!! ,Date From and Date To must be same month!!!.</div>";
				die();

			}
		}
		
		$fld_lbdate = date('Y-m-d'); //2020-09-19
		if($fld_stinqbr_dteto != '') { 
			$fld_lbdate = $fld_stinqbr_dteto;
		}
		
		$str_itemc = '';
		$str_itemc2 ='';
 
		$__flag="C";
		
		$str_optn = "";
		$str_optn1 = "";
		$str_optn1_b = "";
		$str_branch ='';
		$str_comp ='';
		
		$str_branch_m ='';
		$str_comp_m ='';
		$str_optn1_r ='';
		$str_optn1_p='';
		$str_optn1_pos ='';
		$str_branch_pos ='';
		$str_branch_cyc = '';
		$str_optn1_cyc = '';
		$str_optn1_lb ='';
		$str_branch_lb ='';
		$str_optn_drcv ='';
		$str_optn_dpout ='';
		$str_optn_dpos ='';
		$str_optn_dcyc ='';
		$str_optn_dlb ='';

		$str_cyc_optn1_r ='';
		$str_cyc_optn1_p ='';
		$str_cyc_optn1_pos ='';

		$str_itemc_b = '';
		$str_itemc_r = '';
		$str_itemc_so = '';
		$str_itemc_n = '';
		$str_itemc_cyc ='';
		$str_optn2 = '';
		//NEW TABLE SALES
		$fld_stinqbrbranch_code = '';
		$tblsaleso_nw ='';
		$fld_lbdate_beg_first ='';
		
		$lb_temp = " {$this->db_erp}.`trx_manrecs_lb_dt`";
		if(!empty($fld_lbdate)) {
			//KUNIN ANG BEGINNING SA LAST MONT hal SEPT 2020 ngayon AUG 2020 ang kunin para begining
			$month = date("m",strtotime($fld_lbdate));
			$year = date("Y",strtotime($fld_lbdate));

			//FIRST DAY OF CURRENT MONTH'
			$str = " SELECT DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))-1 DAY) AS 'FIRST_DAY_CURR_MONTH'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->getRowArray();
			$fld_lbdate_first = $rw['FIRST_DAY_CURR_MONTH'];
			$q->freeResult();

			$fld_lbdate_beg = date('Y-m-d', strtotime('-1 day', strtotime($fld_lbdate_first)));//2020-08-31
			//USER ACCESS ONLY AS ALLEIN
			if($fld_stinqbr_dtefrom != ''){
				
				$str = "SELECT LAST_DAY('$fld_stinqbr_dtefrom' - INTERVAL 1 MONTH) AS 'LAST_DAY_OF_PREVIOUS_MONTH';";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$rw = $q->getRowArray();

				$fld_lbdate_first = $fld_stinqbr_dtefrom;
				$fld_lbdate_beg = $rw['LAST_DAY_OF_PREVIOUS_MONTH'];//
				$q->freeResult();
			}

			//BEGINNING FIRST DAY
			$str = " SELECT DATE_SUB(LAST_DAY('$fld_lbdate_beg'),INTERVAL DAY(LAST_DAY('$fld_lbdate_beg'))-1 DAY) AS 'FIRST_DAY_CURR_MONTH_BEG'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->getRowArray();
			$fld_lbdate_beg_first = $rw['FIRST_DAY_CURR_MONTH_BEG'];
			$q->freeResult();


			$str_optn1_b = "AND (aa.`LB_DATE` = '$fld_lbdate_beg' AND aa.`LB_DATE` ='$fld_lbdate_beg')";
			$str_optn1 = "AND (aa.`LB_DATE` ='$fld_lbdate' AND aa.`LB_DATE` ='$fld_lbdate')";
			

			$str_optn1_r = "AND (aa.`rcv_date` >= '$fld_lbdate_first' AND aa.`rcv_date`  <='$fld_lbdate') ";
			$str_optn1_p = "AND (aa.`po_date` >= '$fld_lbdate_first' AND aa.`po_date`  <= '$fld_lbdate') ";
			
			$str_optn1_pos = "AND (DATE(aa.`SO_DATE`) >= '$fld_lbdate_first' AND DATE(aa.`SO_DATE`) <='$fld_lbdate')";
			$str_optn1_cyc = "AND (yy.`C_DATE` >= '$fld_lbdate_first' AND yy.`C_DATE` <= '$fld_lbdate')";

			$str_optn1_lb = "AND (aa.`LB_DATE` >= '$fld_lbdate_beg_first' AND aa.`LB_DATE` <= '$fld_lbdate_beg')";
			

		}
		$startposting_date = "2020-08-31";
		if($fld_lbdate_beg == $startposting_date){
			$lb_temp = "{$this->db_erp}.`trx_manrecs_lb_hd`";
		}
		if(!empty($fld_stinqbritemcode_s)) { 
			$str = "select `ART_CODE` 
			from {$this->db_erp}.`mst_article` aa where `ART_CODE` = '$fld_stinqbritemcode_s'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> invalid itemcode!!!.</div>";
				die();
			}

			$rw = $q->getRowArray();
			$fld_stinqbritemcode_s = $rw['ART_CODE'];
			$q->free_result();
			
			$str_itemc2 = "WHERE (xx.`LB_ITEMC` = '$fld_stinqbritemcode_s')";
			
		}
		//BRANCH
		if(!empty($fld_stinqbrbranch) && !empty($fld_stinqbrbranch_id)) {
			$str = "select recid,BRNCH_NAME,BRNCH_OCODE2 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_stinqbrbranch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_stinqbrbranch_id'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'BRNCH','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}

			$rw = $q->getRowArray();
			$fld_stinqbrbranch_id = $rw['recid'];
			$fld_stinqbrbranch_code = $rw['BRNCH_OCODE2'];

			$q->freeResult();
			//END BRANCH
		}
		//CHECKING BRANCH CODE SALES
		if(empty($fld_stinqbrbranch_code)){
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid</strong> No Branch Code!!!.</div>";
			die();
		}else{
			$tblsaleso_nw = "`trx_E".$fld_stinqbrbranch_code ."_salesout`";
			$lb_temp =  " {$this->db_erp_br}.`trx_E".$fld_stinqbrbranch_code ."_lb_dly`";
		}


		$strqry = "
		SELECT
				    xx.`LB_ITEMC` ART_CODE,
			        xx.`LB_ITEMDESC` ART_DESC,
			        IFNULL(xx.`LBBE_BALQTY`,0) LBBE_BALQTY,
					IFNULL(xx.`LBRC_BALQTY`,0) LBRC_BALQTY,
					IFNULL(xx.`LBRCPO_BALQTY`,0) LBRCPO_BALQTY,
					IFNULL(xx.`LBPO_BALQTY`,0) LBPO_BALQTY,
					IFNULL(xx.`LBPOBR_BALQTY`,0) LBPOBR_BALQTY,
					IFNULL(xx.`LBSO_BALQTY`,0) LBSO_BALQTY,
					IFNULL(xx.`LB_BALQTY`,0) BQTY,
					IFNULL(xx.`LB_USRP`,0) ART_UPRICE,
					IFNULL(xx.`LB_TSRP`,0) TAMOUNT,
					'0.00' CQTY,
					'0.00' NLQTY,
					'0.00' NLVAR,
					 xx.`LB_DELV_LDTETRX`
					FROM {$lb_temp} xx
			        
				{$str_itemc2}
				GROUP BY xx.`LB_ITEMC`
		";
		//var_dump($strqry);
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
		
		$strqry_count = "
		SELECT
				    IFNULL(SUM(xx.`LBBE_BALQTY`),0) LBBE_BALQTY,
					IFNULL(SUM(xx.`LBRC_BALQTY`),0) LBRC_BALQTY,
					IFNULL(SUM(xx.`LBRCPO_BALQTY`),0) LBRCPO_BALQTY,
					IFNULL(SUM(xx.`LBPO_BALQTY`),0) LBPO_BALQTY,
					IFNULL(SUM(xx.`LBPOBR_BALQTY`),0) LBPOBR_BALQTY,
					IFNULL(SUM(xx.`LBSO_BALQTY`),0) LBSO_BALQTY,
					IFNULL(SUM(xx.`LB_BALQTY`),0) BQTY,
					IFNULL(SUM(xx.`LB_USRP`),0),
					IFNULL(SUM(xx.`LB_TSRP`),0) TAMOUNT,
					xx.`LB_SO_LDTETRX`
					FROM {$lb_temp} xx
			        
       			{$str_itemc2}
				

		";
		$qry_count = $this->mylibzdb->myoa_sql_exec($strqry_count,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry_count->getRowArray();
		$LBBE_BALQTY = $rw['LBBE_BALQTY'];
		$LBRC_BALQTY = $rw['LBRC_BALQTY'];
		$LBRCPO_BALQTY = $rw['LBRCPO_BALQTY'];
		$LBPO_BALQTY = $rw['LBPO_BALQTY'];
		$LBPOBR_BALQTY = $rw['LBPOBR_BALQTY'];
		$LBSO_BALQTY = $rw['LBSO_BALQTY'];
		$BQTY = $rw['BQTY'];
		$TAMOUNT = $rw['TAMOUNT'];
		$last_sales_date = $rw['LB_SO_LDTETRX'];
		$CQTY = 0;//$rw['CQTY'];
		$NLVAR = 0;//$rw['NLQTY'];
		$NLQTY = 0;//$rw['NLVAR'];
		
		$qry_count->freeResult();
		//0.00;//

		if($qry->getNumRows() > 0) { 
			$data['rlist'] = $qry->getResultArray();
			$data['tbltemp'] = $lb_temp;
			$data['last_sales_date'] =$last_sales_date;

			$data['fld_lbdate'] = $fld_lbdate;
			$data['fld_stinqbrbranch'] = $fld_stinqbrbranch;
			$data['fld_stinqbrbranch_id'] = $fld_stinqbrbranch_id;

			$data['LBBE_BALQTY'] = $LBBE_BALQTY;
			$data['LBRC_BALQTY'] = $LBRC_BALQTY;
			$data['LBRCPO_BALQTY'] = $LBRCPO_BALQTY;
			$data['LBPO_BALQTY'] = $LBPO_BALQTY;
			$data['LBPOBR_BALQTY'] = $LBPOBR_BALQTY;
			$data['LBSO_BALQTY'] = $LBSO_BALQTY;

			$data['BQTY'] = $BQTY;
			$data['TAMOUNT'] = $TAMOUNT;
			$data['CQTY'] = $CQTY;
			$data['NLVAR'] = $NLVAR;
			$data['NLQTY'] = $NLQTY;
			$data['fld_stinqbritemcode_s'] = $fld_stinqbritemcode_s;
		} else { 
			$data = array();
			$data['last_sales_date'] =$last_sales_date;
			$data['tbltemp'] = $lb_temp;
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['fld_lbdate'] = $fld_lbdate;
			$data['fld_stinqbrbranch'] = $fld_stinqbrbranch;
			$data['fld_stinqbrbranch_id'] = $fld_stinqbrbranch_id;

			$data['LBBE_BALQTY'] = $LBBE_BALQTY;
			$data['LBRC_BALQTY'] = $LBRC_BALQTY;
			$data['LBRCPO_BALQTY'] = $LBRCPO_BALQTY;
			$data['LBPO_BALQTY'] = $LBPO_BALQTY;
			$data['LBPOBR_BALQTY'] = $LBPOBR_BALQTY;
			$data['LBSO_BALQTY'] = $LBSO_BALQTY;

			$data['BQTY'] = $BQTY;
			$data['TAMOUNT'] = $TAMOUNT;
			$data['CQTY'] = $CQTY;
			$data['NLVAR'] = $NLVAR;
			$data['NLQTY'] = $NLQTY;
			$data['fld_stinqbritemcode_s'] = $fld_stinqbritemcode_s;
		}
		$qry->freeResult();
		return $data;
	} //end stockcard
}  //end main class 
