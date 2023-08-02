<?php namespace App\Controllers;
  
use CodeIgniter\Controller;

class My_search extends BaseController
{
    public function __construct()
    {
        
    }
  
    public function index()
    {
        //helper(['form']);
        //echo view('login');
        echo view('sign-in');
    } 

    public function company() { 
        $this->db_erp = $this->mydbname->medb(0);
        $autoCompleteResult = array();
        $term = $this->request->getVar('term');
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $str = "SELECT aa.`recid`, TRIM(aa.`COMP_NAME`) AS __mdata, aa.`COMP_CODE` AS _compcode FROM {$this->db_erp}.`mst_company` AS aa 
        LEFT JOIN {$this->db_erp}.`mst_companyBranch` AS bb ON aa.`recid` = bb.`COMP_ID` WHERE (`COMP_NAME` LIKE '%{$term}%')  GROUP BY `COMP_NAME` ORDER BY `COMP_NAME` LIMIT 50";            
        $q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if($q->resultID->num_rows > 0) { 
            $rrec = $q->getResultArray();
            foreach($rrec as $row):
                $mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
                array_push($autoCompleteResult,array("value" => $row['__mdata'], 
                    "mtkn_rid" => $mtkn_rid,
                    "_compcode" => $row["_compcode"]));
                
            endforeach;
        }
        $q->freeResult();
        echo json_encode($autoCompleteResult);    

    }  //end company

	public function gj_entyp() { 
        $this->db_erp = $this->mydbname->medb(0);
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();

        $term = $this->request->getVar('term');
		$autoCompleteResult = array();

		$str = "SELECT `recid`, `entyp_desc` AS __mdata FROM {$this->db_erp}.`mst_gj_entyp` WHERE (`entyp_desc` like '%{$term}%') GROUP BY `entyp_desc` ORDER BY `recid` LIMIT 10";			
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):

				array_push($autoCompleteResult,array(
					"value" => $row['__mdata'], 
					"mtkn_rid" => $row['recid']
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	} //end gj_entyp

	public function vendor1() { 
        $this->db_erp = $this->mydbname->medb(0);
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$term = $this->mylibzdb->dbx->escapeString(trim($this->request->getVar('term')));
		
		$autoCompleteResult = array();

		$str = "
		select recid,VEND_CODE,trim(VEND_NAME) __mdata ,  concat(VEND_ADDR1,' ',VEND_ADDR2,' ',VEND_ADDR3) _address , concat(VEND_CPRSN) cont_prsn , concat(VEND_CPRSN_DESGN) cp_desig , concat(VEND_CPRSN_TELNO) cp_no , VEND_TINNO
		from {$this->db_erp}.mst_vendor where (VEND_CODE like '%{$term}%' or VEND_NAME like '%{$term}%') order BY VEND_NAME ASC limit 25 ";			
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_rid" => $mtkn_rid,
					"_address" => $row["_address"], 
					"__vend_code" => $row['VEND_CODE'],
					"__vend_tinno" => $row['VEND_TINNO'],
					"cont_prsn" => $row["cont_prsn"] , "cp_desig" => $row["cp_desig"] , "cp_no" => $row["cp_no"]  ));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end vendor1

