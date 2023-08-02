<?php
namespace App\Models;
use CodeIgniter\Model;
class MyMDArticleModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->mylibzsys = model('App\Models\MyLibzSysModel');
		$this->myusermod = model('App\Models\MyUserModel');
		$this->db_erp = $this->myusermod->mydbname->medb(0);
		$this->db_temp = $this->myusermod->mydbname->medb(2);
		$this->cuser = $this->myusermod->mysys_user();
		$this->mpw_tkn = $this->myusermod->mpw_tkn();
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
			$str_optn = " where (ART_CODE like '%$msearchrec%' or ART_DESC like '%$msearchrec%' or 
			ART_BARCODE1 like '%$msearchrec%') ";
		}
		
		$strqry = "
		select aa.*,
		IF(aa.`ART_ISDISABLE` = 1, 'Inactive','Active') _ART_ISDISABLE,
		sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`mst_article` aa {$str_optn} 
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
		$meprodlc = $this->dbx->escapeString($this->request->getVar('meprodlc'));
		$mematcode = $this->dbx->escapeString($this->request->getVar('mematcode'));
		$mebarcode = $this->dbx->escapeString($this->request->getVar('mebarcode'));
		$mematdesc = $this->dbx->escapeString($this->request->getVar('mematdesc'));
		$mepartnumber = $this->dbx->escapeString($this->request->getVar('mepartnumber'));
		$flexSwitchCheckArtRecActive = $this->request->getVar('flexSwitchCheckArtRecActive');
		$meprodt = $this->dbx->escapeString($this->request->getVar('meprodt'));
		$meprodcat = $this->dbx->escapeString($this->request->getVar('meprodcat'));
		$meprodscat = $this->dbx->escapeString($this->request->getVar('meprodscat'));
		$meunitc = (empty($this->request->getVar('meunitc')) ? 0 : ($this->request->getVar('meunitc') + 0));
		$meunitp = (empty($this->request->getVar('meunitp')) ? 0 : ($this->request->getVar('meunitp') + 0));
		$meunitpack = $this->request->getVar('meunitpack');
		$meuom = $this->request->getVar('meuom');
		$megweight = (empty($this->request->getVar('megweight')) ? 0 : ($this->request->getVar('megweight') + 0));
		$meconvf = (empty($this->request->getVar('meconvf')) ? 0 : ($this->request->getVar('meconvf') + 0));
		
		//updating of records
		if(!empty($mtkn_etr)) { 
			$str = "select recid,ART_CODE from {$this->db_erp}.`mst_article` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				if($mematcode == $rw['ART_CODE']) { 
					$adataz = array();
					$adataz[] = "ART_DESCxOx'{$mematdesc}'";
					$adataz[] = "ART_PARTNOxOx'{$mepartnumber}'";
					$adataz[] = "ART_HIERC1xOx'{$meprodcat}'";
					$adataz[] = "ART_HIERC2xOx'{$meprodt}'";
					$adataz[] = "ART_HIERC3xOx'{$meprodscat}'";
					$adataz[] = "ART_SKUxOx'{$meunitpack}'";
					$adataz[] = "ART_UOMxOx'{$meuom}'";
					$adataz[] = "ART_BARCODE1xOx'{$mebarcode}'";
					$adataz[] = "ART_ISDISABLExOx'{$flexSwitchCheckArtRecActive}'";
					$adataz[] = "ART_PRODLxOx'{$meprodlc}'";
					$adataz[] = "ART_NCONVFxOx'{$meconvf}'";
					$adataz[] = "ART_GWEIHGTxOx'{$megweight}'";
					$adataz[] = "ART_UPPRICExOx'{$meunitp}'";
					$adataz[] = "ART_UCOSTxOx'{$meunitc}'";
					$str = " recid = {$rw['recid']} ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_article`','MATITEM_UREC',$mematcode,$str);
					$str = "update {$this->db_erp}.`mst_article` set ART_DESC = '$mematdesc',
					`ART_PARTNO` = '$mepartnumber',
					`ART_HIERC1` = '$meprodcat',
					`ART_HIERC2` = '$meprodt',
					`ART_HIERC3` = '$meprodscat',
					`ART_SKU` = '$meunitpack',
					`ART_UOM` = '$meuom',
					`ART_BARCODE1` = '$mebarcode',
					`ART_ISDISABLE` = '$flexSwitchCheckArtRecActive',
					`ART_PRODL` = '$meprodlc',
					`ART_NCONVF` = '$meconvf',
					`ART_GWEIHGT` = '$megweight',
					`ART_UPPRICE` = '$meunitp',
					`ART_UCOST` = '$meunitc'
					where recid = {$rw['recid']} ";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MATITEM_UREC',$mematcode,$mematcode,$str,'');
					echo "Changes successfuly done!!!";
				} else { 
					echo "Material Code conflict for update!!!";
				}
			} 
		} else { 
			//adding of records
			$str = "select recid,ART_CODE from {$this->db_erp}.`mst_article` aa where `ART_CODE` = '$mematcode'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				echo "Material Code already EXISTS!!!";
				die();
			} else { 
				if($maction == 'A_REC') { 
					$adataz = array();
					$adataz[] = "ART_CODExOx'{$mematcode}'";
					$adataz[] = "ART_DESCxOx'{$mematdesc}'";
					$adataz[] = "ART_PARTNOxOx'{$mepartnumber}'";
					$adataz[] = "ART_HIERC1xOx'{$meprodcat}'";
					$adataz[] = "ART_HIERC2xOx'{$meprodt}'";
					$adataz[] = "ART_HIERC3xOx'{$meprodscat}'";
					$adataz[] = "ART_SKUxOx'{$meunitpack}'";
					$adataz[] = "ART_UOMxOx'{$meuom}'";
					$adataz[] = "ART_BARCODE1xOx'{$mebarcode}'";
					$adataz[] = "ART_ISDISABLExOx'{$flexSwitchCheckArtRecActive}'";
					$adataz[] = "ART_PRODLxOx'{$meprodlc}'";
					$adataz[] = "ART_NCONVFxOx'{$meconvf}'";
					$adataz[] = "ART_GWEIHGTxOx'{$megweight}'";
					$adataz[] = "ART_UPPRICExOx'{$meunitp}'";
					$adataz[] = "ART_UCOSTxOx'{$meunitc}'";
					$str = " ART_CODE = '$mematcode' ";
					$this->mylibzdb->logs_modi_audit($adataz,$this->db_erp,'`mst_article`','MATITEM_AREC',$mematcode,$str);
					$str = "
					insert into {$this->db_erp}.`mst_article` (
					`ART_CODE`,`ART_DESC`,`ART_HIERC1`,`ART_HIERC2`,`ART_HIERC3`,`ART_PRODL`,
					`ART_SKU`,`ART_UOM`,`ART_BARCODE1`,`ART_ISDISABLE`,`ART_NCONVF`,`ART_PARTNO`,
					`ART_GWEIHGT`,`ART_UPPRICE`,`ART_UCOST`,`MUSER`,`ENCD`
					) values (
					'$mematcode','$mematdesc','$meprodcat','$meprodt','$meprodscat','$meprodlc',
					'$meunitpack','$meuom','$mebarcode','$flexSwitchCheckArtRecActive','$meconvf','$mepartnumber',
					'$megweight','$meunitp','$meunitc','$cuser',now()
					)
					";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MATITEM_AREC',$mematcode,$mematcode,$str,'');
					echo "Records successfuly added!!!";
				} else { //end A_REC validation 
					echo "INVALID OPERATION!!!";
				}
			}
		} //end mtkn_etr validation 
		
	} //end profile_save
	
	public function md_dload() { 
		
	} //end md_dload
	
	public function Artm_Branches() { 
		$adata = array();
		$adata[] = "DEPSTORExOxDEPARTMENT STORE";
		$str = "select `recid`,`BRNCH_MBCODE`,`BRNCH_NAME`,concat('E',trim(`BRNCH_OCODE2`)) B_CODE,`BRNCH_OCODE2` from {$this->db_erp}.mst_companyBranch where `BRNCH_MAT_FLAG` = 'G' order by `BRNCH_NAME`";
		$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__  . chr(13) . chr(10) . 'User: ' . $this->cuser);
		foreach($q->getResultArray() as $rw):
			$adata[] = $rw['B_CODE'] . 'xOx' . $rw['BRNCH_NAME'];
		endforeach;
		$q->freeResult();
		return $adata;
	} //end Artm_Branches
	
	public function Artm_Branch_recs($npages = 1,$npagelimit = 30,$msearchrec='') { 
		$data = array();
		$cuser = $this->cuser;
		$mebcode = $this->myusermod->request->getVar('mebcode');
		$data = array();
		$strqry = '';
		$str_optn = "";
		if (!empty($msearchrec)): 
			$msearchrec = $this->myusermod->mylibzdb->me_escapeString($msearchrec);
			$str_optn = " and (aa.`ART_CODE` = '$msearchrec'  or aa.`ART_BARCODE1` = '$msearchrec' or  aa.`ART_DESC` like '%{$msearchrec}%') ";
		endif;
		$lperbr = 0;
		if(!empty($mebcode) && $mebcode !== 'DEPSTORE') { 
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG,BRNCH_MBCODE   
			from {$this->db_erp}.`mst_companyBranch` aa where concat('E',trim(BRNCH_OCODE2)) = '$mebcode'";
			$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->myusermod->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_DTL_GEN','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$B_MBCODE = $rw['BRNCH_MBCODE'];
			$br_id = $rw['recid'];
			$br_ocode2 = $rw['B_OCODE2'];
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
			//END BRANCH
		} elseif ($mebcode == 'DEPSTORE') { 
			//set default for all deptstore since dept store branches are being handled by one master data 
			$B_MBCODE = 'E0023';
		} else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Branch is REQUIRED!!!</div>";
			die();
		} // end if	
		
		if($lperbr) { 
			$strqry = "
			SELECT '{$B_MBCODE}' `Branch Code`,
			        REPLACE(REPLACE(REPLACE(aa.`ART_CODE`,'\t',''),'\n',''),'\r','') `Article Code`,
			        REPLACE(REPLACE(REPLACE(aa.`ART_DESC`,'\t',''),'\n',''),'\r','') `Product Description`,
			        SUBSTR(REPLACE(REPLACE(REPLACE(aa.`ART_DESC`,'\t',''),'\n',''),'\r',''),1,20) `Short Description`,
			        0 __IN_STOCK,
			        REPLACE(REPLACE(REPLACE(aa.`ART_BARCODE1`,'\t',''),'\n',''),'\r','') `Barcode`,
			        REPLACE(REPLACE(REPLACE(aa.`ART_UOM`,'\t',''),'\n',''),'\r','') ART_UOM,
			        aa.`ART_NCONVF`,
			        IFNULL(kk.`art_cost`,0) `Cost`,
			        REPLACE(REPLACE(REPLACE(IFNULL(gg.`MAT_CATG1_DESC`,''),'\t',''),'\n',''),'\r','') `PRODUCT CATEGORY`,
			        REPLACE(REPLACE(REPLACE(IFNULL(hh.`MAT_CATG2_DESC`,''),'\t',''),'\n',''),'\r','') `PRODUCT GROUP`,
			        REPLACE(REPLACE(REPLACE(IFNULL(ii.`MAT_CATG3_DESC`,''),'\t',''),'\n',''),'\r','') `PRODUCT CLASS`,
			        REPLACE(REPLACE(REPLACE(IFNULL(jj.`MAT_CATG4_DESC`,''),'\t',''),'\n',''),'\r','') `PRODUCT SUB GROUP`,
					IF(gg.`MAT_CATG1_CODE` = '0600','Grocery Store','Department Store') `PRODUCT TYPE`,
					IF((aa.`ART_CODE` LIKE '%SPROMO%' OR aa.`ART_CODE` LIKE '%QDAMAGE%'),0,0) __DMG,
			        IFNULL(kk.`art_uprice`,0) `SRP`,
			        0 `POS SRP`,
			        '1' __B_VATABLE,
			        IF(gg.`MAT_CATG1_CODE` = '0600',0,IF((aa.`ART_CODE` LIKE '%SPROMO%' OR aa.`ART_CODE` LIKE '%QDAMAGE%'),0,1)) __MEMB,
			        IFNULL(mm.`B_PWDDISC`,0) __B_PWDDISC,
			        IFNULL(mm.`B_PWDDISC_VAL`,0) __B_PWDDISC_VAL,
			        IFNULL(dd.`B_SCDISC`,0) __B_SCDISC,
			        IFNULL(dd.`B_SCDISC_VAL`,0) __B_SCDISC_VAL,
			        IFNULL(ee.`B_SSPT`,0) __B_SSPT,
			        IFNULL(cc.`B_PNTS_ALLWD`,1) __B_PNTS_ALLWD,
			        IFNULL(aa.`ART_PCODE`,'SRP') __PRICING_CODE,
			        IFNULL(aa.`ART_ISDISABLE`,0) __ART_ISDISABLE,
			        IFNULL(nn.`MPROD_ID`,0) POS_MPRODID 
			        FROM {$this->db_erp}.`mst_article` aa 
			        JOIN {$this->db_erp}.`mst_article_per_branch` kk
			        ON (aa.`recid` = kk.`artID`) 
			        JOIN {$this->db_erp}.`mst_companyBranch` xx 
			        ON (kk.`brnchID` = xx.`recid`) 
			        LEFT JOIN {$this->db_erp}.`mst_article_ptsallwd` cc
			        ON (aa.`ART_CODE`=cc.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_article_scdisc` dd
			        ON (aa.`ART_CODE`=dd.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_article_sspt` ee
			        ON (aa.`ART_CODE`=ee.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_article_vatable` ff
			        ON (aa.`ART_CODE`=ff.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_article_pwddisc` mm
			        ON (aa.`ART_CODE`=mm.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_mat_catg1_hd` gg
				ON (aa.`ART_HIERC1`=gg.`MAT_CATG1_CODE`)
				LEFT JOIN {$this->db_erp}.`mst_mat_catg2_hd` hh
				ON (aa.`ART_HIERC2`=hh.`MAT_CATG2_CODE`)
				LEFT JOIN {$this->db_erp}.`mst_mat_catg3_hd` ii
				ON (aa.`ART_HIERC3`=ii.`MAT_CATG3_CODE`)
				LEFT JOIN {$this->db_erp}.`mst_mat_catg4_hd` jj
				ON (aa.`ART_HIERC4`=jj.`MAT_CATG4_CODE`) 
				left join {$this->db_erp}.`mst_pos_prod_ids` nn on(aa.ART_CODE = nn.ART_CODE) 
				WHERE aa.`ART_HIERC1` = '0600' AND kk.brnchID = {$br_id}  
				and !(trim(aa.ART_DESC) = '' or aa.ART_DESC is null or trim(aa.ART_BARCODE1) = '' or aa.ART_BARCODE1 is null or trim(aa.ART_BARCODE1) = '0' ) 
				and (aa.ART_POS_PROD_ID != 0 OR aa.ART_POS_PROD_ID IS NOT NULL) {$str_optn}
				GROUP BY kk.`recid` 
			";
		} else { 
			$strqry = "
			SELECT 'E0023' `Branch Code`,
			        REPLACE(REPLACE(REPLACE(aa.`ART_CODE`,'\t',''),'\n',''),'\r','') `Article Code`,
			        REPLACE(REPLACE(REPLACE(aa.`ART_DESC`,'\t',''),'\n',''),'\r','') `Product Description`,
			        SUBSTR(REPLACE(REPLACE(REPLACE(aa.`ART_DESC`,'\t',''),'\n',''),'\r',''),1,20) `Short Description`,
			        0 __IN_STOCK,
			        REPLACE(REPLACE(REPLACE(aa.`ART_BARCODE1`,'\t',''),'\n',''),'\r','') `Barcode`,
			        REPLACE(REPLACE(REPLACE(aa.`ART_UOM`,'\t',''),'\n',''),'\r','') ART_UOM,
			        aa.`ART_NCONVF`,
			        aa.`ART_UCOST` `Cost`,
			        REPLACE(REPLACE(REPLACE(IFNULL(gg.`MAT_CATG1_DESC`,''),'\t',''),'\n',''),'\r','') `PRODUCT CATEGORY`,
			        REPLACE(REPLACE(REPLACE(IFNULL(hh.`MAT_CATG2_DESC`,''),'\t',''),'\n',''),'\r','') `PRODUCT GROUP`,
			        REPLACE(REPLACE(REPLACE(IFNULL(ii.`MAT_CATG3_DESC`,''),'\t',''),'\n',''),'\r','') `PRODUCT CLASS`,
			        REPLACE(REPLACE(REPLACE(IFNULL(jj.`MAT_CATG4_DESC`,''),'\t',''),'\n',''),'\r','') `PRODUCT SUB GROUP`,
					IF(gg.`MAT_CATG1_CODE` = '0600','Grocery Store','Department Store') `PRODUCT TYPE`,
					IF((aa.`ART_CODE` LIKE '%SPROMO%' OR aa.`ART_CODE` LIKE '%QDAMAGE%'),0,0) __DMG,
			        aa.`ART_UPRICE` `SRP`,
			        0 `POS SRP`,
			        '1' __B_VATABLE,
			        IF(gg.`MAT_CATG1_CODE` = '0600',0,IF((aa.`ART_CODE` LIKE '%SPROMO%' OR aa.`ART_CODE` LIKE '%QDAMAGE%'),0,1)) __MEMB,
			        IFNULL(mm.`B_PWDDISC`,0) __B_PWDDISC,
			        IFNULL(mm.`B_PWDDISC_VAL`,0) __B_PWDDISC_VAL,
			        IFNULL(dd.`B_SCDISC`,0) __B_SCDISC,
			        IFNULL(dd.`B_SCDISC_VAL`,0) __B_SCDISC_VAL,
			        IFNULL(ee.`B_SSPT`,0) __B_SSPT,
			        IFNULL(cc.`B_PNTS_ALLWD`,1) __B_PNTS_ALLWD,
			        IFNULL(aa.`ART_PCODE`,'SRP') __PRICING_CODE,
			        IFNULL(aa.`ART_ISDISABLE`,0) __ART_ISDISABLE,
					IFNULL(nn.`MPROD_ID`,0) POS_MPRODID 
			        FROM {$this->db_erp}.`mst_article` aa 
			        LEFT JOIN {$this->db_erp}.`mst_article_ptsallwd` cc
			        ON (aa.`ART_CODE`=cc.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_article_scdisc` dd
			        ON (aa.`ART_CODE`=dd.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_article_sspt` ee
			        ON (aa.`ART_CODE`=ee.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_article_vatable` ff
			        ON (aa.`ART_CODE`=ff.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_article_pwddisc` mm
			        ON (aa.`ART_CODE`=mm.`B_ITEMCODE`)
			        LEFT JOIN {$this->db_erp}.`mst_mat_catg1_hd` gg
				ON (aa.`ART_HIERC1`=gg.`MAT_CATG1_CODE`)
				LEFT JOIN {$this->db_erp}.`mst_mat_catg2_hd` hh
				ON (aa.`ART_HIERC2`=hh.`MAT_CATG2_CODE`)
				LEFT JOIN {$this->db_erp}.`mst_mat_catg3_hd` ii
				ON (aa.`ART_HIERC3`=ii.`MAT_CATG3_CODE`) 
				LEFT JOIN {$this->db_erp}.`mst_mat_catg4_hd` jj 
				ON (aa.`ART_HIERC4`=jj.`MAT_CATG4_CODE`) 
				left join {$this->db_erp}.`mst_pos_prod_ids` nn on(aa.ART_CODE = nn.ART_CODE) 
				WHERE !(aa.`ART_CODE` LIKE '%ASSTD%') and !(aa.ART_HIERC1 = '0600') 
				and aa.ART_ISDISABLE = 0 and (aa.ART_POS_PROD_ID != 0 OR aa.ART_POS_PROD_ID IS NOT NULL) {$str_optn} 
				GROUP BY aa.`recid` 
			";
		} //end if
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa 
		";
		$qry = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * (($npages - 1) > 0 ? ($npages - 1) : 0));
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa limit {$nstart},{$npagelimit} ";
		$qry = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->resultID->num_rows > 0) { 
			$data['rlist'] = $qry->getResultArray();
			$data['rfieldnames'] = $qry->getFieldNames();
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['rfieldnames'] = '';
		}
		$qry->freeResult();
		return $data;
		
	} //end Artm_Branch_recs
	
	public function Artm_Branch_dload() { 
		$metmptkn = $this->mylibzsys->random_string(15);
		$metbltmp = "{$this->db_temp}.`tmp_data_mposprice_{$metmptkn}`";
		$mescript = ROOTPATH . 'app/ThirdParty/me-python/dload-pos-pricing.py';
		$B_CODE = "E0023";
		exec("/usr/bin/python3 $mescript $B_CODE /tmp/ {$metmptkn}",$output);
		echo $metbltmp;
		
		
	} //end Artm_Branch_dload
	
	
} //end main class
