<?php

/**
 *	File        : app/Models/MyMDSubItemsConvf.php
 *  Auhtor      : Kyle Alino
 *  Date Created: Jul 28, 2023
 * 	last update : Jul 28, 2023
 * 	description : Sub items Model
 */

namespace App\Models;
use CodeIgniter\Model;

class MyMDSubItemsConvf extends Model
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

    public function sub_items_view_recs($npages = 1,$npagelimit = 10,$msearchrec='') {
        
        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " WHERE
            (`ITEMC` LIKE '%{$msearchrec}%' OR `ART_CODE` LIKE '%{$msearchrec}%') ";
        }
        
        $strqry = "
        SELECT 
            a.`ITEMC`,
            a.`ITEM_DESC`,
            a.`MQTY_CORRECTED`,
            a.`MCOST`,
            a.`MARTM_PRICE`,
            b.`ART_NCONVF`
        FROM
        {$this->db_erp}.`trx_E0020_cs_myivty_lb_dtl` a
        JOIN
        {$this->db_erp}.`mst_article` b
        ON
        a.`ITEMC` = b.`ART_CODE`
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

    public function sub_items_convf_entry_save(){
        $adata1 = $this->request->getVar('adata1');

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
                    SELECT 
                        a.`ITEMC`,
                        b.`SUB_ITEM_MATERIAL`,
                        c.`ART_BARCODE1`,
                        c.`ART_DESC`,
                        (b.`UNIT` * a.`MQTY_CORRECTED`) total_unit,
                        (b.`COST` * a.`MQTY_CORRECTED`) total_cost,
                        (b.`COST_NET` * a.`MQTY_CORRECTED`) total_cost_net
                    FROM 
                        `trx_E0020_cs_myivty_lb_dtl` a
                    JOIN
                        `mst_sub_bom` b
                    ON
                        a.`ITEMC` = b.`SUB_ITEM`
                    JOIN
                        mst_article c
                    ON
                        b.`SUB_ITEM_MATERIAL` = c.`ART_CODE`
                    WHERE
                        a.`ITEMC` = '$mitemc'
                    ";

                    $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                    $rw = $q->getResultArray();
                    foreach ($rw as $data) {
                        $SUB_ITEM_MATERIAL = $data['SUB_ITEM_MATERIAL'];
                        $ART_BARCODE1 = $data['ART_BARCODE1'];
                        $ART_DESC = $data['ART_DESC'];
                        $total_unit = $data['total_unit'];
                        $total_cost = $data['total_cost'];
                        $total_cost_net = $data['total_cost_net'];
                        $convf_unit = $total_unit *-1;

                        $str="
                        INSERT INTO {$this->db_erp1}.`trx_E0020_myivty_lb_dtl` (
                            `MBRANCH_ID`,
                            `ITEMC`,
                            `ITEM_BARCODE`,
                            `ITEM_DESC`,
                            `MQTY`,
                            `MQTY_CORRECTED`,
                            `MCOST`,
                            `SO_GROSS`,
                            `SO_NET`,
                            `MARTM_COST`,
                            `MARTM_PRICE`,
                            `MTYPE`,
                            `MFORM_SIGN`,
                            `MUSER`,
                            `MLASTDELVD`,
                            `MPROCDATE`
                        )
                        VALUES
                            (
                            '115',
                            '$SUB_ITEM_MATERIAL',
                            '$ART_BARCODE1',
                            '$ART_DESC',
                            '$convf_unit',
                            '$total_unit',
                            '$total_cost',
                            '$total_cost_net',
                            '$total_cost_net',
                            '$total_cost',
                            '$total_cost_net',
                            'SALES',
                            '-',
                            'IT-KYLE',
                            now(),
                            now()
                            )
                        ";
                        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        
                    }
                }
                
            }
                
        } 

        echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Converted Successfully!!! </div>
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
    }
}