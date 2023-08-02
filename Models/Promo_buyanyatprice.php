<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
use App\Models\MyPromoBuyanyxatpricey;
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
        $this->mytrxpromo = new MyPromoBuyanyxatpricey();
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
    
    public function buy1take1_save() { 
        $this->mytrxpromo->buyanyatprice_entry_save();
    } 

    public function buy1take1_vw() { 
        $data = $this->mytrxpromo->buy1take1_rec_view(1,10);
        return view('transactions/promotions/buy1take1/trx_buy1take1_rec',$data);
    } //end record viewing

    //start record pagination
    public function buy1take1_recs() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->buy1take1_rec_view($mpages,10,$txtsearchedrec);
        return view('transactions/promotions/buy1take1/trx_buy1take1_rec',$data);
    }//end record pagination


    //show view approval record
     public function buy1take1_vw_appr() { 
        $data = $this->mytrxpromo->buy1take1_post_view(1,10);
        return view('transactions/promotions/buy1take1/trx_buy1take1_appr',$data);
    } //end view approval record

    //view approval pagination
    public function buy1take1_recs_appr() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->buy1take1_post_view($mpages,10,$txtsearchedrec);
        return view('transactions/promotions/buy1take1/trx_buy1take1_appr',$data);
    } //end approval pagination

    //for approval saving
    public function buy1take1_save_appr() { 
        $this->mytrxpromo->buy1take1_for_approval();
    }//end for approval

    //generate barcode
    public function buy1take1_dl_proc() { 
        $buy1take1_trxno = $this->request->getVar('buy1take1_trxno');
        $this->mytrxpromo->download_buy1take1_barcode($buy1take1_trxno);
    }//end generate barcode


}//end main class