	public function glparticularAcct_v() { 
        $this->db_erp = $this->mydbname->medb(0);
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
        $term = $this->mylibzdb->dbx->escapeString(trim($this->request->getVar('term')));
		$autoCompleteResult = array();

		$str = "
		select recid,trim(GLPA_DESC) __mdata, GLPA_CODE _glpacode  
		from {$this->db_erp}.mst_GL_ParticularAcct where (GLP_CODE like '%{$term}%') OR (GLPA_CODE like '%{$term}%') or (GLPA_DESC like '%{$term}%') order BY GLPA_DESC limit 50 
		";			$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['_glpacode'] . $mpw_tkn); 
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_rid" => $mtkn_rid,
					"_glpadesc" => $row["__mdata"],
					"_glpacode" => $row["_glpacode"] ));

			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end glparticularAcct_v

	public function companybranch_v(){ 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$this->db_erp = $this->mydbname->medb(0);
		
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
        	b.`recid` Rcomp
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
        	b.`COMP_NAME`,b.recid m_comprid 
        	from {$this->db_erp}.mst_companyBranch a
        	LEFT JOIN {$this->db_erp}.mst_company b
        	ON (a.`COMP_CODE` = b.`COMP_CODE`)
        	where {$str_branch} {$str_grparea} AND (a.`BRNCH_CODE` like '%{$term}%' or a.`BRNCH_NAME` like '%{$term}%') 
        	order by BRNCH_NAME limit 15 
        	";
        }
        

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getresultArray();
			foreach($rrec as $row):
				$mtkn_rid = hash('sha384', $row['BRNCH_CODE'] . $mpw_tkn);
				$mtknr_rid = hash('sha384', $row['Rbrnch'] . $mpw_tkn); 
				$mtkn_compid = hash('sha384', $row['m_comprid'] . $mpw_tkn); 
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_rid" => $mtkn_rid,
					"mtknr_rid" => $mtknr_rid,
					"mtkn_brnch" => $row['Rbrnch'],
					"mtkn_comp" => $row['COMP_NAME'],
					"mtkn_comp_rid" => $mtkn_compid
					));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	}//end companybranch_v


	public function mat_article() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$this->db_erp = $this->mydbname->medb(0);
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
        	//$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

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
      //  if(!empty($str_comp)){
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
        	ON (a.`recid` = kk.`artID` {$str_branch})
        	where a.ART_ISDISABLE = '0' AND (a.ART_CODE like '%$term%' or a.ART_DESC like '%$term%' or a.ART_BARCODE1 like '%$term%') order BY a.ART_DESC limit 50 
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
      //  }
    } //end mat_article

	public function companybranch_tap(){ 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$this->db_erp = $this->mydbname->medb(0);
		
		$term = $this->request->getVar('term');
       	//$terms = explode('XOX', $term);
		$autoCompleteResult = array();

		$str = "
		SELECT a.`recid` Rbrnch,a.`BRNCH_CODE`,
		a.`BRNCH_NAME` __mdata,
		b.`COMP_NAME`
		from {$this->db_erp}.mst_companyBranch a
		LEFT JOIN {$this->db_erp}.mst_company b
		ON (a.`COMP_CODE` = b.`COMP_CODE`)
		where (a.`BRNCH_CODE` like '%GWEMC%') AND (a.`BRNCH_CODE` like '%{$term}%' or a.`BRNCH_NAME` like '%{$term}%') 
		order by BRNCH_NAME limit 5 
		";


       	//var_dump($str);
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->resultID->num_rows > 0) { 
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
		
	} //end companybranch_tap

	public function mat_art_section2(){ 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$this->db_erp = $this->mydbname->medb(0);
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		
		$str = "
		SELECT
		`MAT_CATG_DESC_CODE` __mdata
		FROM {$this->db_erp}.`mst_mat_catg4_hd_new`
		WHERE
		(`MAT_CATG_DESC_CODE` like '%{$term}%')
		GROUP BY `MAT_CATG_DESC_CODE`
		ORDER BY `MAT_CATG_DESC_CODE` limit 50 
		";			
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array(
					"value" => $row['__mdata']
				));
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end mat_art_section2
  
	public function mat_cg4() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$this->db_erp = $this->mydbname->medb(0);
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		$str = "
		select `recid`,concat(`MAT_CATG4_CODE`,'=>',`MAT_CATG4_DESC`) __mdata from {$this->db_erp}.`mst_mat_catg4_hd_new` where ((`MAT_CATG4_CODE` like '%{$term}%') OR (`MAT_CATG4_DESC` like '%{$term}%')) order by `MAT_CATG4_CODE` limit 25 
		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				//$mtkn_rid = hash('sha384', $row['COMP_CODE'] . $mpw_tkn); 
				$mtkn_recid = hash('sha384', $row['recid'] . $mpw_tkn);
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_recid" => $mtkn_recid ));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end mat_cg4

	

	public function mat_cg1() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$this->db_erp = $this->mydbname->medb(0);
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		$str = "
		select `recid`,concat(`MAT_CATG1_CODE`,'=>',`MAT_CATG1_DESC`) __mdata from {$this->db_erp}.`mst_mat_catg4_hd_new` where ((`MAT_CATG1_CODE` like '%{$term}%') OR (`MAT_CATG1_DESC` like '%{$term}%')) group by MAT_CATG1_CODE,MAT_CATG1_DESC  order by `MAT_CATG1_CODE` limit 25 
		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				//$mtkn_rid = hash('sha384', $row['COMP_CODE'] . $mpw_tkn); 
				$mtkn_recid = hash('sha384', $row['recid'] . $mpw_tkn);
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_recid" => $mtkn_recid ));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end mat_cg1

	public function mat_cg2() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$this->db_erp = $this->mydbname->medb(0);
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		$str = "
		select `recid`,concat(`MAT_CATG2_CODE`,'=>',`MAT_CATG2_DESC`) __mdata from {$this->db_erp}.`mst_mat_catg4_hd_new` where ((`MAT_CATG2_CODE` like '%{$term}%') OR (`MAT_CATG2_DESC` like '%{$term}%')) group by MAT_CATG2_CODE,MAT_CATG2_DESC  order by `MAT_CATG2_CODE` limit 25 
		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				//$mtkn_rid = hash('sha384', $row['COMP_CODE'] . $mpw_tkn); 
				$mtkn_recid = hash('sha384', $row['recid'] . $mpw_tkn);
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_recid" => $mtkn_recid ));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end mat_cg2

	public function mat_cg3() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$this->db_erp = $this->mydbname->medb(0);
		$term = $this->request->getVar('term');
		$autoCompleteResult = array();
		$str = "
		select `recid`,concat(`MAT_CATG3_CODE`,'=>',`MAT_CATG3_DESC`) __mdata from {$this->db_erp}.`mst_mat_catg4_hd_new` where ((`MAT_CATG3_CODE` like '%{$term}%') OR (`MAT_CATG3_DESC` like '%{$term}%')) group by MAT_CATG3_CODE,MAT_CATG3_DESC  order by `MAT_CATG3_CODE` limit 25 
		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->resultID->num_rows > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				//$mtkn_rid = hash('sha384', $row['COMP_CODE'] . $mpw_tkn); 
				$mtkn_recid = hash('sha384', $row['recid'] . $mpw_tkn);
				array_push($autoCompleteResult,array("value" => $row['__mdata'], 
					"mtkn_recid" => $mtkn_recid ));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
	}  //end mat_cg3

} //end main My_search
