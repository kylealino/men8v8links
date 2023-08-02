<?php

namespace App\Controllers;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;
use App\Models\MyUserModel;
use App\Models\MyDatumModel;

class MySearchData extends BaseController
{
	public function __construct()
	{
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
		$this->myusermod =  new MyUserModel();
		$this->mydatum =  new MyDatumModel();
		
	}
	
	public function mat_article_vend() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		$comp_usr = $this->myusermod->ua_comp_code($this->db_erp,$cuser);
		$str_comp='';
		if(count($comp_usr) > 0) { 
			$str_comp = "";
			for($xx = 0; $xx < count($comp_usr); $xx++) { 
				$mart_comp = $comp_usr[$xx];
				$str_comp .= "SUBSTR(ART_COMP,1,INSTR(ART_COMP,'~')-1)= '$mart_comp' or ";
            } //end for 
            $str_comp = "and (" . substr($str_comp,0,strlen($str_comp) - 3) . ")";
        }
        $fld_suppcode = $this->request->getVar('mtknvcode'); //GET id
        $fld_supptag = $this->request->getVar('mtknvtag'); //GET id
       	$fld_pbranch = $this->request->getVar('pbranchid'); //GET id
       	$str_branch ="";
       	$BRNCH_MAT_FLAG ='';
       	if(!empty($fld_pbranch)){
       		$str = "select recid,BRNCH_NAME,BRNCH_CODE,BRNCH_OCODE2,BRNCH_MAT_FLAG
       		from {$this->db_erp}.`mst_companyBranch` aa where `recid` = '$fld_pbranch'";
       		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
       		$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
       		$rw = $q->getRowArray();
       		$BRNCH_MAT_FLAG = $rw['BRNCH_MAT_FLAG'];
       		$fld_branch_recid = $rw['recid'];
       		$str_branch ="AND kk.`brnchID` = '$fld_branch_recid' ";
       		$q->freeResult();
			//END BRANCH
       	}
       	
       	$result = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuamd_id='145'","myua_md");
       	if($result == 1){
       		$str = "
       		SELECT recid,ART_DESC,trim(ART_CODE) __mdata,
       		ART_SKU,ART_SDU,ART_IMG,ART_NCBM,ART_NCONVF,ART_UPRICE,ART_UCOST,
       		sha2(concat(recid,'{$mpw_tkn}'),384) mtkn_prdltr 
       		FROM {$this->db_erp}.`mst_article` 
       		WHERE ART_ISDISABLE = '0' 
       		AND (ART_HIERC1 = 'TSHIRT' OR ART_HIERC1 = 'PANTS') 
       		AND (ART_CODE like '%$term%' or ART_DESC like '%$term%' or ART_BARCODE1 like '%$term%') 
       		ORDER BY ART_DESC LIMIT 50 
       		";
       	}
       	elseif($BRNCH_MAT_FLAG === 'G'){
       		$str = "
       		SELECT 
       		a.`recid`,
       		a.`ART_DESC`,
       		trim(a.`ART_CODE`) __mdata,
       		a.`ART_SKU`,
       		a.`ART_SDU`,
       		a.`ART_IMG`,
       		a.`ART_NCBM`,
       		a.`ART_NCONVF`,
       		IFNULL(kk.`art_uprice`,a.`ART_UPRICE`) ART_UPRICE,
       		IFNULL(kk.`art_cost`,a.`ART_UCOST`) ART_UCOST,
       		sha2(concat(a.`recid`,'{$mpw_tkn}'),384) mtkn_prdltr 
       		FROM {$this->db_erp}.`mst_article`  a
       		JOIN `mst_article_per_branch` kk
       		ON (a.`recid` = kk.`artID`)
       		WHERE a.`ART_ISDISABLE` = '0' {$str_branch} 
       		AND (a.`ART_CODE` like '%$term%' or a.`ART_DESC` like '%$term%' or a.`ART_BARCODE1` like '%$term%') 
       		ORDER BY a.`ART_DESC` LIMIT 50 
       		";
       	}

