<?php
namespace App\Controllers;

class MyWidgets extends BaseController
{
	public function __construct()
	{
		$this->mytrxme =  '';
	}	
	
	public function delvreg() {
		echo view('widgets/mywg-delvreg');
	} //end delvreg
	
	public function rcvdinfpout() { 
		echo view('widgets/mywg-rcvin-fpout');
	} //end rcvdinfpout
	
	public function pouttob() { 
		echo view('widgets/mywg-pout-tob');
	} //end poutob
	
	public function pouts() { 
		echo view('widgets/mywg-pouts');
	} //end pouts

}  //end main class Mytrx
