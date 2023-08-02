<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MyPromoDiscountModel;
use App\Models\MyLibzDBModel;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzSysModel;
use App\Models\MyUserModel;
use App\Libraries\Fpdf\Mypdf;

class Sub_masterdata extends BaseController
{

    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mytrxpromo = new MyPromoDiscountModel();
        $this->mylibzdb = new MyDBNamesModel();
        $this->mylibzsys = new MyLibzSysModel();
        $this->myusermod = new MyUserModel();
        $this->request = \Config\Services::request();
        $this->data['message'] = "Sorry, You Are Not Allowed to Access This Page";
        $this->db = \config\Database::connect();
    }

    public function index()
    {
        $cuser = $this->myusermod->mysys_user();
        echo view('templates/meheader01');
        echo view('convenience/promotions/discount/trx_promo');
        echo view('templates/mefooter01');
    } 

}