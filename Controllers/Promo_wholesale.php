<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
use App\Models\MyPromoWholesaleModel;
use App\Models\MyLibzDBModel;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzSysModel;
use App\Models\MyUserModel;
use App\Libraries\Fpdf\Mypdf;

class Promo_wholesale extends BaseController
{
    //start construct
    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mytrxpromo = new MyPromoWholesaleModel();
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
        $result=$this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuatrx_id='241'","myua_trx");
        if($result==0):
            echo view('errors/html/error_404',$this->data);
            die();
        endif;
        echo view('templates/meheader01');
        echo view('transactions/promotions/wholesale/trx_wholesale');
        echo view('templates/mefooter01');
    } //end index route
    
    //entry save
    public function wholesale_save() { 
        $this->mytrxpromo->wholesale_entry_save();
    } //end test_entry_save

    //start record viewing
    public function wholesale_vw() { 
        $data = $this->mytrxpromo->wholesale_rec_view(1,10);
        return view('transactions/promotions/wholesale/trx_wholesale_rec',$data);
    } //end record viewing

    //start record pagination
    public function wholesale_recs() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->wholesale_rec_view($mpages,10,$txtsearchedrec);
        return view('transactions/promotions/wholesale/trx_wholesale_rec',$data);
    }//end record pagination


    //show view approval record
     public function wholesale_vw_appr() { 
        $data = $this->mytrxpromo->wholesale_post_view(1,20);
        return view('transactions/promotions/wholesale/trx_wholesale_appr',$data);
    } //end view approval record

    //view approval pagination
    public function wholesale_recs_appr() { 
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0: $mpages);
        $data = $this->mytrxpromo->wholesale_post_view($mpages,20,$txtsearchedrec);
        return view('transactions/promotions/wholesale/trx_wholesale_appr',$data);
    } //end approval pagination
    

    //for approval saving
    public function wholesale_save_appr() { 
        $this->mytrxpromo->wholesale_for_approval();
    }//end for approval

    //generate barcode
    public function wholesale_barcode_dl_proc() { 
        $wholesale_trxno = $this->request->getVar('wholesale_trxno');
        $this->mytrxpromo->download_wholesale_barcode($wholesale_trxno);
    }//end generate barcode


}//end main class
