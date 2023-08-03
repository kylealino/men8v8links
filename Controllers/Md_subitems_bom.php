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
use App\Models\MyMDSubItemsInv;

use App\Libraries\Fpdf\Mypdf;
class Md_subitems_bom extends BaseController 
{ 
	
	public function __construct()
	{ 
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibzdb = new MyLibzDBModel();
		$this->mymdsubitemsinv = new MyMDSubItemsInv();
		$this->request = \Config\Services::request();
   		$this->db = \Config\Database::connect();
	}

	public function index(){

		echo view('templates/meheader03');
		echo view('masterdata/sub_masterdata_bom/sub-md-item-bom-main');
		echo view('templates/mefooter01');

	} 

}
