<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;
use App\Models\MyDatumModel;
use App\Models\MyUserModel;

class MyPullOutTrxModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->request = \Config\Services::request();
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
		$this->mydatum =  new MyDatumModel();
		$this->myusermod =  new MyUserModel();
		$this->cusergrp = $this->myusermod->mysys_usergrp();
	}	
	
	public function view_recs($npages = 1,$npagelimit = 30,$msearchrec='') { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		//PARA SA MGA ADMINSITRATOR LANG
		$cusergrp = $this->myusermod->mysys_usergrp();
		/*if($cusergrp != 'SA'){
			$data = "</br><div class=\"col-md-3 alert alert-warning\"><strong>Note:</strong><br>Only administrative users can view this records.</div>";
			echo $data;
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			return $data;
		}*/
		$__flag="C";
		$str_optn = "";
		//IF USERGROUP IS EQUAL SA THEN ALL DATA WILL VIEW ELSE PER USER
		$str_vwrecs = "AND aa.`muser` = '$cuser'";
		if($this->cusergrp == 'SA') {
			$str_vwrecs = "";
		}
		if(!empty($msearchrec)) { 
			$msearchrec = $this->mylibzdb->me_escapeString($msearchrec);
			$str_optn = " where (aa.`potrx_no` like '%$msearchrec%' or aa.`po_no` like '%$msearchrec%' or bb.`COMP_NAME` like '%$msearchrec%' or cc.`BRNCH_NAME` like '%$msearchrec%' or dd.`VEND_NAME` like '%$msearchrec%') AND aa.flag != '$__flag' {$str_vwrecs}";
		}
		if(empty($msearchrec)) {
			$str_optn = " where aa.flag != '$__flag' {$str_vwrecs}";
		} 
		$strqry = "
		select aa.*,
		bb.COMP_NAME,
		cc.BRNCH_NAME,
		dd.VEND_NAME,
		sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`trx_manrecs_po_hd` aa
		JOIN {$this->db_erp}.`mst_company` bb
		ON (aa.`comp_id` = bb.`recid`)
		JOIN {$this->db_erp}.`mst_companyBranch` cc
		ON (aa.`branch_id` = cc.`recid`)
		JOIN {$this->db_erp}.`mst_vendor` dd
		ON (aa.`supplier_id` = dd.`recid`)
		{$str_optn} 
		";
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
		$qry->freeResult();
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa order by recid desc limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
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
	} //end view_recs
	
	public function save_trade() {
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$cuserrema=$this->myusermod->mysys_userrema();
		
		$trxno = $this->request->getVar('trxno_id');
		//$this->mylibzdb->me_escapeString($this->request->getVar('fld_txtpotrx_no'));//systemgenfld_dftag
		$tfld_Company_po =  $this->mylibzdb->me_escapeString($this->request->getVar('fld_Company_po'));//GET id
		$tfld_area_code_po = $this->mylibzdb->me_escapeString($this->request->getVar('fld_area_code_po'));//GET id
		$tfld_supplier_po = $this->mylibzdb->me_escapeString($this->request->getVar('fld_supplier_po'));//GET id
		
		//this is for branch tag
		$fld_dftag_temp  = $this->mylibzdb->me_escapeString($this->request->getVar('fld_dftag'));
		$fld_dftag_r = (empty($fld_dftag_temp) ? 'F' : $fld_dftag_temp);
		$fld_dftag =(($cuserrema ==='B') ? 'D': $fld_dftag_r);
		
		$fld_pono  = $this->mylibzdb->me_escapeString($this->request->getVar('fld_pono'));
		$fld_imsno  = $this->mylibzdb->me_escapeString($this->request->getVar('fld_imsno'));
		

		$fld_podate = $this->mylibz->mydate_yyyymmdd($this->request->getVar('fld_podate'));
		$fld_podate = $this->request->getVar('fld_podate');
		$fld_rems = $this->request->getVar('fld_rems');
		$fld_rson = $this->request->getVar('fld_rson');
		$fld_ptyp = $this->request->getVar('fld_ptyp');

		/*$fld_subtqty = $this->mylibzdb->me_escapeString(str_replace(',','',$this->request->getVar('fld_subtqty')));
		$fld_subtcost = $this->mylibzdb->me_escapeString(str_replace(',','',$this->request->getVar('fld_subtcost')));
		$fld_subtamt = $this->mylibzdb->me_escapeString(str_replace(',','',$this->request->getVar('fld_subtamt')));*/
		$__pfrom = $this->request->getVar('__pfrom');
		$adata1 = $this->request->getVar('adata1');
		$adata2 = $this->request->getVar('adata2');
	
		$mmn_rid = '';
		$fld_txtpotrx_no = '';
		$fld_Company_po =  '';
		$fld_area_code_po = '';
		$fld_supplier_po = '';
		$fld_posttag = 'N';
		// //HINDI kASI REQUIRED
		// if((empty($fld_pono)) && (empty($fld_imsno)) ){
		// 	echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong> Please input POA/IMS No.!!!</div>";
		// 	die();
		// }
		// if(empty($fld_pono)){
		// 	$fld_pono  = 'IMS'.$fld_imsno;
		// }
		// if(empty($fld_imsno)){
		// 	$fld_imsno  = 'POA'.$fld_pono;
		// }
		if(empty($trxno)) {
			//DEADLINE
			$str = "SELECT DATE(NOW()) >=  DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))-1 DAY)  
					AND DATE(NOW()) <  DATE(CONCAT(YEAR(NOW()),'-',MONTH(NOW()),'-',`CUTOFF_DATE`)) 
					AND DATE_SUB(LAST_DAY(NOW()  - INTERVAL 1 MONTH ),INTERVAL DAY(LAST_DAY(NOW()  - INTERVAL 1 MONTH ))-1 DAY)  <= DATE('$fld_podate')
					OR( MONTH('$fld_podate') >= MONTH(NOW()))
					AND( YEAR('$fld_podate') >= YEAR(NOW()))
					AS DATE_DEADLINE FROM {$this->db_erp}.`mst_cutoff_date`";
			//var_dump($str);
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEADLINE','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->getRowArray();
			$DATE_DEADLINE = $rw['DATE_DEADLINE'];
			
			if($DATE_DEADLINE == 0) { 
				echo "<div class=\"alert alert-warning\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Unable to save PO Transaction [$fld_pono], you've reached the cut off date in encoding this transaction.</div>";
				die();
			}
			$q->freeResult();
		}

		//COMPANY
		$str = "select recid,COMP_NAME 
		 from {$this->db_erp}.`mst_company` aa where aa.`COMP_NAME` = '$tfld_Company_po'";//mgdagdag ng id
		
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->getNumRows() == 0) { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
			die();
		}

		$rw = $q->getRowArray();
		$fld_Company_po = $rw['recid'];
		$q->freeResult();
		//END COMPANY

		//BRANCH
		$str = "select recid,BRNCH_NAME 
		 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$tfld_area_code_po'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->getNumRows() == 0) { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Branch Data!!!.</div>";
			die();
		}

		$rw = $q->getRowArray();
		$fld_area_code_po = $rw['recid'];
		$q->freeResult();
		//END BRANCH
		//BRANCH FROM
		if(!empty($__pfrom)){

			$str = "select recid,BRNCH_NAME 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$__pfrom'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
				die();
			}

			$rw = $q->getRowArray();
			$__pfrom = $rw['recid'];
			$q->freeResult();
			//END BRANCH
		}
		//VENDOR
		$str = "select recid,VEND_NAME 
		 from {$this->db_erp}.mst_vendor aa where `VEND_NAME` = '$tfld_supplier_po'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'VENDOR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->getNumRows() == 0) { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Supplier Data!!!.</div>";
			die();
		}

		$rw = $q->getRowArray();
		$fld_supplier_po = $rw['recid'];
		$q->freeResult();
		//END VENDOR
		
		//CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		if(!empty($trxno)) { 
			if($this->cusergrp != 'SA') { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Note</strong> You don't authorized to edit this data!!!</div>";
				die();
			}
		} //END CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		//CHECK IF VALID PO
		if(!empty($trxno)) { 
			$str = "select aa.recid,aa.potrx_no from {$this->db_erp}.`trx_manrecs_po_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' ";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Transaction DATA!!!.</div>";
				die();
			}
			$rw = $q->getRowArray();
			$mmn_rid  = $rw['recid'];
			$fld_txtpotrx_no = $rw['potrx_no'];
			$q->freeResult();
		} //END CHECK IF VALID PO

		//GENERATE NEW PO CTRL NO
		else { 
			$fld_txtpotrx_no =  $this->mydatum->get_ctr_new($fld_Company_po.$fld_area_code_po,$fld_supplier_po.$fld_pono,$this->db_erp,'CTRL_NO04');//TRANSACTION NO
		} //end mtkn_potr
		//RETURN TO mAPULANg LUPA ONLY
		if($fld_rson == 5){
			$fld_pono = $fld_txtpotrx_no;
			$fld_imsno = $fld_txtpotrx_no;
			$fld_dftag ='F';
			$fld_posttag = 'Y';

		}
		else{
			$fld_posttag = 'N';
			if(empty($fld_pono)){
				$fld_pono  = 'IMS'.$fld_imsno;
			}
			if(empty($fld_imsno)){
				$fld_imsno  = 'POA'.$fld_pono;
			}
			//HINDI kASI REQUIRED
			if((empty($fld_pono)) && (empty($fld_imsno)) ){
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong> Please input POA/IMS No.!!!</div>";
				die();
			}
			// $fld_pono = $fld_txtpotrx_no;
			// $fld_imsno = $fld_txtpotrx_no;
		}
		//ITEM
		if(empty($adata1)) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
			die();
		}
		if(count($adata1) > 0) { 
			$ame = array();
			$adatar1 = array();
			$adatar2 = array();
			$ntqty = 0;
			$ntamt = 0;
			$ntcost = 0;
	
			for($aa = 0; $aa < count($adata1); $aa++) { 
				$frmmmat_rid = 0;
				$medata = explode("x|x",$adata1[$aa]);
				$mat_mtkn = $adata2[$aa];
				$fld_mitemcode = $this->mylibzdb->me_escapeString(trim($medata[0]));
				$fld_mitemdesc = $this->mylibzdb->me_escapeString(trim($medata[1]));
				$fld_mitempkg = $this->mylibzdb->me_escapeString(trim($medata[2]));
				$fld_ucost = (empty(str_replace(',','',$medata[3])) ? 0 : (str_replace(',','',$medata[3]) + 0));
				$fld_mitemtcost = (empty(str_replace(',','',$medata[4])) ? 0 : (str_replace(',','',$medata[4]) + 0));
				$fld_srp =  (empty(str_replace(',','',$medata[5])) ? 0 : (str_replace(',','',$medata[5]) + 0));
				$fld_mitemtamt =(empty(str_replace(',','',$medata[6])) ? 0 : (str_replace(',','',$medata[6]) + 0));
				$fld_mitemqty = (empty(str_replace(',','',$medata[7])) ? 0 : (str_replace(',','',$medata[7]) + 0));
				//$fld_mitemqtyc = (empty($medata[7]) ? 0 : ($medata[7] + 0));
				$fld_remks = $this->mylibzdb->me_escapeString(trim($medata[8]));
				$fld_pout_rson = "";//$this->mylibzdb->me_escapeString(trim($medata[10]));
				//Additional when reason is bargain
				$fld_frmmndt_rid = $medata[11];
				$fld_frmmitemcode = $this->mylibzdb->me_escapeString(trim($medata[12]));

				//COMPUTATION ON SAVING
				$fld_mitemtcost = ($fld_mitemqty * $fld_ucost);
				$fld_mitemtamt =($fld_mitemqty * $fld_srp);
				
				$ntqty = $ntqty + $fld_mitemqty;//actual hd_subtqty
				$ntcost = $ntcost + $fld_mitemtcost;//actual hd_subtcost
				$ntamt = $ntamt + $fld_mitemtamt;//actual hd_subtamt
				
				//GETTING THE GRAND TOTAL HD
				$fld_subtqty = $this->mylibzdb->me_escapeString(str_replace(',','',$ntqty));
				$fld_subtcost = $this->mylibzdb->me_escapeString(str_replace(',','',$ntcost));
				$fld_subtamt = $this->mylibzdb->me_escapeString(str_replace(',','',$ntamt));
				//$total_pcs = $nconvf*$nqty;
				//$cmat_code = $this->mylibzdb->me_escapeString(trim($medata[0])) . $mktn_plnt_id . $mtkn_wshe_id;

				$amatnr = array();
				if(!empty($fld_mitemcode)) { 
					$str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mat_mtkn' and aa.`ART_CODE` = '$fld_mitemcode' ";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() == 0) { 
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$fld_mitemcode]</div>";
						die();
					} else { 
						//VALIDATION OF ITEMS,QTY,PRICE
						//if(in_array($cmat_code,$ame)) { 
						if(in_array($fld_mitemcode,$ame)) { 
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Material Data already exists [$fld_mitemcode]</div>";
							die();
						} else { 
							if($fld_mitemqty == 0 || $fld_mitemtamt == 0) { 
								echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid QTY or Price entries [$fld_mitemcode]!!!</div>";
								die();
							}
							if($fld_rson == 8){
								if(!empty($fld_frmmitemcode)) { 
									$frmstr = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_frmmndt_rid' and aa.`ART_CODE` = '$fld_frmmitemcode' ";
									$frmq = $this->mylibzdb->myoa_sql_exec($frmstr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
									if($frmq->getNumRows() == 0) { 
										echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid From Itemcode Data!!!</div>";
										die();
									}
									$frmrw = $frmq->getRowArray();
									$frmmmat_rid = $frmrw['recid'];

								}
								else{
									echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid</strong> Please select From Itemcode<br/>[$fld_mitemcode]</div>";
									die();
								}
							}
							
						}
						
						$rw = $q->getRowArray();
						$mmat_rid = $rw['recid'];
						array_push($ame,$fld_mitemcode); 
						array_push($adatar1,$medata);
						array_push($adatar2,$mmat_rid);
					}

					$q->freeResult();
				}

			}  //end for 
			
			//no item validation
			//if(count($adatar1) > 0) { 
			if(((count($adatar1) == 0) && (!empty($trxno))) || ((count($adatar1) > 0) && ((empty($trxno)) || (!empty($trxno)))  )) { //added enhancement with Jo 2022.12.23
				if(!empty($trxno)) { 
					//DR bAKA MAGAKATAON NA MAY MAGAKAIBANG SUP NA PAREHAS ANG DR
					$str = "select aa.`po_no` from {$this->db_erp}.`trx_manrecs_po_hd` aa where aa.`po_no` = '$fld_pono' AND aa.`branch_id` = '$fld_area_code_po' AND !(aa.`flag`='C') AND !(sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$trxno')";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() > 0) { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> POA No already exists.!!!.[".$fld_pono."]</div>";
						die();
					}
					$str = "select aa.`ims_no` from {$this->db_erp}.`trx_manrecs_po_hd` aa where aa.`ims_no` = '$fld_pono' AND aa.`branch_id` = '$fld_area_code_po' AND !(aa.`flag`='C') AND !(sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$trxno')";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() > 0) { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> IMS No already exists.!!!.[".$fld_imsno."]</div>";
						die();
					}

					$str = "
					update {$this->db_erp}.`trx_manrecs_po_hd`
					SET `comp_id` = '$fld_Company_po',
					  	`branch_id` = '$fld_area_code_po',
					  	`po_no` = '$fld_pono',
					  	`ims_no` = '$fld_imsno',
					  	`po_date` ='$fld_podate',
					  	`supplier_id` = '$fld_supplier_po',
					  	`df_tag`='$fld_dftag',
					  	`rems` = '$fld_rems',
					  	`hd_subtqty`='$fld_subtqty',
						`hd_subtcost`='$fld_subtcost',
						`hd_subtamt`='$fld_subtamt',
						`po_rsons_id`= '$fld_rson',
						`hd_pfrom_id`='$__pfrom'
					WHERE `recid` = '$mmn_rid';
					";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MN_UREC','',$fld_txtpotrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		

				} else { 
					//PO bAKA MAGAKATAON NA MAY MAGAKAIBANG SUP NA PAREHAS ANG DR
					$str = "select aa.`po_no` from {$this->db_erp}.`trx_manrecs_po_hd` aa where aa.`po_no` = '$fld_pono' AND aa.`branch_id` = '$fld_area_code_po' AND !(aa.`flag`='C')";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() > 0) { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> POA No already exists.!!!.[".$fld_pono."]</div>";
						die();
					}
					$str = "select aa.`ims_no` from {$this->db_erp}.`trx_manrecs_po_hd` aa where aa.`ims_no` = '$fld_pono' AND aa.`branch_id` = '$fld_area_code_po' AND !(aa.`flag`='C')";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() > 0) { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> IMS No already exists.!!!.[".$fld_imsno."]</div>";
						die();
					}

					$str = "insert into {$this->db_erp}.`trx_manrecs_po_hd`
					(`potrx_no`,
					`comp_id`,
					`branch_id`,
					`po_no`,
					`ims_no`,
					`po_date`,
					`supplier_id`,
					`rems`,
					`hd_subtqty`,
					`hd_subtcost`,
					`hd_subtamt`,
					`po_rsons_id`,
					`hd_pfrom_id`,
					`po_type`,
					`muser`,
					`df_tag`,
					`post_tag`)
					VALUES ('$fld_txtpotrx_no',
					'$fld_Company_po',
					'$fld_area_code_po',
					'$fld_pono',
					'$fld_imsno',
					'$fld_podate',
					'$fld_supplier_po',
					'$fld_rems',
					'$fld_subtqty',
					'$fld_subtcost',
					'$fld_subtamt',
					'$fld_rson',
					'$__pfrom',
					'$fld_ptyp',
					'$cuser',
					'$fld_dftag',
					'$fld_posttag')";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MN_PO_AREC','',$fld_txtpotrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_mntr from {$this->db_erp}.`trx_manrecs_po_hd` aa where `potrx_no` = '$fld_txtpotrx_no' ";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$rw = $q->getRowArray();
					$mmn_rid = $rw['recid'];
					//var_dump($mmn_rid);
					$__hmtkn_mntr = $rw['mtkn_mntr'];
					$q->freeResult();


				}

				//GET PLNT, WSHE, SBIN


				for($xx = 0; $xx < count($adatar1); $xx++) {  //MAY MALI DITO
					$frmmmat_rid = 0;
					$xdata = $adatar1[$xx];
					$mat_rid = $adatar2[$xx];
					
					//$fld_mitemrid = $this->mylibzdb->me_escapeString(trim($xdata[0]));
					$fld_mitemcode = $xdata[0];
					$fld_mitemdesc = $this->mylibzdb->me_escapeString(trim($xdata[1]));
					$fld_mitempkg = $this->mylibzdb->me_escapeString(trim($xdata[2]));
					$fld_ucost = (empty(str_replace(',','',$xdata[3])) ? 0 : (str_replace(',','',$xdata[3]) + 0));
					$fld_mitemtcost = (empty(str_replace(',','',$xdata[4])) ? 0 : (str_replace(',','',$xdata[4]) + 0));
					$fld_srp =  (empty(str_replace(',','',$xdata[5])) ? 0 : (str_replace(',','',$xdata[5]) + 0));
					$fld_mitemtamt =(empty(str_replace(',','',$xdata[6])) ? 0 : (str_replace(',','',$xdata[6]) + 0));
					$fld_mitemqty = (empty(str_replace(',','',$xdata[7])) ? 0 : (str_replace(',','',$xdata[7]) + 0));
					//$fld_mitemqtyc = (empty($xdata[7]) ? 0 : ($xdata[7] + 0));
					$fld_remks = $this->mylibzdb->me_escapeString(trim($xdata[8]));
					//$fld_olt = $this->mylibzdb->me_escapeString(trim($xdata[9]));
					$mndt_rid = $this->mylibzdb->me_escapeString(trim($xdata[9]));//dt mn id
					$fld_pout_rson = "";//$this->mylibzdb->me_escapeString(trim($xdata[10]));
					//FOR BARGAIN
					$fld_frmmndt_rid = $xdata[11];
					$fld_frmmitemcode = $this->mylibzdb->me_escapeString(trim($xdata[12]));
					//COMPUTATION ON SAVING
					$fld_mitemtcost = ($fld_mitemqty * $fld_ucost);
					$fld_mitemtamt =($fld_mitemqty * $fld_srp);
			
				//	$tamt = $xdata[7];
					//CONDITION PARA SA TRADE AT NON TRADE  YUNG TRADE KASI HINDI KASAMA SA INVENTORY KAYA IBA LAGAYAN NG QTY
					if(($fld_rson == 5) && ($fld_ptyp == 'T')){
						$str_ptyp = "`qty_encd`";
						// $str_ptyp_ins = "`qty_encd`,";
						// $str_ptyp_ins2 = "'$fld_mitemqty',";
						// $str_ptyp_upd = "`qty_encd` = '$fld_mitemqty',";
						$str_ptyp_ins = "";
						$str_ptyp_ins2 = "";
						$str_ptyp_upd = "";
					}
					else{
						$str_ptyp = "`qty`";
						$str_ptyp_ins = "";
						$str_ptyp_ins2 = "";
						$str_ptyp_upd = "";
					}
					
					
					if(empty($trxno)) {  
						
						$str = "select recid from {$this->db_erp}.`trx_manrecs_po_dt` where `potrx_no` = '$fld_txtpotrx_no' and `mat_rid` = '$mat_rid'";
						$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->getNumRows() > 0 ) { 
							$rw = $q->getRowArray();
							$mndt_rid = $rw['recid'];

							$str = "update {$this->db_erp}.`trx_manrecs_po_dt`
							SET `mat_rid` = '$mat_rid',
							  `mat_code` = '$fld_mitemcode',
							  {$str_ptyp} = '$fld_mitemqty',
							  {$str_ptyp_upd}
							  `ucost` = '$fld_ucost',
							  `tcost` = '$fld_mitemtcost',
							  `uprice` = '$fld_srp',
							  `tamt` = '$fld_mitemtamt',
							  `nremarks` = '$fld_remks',
							  `pout_rson_rid` = '$fld_pout_rson',
							  `frmmat_rid` = '$frmmmat_rid',
							  `frmmat_code` = '$fld_frmmitemcode',
							  `muser` = '$cuser'
							WHERE `recid` = '$mndt_rid'
							";
						} else { 
							$str = "insert into {$this->db_erp}.`trx_manrecs_po_dt`
							(`mrhd_rid`,
							`potrx_no`,
							`mat_rid`,
							`mat_code`,
							`ucost`,
							`tcost`,
							`uprice`,
							`tamt`,
							{$str_ptyp},
							{$str_ptyp_ins}
							`nremarks`,
							`pout_rson_rid`,
							`frmmat_rid`,
							`frmmat_code`,
							`muser`)
							VALUES ('$mmn_rid',
							'$fld_txtpotrx_no',
							'$mat_rid',
							'$fld_mitemcode',
							'$fld_ucost',
							'$fld_mitemtcost',
							'$fld_srp',
							'$fld_mitemtamt',
							'$fld_mitemqty',
							{$str_ptyp_ins2}
							'$fld_remks',
							'$fld_pout_rson',
							'$frmmmat_rid',
							'$fld_frmmitemcode',
							'$cuser')
							";
						}
						$q->freeResult();
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						$this->mylibzdb->user_logs_activity_module($this->db_erp,'TRX_mn_DT','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
						
						
						
						
						
					} else { 
						if(empty($mndt_rid)) { 
							$str = "select recid from {$this->db_erp}.`trx_manrecs_po_dt` where `potrx_no` = '$fld_txtpotrx_no' and `mat_rid` = '$mat_rid'";
							
							$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($q->getNumRows() > 0 ) { 
								$rw = $q->getRowArray();
								$mn_rid = $rw['recid'];
								$str = "update {$this->db_erp}.`trx_manrecs_po_dt`
										SET `mat_rid` = '$mat_rid',
										  `mat_code` = '$fld_mitemcode',
										   {$str_ptyp} = '$fld_mitemqty',
										   {$str_ptyp_upd}
										  `ucost` = '$fld_ucost',
										  `tcost` = '$fld_mitemtcost',
										  `uprice` = '$fld_srp',
										  `tamt` = '$fld_mitemtamt',
										  `nremarks` = '$fld_remks',
										  `pout_rson_rid` = '$fld_pout_rson',
										  `frmmat_rid` = '$frmmmat_rid',
							  			  `frmmat_code` = '$fld_frmmitemcode',
							  			  `muser` = '$cuser'
										WHERE `recid` = '$mn_rid'
										";
						} else { 
							$str = "insert into {$this->db_erp}.`trx_manrecs_po_dt`
							(`mrhd_rid`,
							`potrx_no`,
							`mat_rid`,
							`mat_code`,
							`ucost`,
							`tcost`,
							`uprice`,
							`tamt`,
							{$str_ptyp},
							{$str_ptyp_ins}
							`nremarks`,
							`pout_rson_rid`,
							`frmmat_rid`,
							`frmmat_code`,
							`muser`)
							VALUES ('$mmn_rid',
							'$fld_txtpotrx_no',
							'$mat_rid',
							'$fld_mitemcode',
							'$fld_ucost',
							'$fld_mitemtcost',
							'$fld_srp',
							'$fld_mitemtamt',
							'$fld_mitemqty',
							{$str_ptyp_ins2}
							'$fld_remks',
							'$fld_pout_rson',
							'$frmmmat_rid',
							'$fld_frmmitemcode',
							'$cuser')
							";
							}
							$q->freeResult();
							$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							$this->mylibzdb->user_logs_activity_module($this->db_erp,'trx_po_dt','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
							
						} else { // end empty podt_rid 
							$str = "select recid from {$this->db_erp}.`trx_manrecs_po_dt` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mndt_rid'";
							$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($qq->getNumRows() > 0) { 
								$rrw = $qq->getRowArray();
								$mn_dtrid = $rrw['recid'];
								$str = "
								update {$this->db_erp}.`trx_manrecs_po_dt`
										SET `mat_rid` = '$mat_rid',
										  `mat_code` = '$fld_mitemcode',
										   {$str_ptyp} = '$fld_mitemqty',
										   {$str_ptyp_upd}
										  `ucost` = '$fld_ucost',
										  `tcost` = '$fld_mitemtcost',
										  `uprice` = '$fld_srp',
										  `tamt` = '$fld_mitemtamt',
										  `nremarks` = '$fld_remks',
										  `pout_rson_rid` = '$fld_pout_rson',
										  `frmmat_rid` = '$frmmmat_rid',
							  			  `frmmat_code` = '$fld_frmmitemcode',
										  `muser` = '$cuser'
										WHERE `recid` = '$mn_dtrid'";
								$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
								$this->mylibzdb->user_logs_activity_module($this->db_erp,'trx_mn_dt','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
							}
							$qq->freeResult();
						}  //end 
						
					}
					
					
				}  //end for 

				//record on AV Work Flow
				//$qry->freeResult();	
				if(empty($trxno)) { 
					echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Recorded Successfully!!!</div>
					<script type=\"text/javascript\"> 
						function __po_refresh_data() { 
							try { 
								$('#__hmtkn_trxnoid').val('{$__hmtkn_mntr}');
								$('#txtpotrx_no').val('{$fld_txtpotrx_no}');
								$('#mbtn_mn_Save').prop('disabled',true);
							} catch(err) { 
								var mtxt = 'There was an error on this page.\\n';
								mtxt += 'Error description: ' + err.message;
								mtxt += '\\nClick OK to continue.';
								alert(mtxt);
								return false;
							}  //end try 
						} 
						
						__po_refresh_data();
					</script>
					";
					die();
				} else { 
					echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
					";
					die();
				}
			} else { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Item Data!!!.</div>";
				die();
			} //end if 
		} else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
			die();
		}
	}  //end save_trade

	public function save_nontrade() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$cuserrema=$this->myusermod->mysys_userrema();
		
		$trxno = $this->request->getVar('trxno_id');
		//$this->mylibzdb->me_escapeString($this->request->getVar('fld_txtpotrx_no'));//systemgenfld_dftag
		$tfld_Company_po =  $this->mylibzdb->me_escapeString($this->request->getVar('fld_Company_po'));//GET id
		$tfld_area_code_po = $this->mylibzdb->me_escapeString($this->request->getVar('fld_area_code_po'));//GET id
		$tfld_supplier_po = $this->mylibzdb->me_escapeString($this->request->getVar('fld_supplier_po'));//GET id
		
		//this is for branch tag
		$fld_dftag_temp  = $this->mylibzdb->me_escapeString($this->request->getVar('fld_dftag'));
		$fld_dftag_r = (empty($fld_dftag_temp) ? 'F' : $fld_dftag_temp);
		$fld_dftag =(($cuserrema ==='B') ? 'D': $fld_dftag_r);
		
		$fld_pono  = $this->mylibzdb->me_escapeString($this->request->getVar('fld_pono'));
		$fld_imsno  = $this->mylibzdb->me_escapeString($this->request->getVar('fld_imsno'));
		

		//$fld_podate = $this->mylibz->mydate_yyyymmdd($this->request->getVar('fld_podate'));
		$fld_podate = $this->request->getVar('fld_podate');
		$fld_rems = $this->request->getVar('fld_rems');
		$fld_rson = $this->request->getVar('fld_rson');
		$fld_ptyp = $this->request->getVar('fld_ptyp');

	
		$__pfrom = $this->request->getVar('__pfrom');
		$adata1 = $this->request->getVar('adata1');
		$adata2 = $this->request->getVar('adata2');
	
		$mmn_rid = '';
		$fld_txtpotrx_no = '';
		$fld_Company_po =  '';
		$fld_area_code_po = '';
		$fld_supplier_po = '';
		
		
		//COMPANY
		$str = "select recid,COMP_NAME 
		 from {$this->db_erp}.`mst_company` aa where aa.`COMP_NAME` = '$tfld_Company_po'";  //mgdagdag ng id
		
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->getNumRows() == 0) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
			die();
		}

		$rw = $q->getRowArray();
		$fld_Company_po = $rw['recid'];
		$q->freeResult();
		//END COMPANY

		//BRANCH
		$str = "select recid,BRNCH_NAME 
		 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$tfld_area_code_po'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->getNumRows() == 0) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Branch Data!!!.</div>";
			die();
		}

		$rw = $q->getRowArray();
		$fld_area_code_po = $rw['recid'];
		$q->freeResult();
		//END BRANCH
		//BRANCH FROM
		if(!empty($__pfrom)){

			$str = "select recid,BRNCH_NAME 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$__pfrom'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
				die();
			}

			$rw = $q->getRowArray();
			$__pfrom = $rw['recid'];
			$q->freeResult();
			//END BRANCH
		}
		//VENDOR
		$str = "select recid,VEND_NAME 
		 from {$this->db_erp}.mst_vendor aa where `VEND_NAME` = '$tfld_supplier_po'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'VENDOR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->getNumRows() == 0) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Supplier Data!!!.</div>";
			die();
		}

		$rw = $q->getRowArray();
		$fld_supplier_po = $rw['recid'];
		$q->freeResult();
		//END VENDOR
		
		//CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		if(!empty($trxno)) { 
			if($this->cusergrp != 'SA') { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Note</strong> You don't authorized to edit this data!!!</div>";
				die();
			}
		} //END CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		//CHECK IF VALID PO
		if(!empty($trxno)) { 
			$str = "select aa.recid,aa.potrx_no from {$this->db_erp}.`trx_manrecs_po_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' ";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Transaction DATA!!!.</div>";
				die();
			}
			$rw = $q->getRowArray();
			$mmn_rid  = $rw['recid'];
			$fld_txtpotrx_no = $rw['potrx_no'];
			$q->freeResult();
		} //END CHECK IF VALID PO
		
		//GENERATE NEW PO CTRL NO
		else { 
			$fld_txtpotrx_no =  $this->mydatum->get_ctr_new($fld_Company_po.$fld_area_code_po,$fld_supplier_po.$fld_pono,$this->db_erp,'CTRL_NO04'); //TRANSACTION NO
		} //end mtkn_potr
		
		//RETURN TO mAPULANg LUPA ONLY
		if($fld_rson == 5){
			$fld_pono = $fld_txtpotrx_no;
			$fld_imsno = $fld_txtpotrx_no;
		}
		else{
			if(empty($fld_pono)){
				$fld_pono  = 'IMS'.$fld_imsno;
			}
			if(empty($fld_imsno)){
				$fld_imsno  = 'POA'.$fld_pono;
			}
			//HINDI kASI REQUIRED
			if((empty($fld_pono)) && (empty($fld_imsno)) ){
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong> Please input POA/IMS No.!!!</div>";
				die();
			}
			// $fld_pono = $fld_txtpotrx_no;
			// $fld_imsno = $fld_txtpotrx_no;
		}
		
		//ITEM
		if(empty($adata1)) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
			die();
		}
		if(count($adata1) > 0) { 
			$ame = array();
			$adatar1 = array();
			$adatar2 = array();
			$ntqty = 0;
			$ntamt = 0;
			$ntcost = 0;
			 $cc= 1;
			for($aa = 0; $aa < count($adata1); $aa++) { 
				$medata = explode("x|x",$adata1[$aa]);
				$mat_mtkn = $adata2[$aa];
				$fld_mitemcode = $aa;//$this->mylibzdb->me_escapeString(trim($medata[0]));
				$fld_mitemdesc = $this->mylibzdb->me_escapeString(trim($medata[1]));
				$fld_mitempkg = $this->mylibzdb->me_escapeString(trim($medata[2]));
				$fld_ucost = (empty(str_replace(',','',$medata[3])) ? 0 : (str_replace(',','',$medata[3]) + 0));
				$fld_mitemtcost = (empty(str_replace(',','',$medata[4])) ? 0 : (str_replace(',','',$medata[4]) + 0));
				$fld_srp =  (empty(str_replace(',','',$medata[5])) ? 0 : (str_replace(',','',$medata[5]) + 0));
				$fld_mitemtamt =(empty(str_replace(',','',$medata[6])) ? 0 : (str_replace(',','',$medata[6]) + 0));
				$fld_mitemqty = (empty(str_replace(',','',$medata[7])) ? 0 : (str_replace(',','',$medata[7]) + 0));
				//$fld_mitemqtyc = (empty($medata[7]) ? 0 : ($medata[7] + 0));
				$fld_remks = $this->mylibzdb->me_escapeString(trim($medata[8]));
				$fld_pout_rson = "";//$this->mylibzdb->me_escapeString(trim($medata[10]));
				
				//COMPUTATION ON SAVING
				$fld_mitemtcost = ($fld_mitemqty * $fld_ucost);
				$fld_mitemtamt =($fld_mitemqty * $fld_srp);
				
				$ntqty = $ntqty + $fld_mitemqty;//actual hd_subtqty
				$ntcost = $ntcost + $fld_mitemtcost;//actual hd_subtcost
				$ntamt = $ntamt + $fld_mitemtamt;//actual hd_subtamt
				
				//GETTING THE GRAND TOTAL HD
				$fld_subtqty = $this->mylibzdb->me_escapeString(str_replace(',','',$ntqty));
				$fld_subtcost = $this->mylibzdb->me_escapeString(str_replace(',','',$ntcost));
				$fld_subtamt = $this->mylibzdb->me_escapeString(str_replace(',','',$ntamt));
				//$total_pcs = $nconvf*$nqty;
				//$cmat_code = $this->mylibzdb->me_escapeString(trim($medata[0])) . $mktn_plnt_id . $mtkn_wshe_id;

				$amatnr = array(); 
				if(!empty($fld_mitemcode)) { 
						//VALIDATION OF ITEMS,QTY,PRICE
						//if(in_array($cmat_code,$ame)) { 
						if(in_array($fld_mitemcode,$ame)) { 
							echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Material Data already exists [$fld_remks]</div>";
							die();
						} else { 
							if($fld_mitemqty == 0) { 
								echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid QTY or Price entries [$fld_remks]!!!</div>";
								die();
							}
							
						}
						array_push($ame,$fld_mitemcode); 
						array_push($adatar1,$medata);
						$q->freeResult();
				}

			 $cc++;
			}  //end for 
		
			if(count($adatar1) > 0) { 
				if(!empty($trxno)) { 
					//DR bAKA MAGAKATAON NA MAY MAGAKAIBANG SUP NA PAREHAS ANG DR
					$str = "select aa.`po_no` from {$this->db_erp}.`trx_manrecs_po_hd` aa where aa.`po_no` = '$fld_pono' AND aa.`branch_id` = '$fld_area_code_po' AND !(aa.`flag`='C') AND !(sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$trxno')";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() > 0) { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> POA No already exists.!!!.[".$fld_pono."]</div>";
						die();
					}
					$str = "select aa.`ims_no` from {$this->db_erp}.`trx_manrecs_po_hd` aa where aa.`ims_no` = '$fld_pono' AND aa.`branch_id` = '$fld_area_code_po' AND !(aa.`flag`='C') AND !(sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$trxno')";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() > 0) { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> IMS No already exists.!!!.[".$fld_imsno."]</div>";
						die();
					}

					$str = "
					update {$this->db_erp}.`trx_manrecs_po_hd`
					SET `comp_id` = '$fld_Company_po',
					  	`branch_id` = '$fld_area_code_po',
					  	`po_no` = '$fld_pono',
					  	`ims_no` = '$fld_imsno',
					  	`po_date` ='$fld_podate',
					  	`supplier_id` = '$fld_supplier_po',
					  	`df_tag`='$fld_dftag',
					  	`rems` = '$fld_rems',
					  	`hd_subtqty`='$fld_subtqty',
						`hd_subtcost`='$fld_subtcost',
						`hd_subtamt`='$fld_subtamt',
						`po_rsons_id`= '$fld_rson',
						`hd_pfrom_id`='$__pfrom'
					WHERE `recid` = '$mmn_rid';
					";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MN_UREC','',$fld_txtpotrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		

				} else { 
					//PO bAKA MAGAKATAON NA MAY MAGAKAIBANG SUP NA PAREHAS ANG DR
					$str = "select aa.`po_no` from {$this->db_erp}.`trx_manrecs_po_hd` aa where aa.`po_no` = '$fld_pono' AND aa.`branch_id` = '$fld_area_code_po' AND !(aa.`flag`='C')";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() > 0) { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> POA No already exists.!!!.[".$fld_pono."]</div>";
						die();
					}
					$str = "select aa.`ims_no` from {$this->db_erp}.`trx_manrecs_po_hd` aa where aa.`ims_no` = '$fld_pono' AND aa.`branch_id` = '$fld_area_code_po' AND !(aa.`flag`='C')";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() > 0) { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> IMS No already exists.!!!.[".$fld_imsno."]</div>";
						die();
					}

					$str = "insert into {$this->db_erp}.`trx_manrecs_po_hd`
					(`potrx_no`,
					`comp_id`,
					`branch_id`,
					`po_no`,
					`ims_no`,
					`po_date`,
					`supplier_id`,
					`rems`,
					`hd_subtqty`,
					`hd_subtcost`,
					`hd_subtamt`,
					`po_rsons_id`,
					`hd_pfrom_id`,
					`po_type`,
					`muser`,
					`df_tag`,
					`post_tag`)
					VALUES ('$fld_txtpotrx_no',
					'$fld_Company_po',
					'$fld_area_code_po',
					'$fld_pono',
					'$fld_imsno',
					'$fld_podate',
					'$fld_supplier_po',
					'$fld_rems',
					'$fld_subtqty',
					'$fld_subtcost',
					'$fld_subtamt',
					'$fld_rson',
					'$__pfrom',
					'$fld_ptyp',
					'$cuser',
					'F',
					'Y')";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MN_PO_AREC','',$fld_txtpotrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_mntr from {$this->db_erp}.`trx_manrecs_po_hd` aa where `potrx_no` = '$fld_txtpotrx_no' ";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$rw = $q->getRowArray();
					$mmn_rid = $rw['recid'];
					//var_dump($mmn_rid);
					$__hmtkn_mntr = $rw['mtkn_mntr'];
					$q->freeResult();


				}

				//GET PLNT, WSHE, SBIN

				 $cc= 1;
				for($xx = 0; $xx < count($adatar1); $xx++) {  //MAY MALI DITO
		
					$xdata = $adatar1[$xx];
					$mat_rid = '';//$adatar2[$xx];
					
					//$fld_mitemrid = $this->mylibzdb->me_escapeString(trim($xdata[0]));
					$fld_mitemcode = $xx;//$xdata[0];
					$fld_mitemdesc = $this->mylibzdb->me_escapeString(trim($xdata[1]));
					$fld_mitempkg = $this->mylibzdb->me_escapeString(trim($xdata[2]));
					$fld_ucost = (empty(str_replace(',','',$xdata[3])) ? 0 : (str_replace(',','',$xdata[3]) + 0));
					$fld_mitemtcost = (empty(str_replace(',','',$xdata[4])) ? 0 : (str_replace(',','',$xdata[4]) + 0));
					$fld_srp =  (empty(str_replace(',','',$xdata[5])) ? 0 : (str_replace(',','',$xdata[5]) + 0));
					$fld_mitemtamt =(empty(str_replace(',','',$xdata[6])) ? 0 : (str_replace(',','',$xdata[6]) + 0));
					$fld_mitemqty = (empty(str_replace(',','',$xdata[7])) ? 0 : (str_replace(',','',$xdata[7]) + 0));
					//$fld_mitemqtyc = (empty($xdata[7]) ? 0 : ($xdata[7] + 0));
					$fld_remks = $this->mylibzdb->me_escapeString(trim($xdata[8]));
					//$fld_olt = $this->mylibzdb->me_escapeString(trim($xdata[9]));
					$mndt_rid = $this->mylibzdb->me_escapeString(trim($xdata[9]));//dt mn id
					$fld_pout_rson = "";//$this->mylibzdb->me_escapeString(trim($xdata[10]));
					
					//COMPUTATION ON SAVING
					$fld_mitemtcost = ($fld_mitemqty * $fld_ucost);
					$fld_mitemtamt =($fld_mitemqty * $fld_srp);
			
					//	$tamt = $xdata[7];
					//CONDITION PARA SA TRADE AT NON TRADE  YUNG TRADE KASI HINDI KASAMA SA INVENTORY KAYA IBA LAGAYAN NG QTY
					if(($fld_rson == 5) && ($fld_ptyp == 'T')){
						$str_ptyp = "`qty_encd`";
						// $str_ptyp_ins = "`qty_encd`,";
						// $str_ptyp_ins2 = "'$fld_mitemqty',";
						// $str_ptyp_upd = "`qty_encd` = '$fld_mitemqty',";
						$str_ptyp_ins = "";
						$str_ptyp_ins2 = "";
						$str_ptyp_upd = "";
					}
					else{
						$str_ptyp = "`qty_encd`";
						$str_ptyp_ins = "";
						$str_ptyp_ins2 = "";
						$str_ptyp_upd = "";
					}
					
					
					if(empty($trxno)) {  
						
						$str = "select recid from {$this->db_erp}.`trx_manrecs_po_dt` where `potrx_no` = '$fld_txtpotrx_no' and `mat_code` = '$fld_mitemcode'";
						$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->getNumRows() > 0 ) { 
							$rw = $q->getRowArray();
							$mndt_rid = $rw['recid'];

							$str = "update {$this->db_erp}.`trx_manrecs_po_dt`
							SET `mat_rid` = '$mat_rid',
							  `mat_code` = '$fld_mitemcode',
							  {$str_ptyp} = '$fld_mitemqty',
							  {$str_ptyp_upd}
							  `ucost` = '$fld_ucost',
							  `tcost` = '$fld_mitemtcost',
							  `uprice` = '$fld_srp',
							  `tamt` = '$fld_mitemtamt',
							  `nremarks` = '$fld_remks',
							  `pout_rson_rid` = '$fld_pout_rson',
							  `muser` = '$cuser'
							WHERE `mat_code` = '$fld_mitemcode'
							";
						} else { 
							$str = "insert into {$this->db_erp}.`trx_manrecs_po_dt`
							(`mrhd_rid`,
							`potrx_no`,
							`mat_rid`,
							`mat_code`,
							`ucost`,
							`tcost`,
							`uprice`,
							`tamt`,
							{$str_ptyp},
							{$str_ptyp_ins}
							`nremarks`,
							`pout_rson_rid`,
							`muser`)
							VALUES ('$mmn_rid',
							'$fld_txtpotrx_no',
							'$mat_rid',
							'$fld_mitemcode',
							'$fld_ucost',
							'$fld_mitemtcost',
							'$fld_srp',
							'$fld_mitemtamt',
							'$fld_mitemqty',
							{$str_ptyp_ins2}
							'$fld_remks',
							'$fld_pout_rson',
							'$cuser')
							";
						}
						$q->freeResult();
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						$this->mylibzdb->user_logs_activity_module($this->db_erp,'TRX_mn_DT','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
					} else { 
						if(empty($mndt_rid)) { 
							$str = "select recid from {$this->db_erp}.`trx_manrecs_po_dt` where `potrx_no` = '$fld_txtpotrx_no' and `mat_code` = '$fld_mitemcode'";
							
							$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($q->getNumRows() > 0 ) { 
								$rw = $q->getRowArray();
								$mn_rid = $rw['recid'];
								$str = "update {$this->db_erp}.`trx_manrecs_po_dt`
										SET `mat_rid` = '$mat_rid',
										  `mat_code` = '$fld_mitemcode',
										   {$str_ptyp} = '$fld_mitemqty',
										   {$str_ptyp_upd}
										  `ucost` = '$fld_ucost',
										  `tcost` = '$fld_mitemtcost',
										  `uprice` = '$fld_srp',
										  `tamt` = '$fld_mitemtamt',
										  `nremarks` = '$fld_remks',
										  `pout_rson_rid` = '$fld_pout_rson',
							  			  `muser` = '$cuser'
										WHERE `mat_code` = '$fld_mitemcode'
										";
						} else { 
							$str = "insert into {$this->db_erp}.`trx_manrecs_po_dt`
							(`mrhd_rid`,
							`potrx_no`,
							`mat_rid`,
							`mat_code`,
							`ucost`,
							`tcost`,
							`uprice`,
							`tamt`,
							{$str_ptyp},
							{$str_ptyp_ins}
							`nremarks`,
							`pout_rson_rid`,
							`muser`)
							VALUES ('$mmn_rid',
							'$fld_txtpotrx_no',
							'$mat_rid',
							'$fld_mitemcode',
							'$fld_ucost',
							'$fld_mitemtcost',
							'$fld_srp',
							'$fld_mitemtamt',
							'$fld_mitemqty',
							{$str_ptyp_ins2}
							'$fld_remks',
							'$fld_pout_rson',
							'$cuser')
							";
							}
							$q->freeResult();
							$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							$this->mylibzdb->user_logs_activity_module($this->db_erp,'trx_po_dt','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
							
						} else { // end empty podt_rid 
							$str = "select recid from {$this->db_erp}.`trx_manrecs_po_dt` aa where and `mat_code` = '$fld_mitemcode'";
							$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($qq->getNumRows() > 0) { 
								$rrw = $qq->getRowArray();
								$mn_dtrid = $rrw['recid'];
								$str = "
								update {$this->db_erp}.`trx_manrecs_po_dt`
										SET `mat_rid` = '$mat_rid',
										  `mat_code` = '$fld_mitemcode',
										   {$str_ptyp} = '$fld_mitemqty',
										   {$str_ptyp_upd}
										  `ucost` = '$fld_ucost',
										  `tcost` = '$fld_mitemtcost',
										  `uprice` = '$fld_srp',
										  `tamt` = '$fld_mitemtamt',
										  `nremarks` = '$fld_remks',
										  `pout_rson_rid` = '$fld_pout_rson',
										  `muser` = '$cuser'
										WHERE `mat_code` = '$fld_mitemcode'";
								$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
								$this->mylibzdb->user_logs_activity_module($this->db_erp,'trx_mn_dt','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
							}
							$qq->freeResult();
							
						}  //end 
						
					}
					
					
				$cc++;
				}  //end for
				//record on AV Work Flow
				//$qry->freeResult();	
				if(empty($trxno)) { 
					echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Recorded Successfully!!!</div>
					<script type=\"text/javascript\"> 
						function __po_refresh_data() { 
							try { 
								$('#__hmtkn_trxnoid').val('{$__hmtkn_mntr}');
								$('#txtpotrx_no').val('{$fld_txtpotrx_no}');
								$('#mbtn_mn_Save').prop('disabled',true);
							} catch(err) { 
								var mtxt = 'There was an error on this page.\\n';
								mtxt += 'Error description: ' + err.message;
								mtxt += '\\nClick OK to continue.';
								alert(mtxt);
								return false;
							}  //end try 
						} 
						
						__po_refresh_data();
					</script>
					";
					die();
				} else { 
					echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
					";
					die();
				}
			} else { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Item Data!!!.</div>";
				die();
			} //end if 
		} else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
			die();
		}
			
	}  //end save_nontrade	
	
	
}  //end main class 
