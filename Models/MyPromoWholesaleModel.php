<?php
namespace App\Models;
use CodeIgniter\Model;

class MyPromoWholesaleModel extends Model
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

//WHOLESALE MODEL

    //show recorded datas
    public function wholesale_rec_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " WHERE
            (a.`wholesale_trxno` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_wholesale_hd` a
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
    } // end promo_rec_view

    //start promo entry save
    public function wholesale_entry_save() { 
        
        //variable declarations
      $cuser = $this->myusermod->mysys_user();
      $mpw_tkn = $this->myusermod->mpw_tkn();
      $mtkn_mntr = $this->request->getVar('mtkn_mntr');
      $wholesale_trxno = $this->request->getVar('wholesale_trxno');
      $branch_code = $this->request->getVar('branch_code');
      $branch_name = $this->request->getVar('branch_name');
      $start_date = $this->request->getVar('start_date');
      $start_time = $this->request->getVar('start_time');
      $end_date = $this->request->getVar('end_date');
      $end_time = $this->request->getVar('end_time');
      $discount_value = $this->request->getVar('txt_discount');
      $ART_QUANTITY = $this->request->getVar('txt_quantity');
      $__hmtkn_fgpacktr = '';
      $prod_barcode = $this->request->getVar('ART_BARCODE1');
      $is_disable='FALSE';
      $encd = '';
      $invalid_disc = '76';
      $adata1 = $this->request->getVar('adata1');
      $adata2 = $this->request->getVar('adata2');
      $mtkn_branch = $this->request->getVar('mtkn_branch');
      $txt_branch_id = '';
      $is_peso_discount_checked =$this->request->getVar('is_peso_discount_checked');
      $is_percent_discount_checked =$this->request->getVar('is_percent_discount_checked');
      $is_percent_discount = $this->request->getVar('is_percent_discount');
      $cb_fix_value = $this->request->getVar('cb_fix_value');
      $cb_fix_percent_discount_value = $this->request->getVar('cb_fix_percent_discount_value');
      $promo_name = $this->request->getVar('promo_name');
      $ART_UPRICE='';
		
        //validate if branch is not selecte
        if((!empty($branch_name)) && !empty($mtkn_branch)) {
            $str = "SELECT `recid`,`BRNCH_MBCODE` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
            $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    
            $rw = $q->getRowArray();
            $txt_branch_id = $rw['recid'];
            $B_CODE = trim($rw['BRNCH_MBCODE']);
            $B_CODE_POS = 'E' . trim($B_CODE);
          
        $q->freeResult();
        
            //END BRANCH
        }
        else{ 
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
            die();
        }

                //validate date
        if(!empty($start_date) && !empty($end_date) && !empty($end_time) && !empty($end_date)) {
        
        }
        else{
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Dates or Time is required!!!.</div>";
            die();
        }

        if ($cb_fix_value == 0 & $cb_fix_percent_discount_value == 0) {
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please select a discount type!!!</div>";
            die();
        }

                //validated if no product is inserted
        if(empty($adata1)) { 
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
            die();
        }

        $recid = '';
        $wholesale_trxno = '';
        
                //UPDATE
        if(!empty($mtkn_mntr)) { 
                //CHECK IF VALID PO
            die($recid);
            $str = "select aa.`recid`,aa.`wholesale_trxno` from {$this->db_erp}.`gw_wholesale_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_mntr'";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            if($q->resultID->num_rows == 0) {
                echo "No Records Found!!!";
                die();
            }
            $rw = $q->getRowArray();
            $recid = $rw['recid'];
            $wholesale_trxno = $rw['wholesale_trxno'];
            $q->freeResult();

        }//endif

            //generate promo transaction number
            else{
                
                $wholesale_trxno =  $this->mydataz->get_ctr_promotions('PD','',$this->db_erp,'CTRL_NO04');//PROMO TRANSACTION NO

            } //end else

            //validate if there is a valid material data
            if(count($adata1) > 0) { 
                $ame = array();
                $adatar1 = array();
                $ntqty = 0;
                $ntamt = 0;
                $total =0;
                
                for($aa = 0; $aa < count($adata1); $aa++) { 
                    $medata = explode("x|x",$adata1[$aa]);
                    $ART_CODE = trim($medata[0]);
                    $mat_mtkn = $adata2[$aa];
                    $ART_DESC = (trim($medata[1]));
                    $ART_SRP = (trim($medata[2]));
                    $ART_BARCODE1 = (trim($medata[3]));
                    $amatnr = array();

                    if(!empty($ART_CODE)) { 
                        $str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where  aa.`ART_CODE` = '$ART_CODE' ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        
                        if($q->resultID->num_rows == 0) {
                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$ART_CODE]</div>";
                            die();
                        }//end if

                         
                            
                            if (empty($ART_BARCODE1)) {
                                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item barcode cannot be null.</div>";
                                die();
                             }

                            if(empty($ART_QUANTITY)){
                                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please enter a Quantity Value.</div>";
                                die();
                            }
                            
                            if(empty($discount_value)){
                                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please enter a discount value.</div>";
                                die();
                            }

                        
                        
                            $str = "SELECT a.`ART_UPRICE` FROM {$this->db_erp}.`mst_article` a JOIN {$this->db_erp}.`gw_wholesale_dt` b ON a.`ART_CODE` = b.`store_code` WHERE a.`ART_CODE` = b.`store_code`" ;
                            $qv = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            $rww = $qv->getResultArray();
                            foreach ($rww as $data) {
                                $ART_UPRICE = $data['ART_UPRICE'];
                            }

                            $total = ($ART_UPRICE  * 0.75);
                            if ($cb_fix_percent_discount_value == 1 && $discount_value >= $total) {
                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Discount value must not be greater than 75% </div>";
                            die();
                            }


                        }
                    }
                         //validation
                        array_push($ame, $ART_CODE);
                        array_push($adatar1,$medata);
                        $adatac = $this->myposconn->POS_check_promo_exists($ART_CODE,$B_CODE_POS,$start_date . ' ' . $start_time,$end_date . ' ' .  $end_time);
                        if (count($adatac) > 0):
                            $pos_promocode = trim($adatac['code']);
                            $pos_promotype = $adatac['me_promo_type'];
                            $pos_start_date = substr($adatac['start_date'],0,19);
                            $pos_end_date = substr($adatac['end_date'],0,19);
                            
                            
                            
                            if (!empty($mtkn_mntr)):
                                $nrecscount = $adatac['nrecsme'];	
                                
                                if($nrecscount > 1): 
                                    echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing in Multipe POS Promo already!!! <br/> 
                                    [$ART_CODE]
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
                                    if ($wholesale_trxno !== $pos_promocode && !empty($pos_promocode)):
                                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing or conflict POS Promo already!!! <br/> 
                                        [$ART_CODE]
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
                                    [$ART_CODE]
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
            //end for 
                

                //insert header data
                if(count($adatar1) > 0) { 
                    if(!empty($mtkn_mntr)) {       
                       $str = "
                       update {$this->db_erp}.`gw_wholesale_hd` set 
                       `branch_code` = '$B_CODE',
                       `wholesale_trxno` = '$wholesale_trxno',
                       `promo_name` = '$promo_name',
                       `start_date` = '$start_date',
                       `start_time` = '$start_time',
                       `end_date` = '$end_date',
                       `end_time`= '$end_time'
                       where recid = '$recid' 
                       ";
                       $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                       
                    } else {  
                    $str = "
                    insert into {$this->db_erp}.`gw_wholesale_hd` (
                    `branch_code`,
                    `branch_name`,
                    `wholesale_trxno`,
                    `promo_name`,
                    `start_date`,
                    `start_time`,
                    `end_date`,
                    `end_time`,
                    `muser`,
                    `encd_date`
                    ) values(
                    '$B_CODE',
                    '$branch_name',
                    '$wholesale_trxno',
                    '$promo_name',
                    '$start_date',
                    '$start_time',
                    '$end_date',
                    '$end_time',
                    '$cuser',
                    now()
                    )
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                          //GET ID
                    $str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_fgpacktr from {$this->db_erp}.`gw_wholesale_hd` aa where `wholesale_trxno` = '$wholesale_trxno' ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                    $rr = $q->getRowArray();
                    $q->freeResult();
                    
                    }//endesle
                    
                    //insert details data
                    for($xx = 0; $xx < count($adatar1); $xx++) { 
                        $xdata = $adatar1[$xx];
                        $ART_CODE = $xdata[0];
                        $ART_DESC = $xdata[1];
                        $ART_SRP = $xdata[2];
                     
                        $ART_BARCODE1 = $xdata[3];

                        
                        if(empty($mtkn_mntr)) {  
                            $str = "
                            insert into {$this->db_erp}.`gw_wholesale_dt` ( 
                            `wholesale_trxno`,
                            `quantity`,
                            `is_peso_discount`,
                            `is_percent_discount`,
                            `discount_value`,
                            `prod_barcode`,
                            `store_code`,
                            `is_disable`
                            ) values(
                            '$wholesale_trxno',
                            '$ART_QUANTITY',
                            '$cb_fix_value',
                            '$cb_fix_percent_discount_value',
                            '$discount_value',
                            '$ART_BARCODE1',
                            '$ART_CODE',
                            '$is_disable'
                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        }  
                    }  //end for 
                    
                    //show success modal
                    if(empty($mtkn_mntr)) { 
                        $this->save_promo_threshold_from_POS($wholesale_trxno,$promo_name,$ART_QUANTITY,$cb_fix_value,$cb_fix_percent_discount_value,$discount_value,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                        echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Wholesale Code:{$wholesale_trxno} </div>
                        <script type=\"text/javascript\"> 
                        function __fg_refresh_data() { 
                            try { 
                                $('#wholesale_trxno').val('{$wholesale_trxno}');
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
                                $this->save_promo_threshold_from_POS($wholesale_trxno,$promo_name,$ART_QUANTITY,$cb_fix_value,$cb_fix_percent_discount_value,$discount_value,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                                echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
                                ";
                                die();
                            }
                        } else { 
                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Material Data!!!.</div>";
                            die();
                        } //end if 
    } //end promo_entry_save
    public function save_promo_threshold_from_POS($wholesale_trxno,$promo_name,$ART_QUANTITY,$cb_fix_value,$cb_fix_percent_discount_value,$discount_value,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time){
		$lcon = 0;
        $promo_name = $this->request->getVar('promo_name');
       
		if ($this->myposdbconn):
			$str_items = '';
			for($aa = 0; $aa < count($adata1); $aa++):
				$medata = explode("x|x",$adata1[$aa]);
				$mitemc = trim($medata[0]);
				$mdesc = (trim($medata[1]));
				$mbcode = (trim($medata[3]));
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
			$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoWholesale] where [code] = ?";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array($wholesale_trxno), array("Scrollable"=>"buffered") );
			if( $stmt === false) { 
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$row_count = sqlsrv_num_rows( $stmt );
			if ($row_count > 0):
				$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
				$lcon = 1;
				$promoid = $row['id'];
				$str = "
				update [diQtech_db].[dbo].[diQt_PromoWholesale] set 
				[name] = ?,
				[start_date] = ?,
				[end_date] = ?,
				[quantity] = ?,
				[is_peso_discount] = ?,
				[is_percent_discount] = ?,
                [discount] = ?,
                [product_ids] ?,
				[date_changed] = getdate() ,
                [disable] = ?
				where [id] = ?
				";
				$mstart_date = $start_date . ' ' . $start_time;
				$mend_date = $end_date . ' ' . $end_time;
			 
				$mevalarray = array($promo_name,$mstart_date,$mend_date,$ART_QUANTITY,$cb_fix_value,
				$cb_fix_percent_discount_value,$discount_value,$meproditems,1,$promoid);
				$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
				if( $stmt === false) { 
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				}
				sqlsrv_free_stmt( $stmt);
				
			else: 
				//get the incremental id prior adding of records 
				$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoWholesale]), 0) + 1 as me_rec_id";
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
					insert into [diQtech_db].[dbo].[diQt_PromoWholesale] ([id]
					,[code]
					,[name]
					,[start_date]
					,[end_date]
                    ,[quantity]
                    ,[is_peso_discount]
					,[is_percent_discount]
                    ,[discount]
                    ,[product_ids]
					,[date_changed]
					,[disable]
					) values (?,?,?,?,?,?,?,?,?,?,getdate(),?)
					";
					$mstart_date = $start_date . ' ' . $start_time;
					$mend_date = $end_date . ' ' . $end_time;
					$is_fixed_price = 1;
					$mevalarray = array($promoid,$wholesale_trxno,$promo_name,$mstart_date,$mend_date,
					$ART_QUANTITY,$cb_fix_value,$cb_fix_percent_discount_value,$discount_value,$meproditems,0);
					$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
					if( $stmt === false) { 
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					}
					sqlsrv_free_stmt( $stmt);
				}
			endif;
			
			//branch detail table promo fixed/_v$discount_value 
           
			if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
				$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoWholesaleBranch] where [promo_wholesale_id] = ? and 
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
					update [diQtech_db].[dbo].[diQt_PromoWholesaleBranch] set [date_changed] = getdate() 
					where [id] = ? 
					";
					$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
					if ( $stmt === false):
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					endif;
					sqlsrv_free_stmt( $stmt);
				else:
					//add records 
					$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoWholesaleBranch]), 0) + 1 as me_rec_id";
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
						insert into [diQtech_db].[dbo].[diQt_PromoWholesaleBranch] (
						[id]
						,[promo_wholesale_id]
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
    public function wholesale_post_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        //variable declarations
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " AND
            a.`wholesale_trxno` LIKE '%{$msearchrec}%' ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_wholesale_hd` a
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


    //start fgpack update approval
    public function wholesale_for_approval() {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_fgpacktr = $this->request->getVar('mtkn_fgpacktr');
        $wholesale_trxno = '';
        
        if(!empty($mtkn_fgpacktr)) { 
            //SELECT IF ALREADY POSTED
            $str = "select is_approved,wholesale_trxno from {$this->db_erp}.`gw_wholesale_hd` aa WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_fgpacktr' AND `is_approved` = 'N'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($qry->resultID->num_rows == 0) { 
                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
                die();
            }
            else{
                $rr = $qry->getRowArray();
                $wholesale_trxno = $rr['wholesale_trxno'];
            }
            $str = "
            update {$this->db_erp}.`gw_wholesale_hd`
            SET `is_approved` = 'Y',
            `date_approved` = now()
            WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_fgpacktr'
            AND `is_approved` = 'N';
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            echo  "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Approved Successfully!!!</div>
            ";

        }//endif

    }//end  fgpack update approval

    //start download barcode
    public function download_wholesale_barcode($wholesale_trxno){
        //declaire variables
        $cfilelnk='';
        $file_name='';
        $chtmljs='';
        $discount_name='';
        $discount_chosen='';
        $percentsign='';
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $chtmljs ="";
        $fix_value='';
        $perc_value = '';
        //check if there is an existing transaction number
        $str = "
        SELECT 
        a.`recid`,a.`wholesale_trxno`
        FROM
        {$this->db_erp}.`gw_wholesale_hd` a
        WHERE
        a.`wholesale_trxno` = '{$wholesale_trxno}'
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
            $wholesale_trxno = $rr['wholesale_trxno'];
        }

        //setup file
        if($wholesale_trxno != ''){
            $file_name = 'Promo_wholesale'.'_'.$wholesale_trxno.'_'.$cuser.'_'.date('Ymd').$this->mylibzsys->random_string(15);
            $mpathdn   = ROOTPATH;
            $_csv_path = '/public/downloads/me/';
            //if(!is_dir($_csv_path)) mkdir($_csv_path, '0755', true);
            $filepath = $mpathdn.$_csv_path.$file_name.'.csv';
            $cfilelnk = site_url() . 'downloads/me/' . $file_name.'.csv'; 
            
            $strr ="
            Select 
            a.`wholesale_trxno`,
            b.`quantity`,
            b.`is_peso_discount`,
            b.`is_percent_discount`,
            b.`discount_value`
            from
            gw_wholesale_hd a 
            join
            gw_wholesale_dt b
            on
            a.`wholesale_trxno` = b.`wholesale_trxno`
            where a.`wholesale_trxno` = '$wholesale_trxno'
            ";
          
            $qq = $this->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
            $rw = $qq->getRowArray();
            $quantity = $rw['quantity'];
            $is_peso_discount = $rw['is_peso_discount'];
            $is_percent_discount = $rw['is_percent_discount'];
            $discount_value = $rw['discount_value'];

            if ($is_peso_discount == 1) {
                $fix_value = '1';
            }
            else{
               $fix_value = '0';
            }
            if ($is_percent_discount) {
               $perc_value = '1';
            }else{
               $perc_value = '0';
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
                'PromoWholesaleCode',
                'PromoWholesale_Name',
                'start_date',
                'end_date',
                'Quantity',
                'is_peso_discount',
                'is_percent_discount',
                'discount',
                'Product Barcode',
                'Store Code',
                'disable'

                UNION ALL
                
                SELECT
                CONCAT('E',a.`branch_code`),CONCAT(a.`wholesale_trxno`,'-',ROW_NUMBER() OVER (ORDER BY b.wholesale_trxno)),CONCAT ('avail ',`quantity`,' ','Items ','get ',`discount_value`,'%'),CONCAT (a.`start_date`,' ',a.`start_time`),CONCAT (a.`end_date`,' ',a.`end_time`),
                b.`quantity`,'$fix_value','$perc_value',b.`discount_value`, b.`prod_barcode`, b.`store_code`, b.`is_disable`
                FROM
                `gw_wholesale_hd` a
                JOIN 
                `gw_wholesale_dt` b
                ON 
                a.`wholesale_trxno` = b.`wholesale_trxno`
                WHERE
                a.`wholesale_trxno` = '$wholesale_trxno'
                
                ) oa
            ";

            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
        
        $chtmljs .= "
        <a href=\"{$cfilelnk}\" download=" . $file_name ." class='btn btn-dgreen btn-sm col-lg-12'> <i class='bi bi-save'></i> DOWNLOAD IT!</a>        
        ";
        echo $chtmljs;
    }//end download barcode
}
    //END WHOLESALE MODEL
} //end main MyMDCustomerModel