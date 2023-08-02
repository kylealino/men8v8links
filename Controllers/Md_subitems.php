<?php namespace App\Controllers;

/**
 *	File        : app/Controllers/Md_subitems.php
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
use App\Models\MyMDSubItems;

use App\Libraries\Fpdf\Mypdf;
class Md_subitems extends BaseController 
{ 
	
	public function __construct()
	{ 
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibzdb = new MyLibzDBModel();
		$this->mymdsubitems = new MyMDSubItems();
		$this->request = \Config\Services::request();
   		$this->db = \Config\Database::connect();
	}

	public function index(){

		echo view('templates/meheader03');
		echo view('masterdata/sub_masterdata/sub-md-item-main');
		echo view('templates/mefooter01');

	} //end	index

    // public function sub_item_recs() { 
	// 	$data = $this->mymdsubitems->sub_items_view_recs();
	// 	return view('masterdata/sub_masterdata/sub-md-item-recs',$data);
	// } //end sub_item_recs

	public function sub_item_recs() { 
		$data = $this->mymdsubitems->sub_items_view_recs(1, 10);
        return view('masterdata/sub_masterdata/sub-md-item-recs',$data);
    }

	public function sub_item_recs_vw()
    {
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdsubitems->sub_items_view_recs($mpages, 10, $txtsearchedrec);
        return view('masterdata/sub_masterdata/sub-md-item-recs', $data);
    } 

    public function sub_item_save(){

		$this->mymdsubitems->sub_items_entry_save();

	} //end	sub_item_save

	public function sub_item_update(){

		$this->mymdsubitems->sub_items_update();

	} //end	sub_item_update

	public function get_main_itemc(){
		
		$term    = $this->request->getVar('term');
		$autoCompleteResult = array();

		$str = "
		SELECT
		aa.`recid`,
		aa.`ART_CODE`,
		aa.`ART_DESC`

		FROM 
		`mst_article` aa

		WHERE aa.`ART_HIERC4` LIKE '%4766%' AND aa.`ART_CODE` LIKE '%{$term}%'

		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) {
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array("value" => $row['ART_CODE']
				));
			endforeach;
		}

		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	} //end get_main_itemc	

	public function get_uom(){
		
		$term    = $this->request->getVar('term');
		$autoCompleteResult = array();

		$str = "
			SELECT `recid`, `ART_UOM` FROM mst_article WHERE `ART_UOM` REGEXP '^[A-Za-z]+$' AND `ART_UOM` LIKE '%{$term}%' GROUP BY `ART_UOM`
		";

		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) {
			$rrec = $q->getResultArray();
			foreach($rrec as $row):

				array_push($autoCompleteResult,array("value" => $row['ART_UOM']

				));
			endforeach;
		}

		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	} //end get_uom	

}
