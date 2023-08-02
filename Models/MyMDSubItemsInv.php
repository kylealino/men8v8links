<?php

/**
 *	File        : app/Models/MyMDSubItems.php
 *  Auhtor      : Kyle Alino
 *  Date Created: Jul 28, 2023
 * 	last update : Jul 28, 2023
 * 	description : Sub items Model
 */

namespace App\Models;
use CodeIgniter\Model;

class MyMDSubItemsInv extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mylibzdb = model('App\Models\MyLibzDBModel');
        $this->mylibzsys = model('App\Models\MyLibzSysModel');
        $this->mymelibzsys = model('App\Models\Mymelibsys_model');
        $this->mydataz = model('App\Models\MyDatumModel');
        $this->myusermod = model('App\Models\MyUserModel');
        $this->dbx = $this->mylibzdb->dbx;
        $this->request = \Config\Services::request();

    }


    public function sub_items_inv_view_recs($npages = 1,$npagelimit = 10,$msearchrec='') {
        $branch_name = $this->request->getvar('branch_name');

        if (empty($branch_name)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please select a branch! </div>";
            die();
        }
        
        $str="
            SELECT BRNCH_MBCODE FROM mst_companyBranch WHERE BRNCH_NAME = '$branch_name'
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $rw = $qry->getRowArray();
        $mb_code = $rw['BRNCH_MBCODE'];

        

        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " AND
            (`SO_ITEMCODE` LIKE '%{$msearchrec}%' OR `SO_BARCODE` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
            a.`SO_ITEMCODE`,
            a.`SO_BARCODE`,
            a.`SO_QTY`,
            a.`SO_NET`

        FROM 
            {$this->db_erp}.`trx_E0021_salesout` a
        WHERE 
            MONTH(a.`SO_DATE`) = MONTH(CURDATE()) AND YEAR(a.`SO_DATE`) = YEAR(CURDATE())
            AND a.`SO_BRANCH` = '$mb_code'
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
		SELECT * from ({$strqry}) oa limit 0,{$npagelimit} ";
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

    public function sub_inv_entry_save() {

        $adata1 = $this->request->getVar('adata1');
        var_dump($adata1);
        die();
        // if(count($adata1) > 0) { 
        //     $ame = array();
        //     $adatar1 = array();

        //     for($aa = 0; $aa < count($adata1); $aa++) { 
        //         $medata = explode("x|x",$adata1[$aa]);
        //         $mitemc = trim($medata[0]);

        //         array_push($adatar1,$medata);
        //     }  
           
        //     if(count($adatar1) > 0) { 
        //         for($xx = 0; $xx < count($adatar1); $xx++) { 
                    
        //             $xdata = $adatar1[$xx];
        //             $mitemc = $xdata[0];
                    
        //             $str="
        //             SELECT
        //                 `SO_ITEMCODE`,
        //                 `SO_BARCODE`,
        //                 `SO_QTY`,
        //                 `SO_COST`,
        //                 `SO_SRP`,
        //                 `SO_TAMT`,
        //                 `SO_DISC_AMT`,
        //                 `SO_GROSS`,
        //                 `SO_REFUND`,
        //                 `SO_RETURNS`,
        //                 `SO_VOIDS`,
        //                 `SO_NET`,
        //                 `SO_DATE`,
        //                 `SO_BRANCH`,
        //                 `SO_COMP_ID`,
        //                 `SO_BRANCH_ID`,
        //                 `SO_OPRICE`,
        //                 `SO_ASRP`,
        //                 `SO_SC_DISC`,
        //                 `SO_PWD_DISC`,
        //                 `SO_OTHER_DISC`,
        //                 `SO_SC_PWD_VAT_EXMP`,
        //                 `SO_PROMO_DISC`,
        //                 `SO_QTY_RETURN`,
        //                 `SO_AMOUNT_RETURN`,
        //                 `SO_TOT_QTY`,
        //                 `SO_TAG`,
        //                 `SO_ENCD`,
        //                 `SO_MUSER`
        //             FROM
        //                 `trx_E0021_salesout`
        //             WHERE 
        //                 MONTH(`SO_DATE`) = MONTH(CURDATE()) AND YEAR(`SO_DATE`) = YEAR(CURDATE())
        //                 AND
        //                 `SO_ITEMCODE` = '$mitemc'
        //             ";
        //             $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        //             $rw = $q->getRowArray();
        //             $SO_ITEMCODE = $rw['SO_ITEMCODE'];
        //             $SO_BARCODE = $rw['SO_BARCODE'];
        //             $SO_QTY = $rw['SO_QTY'];
        //             $SO_NET = $rw['SO_NET'];
        //             $SO_BRANCH_ID = $rw['SO_BRANCH_ID'];

        //             $str="
        //             INSERT INTO .`trx_E0021_cs_myivty_lb_dtl` (
        //                 `MBRANCH_ID`,
        //                 `ITEMC`,
        //                 `ITEM_BARCODE`,
        //                 `ITEM_DESC`,
        //                 `MQTY`,
        //                 `MQTY_CORRECTED`,
        //                 `MCOST`,
        //                 `MSRP`,
        //                 `SO_GROSS`,
        //                 `SO_NET`,
        //                 `MARTM_COST`,
        //                 `MARTM_PRICE`,
        //                 `MTYPE`,
        //                 `MFORM_SIGN`,
        //                 `ITEMC_HIER1`,
        //                 `MUSER`,
        //                 `MLASTDELVD`,
        //                 `MPROCDATE`
        //               )
        //               VALUES
        //                 (
        //                   '$SO_BRANCH_ID',
        //                   '$SO_ITEMCODE',
        //                   '$SO_BARCODE',
        //                   '$SO_ITEMCODE',
        //                   '$SO_QTY',
        //                   '$SO_QTY',
        //                   'SO_COST',
        //                   'MSRP',
        //                   'SO_GROSS',
        //                   'SO_NET',
        //                   'MARTM_COST',
        //                   'MARTM_PRICE',
        //                   'MTYPE',
        //                   'MFORM_SIGN',
        //                   'ITEMC_HIER1',
        //                   'MUSER',
        //                   'MLASTDELVD',
        //                   'MPROCDATE'
        //                 )
        //             ";
        //             $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        //         }
                
        //     } 
                
        // }
        echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Recorded Successfully!!! </div>
        <script type=\"text/javascript\"> 
            function __fg_refresh_data() { 
                try { 
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
    } //end sub_items_entry_save


}