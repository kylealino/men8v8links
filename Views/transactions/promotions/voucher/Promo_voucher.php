<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
use App\Models\MyPromoVoucherModel;
use App\Models\MyLibzDBModel;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzSysModel;
use App\Models\MyUserModel;
use App\Libraries\Fpdf\Mypdf;

class Promo_voucher extends BaseController
{
    //start construct
    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mytrxpromo = new MyPromoVoucherModel();
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
        echo view('transactions/promotions/voucher/trx_voucher');
        echo view('templates/mefooter01');
    } //end index route
    
    //entry save
    public function voucher_save() { 
        $this->mytrxpromo->voucher_entry_save();
    } //end test_entry_save

    //start record viewing
    public function voucher_vw() { 
        $data = $this->mytrxpromo->voucher_rec_view(1,10);
        return view('transactions/promotions/voucher/trx_voucher_rec',$data);
    } //end record viewing

    //start record pagination
    public function voucher_recs() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->voucher_rec_view($mpages,10,$txtsearchedrec);
        return view('transactions/promotions/voucher/trx_voucher_rec',$data);
    }//end record pagination


    //show view approval record
     public function voucher_vw_appr() { 
        $data = $this->mytrxpromo->voucher_post_view(1,20);
        return view('transactions/promotions/voucher/trx_voucher_appr',$data);
    } //end view approval record

    //view approval pagination
    public function voucher_recs_appr() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->voucher_post_view($mpages,20,$txtsearchedrec);
        return view('transactions/promotions/voucher/trx_voucher_appr',$data);
    } //end approval pagination
    

    //for approval saving
    public function voucher_save_appr() { 
        $this->mytrxpromo->voucher_for_approval();
    }//end for approval

    //generate barcode
    public function voucher_barcode_dl_proc() { 
        $voucher_trxno = $this->request->getVar('voucher_trxno');
        $this->mytrxpromo->download_voucher_barcode($voucher_trxno);
    }//end generate barcode



}//end main class