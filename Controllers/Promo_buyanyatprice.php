<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
use App\Models\MyPromoBuyanyxatpriceyModel;
use App\Models\MyLibzDBModel;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzSysModel;
use App\Models\MyUserModel;
use App\Libraries\Fpdf\Mypdf;

class Promo_buyanyatprice extends BaseController
{
    //start construct
    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mytrxpromo = new MyPromoBuyanyxatpriceyModel();
        $this->mylibzdb= new MyDBNamesModel();
        $this->mylibzsys= new MyLibzSysModel();
        $this->myusermod= new MyUserModel();
        $this->request = \Config\Services::request();
        $this->data['message'] = "Sorry, You Are Not Allowed to Access This Page";
        $this->db=\config\Database::connect();
    }//end construct
    
    //default route
    public function index()
    {
        $cuser = $this->myusermod->mysys_user();
      
        echo view('templates/meheader01');
        echo view('transactions/promotions/buyanyatprice/trx_buyanyatprice');
        echo view('templates/mefooter01');
    } //end index route
    
    public function buyanyatprice_save() { 
        $this->mytrxpromo->buyanyatprice_entry_save();
    } 

    public function buyanyatprice_vw() { 
        $data = $this->mytrxpromo->buyanyatprice_rec_view(1,10);
        return view('transactions/promotions/buyanyatprice/trx_buyanyatprice_rec',$data);
    } //end record viewing

    //start record pagination
    public function buyanyatprice_recs() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->buyanyatprice_rec_view($mpages,10,$txtsearchedrec);
        return view('transactions/promotions/buyanyatprice/trx_buyanyatprice_rec',$data);
    }//end record pagination


    //show view approval record
     public function buyanyatprice_vw_appr() { 
        $data = $this->mytrxpromo->buyanyatprice_post_view(1,10);
        return view('transactions/promotions/buyanyatprice/trx_buyanyatprice_appr',$data);
    } //end view approval record

    //view approval pagination
    public function buyanyatprice_recs_appr() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->buyanyatprice_post_view($mpages,10,$txtsearchedrec);
        return view('transactions/promotions/buyanyatprice/trx_buyanyatprice_appr',$data);
    } //end approval pagination

    //for approval saving
    public function buyanyatprice_save_appr() { 
        $this->mytrxpromo->buyanyatprice_for_approval();
    }//end for approval

    //generate barcode
    public function buyanyatprice_dl_proc() { 
        $buyanyatprice_trxno = $this->request->getVar('buyanyatprice_trxno');
        $this->mytrxpromo->download_buyanyatprice_barcode($buyanyatprice_trxno);
    }//end generate barcode


}//end main class
