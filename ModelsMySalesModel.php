<?php
namespace App\Models;
use CodeIgniter\Model;

class MySalesModel extends Model
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
    }//end construct


    //show recorded datas
    public function promo_rec_view($npages = 1,$npagelimit = 10,$msearchrec='') {
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
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_promo_hd` a
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

    public function buy1take1_rec_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " WHERE
            (a.`buy1take1_trxno` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_buy1take1_hd` a
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
    public function promo_entry_save() { 
        
        //variable declarations
      $cuser            = $this->myusermod->mysys_user();
      $mpw_tkn          = $this->myusermod->mpw_tkn();
      $mtkn_mntr = $this->request->getVar('mtkn_mntr');
      $txt_promotrxno = $this->request->getVar('txt_promotrxno');
      $branch_code = $this->request->getVar('branch_code');
      $branch_name = $this->request->getVar('branch_name');
      $start_date = $this->request->getVar('start_date');
      $start_time = $this->request->getVar('start_time');
      $end_date = $this->request->getVar('end_date');
      $end_time = $this->request->getVar('end_time');
      $discount_value = $this->request->getVar('discount_value');
      $discount_srp = $this->request->getVar('discount_srp');
      $is_fixed_price = $this->request->getVar('is_fixed_price');
      $__hmtkn_fgpacktr = '';
      $prod_barcode = $this->request->getVar('ART_BARCODE1');
      $is_disabled='FALSE';
      $is_approved='N';
      $is_bcodegen='N';
      $encd = '';
      $invalid_disc = '76';
      $is_fixed_price_checked =$this->request->getVar('is_fixed_price_checked');
      $is_discount_percent_checked =$this->request->getVar('is_discount_percent_checked');
      $is_discount_percent = $this->request->getVar('is_discount_percent');
      $adata1 = $this->request->getVar('adata1');
      $adata2 = $this->request->getVar('adata2');
      $branch_code = $this->request->getVar('branch_code');
      $mtkn_branch = $this->request->getVar('mtkn_branch');
      $txt_branch_id = '';
      $cb_fix_value = $this->request->getVar('cb_fix_value');
      $cb_fix_discount_percent_value = $this->request->getVar('cb_fix_discount_percent_value');

        //validate if branch is not selected
      if((!empty($branch_name)) && !empty($mtkn_branch)) {
        $str = "SELECT `recid` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
        $rw = $q->getRowArray();
        $txt_branch_id = $rw['recid'];
        
        $q->freeResult();
        
            //END BRANCH
        }
        else{ 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
            die();
        }

                //validate date
        if(!empty($start_date) && !empty($end_date) && !empty($end_time) && !empty($end_date)) {
        
        }
        else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Dates or Time is required!!!.</div>";
            die();
        }

        if ($cb_fix_value == 0 & $cb_fix_discount_percent_value == 0) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please select a discount type!!!</div>";
            die();
        }

                //validated if no product is inserted
        if(empty($adata1)) { 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
            die();
        }

        
        $recid = '';
        $promo_trxno = '';
        
                //UPDATE
        if(!empty($mtkn_mntr)) { 
                //CHECK IF VALID PO
            die($recid);
            $str = "select aa.`recid`,aa.`promo_trxno` from {$this->db_erp}.`gw_promo_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_mntr'";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            if($q->resultID->num_rows == 0) {
                echo "No Records Found!!!";
                die();
            }
            $rw = $q->getRowArray();
            $recid = $rw['recid'];
            $promo_trxno = $rw['promo_trxno'];
            $q->freeResult();

            }//endif

            //generate promo transaction number
            else{
                
                $promo_trxno =  $this->mydataz->get_ctr_new_dr('PD','',$this->db_erp,'');//PROMO TRANSACTION NO

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
                    $mat_mtkn = $adata2[$aa];
                    $mdesc = (trim($medata[1]));
                    $mbcode = (trim($medata[2]));
                    $mdisc = (trim($medata[3]));
                    $morigsrp = (trim($medata[4]));
                    $mdiscsrp = (trim($medata[5]));
                    $mucost = $medata[6];
                    $amatnr = array();

                    if(!empty($mitemc)) { 
                        $str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mat_mtkn' and ART_CODE = '$mitemc' ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        
                        if($q->resultID->num_rows == 0) {
                            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$cmat_code]</div>";
                            die();
                        }//end if
                        else{

                            $strr = "SELECT ('$mbcode') <> (b.`prod_barcode`) _bcode FROM {$this->db_erp}.`gw_promo_hd` a JOIN {$this->db_erp}.`gw_promo_dt` b ON a.`branch_code` = b.`branch_code` WHERE a.`branch_code` = '$branch_code' AND DATE('$start_date') <= DATE(a.`end_date`)" ;
                            $qv = $this->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            
                            if($qv->resultID->num_rows > 0) { 
                                $rww = $qv->getResultArray();
                                foreach ($rww as  $data) {
                                    $bcode = $data['_bcode'];
                                    if ($bcode == 0) {
                                        echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$mitemc]</div>";
                                        die();
                                    }  
                                }
                                
                            }

                            if ($cb_fix_discount_percent_value == 1) {
                                if ($mdisc >= $invalid_disc) {
                                    echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Discount value must not be greater than 75%</div>";
                                    die();
                                }
                            }

                            if ($cb_fix_value ==1) {
                                $total = ($morigsrp * 0.75);

                                if ($mdisc > $total) {
                                    echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Fix value must not be greater than 75% of Orig Srp</div>";
                                    die();
                                }
                                
                            }
                            
                            if(empty($mdisc)){
                                echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please enter a discount value.</div>";
                                die();
                            }
                            
                        }

                        // validation
                        $rw = $q->getRowArray(); 
                        $mmat_rid = $rw['recid'];  
                        array_push($ame,$mitemc); 
                        array_push($adatar1,$medata);
                        array_push($adatar2,$mmat_rid);

                    }//end if

                    
                }  //end for 
                
                //insert header data
                if(count($adatar1) > 0) { 
                    if(!empty($mtkn_mntr)) {       
                       $str = "
                       update {$this->db_erp}.`gw_promo_hd` set 
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
                    insert into {$this->db_erp}.`gw_promo_hd` (
                    `promo_trxno`,
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
                    `is_discount_percent`

                    ) values(
                    '$promo_trxno',
                    '$branch_code',
                    '$start_date',
                    '$start_time',
                    '$end_date',
                    '$end_time',
                    '$cb_fix_value',
                    '$cuser',
                    now(),
                    '$is_approved',
                    '$is_bcodegen',
                    '$cb_fix_discount_percent_value'
                    )
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

                          //GET ID
                    $str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_fgpacktr from {$this->db_erp}.`gw_promo_hd` aa where `promo_trxno` = '$promo_trxno' ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                    $rr = $q->getRowArray();
                    $q->freeResult();
                    
                    }//endesle
                    
                    //insert details data
                    for($xx = 0; $xx < count($adatar1); $xx++) { 
                        
                        $xdata = $adatar1[$xx];
                        $mitemc = $xdata[0];
                        $mat_rid = $adatar2[$xx];
                        $mdesc = $xdata[1];
                        $mbcode = $xdata[2];
                        $mdisc = $xdata[3];
                        $morigsrp = $xdata[4];
                        $mdiscsrp = $xdata[5];
                        $mucost = $xdata[6];
                        
                        if(empty($mtkn_mntr)) {  
                            $str = "
                            insert into {$this->db_erp}.`gw_promo_dt` ( 
                            `promo_trxno`,
                            `discount_value`,
                            `discount_srp`,
                            `prod_barcode`,
                            `is_disabled`,
                            `muser`,
                            `encd`,
                            `mat_rid`, 
                            `promohd_rid`,
                            `branch_code` 
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
                            '$branch_code'
                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        } 
                        
                    }  //end for 
                    
                    //show success modal
                    if(empty($mtkn_mntr)) { 
                        echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Promo Discount No:{$promo_trxno} </div>
                        <script type=\"text/javascript\"> 
                        function __fg_refresh_data() { 
                            try { 
                                $('#txt_promotrxno').val('{$promo_trxno}');
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
                                echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
                                ";
                                die();
                            }
                        } else { 
                            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Material Data!!!.</div>";
                            die();
            } //end if 
        } else { 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
            die();
        }
        
	} //end promo_entry_save
 
    //start post view
    public function promo_post_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        //variable declarations
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " AND
            a.`promo_trxno` LIKE '%{$msearchrec}%' ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_promo_hd` a
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
    public function promo_for_approval() {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_recid = $this->request->getVar('mtkn_recid');
        $promo_trxno = '';
        
        if(!empty($mtkn_recid)) { 
            //SELECT IF ALREADY POSTED
            $str = "select is_approved,promo_trxno from {$this->db_erp}.`gw_promo_hd` aa WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_recid' AND `is_approved` = 'N'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($qry->resultID->num_rows == 0) { 
                echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
                die();
            }
            else{
                $rr = $qry->getRowArray();
                $promo_trxno = $rr['promo_trxno'];
            }
            $str = "
            update {$this->db_erp}.`gw_promo_hd`
            SET `is_approved` = 'Y',
            `date_approved` = now()
            WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_recid'
            AND `is_approved` = 'N';
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            echo  "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Approved Successfully!!!</div>
            
            ";

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
            $data = "<div class=\"alert alert-danger\"><strong>Invalid Input</strong><br>Invalid Promo Number.</div>";
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
    } // end promo_rec_view

    public function buy1take1_entry_save() { 
        
        //variable declarations
      $cuser            = $this->myusermod->mysys_user();
      $mpw_tkn          = $this->myusermod->mpw_tkn();
      $mtkn_mntr = $this->request->getVar('mtkn_mntr');
      $txt_buy1take1trxno = $this->request->getVar('txt_buy1take1trxno');
      $branch_code = $this->request->getVar('branch_code');
      $branch_name = $this->request->getVar('branch_name');
      $start_date = $this->request->getVar('start_date');
      $start_time = $this->request->getVar('start_time');
      $end_date = $this->request->getVar('end_date');
      $end_time = $this->request->getVar('end_time');
      $discount_value = $this->request->getVar('discount_value');
      $discount_srp = $this->request->getVar('discount_srp');
      $is_fixed_price = $this->request->getVar('is_fixed_price');
      $__hmtkn_fgpacktr = '';
      $prod_barcode = $this->request->getVar('ART_BARCODE1');
      $is_disabled='FALSE';
      $is_approved='N';
      $is_bcodegen='N';
      $encd = '';
      $invalid_disc = '76';
      $is_fixed_price_checked =$this->request->getVar('is_fixed_price_checked');
      $is_discount_percent_checked =$this->request->getVar('is_discount_percent_checked');
      $is_discount_percent = $this->request->getVar('is_discount_percent');
      $adata1 = $this->request->getVar('adata1');
      $adata2 = $this->request->getVar('adata2');
      $branch_code = $this->request->getVar('branch_code');
      $mtkn_branch = $this->request->getVar('mtkn_branch');
      $txt_branch_id = '';
      $cb_fix_value = $this->request->getVar('cb_fix_value');
      $cb_fix_discount_percent_value = $this->request->getVar('cb_fix_discount_percent_value');

        //validate if branch is not selected
      if((!empty($branch_name)) && !empty($mtkn_branch)) {
        $str = "SELECT `recid` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
        $rw = $q->getRowArray();
        $txt_branch_id = $rw['recid'];
        
        $q->freeResult();
        
            //END BRANCH
        }
        else{ 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
            die();
        }

                //validate date
        if(!empty($start_date) && !empty($end_date) && !empty($end_time) && !empty($end_date)) {
        
        }
        else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Dates or Time is required!!!.</div>";
            die();
        }

        if ($cb_fix_value == 0 & $cb_fix_discount_percent_value == 0) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please select a discount type!!!</div>";
            die();
        }

                //validated if no product is inserted
        if(empty($adata1)) { 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
            die();
        }

        
        $recid = '';
        $promo_trxno = '';
        
                //UPDATE
        if(!empty($mtkn_mntr)) { 
                //CHECK IF VALID PO
            die($recid);
            $str = "select aa.`recid`,aa.`promo_trxno` from {$this->db_erp}.`gw_promo_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_mntr'";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            if($q->resultID->num_rows == 0) {
                echo "No Records Found!!!";
                die();
            }
            $rw = $q->getRowArray();
            $recid = $rw['recid'];
            $promo_trxno = $rw['promo_trxno'];
            $q->freeResult();

            }//endif

            //generate promo transaction number
            else{
                
                $buy1take1_trxno =  $this->mydataz->get_ctr_buy1take1($this->db_erp,'');

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
                    $mat_mtkn = $adata2[$aa];
                    $mdesc = $medata[1];
                    $mbcode = $medata[2];
                    $mitemc2 = $medata[3];
                    $mdesc2 = $medata[4];
                    $mbcode2 = $medata[5];
                    $qty = $medata[6];
                    $take = $medata[7];
                    $amatnr = array();

                    if(!empty($mitemc) && !empty($mitemc2)) { 
                        $str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mat_mtkn' and ART_CODE = '$mitemc' ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        
                        if($q->resultID->num_rows == 0) {
                            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$cmat_code]</div>";
                            die();
                        }//end if
                        else{

                            $strr = "SELECT ('$mbcode') <> (b.`prod_barcode_buy`) _bcode FROM {$this->db_erp}.`gw_buy1take1_hd` a JOIN {$this->db_erp}.`gw_buy1take1_dt` b ON a.`buy1take1_trxno` = b.`buy1take1_trxno` WHERE a.`branch_code` = '$branch_code' AND DATE('$start_date') <= DATE(a.`end_date`)" ;
                            $qv = $this->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            
                            if($qv->resultID->num_rows > 0) { 
                                $rww = $qv->getResultArray();
                                foreach ($rww as  $data) {
                                    $bcode = $data['_bcode'];
                                    if ($bcode == 0) {
                                        echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$mitemc]</div>";
                                        die();
                                    }  
                                }
                                
                            }

                            $strr = "SELECT ('$mbcode2') <> (b.`prod_barcode_take`) _bcode FROM {$this->db_erp}.`gw_buy1take1_hd` a JOIN {$this->db_erp}.`gw_buy1take1_dt` b ON a.`buy1take1_trxno` = b.`buy1take1_trxno` WHERE a.`branch_code` = '$branch_code' AND DATE('$start_date') <= DATE(a.`end_date`)" ;
                            $qv = $this->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            
                            if($qv->resultID->num_rows > 0) { 
                                $rww = $qv->getResultArray();
                                foreach ($rww as  $data) {
                                    $bcode = $data['_bcode'];
                                    if ($bcode == 0) {
                                        echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$mitemc2]</div>";
                                        die();
                                    }  
                                }
                                
                            }
                            
                        }

                        // validation
                        $rw = $q->getRowArray(); 
                        $mmat_rid = $rw['recid'];  
                        array_push($ame,$mitemc); 
                        array_push($adatar1,$medata);
                        array_push($adatar2,$mmat_rid);

                    }//end if

                    
                }  //end for 
                
                //insert header data
                if(count($adatar1) > 0) { 
                    if(!empty($mtkn_mntr)) {       
                       $str = "
                       update {$this->db_erp}.`gw_buy1take1_hd` set 
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
                    insert into {$this->db_erp}.`gw_buy1take1_hd` (
                    `buy1take1_trxno`,
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
                    `is_discount_percent`

                    ) values(
                    '$buy1take1_trxno',
                    '$branch_code',
                    '$start_date',
                    '$start_time',
                    '$end_date',
                    '$end_time',
                    '$cb_fix_value',
                    '$cuser',
                    now(),
                    '$is_approved',
                    '$is_bcodegen',
                    '$cb_fix_discount_percent_value'
                    )
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

                          //GET ID
                    $str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_fgpacktr from {$this->db_erp}.`gw_buy1take1_hd` aa where `buy1take1_trxno` = '$buy1take1_trxno' ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
                    $rr = $q->getRowArray();
                    $q->freeResult();
                    
                    }//endesle
                    
                    //insert details data
                    for($xx = 0; $xx < count($adatar1); $xx++) { 
                        
                        $xdata = $adatar1[$xx];
                        $mitemc = $xdata[0];
                        $mat_rid = $adatar2[$xx];
                        $mdesc = $xdata[1];
                        $mbcode = $xdata[2];
                        $mitemc2 = $xdata[3];
                        $mdesc2 = $xdata[4];
                        $mbcode2 = $xdata[5];
                        $qty = $xdata[6];
                        $take = $xdata[7];
                        
                        if(empty($mtkn_mntr)) {  
                            $str = "
                            insert into {$this->db_erp}.`gw_buy1take1_dt` ( 
                            `buy1take1_trxno`,
                            `prod_barcode_buy`,
                            `prod_barcode_take`,
                            `qty`,
                            `take`,
                            `encd` 
                            ) values(
                            '$buy1take1_trxno',
                            '$mbcode',
                            '$mbcode2',
                            '$qty',
                            '$take',
                            now()
                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        } 
                        
                    }  //end for 
                    
                    //show success modal
                    if(empty($mtkn_mntr)) { 
                        echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Buy One Take One No:{$buy1take1_trxno} </div>
                        <script type=\"text/javascript\"> 
                        function __fg_refresh_data() { 
                            try { 
                                $('#txt_buy1take1trxno').val('{$buy1take1_trxno}');
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
                                echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
                                ";
                                die();
                            }
                        } else { 
                            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Material Data!!!.</div>";
                            die();
            } //end if 
        } else { 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
            die();
        }
        
	} //end promo_entry_save

    public function buy1take1_post_view($npages = 1,$npagelimit = 10,$msearchrec='') {
        //variable declarations
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_etr = $this->request->getVar('mtkn_etr');
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " AND
            a.`buy1take1_trxno` LIKE '%{$msearchrec}%' ";
        }
        
        $strqry = "
        SELECT 
        a.*
        FROM
        {$this->db_erp}.`gw_buy1take1_hd` a
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

    public function buy1take1_for_approval() {
        $cuser = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $mtkn_recid = $this->request->getVar('mtkn_recid');
        $buy1take1_trxno = '';
        
        if(!empty($mtkn_recid)) { 
            //SELECT IF ALREADY POSTED
            $str = "select is_approved,buy1take1_trxno from {$this->db_erp}.`gw_buy1take1_hd` aa WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_recid' AND `is_approved` = 'N'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($qry->resultID->num_rows == 0) { 
                echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
                die();
            }
            else{
                $rr = $qry->getRowArray();
                $buy1take1_trxno = $rr['buy1take1_trxno'];
            }
            $str = "
            update {$this->db_erp}.`gw_buy1take1_hd`
            SET `is_approved` = 'Y',
            `date_approved` = now()
            WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_recid'
            AND `is_approved` = 'N';
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            echo  "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Approved Successfully!!!</div>
            
            ";

        }//endif

    }//end  fgpack update approval

    public function download_buy1take1_barcode($buy1take1_trxno){
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
        a.`recid`,a.`buy1take1_trxno`
        FROM
        {$this->db_erp}.`gw_buy1take1_hd` a
        WHERE
        a.`buy1take1_trxno` = '{$buy1take1_trxno}'
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        //validated if trx is existing
        if($qry->resultID->num_rows == 0) { 
            $data = "<div class=\"alert alert-danger\"><strong>Invalid Input</strong><br>Invalid Buy One Take One Number.</div>";
            echo $data;
            die();
        }
        else{
            $rr = $qry->getRowArray();
            $buy1take1_trxno = $rr['buy1take1_trxno'];
        }


            $file_name = 'buy1take1_discount_'.$buy1take1_trxno.'_'.$cuser.'_'.date('Ymd').$this->mylibzsys->random_string(15);
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
                'PromoBuyXTakeYCode',
                'PromoBuyXTakeY_Name',
                'start_date',
                'end_date',
                'quantity',
                'take',
                'is_discount_percent',
                'is_discount_amount',
                'is_fixed_price',
                'value',
                'Product_Barcode_Buy',
                'Product_Barcode_Take',
                'disable'

                UNION ALL
                
                SELECT
                CONCAT('E',a.`branch_code`),a.`buy1take1_trxno`,'BUY X TAKE Y', CONCAT(a.`start_date`,' ', a.`start_time`),CONCAT(a.`end_date`,' ', a.`end_time`),b.`qty`, b.`take`, a.`is_discount_percent`,a.`is_discount_amount`,
                 a.`is_fixed_price`, b.`discount_value`, b.`prod_barcode_buy`, b.`prod_barcode_take`, b.`is_disabled`
                FROM
                {$this->db_erp}.`gw_buy1take1_hd` a
                JOIN 
                {$this->db_erp}.`gw_buy1take1_dt` b
                ON 
                a.`buy1take1_trxno` = b.`buy1take1_trxno`
                WHERE
                a.`buy1take1_trxno` = '{$buy1take1_trxno}'
                
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
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
            die();
        }

                //validated if no product is inserted
        if(empty($adata1)) { 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
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
                            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$cmat_code]</div>";
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
                       update {$this->db_erp}.`gw_buy1take1_hd` set 
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
                        echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Buy One Take One No:{$depctrlno_trx} </div>
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
                                echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
                                ";
                                die();
                            }
                        } else { 
                            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Material Data!!!.</div>";
                            die();
            } //end if 
        } else { 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
            die();
        }
        
	} 

    //start voucher entry save
    public function voucher_entry_save() { 
        
        //variable declarations
      $cuser = $this->myusermod->mysys_user();
      $mpw_tkn = $this->myusermod->mpw_tkn();
      $mtkn_mntr = $this->request->getVar('mtkn_mntr');
      $voucher_trxno = $this->request->getVar('voucher_trxno');
      $branch_code = $this->request->getVar('branch_code');
      $branch_name = $this->request->getVar('branch_name');
      $voucher_name = $this->request->getVar('voucher_name');
      $start_date = $this->request->getVar('start_date');
      $start_time = $this->request->getVar('start_time');
      $end_date = $this->request->getVar('end_date');
      $end_time = $this->request->getVar('end_time');
      $__hmtkn_fgpacktr = '';
      $encd = '';
      $invalid_disc = '76';
      $adata1 = $this->request->getVar('adata1');
      $adata2 = $this->request->getVar('adata2');
      $mtkn_branch = $this->request->getVar('mtkn_branch');
      $txt_branch_id = '';
      $amount='';
      $is_disabled='';
      $is_approved='N';
  
       

        //validate if branch is not selected
      if((!empty($branch_code)) && !empty($mtkn_branch)) {
        $str = "SELECT `recid` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
        $rw = $q->getRowArray();
        $txt_branch_id = $rw['recid'];
        
        $q->freeResult();
        
            //END BRANCH
        }
        else{ 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
            die();
        }

                //validate date
        if(!empty($start_date) && !empty($end_date) && !empty($end_time) && !empty($end_date)) {
        
        }
        else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Dates or Time is required!!!.</div>";
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

            //generate voucher transaction number
            else{
                
                $voucher_trxno =  $this->mydataz->get_ctr_new_dr('PD','',$this->db_erp,'');//PROMO TRANSACTION NO

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

                    $discount_value = (trim($medata[0]));
                    $voucher_code = (trim($medata[1]));
                    
                    $amatnr = array();


                            if(empty($voucher_code)){
                                echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please enter a voucher code.</div>";
                                die();
                            }
                            
                        }

                        // validation
                        $rw = $q->getRowArray(); 
                        $mmat_rid = $rw['recid'];  
                        array_push($adatar1,$medata);
                        array_push($adatar2,$mmat_rid);

                    }//end if

                    

                
                //insert header data
                if(count($adatar1) > 0) { 
                    if(!empty($mtkn_mntr)) {       
                       $str = "
                       update {$this->db_erp}.`gw_voucher_hd` set 
                       `branch_code` = '$branch_code',
                       `voucher_name` = '$voucher_name',
                       `start_date` = '$start_date',
                       `start_time` = '$start_time',
                       `end_date` = '$end_date',
                       `end_time`= '$end_time',
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
                    `start_date`,
                    `start_time`,
                    `end_date`,
                    `end_time`,
                    `is_approved`,
                    `encd_date`

                    ) values(
                    '$voucher_trxno',
                    '$branch_code',
                    '$branch_name',
                    '$voucher_name',
                    '$start_date',
                    '$start_time',
                    '$end_date',
                    '$end_time',
                    '$is_approved',
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
                        $discount_value = $xdata[0];
                        $voucher_code = $xdata[1];

                        if(empty($mtkn_mntr)) {  
                            $str = "
                            insert into {$this->db_erp}.`gw_voucher_dt` ( 
                            `voucher_trxno`,
                            `amount`,
                            `discount_value`,
                            `voucher_code`,
                            `is_disabled`
                        
                            ) values(
                            '$voucher_trxno',
                            '$amount',
                            '$discount_value',
                            '$voucher_code',
                            '$is_disabled'

                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        } 
                        
                    }  //end for 
                    
                    //show success modal
                    if(empty($mtkn_mntr)) { 
                        echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Voucher:{$voucher_trxno} </div>
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
                                echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
                                ";
                                die();
                            }
                         
            } //end if 
       else { 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
            die();
        }
        
    } //end voucher_entry_save

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
            $str = "select is_approved,voucher_trxno from {$this->db_erp}.`gw_voucher_hd` aa WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_fgpacktr' AND `is_approved` = 'N'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($qry->resultID->num_rows == 0) { 
                echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
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
            
            echo  "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Approved Successfully!!!</div>
            
            ";

        }//endif

    }//end  fgpack update approval

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
      $discount = $this->request->getVar('discount');
      $__hmtkn_fgpacktr = '';
      $prod_barcode = $this->request->getVar('ART_BARCODE1');
      $is_disable='FALSE';
      $is_approved='N';
      $is_bcodegen='N';
      $encd = '';
      $invalid_disc = '76';
      $adata1 = $this->request->getVar('adata1');
      $adata2 = $this->request->getVar('adata2');
      $mtkn_branch = $this->request->getVar('mtkn_branch');
      $txt_branch_id = '';
      $amount = $this->request->getVar('amount');
    

      

      //validate if branch is not selected
      if((!empty($branch_code)) && !empty($mtkn_branch)) {
        $str = "SELECT `recid` FROM {$this->db_erp}.`mst_companyBranch` WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '{$mtkn_branch}' ";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
        $rw = $q->getRowArray();
        $txt_branch_id = $rw['recid'];
        
        $q->freeResult();
        
            //END BRANCH
        }
        else{ 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please Select Branch!!!.</div>";
            die();
        }



                //validate date
        if(!empty($start_date) && !empty($end_date) && !empty($start_time) && !empty($end_date)) {
        
        }
        else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Dates or Time is required!!!.</div>";
            die();
        }


     
        
                //validated if no product is inserted
        if(empty($adata1)) { 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
            die();
        }

        
        $recid = '';
        $threshold_trxno = '';
        
                //UPDATE
        if(!empty($mtkn_mntr)) { 
                //CHECK IF VALID PO
            die($recid);
            $str = "select aa.`recid`,aa.`promo_trxno` from {$this->db_erp}.`gw_threshold_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_mntr'";
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
                
                $threshold_trxno =  $this->mydataz->get_ctr_new_dr('PTC','',$this->db_erp,'');//PROMO TRANSACTION NO

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
                    $ART_UPRICE = $medata[2];
                    $mdisc = (trim($medata[3]));
                    $ART_BARCODE1 = (trim($medata[4]));
                    $amatnr = array();

                    if(!empty($ART_CODE)) { 
                        $str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where  ART_CODE = '$ART_CODE' ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        
                        if($q->resultID->num_rows == 0) {
                            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$ART_CODE]</div>";
                            die();
                        }//end if

                        else{

                            $strr = "SELECT ('$ART_BARCODE1') <> (b.`prod_barcode`) _bcode FROM {$this->db_erp}.`gw_threshold_hd` a JOIN {$this->db_erp}.`gw_threshold_dt` b ON a.`threshold_trxno` = b.`threshold_trxno` WHERE a.`branch_code` = '$branch_code' AND DATE('$start_date') <= DATE(a.`end_date`)" ;
                            $qv = $this->mylibzdb->myoa_sql_exec($strr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                            
                            if($qv->resultID->num_rows > 0) { 
                                $rww = $qv->getResultArray();
                                foreach ($rww as  $data) {
                                    $bcode = $data['_bcode'];
                                    if ($bcode == 0) {
                                        echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Item is already existing. <br/> [$ART_CODE]</div>";
                                        die();
                                    }  
                                }
                                
                            }
                        
                        }

                        // validation
                        $rw = $q->getRowArray(); 
                        $mmat_rid = $rw['recid'];  
                        array_push($ame,$ART_CODE); 
                        array_push($adatar1,$medata);
                        array_push($adatar2,$mmat_rid);

                    }//end if

                    
                }  //end for 
                
                //insert header data
                if(count($adatar1) > 0) { 
                    if(!empty($mtkn_mntr)) {       
                       $str = "
                       update {$this->db_erp}.`gw_threshold_hd` set 
                       `threshold_trxno` = '$promo_trxno',
                       `branch_code` = '$branch_code',
                       `start_date` = '$start_date',
                       `start_time` = '$start_time',
                       `end_date` = '$end_date',
                       `end_time`= '$end_time',
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
                    `encd_date`,
                    `is_approved`,
                    `is_bcodegen`

                    ) values(
                    '$threshold_trxno',
                    '$branch_code',
                    '$branch_name',
                    '$start_date',
                    '$start_time',
                    '$end_date',
                    '$end_time',
                    '$cuser',
                    now(),
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
                        $mat_rid = $adatar2[$xx];
                        $ART_DESC = $xdata[1];
                        $ART_UPRICE = $xdata[2];
                        $mdisc = $xdata[3];
                        $ART_BARCODE1 = $xdata[4];
                        
                        
                        if(empty($mtkn_mntr)) {  
                            $str = "
                            insert into {$this->db_erp}.`gw_threshold_dt` ( 
                            `threshold_trxno`,
                            `amount`,
                            `discount`,
                            `prod_barcode`,
                            `is_disable`
                            
                            ) values(
                            '$threshold_trxno',
                            '$ART_UPRICE',
                            '$mdisc',
                            '$ART_BARCODE1',
                            '$is_disable'

                            )
                            ";
                            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__); 
                        } 
                        
                    }  //end for 
                    
                    //show success modal
                    if(empty($mtkn_mntr)) { 
                        echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong>Data Recorded Successfully!!! Promo Threshold Code:{$threshold_trxno} </div>
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
                                echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
                                ";
                                die();
                            }
                        } else { 
                            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Material Data!!!.</div>";
                            die();
            } //end if 
        } else { 
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
            die();
        }
        
    } //end threshold_entry_save

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
                echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already approved!!!.</div>";
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
            
            echo  "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Approved Successfully!!!</div>
            
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
            $data = "<div class=\"alert alert-danger\"><strong>Invalid Input</strong><br>Invalid Input.</div>";
            echo $data;
            die();
        }
        else{
            $rr = $qry->getRowArray();
            $threshold_trxno = $rr['threshold_trxno'];
        }

        //setup file
        if($threshold_trxno != ''){
            $file_name = 'Webtask'. $threshold_trxno . '_' . $cuser . '_'.date('Ymd').$this->mylibzsys->random_string(15);
            $mpathdn   = ROOTPATH;
            $_csv_path = '/public/downloads/me/';
            //if(!is_dir($_csv_path)) mkdir($_csv_path, '0755', true);
            $filepath = $mpathdn.$_csv_path.$file_name.'.csv';
            $cfilelnk = site_url() . 'downloads/me/' . $file_name.'.csv'; 
            
            $strr ="
            Select 
            a.`threshold_trxno`,
            b.`discount`,
            b.`amount`

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
            FIELDS TERMINATED BY '\t'
            LINES TERMINATED BY '\r\n'
            FROM(
                SELECT 
                'Branch_code',
                'PromoThresholdCode'
                'PromoThreshold_Name',
                'start_date',
                'start_time',
                'end_date',
                'end_time',
                'Amount',
                'discount',
                'Product Barcode',
                'disable'

                UNION ALL
                
                SELECT
                CONCAT('E',a.`branch_code`),a.`threshold_trxno`,CONCAT('WORTH','$amount','DISC','$discount'), CONCAT (a.`start_date`,' ', ,a.`start_time`),CONCAT (a.`end_date`,' ',a.`end_time`),
                b.`amount`,b.`discount`, b.`prod_barcode`, b.`is_disable`
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


} //end main MyMDCustomerModel
