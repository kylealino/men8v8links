<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(true);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->get('mylogin', 'Mylogin::index');
$routes->add('mylogin-auth', 'Mylogin::auth');
$routes->get('melogout', 'Mylogin::logout');

$routes->get('mysecretme', 'MyTestMe::index');
$routes->get('mysecret-testlangito', 'MyTestMe::testlangito');
$routes->post('mdata-pos-mtkn', 'MyDataPOS::get_token');
$routes->get('mdata-pos-proc', 'MyDataPOS::mdata_pos_dload');

$routes->get('master-data-artm-dload', 'Md_article::md_dload',['filter' => 'myauthuser']);
$routes->post('master-data-artm-dload-proc', 'Md_article::md_dload_proc',['filter' => 'myauthuser']);

$routes->post('mywg-delvreg', 'MyWidgets::delvreg');
$routes->post('mywg-rcvdinfpout', 'MyWidgets::rcvdinfpout');
$routes->post('mywg-pouttob', 'MyWidgets::pouttob');
$routes->post('mywg-pouts', 'MyWidgets::pouts');

$routes->get('/', 'Home::index',['filter' => 'myauthuser']);
$routes->get('search-mat-article-vend', 'MySearchData::mat_article_vend');
$routes->get('search-company', 'MySearchData::company_search_v');
$routes->get('search-area-company', 'MySearchData::area_company');
$routes->get('search-vendor', 'MySearchData::vendor_ua');
$routes->get('search-rcv-frm-brnch-pullout', 'MySearchData::companybranch_pout');
$routes->get('search-mat-article-vend', 'MySearchData::mat_article_vend');
$routes->post('search-pick-from-trx', 'MySearchData::select_mo_items_rcv');
$routes->post('search-check-dr', 'MySearchData::drin_dr_checking');
$routes->get('search-mat-article', 'MySearchData::mat_article');

$routes->get('mymd-item-materials', 'Md_article::index');
$routes->post('search-mymd-item-materials', 'Md_article::recs');
$routes->post('mymd-item-materials-profile', 'Md_article::profile');
$routes->get('mymd-article-master-poslink', 'Md_article::POSLink',['filter' => 'myauthuser']);
$routes->post('mymd-article-master-poslink-branch', 'Md_article::POSLink_branch',['filter' => 'myauthuser']);
$routes->post('mymd-article-master-poslink-branch-recs', 'Md_article::POSLink_branch_recs',['filter' => 'myauthuser']);
$routes->post('mymd-article-master-poslink-download', 'Md_article::POSLink_branch_download',['filter' => 'myauthuser']);

$routes->get('dr-trx', 'MyDRTrx::dr_trx');
$routes->post('dr-trx-save', 'MyDRTrx::dr_trx_save');
$routes->post('dr-trx-rcv-recs', 'MyDRTrx::rcvrec_vw');
$routes->post('dr-trx-rcv-claims-save', 'MyDRTrx::dr_claims_save');

$routes->get('pullout-trx', 'MyPullOutTrx::index');
$routes->post('pullout-trx-save', 'MyPullOutTrx::man_recs_po_sv');
$routes->post('search-pullout-trx', 'MyPullOutTrx::poutrec_vw');
$routes->get('mytrx_acct/rpts_print', 'MyPullOutTrx::printing');


$routes->get('mymd-customer', 'Md_customer::index');
$routes->post('search-mymd-customer', 'Md_customer::recs');
$routes->post('mymd-customer-profile', 'Md_customer::profile');
$routes->post('mymd-customer-profile-save', 'Md_customer::profile_save');

$routes->get('mymd-supplier', 'Md_supplier::index');
$routes->post('search-mymd-supplier', 'Md_supplier::recs');
$routes->post('mymd-supplier-profile', 'Md_supplier::profile');
$routes->post('mymd-supplier-profile-save', 'Md_supplier::profile_save');

