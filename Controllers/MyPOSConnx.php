<?php namespace App\Controllers;
  
use CodeIgniter\Controller;

class MyPOSConnx extends Controller
{

    public function index()
    {
        echo view('templates/meheader01');
        echo view('mypos/reprint-logs/reprint-logs');
        echo view('templates/mefooter01');
    } //end index route

}  //end main Md_article
