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

class MyMDSubItemsBom extends Model
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

    public function sub_items_bom_entry_save() {

        $adata1 = $this->request->getVar('adata1');
        $sub_item = $this->request->getVar('sub_item');

        
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
        b.`ART_CODE`,
        a.`SUB_ITEM`, 
        GROUP_CONCAT(a.`SUB_ITEM_MATERIAL`) AS Sub_Materials
        FROM
        mst_sub_bom a
        join
        mst_cs_article b
        on
        a.`SUB_ITEM` = b.`SUB_ART_CODE`
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

}