$routes->get('mymd-quota-rate', 'Md_quotarate::index');
$routes->post('search-mymd-quota-rate', 'Md_quotarate::recs');
$routes->post('mymd-qpr-profile', 'Md_qpr_employees::profile');
$routes->post('mymd-qpr-employees-save', 'Md_qpr_employees::profile_save');
$routes->get('mymd-qpr-employees', 'Md_qpr_employees::index');
$routes->post('search-mymd-qpr-employees', 'Md_qpr_employees::recs');

$routes->get('inventory-dr-in', 'MyInventory::dr_in');
$routes->get('trx-jo-quota', 'Mytrx::jo_quota');
$routes->get('trx-jo-delv-in', 'Mytrx::trx_jo_delv_in');
$routes->post('trx-jo-delv-in-sv', 'Mytrx::trx_jo_delv_in_sv');
$routes->get('search-customer', 'MySearchData::search_customer');
$routes->get('search-proc-quota-rate', 'MySearchData::proc_quota_rate');
$routes->get('search-prod-items', 'MySearchData::prod_items');
$routes->get('search-prod-items-uom', 'MySearchData::prod_items_uom');
$routes->get('search-prod-items-packaging', 'MySearchData::prod_items_packaging');
$routes->get('search-prod-type', 'MySearchData::prod_type');
$routes->get('search-prod-category', 'MySearchData::prod_category');
$routes->get('search-prod-sub-category', 'MySearchData::prod_sub_category');
$routes->get('search-mymd-qpr-prod-services', 'MySearchData::qpr_prod_services');
$routes->get('search-mymd-qpr-prod-operation', 'MySearchData::qpr_prod_operation');
$routes->get('search-mymd-qpr-prod-design-pattern', 'MySearchData::qpr_prod_design_pattern');
$routes->get('search-mymd-qpr-prod-sub-operation', 'MySearchData::qpr_prod_sub_operation');
$routes->get('search-mymd-qpr-prod-processes', 'MySearchData::qpr_prod_processes');

$routes->get('search-mat-article-ho', 'MySearchData::ho_mat_article',['filter' => 'myauthuser']);

//Promo Discount Routes

$routes->get('me-promo', 'Promo_discount::index');
$routes->get('me-promo-vw', 'Promo_discount::index');
$routes->add('me-promo-save', 'Promo_discount::promo_save');
$routes->add('me-promo-recs', 'Promo_discount::promo_recs');
$routes->add('me-promo-view', 'Promo_discount::promo_vw');
$routes->add('me-promo-print', 'Promo_discount::promo_print');
$routes->add('me-promo-appr', 'Promo_discount::promo_recs_appr');
$routes->add('me-promo-view-appr', 'Promo_discount::promo_vw_appr');
$routes->add('me-promo-appr-save', 'Promo_discount::promo_save_appr');
$routes->add('me-promo-barcode-dl', 'Promo_discount::promo_barcode_dl_proc');
$routes->get('get-promo-itemc','Promo_discount::mat_article');
$routes->get('get-branch-list','Promo_discount::companybranch_v');

//Promo buy1take1 Routes

$routes->get('me-buy1take1','Promo_buy1take1::index');
$routes->add('me-buy1take1-save', 'Promo_buy1take1::buy1take1_save');
$routes->add('me-buy1take1-view', 'Promo_buy1take1::buy1take1_vw');
$routes->add('me-buy1take1-recs', 'Promo_buy1take1::buy1take1_recs');
$routes->add('me-buy1take1-view-appr', 'Promo_buy1take1::buy1take1_vw_appr');
$routes->add('me-buy1take1-appr', 'Promo_buy1take1::buy1take1_recs_appr');
$routes->add('me-buy1take1-appr-save', 'Promo_buy1take1::buy1take1_save_appr');
$routes->add('me-buy1take1-barcode-dl', 'Promo_buy1take1::buy1take1_dl_proc');
$routes->get('me-buy1take1-vw', 'Promo_buy1take1::index');

