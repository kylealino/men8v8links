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

class MyMDSubItems extends Model
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

    public function sub_items_entry_save() {

        $main_itemc = $this->request->getVar('main_itemc');
        $sub_itemc = $this->request->getVar('sub_itemc');
        $sub_desc = $this->request->getVar('sub_desc');
        $barcode = $this->request->getVar('barcode');
        $convf = $this->request->getVar('convf');
        $uom = $this->request->getVar('uom');
        $cost = $this->request->getVar('cost');
        $srp = $this->request->getVar('srp');

        //validate empty fields
        if (empty($main_itemc) || empty($sub_itemc) || empty($barcode) || empty($convf) || empty($uom) || empty($srp) || empty($sub_desc) || empty($cost)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please fill up the required fields! </div>";
            die();
        }

        //limit to 13 characters
        $characterCount = strlen($barcode);
        if ($characterCount >13) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Only 13 digits of barcode is required! </div>";
            die();
        }

        //validate spaces
        $trimmed_sub_itemc = trim($sub_itemc);
        if (empty($trimmed_sub_itemc)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Spaces is not allowed! </div>";
            die();
        }

        //validate regex
        $pattern = '/^[A-Za-z0-9]+$/';
        if (!preg_match($pattern, $sub_itemc) || !preg_match($pattern, $barcode) || !preg_match($pattern, $uom))  {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Spaces or Special characters are not allowed! </div>";
            die();
        }

        //validate regex for conv and srp
        $pattern = '/^[A-Za-z0-9.]+$/';
        if (!preg_match($pattern, $convf) || !preg_match($pattern, $srp)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Spaces or Special characters are not allowed! </div>";
            die();
        }

        //check if sub item code is existing
        $str="
            SELECT `SUB_ART_CODE` FROM mst_cs_article WHERE `SUB_ART_CODE` = '$sub_itemc'
        ";

        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        if($qry->getNumRows() > 0) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> '$sub_itemc' is already existing! </div>";
            die();
        }

        //check if barcode is existing
        $str="
            SELECT `BARCODE` FROM mst_cs_article WHERE `BARCODE` = '$barcode'
        ";

        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        if($qry->getNumRows() > 0) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> '$barcode' is already existing! </div>";
            die();
        }

        //validate valid main item code
        $str = "
		SELECT
		aa.`recid`,
		aa.`ART_CODE`,
		aa.`ART_DESC`

		FROM 
		`mst_article` aa

		WHERE aa.`ART_HIERC4` LIKE '%4766%' AND aa.`ART_CODE` = '$main_itemc'

		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) {

		}else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Invalid item '$main_itemc'! </div>";
            die();
        }

        //validate uom
		$str = "
			SELECT `recid`, `ART_UOM` FROM mst_article WHERE `ART_UOM` REGEXP '^[A-Za-z]+$' AND `ART_UOM` = '$uom' GROUP BY `ART_UOM`
		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) {

		}else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Invalid UOM '$uom'! </div>";
            die();
        }
        

        //check if barcode in main article master
        // $str="
        //     SELECT `ART_BARCODE1` FROM mst_article WHERE `ART_BARCODE1` = '$barcode'
        // ";

        // $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        // if($qry->getNumRows() > 0) {
        //     echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> '$barcode' is already existing in MAIN masterdata! </div>";
        //     die();
        // }

        //check if sub item existing in main article master
        // $str="
        //     SELECT `ART_CODE` FROM mst_article WHERE `ART_CODE` = '$sub_itemc'
        // ";

        // $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        // if($qry->getNumRows() > 0) {
        //     echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> '$sub_itemc' is already existing in MAIN masterdata! </div>";
        //     die();
        // }


        $str="
        
        INSERT INTO `mst_cs_article` (
            `ART_CODE`,
            `SUB_ART_CODE`,
            `SUB_DESC`,
            `BARCODE`,
            `CONVF`,
            `UOM`,
            `COST`,
            `SRP`,
            `DATE`
        )
        VALUES
            (
            '$main_itemc',
            '$sub_itemc',
            '$sub_desc',
            '$barcode',
            '$convf',
            '$uom',
            '$cost',
            '$srp',
            now()
            );
  
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

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

    // public function sub_items_view_recs(){ 

    //     $strqry = "
    //     SELECT
    //         `ART_CODE` as main_itemc,
    //         `SUB_ART_CODE` as sub_itemc,
    //         `BARCODE` as barcode,
    //         `CONVF` as convf,
    //         `UOM` as uom,
    //         `SRP` as srp,
    //         `DATE`
    //     FROM
    //         `mst_cs_article`
    //     ORDER BY `DATE` DESC
    //     ";

    //     $qry = $this->mylibzdb->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

    //     if($qry->getNumRows() > 0) { 
    //      $data['rlist'] = $qry->getResultArray();
    //      $data['response'] = true;

    //     } else { 
    //      $data = array();
    //      $data['rlist'] = '';
    //      $data['response'] = false;

    //     }
    //     return $data;
    // } //end sub_items_view_recs

    public function sub_items_view_recs($npages = 1,$npagelimit = 10,$msearchrec='') {
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " WHERE
            (`ART_CODE` LIKE '%{$msearchrec}%' OR `SUB_ART_CODE` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
            `ART_CODE` as main_itemc,
            `SUB_ART_CODE` as sub_itemc,
            `BARCODE` as barcode,
            `CONVF` as convf,
            `UOM` as uom,
            `SRP` as srp,
            `DATE`
        FROM
        {$this->db_erp}.`mst_cs_article` 
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
    }

    public function sub_items_update() {
        $cuser = $this->myusermod->mysys_user();
        $main_itemc = $this->request->getVar('main_itemc');
        $sub_itemc = $this->request->getVar('sub_itemc');
        $sub_desc = $this->request->getVar('sub_desc');
        $barcode = $this->request->getVar('barcode');
        $convf = $this->request->getVar('convf');
        $uom = $this->request->getVar('uom');
        $cost = $this->request->getVar('cost');
        $srp = $this->request->getVar('srp');
        $recid = $this->request->getVar('recid');

        //validate empty fields
        if (empty($main_itemc) || empty($sub_itemc) || empty($barcode) || empty($convf) || empty($uom) || empty($srp) || empty($sub_desc) || empty($cost)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please fill up the required fields! </div>";
            die();
        }

        //validate maximum length
        $characterCount = strlen($barcode);
        if ($characterCount >13) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Only 13 digits of barcode is required! </div>";
            die();
        }

        //validate spaces
        $trimmed_sub_itemc = trim($sub_itemc);
        if (empty($trimmed_sub_itemc)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Spaces is not allowed! </div>";
            die();
        }

        //validate regex
        $pattern = '/^[A-Za-z0-9]+$/';
        if (!preg_match($pattern, $sub_itemc) || !preg_match($pattern, $barcode) || !preg_match($pattern, $uom))  {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Spaces or Special characters are not allowed! </div>";
            die();
        }

        //validate regex for conv and srp
        $pattern = '/^[A-Za-z0-9.]+$/';
        if (!preg_match($pattern, $convf) || !preg_match($pattern, $srp)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Spaces or Special characters are not allowed! </div>";
            die();
        }

        //check if barcode in main article master
        // $str="
        //     SELECT `ART_BARCODE1` FROM mst_article WHERE `ART_BARCODE1` = '$barcode'
        // ";

        // $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        // if($qry->getNumRows() > 0) {
        //     echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> '$barcode' is already existing in MAIN masterdata! </div>";
        //     die();
        // }

        //check if sub item existing in main article master
        // $str="
        //     SELECT `ART_CODE` FROM mst_article WHERE `ART_CODE` = '$sub_itemc'
        // ";

        // $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        // if($qry->getNumRows() > 0) {
        //     echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> '$sub_itemc' is already existing in MAIN masterdata! </div>";
        //     die();
        // }

        //validate valid main item code
        $str = "
		SELECT
		aa.`recid`,
		aa.`ART_CODE`,
		aa.`ART_DESC`

		FROM 
		`mst_article` aa

		WHERE aa.`ART_HIERC4` LIKE '%4766%' AND aa.`ART_CODE` = '$main_itemc'

		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) {

		}else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Invalid item '$main_itemc'! </div>";
            die();
        }

        //validate uom
		$str = "
			SELECT `recid`, `ART_UOM` FROM mst_article WHERE `ART_UOM` REGEXP '^[A-Za-z]+$' AND `ART_UOM` = '$uom' GROUP BY `ART_UOM`
		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) {

		}else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Invalid UOM '$uom'! </div>";
            die();
        }
        
        $str="
        
        UPDATE
        `mst_cs_article`
        SET
            `ART_CODE` = '$main_itemc',
            `SUB_ART_CODE` = '$sub_itemc',
            `SUB_DESC` = '$sub_desc',
            `BARCODE` = '$barcode',
            `CONVF` = '$convf',
            `UOM` = '$uom',
            `COST` = '$cost',
            `SRP` = '$srp'
        WHERE `recid` = '$recid'
  
        ";

        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        $str="
        INSERT INTO `mst_cs_article_logs` (

            `ART_CODE`,
            `SUB_ART_CODE`,
            `SUB_DESC`,
            `BARCODE`,
            `CONVF`,
            `UOM`,
            `COST`,
            `SRP`,
            `ACTION`,
            `CUSER`,
            `DATE`
          )
          VALUES
            (
              '$main_itemc',
              '$sub_itemc',
              '$sub_desc',
              '$barcode',
              '$convf',
              '$uom',
              '$cost',
              '$srp',
              'UPDATE',
              '$cuser',
              now()
            )
        ";

        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Updated Successfully!!! </div>
        <script type=\"text/javascript\"> 
            function __fg_refresh_data() { 
                try { 
                    jQuery('#mbtn_mn_Update').prop('disabled',true);
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

    } //end sub_items_update

}