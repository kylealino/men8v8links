<?php namespace App\Controllers;

/**
 *	File        : app/Controllers/MdCs_subitems_convf.php
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
use App\Models\MyMDCSConvfModel;

use App\Libraries\Fpdf\Mypdf;
class MdCs_subitems_convf extends BaseController 
{ 
	
	public function __construct()
	{ 
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibzdb = new MyLibzDBModel();
		$this->mymdsubitemsinv = new MyMDCSConvfModel();
		$this->request = \Config\Services::request();
   		$this->db = \Config\Database::connect();
	}

	public function index(){

		echo view('templates/meheader03');
		echo view('masterdata/cs/mdcs_convf/mdcs-convf-main');
		echo view('templates/mefooter01');

	} 

	public function get_branch(){ 

        $term = $this->request->getVar('term');

        $autoCompleteResult = array();

		$str = "
		SELECT 
		a.`BRNCH_NAME` __mdata
		FROM 
		mst_companyBranch a
		WHERE  
		a.`BRNCH_NAME` like '%{$term}%'
		ORDER
		by BRNCH_NAME limit 15 
		";

        
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array("value" => $row['__mdata']
				));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	} //end get_branch

	public function sub_inv_recs() { 
		
		$data = $this->mymdsubitemsinv->sub_items_inv_view_recs(1, 10);
        return view('masterdata/cs/mdcs_convf/mdcs-convf-recs',$data);
		
    }

	public function sub_inv_recs_vw(){

        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdsubitemsinv->sub_items_inv_view_recs($mpages, 10, $txtsearchedrec);
        return view('masterdata/cs/mdcs_convf/mdcs-convf-recs', $data);

    } //end sub_inv_recs_vw
	
	public function sub_inv_recs_convf() { 
		
		$data = $this->mymdsubitemsinv->sub_items_inv_view_recs_convf(1, 10);
        return view('masterdata/cs/mdcs_convf/mdcs-convf-recs-vw',$data);
		
    }

	public function sub_inv_recs_vw_convf(){

        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdsubitemsinv->sub_items_inv_view_recs_convf($mpages, 10, $txtsearchedrec);
        return view('masterdata/cs/mdcs_convf/mdcs-convf-recs-vw', $data);

    } //end sub_inv_recs_vw 

	public function sub_inv_save(){

		$this->mymdsubitemsinv->sub_inv_entry_save();

	} //end	sub_inv_save

}
