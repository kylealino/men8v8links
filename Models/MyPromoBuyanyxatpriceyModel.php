<?php
namespace App\Models;
use CodeIgniter\Model;

class MyPromoBuyanyxatpriceyModel extends Model
{
    
    public function __construct(){
        parent::__construct();
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mylibzdb = model('App\Models\MyLibzDBModel');
        $this->mylibzsys = model('App\Models\MyLibzSysModel');
        $this->mydataz = model('App\Models\MyDatumModel');
        $this->myusermod = model('App\Models\MyUserModel');
        $this->dbx = $this->mylibzdb->dbx;
        $this->request = \Config\Services::request();
        $this->myposconn = model('App\Models\MyPOSConnModel');
        $this->myposdbconn = $this->myposconn->connectdb();
    }//end construct
    
    

    public function buyanyatprice_entry_save() { 
        
        //variable declarations
      $cuser            = $this->myusermod->mysys_user();
      $mpw_tkn          = $this->myusermod->mpw_tkn();
      $mtkn_mntr = $this->request->getVar('mtkn_mntr');
      $branch_name = $this->request->getVar('branch_name');
      $start_date = $this->request->getVar('start_date');
      $start_time = $this->request->getVar('start_time');
      $end_date = $this->request->getVar('end_date');
      $end_time = $this->request->getVar('end_time');
      $is_fixed_price = $this->request->getVar('is_fixed_price');
      $txt_quantity = $this->request->getVar('txt_quantity');
      $txt_price = $this->request->getVar('txt_price');
      $promo_name = $this->request->getVar('promo_name');
      $__hmtkn_fgpacktr = '';
      $prod_barcode = $this->request->getVar('ART_BARCODE1');
      $is_disabled='FALSE';
      $is_approved='N';
      $is_bcodegen='N';
      $encd = '';
      $invalid_disc = '76';
      $adata1 = $this->request->getVar('adata1');
      $adata2 = $this->request->getVar('adata2');
      $mtkn_branch = $this->request->getVar('mtkn_branch');
      $txt_branch_id = '';
      $B_CODE ='';
    
      
     
      
      if((!empty($branch_name)) && !empty($mtkn_branch)) {
        $str = "SELECT `recid`,`BRNCH_OCODE2` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
        $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        $rw = $q->getRowArray();
        $txt_branch_id = $rw['recid'];
        $B_CODE = trim($rw['BRNCH_OCODE2']);
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
        else{
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Dates or Time is required!!!.</div>";
            die();
        }

      

                //validated if no product is inserted
        if(empty($adata1)) { 
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
            die();
        }

        
        $recid = '';
        $buyanyatprice_trxno = '';
        
                //UPDATE
        if(!empty($mtkn_mntr)) { 
                //CHECK IF VALID PO
            
            $str = "select aa.`id`,aa.`buyanyxatpricey_trxno` from {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` aa where buyanyxatpricey_trxno = '$mtkn_mntr'";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            if($q->resultID->num_rows == 0) {
                echo "test[$mpw_tkn]-";
                echo $mtkn_mntr;
                echo "No Records Found!!!";
                die();
            }
            $rw = $q->getRowArray();
            $recid = $rw['id'];
            $buyanyatprice_trxno = $rw['buyanyxatpricey_trxno'];
            $q->freeResult();

            }//endif
           
            //generate promo transaction number
            else{
                
                $buyanyatprice_trxno =  $this->mydataz->get_ctr_promotions('PBAXPY','',$this->db_erp,'CTRL_NO03');//PBXTY TRANSACTION NO

            } //end else

            //validate if there is a valid material data
            if(count($adata1) > 0) { 
                $ame = array();
                $adatar1 = array();
                $adatar2 = array();
                $ntqty = 0;
                $ntamt = 0;
                $total =0;
                
                for($aa = 0; $aa < count($adata1); $aa++) { 
                    $medata = explode("x|x",$adata1[$aa]);
                    $mitemc = trim($medata[0]);
                    
                    $mdesc = $medata[1];
                    $mbcode = $medata[2];
           
                    $amatnr = array();
                    ;

                    if(empty($txt_price) && empty($txt_quantity)){
                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Quanty or Take !!!<br/></div>";
                        die();
                    }

                   
                    
                    if(!empty($mitemc)) { 
                        // $mat_mtkn = $adata2[$aa];
                        
                        // aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mat_mtkn'
                        $str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where ART_CODE = '$mitemc' ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        
                        if($q->resultID->num_rows == 0) {
                            
                           
                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data !!!<br/></div>";
                          
                        }//end if
                       
                    $strr = "SELECT a.id,a.`buyanyxatpricey_trxno`,date_format(a.`start_date`,'%m/%d/%Y') `m_start_date`,date_format(a.`end_date`,'%m/%d/%Y') `m_end_date` 
					FROM {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` a  
					JOIN {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_dt` b 
                    ON a.`buyanyxatpricey_trxno` = b.`buyanyxatpricey_trxno`
                     WHERE a.`branch_code` = '$B_CODE' 
					and b.`product_barcode` = '$mbcode' and 
					((date(a.`start_date`) between DATE('$start_date') and DATE('$end_date')) or (DATE(a.`end_date`) between DATE('$start_date')  and DATE('$end_date')) ) 
					group by a.id,date(a.`start_date`),date(a.`end_date`) 
					" ;
					$qv = $this->myusermod->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$nrw = $qv->getNumRows();
					//skip when editing
					if ($recid > 0):
						if ($nrw > 0): 
							$rv = $qv->getRowArray();
							//check if no other entries but have existing for editing
                         
                           
							if($nrw == 1 && $rv['id'] == $recid): 
							else:
								foreach ($qv->getResultArray() as $data) { 
                              
									echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$mitemc] - [{$data['buyanyxatpricey_trxno']}]</div>";
									die();
								}
							endif;
						endif;
						$qv->freeResult();
					else:
						if ($nrw > 0): 
							$rv = $qv->getRowArray();
							if(!empty($rv['id'])) { 
								$qv->freeResult();
								echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$mitemc] - [$nrw]</div>";
								die();
							}
							$qv->freeResult(); 
						endif;
					endif;
                        // validation
                        $rw = $q->getRowArray(); 
                        $mmat_rid = $rw['recid'];  
                        array_push($ame,$mitemc); 
                        array_push($adatar1,$medata);
                        array_push($adatar2,$mmat_rid);
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
								if ($buyanyatprice_trxno !== $pos_promocode && !empty($pos_promocode)):
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

                    }//end if

                    
                }  //end for 
                
                //insert header data
                if(count($adatar1) > 0) { 
                    if(!empty($mtkn_mntr)) {       
                       $str = "
                       update {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` set 
                       `branch_code` = '$B_CODE',
                       `start_date` = '$start_date',
                       `start_time` = '$start_time',
                       `end_date` = '$end_date',
                       `end_time`= '$end_time',
                       `quantity`= '$txt_quantity',
                       `price`= '$txt_price',
                       `promo_name`= '$promo_name'

                       where id = '$recid' 
                       ";
                       $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                       
                   } else {  
                  
                    $str = "
                    insert into {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` (
                    `buyanyxatpricey_trxno`,
                    `branch_code`,
                    `start_date`,
                    `start_time`,
                    `end_date`,
                    `end_time`,
                    `promo_name`,
                    `product_barcode`,
                    `quantity`,
                    `price`,
                    `encd_date`
                    

                    ) values(
                    '$buyanyatprice_trxno',
                    '$B_CODE',
                    '$start_date',
                    '$start_time',
                    '$end_date',
                    '$end_time',
                    '$promo_name',
                    '$mbcode',
                    '$txt_quantity',
                    '$txt_price',
                     now()
                    )
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

                          //GET ID
                    $str = "select id,sha2(concat(aa.id,'{$mpw_tkn}'),384) mtkn_fgpacktr from {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` aa where `buyanyxatpricey_trxno` = '$buyanyatprice_trxno' ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                    $rr = $q->getRowArray();
                    $q->freeResult();
                } // end for
                    
                    //insert details data
                  
                    for($xx = 0; $xx < count($adatar1); $xx++) { 
                        
                        $xdata = $adatar1[$xx];
                        $mitemc = $xdata[0];
                        $mat_rid = $adatar2[$xx];
                        $mdesc = $xdata[1];
                        $mbcode = $xdata[2];
            
                        $str1 = "
                        SELECT `product_barcode`,  `buyanyxatpricey_trxno` FROM {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_dt` WHERE `buyanyxatpricey_trxno` = '$buyanyatprice_trxno' AND `product_barcode` = '$mbcode' 
                        
                        ";
                        

                        $qv = $this->myusermod->mylibzdb->myoa_sql_exec($str1,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					    $getrw = $qv->getNumRows();
                        $qv->freeResult();
                        if(!empty($mtkn_mntr) && $getrw == 0) {  
                            $str = "
                            insert into {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_dt` ( 
                            `buyanyxatpricey_trxno`,
                            `item_code`,
                            `item_description`,
                            `product_barcode`
                            ) values(
                            '$buyanyatprice_trxno',
                            '$mitemc',
                            '$mdesc',
                            '$mbcode'
                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        } 
                    
                       
                       
                        if(empty($mtkn_mntr) ) { 
                            $str = "
                            insert into {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_dt` ( 
                                `buyanyxatpricey_trxno`,
                                `item_code`,
                                `item_description`,
                                `product_barcode`
                                ) values(
                                '$buyanyatprice_trxno',
                                '$mitemc',
                                '$mdesc',
                                '$mbcode'
                                )
                                ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        }
                        
                    }  //end for 
                    
                    //show success modal
                    if(empty($mtkn_mntr)) { 
                        $this->save_promo_buyanyatprice_from_POS($buyanyatprice_trxno,$promo_name,$txt_quantity,$txt_price,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                        // $this->save_promo_buy1take1_from_POS($buy1take1_trxno,$promo_name,$cb_fix_value,$qty,$take,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                        echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Buy Any At Price No.:{$buyanyatprice_trxno} </div>
                        <script type=\"text/javascript\"> 
                        function __fg_refresh_data() { 
                            try { 
                                $('#txt_buyanyatpricetrxno').val('{$buyanyatprice_trxno}');
                                $('#__hmpacktrxnoid').val('{$__hmtkn_fgpacktr}');
                                
                                jQuery('#mbtn_mn_Save').prop('disabled',true);
                                } catch(err) { 
                                    var mtxt = 'There was an error on this page.\\n';
                                    mtxt += 'Error description: ' + err.message;
                                    mtxt += '\\nClick OK to continue.';
                                    alert(mtxt);
                                    return false;
                                    }  //end try 
                                } 
                                
                                __fg_refresh_data();
                                </script>
                                ";
                                die();
                            } else { 
                                $this->save_promo_buyanyatprice_from_POS($buyanyatprice_trxno,$promo_name,$txt_quantity,$txt_price,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                                // $this->save_promo_buy1take1_from_POS($buy1take1_trxno,$promo_name,$cb_fix_value,$qty,$take,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                                echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!! </div>
                                ";
                                die();
                            }
                        } else { 
                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Material Data!!!.</div>";
                            die();
                            
            } //end if 
            
        } else { 
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
            die();
        }
       
        
	} //end promo_entry_save

    public function buyanyatprice_rec_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " WHERE
            (a.`buyanyxatpricey_trxno` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` a
        {$str_optn}
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
    } // end buy1take1_rec_view
 
    

   
        
    public function save_promo_buyanyatprice_from_POS($buyanyatprice_trxno,$promo_name,$txt_quantity,$txt_price,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time){
		$lcon = 0;
        $promo_name = $this->request->getVar('promo_name');
       
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
			$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoBuyAnyXPriceY] where [code] = ?";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array($buyanyatprice_trxno), array("Scrollable"=>"buffered") );
			if( $stmt === false) { 
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$row_count = sqlsrv_num_rows( $stmt );
			if ($row_count > 0):
				$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
				$lcon = 1;
				$promoid = $row['id'];
				$str = "
				update [diQtech_db].[dbo].[diQt_PromoBuyAnyXPriceY] set 
				[name] = ?,
				[start_date] = ?,
				[end_date] = ?,
				[quantity] = ?,
				[price] = ?,
				[product_ids] = ?,
				[date_changed] = getdate() 
				where [id] = ?
				";
				$mstart_date = $start_date . ' ' . $start_time;
				$mend_date = $end_date . ' ' . $end_time;
			 
				$mevalarray = array($promo_name,$mstart_date,$mend_date,$txt_quantity,$txt_price,
				$meproditems,$promoid);
				$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
				if( $stmt === false) { 
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				}
				sqlsrv_free_stmt( $stmt);
				
			else: 
				//get the incremental id prior adding of records 
				$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoBuyAnyXPriceY]), 0) + 1 as me_rec_id";
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
					insert into [diQtech_db].[dbo].[diQt_PromoBuyAnyXPriceY] ([id]
					,[code]
					,[name]
					,[start_date]
					,[end_date]
                    ,[quantity]
                    ,[price]
					,[product_ids]
					,[date_changed]
					,[disable]
					) values (?,?,?,?,?,?,?,?,getdate(),?)
					";
					$mstart_date = $start_date . ' ' . $start_time;
					$mend_date = $end_date . ' ' . $end_time;
					$is_fixed_price = 1;
					$mevalarray = array($promoid,$buyanyatprice_trxno,$promo_name,$mstart_date,$mend_date,
					$txt_quantity,$txt_price,$meproditems,0);
					$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
					if( $stmt === false) { 
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					}
					sqlsrv_free_stmt( $stmt);
				}
			endif;
			
			//branch detail table promo fixed/discount 
           
			if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
				$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoBuyAnyXPriceYBranch] where [promo_buy_any_x_price_y_id] = ? and 
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
					update [diQtech_db].[dbo].[diQt_PromoBuyAnyXPriceYBranch] set [date_changed] = getdate() 
					where [id] = ? 
					";
					$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
					if ( $stmt === false):
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					endif;
					sqlsrv_free_stmt( $stmt);
				else:
					//add records 
					$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoBuyAnyXPriceYBranch]), 0) + 1 as me_rec_id";
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
						insert into [diQtech_db].[dbo].[diQt_PromoBuyAnyXPriceYBranch] (
						[id]
						,[promo_buy_any_x_price_y_id]
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


    public function buyanyatprice_post_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        //variable declarations
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " AND
            a.`buyanyxatpricey_trxno` LIKE '%{$msearchrec}%' ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` a
        WHERE a.`is_approved` = 'N'
        {$str_optn}
        ";
        
        // var_dump($strqry);
        
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

    } //end post view

    public function buyanyatprice_for_approval() {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_recid = $this->request->getVar('mtkn_recid');
        $buyanyatprice_trxno = '';
        
        if(!empty($mtkn_recid)) { 
            //SELECT IF ALREADY POSTED
            $str = "select is_approved,buyanyxatpricey_trxno from {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` aa WHERE sha2(concat(`id`,'{$mpw_tkn}'),384) = '$mtkn_recid' AND `is_approved` = 'N'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($qry->resultID->num_rows == 0) { 
                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
                die();
            }
            else{
                $rr = $qry->getRowArray();
                $buyanyatprice_trxno = $rr['buyanyxatpricey_trxno'];
            }
            $str = "
            update {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd`
            SET `is_approved` = 'Y',
            `date_approved` = now()
            WHERE sha2(concat(`id`,'{$mpw_tkn}'),384) = '$mtkn_recid'
            AND `is_approved` = 'N';
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            echo  "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Approved Successfully!!!</div>
            
            ";

        }//endif

    }//end  fgpack update approval

    public function download_buyanyatprice_barcode($buyanyatprice_trxno){
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
        a.`id`,a.`buyanyxatpricey_trxno`
        FROM
        {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` a
        WHERE
        a.`buyanyxatpricey_trxno` = '{$buyanyatprice_trxno}'
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
     
        //validated if trx is existing
        if($qry->resultID->num_rows == 0) { 
            $data = "<div class=\"alert alert-danger mb-0\"><strong>Invalid Input</strong><br>Invalid Buy Any AT Price Number.</div>";
            echo $data;
            die();
        }
        else{
            $rr = $qry->getRowArray();
            $buyanyatprice_trxno = $rr['buyanyxatpricey_trxno'];
        }


            $file_name = 'buy_any_atprice'.$buyanyatprice_trxno.'_'.$cuser.'_'.date('Ymd').$this->mylibzsys->random_string(15);
            $mpathdn   = ROOTPATH;
            $_csv_path = '/public/downloads/me/';
            //if(!is_dir($_csv_path)) mkdir($_csv_path, '0755', true);
            $filepath = $mpathdn.$_csv_path.$file_name.'.csv';
            $cfilelnk = site_url() . 'downloads/me/' . $file_name.'.csv'; 
            

            //generate hd and dt data to file format
            $str = "
            SELECT *
            INTO OUTFILE '{$filepath}'
            FIELDS TERMINATED BY '\t'
            LINES TERMINATED BY '\r\n'
            FROM(
                SELECT 
                'Branch Code',
                'PromoBuyAnyXAtPriceYCode',
                'Promo Name',
                'start_date',
                'end_date',
                'quantity',
                'Price',
                'Barcode',
                'disable'

                UNION ALL
                
                SELECT
                CONCAT('E',a.`branch_code`),CONCAT(b.`buyanyxatpricey_trxno`,'-',ROW_NUMBER() OVER (ORDER BY a.`buyanyxatpricey_trxno`)),CONCAT('BUY ',a.`quantity`,' AT ',a.`price`), CONCAT(a.`start_date`,' ', a.`start_time`),CONCAT(a.`end_date`,' ', a.`end_time`),a.`quantity`, a.`price`, 
                 b.`product_barcode`,  b.`is_disabled`
                FROM
                {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_hd` a
                JOIN 
                {$this->db_erp}.`trx_pos_promo_buyanyxatpricey_dt` b
                ON 
                a.`buyanyxatpricey_trxno` = b.`buyanyxatpricey_trxno`
                WHERE
                a.`buyanyxatpricey_trxno` = '{$buyanyatprice_trxno}'
                
                ) oa
            ";

            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
        
        $chtmljs .= "
        <a href=\"{$cfilelnk}\" download=" . $file_name ." class='btn btn-dgreen btn-sm col-lg-12'> <i class='bi bi-save'></i> DOWNLOAD IT!</a>        
        ";
        echo $chtmljs;
    }//end download barcode

    public function deposit_entry_save() { 
        
        //variable declarations
      $cuser            = $this->myusermod->mysys_user();
      $mpw_tkn          = $this->myusermod->mpw_tkn();
      $mtkn_mntr = $this->request->getVar('mtkn_mntr');
      $depctrlno_trx = $this->request->getVar('depctrlno_trx');
      $branch_code = $this->request->getVar('branch_code');
      $branch_name = $this->request->getVar('branch_name');
      $comp_name = $this->request->getVar('comp_name');
      $start_date = $this->request->getVar('start_date');
      $is_approved='N';
      $is_bcodegen='N';
      $adata1 = $this->request->getVar('adata1');
      $adata2 = $this->request->getVar('adata2');
      $opt_df = $this->request->getVar('opt_df');
      $opt_grp = $this->request->getVar('opt_grp');
      $mtkn_branch = $this->request->getVar('mtkn_branch');
      $__hmtkn_fgpacktr = '';


        //validate if branch is not selected
      if(!empty($branch_name)) {
        $str = "SELECT `recid` FROM {$this->db_erp}.`mst_companyBranch` WHERE BRNCH_NAME = '$branch_name' ";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
        $rw = $q->getRowArray();
        $txt_branch_id = $rw['recid'];
        
        $q->freeResult();
        
            //END BRANCH
        }
        else{ 
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch test!!!.</div>";
            die();
        }

                //validated if no product is inserted
        if(empty($adata1)) { 
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
            die();
        }

        
        $recid = '';
        $depctrlno_trx = '';
        
                //UPDATE
        if(!empty($mtkn_mntr)) { 
                //CHECK IF VALID PO
            die($recid);
            $str = "select aa.`recid`,aa.`depctrlno_trx` from {$this->db_erp}.`trx_deposit_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_mntr'";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            if($q->resultID->num_rows == 0) {
                echo "No Records Found!!!";
                die();
            }
            $rw = $q->getRowArray();
            $recid = $rw['recid'];
            $depctrlno_trx = $rw['depctrlno_trx'];
            $q->freeResult();

            }//endif

            //generate promo transaction number
            else{
                
                $depctrlno_trx =  $this->mydataz->get_ctr_6($this->db_erp,'');

            } //end else

            //validate if there is a valid material data
            if(count($adata1) > 0) { 
                $ame = array();
                $adatar1 = array();
                $adatar2 = array();
                $ntqty = 0;
                $ntamt = 0;
                $total =0;
                
                for($aa = 0; $aa < count($adata1); $aa++) { 
                    $medata = explode("x|x",$adata1[$aa]);
                    $bname = trim($medata[0]);
                    $mat_mtkn = $adata2[$aa];
                    $acctno = $medata[1];
                    $salesdate = $medata[2];
                    $sales = $medata[3];
                    $shopeepay = $medata[4];
                    $expense = $medata[5];
                    $amountdeposit = $medata[6];
                    $rmks = $medata[7];
                    $amatnr = array();

                    if(!empty($bname)) { 
                        $str = "select aa.recid,aa.bankName from {$this->db_erp}.`mst_depositBranchAcct` aa where bankName = '$bname' ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        
                        if($q->resultID->num_rows == 0) {
                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$cmat_code]</div>";
                            die();
                        }//end if
                       
                        // validation
                        $rw = $q->getRowArray(); 
                        $mmat_rid = $rw['recid'];  
                        array_push($ame,$bname); 
                        array_push($adatar1,$medata);
                        array_push($adatar2,$mmat_rid);

                    }//end if

                    
                }  //end for 
                
                //insert header data
                if(count($adatar1) > 0) { 
                    if(!empty($mtkn_mntr)) {       
                       $str = "
                       update {$this->db_erp}.`trx_pos_promo_buy1take1_hd` set 
                       `branch_code` = '$branch_code',
                       `start_date` = '$start_date',
                       `start_time` = '$start_time',
                       `end_date` = '$end_date',
                       `end_time`= '$end_time',
                       `is_fixed_price` = '$is_fixed_price'
                       where recid = '$recid' 
                       ";
                       $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                       
                   } else {  

                    $str = "
                    insert into {$this->db_erp}.`trx_deposit_hd` (
                    `depctrlno_trx`,
                    `branch_code`,
                    `start_date`,
                    `branch_name`,
                    `opt_df`,
                    `opt_grp`,
                    `encd`

                    ) values(
                    '$depctrlno_trx',
                    '$branch_code',
                    '$start_date',
                    '$branch_name',
                    '$opt_df',
                    '$opt_grp',
                    now()
                    )
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

                          //GET ID
                    $str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_fgpacktr from {$this->db_erp}.`trx_deposit_hd` aa where `depctrlno_trx` = '$depctrlno_trx' ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                    $rr = $q->getRowArray();
                    $q->freeResult();
                    
                    }//endesle
                    
                    //insert details data
                    for($xx = 0; $xx < count($adatar1); $xx++) { 
                        
                        $xdata = $adatar1[$xx];
                        $bname = $xdata[0];
                        $mat_rid = $adatar2[$xx];
                        $acctno = $xdata[1];
                        $salesdate = $xdata[2];
                        $sales = $xdata[3];
                        $shopeepay = $xdata[4];
                        $expense = $xdata[5];
                        $amountdeposit = $xdata[6];
                        $rmks = $xdata[7];
                        
                        if(empty($mtkn_mntr)) {  
                            $str = "
                            insert into {$this->db_erp}.`trx_deposit_dt` ( 
                            `depctrlno_trx`,
                            `bname`,
                            `acctno`,
                            `salesdate`,
                            `sales`,
                            `shopeepay`,
                            `expense`,
                            `amountdeposit`,
                            `rmks`,
                            `encd` 
                            ) values(
                            '$depctrlno_trx',
                            '$bname',
                            '$acctno',
                            '$salesdate',
                            '$sales',
                            '$shopeepay',
                            '$expense',
                            '$amountdeposit',
                            '$rmks',
                            now()
                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        } 
                        
                    }  //end for 
                    
                    //show success modal
                    if(empty($mtkn_mntr)) { 
                        echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Buy One Take One No:{$depctrlno_trx} </div>
                        <script type=\"text/javascript\"> 
                        function __fg_refresh_data() { 
                            try { 
                                $('#depctrlno_trx').val('{$depctrlno_trx}');
                                $('#__hmpacktrxnoid').val('{$__hmtkn_fgpacktr}');
                                
                                jQuery('#mbtn_mn_Save').prop('disabled',true);
                                } catch(err) { 
                                    var mtxt = 'There was an error on this page.\\n';
                                    mtxt += 'Error description: ' + err.message;
                                    mtxt += '\\nClick OK to continue.';
                                    alert(mtxt);
                                    return false;
                                    }  //end try 
                                } 
                                
                                __fg_refresh_data();
                                </script>
                                ";
                                die();
                            } else { 
                                echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!! </div>
                                ";
                                die();
                            }
                        } else { 
                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Material Data!!!.</div>";
                            die();
            } //end if 
        } else { 
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
            die();
        }
        
    }
    
    
    
    
    
    
    } //end main MyMDCustomerModel