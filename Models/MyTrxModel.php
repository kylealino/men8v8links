<?php
namespace App\Models;
use CodeIgniter\Model;
class MyTrxModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->request = \Config\Services::request();
	}	
	
	public function trx_jo_delv_in_sv() { 
		$adata = $this->request->getVar('adata');
		echo "hello me: " . count($adata);
	}
	
	
} //end main class
