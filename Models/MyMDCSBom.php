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

class MyMDCSBom extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->db_erp1 = $this->mydbname->medb(1);
        $this->mylibzdb = model('App\Models\MyLibzDBModel');
        $this->mylibzsys = model('App\Models\MyLibzSysModel');
        $this->mymelibzsys = model('App\Models\Mymelibsys_model');
        $this->mydataz = model('App\Models\MyDatumModel');
        $this->myusermod = model('App\Models\MyUserModel');
        $this->dbx = $this->mylibzdb->dbx;
        $this->request = \Config\Services::request();

    }

    public function sub_items_bom_entry_save() {

        $adata1 = $this->request->getVar('adata1');
        $sub_item = $this->request->getVar('sub_item');

        //empty fields
        if (empty($sub_item)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please select a Sub Itemcode! </div>";
            die();
        }

        //validate valid sub item code
        $str = "
            SELECT 
                `SUB_ART_CODE`
            FROM 
                mst_cs_article
            WHERE `SUB_ART_CODE` LIKE '%{$sub_item}%'
            GROUP BY 
            `SUB_ART_CODE`
        ";

        $q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if($q->getNumRows() > 0) {

        }else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Invalid item '$sub_item'! </div>";
            die();
        }

        //validate if existing sub item
        $str="
            SELECT `SUB_ITEM` FROM mst_sub_bom WHERE `SUB_ITEM` = '$sub_item';
        ";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        if($q->resultID->num_rows == 0) {
            
        }else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Item ['$sub_item'] is already existing! </div>";
            die();
        }

        if(empty($adata1)){
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> No item is detected! </div>";
            die();
        }

        if(count($adata1) > 0) { 
            $ame = array();
            $adatar1 = array();

            for($aa = 0; $aa < count($adata1); $aa++) { 
                $medata = explode("x|x",$adata1[$aa]);
                $mitemc = trim($medata[0]);
                $amatnr = array();

                array_push($adatar1,$medata);

            }  
           
            if(count($adatar1) > 0) { 
                for($xx = 0; $xx < count($adatar1); $xx++) { 
                    
                    $xdata = $adatar1[$xx];
                    $mitemc = $xdata[0];
                    $munit = $xdata[1];
                    $muom = $xdata[2];
                    $mcost = $xdata[3];
                    $mcostnet = $xdata[4];

                    if (empty($mitemc) || empty($munit) || empty($muom) || empty($mcost) || empty($mcostnet)) {
                        echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> All fields are required! </div>";
                        die();
                    }

                    $str="
                    SELECT
                        a.`recid`,
                        a.`ART_CODE`,
                        a.`ART_UOM`,
                        a.`ART_NCONVF`
                    FROM 
                        `mst_article` a
                    WHERE 
                        a.`ART_HIERC1` = '1001' AND a.`ART_CODE` = '$mitemc'
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                    $rw = $q->getRowArray();
                    $convf = $rw['ART_NCONVF'];

                    if ($munit > $convf) {
                        echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Unit must not be greater than the actual conversion! </div>";
                        die();
                    }

                    if($q->resultID->num_rows == 0) {
                        echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Invalid Item ['$mitemc'] ! </div>";
                        die();
                    }else{

                        $str="
                        INSERT INTO `mst_sub_bom` (
                            `SUB_ITEM`,
                            `SUB_ITEM_MATERIAL`,
                            `UNIT`,
                            `UOM`,
                            `COST`,
                            `COST_NET`
                          )
                          VALUES
                            (
                              '$sub_item',
                              '$mitemc',
                              '$munit',
                              '$muom',
                              '$mcost',
                              '$mcostnet'
                            );
                        ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                    }

                }
                
            }
                
        } 

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

    public function sub_items_bom_view_recs($npages = 1,$npagelimit = 10,$msearchrec='') {
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " WHERE
            (`ART_CODE` LIKE '%{$msearchrec}%' OR `SUB_ITEM` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
        a.`SUB_ITEM`, 
        GROUP_CONCAT(a.`SUB_ITEM_MATERIAL`) AS Sub_Materials
        FROM
        mst_sub_bom a
        {$str_optn}
        GROUP BY `SUB_ITEM`
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

    public function sub_items_bom_update(){

        $adata1 = $this->request->getVar('adata1');
        $sub_item = $this->request->getVar('sub_item');

        //empty fields
        if (empty($sub_item)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Please select a Sub Itemcode! </div>";
            die();
        }

        //validate valid sub item code
        $str = "
            SELECT 
                `SUB_ART_CODE`
            FROM 
                mst_cs_article
            WHERE `SUB_ART_CODE` LIKE '%{$sub_item}%'
            GROUP BY 
            `SUB_ART_CODE`
        ";

        $q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if($q->getNumRows() > 0) {

        }else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Invalid item '$sub_item'! </div>";
            die();
        }

        //validate if there is no item
        if(empty($adata1)){
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> No item is detected! </div>";
            die();
        }


        if(count($adata1) > 0) { 
            $ame = array();
            $adatar1 = array();

            for($aa = 0; $aa < count($adata1); $aa++) { 
                $medata = explode("x|x",$adata1[$aa]);
                $mitemc = trim($medata[0]);
                $amatnr = array();

                array_push($adatar1,$medata);

            }  
           
            if(count($adatar1) > 0) { 
                for($xx = 0; $xx < count($adatar1); $xx++) { 
                    
                    $xdata = $adatar1[$xx];
                    $mitemc = $xdata[0];
                    $munit = $xdata[1];
                    $muom = $xdata[2];
                    $mcost = $xdata[3];
                    $mcostnet = $xdata[4];

                    if (empty($mitemc) || empty($munit) || empty($muom) || empty($mcost) || empty($mcostnet)) {
                        echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> All fields are required! </div>";
                        die();
                    }

                    $str="
                    SELECT
                        a.`recid`,
                        a.`ART_CODE`,
                        a.`ART_NCONVF`
                    FROM 
                        `mst_article` a
                    WHERE 
                        a.`ART_HIERC1` = '1001' AND a.`ART_CODE` = '$mitemc'
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                    $rw = $q->getRowArray();


                    if($q->resultID->num_rows == 0) {
                        echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Invalid Item ['$mitemc'] ! </div>";
                        die();
                    }else{
                        $convf = $rw['ART_NCONVF'];

                        if ($munit > $convf) {
                            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> Unit must not be greater than the actual conversion! </div>";
                            die();
                        }
                    }

                    $str="
                        SELECT `SUB_ITEM_MATERIAL` FROM mst_sub_bom WHERE `SUB_ITEM_MATERIAL` = '$mitemc'
                    ";
                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                    if($q->resultID->num_rows == 0) {
                        $str="
                        INSERT INTO `mst_sub_bom` (
                            `SUB_ITEM`,
                            `SUB_ITEM_MATERIAL`,
                            `UNIT`,
                            `UOM`,
                            `COST`,
                            `COST_NET`
                          )
                          VALUES
                            (
                              '$sub_item',
                              '$mitemc',
                              '$munit',
                              '$muom',
                              '$mcost',
                              '$mcostnet'
                            );
                        ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                    } else {
                        $str="
                        UPDATE
                            `mst_sub_bom`
                        SET
                            `SUB_ITEM` = '$sub_item',
                            `SUB_ITEM_MATERIAL` = '$mitemc',
                            `UNIT` = '$munit',
                            `UOM` = '$muom',
                            `COST` = '$mcost',
                            `COST_NET` = '$mcostnet'
                        WHERE 
                            `SUB_ITEM_MATERIAL` = '$mitemc' AND
                            `SUB_ITEM` = '$sub_item'
                      
                        ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                    }

                }
                
            } 
                
        } 

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


    }

}