       	elseif(($BRNCH_MAT_FLAG != 'G') && ($fld_supptag == 'N')){
       		$str = "
       		SELECT aa.`recid`,aa.`ART_DESC`,trim(aa.`ART_CODE`) __mdata,
       		aa.`ART_SKU`,aa.`ART_SDU`,aa.`ART_IMG`,aa.`ART_NCBM`,aa.`ART_NCONVF`,aa.`ART_UPRICE`,aa.`ART_UCOST`,
       		sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) mtkn_prdltr 
       		FROM {$this->db_erp}.`mst_article` aa
       		JOIN `mst_vend_artm` bb
       		ON (aa.`ART_CODE` = bb.`ART_CODE`)
       		WHERE aa.`ART_ISDISABLE` = '0' 
       		AND bb.`ART_VCODE` = '{$fld_suppcode}'
       		AND (aa.`ART_CODE` like '%$term%' or aa.`ART_DESC` like '%$term%' or aa.`ART_BARCODE1` like '%$term%') 
       		ORDER BY aa.`ART_DESC` LIMIT 50 
       		";
       	}
       	else{
       		$str = "
       		SELECT aa.`recid`,aa.`ART_DESC`,trim(aa.`ART_CODE`) __mdata,
       		aa.`ART_SKU`,aa.`ART_SDU`,aa.`ART_IMG`,aa.`ART_NCBM`,aa.`ART_NCONVF`,aa.`ART_UPRICE`,aa.`ART_UCOST`,
       		sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) mtkn_prdltr 
       		FROM {$this->db_erp}.`mst_article` aa
       		WHERE aa.`ART_ISDISABLE` = '0' 
       		AND (aa.`ART_CODE` like '%$term%' or aa.`ART_DESC` like '%$term%' or aa.`ART_BARCODE1` like '%$term%') 
       		ORDER BY aa.`ART_DESC` LIMIT 50 
       		";
       	}

       	$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
       	if($q->getNumRows() > 0) { 
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
       				"ART_NCONVF" => $row['ART_NCONVF'],
       				"ART_UPRICE" => $row['ART_UPRICE'],
       				"ART_UCOST" => $row['ART_UCOST'],  
       				"ART_CODE" => $row['__mdata'],
       				"ART_NCBM" => $row['ART_NCBM'],
       				"ART_MATRID" => $row['recid'],
       			));
       		endforeach;
       	}
       	$q->freeResult();
       	echo json_encode($autoCompleteResult);
       } //mat_article_vend receiving
	
	public function company_search_v() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		$aua_comp = $this->myusermod->ua_comp($this->db_erp,$cuser);
		$str_comp = " recid = '__MECOMP__' ";

		if(count($aua_comp) > 0) { 
			$str_comp = "";
			for($xx = 0; $xx < count($aua_comp); $xx++) { 
				$mcomp = $aua_comp[$xx];
				$str_comp .= " recid = '$mcomp' or ";
            } //end for 
            $str_comp = "(" . substr($str_comp,0,strlen($str_comp) - 3) . ")";
        }
                
        $term = $this->request->getVar('term');
        $autoCompleteResult = array();
        $str = "
        select recid,COMP_CODE,COMP_NAME __mdata,COMP_ADDR1,COMP_TINNO from {$this->db_erp}.mst_company where {$str_comp} AND (COMP_CODE like '%{$term}%' or COMP_NAME like '%{$term}%') order by COMP_NAME limit 5 ";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); // AND {$str_comp} 
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['COMP_CODE'] . $mpw_tkn); 
				$mtkn_recid = hash('sha384', $row['recid'] . $mpw_tkn);
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_rid" => $mtkn_rid,
					"COMP_ADDR1" => $row['COMP_ADDR1'],
					"COMP_TINNO" => $row['COMP_TINNO'],
					"mtkn_recid" => $mtkn_recid ));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end company_search_v receiving
	
	//old from companybranch_v
	public function area_company(){ 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		$mtkn_grparea = $this->request->getVar('mtkn_grparea');
		$str_grparea = '';
		if(!empty($mtkn_grparea)){
			$fld_itmgrparea_f = explode("=>",$mtkn_grparea);
			$fld_itmgrp_s = $fld_itmgrparea_f[0];
			$fld_itmarea_s = $fld_itmgrparea_f[1];
			$str_grparea = " AND (a.`BRNCH_GROUP` = '$fld_itmgrp_s') AND (a.`BRNCH_AREA` = '$fld_itmarea_s') ";
		}
		
		$aua_branch = $this->myusermod->ua_brnch($this->db_erp,$cuser);
		$str_branch = " a.`recid` = '__MEBRNCH__' ";

		if(count($aua_branch) > 0) { 
			$str_branch = "";
			for($xx = 0; $xx < count($aua_branch); $xx++) { 
				$mbranch = $aua_branch[$xx];
				$str_branch .= " a.`recid` = '$mbranch' or ";
            } //end for 
            $str_branch = "(" . substr($str_branch,0,strlen($str_branch) - 3) . ")";
        }
        
        $mtkn_compid = $this->request->getVar('mtkn_compid');
        $term = $this->request->getVar('term');
       	//$terms = explode('XOX', $term);
        $autoCompleteResult = array();
        if(!empty($mtkn_compid)){
        	$str = "
        	SELECT a.`recid` Rbrnch,
        	a.`BRNCH_CODE`,
        	a.`BRNCH_NAME` __mdata,
        	b.`COMP_NAME`,
        	b.`recid` Rcomp,
        	a.`BRNCH_CPRSN`
        	from {$this->db_erp}.mst_companyBranch a
        	LEFT JOIN {$this->db_erp}.mst_company b
        	ON (a.`COMP_CODE` = b.`COMP_CODE`)
        	where {$str_branch} {$str_grparea} AND sha2(concat(b.`recid`,'{$mpw_tkn}'),384) = '{$mtkn_compid}' AND (a.`BRNCH_CODE` like '%{$term}%' or a.`BRNCH_NAME` like '%{$term}%') 
        	order by BRNCH_NAME limit 15 
        	";

        }
        else{
        	$str = "
        	SELECT a.`recid` Rbrnch,a.`BRNCH_CODE`,
        	a.`BRNCH_NAME` __mdata,
        	b.`COMP_NAME`,
        	a.`BRNCH_CPRSN`
        	from {$this->db_erp}.mst_companyBranch a
        	LEFT JOIN {$this->db_erp}.mst_company b
        	ON (a.`COMP_CODE` = b.`COMP_CODE`)
        	where {$str_branch} {$str_grparea} AND (a.`BRNCH_CODE` like '%{$term}%' or a.`BRNCH_NAME` like '%{$term}%') 
        	order by BRNCH_NAME limit 15 
        	";
        }


		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['BRNCH_CODE'] . $mpw_tkn);
				$mtknr_rid = hash('sha384', $row['Rbrnch'] . $mpw_tkn);  
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_rid" => $mtkn_rid,
					"mtknr_rid" => $mtknr_rid,
					"mtkn_brnch" => $row['Rbrnch'],
					"mtkn_comp" => $row['COMP_NAME'],
					"contact_person" => $row['BRNCH_CPRSN'],
				));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	} //end	area_company
	
	//old from vendor_ua 
	public function vendor_ua() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		$aua_supp = $this->myusermod->ua_supp($this->db_erp,$cuser);
		$str_supp = "`recid` = '__MESUPP__' ";
		if(count($aua_supp) > 0) { 
			$str_supp = "";
			for($xx = 0; $xx < count($aua_supp); $xx++) { 
				$msupp = $aua_supp[$xx];
				$str_supp .= "`recid` = '$msupp' or ";
            } //end for 
            $str_supp = " (" . substr($str_supp,0,strlen($str_supp) - 3) . ") ";
        } 
        $str = "
        select recid,trim(VEND_NAME) __mdata ,trim(VEND_CODE) VEND_CODE,VEND_ENABLED, concat(VEND_ADDR1,' ',VEND_ADDR2,' ',VEND_ADDR3) _address , concat(VEND_CPRSN) cont_prsn , concat(VEND_CPRSN_DESGN) cp_desig , concat(VEND_CPRSN_TELNO) cp_no 
        from {$this->db_erp}.mst_vendor where ({$str_supp}) AND (VEND_CODE like '%{$term}%' or VEND_NAME like '%{$term}%') order BY VEND_NAME ASC limit 15 "; 
        $q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if($q->getNumRows() > 0) { 
        	$rrec = $q->getResultArray();
        	foreach($rrec as $row):
        		$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
        		array_push($autoCompleteResult,array("value" => $row['__mdata'], 
        			"mtkn_rid" => $mtkn_rid,
        			"mtkn_vcode" => $row["VEND_CODE"],
        			"mtkn_vtag" => $row["VEND_ENABLED"], 
        			"_rid" => $row['recid'],
        			"_address" => $row["_address"], 
        			"cont_prsn" => $row["cont_prsn"] , "cp_desig" => $row["cp_desig"] , "cp_no" => $row["cp_no"]  ));
        	endforeach;
        }
        $q->freeResult();
        echo json_encode($autoCompleteResult);
	}  //end vendor_ua	
	
	public function companybranch_pout(){ 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		$aua_branch = $this->myusermod->ua_brnch($this->db_erp,$cuser);
		$str_branch = " a.`recid` = '__MEBRNCH__' ";
		if(count($aua_branch) > 0) { 
			$str_branch = "";
			for($xx = 0; $xx < count($aua_branch); $xx++) { 
				$mbranch = $aua_branch[$xx];
				$str_branch .= " a.`recid` = '$mbranch' or ";
            } //end for 
            $str_branch = "(" . substr($str_branch,0,strlen($str_branch) - 3) . ")";
        } 
        $mtkn_compid = $this->request->getVar('mtkn_compid');
        $term = $this->request->getVar('term');
       	//$terms = explode('XOX', $term);
        $autoCompleteResult = array();
        if(!empty($mtkn_compid)){
        	$str = "
        	SELECT a.`recid` Rbrnch,
        	a.`BRNCH_CODE`,
        	a.`BRNCH_NAME` __mdata,
        	b.`COMP_NAME`,
        	b.`recid` Rcomp
        	from {$this->db_erp}.mst_companyBranch a
        	LEFT JOIN {$this->db_erp}.mst_company b
        	ON (a.`COMP_CODE` = b.`COMP_CODE`)
        	where sha2(concat(b.`recid`,'{$mpw_tkn}'),384) = '{$mtkn_compid}' AND (a.`BRNCH_NAME` like '% - %') AND (a.`BRNCH_CODE` like '%{$term}%' or a.`BRNCH_NAME` like '%{$term}%') 
        	order by BRNCH_NAME limit 5 
        	";

        }
        else{
        	$str = "
        	SELECT a.`recid` Rbrnch,a.`BRNCH_CODE`,
        	a.`BRNCH_NAME` __mdata,
        	b.`COMP_NAME`
        	from {$this->db_erp}.mst_companyBranch a
        	LEFT JOIN {$this->db_erp}.mst_company b
        	ON (a.`COMP_CODE` = b.`COMP_CODE`)
        	where (a.`BRNCH_NAME` like '% - %') AND (a.`BRNCH_CODE` like '%{$term}%' or a.`BRNCH_NAME` like '%{$term}%') 
        	order by BRNCH_NAME limit 5 
        	";
        }
        
       	//var_dump($str);
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  // AND {$str_comp} 
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['BRNCH_CODE'] . $mpw_tkn);
				$mtknr_rid = hash('sha384', $row['Rbrnch'] . $mpw_tkn);  
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_rid" => $mtkn_rid,
					"mtknr_rid" => $mtknr_rid,
					"mtkn_brnch" => $row['Rbrnch'],
					"mtkn_comp" => $row['COMP_NAME']));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	}  //end companybranch_pout
	
	public function select_mo_items_rcv() {
		$cuser = $this->myusermod->mysys_user();
		$cuserlvl = $this->myusermod->mysys_userlvl();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$check_dr = $this->request->getVar('dr_no');
		$trxno = $this->request->getVar('trxno');
		$supp = $this->request->getVar('supp');
		$strno ='';
		$txt_mo_d = substr($check_dr, 0,3);
		$branch_id = $this->request->getVar('branch_id');
		//if($cuserlvl != 'S'){
			//echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You are not authorized for this module.!!!.</div>";
			//die();
		//}
		if(empty($supp)){
			echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Please select supplier.!!!.</div>";
			die();
		}
		if(!empty($trxno)){
			$strno = "AND !(sha2(concat(recid,'{$mpw_tkn}'),384) = '$trxno')";
		}
		if($txt_mo_d == "GRO") { 
			$str = "
			select trx_no,drno __mdata from {$this->db_erp}.trx_manrecs_hd where drno = '$check_dr' AND supplier_id ='$supp' AND df_tag= 'F'";
			$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			//var_dump($str);
			if($q->getNumRows() > 0) { 
				$rrec = $q->getRowArray();
				$trxno = $rrec['trx_no'];
				$drno = $rrec['__mdata'];
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> DR No ".$drno." Already in Final Tag in transaction number ".$trxno." !!!.</div>
				";
				die();
			}
			else{
				//PARTIAL
				$str = "
				select trx_no,drno __mdata from {$this->db_erp}.trx_manrecs_hd where drno = '$check_dr' AND supplier_id ='$supp' AND df_tag= 'D' AND flag= 'R'";
				$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				if($q->getNumRows() > 0) { 
					$rrec = $q->getRowArray();
					$trxno = $rrec['trx_no'];
					$drno = $rrec['__mdata'];
					$data = $this->mydatum->mo_select_items_rcv($trxno);
					$data['mmnhd_rid'] = '';
					$data['dis3']= '';
					$data['txtdrno'] = $check_dr;
					//$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs_mo',$data);
					echo view('transactions/dr/dr-trx-recs',$data);

				}
				//COMPLETE
				else{
					$data = $this->mydatum->mo_select_items_rcv();
					$data['mmnhd_rid'] = '';
					$data['dis3']= '';
					$data['txtdrno'] = $check_dr;
					//$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs_mo',$data);
					echo view('transactions/dr/dr-trx-recs',$data);
				}
				
			}
		}
		//NON GROCERY OR SMC OR DR OR SIXUN
		else {
			$str = "
			select trx_no,drno __mdata from {$this->db_erp}.trx_manrecs_hd where drno = '$check_dr' AND sha2(concat(supplier_id,'{$mpw_tkn}'),384) ='$supp' AND flag= 'R' {$strno}";
			$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			// var_dump($str);
			// 	die();
			if($q->getNumRows() > 0) { 
				$rrec = $q->getRowArray();
				$trxno = $rrec['trx_no'];
				$drno = $rrec['__mdata'];
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> DR No ".$drno." Already exist in transaction number ".$trxno." !!!.</div>
				";
				die();
			}
			else{
				$data = $this->mydatum->mo_select_items_rcv();
				$data['mmnhd_rid'] = '';
				$data['dis3']= '';
				$data['txtdrno'] = $check_dr;
				$data['branch_id'] = $branch_id;
				//echo view('masterdata/acct_mod/man_recs/myacct_manrecs_mo',$data);
				echo view('transactions/dr/dr-trx-recs',$data);
			}
		}
	}  //end select_mo_items_rcv	
	
	public function search_customer() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		//$term               = $this->mylibzdb->dbx->escapeString($this->request->getVar('term'));
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT
			aa.	recid,trim(aa.`CUST_NAME`) __mdata,  
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_rid 
		FROM {$this->db_erp}.`mst_customer` aa
		WHERE (aa.`CUST_CODE` like '%{$term}%' or aa.`CUST_NAME` LIKE '%{$term}%')  ORDER BY aa.`CUST_NAME`
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
					"mtkn_rid" => $mtkn_rid,
					));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	} //end search_customer
	
	//DR CHECKING
	public function drin_dr_checking(){
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$check_dr = $this->request->getVar('dr_no');
		$trxno = $this->request->getVar('trxno');
		$supp = $this->request->getVar('supp');
		if(empty($supp)){
			echo "<div><strong>Info.<br/></strong><strong>Status</strong> Please select supplier.!!!.</div>";
			die();
		}
		$strno ='';
		if(!empty($trxno)){
			$strno = "AND (sha2(concat(recid,'{$mpw_tkn}'),384) = '$trxno')";
		}
		$str = "
			select trx_no,drno __mdata from {$this->db_erp}.trx_manrecs_hd where drno = '$check_dr' AND sha2(concat(supplier_id,'{$mpw_tkn}'),384) ='$supp' AND flag= 'R' {$strno}";
			$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() > 0) { 
				$rrec = $q->getRowArray();
				$trxno = $rrec['trx_no'];
				$drno = $rrec['__mdata'];
				echo "<div><strong>Info.<br/></strong><strong>Status</strong> DR No ".$drno." Already exist in transaction number ".$trxno." !!!.</div>";
				die();
			}
			else{
				echo "<div><strong>Info.<br/></strong><strong>Status</strong> DR No is available.!!!.</div>";
			}
		$q->freeResult();
	} // end drin_dr_checking
	
	public function ho_mat_article() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$this->db_erp = $this->mydbname->medb(0);
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		//$comp_usr = $this->myusermod->ua_comp_code($this->db_erp,$cuser);
		$str = "
		select recid,ART_DESC,trim(ART_CODE) __mdata,
		ART_SKU,ART_SDU,ART_IMG,ART_NCBM,ART_NCONVF,ART_UPRICE,ART_UCOST,
		ART_BARCODE1,ART_HIERC3,ART_HIERC4,ART_UOM,
		sha2(concat(recid,'{$mpw_tkn}'),384) mtkn_prdltr 
		from {$this->db_erp}.`mst_article` where ART_ISDISABLE = 0 AND (ART_CODE like '%{$term}%' or ART_DESC like '%{$term}%' or ART_BARCODE1 like '%{$term}%') order BY ART_DESC limit 50 
		";
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
    } //end ho_mat_article
    	
	
    public function mat_article(){ 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		$filter  = $this->request->getVar('filter');
		$filter2 = $this->request->getVar('filter2');
		$ischck_mkg = $this->request->getVar('ischck_mkg');
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		$comp_usr = $this->myusermod->ua_comp_code($this->db_erp,$cuser);
		$str_comp='';
		$str_filter = '';
		$str_filter2 = '';
		if(count($comp_usr) > 0) { 
			$str_comp = "";
			for($xx = 0; $xx < count($comp_usr); $xx++) { 
				$mart_comp = $comp_usr[$xx];
				$str_comp .= "SUBSTR(ART_COMP,1,INSTR(ART_COMP,'~')-1)= '$mart_comp' or ";
	            } //end for 
	            $str_comp = "and (" . substr($str_comp,0,strlen($str_comp) - 3) . ")";

	        }
	        $fld_pbranch = $this->request->getVar('pbranchid');//GET id
	        $str_branch ="";
	        $BRNCH_MAT_FLAG ='';
	        if(!empty($fld_pbranch)){
	        	$str = "select recid,BRNCH_NAME,BRNCH_CODE,BRNCH_OCODE2,BRNCH_MAT_FLAG
	        	from {$this->db_erp}.`mst_companyBranch` aa where `recid` = '$fld_pbranch'";
	        	$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	        	$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	        	
	        	$rw = $q->getRowArray();
	        	$BRNCH_MAT_FLAG = $rw['BRNCH_MAT_FLAG'];
	        	$fld_branch_recid = $rw['recid'];
	        	$str_branch ="AND kk.`brnchID` = '$fld_branch_recid' ";
	        	
	        	
	        	$q->freeResult();
				//END BRANCH
	        }
	                //if filter id not empty
	        if(!empty($filter)):
	        	$str_filter = "AND ART_HIERC2 = '{$filter}'";
	        endif;
	        		//if filter id not empty
	        if(!empty($filter2)):
	        	$str_filter2 = "AND ART_DESC_CODE = '{$filter2}'";
	        endif;
	        
	        if($ischck_mkg == 'Y'){
	        	$str_mkg = "AND ART_CODE like 'MKG%'";
	        }
	        elseif($ischck_mkg == 'N'){
	        	$str_mkg = "AND !(ART_CODE like 'MKG%')";
	        }
	        else{
	        	$str_mkg = "";
	        }
	        $result = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuamd_id='145'","myua_md");
	        if($result == 1){
	        	$str = "
	        	select recid,ART_DESC,trim(ART_CODE) __mdata,
	        	ART_SKU,ART_SDU,ART_IMG,ART_NCBM,ART_NCONVF,ART_UPRICE,ART_UCOST,
	        	ART_BARCODE1,ART_HIERC3,ART_HIERC4,ART_UOM,
	        	sha2(concat(recid,'{$mpw_tkn}'),384) mtkn_prdltr 
	        	from {$this->db_erp}.`mst_article` where ART_ISDISABLE = '0' AND (ART_HIERC1 = 'TSHIRT' OR ART_HIERC1 = 'PANTS') AND ART_HIERC2 = 'CLOTHING' AND (ART_CODE like '%{$term}%' or ART_DESC like '%{$term}%' or ART_BARCODE1 like '%{$term}%') order BY ART_DESC limit 50 
	        	";
	        }
	        elseif($BRNCH_MAT_FLAG == 'G'){
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
	        	sha2(concat(a.recid,'{$mpw_tkn}'),384) mtkn_prdltr 
	        	from {$this->db_erp}.`mst_article`  a
	        	LEFT JOIN `mst_article_per_branch` kk
	        	ON (a.`recid` = kk.`artID`)
	        	where a.ART_ISDISABLE = '0' {$str_branch} AND (a.ART_CODE like '%$term%' or a.ART_DESC like '%$term%' or a.ART_BARCODE1 like '%$term%') order BY a.ART_DESC limit 50 
	        	";
	        }
	        else{
	        	$str = "
	        	select recid,ART_DESC,trim(ART_CODE) __mdata,
	        	ART_SKU,ART_SDU,ART_IMG,ART_NCBM,ART_NCONVF,ART_UPRICE,ART_UCOST,
	        	ART_BARCODE1,ART_HIERC3,ART_HIERC4,ART_UOM,
	        	sha2(concat(recid,'{$mpw_tkn}'),384) mtkn_prdltr 
	        	from {$this->db_erp}.`mst_article` where ART_ISDISABLE = '0' AND (ART_CODE like '%{$term}%' or ART_DESC like '%{$term}%' or ART_BARCODE1 like '%{$term}%') {$str_mkg} {$str_filter} {$str_filter2} order BY ART_DESC limit 50 
	        	";
	        }			
	        $q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	        if($q->getNumRows() > 0) { 
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
    } //end mat_article
	
	
	/* ------ */
	
	public function proc_quota_rate() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		//$term               = $this->mylibzdb->dbx->escapeString($this->request->getVar('term'));
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT
			aa.	recid,trim(aa.`PROD_SUB_OPERATION_PROCESS`) __mdata,  
			concat(aa.`PRODL_SERVICES`,'->',aa.`PROD_OPERATION`,'->',aa.`PROD_DESGNT`,'->',aa.`PROD_SUB_OPERATION`,'->',`PROD_SUB_OPERATION_PROCESS`,'->',`PROD_SOP_RATE_AMT`) mehierc,
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_rid 
		FROM {$this->db_erp}.`mst_process_rate_amnt` aa
		WHERE (aa.`PROD_SUB_OPERATION_PROCESS` like '%{$term}%')  ORDER BY aa.`PROD_SUB_OPERATION_PROCESS`
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
				array_push($autoCompleteResult,array(
					"mdata" => $row['__mdata'], 
					"value" => $row['mehierc'], 
					"mtkn_rid" => $mtkn_rid,
					"mehierc" => $row['mehierc'],
					));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	} //end proc_quota_rate
	
	public function prod_items() {
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		//$term               = $this->mylibzdb->dbx->escapeString($this->request->getVar('term'));
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT
			aa.	recid,trim(aa.`ART_CODE`) __mdata,  
			aa.`ART_DESC`,
			aa.`ART_UPRICE`,
			aa.`ART_UCOST`,
			concat(aa.`ART_HIERC1`,'->',aa.`ART_HIERC2`) mehierc,
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_rid 
		FROM {$this->db_erp}.`mst_article` aa 
		WHERE (aa.`ART_CODE` like '%{$term}%' or aa.`ART_DESC` like '%{$term}%')  ORDER BY aa.`ART_DESC`
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
					"mtkn_rid" => $mtkn_rid,
					"mehierc" => $row['mehierc'],
					"proddesc" => $row['ART_DESC'],
					"prodprice" => $row['ART_UPRICE'],
					"prodcost" => $row['ART_UCOST'],
					));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	} //end prod_items
	
	public function prod_type() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		//$term               = $this->mylibzdb->dbx->escapeString($this->request->getVar('term'));
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT
			trim(aa.`ART_HIERC2`) __mdata 
		FROM {$this->db_erp}.`mst_article` aa 
		WHERE (aa.`ART_HIERC2` like '%{$term}%') GROUP BY aa.`ART_HIERC2` ORDER BY aa.`ART_HIERC2` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end prod_type
	
	public function prod_category() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT
			trim(aa.`ART_HIERC1`) __mdata 
		FROM {$this->db_erp}.`mst_article` aa 
		WHERE (aa.`ART_HIERC1` like '%{$term}%') GROUP BY aa.`ART_HIERC1` ORDER BY aa.`ART_HIERC1` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end prod_category

	public function prod_sub_category() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT
			trim(aa.`ART_HIERC3`) __mdata 
		FROM {$this->db_erp}.`mst_article` aa 
		WHERE (aa.`ART_HIERC3` like '%{$term}%') and !(trim(aa.`ART_HIERC3`) = '') GROUP BY aa.`ART_HIERC3` ORDER BY aa.`ART_HIERC3` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end prod_category
	
	public function prod_items_uom() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT
			trim(aa.`ART_UOM`) __mdata 
		FROM {$this->db_erp}.`mst_article` aa 
		WHERE (aa.`ART_UOM` like '%{$term}%') and !(trim(aa.`ART_UOM`) = '') GROUP BY aa.`ART_UOM` ORDER BY aa.`ART_UOM` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	} //end prod_items_uom
	
	public function prod_items_packaging() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT
			trim(aa.`ART_SKU`) __mdata 
		FROM {$this->db_erp}.`mst_article` aa 
		WHERE (aa.`ART_SKU` like '%{$term}%') and !(trim(aa.`ART_SKU`) = '') GROUP BY aa.`ART_SKU` ORDER BY aa.`ART_SKU` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	} //end prod_items_packaging
	
	public function qpr_prod_services() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT distinct 
			trim(aa.`PRODL_SERVICES`) __mdata 
		FROM {$this->db_erp}.`mst_process_rate_amnt` aa 
		WHERE (aa.`PRODL_SERVICES` like '%{$term}%') and !((aa.`PRODL_SERVICES`) = '') ORDER BY aa.`PRODL_SERVICES` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);		
	} //end qpr_prod_services
	
	public function qpr_prod_operation() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT distinct 
			trim(aa.`PROD_OPERATION`) __mdata 
		FROM {$this->db_erp}.`mst_process_rate_amnt` aa 
		WHERE (aa.`PROD_OPERATION` like '%{$term}%') and !((aa.`PROD_OPERATION`) = '') ORDER BY aa.`PROD_OPERATION` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);		
	} //end qpr_prod_operation
	
	public function qpr_prod_design_pattern() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT distinct 
			trim(aa.`PROD_DESGNT`) __mdata 
		FROM {$this->db_erp}.`mst_process_rate_amnt` aa 
		WHERE (aa.`PROD_DESGNT` like '%{$term}%') and !((aa.`PROD_DESGNT`) = '') ORDER BY aa.`PROD_DESGNT` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);		
	} //end qpr_prod_design_pattern	

	public function qpr_prod_sub_operation() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT distinct 
			trim(aa.`PROD_SUB_OPERATION`) __mdata 
		FROM {$this->db_erp}.`mst_process_rate_amnt` aa 
		WHERE (aa.`PROD_SUB_OPERATION` like '%{$term}%') and !((aa.`PROD_SUB_OPERATION`) = '') ORDER BY aa.`PROD_SUB_OPERATION` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);		
	} //end qpr_prod_sub_operation
	
	
	public function qpr_prod_processes() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$term               = $this->mylibzdb->dbx->escapeString(urldecode($this->request->getVar('term')));
		$autoCompleteResult = array();
		$str = "
		SELECT distinct 
			trim(aa.`PROD_SUB_OPERATION_PROCESS`) __mdata 
		FROM {$this->db_erp}.`mst_process_rate_amnt` aa 
		WHERE (aa.`PROD_SUB_OPERATION_PROCESS` like '%{$term}%') and !((aa.`PROD_SUB_OPERATION_PROCESS`) = '') and PROD_RFLAG = 'A' ORDER BY aa.`PROD_SUB_OPERATION_PROCESS` 
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);		
	} //end qpr_prod_sub_operation	


} //end main class