//Promotion Buy Any at Price me-buyanyatprice

$routes->get('me-buyanyatprice', 'Promo_buyanyatprice::index');
$routes->add('me-buyanyatprice-save', 'Promo_buyanyatprice::buyanyatprice_save');
$routes->add('me-buyanyatprice-view', 'Promo_buyanyatprice::buyanyatprice_vw');
$routes->add('me-buyanyatprice-recs', 'Promo_buyanyatprice::buyanyatprice_recs');
$routes->add('me-buyanyatprice-view-appr', 'Promo_buyanyatprice::buyanyatprice_vw_appr');
$routes->add('me-buyanyatprice-appr', 'Promo_buyanyatprice::buyanyatprice_recs_appr');
$routes->add('me-buyanyatprice-appr-save', 'Promo_buyanyatprice::buyanyatprice_save_appr');
$routes->add('me-buyanyatprice-barcode-dl', 'Promo_buyanyatprice::buyanyatprice_dl_proc');


//Promo voucher routes

$routes->get('me-voucher', 'Promo_voucher::index');
$routes->get('me-voucher-vw', 'Promo_voucher::index');
$routes->add('me-voucher-save', 'Promo_voucher::voucher_save');
$routes->add('me-voucher-recs', 'Promo_voucher::voucher_recs');
$routes->add('me-voucher-view', 'Promo_voucher::voucher_vw');
$routes->add('me-voucher-print', 'Promo_voucher::voucher_print');
$routes->add('me-voucher-appr', 'Promo_voucher::voucher_recs_appr');
$routes->add('me-voucher-view-appr', 'Promo_voucher::voucher_vw_appr');
$routes->add('me-voucher-appr-save', 'Promo_voucher::voucher_save_appr');
$routes->add('me-voucher-barcode-dl', 'Promo_voucher::voucher_barcode_dl_proc');

//Promo threshhold routes

$routes->get('me-threshold', 'Promo_threshold::index');
$routes->get('me-threshold-vw', 'Promo_threshold::index');
$routes->add('me-threshold-save', 'Promo_threshold::threshold_save');
$routes->add('me-threshold-recs', 'Promo_threshold::threshold_recs');
$routes->add('me-threshold-view', 'Promo_threshold::threshold_vw');
$routes->add('me-threshold-print', 'Promo_threshold::threshold_print');
$routes->add('me-threshold-appr', 'Promo_threshold::threshold_recs_appr');
$routes->add('me-threshold-view-appr', 'Promo_threshold::threshold_vw_appr');
$routes->add('me-threshold-appr-save', 'Promo_threshold::threshold_save_appr');
$routes->add('me-threshold-barcode-dl', 'Promo_threshold::threshold_barcode_dl_proc');

//Promo wholesale routes

$routes->get('me-wholesale', 'Promo_wholesale::index');
$routes->get('me-wholesale-vw', 'Promo_wholesale::index');
$routes->add('me-wholesale-save', 'Promo_wholesale::wholesale_save');
$routes->add('me-wholesale-recs', 'Promo_wholesale::wholesale_recs');
$routes->add('me-wholesale-view', 'Promo_wholesale::wholesale_vw');
$routes->add('me-wholesale-print', 'Promo_wholesale::wholesale_print');
$routes->add('me-wholesale-appr', 'Promo_wholesale::wholesale_recs_appr');
$routes->add('me-wholesale-view-appr', 'Promo_wholesale::wholesale_vw_appr');
$routes->add('me-wholesale-appr-save', 'Promo_wholesale::wholesale_save_appr');
$routes->add('me-wholesale-barcode-dl', 'Promo_wholesale::wholesale_barcode_dl_proc');

//Pos Tally Upload

$routes->get('postally_upload', 'Postally_upload::index');
$routes->post('upload-do_upload', 'Postally_upload::do_upload');
$routes->post('upload-delete_file', 'Postally_upload::delete_file');
$routes->post('upload-view_file', 'Postally_upload::view_file');


