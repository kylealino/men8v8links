<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
use App\Models\MyPromoThresholdModel;
use App\Models\MyLibzDBModel;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzSysModel;
use App\Models\MyUserModel;
use App\Libraries\Fpdf\Mypdf;

class Promo_threshold extends BaseController
{
    //start construct
    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mytrxpromo = new MyPromoThresholdModel();
        $this->mylibzdb= new MyDBNamesModel();
        $this->mylibzsys= new MyLibzSysModel();
        $this->myusermod= new MyUserModel();
        $this->request = \Config\Services::request();
        $this->db=\config\Database::connect();
    }//end construct
    
    //default route
    public function index()
    {

        $cuser = $this->myusermod->mysys_user();
        echo view('templates/meheader01');
        echo view('transactions/promotions/threshold/trx_threshold');
        echo view('templates/mefooter01');
        

    } //end index route
    
    //entry save
    public function threshold_save() { 
        $this->mytrxpromo->threshold_entry_save();
    } //end test_entry_save

    //start record viewing
    public function threshold_vw() { 
        $data = $this->mytrxpromo->threshold_rec_view(1,10);
        return view('transactions/promotions/threshold/trx_threshold_rec',$data);
    } //end record viewing

    //start record pagination
    public function threshold_recs() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->threshold_rec_view($mpages,10,$txtsearchedrec);
        return view('transactions/promotions/threshold/trx_threshold_rec',$data);
    }//end record pagination


    //show view approval record
     public function threshold_vw_appr() { 
        
        $data = $this->mytrxpromo->threshold_post_view(1,20);
        return view('transactions/promotions/threshold/trx_threshold_appr',$data);
    } //end view approval record

    //view approval pagination
    public function threshold_recs_appr() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->threshold_post_view($mpages,20,$txtsearchedrec);
        return view('transactions/promotions/threshold/trx_threshold_appr',$data);
    } //end approval pagination
    

    //for approval saving
    public function threshold_save_appr() { 
        $this->mytrxpromo->threshold_for_approval();
    }//end for approval

    //generate barcode
    public function threshold_barcode_dl_proc() { 
        $threshold_trxno = $this->request->getVar('threshold_trxno');
        $this->mytrxpromo->download_threshold_barcode($threshold_trxno);
    }//end generate barcode


}//end main class