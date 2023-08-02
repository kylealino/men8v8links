<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MyPromoDiscountModel;
use App\Models\MyLibzDBModel;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzSysModel;
use App\Models\MyUserModel;
use App\Libraries\Fpdf\Mypdf;

class Promo_discount extends BaseController
{
    //start construct
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
    } //end construct

    //default route
    public function index()
    {
        $cuser = $this->myusermod->mysys_user();
        //$result = $this->myusermod->get_Active_menus($this->db_erp, $cuser, "myuatrx_id='236'", "myua_trx");
        //if ($result == 0) :
        //    echo view('errors/html/error_404', $this->data);
        //    die();
        //endif;
        echo view('templates/meheader01');
        echo view('transactions/promotions/discount/trx_promo');
        echo view('templates/mefooter01');
    } //end index route

    //entry save
    public function promo_save()
    {
        $this->mytrxpromo->promo_entry_save();
    } //end test_entry_save

    //start record viewing
    public function promo_vw()
    {
        $data = $this->mytrxpromo->promo_rec_view(1, 10);
        return view('transactions/promotions/discount/trx_promo_rec', $data);
    } //end record viewing

    //start record pagination
    public function promo_recs()
    {
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mytrxpromo->promo_rec_view($mpages, 10, $txtsearchedrec);
        return view('transactions/promotions/discount/trx_promo_rec', $data);
    } //end record pagination


    //show view approval record
    public function promo_vw_appr()
    {
        $data = $this->mytrxpromo->promo_post_view(1, 20);
        return view('transactions/promotions/discount/trx_promo_appr', $data);
    } //end view approval record

    //view approval pagination
    public function promo_recs_appr()
    {
        $txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mytrxpromo->promo_post_view($mpages, 20, $txtsearchedrec);
        return view('transactions/promotions/discount/trx_promo_appr', $data);
    } //end approval pagination

    //barcode generate
    public function barcde_gnrtion()
    {
        $this->mytrxpromo->promo_barcde_gnrtion();
    } //end barcode generate 

    //for approval saving
    public function promo_save_appr()
    {
        $this->mytrxpromo->promo_for_approval();
    } //end for approval

    //generate barcode
    public function promo_barcode_dl_proc()
    {
        $promo_trxno = $this->request->getVar('promo_trxno');
        $this->mytrxpromo->download_promo_barcode($promo_trxno);
    } //end generate barcode

    public function mat_article()
    {

        $cuser   = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $term    = $this->request->getVar('term');

        $autoCompleteResult = array();
        $str = "
        SELECT
            aa.	recid,trim(aa.`ART_CODE`) __mdata, aa.`ART_DESC`, aa.`ART_BARCODE1`,aa.`ART_UPRICE`, aa.`ART_UCOST`,
            sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_rid 
        FROM {$this->db_erp}.`mst_article` aa 
        WHERE (aa.ART_CODE like '%$term%' or aa.ART_DESC like '%$term%')
       
        ";
       
        
      
        $q =  $this->mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if ($q->getNumRows() > 0) {
            $rrec = $q->getResultArray();
            foreach ($rrec as $row) :
                $mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn);
                array_push($autoCompleteResult, array(
                    "value" => $row['BRNCH_NAME'],
                    "mtkn_rid" => $mtkn_rid,
                    "BRNCH_NAME" => $row['BRNCH_NAME'],
                    "BRNCH_OCODE2" => $row['BRNCH_OCODE2'],
                    "COMP_NAME" => $row['COMP_NAME']

                ));
            endforeach;
        }
        $this->mytrxpromo->__search_artmaster();
        $q->freeResult();
        
    } //end mat_article	

    //start hover plant

    //start hover branch
    public function companybranch_v()
    {
        $cuser   = $this->myusermod->mysys_user();
        $mpw_tkn = $this->myusermod->mpw_tkn();
        $term    = $this->request->getVar('term');

        $autoCompleteResult = array();

        $str = "
            SELECT
            aa.`recid`,
            aa.`BRNCH_OCODE2`,
            aa.`BRNCH_NAME`,
            bb.`COMP_NAME`
    
            FROM 
            `mst_companyBranch` aa
            JOIN
            mst_company bb
            ON
            aa.`COMP_ID` = bb.`recid`
            
    
            WHERE aa.`BRNCH_OCODE2` like '%{$term}%'
            ";

        $q =  $this->mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if ($q->getNumRows() > 0) {
            $rrec = $q->getResultArray();
            foreach ($rrec as $row) :
                $mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn);
                array_push($autoCompleteResult, array(
                    "value" => $row['BRNCH_NAME'],
                    "mtkn_rid" => $mtkn_rid,
                    "BRNCH_NAME" => $row['BRNCH_NAME'],
                    "BRNCH_OCODE2" => $row['BRNCH_OCODE2'],
                    "COMP_NAME" => $row['COMP_NAME']

                ));
            endforeach;
        }

        $q->freeResult();
        echo json_encode($autoCompleteResult);
    } // end companybranch


}//end main class