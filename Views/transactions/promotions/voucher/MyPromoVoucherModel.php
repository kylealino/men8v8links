<?php
namespace App\Models;
use CodeIgniter\Model;

class MyPromoVoucherModel extends Model
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

    //VOUCHER MODEL

    public function voucher_rec_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " WHERE
            (a.`voucher_trxno` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_voucher_hd` a
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
    } // end voucher_rec_view
    

    //start voucher entry save
    public function voucher_entry_save() { 
         
        //variable declarations
      $cuser = $this->myusermod->mysys_user();
      $mpw_tkn = $this->myusermod->mpw_tkn();
      $mtkn_mntr = $this->request->getVar('mtkn_mntr');
      $voucher_trxno = $this->request->getVar('voucher_trxno');
      $branch_code = $this->request->getVar('branch_code');
      $branch_name = $this->request->getVar('branch_name');
      $start_date = $this->request->getVar('start_date');
      $start_time = $this->request->getVar('start_time');
      $end_date = $this->request->getVar('end_date');
      $end_time = $this->request->getVar('end_time');
      $discount_value = $this->request->getVar('discount_value');
      $voucher_code = $this->request->getVar('voucher_code');
      $voucher_name = $this->request->getVar('voucher_name');
      $__hmtkn_fgpacktr = '';
      $is_disabled='FALSE';
      $is_approved='N';
      $date_approved='';
      $encd = '';
      $adata1 = $this->request->getVar('adata1');
      $adata2 = $this->request->getVar('adata2');
      $mtkn_branch = $this->request->getVar('mtkn_branch');
      $txt_branch_id = '';
      $amount='0';

      

      //validate if branch is not selected
      
      if((!empty($branch_name)) && !empty($mtkn_branch)) {
        $str = "SELECT `recid`,`BRNCH_OCODE2` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
        $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    
        $rw = $q->getRowArray();
        $txt_branch_id = $rw['recid'];
        $B_CODE = trim($rw['BRNCH_OCODE2']);
        $B_CODE_POS = 'E' . trim($B_CODE);
            //END BRANCH
        }
        else{ 
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
            die();
        }
        
               
        if(!empty($start_date) && !empty($end_date) && !empty($start_time) && !empty($end_date)) {
        
        }
        else{
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Dates or Time is required!!!.</div>";
            die();
        }
        
     

        $recid = '';
        $voucher_trxno = '';
        
                //UPDATE
        if(!empty($mtkn_mntr)) { 
                //CHECK IF VALID PO
            die($recid);
            $str = "select aa.`recid`,aa.`voucher_trxno` from {$this->db_erp}.`gw_voucher_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_mntr'";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            if($q->resultID->num_rows == 0) {
                echo "No Records Found!!!";
                die();
            }
            $rw = $q->getRowArray();
            $recid = $rw['recid'];
            $voucher_trxno = $rw['voucher_trxno'];
            $q->freeResult();

        }//endif

            //generate threshold transaction number
            else{
                
            $voucher_trxno =  $this->mydataz->get_ctr_new_dr('PTC','',$this->db_erp,'CTRL_NO04');//VOUCHER TRANSACTION NO

            } //end else

            //validate if there is a valid material data
            if(count($adata1) > 0) { 
                $ame = array();
                $adatar1 = array();
                $adatar2 = array();

                
                for($aa = 0; $aa < count($adata1); $aa++) { 
                    $medata = explode("x|x",$adata1[$aa]);
                    $mat_mtkn = $adata2[$aa];
                    $voucher_code = (trim($medata[0]));
                    $voucher_amount = (trim($medata[1]));
                    $voucher_discount = (trim($medata[2]));
                    
                    
                    $amatnr = array();
                    if(empty($voucher_amount)){
                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please enter a Voucher Amount.</div>";
                        die(); 
                    
                    }
                    if(empty($voucher_discount)){
                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please enter a Voucher Discount.</div>";
                        die(); 
                    
                    }
                    if(empty($voucher_code)){
                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please enter a Voucher Code.</div>";
                        die(); 
                    
                    }
                    else{

                            $strr = "SELECT a.recid,a.`voucher_trxno`,date_format(a.`start_date`,'%m/%d/%Y') `m_start_date`,date_format(a.`end_date`,'%m/%d/%Y') `m_end_date` 
                            FROM {$this->db_erp}.`gw_voucher_hd` a  
                            JOIN {$this->db_erp}.`gw_voucher_dt` b 
                            ON a.`voucher_trxno` = b.`voucher_trxno`
                            WHERE a.`branch_code` = '$B_CODE' 
                            and b.`voucher_code` = '$voucher_code' and 
                            ((date(a.`start_date`) between DATE('$start_date') and DATE('$end_date')) or (DATE(a.`end_date`) between DATE('$start_date')  and DATE('$end_date')) ) 
                            group by a.recid,date(a.`start_date`),date(a.`end_date`) 
                            " ;
                            var_dump($voucher_code);
                            die();
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
                                    
                                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Voucher is already existing. <br/> [$voucher_code] - [{$data['voucher_trxno']}]</div>";
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
                                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Voucher is already existing. <br/> [$voucher_code] - [$nrw]</div>";
                                        die();
                                    }
                                    $qv->freeResult(); 
                                endif;
                            endif;

                        }
          

                        // validation
                        $rw = $q->getRowArray(); 
                        array_push($ame,$voucher_code); 
                        array_push($adatar1,$medata);
                        $adatac = $this->myposconn->POS_check_promo_exists($voucher_code,$B_CODE_POS,$start_date . ' ' . $start_time,$end_date . ' ' .  $end_time);
                        if (count($adatac) > 0):
                            $pos_promocode = trim($adatac['code']);
                            $pos_promotype = $adatac['me_promo_type'];
                            $pos_start_date = substr($adatac['start_date'],0,19);
                            $pos_end_date = substr($adatac['end_date'],0,19);
                            
                            
                            
                            if (!empty($mtkn_mntr)):
                                $nrecscount = $adatac['nrecsme'];	
                                
                                if($nrecscount > 1): 
                                    echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing in Multipe POS Promo already!!! <br/> 
                                    [$voucher_code]
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
                                    if ($voucher_trxno !== $pos_promocode && !empty($pos_promocode)):
                                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is existing or conflict POS Promo already!!! <br/> 
                                        [$voucher_code]
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
    
            }//end for 
                }  //end if 
                
                //insert header data
                if(count($adata1) > 0) { 
                    if(!empty($mtkn_mntr)) {       
                       $str = "
                       update {$this->db_erp}.`gw_voucher_hd` set 
                       `voucher_trxno` = '$voucher_trxno',
                       `branch_code` = '$B_CODE',
                       `discount_value` = '$discount_value',
                       `start_date` = '$start_date',
                       `start_time` = '$start_time',
                       `end_date` = '$end_date',
                       `voucher_name` = '$voucher_name',
                       `end_time`= '$end_time'
                       where recid = '$recid' 
                       ";
                       $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                       
                    } else {  
                    $str = "
                    insert into {$this->db_erp}.`gw_voucher_hd` (
                    `voucher_trxno`,
                    `branch_code`,
                    `branch_name`,
                    `voucher_name`,
                    `discount_value`,
                    `start_date`,
                    `start_time`,
                    `end_date`,
                    `end_time`,
                    `is_approved`,
                    `date_approved`,
                    `muser`,
                    `encd_date`
                    ) values(
                    '$voucher_trxno',
                    '$B_CODE',
                    '$branch_name',
                    '$voucher_name',
                    '$discount_value',
                    '$start_date',
                    '$start_time',
                    '$end_date',
                    '$end_time',
                    '$is_approved',
                    '$date_approved',
                    '$cuser',
                    now()
                    )
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                          //GET ID
                    $str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_fgpacktr from {$this->db_erp}.`gw_voucher_hd` aa where `voucher_trxno` = '$voucher_trxno' ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                    $rr = $q->getRowArray();
                    $q->freeResult();
                    }//endesle
                    
                    //insert details data
                    for($xx = 0; $xx < count($adatar1); $xx++) { 
                        
                        $xdata = $adatar1[$xx];
                        $voucher_code = $xdata[0];
                        $voucher_amount = $xdata[1];
                        $voucher_discount = $xdata[2];


                        
                        if(empty($mtkn_mntr)) {  
                            $str = "
                            insert into {$this->db_erp}.`gw_voucher_dt` ( 
                            `voucher_trxno`,
                            `amount`,
                            `voucher_code`,
                            `voucher_amount`,
                            `voucher_discount`,
                            `is_disabled`
                            ) values(
                            '$voucher_trxno',
                            '$amount',
                            '$voucher_code',
                            '$voucher_amount',
                            '$voucher_discount',
                            '$is_disabled'
                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        }   
                    }  //end for 
                    
                    //show success modal
                    if(empty($mtkn_mntr)) { 
                        $this->save_promo_voucher_from_POS($voucher_trxno,$voucher_name,$voucher_code,$voucher_amount,$voucher_discount,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                        echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Promo Voucher Code:{$voucher_trxno} </div>
                        <script type=\"text/javascript\"> 
                        function __fg_refresh_data() { 
                            try { 
                                $('#voucher_trxno').val('{$voucher_trxno}');
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
                                $this->save_promo_voucher_from_POS($voucher_trxno,$voucher_name,$voucher_code,$voucher_amount,$voucher_discount,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time);
                                echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
                                ";
                                die();
                            }
                            } else { 
                                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Material Data!!!.</div>";
                                die();
                            } //end if 
    } //end voucher_entry_save


    public function save_promo_voucher_from_POS($voucher_trxno,$voucher_name,$voucher_code,$voucher_amount,$voucher_discount,$B_CODE_POS,$adata1,$start_date,$start_time,$end_date,$end_time){
		$lcon = 0;
        $promo_name = $this->request->getVar('promo_name');
       
		if ($this->myposdbconn):
			$str_items = '';
			for($aa = 0; $aa < count($adata1); $aa++):
				$medata = explode("x|x",$adata1[$aa]);
				$voucher_code = trim($medata[0]);
				$voucher_amount = (trim($medata[1]));
				$voucher_discount = (trim($medata[2]));
                $str_items .= $voucher_code . ',';
             
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
			$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoVoucher] where [code] = ?";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array($voucher_trxno), array("Scrollable"=>"buffered") );
			if( $stmt === false) { 
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$row_count = sqlsrv_num_rows( $stmt );
			if ($row_count > 0):
				$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
				$lcon = 1;
				$promoid = $row['id'];
				$str = "
				update [diQtech_db].[dbo].[diQt_PromoVoucher] set 
				[name] = ?,
				[start_date] = ?,
				[end_date] = ?,
				[amount] = ?,
				[discount] = ?,
				[date_changed] = getdate() 
				where [id] = ?
				";
				$mstart_date = $start_date . ' ' . $start_time;
				$mend_date = $end_date . ' ' . $end_time;
			 
				$mevalarray = array($voucher_name,$mstart_date,$mend_date,$voucher_amount,$voucher_discount,
				$promoid);
				$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
				if( $stmt === false) { 
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				}
				sqlsrv_free_stmt( $stmt);
				
			else: 
				//get the incremental id prior adding of records 
				$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoVoucher]), 0) + 1 as me_rec_id";
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
					insert into [diQtech_db].[dbo].[diQt_PromoVoucher] ([id]
					,[code]
					,[name]
					,[start_date]
					,[end_date]
                    ,[amount]
                    ,[discount]
					,[date_changed]
					,[disable]
					) values (?,?,?,?,?,?,?,getdate(),?)
					";
					$mstart_date = $start_date . ' ' . $start_time;
					$mend_date = $end_date . ' ' . $end_time;
					$is_fixed_price = 1;
					$mevalarray = array($promoid,$voucher_trxno,$voucher_name,$mstart_date,$mend_date,
					$voucher_amount,$voucher_discount,0);
					$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
					if( $stmt === false) { 
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					}
					sqlsrv_free_stmt( $stmt);
				}
			endif;
			
			//branch detail table promo fixed/discount 
             //for voucher code  PromoVoucherCode
			if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
				$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoVoucherCode] where [code] = ?";
			    $stmt = sqlsrv_query( $this->myposdbconn, $str,array($voucher_trxno), array("Scrollable"=>"buffered") );
				
				if ( $stmt === false):
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ );
				endif;
				$row_count = sqlsrv_num_rows( $stmt );
				//update if existing 
				if ($row_count > 0):
					$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
					$promoidbr = $row['id'];
					$str = "
					update [diQtech_db].[dbo].[diQt_PromoVoucherCode] set [date_changed] = getdate() 
					where [id] = ? 
					";
					$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
					if ( $stmt === false):
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					endif;
					sqlsrv_free_stmt( $stmt);
				else:
					//add records 
					$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoVoucherCode]), 0) + 1 as me_rec_id";
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
						insert into [diQtech_db].[dbo].[diQt_PromoVoucherCode] (
						[id]
						,[promo_voucher_id]
						,[code]
						,[used]
                        ,[date_changed]
						,[disable]
						) values (?,?,?,?,getdate(),?) 
						";
						$mevalarray = array($promoidbr,$promoid,$voucher_code,0,0);
						$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
                     
                       
						if ( $stmt === false):
							die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						endif;
						sqlsrv_free_stmt( $stmt);
						
					endif;
				endif;
			endif;
           
			if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
				$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoVoucherBranch] where [promo_voucher_id] = ? and 
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
					update [diQtech_db].[dbo].[diQt_PromoVoucherBranch] set [date_changed] = getdate() 
					where [id] = ? 
					";
					$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
					if ( $stmt === false):
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					endif;
					sqlsrv_free_stmt( $stmt);
				else:
					//add records 
					$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoVoucherBranch]), 0) + 1 as me_rec_id";
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
						insert into [diQtech_db].[dbo].[diQt_PromoVoucherBranch] (
						[id]
						,[promo_voucher_id]
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



    public function voucher_post_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        //variable declarations
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " AND
            a.`voucher_trxno` LIKE '%{$msearchrec}%' ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_voucher_hd` a
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

    public function voucher_for_approval() {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_fgpacktr = $this->request->getVar('mtkn_fgpacktr');
        $voucher_trxno = '';
        
        if(!empty($mtkn_fgpacktr)) { 
            //SELECT IF ALREADY POSTED
            $str = "SELECT is_approved,voucher_trxno from {$this->db_erp}.`gw_voucher_hd` aa WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_fgpacktr' AND `is_approved` = 'N'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($qry->resultID->num_rows == 0) { 
                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
                die();
            }
            else{
                $rr = $qry->getRowArray();
                $voucher_trxno = $rr['voucher_trxno'];
            }
            $str = "
            update {$this->db_erp}.`gw_voucher_hd`
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


    public function download_voucher_barcode($voucher_trxno){
        //declaire variables
        $cfilelnk='';
        $file_name='';
        $chtmljs='';
        $voucher_code='';
        $discount_value='';
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $chtmljs ="";

        //check if there is an existing transaction number
        $str = "
        SELECT 
        a.`recid`,a.`voucher_trxno`
        FROM
        {$this->db_erp}.`gw_voucher_hd` a
        WHERE
        a.`voucher_trxno` = '{$voucher_trxno}'
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
            $voucher_trxno = $rr['voucher_trxno'];
        }

        //setup file
        if($voucher_trxno != ''){
            $file_name = 'Promo_Voucher'.'_'. $voucher_trxno . '_' . $cuser . '_'.date('Ymd').$this->mylibzsys->random_string(15);
            $mpathdn   = ROOTPATH;
            $_csv_path = '/public/downloads/me/';
            //if(!is_dir($_csv_path)) mkdir($_csv_path, '0755', true);
            $filepath = $mpathdn.$_csv_path.$file_name.'.csv';
            $cfilelnk = site_url() . 'downloads/me/' . $file_name.'.csv'; 
            
            $strr ="
            Select 
            a.`voucher_trxno`,
            a.`discount_value`,
            b.`amount`,
            b.`voucher_code`
            
            from
            gw_voucher_hd a 
            join
            gw_voucher_dt b
            on
            a.`voucher_trxno` = b.`voucher_trxno`
            where a.`voucher_trxno` = '$voucher_trxno'
            ";
            $qq = $this->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
            $rw = $qq->getRowArray();
            $amount = $rw['amount'];
            $voucher_code = $rw['voucher_code'];
            $discount_value = $rw['discount_value'];

            //if ($promothresholdname == 1) {
                //$promothresholdname = 'Everything @ ';
        }
          
            //generate hd and dt data to file format
            $str = "
            SELECT *
            INTO OUTFILE '{$filepath}'
            FIELDS TERMINATED BY '\t'
            LINES TERMINATED BY '\r\n'
            FROM(
                SELECT 
                'Branch_code',
                'PromoVoucherCode',
                'PromoVoucher_Name',
                'start_date',
                'end_date',
                'Amount',
                'Discount',
                'Voucher Code',
                'disable'

                UNION ALL
                
                SELECT
                CONCAT('E',a.`branch_code`),CONCAT(a.`voucher_trxno`),CONCAT (a.`discount_value` , ' off'),CONCAT(a.`start_date`,' ',a.`start_time`),CONCAT (a.`end_date`,' ',a.`end_time`),
                b.`amount`,a.`discount_value`,b.`voucher_code`, b.`is_disabled`
                FROM
                {$this->db_erp}.`gw_voucher_hd` a
                JOIN 
                {$this->db_erp}.`gw_voucher_dt` b
                ON 
                a.`voucher_trxno` = b.`voucher_trxno`
                WHERE
                a.`voucher_trxno` = '$voucher_trxno'
                
                ) oa
                ";
                $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                $chtmljs .= "
                <a href=\"{$cfilelnk}\" download=" . $file_name ." class='btn btn-dgreen btn-sm col-lg-12'> <i class='bi bi-save'></i> DOWNLOAD IT!</a>        
                ";
                echo $chtmljs;
    }//end download barcode

    //VOUCHER MODEL END
} //end main MyMDCustomerModel