<?php
namespace App\Models;
use CodeIgniter\Model;

class MyPromoDiscountModel extends Model
{
    
    public function __construct() { 
        parent::__construct();
        $this->mydataz = model('App\Models\MyDatumModel');
        $this->myusermod = model('App\Models\MyUserModel');
        $this->db_erp = $this->myusermod->mydbname->medb(0);
        $this->myposconn = model('App\Models\MyPOSConnModel');
        $this->myposdbconn = $this->myposconn->connectdb();
        
    }  //end construct
    
    public function promo_entry_save() { 
      //variable declarations
      $cuser            = $this->myusermod->mysys_user();
      $mpw_tkn          = $this->myusermod->mpw_tkn();
      $mtkn_mntr = $this->myusermod->request->getVar('mtkn_mntr');
      $txt_promotrxno = $this->myusermod->request->getVar('txt_promotrxno');
      $txt_promoname = $this->myusermod->mylibzdb->me_escapeString($this->myusermod->request->getVar('txt_promodesc'));
      $branch_code = $this->myusermod->request->getVar('branch_code');
      $branch_name = $this->myusermod->request->getVar('branch_name');
      $start_date = $this->myusermod->request->getVar('start_date');
      $start_time = $this->myusermod->request->getVar('start_time');
      $end_date = $this->myusermod->request->getVar('end_date');
      $end_time = $this->myusermod->request->getVar('end_time');
      $ndiscvalue = $this->myusermod->request->getVar('ndiscvalue');
      $discount_srp = $this->myusermod->request->getVar('discount_srp');
      $is_fixed_price = $this->myusermod->request->getVar('is_fixed_price');
      $__hmtkn_fgpacktr = '';
      $prod_barcode = $this->myusermod->request->getVar('ART_BARCODE1');
      $is_disabled='FALSE';
      $is_approved='N';
      $is_bcodegen='N';
      $encd = '';
      $invalid_disc = '76';
      $is_fixed_price_checked =$this->myusermod->request->getVar('is_fixed_price_checked');
      $is_discount_percent_checked = $this->myusermod->request->getVar('is_discount_percent_checked');
      $is_discount_percent = $this->myusermod->request->getVar('is_discount_percent');
      $adata1 = $this->myusermod->request->getVar('adata1');
      $adata2 = $this->myusermod->request->getVar('adata2');
      $branch_code = $this->myusermod->request->getVar('branch_code');
      $mtkn_branch = $this->myusermod->request->getVar('mtkn_branch');
      $txt_branch_id = '';
      $cb_fix_value = $this->myusermod->request->getVar('cb_fix_value');
      $cb_fix_discount_percent_value = $this->myusermod->request->getVar('cb_fix_discount_percent_value');
	  $B_CODE = '';
	 

        //validate if branch is not selected
		if((!empty($branch_name)) && !empty($mtkn_branch)) {
			$str = "SELECT `recid`,`BRNCH_OCODE2` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
			$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			$rw = $q->getRowArray();
			$txt_branch_id = $rw['recid'];
			$B_CODE = $rw['BRNCH_OCODE2'];
			$B_CODE_POS = 'E' . trim($B_CODE);
			if (empty($B_CODE)): 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>ERROR</strong> Internal Branch POS CODE!!!.</div>";
				die();
			endif;
			$q->freeResult();
		
		}  
		else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
			die();
		} //end if 

		//validate date
		if(!empty($start_date) && !empty($end_date) && !empty($start_time) && !empty($end_time)) {

		}
		else {
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Dates or Time is required!!!.</div>";
			die();
		} // end if 

		if ($cb_fix_value == 0 & $cb_fix_discount_percent_value == 0) {
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please select a discount type!!!</div>";
			die();
		} // end if 
        if ($cb_fix_discount_percent_value == 1 & $ndiscvalue > 75) {
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Discount value must not be greater than 75% !!!</div>";
			die();
		} // end if 
		
