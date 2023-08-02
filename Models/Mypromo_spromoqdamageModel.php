<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;
use App\Models\MyDatumModel;
use App\Models\MyUserModel;

class Mypromo_spromoqdamageModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->request = \Config\Services::request();
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
        $this->mylibzsys = model('App\Models\MyLibzSysModel');
		$this->mydatum =  new MyDatumModel();
		$this->myusermod =  new MyUserModel();
		$this->cusergrp = $this->myusermod->mysys_usergrp();
        $this->mydataz = model('App\Models\MyDatumModel');
        $this->myposconn = model('App\Models\MyPOSConnModel');
        $this->myposdbconn = $this->myposconn->connectdb();
	}	
	
    public function _search_code()
    {
        $term = $this->myusermod->mylibzdb->me_escapeString($this->myusermod->request->getVar('term'));
        $autoCompleteResult = array();
    
        $str = "SELECT ART_CODE ,ART_BARCODE1,art_uprice FROM mst_article WHERE art_code LIKE '%SPROMO%' and art_code LIKE '%$term%'";
    
        $q = $this->myusermod->mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $rw = $q->getResultArray();
    
        foreach ($rw as $row) {
            array_push($autoCompleteResult, array(
                "label" => $row['ART_CODE'], // Use 'label' instead of 'pro_code_name'
                "value" => $row['ART_CODE'],
                "promo_barcode" => $row['ART_BARCODE1'],
                "pro_code_disc" => $row['art_uprice'],  
                
            ));
        }
    
        $q->freeResult();
        echo json_encode($autoCompleteResult);
    }
    public function save_promo_spqd(){
        $cuser            = $this->myusermod->mysys_user();
        $mpw_tkn          = $this->myusermod->mpw_tkn();
        $mtkn_mntr = $this->request->getVar('mtkn_mntr');
        $__hmtkn_fgpacktr = '';
        $sw_transac_no = $this->request->getVar('sw_transac_no');
        $branch_name = $this->request->getVar('branch_name');
        $adata1 = $this->request->getVar('adata1');
        $adata2 = $this->request->getVar('adata2');
        $mtkn_branch = $this->request->getVar('mtkn_branch');
        $spqd_reason = $this->request->getVar('reason');
        $new_total_srp = $this->request->getVar('total_new_srp');
        $last_total_srp =$this->request->getVar('total_last_srp');
        $total_qty = $this->request->getVar('total_qty');
        $startDate = $this->request->getVar('startDate');
        $endDate =  $this->request->getVar('startDate');
        $startTime =  $this->request->getVar('startTime');
        $endTime =  $this->request->getVar('endTime');
        $txt_spromo = $this->request->getVar('txt_spromo');
        $isValid = false;
        $B_CODE ='';
      
        if((!empty($branch_name)) && !empty($mtkn_branch)) {
            $str = "SELECT `recid`,`BRNCH_MBCODE` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
            $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
            $rw = $q->getRowArray();
            $txt_branch_id = $rw['recid'];
            $B_CODE = trim($rw['BRNCH_MBCODE']);
            $B_CODE_POS = $B_CODE;
           
          
			if (empty($B_CODE)): 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>ERROR</strong> Internal Branch POS CODE</div>";
				die();
			endif;
			$q->freeResult();
		
		}  
		else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
			die();
		} //end if
        //validate date
        if(!empty($startDate) && !empty($endDate) && !empty($endTime) && !empty($endTime)) {

        }
        else{
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Dates or Time is required!!!.</div>";
            die();
        }

        $recid = '';
        $spqd_trxno = '';
        $spqd_trxno2 = '';
        if(!empty($mtkn_mntr)) { 
            //CHECK IF VALID PO
        
        $str = "select aa.`id`, aa.`spqd_trx_no` from {$this->db_erp}.`trx_pos_promo_spqd_hd` aa where spqd_trx_no = '$mtkn_mntr'";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if($q->resultID->num_rows == 0) {
            echo "test[$mpw_tkn]-";
            echo $mtkn_mntr;
            echo "No Records Found!!!";
            die();
        }
        $rw = $q->getRowArray();
        $recid = $rw['id'];
        $spqd_trxno = $rw['spqd_trx_no'];
        $q->freeResult();
        

        }//endif
       
        //generate promo transaction number
        else{
         
            $spqd_trxno =  $this->mydataz->get_ctr_promotions('CLR','',$this->db_erp,'CTRL_NO03');//PBXTY TRANSACTION NO
       

        } //end else
     
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
                $_qty = $medata[2];
                $last_srp = $medata[3];
                $_amount = $medata[4];
                $_promocode = $medata[5];
                $new_srp = $medata[6];
                $new_amount = $medata[7];
                $_profitloss = $medata[8];
                $item_barcode = $medata[9];
                
            
               
                $amatnr = array();
                $str = " SELECT item_barcode ,spqd_trx_no ,promo_code_spqd from `trx_pos_promo_spqd_dt` where item_barcode = '$item_barcode'  and promo_code_spqd = '$txt_spromo' and is_disable = 'N'
                "
                ;
                $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                $getrw = $q->getNumRows();
                $rws = $q->getResultArray();
           
                // if (!empty($getrw)){
                //     foreach ($q->getResultArray() as $data) { 
                //     echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> There is  same promo active in this item code!!!<br/>[$mitemc]- [{$data['spqd_trx_no']}]</div>";
                //     die();
                //     }

                // }
                if(empty($item_barcode)){
                    echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid barcode !!!<br/></div>";
                    die();
                }
                if(empty($_qty)){
                    echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Quanty !!!<br/></div>";
                    die();
                }
                if(!empty($mitemc)){
                    $str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where ART_CODE = '$mitemc' ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                    if($q->resultID->num_rows == 0) {
                            
                           
                        echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data !!!<br/></div>";
                      
                    }//end if
                    $strr = "SELECT a.id, b.`promo_code_spqd`, b.`is_disable`, a.`spqd_trx_no`, DATE_FORMAT(a.`start_date`, '%m/%d/%Y') AS `m_start_date`, DATE_FORMAT(a.`end_date`, '%m/%d/%Y') AS `m_end_date`
                    FROM `trx_pos_promo_spqd_hd` AS a
                    JOIN `trx_pos_promo_spqd_dt` AS b ON a.`spqd_trx_no` = b.`spqd_trx_no`
                    WHERE a.`branch_code` = '$B_CODE' 
                       
                        AND b.`is_disable` = 'N'
                        AND b.`item_barcode` = '$item_barcode' 
                        AND 
                 
                    ((date(a.`start_date`) between DATE('$startDate') and DATE('$endDate')) or (DATE(a.`end_date`) between DATE('$startDate')  and DATE('$endDate')) ) 
                            group by a.id,date(a.`start_date`),date(a.`end_date`) 
                            " ;
					
					$qv = $this->myusermod->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$nrw = $qv->getNumRows();
                    $withDateRange =0;
                    if ($nrw > 0){
                        $withDateRange = 1;
                    }
                   
					//skip when editing
                   
					if ($recid > 0):
						if ($nrw > 0): 
                        
							$rv = $qv->getRowArray();
							//check if no other entries but have existing for editing
							if($nrw == 1 && $rv['id'] == $recid): 
							else:
								foreach ($qv->getResultArray() as $data) { 
                              
									echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already have active  <br/> [$mitemc] - [{$data['spqd_trx_no']}]</div>";
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
                         
             
                    $rw = $q->getRowArray(); 
                    $mmat_rid = $rw['recid'];  
                    array_push($ame,$mitemc); 
                    array_push($adatar1,$medata);
                    array_push($adatar2,$mmat_rid);
                    $adatac = $this->myposconn->POS_check_promo_exists($mitemc,$B_CODE_POS,$startDate . ' ' . $startTime,$endDate . ' ' .  $endTime);
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
								if ($spqd_trxno !== $pos_promocode && !empty($pos_promocode)):
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
                                $str = " SELECT item_barcode ,spqd_trx_no ,promo_code_spqd from `trx_pos_promo_spqd_dt` where item_barcode = '$item_barcode'  and promo_code_spqd = '$txt_spromo' and is_disable = 'N'
                                "  ;
                                $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                                $getrw = $q->getNumRows();
                                
                                    if (!empty($getrw)){
                                        foreach ($q->getResultArray() as $data) { 
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
                                        }

                                    }else{
                                        
                                    }
								
							endif;
						endif;
					endif;
					// end POS_check_promo_exists
                }
              
            } //end for
       
   

          
            if(count($adatar1) > 0) { 
                if(!empty($mtkn_mntr)) {       
                   $str = "
                   update {$this->db_erp}.`trx_pos_promo_spqd_hd` set 
                   `branch_code` = '$B_CODE',
                   `spqd_reason` = '$spqd_reason',
                   `total_qty` = '$total_qty',
                   `new_total_srp` = '$new_total_srp',
                   `start_date` = '$startDate',
                   `end_date` = '$endDate',
                   `start_time` = '$startTime',
                   `end_time` = '$endTime',
                   `spromo_code` = '$txt_spromo',
                   `last_total_srp` = '$last_total_srp'

                   where  spqd_trx_no = '$mtkn_mntr'
                   ";
                   $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                 
               } else {  
          
            $str_hd = "
            insert into {$this->db_erp}.`trx_pos_promo_spqd_hd` (
            `branch_code`,
            `spqd_trx_no`,
            `spqd_reason`,
            `total_qty`,
            `new_total_srp`,
            `start_date` ,
            `end_date` ,
            `spromo_code`,
            `start_time` ,
            `end_time` ,
            `last_total_srp`
            
          

            )values(
            '$B_CODE',
            '$spqd_trxno',
            '$spqd_reason',
            '$total_qty',
            '$new_total_srp',
            '$startDate',
            '$endDate',
            '$txt_spromo',
            '$startTime',
            '$endTime',
            '$last_total_srp'
             )
            ";
            $this->myusermod->mylibzdb->myoa_sql_exec($str_hd,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
            }
         
            $unique_promocodes = array();
            //insert data
            $valid = 1;
            $validItems = array();
            for($aa = 0; $aa < count($adata1); $aa++) { 
            
                $medata = explode("x|x",$adata1[$aa]);
                $mitemc = trim($medata[0]);
    
                $mdesc = $medata[1];
          
                $_qty = $medata[2];
                $last_srp = $medata[3];
                $_amount = $medata[4];
                $_promocode = $medata[5];
                $new_srp = $medata[6];
                $new_amount = $medata[7];
                $_profitloss = $medata[8];
                $item_barcode = $medata[9];
                $promo_barcode = $medata[10];
                $art_ucost = $medata[12];
                $amatnr = array();
       
      
   
              
                if(!empty($mtkn_mntr) && $getrw == 0) {  
                    $isValid = $this->isBelowCost([$new_srp], $art_ucost);
                    if ($isValid){
                        $valid = 1;
                        $str2 = "
                        insert into {$this->db_erp}.`trx_pos_promo_spqd_dt` ( 
                            `branch_code`,
                            `branch_name`,
                            `spqd_trx_no`,
                            `item_code`,
                            `item_desc_spqd`,
                            `qty_spqd`,
                            `last_srp`,
                            `last_srp_amount`,
                            `promo_code_spqd`,   
                            `new_srp`, 
                            `new_srp_amount`, 
                            `profit_loss`,
                            `promo_barcode`,
                            `item_barcode`
                            ) values(
                            '$B_CODE',
                            '$branch_name',
                            '$spqd_trxno',
                            '$mitemc',
                            '$mdesc',
                            '$_qty',
                            '$last_srp',
                            '$_amount',
                            '$txt_spromo',
                            '$new_srp',
                            '$new_amount',
                            '$_profitloss',
                            '$promo_barcode',
                            '$item_barcode'
                            )
                            ";
                        $q = $this->mylibzdb->myoa_sql_exec($str2,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                       
                    }else{
                        $str2 = "
                        insert into {$this->db_erp}.`trx_pos_promo_spqd_dt` ( 
                            `branch_code`,
                            `branch_name`,
                            `spqd_trx_no`,
                            `item_code`,
                            `item_desc_spqd`,
                            `qty_spqd`,
                            `last_srp`,
                            `last_srp_amount`,
                            `promo_code_spqd`,   
                            `new_srp`, 
                            `new_srp_amount`, 
                            `profit_loss`,
                            `promo_barcode`,
                            `item_barcode`
                            ) values(
                            '$B_CODE',
                            '$branch_name',
                            '$spqd_trxno2',
                            '$mitemc',
                            '$mdesc',
                            '$_qty',
                            '$last_srp',
                            '$_amount',
                            '$txt_spromo',
                            '$new_srp',
                            '$new_amount',
                            '$_profitloss',
                            '$promo_barcode',
                            '$item_barcode'
                            )
                            ";
                        $q = $this->mylibzdb->myoa_sql_exec($str2,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                    }
                 
                   
                } 
          
          
                if(!empty($mtkn_mntr)) { 
                    $isValid = $this->isBelowCost([$new_srp], $art_ucost);
                    if ($isValid){
                        $valid = 1;
                
                    $str2 = "
                    
                      update {$this->db_erp}.`trx_pos_promo_spqd_dt` set 
                      `branch_code` = '$B_CODE',
                      `branch_name` = '$branch_name',
                      `spqd_trx_no` = '$spqd_trxno',
                      `item_code` = '$mitemc',
                      `item_desc_spqd` = '$mdesc',
                      `qty_spqd` = '$_qty',
                      `last_srp` = '$last_srp',
                      `last_srp_amount` = '$_amount',
                      `promo_code_spqd` = '$txt_spromo',
                      `new_srp` = '$new_srp',
                      `new_srp_amount` = '$new_amount',
                      `profit_loss` = '$_profitloss',
                      `promo_barcode` = '$promo_barcode',
                      `item_barcode` = '$item_barcode'
                      
                      where spqd_trx_no = '$spqd_trxno' and item_barcode = '$item_barcode'
                        ";
                    $q1 = $this->mylibzdb->myoa_sql_exec($str2,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                   
                    }else{
                        
                    $str2 = "
                    
                    insert into {$this->db_erp}.`trx_pos_promo_spqd_dt` ( 
                        `branch_code`,
                        `branch_name`,
                        `spqd_trx_no`,
                        `item_code`,
                        `item_desc_spqd`,
                        `qty_spqd`,
                        `last_srp`,
                        `last_srp_amount`,
                        `promo_code_spqd`,   
                        `new_srp`, 
                        `new_srp_amount`, 
                        `profit_loss`,
                        `promo_barcode`,
                        `item_barcode`
                        ) values(
                        '$B_CODE',
                        '$branch_name',
                        '$spqd_trxno2',
                        '$mitemc',
                        '$mdesc',
                        '$_qty',
                        '$last_srp',
                        '$_amount',
                        '$txt_spromo',
                        '$new_srp',
                        '$new_amount',
                        '$_profitloss',
                        '$promo_barcode',
                        '$item_barcode'
                        )
                        ";
                    $q = $this->mylibzdb->myoa_sql_exec($str2,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                    }
              }
        
              if (empty($mtkn_mntr)) {

                $isValid = $this->isBelowCost([$new_srp], $art_ucost);
              
                    if ($isValid && $withDateRange == 0) {
                        $valid = 1;
                        
                        $str1 = "
                            UPDATE {$this->db_erp}.`trx_pos_promo_spqd_dt` SET 
                            `is_disable` = 'Y'
                            WHERE 
                            `item_barcode` = '$item_barcode'
                        ";
                        $q1 = $this->mylibzdb->myoa_sql_exec($str1, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        $str = "
                            INSERT INTO {$this->db_erp}.`trx_pos_promo_spqd_dt` ( 
                                `branch_code`,
                                `branch_name`,
                                `spqd_trx_no`,
                                `item_code`,
                                `item_desc_spqd`,
                                `qty_spqd`,
                                `last_srp`,
                                `last_srp_amount`,
                                `promo_code_spqd`,   
                                `new_srp`, 
                                `new_srp_amount`, 
                                `profit_loss`,
                                `promo_barcode`,
                                `item_barcode`,
                                `isvalid`
                            ) VALUES (
                                '$B_CODE',
                                '$branch_name',
                                '$spqd_trxno',
                                '$mitemc',
                                '$mdesc',
                                '$_qty',
                                '$last_srp',
                                '$_amount',
                                '$txt_spromo',
                                '$new_srp',
                                '$new_amount',
                                '$_profitloss',
                                '$promo_barcode',
                                '$item_barcode',
                                'Y'
                            )
                        ";
                    
                        $q = $this->mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        $validItems[] = $mitemc;
                     
                     
                    } else {
               
                        $str = "
                            INSERT INTO {$this->db_erp}.`trx_pos_promo_spqd_dt` ( 
                                `branch_code`,
                                `branch_name`,
                                `spqd_trx_no`,
                                `item_code`,
                                `item_desc_spqd`,
                                `qty_spqd`,
                                `last_srp`,
                                `last_srp_amount`,
                                `promo_code_spqd`,   
                                `new_srp`, 
                                `new_srp_amount`, 
                                `profit_loss`,
                                `promo_barcode`,
                                `item_barcode`
                            ) VALUES (
                                '$B_CODE',
                                '$branch_name',
                                '$spqd_trxno',
                                '$mitemc',
                                '$mdesc',
                                '$_qty',
                                '$last_srp',
                                '$_amount',
                                '$txt_spromo',
                                '$new_srp',
                                '$new_amount',
                                '$_profitloss',
                                '$promo_barcode',
                                '$item_barcode'
                            )
                        ";
                        
                        $q = $this->mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                }
            }
    }
   

            if(empty($mtkn_mntr)) { 
                $str = "select isvalid from {$this->db_erp}.`trx_pos_promo_spqd_dt` where spqd_trx_no = '$spqd_trxno' and isvalid ='N' ";
                $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                $nrws = $q->getNumRows();

                if($withDateRange >= 1 || $nrws > 0){
                    $this->save_promo_spqd_from_POS($spqd_trxno,$validItems,$spqd_reason,$isValid,$B_CODE_POS,$txt_spromo,$adata1,$startDate,$startTime,$endDate,$endTime);
                    echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully(FOR APPROVAL)!!! Spromo No.:{$spqd_trxno} </div>

                    <script type=\"text/javascript\"> 
                    function __fg_refresh_data() { 
                        try { 
                            $('#txt_spqd_trx_no').val('{$spqd_trxno}');
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
                }else{
                 
                    $this->save_promo_spqd_from_POS($spqd_trxno,$validItems,$spqd_reason,$isValid,$B_CODE_POS,$txt_spromo,$adata1,$startDate,$startTime,$endDate,$endTime);
                    echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Spromo No.:{$spqd_trxno} </div>
                    <script type=\"text/javascript\"> 
                    function __fg_refresh_data() { 
                        try { 
                            $('#txt_spqd_trx_no').val('{$spqd_trxno}');
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
                }
            }
           
            if(!empty($mtkn_mntr)) { 
                $str = "select isvalid from {$this->db_erp}.`trx_pos_promo_spqd_dt` where spqd_trx_no = '$spqd_trxno' and isvalid ='N' ";
                $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                $nrws = $q->getNumRows();
                if($withDateRange == 0 && $nrws = 0){
                        $this->save_promo_spqd_from_POS($spqd_trxno,$spqd_reason,$B_CODE_POS,$txt_spromo,$adata1,$startDate,$startTime,$endDate,$endTime);
                        echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!! </div>
                        ";
                        die();
                    }
                 
                } else { 
                    echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED(FOR APPROVAL)!!! </div>";
                    die();
                }
        };//end if
     }//end if
    }

    function isBelowCost($data, $cost) {
        global $isValid;
    
        $isValid = false;
        $item_cost = $cost;
      
    
        foreach ($data as $item) {
            $value = $item;
            if ($value > $item_cost) {
                $isValid = true;
            
            }
        }
     
        return $isValid;

    }
    public function spqd_for_approval() {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_recid = $this->request->getVar('mtkn_recid');
        $sw_transac_no = $this->request->getVar('sw_transac_no');
        $branch_name = $this->request->getVar('branch_name');
        $adata1 = $this->request->getVar('adata1');
        $adata2 = $this->request->getVar('adata2');
        $mtkn_branch = $this->request->getVar('mtkn_branch');
        $spqd_reason = $this->request->getVar('reason');
        $new_total_srp = $this->request->getVar('total_new_srp');
        $last_total_srp =$this->request->getVar('total_last_srp');
        $total_qty = $this->request->getVar('total_qty');
        $art_code = $this->request->getVar('art_code');
        $item_bcode = $this->request->getVar('item_bcode');
        $per_line_save = $this->request->getVar('per_line_save');
        $startDate ='';
        $endDate =  '';
        $startTime ='';
        $endTime =  '';
        $txt_spromo ='';
        $isValid = false;
     
        $str_items = '';
        $adataApproval=array();
        $spqd_trxno = $this->request->getVar('spqd_trxno');;
        $B_CODE_POS =  '';
        $str2 = "
           SELECT * FROM `trx_pos_promo_spqd_hd` AS aa
           JOIN `trx_pos_promo_spqd_dt` AS bb ON aa.spqd_trx_no = bb.spqd_trx_no
           WHERE aa.spqd_trx_no = '$spqd_trxno' AND bb.isvalid = 'N'
       ";
       
       $q = $this->mylibzdb->myoa_sql_exec($str2, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
       $rows = $q->getNumRows();
   
       foreach ($q->getResultArray() as $data) {
           $str_items = '';
           $adataApproval = array(); 
           $spqd_trxno = $data['spqd_trx_no'];
           $B_CODE_POS = $data['branch_code'];
           $txt_spromo = $data['spromo_code'];
           $startDate = $data['start_date'];
           $startTime = $data['start_time'];
           $endDate = $data['end_date'];
           $endTime = $data['end_time'];
           $mitemc = $data['item_code'];
           $new_srpval = $data['new_srp'];
           $qty= $data['qty_spqd'];
           $new_srp = intval($new_srpval);
       
           $adataApproval[] = array(
               'qty' => $qty,
               'new_srp' => $new_srp,
               'mitemc' => $mitemc,
               'startDate' => $startDate,
               'startTime' => $startTime,
               'endDate' => $endDate,
               'endTime' => $endTime
           );
          
       }
       $this->save_promo_spqd_from_POS2($spqd_trxno,$per_line_save,$art_code,$B_CODE_POS, $adataApproval, $txt_spromo,$adata1,$startDate,$startTime,$endDate,$endTime);
        if ($per_line_save){
            $str1 = "
            UPDATE {$this->db_erp}.`trx_pos_promo_spqd_dt` SET 
            `is_disable` = 'Y'
            WHERE 
            `item_barcode` = '$item_bcode'
                ";
                $q1 = $this->mylibzdb->myoa_sql_exec($str1, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
     
        if((!empty($branch_name)) && !empty($mtkn_branch)) {
            $str = "SELECT `recid`,`BRNCH_MBCODE` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
            $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
            $rw = $q->getRowArray();
            $txt_branch_id = $rw['recid'];
            $B_CODE = trim($rw['BRNCH_MBCODE']);
            $B_CODE_POS = $B_CODE;
        }
        if(!empty($mtkn_recid)) { 
            //SELECT IF ALREADY POSTED
            $str = "select is_approved,spqd_trx_no from {$this->db_erp}.`trx_pos_promo_spqd_dt` aa WHERE spqd_trx_no = '$spqd_trxno'AND item_code = '$art_code' AND `is_approved` = 'N'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $numrows = $qry->getNumRows();
            if($qry->getNumRows() == 0) { 
                $str = "
                UPDATE d_ap2.`trx_pos_promo_spqd_hd` AS aa
                SET aa.`is_approved` = 'Y',
                WHERE  aa.spqd_trx_no = '$spqd_trxno'
                AND `is_approved` = 'N';
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
             
            }
            else{
                $rr = $qry->getRowArray();
                $spqd_trxno = $rr['spqd_trx_no'];
            }
            $str = "
            UPDATE d_ap2.`trx_pos_promo_spqd_hd` AS aa
            JOIN trx_pos_promo_spqd_dt AS bb ON aa.spqd_trx_no = bb.spqd_trx_no
            SET aa.`is_approved` = 'Y',
                bb.`is_approved` = 'Y',
                aa.`date_approved` = NOW(),
                bb.`is_disable` = 'N',
                bb.`date_approved` = NOW(),
                bb.`isvalid` = 'Y'
            WHERE item_code = '$art_code' AND aa.spqd_trx_no = '$spqd_trxno'
            AND bb.`is_approved` = 'N';
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            echo  "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Approved Successfully!!!</div>
            
            ";
            
        }//end if!empty
    } //end if perline
    else{//group saving
        for($aa = 0; $aa < count($adata1); $aa++) { 
            
            $medata = explode("x|x",$adata1[$aa]);
            $mitemc = trim($medata[0]);
    
            $mdesc = $medata[1];
      
            $_qty = $medata[2];
            $last_srp = $medata[3];
            $_amount = $medata[4];
            $_promocode = $medata[5];
            $new_srp = $medata[6];
            $new_amount = $medata[7];
            $_profitloss = $medata[8];
            $item_barcode = $medata[9];
            $promo_barcode = $medata[10];
            $art_ucost = $medata[12];
            $amatnr = array();
    
           $str1 = "
           UPDATE {$this->db_erp}.`trx_pos_promo_spqd_dt` SET 
           `is_disable` = 'Y'
           WHERE 
           `item_barcode` = '$item_barcode'
            ";
            $q1 = $this->mylibzdb->myoa_sql_exec($str1, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
           }
            if((!empty($branch_name)) && !empty($mtkn_branch)) {
                $str = "SELECT `recid`,`BRNCH_MBCODE` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
                $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
                $rw = $q->getRowArray();
                $txt_branch_id = $rw['recid'];
                $B_CODE = trim($rw['BRNCH_MBCODE']);
                $B_CODE_POS = $B_CODE;
            }
            if(!empty($mtkn_recid)) { 
                //SELECT IF ALREADY POSTED
                $str = "select is_approved,spqd_trx_no from {$this->db_erp}.`trx_pos_promo_spqd_hd` aa WHERE sha2(concat(`id`,'{$mpw_tkn}'),384) = '$mtkn_recid' AND `is_approved` = 'N'";
                $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    
                if($qry->resultID->num_rows == 0) { 
                    echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
                    die();
                }
                else{
                    $rr = $qry->getRowArray();
                    $spqd_trxno = $rr['spqd_trx_no'];
                }
                $str = "
                UPDATE d_ap2.`trx_pos_promo_spqd_hd` AS aa
                JOIN trx_pos_promo_spqd_dt AS bb ON aa.spqd_trx_no = bb.spqd_trx_no
                SET aa.`is_approved` = 'Y',
                    aa.`date_approved` = NOW(),
                    bb.`isvalid` = 'Y'
                WHERE sha2(concat(aa.`id`,'{$mpw_tkn}'),384) = '$mtkn_recid' AND aa.spqd_trx_no = '$spqd_trxno'
                AND `is_approved` = 'N';
                ";
                $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                
                echo  "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Approved Successfully!!!</div>
                
                ";
               
        }

    }
}//end  fgpack update approval

public function save_promo_spqd_from_POS2($spqd_trxno,$per_line_save,$art_code,$B_CODE_POS, $adataApproval,$txt_spromo,$adata1,$startDate,$startTime,$endDate,$endTime){
    
    $lcon = 0;
   

    if ($this->myposdbconn):
     $_items = array();
     for($aa = 0; $aa < count($adata1); $aa++):
         $medata = explode("x|x",$adata1[$aa]);
         $mitemc = trim($medata[0]);
         $mdesc = (trim($medata[1]));
         $qty = (trim($medata[2]));
 
         $new_srp= (trim($medata[5]));
         $art_ucost= (trim($medata[12]));
     
         $_items[]= $mitemc ;
     endfor;
     $str_items = implode(',', $_items);
     $str_items = "'" . $str_items . "'"; 
        //group items as comma delimited as required 
        if (count($_items) > 1) {
            $str = "select STRING_AGG(aa.[id], ',') AS meiditems  from [diQtech_db].[dbo].[diQt_Product] aa join ( 
            SELECT DISTINCT CAST(value AS varchar) AS meproditemc FROM STRING_SPLIT({$str_items}, ',') WHERE value != ''
            ) bb on (aa.[stock_no] = bb.meproditemc)";
        } else {
            $str_items = rtrim($str_items, ',');
            $str = "select aa.[id] AS meiditems from [diQtech_db].[dbo].[diQt_Product] aa where aa.[stock_no] = {$str_items}";
            echo $str_items;
        }

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
     
     $promoid = 0;
     $meproditemsArray = explode(",", $meproditems);
    //  foreach ($meproditemsArray as $meproditem) {
    
    //     $str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDamage] where [code] = ? and [product_id] = ?";
    //     $stmt = sqlsrv_query( $this->myposdbconn, $str,array($spqd_trxno,$meproditem), array("Scrollable"=>"buffered") );
    //         if( $stmt === false) { 
    //             die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    //         }
    //     $row_count = sqlsrv_num_rows( $stmt );
    //     echo $row_count .'-'.$meproditem;
    //     if ($row_count > 0):
    //         $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
    //         $lcon = 1;
    //         $promoid = $row['id'];
    //         $str = "
    //         update [diQtech_db].[dbo].[diQt_PromoDamage] set 
    //         [name] = ?,
    //         [start_date] = ?,
    //         [end_date] = ?,
    //         [is_peso_discount] = ?,
    //         [is_percent_discount] = ?,
    //         [discount] = ?,
    //         [date_changed] = getdate() ,
    //         [disable] = ?,
    //         [product_id] = ?
    //         where [id] = ?
    //         ";
        
    //         $mstart_date = $startDate . ' ' . $startTime;
    //         $mend_date = $endDate . ' ' . $endTime;
        
            
    //         $mevalarray = array($txt_spromo,$mstart_date,$mend_date,1,0,
    //         $new_srp,0,$meproditem,$promoid);
    //         $stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
    //         if( $stmt === false) { 
    //             die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    //         }
    //         sqlsrv_free_stmt( $stmt);
    //     endif;
    // }


             $str_items = $art_code;
             $str_items = "'" . $str_items . "'"; 
             //group items as comma delimited as required 
             $str = "select aa.[id] AS meiditems from [diQtech_db].[dbo].[diQt_Product] aa where aa.[stock_no] = {$str_items}";
             $stmt = sqlsrv_query( $this->myposdbconn, $str,array(), array("Scrollable"=>"buffered") );
             if( $stmt === false) {
                 die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
             }
             $row_count = sqlsrv_num_rows( $stmt );
             $meproditems2 = '';
             if($row_count > 0) {
                 $nn = 1;
                 while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
                     $meproditems2 = $row['meiditems'];
                 }
             } else { 
                 echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>No Record/s found [POS_ITEM]!!!</strong></div>";
             }
             sqlsrv_free_stmt( $stmt);
             $meproditemsArray2 = explode(",", $meproditems2);

          foreach ($meproditemsArray2 as $meproditem2) {
            
            if ($per_line_save) {
                    $str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDamage] where [code] = ? and [product_id] = ?";
                    $stmt = sqlsrv_query( $this->myposdbconn, $str,array($spqd_trxno,$meproditem2), array("Scrollable"=>"buffered") );
                if( $stmt === false) { 
                    die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                }
                $row_count = sqlsrv_num_rows( $stmt );
            
                if ($row_count == 0):
            
 
                $str1 = "
                UPDATE [diQtech_db].[dbo].[diQt_PromoDamage] SET 
                [disable] = ?
                WHERE [product_id] = ?
                ";
                $mevalarray = array(1, $meproditems2);
                $stmt1 = sqlsrv_query($this->myposdbconn, $str1, $mevalarray, array("Scrollable" => "buffered"));
                if ($stmt1 === false) {
                    die(print_r(sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                }
                 sqlsrv_free_stmt($stmt1);
                //get the incremental id prior adding of records 
                $str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoDamage]), 0) + 1 as me_rec_id";
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
                    insert into [diQtech_db].[dbo].[diQt_PromoDamage] ([id]
                    ,[code]
                    ,[name]
                    ,[start_date]
                    ,[end_date]
                    ,[is_peso_discount]
                    ,[is_percent_discount]
                    ,[discount]
                    ,[date_changed]
                    ,[disable]
                    ,[product_id]
                    ) values (?,?,?,?,?,?,?,?,getdate(),?,?)
                    ";
                    
                    $mstart_date = $startDate . ' ' . $startTime;
                    $mend_date = $endDate . ' ' . $endTime;
                    $is_fixed_price = 1;
                    
                    $mevalarray = array($promoid,$spqd_trxno,$txt_spromo,$mstart_date,$mend_date,
                    1,0,$new_srp,0,$meproditems2);
                    $stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
                    if( $stmt === false) { 
                        die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                    }
                    sqlsrv_free_stmt( $stmt);
                }
            endif;
        }//end for
     }//end of per line
   


      $meproditemsArray = explode(",", $meproditems);
       //for   diQt_PromoDamageDetail
     
       if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
      
         foreach ($meproditemsArray as $meproditem) {

         $str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDamageDetail] where [promo_damage_id] = ? and 
         [product_id] = ?";
         $stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoid,$meproditem), array("Scrollable"=>"buffered") );
         if ( $stmt === false):
             die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ );
         endif;
         $row_count = sqlsrv_num_rows( $stmt );
         //update if existing 
       
         if ($row_count > 0):
             $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
             $promoidbr = $row['id'];
             $str = "
             update [diQtech_db].[dbo].[diQt_PromoDamageDetail] set [date_changed] = getdate() 
             where [id] = ? 
             ";
             $stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
             if ( $stmt === false):
                 die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
             endif;
             sqlsrv_free_stmt( $stmt);
            endif;
        }
    else:   
                if ($per_line_save) {
                    $str_items = $art_code;
                    $str_items = "'" . $str_items . "'"; 
                 
                 //group items as comma delimited as required 
                 $str = "select STRING_AGG(aa.[id], ',') AS meiditems  from [diQtech_db].[dbo].[diQt_Product] aa join ( 
                 SELECT DISTINCT CAST(value AS varchar) AS meproditemc FROM STRING_SPLIT({$str_items}, ',') WHERE value != ''
                 ) bb on (aa.[stock_no] = bb.meproditemc)";
                 $stmt = sqlsrv_query( $this->myposdbconn, $str,array(), array("Scrollable"=>"buffered") );
                 if( $stmt === false) {
                     die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                 }
                 $row_count = sqlsrv_num_rows( $stmt );
                 $meproditems2 = '';
                 if($row_count > 0) {
                     $nn = 1;
                     while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
                         //echo $row['meiditems'] . '<br/>';
                         $meproditems2 = $row['meiditems'];
                     }
                 } else { 
                     echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>No Record/s found [POS_ITEM]!!!</strong></div>";
                 }
                 sqlsrv_free_stmt( $stmt);
             } else{
                 $meproditems2 = $meproditems;
             }
            
             foreach ($meproditemsArray2 as $meproditems2) {
                $str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDamageDetail] where [promo_damage_id] = ? and 
                [product_id] = ?";
                $stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoid,$meproditem2), array("Scrollable"=>"buffered") );
                if( $stmt === false) { 
                    die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                 }
               print_r($meproditems2)  ;
            $row_count = sqlsrv_num_rows( $stmt );
            echo $row_count;
            if ($row_count == 0):
             $meproditemsArray2 = explode(",", $meproditems2);
             foreach ($meproditemsArray2 as $meproditem2) {
                 $str1 = "
                 update [diQtech_db].[dbo].[diQt_PromoDamageDetail] set 
                 [disable] = ?
                 FROM [diQtech_db].[dbo].[diQt_PromoDamageBranch]
                 JOIN [diQtech_db].[dbo].[diQt_PromoDamageDetail]
                 ON [diQtech_db].[dbo].[diQt_PromoDamageBranch].promo_damage_id = [diQtech_db].[dbo].[diQt_PromoDamageDetail].promo_damage_id
                 where [diQtech_db].[dbo].[diQt_PromoDamageDetail].product_id = ? AND [diQtech_db].[dbo].[diQt_PromoDamageBranch].branch_id =?
                 ";
 
                 $mevalarray = array(1,$meproditem2,$brid);
                 $stmt1 = sqlsrv_query( $this->myposdbconn, $str1,$mevalarray, array("Scrollable"=>"buffered") );
                 if( $stmt1 === false) { 
                     die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                 }
                 sqlsrv_free_stmt( $stmt1);
                 //for updating the branch
                 $str2 = "
                 update [diQtech_db].[dbo].[diQt_PromoDamageBranch] set 
                 [disable] = ?
                 FROM [diQtech_db].[dbo].[diQt_PromoDamageBranch]
                 JOIN [diQtech_db].[dbo].[diQt_PromoDamageDetail]
                 ON [diQtech_db].[dbo].[diQt_PromoDamageBranch].promo_damage_id = [diQtech_db].[dbo].[diQt_PromoDamageDetail].promo_damage_id
                 where [diQtech_db].[dbo].[diQt_PromoDamageDetail].product_id = ? AND [diQtech_db].[dbo].[diQt_PromoDamageBranch].branch_id = ?
                 ";
 
                 $mevalarray = array(1,$meproditem2,$brid);
                 $stmt2 = sqlsrv_query( $this->myposdbconn, $str2,$mevalarray, array("Scrollable"=>"buffered") );
                 if( $stmt2 === false) { 
                     die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                 }
                 sqlsrv_free_stmt( $stmt2);
                 $str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoDamageDetail]), 0) + 1 as me_rec_id";
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
                     insert into [diQtech_db].[dbo].[diQt_PromoDamageDetail] (
                     [id]
                     ,[promo_damage_id]
                     ,[product_id]
                     ,[quantity]
                     ,[date_changed]
                     ,[disable]
                     ) values (?,?,?,?,getdate(),?) 
                     ";
                     $mevalarray = array($promoidbr,$promoid,$meproditem2,$qty,0);
                     $stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
                 
                 
                     if ( $stmt === false):
                         die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                     endif;
                     sqlsrv_free_stmt( $stmt);
                     
                 endif;
             }
            endif;
        }//end for
     endif;


    
     //end diQt_PromoDamageDetail
    
     if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
         $str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDamageBranch] where [promo_damage_id] = ? and 
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
             update [diQtech_db].[dbo].[diQt_PromoDamageBranch] set [date_changed] = getdate() 
             where [id] = ? 
             ";
             $stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
             if ( $stmt === false):
                 die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
             endif;
             sqlsrv_free_stmt( $stmt);
         else:
             //add records 
         

             $str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoDamageBranch]), 0) + 1 as me_rec_id";
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
                 insert into [diQtech_db].[dbo].[diQt_PromoDamageBranch] (
                 [id]
                 ,[promo_damage_id]
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
} //end check_promo_from_POS2

    public function spqd_view_rec($npages = 1,$npagelimit = 10,$msearchrec='') {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->db->escapeString($msearchrec);
            $str_optn = " WHERE
            (a.`spqd_trx_no` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`trx_pos_promo_spqd_hd` a
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
        SELECT * from ({$strqry}) oa order by `id` desc limit {$nstart},{$npagelimit} ";
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

    public function spqd_audit(){
        
    }
        
    public function save_promo_spqd_from_POS($spqd_trxno,$validItems,$spqd_reason,$isValid,$B_CODE_POS,$txt_spromo,$adata1,$startDate,$startTime,$endDate,$endTime){
 
		$lcon = 0;
        $str = "select isvalid from {$this->db_erp}.`trx_pos_promo_spqd_dt` where spqd_trx_no = '$spqd_trxno' and isvalid ='N' ";
        $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $nrws = $q->getNumRows();
       
		if ($this->myposdbconn):
			$str_items = '';
			for($aa = 0; $aa < count($adata1); $aa++):
				$medata = explode("x|x",$adata1[$aa]);
				$mitemc = trim($medata[0]);
				$mdesc = (trim($medata[1]));
				$qty = (trim($medata[2]));
                $spromo = (trim($medata[5]));
                $new_srp= (trim($medata[6]));
                $art_ucost= (trim($medata[12]));
            
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
            $spromoArray = explode(",", $spromo);
            $meproditemsArray = explode(",", $meproditems);
          
            foreach ($meproditemsArray as $meproditem) {
              
			$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDamage] where [code] = ? and [product_id] = ?";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array($spqd_trxno,$meproditem), array("Scrollable"=>"buffered") );
            };
			if( $stmt === false) { 
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$row_count = sqlsrv_num_rows( $stmt );
           
			if ($row_count > 0):
				$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
				$lcon = 1;
				$promoid = $row['id'];
				$str = "
				update [diQtech_db].[dbo].[diQt_PromoDamage] set 
				[name] = ?,
				[start_date] = ?,
				[end_date] = ?,
				[is_peso_discount] = ?,
				[is_percent_discount] = ?,
				[discount] = ?,
				[date_changed] = getdate() ,
                [disable] = ?,
				[product_id] = ?
				where [id] = ?
				";
              
				$mstart_date = $startDate . ' ' . $startTime;
				$mend_date = $endDate . ' ' . $endTime;
             
                foreach ($meproditemsArray as $meproditem)
                {
				$mevalarray = array($txt_spromo,$mstart_date,$mend_date,1,0,
				$new_srp,0,$meproditem,$promoid); 
                };
				$stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
				if( $stmt === false) { 
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				}
				sqlsrv_free_stmt( $stmt);
       
          
            else: 
               
                foreach ($meproditemsArray as $meproditem) {
                    
         
                    if ($isValid) {
            
                        $isValid = false;
                       $str_items = implode(',', $validItems);
                       $str_items = "'" . $str_items . "'"; 
                        
                        //group items as comma delimited as required 
                        $str = "select STRING_AGG(aa.[id], ',') AS meiditems  from [diQtech_db].[dbo].[diQt_Product] aa join ( 
                        SELECT DISTINCT CAST(value AS varchar) AS meproditemc FROM STRING_SPLIT({$str_items}, ',') WHERE value != ''
                        ) bb on (aa.[stock_no] = bb.meproditemc)";
                        $stmt = sqlsrv_query( $this->myposdbconn, $str,array(), array("Scrollable"=>"buffered") );
                        if( $stmt === false) {
                            die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        }
                        $row_count = sqlsrv_num_rows( $stmt );
                        $meproditems2 = '';
                        if($row_count > 0) {
                            $nn = 1;
                            while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
                                //echo $row['meiditems'] . '<br/>';
                                $meproditems2 = $row['meiditems'];
                            }
                        } else { 
                            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>No Record/s found [POS_ITEM]!!!</strong></div>";
                        }
                        sqlsrv_free_stmt( $stmt);
                        $meproditemsArray2 = explode(",", $meproditems2);
                        foreach ($meproditemsArray2 as $meproditem2) {
                            $str1 = "
                                UPDATE [diQtech_db].[dbo].[diQt_PromoDamage] SET 
                                [disable] = ?
                                WHERE [product_id] = ?
                            ";
                    
                            $mevalarray = array(1, $meproditem2);
                    
                            $stmt1 = sqlsrv_query($this->myposdbconn, $str1, $mevalarray, array("Scrollable" => "buffered"));
                            if ($stmt1 === false) {
                                die(print_r(sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            }
                    
                            sqlsrv_free_stmt($stmt1);
                    
                            //get the incremental id prior adding of records 
                            $str = "SELECT ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoDamage]), 0) + 1 AS me_rec_id";
                            $stmt = sqlsrv_query($this->myposdbconn, $str, array(), array("Scrollable" => "buffered"));
                            if ($stmt === false) {
                                die(print_r(sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            }
                    
                            $row_count = sqlsrv_num_rows($stmt);
                            if ($row_count > 0) {
                                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                sqlsrv_free_stmt($stmt);
                                $promoid = $row['me_rec_id'];
                    
                                $str = "
                                    INSERT INTO [diQtech_db].[dbo].[diQt_PromoDamage] ([id]
                                    ,[code]
                                    ,[name]
                                    ,[start_date]
                                    ,[end_date]
                                    ,[is_peso_discount]
                                    ,[is_percent_discount]
                                    ,[discount]
                                    ,[date_changed]
                                    ,[disable]
                                    ,[product_id]
                                    ) VALUES (?,?,?,?,?,?,?,?,getdate(),?,?)
                                ";
                    
                                $mstart_date = $startDate . ' ' . $startTime;
                                $mend_date = $endDate . ' ' . $endTime;
                                $is_fixed_price = 1;
                    
                                $mevalarray = array(
                                    $promoid,
                                    $spqd_trxno,
                                    $txt_spromo,
                                    $mstart_date,
                                    $mend_date,
                                    1,
                                    0,
                                    $new_srp,
                                    0,
                                    $meproditem2
                                );
                    
                                $stmt = sqlsrv_query($this->myposdbconn, $str, $mevalarray, array("Scrollable" => "buffered"));
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                                }
                                sqlsrv_free_stmt($stmt);
                            }
                        }
                    }
                }
               
                endif;
        
           
           
         
             $meproditemsArray = explode(",", $meproditems);
          
			  //for   diQt_PromoDamageDetail
              if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
               
                foreach ($meproditemsArray2 as $meproditem2) {
				$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDamageDetail] where [promo_damage_id] = ? and 
				[product_id] = ?";
				$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoid,$meproditem2), array("Scrollable"=>"buffered") );
				if ( $stmt === false):
					die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ );
				endif;
				$row_count = sqlsrv_num_rows( $stmt );
				//update if existing 
             
			if ($row_count > 0):
					$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
					$promoidbr = $row['id'];
					$str = "
					update [diQtech_db].[dbo].[diQt_PromoDamageDetail] set [date_changed] = getdate() 
					where [id] = ? 
					";
					$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
					if ( $stmt === false):
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					endif;
					sqlsrv_free_stmt( $stmt);
			else:
                    
            
                            $str1 = "
                            update [diQtech_db].[dbo].[diQt_PromoDamageDetail] set 
                            [disable] = ?
                            FROM [diQtech_db].[dbo].[diQt_PromoDamageBranch]
                            JOIN [diQtech_db].[dbo].[diQt_PromoDamageDetail]
                            ON [diQtech_db].[dbo].[diQt_PromoDamageBranch].promo_damage_id = [diQtech_db].[dbo].[diQt_PromoDamageDetail].promo_damage_id
                            where [diQtech_db].[dbo].[diQt_PromoDamageDetail].product_id = ? AND [diQtech_db].[dbo].[diQt_PromoDamageBranch].branch_id =?
                            ";
        
                             $mevalarray = array(1,$meproditem2,$brid);
                             $stmt1 = sqlsrv_query( $this->myposdbconn, $str1,$mevalarray, array("Scrollable"=>"buffered") );
                            if( $stmt1 === false) { 
                                die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            }
                             sqlsrv_free_stmt( $stmt1);
                        //for updating the branch
                            $str2 = "
                            update [diQtech_db].[dbo].[diQt_PromoDamageBranch] set 
                            [disable] = ?
                            FROM [diQtech_db].[dbo].[diQt_PromoDamageBranch]
                            JOIN [diQtech_db].[dbo].[diQt_PromoDamageDetail]
                            ON [diQtech_db].[dbo].[diQt_PromoDamageBranch].promo_damage_id = [diQtech_db].[dbo].[diQt_PromoDamageDetail].promo_damage_id
                            where [diQtech_db].[dbo].[diQt_PromoDamageDetail].product_id = ? AND [diQtech_db].[dbo].[diQt_PromoDamageBranch].branch_id = ?
                            ";
        
                            $mevalarray = array(1,$meproditem2,$brid);
                            $stmt2 = sqlsrv_query( $this->myposdbconn, $str2,$mevalarray, array("Scrollable"=>"buffered") );
                        if( $stmt2 === false) { 
                            die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        }
                  
                        sqlsrv_free_stmt( $stmt2);
                        $str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoDamageDetail]), 0) + 1 as me_rec_id";
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
                            insert into [diQtech_db].[dbo].[diQt_PromoDamageDetail] (
                            [id]
                            ,[promo_damage_id]
                            ,[product_id]
                            ,[quantity]
                            ,[date_changed]
                            ,[disable]
                            ) values (?,?,?,?,getdate(),?) 
                            ";
                            $mevalarray = array($promoidbr,$promoid,$meproditem2,$qty,0);
                            $stmt = sqlsrv_query( $this->myposdbconn, $str,$mevalarray, array("Scrollable"=>"buffered") );
                        
                        
                            if ( $stmt === false):
                                die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            endif;
                            sqlsrv_free_stmt( $stmt);
                            
                        endif;
                 
              
			    endif;
                }
			endif;
           
           
			//end diQt_PromoDamageDetail
           
			if ($promoid > 0 && $brid > 0):  //validate promo id and branch id
				$str = "select [id] from [diQtech_db].[dbo].[diQt_PromoDamageBranch] where [promo_damage_id] = ? and 
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
					update [diQtech_db].[dbo].[diQt_PromoDamageBranch] set [date_changed] = getdate() 
					where [id] = ? 
					";
					$stmt = sqlsrv_query( $this->myposdbconn, $str,array($promoidbr), array("Scrollable"=>"buffered") );
					if ( $stmt === false):
						die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					endif;
					sqlsrv_free_stmt( $stmt);
				else:
					//add records 
                

					$str = "select ISNULL((SELECT MAX([id]) FROM [diQtech_db].[dbo].[diQt_PromoDamageBranch]), 0) + 1 as me_rec_id";
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
						insert into [diQtech_db].[dbo].[diQt_PromoDamageBranch] (
						[id]
						,[promo_damage_id]
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

    
    public function spqd_post_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        //variable declarations
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
    
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " AND
            a.`spqd_trx_no` LIKE '%{$msearchrec}%' ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`trx_pos_promo_spqd_hd` a
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
        SELECT * from ({$strqry}) oa order by id desc limit {$nstart},{$npagelimit} ";
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
    
    public function spqd_dashboard_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->myusermod->request->getVar('mtkn_etr');
        $fromspromo = $this->request->getVar('fromspromo');
		$tospromo = $this->request->getVar('tospromo');
        
        $ifcheckvalue = $this->request->getVar('ifcheckvalue');
        $checkifactive = ($ifcheckvalue == 1)  ? "where is_disable = 'N' " : "";

        $remove_strings = ["SPROMOI", "SPROMO"];
        $fromspromo = str_replace($remove_strings, "", $fromspromo);
        $tospromo = str_replace($remove_strings, "", $tospromo);
        
        
        $str_optn = "";
        $str_optn2 = "";
        $filter = 'id';
        $filter2 = 'GROUP BY promo_code_spqd';
        if(!empty($fromspromo) && !empty($tospromo)) { 
            $filter = 'promo_code_spqd';
           
            if(empty($msearchrec)) { 
                $str_optn2 = " WHERE
                (a.`new_srp` >= '$fromspromo' AND a.`new_srp` <= '$tospromo') ";
              
            }
          else{
            $str_optn2 = " AND
            (a.`new_srp` >= '$fromspromo' AND a.`new_srp` <= '$tospromo') ";
          
          }
            
            
        }
        if(!empty($msearchrec) &&!empty($fromspromo) && !empty($tospromo)) { 
            $filter2 = '';
            $msearchrec = $this->myusermod->db->escapeString($msearchrec);
            $str_optn = " WHERE
            (a.`item_code` LIKE '%{$msearchrec}%') ";
            
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`trx_pos_promo_spqd_dt` a
        {$str_optn} {$str_optn2} 
        ";

   

             
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
        SELECT * from ({$strqry}) oa  $checkifactive $filter2  order by new_srp desc limit {$nstart},{$npagelimit} ";
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
    } // end promo_rec_view
    
    public function download_spqd_barcode($spqd_trx_no){
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
        a.`id`,a.`spqd_trx_no`
        FROM
        {$this->db_erp}.`trx_pos_promo_spqd_hd` a
        WHERE
        a.`spqd_trx_no` = '{$spqd_trx_no}'
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    
        //validated if trx is existing
        if($qry->resultID->num_rows == 0) { 
            $data = "<div class=\"alert alert-danger mb-0\"><strong>Invalid Input</strong><br>Invalid Spromo Number.</div>";
            echo $data;
            die();
        }
        else{
            $rr = $qry->getRowArray();
            $spqd_trx_no = $rr['spqd_trx_no'];
        }


            $file_name = 'spromo_discount_'.$spqd_trx_no.'_'.$cuser.'_'.date('Ymd').$this->mylibzsys->random_string(15);
            $mpathdn   = ROOTPATH;
            $_csv_path = '/public/downloads/me/';
            //if(!is_dir($_csv_path)) mkdir($_csv_path, '0755', true);
            $filepath = $mpathdn.$_csv_path.$file_name.'.csv';
            $cfilelnk = site_url() . 'downloads/me/' . $file_name.'.csv'; 
            

            //generate hd and dt data to file format
            $str = "
            SELECT *
            INTO OUTFILE '{$filepath}'
            FIELDS TERMINATED BY ',' ENCLOSED BY '\"'
            LINES TERMINATED BY '\r\n'
            FROM (
                SELECT 
                    'Branch Code' AS `Branch_Code`,
                    'PromoDamageCode' AS `Promo_damage_code`,
                    'PromoDamageName' AS `Promo_damage_name`,
                    'start_date' AS `Start_Date`,
                    'end_date' AS `End_Date`,
                    'quantity' AS `Quantity`,
                    'is_peso_discount' AS `Is_Discount_Percent`,
                    'is_percent_discount' AS `Is_Discount_Amount`,
                    'Discount' AS `Value`,
                    'Promo Item Barcode' AS `Promo_itembarcode`,
                    'Promo Item Store Barcode' AS `Promo_itemstorecode`,
                    'Product Barcode' AS `Product_Barcode`,
                    'Store code' AS `Store_code`,
                    'disable' AS `Disable`
                
                UNION ALL
                
                SELECT
                    CONCAT('E', a.`branch_code`) AS `Branch_Code`,
                    CONCAT(b.`spqd_trx_no`, '-', ROW_NUMBER() OVER (ORDER BY a.`spqd_trx_no`)) AS `Promo_damage_code`,
                    `promo_code_spqd` AS `Promo_damage_name`,
                    CONCAT(a.`start_date`, ' ', a.`start_time`) AS `Start_Date`,
                    CONCAT(a.`end_date`, ' ', a.`end_time`) AS `End_Date`,
                    b.`qty_spqd` AS `Quantity`,
                    a.`is_peso_discount` AS `Is_Discount_Percent`,
                    a.`is_percent_discount` AS `Is_Discount_Amount`,
                    b.`new_srp` AS `Value`,
                    b.`promo_barcode` AS `Promo_itembarcode`,
                    d.`ART_BARCODE1` AS `Promo_itemstorecode`,
                    b.`item_barcode` AS `Product_Barcode`,
                    d.`ART_CODE` AS `Store_code`,
                    b.`is_disable` AS `Disable`
                FROM
                    {$this->db_erp}.`trx_pos_promo_spqd_hd` a
                JOIN
                    {$this->db_erp}.`trx_pos_promo_spqd_dt` b ON a.`spqd_trx_no` = b.`spqd_trx_no`
                JOIN
                `mst_article` d ON b.`item_code` = d.`ART_CODE`
    
                WHERE
                    a.`spqd_trx_no` = '{$spqd_trx_no}'
            ) oa
        ";
        
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
        
        $chtmljs .= "
        <a href=\"{$cfilelnk}\" download=" . $file_name ." class='btn btn-dgreen btn-sm col-lg-12'> <i class='bi bi-save'></i> DOWNLOAD IT!</a>        
        ";
        echo $chtmljs;
    }//end download barcode
    
    
}  //end main class 
	