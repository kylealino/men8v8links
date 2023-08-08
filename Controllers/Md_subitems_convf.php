<?php namespace App\Controllers;

/**
 *	File        : app/Controllers/Md_subitems_convf.php
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
use App\Models\MyMDSubItemsConvf;

use App\Libraries\Fpdf\Mypdf;
class Md_subitems_convf extends BaseController 
{ 
	
	public function __construct()
	{ 
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibzdb = new MyLibzDBModel();
		$this->mymdsubitemsconvf = new MyMDSubItemsConvf();
		$this->request = \Config\Services::request();
   		$this->db = \Config\Database::connect();
	}

	public function index(){

        $data = $this->mymdsubitemsconvf->sub_items_view_recs(1, 10);
		echo view('templates/meheader03');
		echo view('masterdata/sub_masterdata_convf/sub-md-item-convf-main',$data);
		echo view('templates/mefooter01');

	} 

	public function sub_item_convf_vw()
    {
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdsubitemsconvf->sub_items_view_recs($mpages, 10, $txtsearchedrec);
        return view('masterdata/sub_masterdata_convf/sub-md-item-convf-main', $data);
    } 

	public function sub_item_convf_save(){

		$this->mymdsubitemsconvf->sub_items_convf_entry_save();

	} //end	sub_item_convf_save

}
