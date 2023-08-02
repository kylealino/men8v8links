<?php
namespace App\Models;
use CodeIgniter\Model;

class MySalesOutModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->db_erp_br = $this->mydbname->medb(1);
        $this->db_temp = $this->mydbname->medb(2);
        $this->mylibzdb = model('App\Models\MyLibzDBModel');
        $this->myusermod = model('App\Models\MyUserModel');
        $this->mylibzsys = model('App\Models\MyLibzSysModel');
        $this->request = \Config\Services::request();
    }

    public function meindex() {

    }

	public function sales_out_details_daily_rec($npages = 1,$npagelimit = 30,$msearchrec='',$fld_sc2_dtefrom='',$fld_sc2_dteto='') { 
		$cuser            = $this->myusermod->mysys_user();
		$mpw_tkn          = $this->myusermod->mpw_tkn();
		$fld_sc2branch     = $this->request->getVar('fld_sc2branch');
		$fld_sc2branch_id  = $this->request->getVar('fld_sc2branch_id');
		$fld_sc2itemcode_s = $this->request->getVar('fld_sc2itemcode_s');
		$__hmtkn_prd_sc2_c1 = array();
		$__hmtkn_prd_sc2_c2 = array();
		$__hmtkn_prd_sc2_c3 = array();
		$__hmtkn_prd_sc2_c4 = array();
		//CATEGORY
		$__hmtkn_prd_sc2_c1_ar = $this->request->getVar('__hmtkn_prd_sc2_c1');
		$__hmtkn_prd_sc2_c2_ar = $this->request->getVar('__hmtkn_prd_sc2_c2');
		$__hmtkn_prd_sc2_c3_ar = $this->request->getVar('__hmtkn_prd_sc2_c3');
		$__hmtkn_prd_sc2_c4_ar = $this->request->getVar('__hmtkn_prd_sc2_c4');
		$str_prd_sc2_c1		='';
		$str_prd_sc2_c2		='';
		$str_prd_sc2_c4		='';
		$str_prd_sc2_c3		='';
		//$fld_lbdate_beg = date('Y-m-d', strtotime('-1 day', strtotime($fld_sc2_dtefrom)));
		/*$month = date("m",strtotime($fld_sc2_dtefrom));
		var_dump($month);
		die();*/
		
		$__flag     ="C";
		$str_optn   = "";
		$str_optn1  = "";
		$str_branch ='';
		$str_itemc  ='';
		$str_tap ='';
		$str_tap = '';
		$fld_tap = $this->request->getVar('fld_tap');
		$fld_tap_code = '';
		$fld_sc2desccode = $this->request->getVar('fld_sc2desccode');
		$str_descc ='';
		//NEW TABLE SALES
		$fld_sc2branch_code = '';
		$tblsaleso_nw ='';

		if(!empty($fld_sc2desccode)) { 
			$str = "select ART_DESC_CODE 
			from {$this->db_erp}.`mst_article` aa where `ART_DESC_CODE` = '$fld_sc2desccode'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> invalid itemcode!!!.</div>";
				die();
			}

			$rw = $q->getRowArray();
			$fld_sc2desccode = $rw['ART_DESC_CODE'];
			$q->freeResult();
			$str_descc = "AND (c.`ART_DESC_CODE` = '$fld_sc2desccode')";
		}
		if(!empty($fld_tap)){
			if($fld_tap == 'TSHIRT') {
				$fld_tap_code = '0302';
			}
			elseif($fld_tap == 'PANTS'){
				$fld_tap_code = '0301';
			}
			else{ 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Invalid Department Name!!!.</div>";
				die();
			}
			$str_tap = "AND (c.`ART_HIERC2` = '$fld_tap_code' OR c.`ART_HIERC1` = '$fld_tap')";
		}
		if(!empty($fld_sc2itemcode_s)) { 
			$str = "select ART_CODE 
			from {$this->db_erp}.`mst_article` aa where `ART_CODE` = '$fld_sc2itemcode_s'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> invalid itemcode!!!.</div>";
				die();
			}

			$rw = $q->getRowArray();
			$fld_sc2itemcode_s = $rw['ART_CODE'];
			$q->freeResult();
			$str_itemc = "AND (a.`SO_ITEMCODE` = '$fld_sc2itemcode_s')";
		}
		if(!empty($msearchrec)) { 
			$msearchrec = $this->mylibzdb->dbx->escapeString($msearchrec);
			$str_optn = " AND (b.`BRNCH_NAME` like '%$msearchrec%' or a.`SO_COMP_ID` like '%$msearchrec%' or a.`SO_ITEMCODE` like '%$msearchrec%' or c.`ART_DESC` like '%$msearchrec%')";
		}
		$str_date="";
		if((!empty($fld_sc2_dtefrom) && !empty($fld_sc2_dteto)) && (($fld_sc2_dtefrom != '--') && ($fld_sc2_dteto != '--'))){
			$str_date="WHERE (a.`SO_DATE` >= '{$fld_sc2_dtefrom} 00:00:00' AND  a.`SO_DATE` <= '{$fld_sc2_dteto} 23:59:59')";
		}
		//BRANCH
		if(!empty($fld_sc2branch) && !empty($fld_sc2branch_id)) {
			$str = "select recid,BRNCH_NAME,BRNCH_OCODE2 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_sc2branch'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'BRNCH','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->resultID->num_rows == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> invalid Branch Data!!!.</div>";
				die();
			}

			$rw = $q->getRowArray();
			$fld_sc2branch_id = $rw['recid'];
			$fld_sc2branch_code = $rw['BRNCH_OCODE2'];
			$q->freeResult();
			$str_branch = " AND (a.`SO_BRANCH_ID` ='$fld_sc2branch_id')";
			//END BRANCH
		}
		//CHECKING BRANCH CODE SALES
		if(empty($fld_sc2branch_code)){
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid</strong> No Branch Code!!!.</div>";
			die();
		}else{
			$tblsaleso_nw = "`trx_E".$fld_sc2branch_code ."_salesout`";
		}
		//MAGVALIDATE MUNA ng MAY LAMAN
		//DIVISION
		if(!empty($__hmtkn_prd_sc2_c1_ar)){
			if(count($__hmtkn_prd_sc2_c1_ar) > 0) {
		      for($xx = 0; $xx < count($__hmtkn_prd_sc2_c1_ar); $xx++) { 
						$mprodcatg1_code = $__hmtkn_prd_sc2_c1_ar[$xx];
						if(!empty($mprodcatg1_code)){
							$str = "select MAT_CATG1_CODE from {$this->db_erp}.`mst_mat_catg1_hd` aa where MAT_CATG1_CODE = '$mprodcatg1_code' ";
							$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($q->resultID->num_rows == 0) { 
								echo "
								<div class=\"alert alert-danger\" role=\"alert\">
								Invalid Division Code DATA!!!
								</div>
								";
								die();
							}
							array_push($__hmtkn_prd_sc2_c1,$mprodcatg1_code); 
							$q->freeResult();
						}
						
		        } //end for 
		       
	    	}
		}
		//var_dump($str_prd_sc2_c1);
		//die();
		 //endif
		//DEPTCODE
		if(!empty($__hmtkn_prd_sc2_c2_ar)){
			if(count($__hmtkn_prd_sc2_c2_ar) > 0) {
				for($xx = 0; $xx < count($__hmtkn_prd_sc2_c2_ar); $xx++) { 
						$mprodcatg2_code = $__hmtkn_prd_sc2_c2_ar[$xx];
						if(!empty($mprodcatg2_code)){
							$str = "select `MAT_CATG2_CODE` from {$this->db_erp}.`mst_mat_catg2_hd` where MAT_CATG2_CODE = '$mprodcatg2_code' ";
					        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);							
							if($q->resultID->num_rows == 0) { 
								echo "
								<div class=\"alert alert-danger\" role=\"alert\">
								Invalid Department Code!!!
								</div>
								";
								die();
							}
							array_push($__hmtkn_prd_sc2_c2,$mprodcatg2_code); 
							$q->freeResult();
						}
		            } //end for 
		            
	        } //endif
		}//endif
		//CLASS
		if(!empty($__hmtkn_prd_sc2_c3_ar)){
			if(count($__hmtkn_prd_sc2_c3_ar) > 0) {
				for($xx = 0; $xx < count($__hmtkn_prd_sc2_c3_ar); $xx++) { 
						$mprodcatg3_code = $__hmtkn_prd_sc2_c3_ar[$xx];
						if(!empty($mprodcatg3_code)){
							$str = "select `MAT_CATG3_CODE` from {$this->db_erp}.`mst_mat_catg3_hd` where MAT_CATG3_CODE = '$mprodcatg3_code'";
					        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							
							if($q->resultID->num_rows == 0) { 
								echo "
								<div class=\"alert alert-danger\" role=\"alert\">
								Invalid Class Code!!!
								</div>
								";
								die();
							}
							array_push($__hmtkn_prd_sc2_c3,$mprodcatg3_code); 
							$q->freeResult();
						}
		            } //end for 
		   } //endif
		}
		//endif
		//SUBCLASS
	   if(!empty($__hmtkn_prd_sc2_c4_ar)){
	   		if(count($__hmtkn_prd_sc2_c4_ar) > 0) {
				for($xx = 0; $xx < count($__hmtkn_prd_sc2_c4_ar); $xx++) { 
						$mprodcatg4_code = $__hmtkn_prd_sc2_c4_ar[$xx];
						if(!empty($mprodcatg4_code)){
							$str = "
					        select `MAT_CATG4_CODE` from {$this->db_erp}.`mst_mat_catg4_hd` where MAT_CATG4_CODE = '$mprodcatg4_code'";
					        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							
							if($q->resultID->num_rows == 0) { 
								echo "
								<div class=\"alert alert-danger\" role=\"alert\">
								Invalid Sub Class Code!!!
								</div>
								";
								die();
							}
							array_push($__hmtkn_prd_sc2_c4,$mprodcatg4_code); 
							$q->freeResult();
						}
		            } //end for 
		    } //endif
	   }//endif


		//FINALLY
		//DIVISION
		if(!empty($__hmtkn_prd_sc2_c1)){
			if(count($__hmtkn_prd_sc2_c1) > 0) {
		        $str_prd_sc2_c1 = "";
				for($xx = 0; $xx < count($__hmtkn_prd_sc2_c1); $xx++) { 
						$mprodcatg1_code = $__hmtkn_prd_sc2_c1[$xx];
						if(!empty($mprodcatg1_code)){
							$str_prd_sc2_c1	.= "c.`ART_HIERC1` = '$mprodcatg1_code' or ";
						}
						
		        } //end for 
		        $str_prd_sc2_c1 = "AND (" . substr($str_prd_sc2_c1,0,strlen($str_prd_sc2_c1) - 3) . ")";
	    	}
		}
		//var_dump($str_prd_sc2_c1);
		//die();
		 //endif
		//DEPTCODE
		if(!empty($__hmtkn_prd_sc2_c2)){
			if(count($__hmtkn_prd_sc2_c2) > 0) {
				$str_prd_sc2_c2 = "";
				for($xx = 0; $xx < count($__hmtkn_prd_sc2_c2); $xx++) { 
						$mprodcatg2_code = $__hmtkn_prd_sc2_c2[$xx];
						if(!empty($mprodcatg2_code)){
							$str_prd_sc2_c2		.="c.`ART_HIERC2` = '$mprodcatg2_code' or ";
						}
		            } //end for 
		            $str_prd_sc2_c2 = "AND (" . substr($str_prd_sc2_c2,0,strlen($str_prd_sc2_c2) - 3) . ")";
	        	
			} //endif
		}//endif
		//CLASS
		if(!empty($__hmtkn_prd_sc2_c3)){
			if(count($__hmtkn_prd_sc2_c3) > 0) {
				$str_prd_sc2_c3 = "";
				for($xx = 0; $xx < count($__hmtkn_prd_sc2_c3); $xx++) { 
						$mprodcatg3_code = $__hmtkn_prd_sc2_c3[$xx];
						if(!empty($mprodcatg3_code)){
							$str_prd_sc2_c3		.="c.`ART_HIERC3` = '$mprodcatg3_code' or ";
						}
		            } //end for 
		            $str_prd_sc2_c3 = "AND (" . substr($str_prd_sc2_c3,0,strlen($str_prd_sc2_c3) - 3) . ")";
	       } //endif
		}
		//endif
		//SUBCLASS
	   if(!empty($__hmtkn_prd_sc2_c4)){
	   		if(count($__hmtkn_prd_sc2_c4) > 0) {
				$str_prd_sc2_c4 = "";
				for($xx = 0; $xx < count($__hmtkn_prd_sc2_c4); $xx++) { 
						$mprodcatg4_code = $__hmtkn_prd_sc2_c4[$xx];
						if(!empty($mprodcatg4_code)){
							$str_prd_sc2_c4		.="c.`ART_HIERC4` = '$mprodcatg4_code' or ";
						}
		            } //end for 
		            $str_prd_sc2_c4 = "AND (" . substr($str_prd_sc2_c4,0,strlen($str_prd_sc2_c4) - 3) . ")";
	        } //endif
	   }//endif
		$strqry = "
		SELECT 
		a.`recid`,
		a.`SO_ITEMCODE`,
		SUM(a.`SO_QTY`) SO_QTY,
		a.`SO_COST`,
		a.`SO_ASRP` SO_SRP,
		SUM(a.`SO_TAMT`) SO_TAMT,
		SUM(a.`SO_DISC_AMT`) SO_DISC_AMT,
		SUM(a.`SO_GROSS`) SO_GROSS,
		SUM(a.`SO_NET`) SO_NET,
		a.`SO_DATE`,
		a.`SO_BRANCH_ID`,
		a.`SO_COMP_ID`,
		a.`SO_TAG`,
		a.`SO_ENCD`,
		a.`SO_MUSER`,
		b.`BRNCH_NAME`,
		IFNULL(c.`ART_DESC_CODE`, 'NO_ITEM_FOUND') ART_DESC_CODE,
		IFNULL(c.`ART_DESC`,'NO_ITEM_FOUND') ART_DESC,
		IFNULL(c.`ART_UCOST`,0) ART_UCOST,
		IFNULL(c.`ART_UPRICE`,0) ART_UPRICE,
		IFNULL(SUM(c.`ART_UCOST`* a.`SO_QTY`),0) TCOST,
		IFNULL(SUM(c.`ART_UPRICE`* a.`SO_QTY`),0) TSRP
		FROM {$this->db_erp}.{$tblsaleso_nw} a
		JOIN {$this->db_erp}.`mst_companyBranch` b
		ON(a.`SO_BRANCH_ID` = b.`recid`)
		LEFT JOIN {$this->db_erp}.`mst_article` c
		ON(a.`SO_ITEMCODE` = c.`ART_CODE`)
		{$str_date}
		{$str_optn} 
		{$str_branch}
		{$str_itemc}
		{$str_descc}
		{$str_tap}
		{$str_prd_sc2_c1}
		{$str_prd_sc2_c2}
		{$str_prd_sc2_c4}
		{$str_prd_sc2_c3}
		GROUP BY a.`SO_DATE`,a.`SO_ITEMCODE`,`SO_BRANCH_ID` ASC 
		";
		
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'SALES_OUT_INQ','',$cuser,$strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
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
		SUM(a.`SO_QTY`) SO_QTY,
		SUM(a.`SO_DISC_AMT`) SO_DISC_AMT,
		SUM(a.`SO_GROSS`) SO_GROSS,
		SUM(a.`SO_NET`) SO_NET
		FROM {$this->db_erp}.{$tblsaleso_nw} a
		JOIN {$this->db_erp}.`mst_companyBranch` b
		ON(a.`SO_BRANCH_ID` = b.`recid`)
		LEFT JOIN {$this->db_erp}.`mst_article` c
		ON(a.`SO_ITEMCODE` = c.`ART_CODE`)
		{$str_date}
		{$str_optn} 
		{$str_branch}
		{$str_itemc}
		{$str_descc}
		{$str_tap}
		{$str_prd_sc2_c1}
		{$str_prd_sc2_c2}
		{$str_prd_sc2_c4}
		{$str_prd_sc2_c3}
		";
		$qry_count = $this->mylibzdb->myoa_sql_exec($strqry_count,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry_count->getRowArray();
		$SO_QTY = $rw['SO_QTY'];
		$SO_DISC_AMT = $rw['SO_DISC_AMT'];
		$SO_GROSS = $rw['SO_GROSS'];
		$SO_NET = $rw['SO_NET'];
		$qry_count->freeResult();

		if($qry->resultID->num_rows > 0) { 
			$data['rlist']       = $qry->getResultArray();
			$data['SO_QTY']      = $SO_QTY;
			$data['SO_DISC_AMT'] = $SO_DISC_AMT;
			$data['SO_GROSS']    = $SO_GROSS;
			$data['SO_NET']      = $SO_NET;

			$data['fld_sc2_dtefrom']   = $fld_sc2_dtefrom;
			$data['fld_sc2_dteto']     = $fld_sc2_dteto;
			$data['fld_sc2branch']     = $fld_sc2branch;
			$data['fld_sc2branch_id']  = $fld_sc2branch_id;
			$data['fld_sc2itemcode_s'] = $fld_sc2itemcode_s;
			$data['fld_sc2desccode'] = $fld_sc2desccode;
			$data['fld_tap'] = $fld_tap;

			$data['__hmtkn_prd_sc2_c1'] = $__hmtkn_prd_sc2_c1;
	        $data['__hmtkn_prd_sc2_c2'] = $__hmtkn_prd_sc2_c2;
	        $data['__hmtkn_prd_sc2_c4'] = $__hmtkn_prd_sc2_c4;
	        $data['__hmtkn_prd_sc2_c3'] = $__hmtkn_prd_sc2_c3;
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr']  = 1;
			$data['rlist']       = '';
			$data['SO_QTY']      = $SO_QTY;
			$data['SO_DISC_AMT'] = $SO_DISC_AMT;
			$data['SO_GROSS']    = $SO_GROSS;
			$data['SO_NET']      = $SO_NET;

			$data['fld_sc2_dteto']     = $fld_sc2_dteto;
			$data['fld_sc2_dtefrom']   = $fld_sc2_dtefrom;
			$data['fld_sc2branch']     = $fld_sc2branch;
			$data['fld_sc2branch_id']  = $fld_sc2branch_id;
			$data['fld_sc2itemcode_s'] = $fld_sc2itemcode_s;
			$data['fld_sc2desccode'] = $fld_sc2desccode;
			$data['fld_tap'] = $fld_tap;

			$data['__hmtkn_prd_sc2_c1'] = $__hmtkn_prd_sc2_c1;
	        $data['__hmtkn_prd_sc2_c2'] = $__hmtkn_prd_sc2_c2;
	        $data['__hmtkn_prd_sc2_c4'] = $__hmtkn_prd_sc2_c4;
	        $data['__hmtkn_prd_sc2_c3'] = $__hmtkn_prd_sc2_c3;
		}
		$qry->freeResult();
		return $data;
	} //end sales_out_details_daily_rec

	public function get_sales_for_tally() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();		 
		
		$adata = array();
		$medatef = $this->request->getVar('medatef');
		$medatet = $this->request->getVar('medatet');
		$mebranch = $this->request->getVar('mebranch');
		$mebranch_mtkn = $this->request->getVar('mebranch_mtkn');
		
		$str_optn = "";
		if(!empty($mebranch) && !empty($mebranch_mtkn)) {
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$mebranch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mebranch_mtkn'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_POS_TALLY_TAXR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$B_CODE = 'E' . $rw['B_OCODE2'];
			$tblivty = "{$this->db_erp_br}.`trx_{$B_CODE}_myivty_lb_dtl`";
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
			$str_optn = " where bb.`BRNCH_MBCODE` = '$B_CODE' ";
		} //end if
				
		$str = "
		SELECT
		CONCAT(\"select '\",Branch_code,\"' ME_BRANCH,\",
		\"'\",bb.BRNCH_NAME,\"' ME_BRANCH_NAME,\",
		\"'\",bb.BRNCH_MBCODE,\"' ME_BRANCH_MBCODE,\",
		\"'\",bb.BRNCH_MAT_FLAG,\"' ME_BRANCH_MAT_FLAG,\",
		\"(SELECT SUM(SO_NET) FROM {$this->db_erp}.`trx_\",TRIM(Branch_code),\"_salesout` where date(`SO_DATE`) between date('{$medatef}') and date('{$medatet}')) ME_SO_NETSALE_AMT,\",
		\"(SELECT SUM(`NetSales`) FROM {$this->db_erp_br}.`Branch_Terminal_Sales` where (date(`Date`) between date('{$medatef}') and date('{$medatet}')) and `Branch_Code` = '\",Branch_code,\"' ) ME_POST_NETSALE_AMT union all \"
		 ) meqry 
		 FROM {$this->db_erp}.mst_branch_ivty_tag aa JOIN {$this->db_erp}.mst_companyBranch bb ON(aa.Branch_code = CONCAT('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME
		";

		$str = "
		SELECT
		CONCAT(\"select '\",Branch_code,\"' ME_BRANCH,\",
		\"'\",bb.BRNCH_NAME,\"' ME_BRANCH_NAME,\",
		\"'\",bb.BRNCH_MBCODE,\"' ME_BRANCH_MBCODE,\",
		\"'\",bb.BRNCH_MAT_FLAG,\"' ME_BRANCH_MAT_FLAG,\",
		\"(SELECT SUM(cast(SO_NET as decimal(15,2))) FROM {$this->db_erp}.`trx_\",TRIM(Branch_code),\"_salesout` where date(`SO_DATE`) between date('{$medatef}') and date('{$medatet}')) ME_SO_NETSALE_AMT,\",
		\"(SELECT SUM(cast(`NetSales` as decimal(15,2))) FROM {$this->db_erp_br}.`Branch_Terminal_Sales` where (date(`Date`) between date('{$medatef}') and date('{$medatet}')) and `Branch_Code` = '\",Branch_code,\"' ) ME_POST_NETSALE_AMT union all \"
		 ) meqry 
		 FROM {$this->db_erp}.mst_branch_ivty_tag aa JOIN {$this->db_erp}.mst_companyBranch bb ON(aa.Branch_code = CONCAT('E',bb.BRNCH_OCODE2)) {$str_optn} ORDER BY bb.BRNCH_NAME
		";

		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "";
		foreach($q->getResultArray() as $rw): 
			$str .= $rw['meqry'];
		endforeach;
		$q->freeResult();
		//die();
		//$rw = $q->getRowArray();
		$ctbltmp = "{$this->db_temp}.`metmp_sotally_"  . $this->mylibzsys->random_string(15) . "`";
		$str = substr($str,0,strlen($str) - 10);
		$str = "create table if not exists {$ctbltmp} {$str}"; 
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "select aa.*,
		(select sum(cast(ME_SO_NETSALE_AMT as decimal(15,2))) from {$ctbltmp} where ME_BRANCH_MBCODE = aa.ME_BRANCH_MBCODE) MB_CODE_AMOUNT  
		 from {$ctbltmp} aa ";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$adata['rlist'] = $q->getResultArray();
			$adata['rtbltmp'] = $ctbltmp;
		} else { 
			$adata['rlist'] = '';
			$adata['rtbltmp'] = '';
		} 
		$q->freeResult();
		return $adata;
	 } //end get_sales_for_tally

	public function get_sales_branch_per_day_for_tally() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();		 
		
		$adata = array();
		$medatef = $this->request->getVar('medatef');
		$medatet = $this->request->getVar('medatet');
		$mebranch = $this->request->getVar('mebranch');
		$mebranch_mtkn = $this->request->getVar('mebranch_mtkn');
		if($medatef > $medatet):
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Date Range!!!</div>";
			die();
		endif;
		
		$str_optn = "";
		if(!empty($mebranch) && !empty($mebranch_mtkn)) {
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG,BRNCH_MBCODE 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$mebranch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mebranch_mtkn'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_POS_DAILY_TALLY_CHECK','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$B_CODE = 'E' . $rw['B_OCODE2'];
			$B_NAME = $rw['BRNCH_NAME'];
			$B_MBCODE = $rw['BRNCH_MBCODE'];
			$tblivty = "{$this->db_erp_br}.`trx_{$B_CODE}_myivty_lb_dtl`";
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
			$str_optn = " where bb.`BRNCH_MBCODE` = '$B_CODE' ";
		} else {
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong>Branch is REQUIRED!!!</div>";
			die();
		} //end if
		
		$str = "select (DATEDIFF(date('$medatet'),DATE('$medatef')) + 1) medays";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->getRow();
		$ndays = $rw->medays;
		$str = "";
		for($xx = 0; $xx < $ndays; $xx++):
			echo $xx . '<br/>';
			$str = "
			SELECT
			CONCAT(\"select '{$B_CODE}' ME_BRANCH,\",
			\"'{$B_NAME}' ME_BRANCH_NAME,\",
			\"'{$B_MBCODE}' ME_BRANCH_MBCODE,\",
			\"(SELECT SUM(cast(SO_NET as decimal(15,2))) FROM {$this->db_erp}.`trx_{$B_CODE}_salesout` where date(`SO_DATE`) = date_add('{$medatef}',interval 1 day) ) ME_SO_NETSALE_AMT,\",
			\"(SELECT SUM(cast(`NetSales` as decimal(15,2))) FROM {$this->db_erp_br}.`Branch_Terminal_Sales` where (date(`Date`) = date_add('{$medatef}',interval 1 day)) and `Branch_Code` = '{$B_CODE}' ) ME_POST_NETSALE_AMT union all \")			
			";
			
		endfor;
		echo $str . '<br/>';
			$adata['rlist'] = '';
			$adata['rtbltmp'] = '';
		
		return $adata;
	} //end get_sales_branch_per_day_for_tally
	
	 public function POS_Tally_Summary() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();		 
		 
		$adata = array();
		$medatef = $this->request->getVar('medatef');
		$medatet = $this->request->getVar('medatet');
				
		$mebranch = $this->request->getVar('mebranch');
		$mebranch_mtkn = $this->request->getVar('mebranch_mtkn');
		$str_optn = "";
		if(!empty($mebranch) && !empty($mebranch_mtkn)) {
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$mebranch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mebranch_mtkn'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_POS_TALLY_TAXR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$B_CODE = 'E' . $rw['B_OCODE2'];
			$tblivty = "{$this->db_erp_br}.`trx_{$B_CODE}_myivty_lb_dtl`";
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
			$str_optn = " and aa.`Branch_Code` = '$B_CODE' ";
		} //end if
						
		$ctbltmp = "{$this->db_temp}.`metmp_sotally_"  . $this->mylibzsys->random_string(15) . "`";		
		$str = "create table if not exists {$ctbltmp} select `Branch_Code`,aa.`ECRNo`,min(aa.`Date`) `TrxDateF`,max(aa.`Date`) `TrxDateT` from {$this->db_erp_br}.Branch_Terminal_Sales aa 
		where (date(aa.`Date`) between date('{$medatef}') and date('{$medatet}')) group by aa.`Branch_Code`,aa.`ECRNo`
		";
		//$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = " select 
		`Branch`,
		aa.`Branch_Code`,
		`XRefNo`,
		`ZRefNo`,
		`ECRNo`,
		`SerialNo`,
		SUM(`GrossSales`) `Gross Sales`,
		SUM(`TotalReturns`) TotalReturns,
		SUM(`TotalVoids`) TotalVoids,
		SUM(`TotalDiscounts`) TotalDiscounts,
		SUM(`SeniorCitizenDisc`) SeniorCitizenDisc,
		SUM(`PWDDisc`) PWDDisc,
		SUM(`SCPWDVATExempt`) SCPWDVATExempt,
		SUM(`PromoDisc`) PromoDisc,
		SUM(`Refund`) Refund,
		SUM(`NetSales`) `Net Sales`,
		SUM(`QtySold`) `Qty Sold`,
		SUM(`QtySoldPerPos`) QtySoldPerPos,
		SUM(`ActualNetSales`) `Actual Net Sales`,
		sum(`Difference`) `Discrepancy`,
		`MIN`,
		min(`BegSI`) `Beg. S.I.`,
		max(`EndSI`) `End S.I`,
		SUM(`SICount`) SICount,
		SUM(`VATableSales`) VATableSales,
		SUM(`VATExemptSales`) VATExemptSales,
		SUM(`VATZeroRatedSales`) VATZeroRatedSales,
		SUM(`VAT`) `VAT`,
		`OldGrandTotal`,
		`OldGrandTotalPerPos`,
		`NewGrandTotal`,
		`NewGrandTotalPerPos`,
		SUM(`GrossZ`) GrossZ from {$this->db_erp_br}.Branch_Terminal_Sales aa join {$this->db_erp}.mst_branch_ivty_tag bb on (aa.`Branch_Code` = bb.Branch_code) 
		JOIN {$this->db_erp}.mst_companyBranch cc ON (bb.Branch_code = CONCAT('E',cc.BRNCH_OCODE2)) 
		where (date(aa.`Date`) between date('{$medatef}') and date('{$medatet}')) {$str_optn} group by aa.`Branch_Code`,aa.`SerialNo` ORDER BY cc.BRNCH_NAME,aa.`MIN` 
		";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$adata['rfieldnames'] = $q->getFieldNames();
			$adata['rlist'] = $q->getResultArray();
			$adata['rtbltmp'] = $ctbltmp;
		} else { 
			$adata['rfieldnames'] = '';
			$adata['rlist'] = '';
			$adata['rtbltmp'] = '';
		} 
		$q->freeResult();
		return $adata;
		
	 } //end POS_Tally_Summary

	 public function POS_TAXR_Summary() { 
		$cuser            = $this->myusermod->mysys_user();
		$mpw_tkn          = $this->myusermod->mpw_tkn();		 
		$adata = array();
		$medatef = $this->request->getVar('medatef');
		$medatet = $this->request->getVar('medatet');
		$mebranch = $this->request->getVar('mebranch');
		$mebranch_mtkn = $this->request->getVar('mebranch_mtkn');
		$str_optn = "";
		if(!empty($mebranch) && !empty($mebranch_mtkn)) {
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$mebranch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mebranch_mtkn'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_POS_TALLY_TAXR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$B_CODE = 'E' . $rw['B_OCODE2'];
			$tblivty = "{$this->db_erp_br}.`trx_{$B_CODE}_myivty_lb_dtl`";
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
			$str_optn = " and aa.`Branch_Code` = '$B_CODE' ";
		} //end if
				
		$ctbltmp = "{$this->db_temp}.`metmp_sotally_"  . $this->mylibzsys->random_string(15) . "`";		
		$str = "create table if not exists {$ctbltmp} select `Branch_Code`,aa.`ECRNo`,min(aa.`Date`) `TrxDateF`,max(aa.`Date`) `TrxDateT`,0 `BegSI`,0 `EndSI`,
		0.00 `OldGrandTotal`,0.00 `NewGrandTotal` from {$this->db_erp_br}.Branch_Terminal_Sales aa 
		where (date(aa.`Date`) between date('{$medatef}') and date('{$medatet}')) {$str_optn} group by aa.`Branch_Code`,aa.`ECRNo`
		";
		//$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "alter table {$ctbltmp} ADD INDEX `idx01` (`Branch_Code`); ";
		//$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "alter table {$ctbltmp} ADD INDEX `idx02` (`ECRNo`); ";
		//$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = "update {$ctbltmp} dd set 
		`BegSI` = (select `BegSI` from {$this->db_erp_br}.Branch_Terminal_Sales where `Branch_Code` = dd.Branch_code and `ECRNo` = dd.`ECRNo` and date(`Date`) = date(dd.TrxDateF) limit 1),
		`EndSI` = (select `EndSI` from {$this->db_erp_br}.Branch_Terminal_Sales where `Branch_Code` = dd.Branch_code and `ECRNo` = dd.`ECRNo` and date(`Date`) = date(dd.TrxDateT) limit 1),
		`OldGrandTotal` = (select `OldGrandTotal` from {$this->db_erp_br}.Branch_Terminal_Sales where `Branch_Code` = dd.Branch_code and `ECRNo` = dd.`ECRNo` and date(`Date`) = date(dd.TrxDateF) limit 1),
		`NewGrandTotal` = (select `NewGrandTotal` from {$this->db_erp_br}.Branch_Terminal_Sales where `Branch_Code` = dd.Branch_code and `ECRNo` = dd.`ECRNo` and date(`Date`) = date(dd.TrxDateT) limit 1)
		";
		//$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//echo $ctbltmp . '<br/>';
		//die();
		
		$str = " select 
		`Branch`,
		aa.`ECRNo` `ECR No.`,
		`SerialNo`,
		SUM(`GrossSales`) `Gross Sales`,
		SUM(`TotalReturns`) `Returns`,
		SUM(`TotalVoids`) `Voids`,
		SUM(`SeniorCitizenDisc`) `SC Disc.`,
		SUM(`PWDDisc`) `PWD Disc.`,
		SUM(`TotalDiscounts`) `Other Disc.`,
		SUM(`SCPWDVATExempt`) `SC/PWD Vat Ex`,
		SUM(`PromoDisc`) `Promo Disc.`,
		SUM(`Refund`) `Refund`,
		SUM(`NetSales`) `Net Sales`,
		SUM(`QtySold`) `Qty Sold`,
		SUM(`ActualNetSales`) `Actual Net Sales`,
		sum(`Difference`) `Discrepancy`,
		`MIN` `M.I.N.`,
		(select `BegSI` from {$this->db_erp_br}.Branch_Terminal_Sales where `Branch_Code` = dd.Branch_code and `ECRNo` = dd.`ECRNo` and date(`Date`) = date(dd.TrxDateF) limit 1) `Beg. S.I`,
		(select `EndSI` from {$this->db_erp_br}.Branch_Terminal_Sales where `Branch_Code` = dd.Branch_code and `ECRNo` = dd.`ECRNo` and date(`Date`) = date(dd.TrxDateT) limit 1) `End S.I`,
		SUM(`SICount`) `S.I. Count`,
		SUM(`VATableSales`) `VATable Sales`,
		SUM(`VATExemptSales`) `VAT Exempt Sales`,
		SUM(`VATZeroRatedSales`) `Zero Rated Sales`,
		SUM(`VAT`) `VAT`,
		(select `OldGrandTotal` from {$this->db_erp_br}.Branch_Terminal_Sales where `Branch_Code` = dd.Branch_code and `ECRNo` = dd.`ECRNo` and date(`Date`) = date(dd.TrxDateF) limit 1) `Old Grand Total`,
		(select `NewGrandTotal` from {$this->db_erp_br}.Branch_Terminal_Sales where `Branch_Code` = dd.Branch_code and `ECRNo` = dd.`ECRNo` and date(`Date`) = date(dd.TrxDateT) limit 1) `New Grand Total` 
		 from {$this->db_erp_br}.Branch_Terminal_Sales aa join {$this->db_erp}.mst_branch_ivty_tag bb on (aa.`Branch_Code` = bb.Branch_code) 
		JOIN {$this->db_erp}.mst_companyBranch cc ON (bb.Branch_code = CONCAT('E',cc.BRNCH_OCODE2)) 
		join {$ctbltmp} dd on (dd.`Branch_Code` = aa.Branch_code and dd.`ECRNo` = aa.`ECRNo`)  
		where (date(aa.`Date`) between date('{$medatef}') and date('{$medatet}')) {$str_optn} group by aa.`SerialNo` ORDER BY cc.BRNCH_NAME,aa.`MIN` 
		";

		$str = " select 
		`Branch`,
		aa.`ECRNo` `ECR No.`,
		`SerialNo`,
		SUM(`GrossSales`) `Gross Sales`,
		SUM(`TotalReturns`) `Returns`,
		SUM(`TotalVoids`) `Voids`,
		SUM(`SeniorCitizenDisc`) `SC Disc.`,
		SUM(`PWDDisc`) `PWD Disc.`,
		SUM(`TotalDiscounts`) `Other Disc.`,
		SUM(`SCPWDVATExempt`) `SC/PWD Vat Ex`,
		SUM(`PromoDisc`) `Promo Disc.`,
		SUM(`Refund`) `Refund`,
		SUM(`NetSales`) `Net Sales`,
		SUM(`QtySold`) `Qty Sold`,
		SUM(`ActualNetSales`) `Actual Net Sales`,
		sum(`Difference`) `Discrepancy`,
		`MIN` `M.I.N.`,
		min(`BegSI`) `Beg. S.I`,
		max(`EndSI`) `End S.I`,
		SUM(`SICount`) `S.I. Count`,
		SUM(`VATableSales`) `VATable Sales`,
		SUM(`VATExemptSales`) `VAT Exempt Sales`,
		SUM(`VATZeroRatedSales`) `Zero Rated Sales`,
		SUM(`VAT`) `VAT`,
		min(`OldGrandTotal`) `Old Grand Total`,
		max(`NewGrandTotal`) `New Grand Total` 
		 from {$this->db_erp_br}.Branch_Terminal_Sales aa join {$this->db_erp}.mst_branch_ivty_tag bb on (aa.`Branch_Code` = bb.Branch_code) 
		JOIN {$this->db_erp}.mst_companyBranch cc ON (bb.Branch_code = CONCAT('E',cc.BRNCH_OCODE2)) 
		where (date(aa.`Date`) between date('{$medatef}') and date('{$medatet}')) {$str_optn} group by aa.`Branch_Code`,aa.`SerialNo` ORDER BY cc.BRNCH_NAME,aa.`MIN` 
		";
		
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$adata['rfieldnames'] = $q->getFieldNames();
			$adata['rlist'] = $q->getResultArray();
		} else { 
			$adata['rfieldnames'] = '';
			$adata['rlist'] = '';
		} 
		$q->freeResult();
		return $adata;
		
	 } //end POS_TAXR_Summary
	
	public function sales_out_itemized_abranch_proc() { 
		$cuser            = $this->myusermod->mysys_user();
		$mpw_tkn          = $this->myusermod->mpw_tkn();
		$adata = array();
		$medatef = $this->request->getVar('medatef');
		$medatet = $this->request->getVar('medatet');
		
		$medatef = $this->mylibzsys->mydate_yyyymmdd($this->myusermod->request->getVar('medatef'));
		$medatet = $this->mylibzsys->mydate_yyyymmdd($this->myusermod->request->getVar('medatet'));
		
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
			$str_optn = " `SO_ITEMCODE` = '$ART_CODE' AND ";
		} else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong>Product Item is REQUIRED!!!</div>";
			die();
		} //end if
				
		$str = "
		SELECT
		CONCAT(\"select '\",Branch_code,\"' ME_BRANCH,\",
		\"'\",bb.BRNCH_NAME,\"' ME_BRANCH_NAME,\",
		\"'{$ART_CODE}' ART_CODE,\",
		\"'{$ART_DESC}' ART_DESC,\",
		\"'{$ART_BCODE}' ART_BCODE,\",
		\"(SELECT SUM(cast(SO_NET as decimal(15,2))) FROM {$this->db_erp}.`trx_\",TRIM(Branch_code),\"_salesout` where {$str_optn} (date(`SO_DATE`) between date('{$medatef}') and date('{$medatet}')) ) ME_SO_NETSALE_AMT union all \" 
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
		return $adata;		
	} //end sales_out_itemized_abranch_proc
	
	public function mesales_recon_reupload() { 
		$cuser  = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$mebranch = $this->myusermod->request->getVar('mebranch');
		$medatetrx = $this->myusermod->request->getVar('medatetrx');
		$str = "select `recid` from {$this->db_erp_br}.`Branch_POSData_Extract` where `BCODE` = '$mebranch' AND date(`DATA_DATE_EXTR_FROM`) = date('$medatetrx')";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0):
			$str = "update {$this->db_erp_br}.`Branch_POSData_Extract` set `DATA_TAG` = ''
			where `BCODE` = '$mebranch' AND date(`DATA_DATE_EXTR_FROM`) = date('$medatetrx')";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		else:
			$str = "insert into {$this->db_erp_br}.`Branch_POSData_Extract` (`DATA_TAG`,`BCODE`,`DATA_DATE_EXTR_FROM`,`DATA_DATE_EXTR_TO`) 
			values('','$mebranch',date('$medatetrx'),date('$medatetrx'))";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		endif;
		$q->freeResult();
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_POS_SALES_RECON_REUPLOAD','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		echo "<div class=\"alert alert-success mb-0 fw-bold\" role=\"alert\">Requested Sales Recon Reuploading for {$mebranch} transaction dated {$medatetrx} is now queuing for processing...</div>";
	} //end mesales_recon_reupload
	
} //end MySalesOutModel
