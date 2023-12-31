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

class MyMDCSConvfModel extends Model
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


    public function sub_items_inv_view_recs($npages = 1,$npagelimit = 10,$msearchrec='') {
        // $branch_name = $this->request->getvar('branch_name');
        // $str="SELECT `BRNCH_NAME`,`BRNCH_OCODE2`,`BRNCH_MBCODE` FROM mst_companyBranch WHERE `BRNCH_NAME` = '$branch_name'";
        // $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        // $rw = $q->getRowArray();
        // $br_ocode2 = $rw['BRNCH_OCODE2'];
        // $tblSalesout = "{$this->db_erp}.`trx_E{$br_ocode2}_salesout`";
        
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
            SUM(a.`SO_QTY`) SO_QTY,
            SUM(a.`SO_COST`) SO_COST,
            a.`SO_BRANCH_ID`,
            SUM(a.`SO_NET`) SO_NET,
            b.`ART_DESC`
        FROM
            {$this->db_erp}.`trx_E0021CS_salesout` a
        JOIN
            {$this->db_erp}.`mst_article` b
        ON
            a.`SO_ITEMCODE` = b.`ART_CODE`
        WHERE 
            MONTH(`SO_DATE`) = MONTH(CURDATE()) AND YEAR(`SO_DATE`) = YEAR(CURDATE())
            {$str_optn}
        GROUP BY 
            a.`SO_ITEMCODE`
        ";
             
        $str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
        $nstart = $nstart < 0 ? 0 : $nstart; 
		
		
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

    public function sub_inv_entry_save() {

        $branch_name = $this->request->getvar('branch_name');
        $str="SELECT `recid`,`BRNCH_NAME`,`BRNCH_OCODE2`,`BRNCH_MBCODE` FROM mst_companyBranch WHERE `BRNCH_NAME` = '$branch_name'";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $rw = $q->getRowArray();
        $br_ocode2 = $rw['BRNCH_OCODE2'];
        $B_CODE = 'E' . $br_ocode2;
        $MB_CODE = $rw['BRNCH_MBCODE'];
        $recid = $rw['recid'];
        $tblSalesout = "{$this->db_erp}.`trx_{$B_CODE}_salesout`";

        $adata1 = $this->request->getVar('adata1');
        if (empty($adata1)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> No Sales Detected! </div>";
            die();
        }
        
        //DELETE SALES RECORD IF EXISTING AND INSERT NEW
        $str="
            SELECT * FROM {$this->db_erp}.`trx_{$MB_CODE}_cs_myivty_lb_dtl` WHERE `MTYPE` = 'SALES' AND MONTH(`MPROCDATE`) = MONTH(CURDATE()) AND YEAR(`MPROCDATE`) = YEAR(CURDATE())
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        if($qry->resultID->num_rows > 0) { 
            $str="
                DELETE FROM {$this->db_erp}.`trx_{$MB_CODE}_cs_myivty_lb_dtl` WHERE `MTYPE` = 'SALES' AND MONTH(`MPROCDATE`) = MONTH(CURDATE()) AND YEAR(`MPROCDATE`) = YEAR(CURDATE())
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		}

        //INSERT TO CURRENT RECORDS
        $str = "
            INSERT INTO {$this->db_erp}.`trx_{$MB_CODE}_cs_myivty_lb_dtl` (
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
            SELECT
                a.`SO_BRANCH_ID`,
                a.`SO_ITEMCODE`,
                a.`SO_BARCODE`,
                b.`ART_DESC`,
                -1 * SUM(a.`SO_QTY`) AS `MQTY`,
                SUM(a.`SO_QTY`) AS `MQTY_CORRECTED`,
                SUM(a.`SO_COST`) AS `MCOST`,
                SUM(a.`SO_NET`) AS `SO_GROSS`,
                SUM(a.`SO_NET`) AS `SO_NET`,
                SUM(a.`SO_COST`) AS `MARTM_COST`,
                SUM(a.`SO_NET`) AS `MARTM_PRICE`,
                'SALES' AS `MTYPE`,
                '-' AS `MFORM_SIGN`,
                'IT-KYLE' AS `MUSER`,
                NOW() AS `MLASTDELVD`,
                NOW() AS `MPROCDATE`
            FROM
                $tblSalesout a
            JOIN
                {$this->db_erp}.`mst_article` b
            ON
                a.`SO_ITEMCODE` = b.`ART_CODE`
            WHERE 
                MONTH(`SO_DATE`) = MONTH(CURDATE()) AND YEAR(`SO_DATE`) = YEAR(CURDATE())
            GROUP BY 
                a.`SO_ITEMCODE`, a.`SO_BRANCH_ID`
            ";

        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        //DELETE RECORD IF EXISTING AND INSERT NEW
        $str="
            SELECT * FROM {$this->db_erp1}.`trx_{$B_CODE}_myivty_lb_dtl` WHERE `MTYPE` = 'SALES' AND MONTH(`MPROCDATE`) = MONTH(CURDATE()) AND YEAR(`MPROCDATE`) = YEAR(CURDATE())
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        if($qry->resultID->num_rows > 0) { 
            $str="
                DELETE FROM {$this->db_erp1}.`trx_{$B_CODE}_myivty_lb_dtl` WHERE `MTYPE` = 'SALES' AND MONTH(`MPROCDATE`) = MONTH(CURDATE()) AND YEAR(`MPROCDATE`) = YEAR(CURDATE())
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		}

        //INSERT TO INVENTORY
        if(count($adata1) > 0) {
        
            $str = "
                INSERT INTO {$this->db_erp1}.`trx_{$B_CODE}_myivty_lb_dtl` (
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
                    `MPROCDATE`
                )
                SELECT
                    '$recid',
                    b.`SUB_ITEM_MATERIAL`,
                    c.`ART_BARCODE1`,
                    c.`ART_DESC`,
                    -1 * SUM((b.`UNIT` * a.`MQTY_CORRECTED`)) AS `MQTY`,
                    SUM((b.`UNIT` * a.`MQTY_CORRECTED`)) AS `MQTY_CORRECTED`,
                    SUM((b.`COST` * a.`MQTY_CORRECTED`)) AS `MCOST`,
                    SUM((b.`COST_NET` * a.`MQTY_CORRECTED`)) AS `SO_GROSS`,
                    SUM((b.`COST_NET` * a.`MQTY_CORRECTED`)) AS `SO_NET`,
                    SUM((b.`COST` * a.`MQTY_CORRECTED`)) AS `MARTM_COST`,
                    SUM((b.`COST_NET` * a.`MQTY_CORRECTED`)) AS `MARTM_PRICE`,
                    'SALES' AS `MTYPE`,
                    '-' AS `MFORM_SIGN`,
                    'IT-KYLE' AS `MUSER`,
                    NOW() AS `MPROCDATE`
                FROM
                    {$this->db_erp}.`trx_{$MB_CODE}_cs_myivty_lb_dtl` a
                JOIN
                    {$this->db_erp}.`mst_sub_bom` b
                ON
                    a.`ITEMC` = b.`SUB_ITEM`
                JOIN
                    {$this->db_erp}.mst_article c
                ON
                    b.`SUB_ITEM_MATERIAL` = c.`ART_CODE`
                GROUP BY
                    b.`SUB_ITEM_MATERIAL`
                ";

            $q = $this->mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);


        }else{
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>User Error</strong> No Sales Detected! </div>";
            die();
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

    public function sub_items_inv_view_recs_convf($npages = 1,$npagelimit = 10,$msearchrec='') {

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
            b.`ART_NCONVF`,
            b.`ART_CODE`
        FROM
        {$this->db_erp}.`trx_E0021_cs_myivty_lb_dtl` a
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


}