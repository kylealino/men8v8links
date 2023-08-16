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
use App\Models\MyMDCSJoyModel;

use App\Libraries\Fpdf\Mypdf;
class CT_joy extends BaseController 
{ 
	
	public function __construct()
	{ 
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibzdb = new MyLibzDBModel();
		$this->mymdjoy = new MyMDCSJoyModel();
		$this->request = \Config\Services::request();
   		$this->db = \Config\Database::connect();
	}

	public function index(){

		echo view('templates/meheader03');
		echo view('masterdata/cs/mdcs_joy/mdcs-joy-main');
		echo view('templates/mefooter01');

	} 

	public function test_joy_save(){
		$this->mymdjoy->test_joy_entry_save();
	}

	public function test_joy_recs() { 
		
		$data = $this->mymdjoy->test_joy_view_recs(1, 10);
        return view('masterdata/cs/mdcs_joy/mdcs-joy-recs',$data);
		
    }

	public function test_joy_recs_vw(){

        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdjoy->test_joy_view_recs($mpages, 10, $txtsearchedrec);
        return view('masterdata/cs/mdcs_joy/mdcs-joy-recs', $data);

    } 

	public function test_joy_update(){
		$this->mymdjoy->test_joy_update();
	}

	public function test_joy_delete(){
		$this->mymdjoy->test_joy_delete();
	}

}
