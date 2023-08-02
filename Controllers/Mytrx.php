<?php

namespace App\Controllers;
use App\Models\MyTrxModel;

class Mytrx extends BaseController
{
	public function __construct()
	{
		$this->mytrxme =  new MyTrxModel();
	}	
	public function jo_quota()
	{
		echo view('templates/meheader01');
		echo view('transactions/jo-quota');
		echo view('templates/mefooter01');
	} //end jo_quota
	
	public function trx_jo_delv_in()
	{
		echo view('templates/meheader01');
		echo view('transactions/jo-dr-in');
		echo view('templates/mefooter01');
	}  //end trx_jo_delv_in
	
	public function trx_jo_delv_in_sv() {
		$this->mytrxme->trx_jo_delv_in_sv();
	}

}  //end main class Mytrx
