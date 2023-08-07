<?php namespace App\Controllers;

/**
 *	File        : app/Controllers/Md_subitems_bom.php
 *  Auhtor      : Kyle Alino
 *  Date Created: Jul 28, 2023
 * 	last update : Jul 28, 2023
 * 	description : Sub items Controller
 */

use CodeIgniter\Controller;
use App\Models\Mytrx_whcrossing_model;
use App\Models\Mymelibsys_model;
use App\Models\MyDatummodel;
use App\Models\MyDatauaModel;
use App\Models\MyWarehouseoutModel;
use App\Models\MyLibzDBModel;
use App\Models\MyMDSubItemsBom;

use App\Libraries\Fpdf\Mypdf;
class Md_subitems_bom extends BaseController 
{ 
	
	public function __construct()
	{ 
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibzdb = new MyLibzDBModel();
		$this->mymdsubitemsbom = new MyMDSubItemsBom();
		$this->request = \Config\Services::request();
   		$this->db = \Config\Database::connect();
	}

	public function index(){

		echo view('templates/meheader03');
		echo view('masterdata/sub_masterdata_bom/sub-md-item-bom-main');
		echo view('templates/mefooter01');

	} 

	public function get_sub_materials(){
		
		$term    = $this->request->getVar('term');
		$autoCompleteResult = array();

		$str = "
		SELECT
		a.`recid`,
		a.`ART_CODE`,
		a.`ART_UOM`

		FROM 
		`mst_article` a

		WHERE a.`ART_HIERC1` LIKE '%1001%' AND a.`ART_CODE` LIKE '%{$term}%'

		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) {
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array("value" => $row['ART_CODE'],
				"ART_UOM" => $row['ART_UOM']
				));
			endforeach;
		}

		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	} //end get_sub_materials	

	public function get_sub_itemc(){
		
		$term    = $this->request->getVar('term');
		$autoCompleteResult = array();

		$str = "
		SELECT 
		`SUB_ART_CODE`
		FROM 
		mst_cs_article
		WHERE `SUB_ART_CODE` LIKE '%{$term}%'
		GROUP BY 
		`SUB_ART_CODE`

		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) {
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array("value" => $row['SUB_ART_CODE']
				));
			endforeach;
		}

		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	} //end get_sub_itemc	

	public function sub_item_bom_save(){

		$this->mymdsubitemsbom->sub_items_bom_entry_save();

	} //end	sub_item_bom_save

	public function sub_item_bom_recs() { 
		
		$data = $this->mymdsubitemsbom->sub_items_bom_view_recs(1, 10);
        return view('masterdata/sub_masterdata_bom/sub-md-item-bom-recs',$data);
		
    }

	public function sub_item_bom_recs_vw(){

        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdsubitemsbom->sub_items_bom_view_recs($mpages, 10, $txtsearchedrec);
        return view('masterdata/sub_masterdata_bom/sub-md-item-bom-recs', $data);

    } //end sub_inv_recs_vw 

}