		//validated if no product is inserted
		if(empty($adata1)) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
			die();
		}  // end if 
		
		$recid_HD = 0;
		//get initial records if exists
		if (!empty($mtkn_mntr)):
			$str = "select aa.`recid`,aa.`promo_trxno` from {$this->db_erp}.`trx_pos_promo_fpd_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_mntr'";
			$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->getRowArray();
			if($q->resultID->num_rows > 0 && !empty($rw['recid'])) { 
				$recid_HD = $rw['recid'];
				$promo_trxno = $rw['promo_trxno'];
			}
		endif;
		
		
		if(count($adata1) > 0) { 
			$ame = array();
			$adatar1 = array();
			$adatar2 = array();
			$ntqty = 0;
			$ntamt = 0;
			$total =0;
			$ninvalid = 0;
			for($aa = 0; $aa < count($adata1); $aa++) { 
				$medata = explode("x|x",$adata1[$aa]);
				$mitemc = trim($medata[0]);
				$mdesc = (trim($medata[1]));
				$mbcode = (trim($medata[2]));
				$mdisc = $ndiscvalue;
				$morigsrp = (trim($medata[3]));
				$mdiscsrp = (trim($medata[4]));
				$amatnr = array(); 

                if ($cb_fix_value == 1 & $ndiscvalue > $morigsrp ) {
                    echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> fix value discount must not be greater than original SRP!!!<br/>[$mitemc] </div>";
                    die();
                } // end if 


				if(!empty($mitemc) && !empty($mdisc)) { 
					$mat_mtkn = $adata2[$aa];
					
					$str = "select aa.recid,aa.ART_CODE,aa.ART_UCOST from {$this->db_erp}.`mst_article` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mat_mtkn' and ART_CODE = '$mitemc' ";
					$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$rwitem = $q->getRowArray();
					$nitemcost = $rwitem['ART_UCOST'];
					$mmat_rid = $rwitem['recid'];  
					
					
					if($q->resultID->num_rows == 0) {
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$cmat_code]</div>";
						die();
					} 
					
					$strr = "SELECT a.recid,a.`promo_trxno`,date_format(a.`start_date`,'%m/%d/%Y') `m_start_date`,date_format(a.`end_date`,'%m/%d/%Y') `m_end_date` 
					FROM {$this->db_erp}.`trx_pos_promo_fpd_hd` a 
					JOIN {$this->db_erp}.`trx_pos_promo_fpd_dt` b ON a.`branch_code` = b.`branch_code` WHERE a.`branch_code` = '$B_CODE' 
					and b.`prod_barcode` = '$mbcode' and 
					((date(a.`start_date`) between DATE('$start_date') and DATE('$end_date')) or (DATE(a.`end_date`) between DATE('$start_date')  and DATE('$end_date')) ) 
					group by a.recid,date(a.`start_date`),date(a.`end_date`) 
					" ;
					$qv = $this->myusermod->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$nrw = $qv->getNumRows();
					//skip when editing
					if ($recid_HD > 0):
						if ($nrw > 0): 
							$rv = $qv->getRowArray();
							//check if no other entries but have existing for editing
							if($nrw == 1 && $rv['recid'] == $recid_HD): 
							else:
								foreach ($qv->getResultArray() as $data) { 
									echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$mitemc] - [{$data['promo_trxno']}]</div>";
									die();
								}
							endif;
						endif;
						$qv->freeResult();
					else:
						if ($nrw > 0): 
							$rv = $qv->getRowArray();
							if(!empty($rv['recid'])) { 
								$qv->freeResult();
								echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$mitemc] - [$nrw]</div>";
								die();
							}
							$qv->freeResult(); 
						endif;
					endif;
					
					$adatac = $this->myposconn->POS_check_promo_exists($mitemc,$B_CODE_POS,$start_date . ' ' . $start_time,$end_date . ' ' .  $end_time);
					if (count($adatac) > 0):
						$pos_promocode = trim($adatac['code']);
						$pos_promotype = $adatac['me_promo_type'];
						$pos_start_date = substr($adatac['start_date'],0,19);
						$pos_end_date = substr($adatac['end_date'],0,19);
						
                        
						if (!empty($mtkn_mntr)):
							$nrecscount = $adatac['nrecsme'];
							if($nrecscount > 1): 
								echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing in Multipe POS Promo already!!! <br/> 
								[$mitemc]
								<br/>
								POS Promo Code: {$pos_promocode}
								<br/>
								POS Promo Type: {$pos_promotype}
								<br/>
								Start Date: " . $pos_start_date . "
								<br/>
								End Date: " . $pos_end_date . " 
								</div>";
								die();
							else:
								if ($promo_trxno !== $pos_promocode && !empty($pos_promocode)):
									echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing or conflict POS Promo already!!! <br/> 
									[$mitemc]
									<br/>
									POS Promo Code: {$pos_promocode}
									<br/>
									POS Promo Type: {$pos_promotype}
									<br/>
									Start Date: " . $pos_start_date . "
									<br/>
									End Date: " . $pos_end_date . " 
									</div>";
									die();
								endif;
							endif;
						else: 
							if (!empty($pos_promocode)):
								echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing in POS Promo already!!! <br/> 
								[$mitemc]
								<br/>
								POS Promo Code: {$pos_promocode}
								<br/>
								POS Promo Type: {$pos_promotype}
								<br/>
								Start Date: " . $pos_start_date . "
								<br/>
								End Date: " . $pos_end_date . " 
								</div>";
								die();
							endif;
						endif;
					endif;
					// end POS_check_promo_exists
					
					if ($cb_fix_discount_percent_value == 1) { 
						$total = ($morigsrp * ($mdisc / 100));
						if ($mdisc > $nitemcost) { 
							//echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Discount value must not be greater than 75% [{$mitemc}] {$total}</div>";
							//die();
						}
					}

					if ($cb_fix_value == 1) { 
						$total = ($morigsrp * 0.75);
						if ($mdisc > $nitemcost) { 
							//echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Fix Discount Amount value must not be less than 75% of Orig Srp [$mitemc]</div>";
							//die();
						}
						
					}
					
					array_push($ame,$mitemc); 
					array_push($adatar1,$medata);
					array_push($adatar2,$mmat_rid);

				} else { 
					$ninvalid = 1;
				} //end if
			}  //end for 
		} // end if(count($adata1) > 0) 
		
		if ($ninvalid > 0):
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalied Data ENTRIES!!!</div>";
			die();
		endif;

		//related updates/savings validation
        $recid = '';
        $promo_trxno = '';
        
		if(!empty($mtkn_mntr)) { 
			$str = "select aa.`recid`,aa.`promo_trxno` from {$this->db_erp}.`trx_pos_promo_fpd_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_mntr'";
			$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->resultID->num_rows == 0) {
				echo "No Records Found!!!";
				die();
			}
			$rw = $q->getRowArray();
			$recid_HD = $rw['recid'];
			$promo_trxno = $rw['promo_trxno'];
			$q->freeResult();
			$str = "
			update {$this->db_erp}.`trx_pos_promo_fpd_hd` set 
			`promo_name` = '$txt_promoname',
			`start_date` = '$start_date',
			`start_time` = '$start_time',
			`end_date` = '$end_date',
			`end_time`= '$end_time',
			`disc_value` = {$ndiscvalue},
			`is_fixed_price` = {$cb_fix_value},
			`is_discount_percent` = {$cb_fix_discount_percent_value} 
			where recid = '$recid_HD' 
			";
			$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
			
		} else { 
			$promo_trxno =  $this->mydataz->get_ctr_promotions('PD','',$this->db_erp,'CTRL_NO02'); //PROMO TRANSACTION NO
			$str = "
			insert into {$this->db_erp}.`trx_pos_promo_fpd_hd` (
			`promo_trxno`,
			`promo_name`,
			`branch_code`,
			`start_date`,
			`start_time`,
			`end_date`,
			`end_time`,
			`is_fixed_price`,
			`muser`,
			`encd_date`,
			`is_approved`,
			`is_bcodegen`,
			`is_discount_percent`,
			`disc_value`

			) values(
			'$promo_trxno',
			'$txt_promoname',
			'$B_CODE',
			'$start_date',
			'$start_time',
			'$end_date',
			'$end_time',
			'$cb_fix_value',
			'$cuser',
			now(),
			'$is_approved',
			'$is_bcodegen',
			'$cb_fix_discount_percent_value',
			{$ndiscvalue} 
			)
			";
			$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

			$str = "select recid,promo_trxno,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_attr from {$this->db_erp}.`trx_pos_promo_fpd_hd` aa where `promo_trxno` = '$promo_trxno' ";
			$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
			$rw = $q->getRowArray();
			$recid_HD = $rw['recid'];
			$promo_trxno = $rw['promo_trxno'];
			$q->freeResult();
		}  //end if 
		
		
		//insert details data
		for($xx = 0; $xx < count($adatar1); $xx++) { 
			$xdata = $adatar1[$xx];
			$mitemc = $xdata[0];
			$mat_rid = $adatar2[$xx];
			$mdesc = $xdata[1];
			$mbcode = $xdata[2];
			$mdisc = $ndiscvalue;
			$morigsrp = $xdata[3];
			$mdiscsrp = $xdata[4];
			$riddtmtkn = $xdata[5];
			
			$str = "select aa.recid,aa.ART_CODE,aa.ART_UCOST,aa.ART_UPRICE from {$this->db_erp}.`mst_article` aa where aa.recid = {$mat_rid} ";
			$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rwitem = $q->getRowArray();
			$nitemcost = $rwitem['ART_UCOST'];
			$nitemuprice = $rwitem['ART_UPRICE'];
			$q->freeResult();
			
			if ($riddtmtkn == 'undefined'): 
				$str = "select `recid` from {$this->db_erp}.`trx_pos_promo_fpd_dt` where `promo_trxno` = '$promo_trxno' and `branch_code` = '$B_CODE' and 
				prod_barcode = '$mbcode' and `mat_rid` = {$mat_rid} ";
				$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
				$rwdt = $q->getRowArray();
				if ($q->getNumRows() > 0 && !(empty($rwdt['recid']))) { 
					$str = "update {$this->db_erp}.`trx_pos_promo_fpd_dt` set `discount_value` = {$mdisc} 
					where `recid` = {$rwdt['recid']}";
				} else  { 
					$str = "
					insert into {$this->db_erp}.`trx_pos_promo_fpd_dt` ( 
					`promo_trxno`,
					`discount_value`,
					`discount_srp`,
					`prod_barcode`,
					`is_disabled`,
					`muser`,
					`encd`,
					`mat_rid`, 
					`promohd_rid`,
					`branch_code`,`mcost`,`mprice`
					) values(
					'$promo_trxno',
					'$mdisc',
					'$mdiscsrp',
					'$mbcode',
					'$is_disabled',
					'$cuser',
					now(),
					'$mat_rid',
					'$recid',
					'$B_CODE',{$nitemcost},{$nitemuprice} 
					)
					";
				} //end if 
				$q->freeResult();
				$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
			else:
				$str = "select `recid` from {$this->db_erp}.`trx_pos_promo_fpd_dt` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) =  '$riddtmtkn'";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
				$rwdt = $q->getRowArray();
				if ($q->getNumRows() > 0 && !(empty($rwdt['recid']))): 
					$str = "select `recid`,prod_barcode from {$this->db_erp}.`trx_pos_promo_fpd_dt` where `promo_trxno` = '$promo_trxno' and `branch_code` = '$B_CODE' and 
					prod_barcode = '$mbcode' and `mat_rid` = {'$mat_rid'} ";
					$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
					$rrwdt = $qq->getRowArray();
					$prod_barcode = $rrwdt['prod_barcode'];
					if (($rwdt['recid'] !== $rrwdt['recid']) && !empty($rrwdt['recid'])): 
						$str = "update {$this->db_erp}.`trx_pos_promo_fpd_dt` set `discount_value` = {$mdisc},
						prod_barcode = '$mbcode',`mcost` = {$nitemcost},`mprice` = {$nitemuprice},
						`mat_rid` = {'$mat_rid'}
						where `recid` = {$rwdt['recid']}";
						$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
					else:
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>ERROR<br/></strong><strong>Error</strong> Data CONFLICT DETECTED [{$prod_barcode}]!!!</div>";
						die();
					endif;
					$qq->freeResult();
				endif;
				$q->freeResult();
			endif;
		}  //end for count($adatar1)
		
		$this->save_promo_fpd_from_POS($promo_trxno,$txt_promoname,$ndiscvalue,$cb_fix_value,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
		
		
		$mtknattr = hash('sha384', $recid_HD . $mpw_tkn); 
		
		echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Promo Discount No:{$promo_trxno} </div>
		<script type=\"text/javascript\"> 
		function __set_trx_fpd_data() { 
			try { 
				jQuery('#txt_promotrxno').val('{$promo_trxno}');
				jQuery('#txt_promotrxno').attr('data-mtknattr','{$mtknattr}');
				jQuery('#mbtn_mn_Save').prop('disabled',true);
			} catch(err) { 
				var mtxt = 'There was an error on this page.\\n';
				mtxt += 'Error description: ' + err.message;
				mtxt += '\\nClick OK to continue.';
				alert(mtxt);
				return false;
			}  //end try 
		} 
		__set_trx_fpd_data();
		</script>
		";
		die();
    } //end promo_entry_save
 
 	public function save_promo_fpd_from_POS($promo_trxno,$txt_promoname,$ndiscvalue,$cb_fix_value,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time) { 
		$lcon = 0;
		
		if ($this->myposdbconn):
			$str_items = '';
			for($aa = 0; $aa < count($adata1); $aa++):
				$medata = explode("x|x",$adata1[$aa]);
				$mitemc = trim($medata[0]);
				$mdesc = (trim($medata[1]));
				$mbcode = (trim($medata[2]));
				$str_items .= $mitemc . ',';
			endfor;
			$str_items = "'" . substr($str_items,0,strlen($str_items)-1) . "'";
			
			//group items as comma delimited as required 
			$str = "select STRING_AGG(aa.[id], ',') AS meiditems  from [diQtech_db].[dbo].[diQt_Product] aa join ( 
			SELECT DISTINCT CAST(value AS varchar) AS meproditemc FROM STRING_SPLIT({$str_items}, ',') WHERE value != ''
			) bb on (aa.[stock_no] = bb.meproditemc)";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array(), array("Scrollable"=>"buffered") );
			if( $stmt === false) {
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$row_count = sqlsrv_num_rows( $stmt );
			$meproditems = '';
			if($row_count > 0) {
				$nn = 1;
				while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
					//echo $row['meiditems'] . '<br/>';
					$meproditems = $row['meiditems'];
				}
			} else { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>No Record/s found [POS_ITEM]!!!</strong></div>";
			}
			sqlsrv_free_stmt( $stmt);
			
			//get the branch id 
			$str = "select [id] from [diQtech_db].[dbo].[diQt_Branch] where [code] = ?";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array($B_CODE_POS), array("Scrollable"=>"buffered") );
			if( $stmt === false) { 
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$row_count = sqlsrv_num_rows( $stmt );
			$brid = 0;
			if($row_count > 0) {
				$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
				$lcon = 1;
				$brid = $row['id'];
				//echo "Branch ID: " . $row['id'] . '<br/>';
			}
			sqlsrv_free_stmt( $stmt);
			
			//header promo fixed/discount 
			$promoid = 0;
			$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDiscount] where [code] = ?";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promo_trxno), array("Scrollable"=>"buffered") );
			if( $stmt === false) { 
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$row_count = sqlsrv_num_rows( $stmt );
			if ($row_count > 0):
				$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
				$lcon = 1;
				$promoid = $row['id'];
				$str = "
				update [diQtech_db].[dbo].[diQt_PromoDiscount] set 
				[name] = ?,
				[start_date] = ?,
				[end_date] = ?,
				[is_discount_percent] = ?,
				[is_discount_amount] = ?,
				[is_fixed_price] = ?,
				[value] = ?,
				[product_ids] = ?,
				[date_changed] = getdate() 
				where [id] = ?
				";
				$mstart_date = $start_date . ' ' . $start_time;
				$mend_date = $end_date . ' ' . $end_time;
				//echo "me date: " . $mstart_date . ' === ' . $mend_date;
				$is_discount_percent = 0;
				$is_discount_amount = 0;
				$is_fixed_price = 0;
				if ($cb_fix_value):
					$is_fixed_price = 1;
				else:
					$is_discount_percent = 1;
				endif;
				$mevalarray = array($txt_promoname,$mstart_date,$mend_date,$is_discount_percent,$is_discount_amount,
				$is_fixed_price,$ndiscvalue,$meproditems,$promoid);
				$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
				if( $stmt === false) { 
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				}
				sqlsrv_free_stmt( $stmt);
				
			else: 
				//get the incremental id prior adding of records 
				$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoDiscount]), 0) + 1 as me_rec_id";
				$stmt = sqlsrv_query( $this->myposdbconn, $str,array(), array("Scrollable"=>"buffered") );
				if( $stmt === false) { 
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				}
				$row_count = sqlsrv_num_rows( $stmt );
				if($row_count > 0) {
					$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
					sqlsrv_free_stmt( $stmt);
					$promoid = $row['me_rec_id'];
					$str = "
					insert into [diQtech_db].[dbo].[diQt_PromoDiscount] ([id]
					,[code]
					,[name]
					,[start_date]
					,[end_date]
					,[is_discount_percent]
					,[is_discount_amount]
					,[is_fixed_price]
					,[value]
					,[product_ids]
					,[date_changed]
					,[disable]
					) values (?,?,?,?,?,?,?,?,?,?,getdate(),?)
					";
					$mstart_date = $start_date . ' ' . $start_time;
					$mend_date = $end_date . ' ' . $end_time;
					$is_discount_percent = 0;
					$is_discount_amount = 0;
					$is_fixed_price = 0;
					if ($cb_fix_value):
						$is_fixed_price = 1;
					else:
						$is_discount_percent = 1;
					endif;
					$mevalarray = array($promoid,$promo_trxno,$txt_promoname,$mstart_date,$mend_date,$is_discount_percent,$is_discount_amount,
					$is_fixed_price,$ndiscvalue,$meproditems,0);
					$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
					if( $stmt === false) { 
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					}
					sqlsrv_free_stmt( $stmt);
				}
			endif;
			
			//branch detail table promo fixed/discount 
			if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
				$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDiscountBranch] where [promo_discount_id] = ? and 
				[branch_id] = ?";
				$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoid,$brid), array("Scrollable"=>"buffered") );
				if ( $stmt === false):
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ );
				endif;
				$row_count = sqlsrv_num_rows( $stmt );
				//update if existing 
				if ($row_count > 0):
					$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
					$promoidbr = $row['id'];
					$str = "
					update [diQtech_db].[dbo].[diQt_PromoDiscountBranch] set [date_changed] = getdate() 
					where [id] = ? 
					";
					$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
					if ( $stmt === false):
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					endif;
					sqlsrv_free_stmt( $stmt);
				else:
					//add records 
					$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoDiscountBranch]), 0) + 1 as me_rec_id";
					$stmt = sqlsrv_query( $this->myposdbconn, $str,array(), array("Scrollable"=>"buffered") );
					if ( $stmt === false):
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					endif;
					$row_count = sqlsrv_num_rows( $stmt );
					if ($row_count > 0):
						$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
						sqlsrv_free_stmt( $stmt);
						$promoidbr = $row['me_rec_id'];
						$str = "
						insert into [diQtech_db].[dbo].[diQt_PromoDiscountBranch] (
						[id]
						,[promo_discount_id]
						,[branch_id]
						,[date_changed]
						,[disable]
						) values (?,?,?,getdate(),?) 
						";
						$mevalarray = array($promoidbr,$promoid,$brid,0);
						$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
						if ( $stmt === false):
							die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						endif;
						sqlsrv_free_stmt( $stmt);
						
					endif;
				endif;
			endif;
		endif;
	} //end check_promo_from_POS

    //start post view
    public function promo_post_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        //variable declarations
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->myusermod->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->myusermod->mylibzdb->escapeString($msearchrec);
            $str_optn = " AND
            a.`promo_trxno` LIKE '%{$msearchrec}%' ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`trx_pos_promo_fpd_hd` a
        WHERE a.`is_approved` = 'N'
        {$str_optn} 
        ";
        
        // var_dump($strqry);
        
        $str = "
        select count(*) __nrecs from ({$strqry}) oa
        ";
        $qry = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $rw = $qry->getRowArray();
        $npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
        $nstart = ($npagelimit * ($npages - 1));
        
        
        $npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
        $data['npage_count'] = $npage_count;
        $data['npage_curr'] = $npages;
        $str = "
        SELECT * from ({$strqry}) oa limit {$nstart},{$npagelimit} ";
        $qry = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
        if($qry->resultID->num_rows > 0) { 
            $data['rlist'] = $qry->getResultArray();
        } else { 
            $data = array();
            $data['npage_count'] = 1;
            $data['npage_curr'] = 1;
            $data['rlist'] = '';
        }
        return $data;

    } //end post view

    //start fgpack update approval
    public function promo_for_approval() {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_recid = $this->myusermod->request->getVar('mtkn_recid');
        $promo_trxno = '';
        
        if(!empty($mtkn_recid)) { 
            //SELECT IF ALREADY POSTED
            $str = "select is_approved,promo_trxno from {$this->db_erp}.`trx_pos_promo_fpd_hd` aa WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_recid' AND `is_approved` = 'N'";
            $qry = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($qry->resultID->num_rows == 0) { 
                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
                die();
            }
            else{
                $rr = $qry->getRowArray();
                $promo_trxno = $rr['promo_trxno'];
            }
            $str = "
            update {$this->db_erp}.`trx_pos_promo_fpd_hd`
            SET `is_approved` = 'Y',
            `date_approved` = now()
            WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_recid'
            AND `is_approved` = 'N';
            ";
            $qry = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            echo  "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Approved Successfully!!!</div>";
        }//endif
        
    }//end  fgpack update approval

    //start download barcode
    public function download_promo_barcode($promo_trxno){
        //declaire variables
        $cfilelnk='';
        $file_name='';
        $chtmljs='';
        $discount_name='';
        $discount_chosen='';
        $percentsign='';
        $cuser       = $this->myusermod->mysys_user();
        $mpw_tkn     = $this->myusermod->mpw_tkn();
        $chtmljs ="";

        //check if there is an existing transaction number
        $str = "
        SELECT 
        a.`recid`,a.`promo_trxno`
        FROM
        {$this->db_erp}.`gw_promo_hd` a
        WHERE
        a.`promo_trxno` = '{$promo_trxno}'
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        //validated if trx is existing
        if($qry->resultID->num_rows == 0) { 
            $data = "<div class=\"alert alert-danger mb-0\"><strong>Invalid Input</strong><br>Invalid Promo Number.</div>";
            echo $data;
            die();
        }
        else{
            $rr = $qry->getRowArray();
            $promo_trxno = $rr['promo_trxno'];
        }

        //setup file
        if($promo_trxno != ''){
            $file_name = 'promo_discount_'.$promo_trxno.'_'.$cuser.'_'.date('Ymd').$this->mylibzsys->random_string(15);
            $mpathdn   = ROOTPATH;
            $_csv_path = '/public/downloads/me/';
            //if(!is_dir($_csv_path)) mkdir($_csv_path, '0755', true);
            $filepath = $mpathdn.$_csv_path.$file_name.'.csv';
            $cfilelnk = site_url() . 'downloads/me/' . $file_name.'.csv'; 

            $strr ="
            Select 
            a.`promo_trxno`,
            a.`is_fixed_price`,
            a.`is_discount_percent`,
            b.`discount_value`
            from
            gw_promo_hd a 
            join
            gw_promo_dt b
            on
            a.`promo_trxno` = b.`promo_trxno`
            where a.`promo_trxno` = '$promo_trxno'
            ";
            $qq = $this->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
            $rw = $qq->getRowArray();
            $is_fixed = $rw['is_fixed_price'];
            $is_perc = $rw['is_discount_percent'];
            $discount_value = $rw['discount_value'];

            if ($is_fixed == 1) {
                $discount_name = 'Everything @ ';
            }
            if ($is_perc) {
                $discount_name = 'Less ';
                $percentsign = '%';
            }

            //generate hd and dt data to file format
            $str = "
            SELECT *
            INTO OUTFILE '{$filepath}'
            FIELDS TERMINATED BY '\t'
            LINES TERMINATED BY '\r\n'
            FROM(
                SELECT 
                'Branch Code',
                'PromoDiscountCode',
                'PromoDiscount_Name',
                'start_date',
                'end_date',
                'is_discount_percent',
                'is_discount_amount',
                'is_fixed_price',
                'value',
                'Product Barcode',
                'disable'

                UNION ALL
                
                SELECT
                CONCAT('E',a.`branch_code`),CONCAT(a.`promo_trxno`,'-',b.`discount_value`),CONCAT('$discount_name',b.`discount_value`,'$percentsign'), CONCAT(a.`start_date`,' ',a.`start_time`),CONCAT(a.`end_date`,' ',a.`end_time`),a.`is_discount_percent`,b.`is_discount_amount`, a.`is_fixed_price`,
                b.`discount_value`, b.`prod_barcode`, b.`is_disabled`
                FROM
                {$this->db_erp}.`gw_promo_hd` a
                JOIN 
                {$this->db_erp}.`gw_promo_dt` b
                ON 
                a.`promo_trxno` = b.`promo_trxno`
                WHERE
                a.`promo_trxno` = '{$promo_trxno}'
                
                ) oa
            ";

            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
        }//endif
        
        $chtmljs .= "
        <a href=\"{$cfilelnk}\" download=" . $file_name ." class='btn btn-dgreen btn-sm col-lg-12'> <i class='bi bi-save'></i> DOWNLOAD IT!</a>        
        ";
        echo $chtmljs;
    }//end download barcode

    public function promo_dashboard_rec_view($npages = 1,$npagelimit = 50,$msearchrec='') {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " WHERE
            (a.`promo_trxno` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT b.`BRNCH_NAME`, a.`branch_code`, a.`start_date`, a.`end_date` 
        FROM gw_promo_hd a 
        JOIN mst_companyBranch b 
        ON a.`branch_code` = b.`BRNCH_OCODE2`

        {$str_optn}
        ";
        
        // var_dump($strqry);
        
        $str = "
        select count(*) __nrecs from ({$strqry}) oa
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $rw = $qry->getRowArray();
        $npagelimit = ($npagelimit > 0 ? $npagelimit : 50);
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
    } 

    public function promo_rec_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->myusermod->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->myusermod->mylibzdb->escapeString($msearchrec);
            $str_optn = " WHERE
            (a.`promo_trxno` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`trx_pos_promo_fpd_hd` a
        {$str_optn}
        ";
             
        $str = "
        select count(*) __nrecs from ({$strqry}) oa
        ";
        $qry = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $rw = $qry->getRowArray();
        $npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
        $nstart = (($npagelimit * ($npages - 1)) > 0 ? ($npagelimit * ($npages - 1)) : 0);
        
        
        $npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
        $data['npage_count'] = $npage_count;
        $data['npage_curr'] = $npages;
        $str = "
        SELECT * from ({$strqry}) oa order by `promo_trxno` desc limit {$nstart},{$npagelimit} ";
        $qry = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
        if($qry->resultID->num_rows > 0) { 
            $data['rlist'] = $qry->getResultArray();
        } else { 
            $data = array();
            $data['npage_count'] = 1;
            $data['npage_curr'] = 1;
            $data['rlist'] = '';
        }
        $qry->freeResult();
        return $data;
    } // end promo_rec_view

	//PROMO DISCOUNT MODEL END
	
	public function __search_artmaster() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$term = $this->myusermod->mylibzdb->me_escapeString($this->myusermod->request->getVar('term'));
		$autoCompleteResult = array();
		
        $mtkn_branch = $this->myusermod->request->getVar('mtkn_branch'); 
        $branch_name = $this->myusermod->mylibzdb->me_escapeString($this->myusermod->request->getVar('branch_name')); 
       	$str_branch ="";
       	$BRNCH_MAT_FLAG ='';
       	if(!empty($branch_name)){
       		$str = "select recid,BRNCH_NAME,BRNCH_CODE,BRNCH_OCODE2,BRNCH_MAT_FLAG 
       		from {$this->db_erp}.`mst_companyBranch` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_branch' and `BRNCH_NAME` = '$branch_name'";
       		$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
       		//$this->myusermod->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
       		$rw = $q->getRowArray();
       		if (empty($rw['BRNCH_MAT_FLAG'])): 
       			array_push($autoCompleteResult,array(
       				"mtkn_rid" => '',
       				"value" => 'ERROR BRANCH TAGGING',
       				"_DESC" => '',  
       				"_BARCODE" => '',  
       				"_SKU" => '', 
       				"_SDU" => '', 
       				"_IMG" => '',
       				"_NCONVF" => '',
       				"_UPRICE" => '',
       				"_UCOST" => '',  
       				"_CODE" => '',
       				"_NCBM" => '',
       				"_MATRID" => '',
       			));
       			echo json_encode($autoCompleteResult);
				die();
       		endif;
       		$BRNCH_MAT_FLAG = $rw['BRNCH_MAT_FLAG'];
       		$fld_branch_recid = $rw['recid'];
       		$str_branch = "AND kk.`brnchID` = {$fld_branch_recid} ";
       		
       		$q->freeResult();
			//END BRANCH
       	} 
       	
       	if($BRNCH_MAT_FLAG === 'G') {
       		$str = "
       		SELECT 
       		a.`recid`,
       		sha2(concat(a.recid,'{$mpw_tkn}'),384) `mtkn_attr`,
       		a.`ART_DESC`,
       		trim(a.`ART_CODE`) __mdata,
       		trim(a.`ART_BARCODE1`) __barcode1,
       		a.`ART_SKU`,
       		a.`ART_SDU`,
       		a.`ART_IMG`,
       		a.`ART_NCBM`,
			a.`ART_UOM`,
       		a.`ART_NCONVF`,
       		IFNULL(kk.`art_uprice`,a.`ART_UPRICE`) ART_UPRICE,
       		IFNULL(kk.`art_cost`,a.`ART_UCOST`) ART_UCOST 
       		FROM {$this->db_erp}.`mst_article`  a
       		JOIN `mst_article_per_branch` kk
       		ON (a.`recid` = kk.`artID`) 
       		WHERE a.`ART_ISDISABLE` = 0 {$str_branch} 
       		AND (a.`ART_CODE` like '%$term%' or a.`ART_DESC` like '%$term%' or a.`ART_BARCODE1` like '%$term%') 
       		ORDER BY a.`ART_DESC` LIMIT 50 
       		";
       	} else { 
       		$str = "
       		SELECT aa.`recid`,sha2(concat(aa.recid,'{$mpw_tkn}'),384) `mtkn_attr`,aa.`ART_DESC`,trim(aa.`ART_CODE`) __mdata,
       		trim(aa.`ART_BARCODE1`) __barcode1,
       		aa.`ART_UOM`,aa.`ART_SKU`,aa.`ART_SDU`,aa.`ART_IMG`,aa.`ART_NCBM`,aa.`ART_NCONVF`,aa.`ART_UPRICE`,aa.`ART_UCOST` 
       		FROM {$this->db_erp}.`mst_article` aa
       		WHERE aa.`ART_ISDISABLE` = 0 
       		AND (aa.`ART_CODE` like '%$term%' or aa.`ART_DESC` like '%$term%' or aa.`ART_BARCODE1` like '%$term%') 
       		ORDER BY aa.`ART_DESC` LIMIT 50";
		}
       	$q =  $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
       	if($q->getNumRows() > 0) { 
       		$rrec = $q->getResultArray();
       		foreach($rrec as $row):
       			$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn); 
       			array_push($autoCompleteResult,array(
       				"mtkn_rid" => $mtkn_rid,
       				"value" => $row['__mdata'],
       				"_DESC" => $row['ART_DESC'],  
       				"_BARCODE" => $row['__barcode1'],  
       				"_SKU" => $row['ART_SKU'], 
       				"_SDU" => $row['ART_SDU'], 
       				"_IMG" => $row['ART_IMG'],
       				"_NCONVF" => $row['ART_NCONVF'],
       				"_UPRICE" => $row['ART_UPRICE'],
       				"_UCOST" => $row['ART_UCOST'],  
       				"_CODE" => $row['__mdata'],
       				"_NCBM" => $row['ART_NCBM'],
       				"_MATRID" => $row['mtkn_attr'],
					"_UOM" => $row['ART_UOM'],
       			));
       		endforeach;
       	}
       	$q->freeResult();
       	echo json_encode($autoCompleteResult);
		
	} //end __search_artmaster
	

} //end main class MyPromoDiscountModel