//promotion spromo/qdamage
$routes->get('mypromo-spqd', 'Mypromo_spromoqdamage::index');
$routes->post('me-spqd-save', 'Mypromo_spromoqdamage::save_spqd');
$routes->get('promo_search', 'Mypromo_spromoqdamage::spqd_promo_search');
$routes->post('me-spqd-view', 'Mypromo_spromoqdamage::spqd_vw');
$routes->add('me-spqd-recs', 'Mypromo_spromoqdamage::spqd_recs');
$routes->get('mepromo-spqd-view', 'Mypromo_spromoqdamage::index');
$routes->add('me-spqd-view-appr', 'Mypromo_spromoqdamage::spqd_vw_appr');
$routes->add('me-spqd-dashboard', 'Mypromo_spromoqdamage::spqd_vw_dashboard');
$routes->add('me-spqd-appr', 'Mypromo_spromoqdamage::spqd_recs_appr');
$routes->add('me-spqd-appr-save', 'Mypromo_spromoqdamage::spqd_save_appr');
$routes->add('me-spqd-dashboard-view', 'Mypromo_spromoqdamage::dashboard_recs');
$routes->add('me-spqd-barcode-dl', 'Mypromo_spromoqdamage::spqd_dl_proc');

$routes->get('mypromo-spromo', 'Mypromo_spromo::index',['filter' => 'myauthuser']);
$routes->get('mypromo-spromo-codes', 'Mypromo_spromo::spromo_search',['filter' => 'myauthuser']);
$routes->get('mypromo-spromo-fromitems', 'Mypromo_spromo::fromspromo_items_search',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-save', 'Mypromo_spromo::save_recs',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-recs', 'Mypromo_spromo::view_recs',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-rundown', 'Mypromo_spromo::spromo_rundown',['filter' => 'myauthuser']);
$routes->get('mypromo-spromo-codes-rundown', 'Mypromo_spromo::tospromo_item_rundown_search',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-rundown-save', 'Mypromo_spromo::spromo_rundown_save',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-ivty-reg-mapping', 'Mypromo_spromo::spromo_ivty_reg_mapping',['filter' => 'myauthuser']);

//mypos related module
$routes->get('mypos-reprint-logs', 'MyPOSConn::reprint_logs');
$routes->post('mypos-reprint-recs-logs', 'MyPOSConn::reprint_recs_logs');
//mypos temporary routes
$routes->get('mypos-reprint', 'MyPOSConnx::index');

$routes->add('company-branch-ua', 'My_search::companybranch_v',['filter' => 'myauthuser']);
$routes->add('mat-article-ua', 'My_search::mat_article',['filter' => 'myauthuser']);
$routes->add('company-branch-tap-ua', 'My_search::companybranch_tap',['filter' => 'myauthuser']);
$routes->add('mat-art-section2', 'My_search::mat_art_section2',['filter' => 'myauthuser']);
$routes->add('mat-cg1', 'My_search::mat_cg1',['filter' => 'myauthuser']);
$routes->add('mat-cg2', 'My_search::mat_cg2',['filter' => 'myauthuser']);
$routes->add('mat-cg3', 'My_search::mat_cg3',['filter' => 'myauthuser']);
$routes->add('mat-cg4', 'My_search::mat_cg4',['filter' => 'myauthuser']);

//reports
//sales out daily tab 
$routes->get('sales-out-details', 'MyRpt_sales::sales-out-details',['filter' => 'myauthuser']);
$routes->post('sales-out-details-tab-daily', 'MyRpt_sales::sales-out-details-tab-daily',['filter' => 'myauthuser']);
$routes->post('sales-out-tally-daily', 'MyRpt_sales::sales-out-tally-daily',['filter' => 'myauthuser']);
$routes->post('sales-out-Acct-POS-tally', 'MyRpt_sales::sales-out-Acct-POS-tally',['filter' => 'myauthuser']);

//sales out daily tab generation
$routes->post('sales-out-details-tab-daily-proc', 'MyRpt_sales::sales_out_details_tab_daily_proc',['filter' => 'myauthuser']);
$routes->post('sales-out-details-tab-daily-rec', 'MyRpt_sales::sales_out_details_tab_daily_rec',['filter' => 'myauthuser']);
$routes->post('sales-out-details-daily-download', 'MyRpt_sales::sales_out_details_daily_download',['filter' => 'myauthuser']);
$routes->post('sales-out-tally-daily-proc', 'MyRpt_sales::sales-out-tally-daily-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-Acct-POS-tally-proc', 'MyRpt_sales::sales-out-Acct-POS-tally-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-Acct-POS-TAXR-proc', 'MyRpt_sales::sales-out-Acct-POS-TAXR-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-itemized-abranch-proc', 'MyRpt_sales::sales-out-itemized-abranch-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-recon-proc', 'MyRpt_sales::sales-out-recon-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-tally-daily-check-proc', 'MyRpt_sales::sales-out-tally-daily-check-proc',['filter' => 'myauthuser']);

$routes->get('myua', 'MyUser::user_access',['filter' => 'myauthuser']);
$routes->post('search-myuser', 'MyUser::user_rec',['filter' => 'myauthuser']);
$routes->post('myua-module-save', 'MyUser::user_module_access_save',['filter' => 'myauthuser']);

// reports 
$routes->get('myreport-inventory', 'MyRpt_inventory::index',['filter' => 'myauthuser']);
$routes->post('myreport-stockcard', 'MyRpt_inventory::stockcard',['filter' => 'myauthuser']);
$routes->post('myreport-stockcard-recs', 'MyRpt_inventory::stockcard_recs',['filter' => 'myauthuser']);

//ho version inventory report
$routes->get('myinventory-report', 'MyRpt_inventory::ho',['filter' => 'myauthuser']);
$routes->post('myinventory-report-detailed', 'MyRpt_inventory::ho_detailed',['filter' => 'myauthuser']);
$routes->post('myinventory-report-detailed-gen', 'MyRpt_inventory::ho_detailed_gen',['filter' => 'myauthuser']);
$routes->post('myinventory-report-summary', 'MyRpt_inventory::ho_summary',['filter' => 'myauthuser']);
$routes->get('myinventory-report-live-balance-dashboard', 'MyRpt_inventory::live_inventory_balance');
$routes->post('myreport-inventory-itemized', 'MyRpt_inventory::me_itemized',['filter' => 'myauthuser']);
$routes->post('myreport-inventory-itemized-proc', 'MyRpt_inventory::me_itemized_proc',['filter' => 'myauthuser']);
$routes->get('myreport-inventory-itemized-download', 'MyRpt_inventory::me_itemized_download',['filter' => 'myauthuser']);
$routes->post('myinventory-report-branch-conso', 'MyRpt_inventory::me_branch_conso',['filter' => 'myauthuser']);

$routes->get('mysales-deposit', 'MySalesDeposit::index',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-entry', 'MySalesDeposit::entry',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-save', 'MySalesDeposit::me_save',['filter' => 'myauthuser']);
$routes->get('mysales-deposit-get-group', 'MySalesDeposit::getdepositGroup',['filter' => 'myauthuser']);
$routes->get('mysales-deposit-get-Deposit-BranchAcct', 'MySalesDeposit::getDeposit_BrcnhAcct',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-recs', 'MySalesDeposit::deposit_recs_branch',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-dload-zip-files', 'MySalesDeposit::deposit_download_zip_file',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-delrec', 'MySalesDeposit::me_delrec',['filter' => 'myauthuser']);


$routes->get('myinventory-cycle-count', 'MyInventory::cycle_count',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-proc-upld-files', 'MyInventory::cycle_count_proc_uploaded_files',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-posting-uploaded', 'MyInventory::cycle_count_posting_uploaded',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-posting-uploaded-recs', 'MyInventory::cycle_count_posting_uploaded_recs',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-post-uploaded', 'MyInventory::cycle_count_post_uploaded',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-editing-uploaded-inquiry', 'MyInventory::cycle_count_uploaded_editing',['filter' => 'myauthuser']);

$routes->post('myinventory-proc-balance', 'MyInventory::proc_balance',['filter' => 'myauthuser']);

$routes->get('myinventory-recon-adj', 'MyInventory::recon_adj',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-entry', 'MyInventory::recon_adj_entry',['filter' => 'myauthuser']);
$routes->get('myinventory-recon-adj-search-mat', 'MyInventory::recon_adj_search_mat',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-entry-sv', 'MyInventory::recon_adj_entry_sv',['filter' => 'myauthuser']);
$routes->post('myinventory-report-detailed-download', 'MyRpt_inventory::ho_detailed_download',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-recs', 'MyInventory::recon_adj_recs',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-delrec', 'MyInventory::recon_adj_delrec',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-post-rec', 'MyInventory::recon_adj_postrec',['filter' => 'myauthuser']);

//RCP-PCF
$routes->get('myrfp','MyRfp::index',['filter' => 'myauthuser']);
$routes->post('myrfp-entry', 'MyRfp::entry',['filter' => 'myauthuser']);
$routes->get('get-expense-type','MyRfp::rfpcf_expense_type',['filter' => 'myauthuser']);
$routes->get('get-branch-name','MyRfp::company_search',['filter' => 'myauthuser']);
$routes->post('myrfp-view', 'MyRfp::myrfp_view',['filter' => 'myauthuser']);
$routes->post('mypcf-view', 'MyRfp::mypcf_view',['filter' => 'myauthuser']);

//Sub Item Masterdata 
$routes->get('sub-item-masterdata','Md_subitems::index',['filter' => 'myauthuser']);
$routes->post('sub-items-recs','Md_subitems::sub_item_recs',['filter' => 'myauthuser']);
$routes->post('sub-items-save','Md_subitems::sub_item_save',['filter' => 'myauthuser']);
$routes->get('get-main-itemc','Md_subitems::get_main_itemc',['filter' => 'myauthuser']);
$routes->get('get-uom','Md_subitems::get_uom',['filter' => 'myauthuser']);
$routes->post('sub-items-update','Md_subitems::sub_item_update',['filter' => 'myauthuser']);
$routes->add('sub-items-recs-vw', 'Md_subitems::sub_item_recs_vw');

//Sub Item Invetory Insertion
$routes->get('sub-item-inv','Md_subitems_inv::index',['filter' => 'myauthuser']);
$routes->get('get-branch','Md_subitems_inv::get_branch',['filter' => 'myauthuser']);
$routes->post('sub-inv-recs', 'Md_subitems_inv::sub_inv_recs_vw',['filter' => 'myauthuser']);
$routes->add('sub-inv-recs-vw', 'Md_subitems_inv::sub_inv_recs_vw',['filter' => 'myauthuser']);
$routes->post('sub-inv-recs-convf', 'Md_subitems_inv::sub_inv_recs_convf',['filter' => 'myauthuser']);
$routes->add('sub-inv-recs-vw-convf', 'Md_subitems_inv::sub_inv_recs_vw_convf',['filter' => 'myauthuser']);
$routes->post('sub-inv-save','Md_subitems_inv::sub_inv_save',['filter' => 'myauthuser']);

//Sub Item BOM
$routes->get('sub-item-bom','Md_subitems_bom::index',['filter' => 'myauthuser']);
$routes->get('get-sub-materials','Md_subitems_bom::get_sub_materials',['filter' => 'myauthuser']);
//Sub item Convertion
$routes->get('sub-item-convf','Md_subitems_convf::index',['filter' => 'myauthuser']);
$routes->add('sub-items-convf-vw', 'Md_subitems_convf::sub_item_convf_vw');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
