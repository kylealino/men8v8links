<?php
namespace App\Models;
use CodeIgniter\Model;

class MyPromoThresholdModel extends Model
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
        $this->myposconn = model('App\Models\MyPOSConnModel');
        $this->myposdbconn = $this->myposconn->connectdb();
        $this->request = \Config\Services::request();
    }//end construct

    //THRESHOLD MODEL

    //start threshold entry save
    public function threshold_entry_save() { 
        
        //variable declarations
      $cuser = $this->myusermod->mysys_user();
      $mpw_tkn = $this->myusermod->mpw_tkn();
      $mtkn_mntr = $this->request->getVar('mtkn_mntr');
      $threshold_trxno = $this->request->getVar('threshold_trxno');
      $branch_code = $this->request->getVar('branch_code');
      $branch_name = $this->request->getVar('branch_name');
      $start_date = $this->request->getVar('start_date');
      $start_time = $this->request->getVar('start_time');
      $end_date = $this->request->getVar('end_date');
      $end_time = $this->request->getVar('end_time');
      $discount = $this->request->getVar('txt_discount');
      $__hmtkn_fgpacktr = '';
      $promo_name = $this->request->getVar('promo_name');
      $prod_barcode = $this->request->getVar('ART_BARCODE1');
      $is_disable='FALSE';
      $is_approved='N';
      $is_bcodegen='N';
      $encd = '';
      $invalid_disc = '0.50';
      $adata1 = $this->request->getVar('adata1');
      $adata2 = $this->request->getVar('adata2');
      $mtkn_branch = $this->request->getVar('mtkn_branch');
      $txt_branch_id = '';
      $amount = $this->request->getVar('txt_amount');
      $promo_name = 'Worth '. $amount .' Discount '.$discount.'%';
      $B_CODE ='';
    

      

        //validate if branch is not selected
      
        if((!empty($branch_name)) && !empty($mtkn_branch)) {
            $str = "SELECT `recid`,`BRNCH_OCODE2` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
            $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    
            $rw = $q->getRowArray();
            $txt_branch_id = $rw['recid'];
            $B_CODE = trim($rw['BRNCH_OCODE2']);
            $B_CODE_POS = 'E' . trim($B_CODE);
          
       
        
        $q->freeResult();
        
            //END BRANCH
        }
        else{ 
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
            die();
        }



                //validate date
        if(!empty($start_date) && !empty($end_date) && !empty($start_time) && !empty($end_date)) {
        
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
        $threshold_trxno = '';
        
                //UPDATE
        if(!empty($mtkn_mntr)) { 
                //CHECK IF VALID PO
            
            $str = "select aa.`recid`,aa.`threshold_trxno` from {$this->db_erp}.`gw_threshold_hd` aa where threshold_trxno = '$mtkn_mntr'";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            if($q->resultID->num_rows == 0) {
                echo "No Records Found!!!";
                die();
            }
            $rw = $q->getRowArray();
            $recid = $rw['recid'];
            $threshold_trxno = $rw['threshold_trxno'];
            $q->freeResult();
        }//endif

            //generate threshold transaction number
            else{

            $threshold_trxno =  $this->mydataz->get_ctr_promotions('PTC','',$this->db_erp,'CTRL_NO04');//THRESHOLD TRANSACTION NO

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
                    $ART_CODE = trim($medata[0]);
                    $mat_mtkn = $adata2[$aa];
                    $ART_DESC = (trim($medata[1]));
                    $ART_BARCODE1 = (trim($medata[2]));
                    $amatnr = array();

                    if(!empty($ART_CODE)) { 
                        $str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where  ART_CODE = '$ART_CODE' ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        
                        if($q->resultID->num_rows == 0) {
                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$ART_CODE]</div>";
                            die();
                        }//end if

                        else{
                            $strr = "SELECT a.`recid`,a.`threshold_trxno`,date_format(a.`start_date`,'%m/%d/%Y') `m_start_date`,date_format(a.`end_date`,'%m/%d/%Y') `m_end_date` 
                            FROM {$this->db_erp}.`gw_threshold_hd` a  
                            JOIN {$this->db_erp}.`gw_threshold_dt` b 
                            ON a.`threshold_trxno` = b.`threshold_trxno`
                             WHERE a.`branch_code` = '$B_CODE' 
                            and b.`prod_barcode` = '$ART_BARCODE1' and 
                            ((date(a.`start_date`) between DATE('$start_date') and DATE('$end_date')) or (DATE(a.`end_date`) between DATE('$start_date')  and DATE('$end_date')) ) 
                            group by a.recid,date(a.`start_date`),date(a.`end_date`) 
                            " ;
                            $qv = $this->myusermod->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            $nrw = $qv->getNumRows();
                            //skip when editing
                            if ($recid > 0):
                                if ($nrw > 0): 
                                    $rv = $qv->getRowArray();
                                    //check if no other entries but have existing for editing
                                 
                                  
                                    if($nrw == 1 && $rv['recid'] == $recid): 
                                    else:
                                        foreach ($qv->getResultArray() as $data) { 
                                      
                                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$ART_CODE] - [{$data['threshold_trxno']}]</div>";
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
                                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$ART_CODE] - [$nrw]</div>";
                                        die();
                                    }
                                    $qv->freeResult(); 
                                endif;
                            endif;

                            $str = "SELECT ('$ART_BARCODE1') <> (b.`prod_barcode`) _bcode FROM {$this->db_erp}.`gw_promo_hd` a JOIN {$this->db_erp}.`gw_promo_dt` b ON a.`promo_trxno` = b.`promo_trxno` WHERE a.`branch_code` = '$branch_code' AND DATE('$start_date') <= DATE(a.`end_date`)" ;
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            
                            if($q->resultID->num_rows > 0) { 
                                $rww = $q->getResultArray();
                                foreach ($rww as  $data) {
                                    $bcode = $data['_bcode'];
                                    if ($bcode == 0) {
                                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing in promo discount. <br/> [$ART_CODE]</div>";
                                        die();
                                    }  
                                }   
                            }

                            $str = "SELECT ('$ART_BARCODE1') <> (b.`prod_barcode`) _bcode FROM {$this->db_erp}.`gw_wholesale_hd` a JOIN {$this->db_erp}.`gw_wholesale_dt` b ON a.`wholesale_trxno` = b.`wholesale_trxno` WHERE a.`branch_code` = '$branch_code' AND DATE('$start_date') <= DATE(a.`end_date`)" ;
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            
                            if($q->resultID->num_rows > 0) { 
                                $rww = $q->getResultArray();
                                foreach ($rww as  $data) {
                                    $bcode = $data['_bcode'];
                                    if ($bcode == 0) {
                                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing in Wholesale. <br/> [$ART_CODE]</div>";
                                        die();
                                    }  
                                }  
                            }

                            $str = "SELECT ('$ART_BARCODE1') <> (b.`prod_barcode_buy`) _bcode FROM {$this->db_erp}.`gw_buy1take1_hd` a JOIN {$this->db_erp}.`gw_buy1take1_dt` b ON a.`buy1take1_trxno` = b.`buy1take1_trxno` WHERE a.`branch_code` = '$branch_code' AND DATE('$start_date') <= DATE(a.`end_date`)" ;
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            
                            if($q->resultID->num_rows > 0) { 
                                $rww = $q->getResultArray();
                                foreach ($rww as  $data) {
                                    $bcode = $data['_bcode'];
                                    if ($bcode == 0) {
                                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing in buy 1 take 1 promo. <br/> [$ART_CODE]</div>";
                                        die();
                                    }  
                                } 
                            }

                            $str = "SELECT ('$ART_BARCODE1') <> (b.`prod_barcode_take`) _bcode FROM {$this->db_erp}.`gw_buy1take1_hd` a JOIN {$this->db_erp}.`gw_buy1take1_dt` b ON a.`buy1take1_trxno` = b.`buy1take1_trxno` WHERE a.`branch_code` = '$branch_code' AND DATE('$start_date') <= DATE(a.`end_date`)" ;
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            
                            if($q->resultID->num_rows > 0) { 
                                $rww = $q->getResultArray();
                                foreach ($rww as  $data) {
                                    $bcode = $data['_bcode'];
                                    if ($bcode == 0) {
                                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing in buy 1 take 1 promo. <br/> [$ART_CODE]</div>";
                                        die();
                                    }  
                                } 
                            }
                            
                          //  Discount value must not be greater than 50%
                            
                             if (empty($ART_BARCODE1)) {
                                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item barcode cannot be null.</div>";
                                die();
                             }

                            if(empty($amount)){
                                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please enter a Amount value.</div>";
                                die();
                            }
                            
                            if(empty($discount)){
                                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please enter a discount value.</div>";
                                die();
                            }
                            
                            if ($discount >= $amount) {
                                    echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Discount value is Invalid</div>";
                                    die();
                           }
                        }

                            $total = (100 * 0.75);
                            if ($discount >= $total) {
                                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Discount value must not be greater than 75%</div>";
                                die();
                            }
                        
                      
                        
                            // validation
                            array_push($ame,$ART_CODE); 
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
                                        if ($threshold_trxno !== $pos_promocode && !empty($pos_promocode)):
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
                }  //end for 

                
                //insert header data
                if(count($adatar1) > 0) { 
                    if(!empty($mtkn_mntr)) {       
                       $str = "
                       update {$this->db_erp}.`gw_threshold_hd` set 
                       `threshold_trxno` = '$threshold_trxno',
                       `branch_code` = '$B_CODE',
                       `start_date` = '$start_date',
                       `start_time` = '$start_time',
                       `end_date` = '$end_date',
                       `promo_name` = '$promo_name',
                       `amount` = '$amount',
                       `discount` = '$discount',
                       `end_time`= '$end_time'
                        where recid = '$recid' 
                        ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                       
                    } else {  

                    $str = "
                    insert into {$this->db_erp}.`gw_threshold_hd` (
                    `threshold_trxno`,
                    `branch_code`,
                    `branch_name`,
                    `start_date`,
                    `start_time`,
                    `end_date`,
                    `end_time`,
                    `muser`,
                    `amount`,
                    `discount`,
                    `encd_date`,
                    `promo_name`,
                    `is_approved`,
                    `is_bcodegen`

                    ) values(
                    '$threshold_trxno',
                    '$B_CODE',
                    '$branch_name',
                    '$start_date',
                    '$start_time',
                    '$end_date',
                    '$end_time',
                    '$cuser',
                    '$amount',
                    '$discount',
                    now(),
                    '$promo_name',
                    '$is_approved',
                    '$is_bcodegen'
                    )
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

                          //GET ID
                    $str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_fgpacktr from {$this->db_erp}.`gw_threshold_hd` aa where `threshold_trxno` = '$threshold_trxno' ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                    $rr = $q->getRowArray();
                    $q->freeResult();
                    
                    }//endesle
                    
                    //insert details data
                    for($xx = 0; $xx < count($adatar1); $xx++) { 
                        $xdata = $adatar1[$xx];
                        $ART_CODE = $xdata[0];
                        $ART_DESC = $xdata[1];
                        $ART_BARCODE1 = $xdata[2];

                        $str1 = "
                        SELECT `prod_barcode`,  `threshold_trxno` FROM {$this->db_erp}.`gw_threshold_dt` WHERE `threshold_trxno` = '$threshold_trxno' AND `prod_barcode` = '$ART_BARCODE1' 
                        
                        ";
                        

                        $qv = $this->myusermod->mylibzdb->myoa_sql_exec($str1,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					    $getrw = $qv->getNumRows();
                        $qv->freeResult();
                        if(!empty($mtkn_mntr) && $getrw == 0) {  
                            $str = "
                            insert into {$this->db_erp}.`gw_threshold_dt` ( 
                            `threshold_trxno`,
                            `prod_barcode`,
                            `is_disable`
                            ) values(
                            '$threshold_trxno',
                            '$ART_BARCODE1',
                            '$is_disable'
                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        } 
                        
                        if(empty($mtkn_mntr)) {  
                            $str = "
                            insert into {$this->db_erp}.`gw_threshold_dt` ( 
                            `threshold_trxno`,
                            `prod_barcode`,
                            `is_disable`
                            ) values(
                            '$threshold_trxno',
                            '$ART_BARCODE1',
                            '$is_disable'
                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        }  
                    }  //end for 
                    
                    //show success modal
                    if(empty($mtkn_mntr)) { 
                        $this->save_promo_threshold_from_POS($threshold_trxno,$promo_name,$amount,$discount,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                        echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Promo Threshold Code:{$threshold_trxno} </div>
                        <script type=\"text/javascript\"> 
                        function __fg_refresh_data() { 
                            try { 
                                $('#threshold_trxno').val('{$threshold_trxno}');
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
                                $this->save_promo_threshold_from_POS($threshold_trxno,$promo_name,$amount,$discount,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                                echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
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
    } //end threshold_entry_save

    public function save_promo_threshold_from_POS($threshold_trxno,$promo_name,$amount,$discount,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time){
		$lcon = 0;
        $promo_name = 'Worth '. $amount .' Discount '.$discount.'%';
       
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
			$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoThreshold] where [code] = ?";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array($threshold_trxno), array("Scrollable"=>"buffered") );
			if( $stmt === false) { 
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$row_count = sqlsrv_num_rows( $stmt );
			if ($row_count > 0):
				$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
				$lcon = 1;
				$promoid = $row['id'];
				$str = "
				update [diQtech_db].[dbo].[diQt_PromoThreshold] set 
				[name] = ?,
				[start_date] = ?,
				[end_date] = ?,
				[amount] = ?,
				[discount] = ?,
				[product_ids] = ?,
				[date_changed] = getdate() 
				where [id] = ?
				";
				$mstart_date = $start_date . ' ' . $start_time;
				$mend_date = $end_date . ' ' . $end_time;
			 
				$mevalarray = array($promo_name,$mstart_date,$mend_date,$amount,$discount,
				$meproditems,$promoid);
				$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
				if( $stmt === false) { 
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				}
				sqlsrv_free_stmt( $stmt);
				
			else: 
				//get the incremental id prior adding of records 
				$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoThreshold]), 0) + 1 as me_rec_id";
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
					insert into [diQtech_db].[dbo].[diQt_PromoThreshold] ([id]
					,[code]
					,[name]
					,[start_date]
					,[end_date]
                    ,[amount]
                    ,[discount]
					,[product_ids]
					,[date_changed]
					,[disable]
					) values (?,?,?,?,?,?,?,?,getdate(),?)
					";
					$mstart_date = $start_date . ' ' . $start_time;
					$mend_date = $end_date . ' ' . $end_time;
					$is_fixed_price = 1;
					$mevalarray = array($promoid,$threshold_trxno,$promo_name,$mstart_date,$mend_date,
					$amount,$discount,$meproditems,0);
					$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
					if( $stmt === false) { 
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					}
					sqlsrv_free_stmt( $stmt);
				}
			endif;
			
			//branch detail table promo fixed/discount 
           
			if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
				$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoThresholdBranch] where [promo_threshold_id] = ? and 
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
					update [diQtech_db].[dbo].[diQt_PromoThresholdBranch] set [date_changed] = getdate() 
					where [id] = ? 
					";
					$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
					if ( $stmt === false):
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					endif;
					sqlsrv_free_stmt( $stmt);
				else:
					//add records 
					$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoThresholdBranch]), 0) + 1 as me_rec_id";
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
						insert into [diQtech_db].[dbo].[diQt_PromoThresholdBranch] (
						[id]
						,[promo_threshold_id]
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



    public function threshold_rec_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " WHERE
            (a.`threshold_trxno` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_threshold_hd` a
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
        SELECT * from ({$strqry}) oa order by threshold_trxno desc limit {$nstart},{$npagelimit} ";
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

    public function threshold_post_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        //variable declarations
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " AND
            a.`threshold_trxno` LIKE '%{$msearchrec}%' ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_threshold_hd` a
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
        SELECT * from ({$strqry}) oa order by threshold_trxno desc limit {$nstart},{$npagelimit} ";
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

    public function threshold_for_approval() {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_fgpacktr = $this->request->getVar('mtkn_fgpacktr');
        $threshold_trxno = '';
        
        if(!empty($mtkn_fgpacktr)) { 
            //SELECT IF ALREADY POSTED
            $str = "select is_approved,threshold_trxno from {$this->db_erp}.`gw_threshold_hd` aa WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_fgpacktr' AND `is_approved` = 'N'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($qry->resultID->num_rows == 0) { 
                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
                die();
            }
            else{
                $rr = $qry->getRowArray();
                $threshold_trxno = $rr['threshold_trxno'];
            }
            $str = "
            update {$this->db_erp}.`gw_threshold_hd`
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

    public function download_threshold_barcode($threshold_trxno){
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

        //check if there is an existing transaction number
        $str = "
        SELECT 
        a.`recid`,a.`threshold_trxno`
        FROM
        {$this->db_erp}.`gw_threshold_hd` a
        WHERE
        a.`threshold_trxno` = '{$threshold_trxno}'
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        //validated if trx is existing
        if($qry->resultID->num_rows == 0) { 
            $data = "<div class=\"alert alert-danger mb-0\"><strong>Invalid Input</strong><br>Invalid Input.</div>";
            echo $data;
            die();
        }
        else{
            $rr = $qry->getRowArray();
            $threshold_trxno = $rr['threshold_trxno'];
        }

        //setup file
        if($threshold_trxno != ''){
            $file_name = 'Promo_Threshold'.'_'. $threshold_trxno . '_' . $cuser . '_'.date('Ymd').$this->mylibzsys->random_string(15);
            $mpathdn   = ROOTPATH;
            $_csv_path = '/public/downloads/me/';
            //if(!is_dir($_csv_path)) mkdir($_csv_path, '0755', true);
            $filepath = $mpathdn.$_csv_path.$file_name.'.csv';
            $cfilelnk = site_url() . 'downloads/me/' . $file_name.'.csv'; 
            
            $strr ="
            Select 
            a.`threshold_trxno`,
            a.`discount`,
            a.`amount`

            from
            gw_threshold_hd a 
            join
            gw_threshold_dt b
            on
            a.`threshold_trxno` = b.`threshold_trxno`
            where a.`threshold_trxno` = '$threshold_trxno'
            ";
            $qq = $this->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
            $rw = $qq->getRowArray();
            $discount = $rw['discount'];
            $amount = $rw['amount'];

            //if ($promothresholdname == 1) {
                //$promothresholdname = 'Everything @ ';
           }
          
            //generate hd and dt data to file format
            $str = "
            SELECT *
            INTO OUTFILE '{$filepath}'
            FIELDS TERMINATED BY ',' ENCLOSED BY '\"'
            LINES TERMINATED BY '\r\n'
            FROM (
                SELECT 
                'Branch_code',
                'PromoThresholdCode',
                'PromoThreshold_Name',
                'start_date',
                'end_date',
                'amount',
                'discount',
                'Product Barcode',
                'disable'

                UNION ALL
                
                SELECT
                CONCAT('E',a.`branch_code`),CONCAT (a.`threshold_trxno`,'-',ROW_NUMBER() OVER (ORDER BY b. `threshold_trxno`)), CONCAT('WORTH ',`amount` ,' DISC ',`discount`), CONCAT (a.`start_date`,' ',a.`start_time`),CONCAT (a.`end_date`,' ',a.`end_time`),
                a.`amount`,a.`discount`, b.`prod_barcode`, b.`is_disable`
                FROM
                {$this->db_erp}.`gw_threshold_hd` a
                JOIN 
                {$this->db_erp}.`gw_threshold_dt` b
                ON 
                a.`threshold_trxno` = b.`threshold_trxno`
                WHERE
                a.`threshold_trxno` = '$threshold_trxno'
                
                ) oa
            ";

            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
        
        $chtmljs .= "
        <a href=\"{$cfilelnk}\" download=" . $file_name ." class='btn btn-dgreen btn-sm col-lg-12'> <i class='bi bi-save'></i> DOWNLOAD IT!</a>        
        ";
        echo $chtmljs;
    }//end download barcode

    //THRESHOLD MODEL END

} //end main MyMDCustomerModel