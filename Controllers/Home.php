<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		echo view('templates/meheader01');
		echo view('MyDashboard');
		echo view('templates/mefooter01');
		//return view('welcome_message');
	}
}
