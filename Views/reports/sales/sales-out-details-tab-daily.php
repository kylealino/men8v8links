<?php

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');

$mydbname = model('App\Models\MyDBNamesModel');

$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();


$db_erp = $mydbname->medb(0);
$mt_2 = '';
$result = $myusermod->get_Active_menus($db_erp,$cuser,"myuaacct_id='167'","myua_acct");
if($result == 1){
    $str_style_d='';
}
else{
    $str_style_d=" style=\"display:none;\"";
    $mt_2 = 'mt-2';
}

$txt_sc2_dtefrom= '';
$txt_sc2_dteto= '';

?>
<div class="row mt-1 ms-1 me-1">
    <div class="col-lg-3 col-md-3 col-sm-3">
        <div class="card pt-1 mb-2">
            <div class="table-responsive">
                <table id="tbl_sc2_cat1" class="metblentry-font">
                    <thead>
                        <th> </th>
                        <th class="text-center">
                            <button type="button" class="btn btn-primary btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item('tbl_sc2_cat1');" >
                            <i class="bi bi-plus-lg"></i>
                            </button>
                        </th>
                        <th class="bg-warning metbl-th-pad6 bg-opacity-10">Product Type</th>
                    </thead>
                    <tbody id="contentArea">
                        <tr style="display:none;">
                            <td></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:confirmalert(this,'tbl_sc2_cat1','__hmtkn_prd_sc2_c1');">
                                <i class="bi bi-x-square-fill"></i>
                                </button>
                                <input type="hidden" value=""/>
                            </td>
                            <td><input type="text" size="50" class="txttopprdcat1" value="" data-meitmdata=""/></td> <!--itemcode-->
                        </tr>                
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3">
        <div class="card pt-1 mb-2">
            <div class="table-responsive">
                <table id="tbl_sc2_cat2" class="metblentry-font">
                    <thead>
                        <th> </th>
                        <th class="text-center">
                            <button type="button" class="btn btn-primary btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item('tbl_sc2_cat2');" >
                            <i class="bi bi-plus-lg"></i>
                            </button>
                        </th>
                        <th class="bg-warning metbl-th-pad6 bg-opacity-10">Section</th>
                    </thead>
                    <tbody id="contentArea">
                        <tr style="display:none;">
                            <td></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:confirmalert(this,'tbl_sc2_cat2','__hmtkn_prd_sc2_c2');">
                                <i class="bi bi-x-square-fill"></i>
                                </button>
                                <input type="hidden" value=""/>
                            </td>
                            <td><input type="text" size="50" class="txttopprdcat2" value="" /></td> <!--itemcode-->
                            </tr>                
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-3">
        <div class="card pt-1 mb-2">
            <div class="table-responsive">
                <table id="tbl_sc2_cat3" class="metblentry-font">
                    <thead>
                        <th> </th>
                        <th class="text-center">
                            <button type="button" class="btn btn-primary btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item('tbl_sc2_cat3');" >
                            <i class="bi bi-plus-lg"></i>
                            </button>
                        </th>
                        <th class="bg-warning metbl-th-pad6 bg-opacity-10">Product Class</th>
                    </thead>
                    <tbody id="contentArea">
                    <tr style="display:none;">
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:confirmalert(this,'tbl_sc2_cat3','__hmtkn_prd_sc2_c3');">
                            <i class="bi bi-x-square-fill"></i>
                            </button>
                            <input type="hidden" value=""/>
                        </td>
                        <td><input type="text" size="50" class="txttopprdcat3" value="" data-meitmdata="" /></td> <!--itemcode-->
                    </tr>                
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-3">
        <div class="card pt-1 mb-2">
            <div class="table-responsive">
                <table id="tbl_sc2_cat4" class="metblentry-font">
                    <thead>
                        <th> </th>
                        <th class="text-center">
                            <button type="button" class="btn btn-primary btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item('tbl_sc2_cat4');" >
                            <i class="bi bi-plus-lg"></i>
                            </button>
                        </th>
                        <th class="bg-warning metbl-th-pad6 bg-opacity-10">Product Specifications</th>
                    </thead>
                    <tbody id="contentArea">
                    <tr style="display:none;">
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:confirmalert(this,'tbl_sc2_cat4','__hmtkn_prd_sc2_c4');">
                            <i class="bi bi-x-square-fill"></i>
                            </button>
                            <input type="hidden" value=""/>
                        </td>
                        <td><input type="text" size="50" class="txttopprdcat4" value="" data-meitmdata="" /></td> <!--itemcode-->
                    </tr>                
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div> <!-- end row 1st -->
<div class="row mt-3 ms-1 me-1">
    <div class="col-md-8">
        <div class="card">
            <div class="row mt-2 mb-3 ms-1 me-1" <?=$str_style_d;?>>
                <div class="col-sm-3">
                    <span class="fw-bold">Department Name</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" Placeholder="Please select TSHIRT/PANTS"  data-id="" id="fld_tap_sc2" name="fld_tap_sc2" value="" required/>
                </div>
            </div>
            <div class="row <?=$mt_2;?> mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Item Code</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" data-id="" id="fld_sc2itemcode_s" name="fld_sc2itemcode_s" data-memtkn="" value="" required/>
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Desc. Code</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" data-id="" id="fld_sc2desccode" name="fld_sc2desccode" value="" required/>
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Branch</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" data-id="" id="fld_sc2branch_s" name="fld_sc2branch_s" value="" required/>
                    <input type="hidden"  data-id="" id="fld_sc2branch_id_s" name="fld_sc2branch_id_s"/>                    
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Date From</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm meform_datetime" name="fld_sc2_dtefrom" id="fld_sc2_dtefrom" placeholder="mm/dd/yyyy" value="" required/>
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Date To</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm meform_datetime" name="fld_sc2_dteto" id="fld_sc2_dteto" placeholder="mm/dd/yyyy" value="" required/>                    
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-6">
                    <button class="btn btn-success btn-sm" id="btn_recs_sv_sc2" name="btn_recs_sv_sc2" type="submit">Submit</button>
                    <button class="btn btn-success btn-sm" id="btn_itemized_abranch" name="btn_itemized_abranch" type="submit">All Branches Itemized</button>
                    <button class="btn btn-info btn-sm" id="__mbtn_sc2_download" name="__mbtn_sc2_download" type="submit">Download </button>                    
                </div>
            </div>

        </div>        

    </div> <!-- end col-8 -->    
</div> <!-- end row 2nd -->

<div class="row mt-3 ms-1 me-1">
    <div class="col-md-12" >
        <div class="card" id="meout-sales-out-defailts-daily">
        </div>
    </div>
</div> <!-- end row 3rd -->
<script type="text/javascript"> 
    // const mediv =  document.querySelector('div');

    document.body.classList.remove('toggle-sidebar');
    document.body.classList.add('toggle-sidebar');

    my_add_line_item('tbl_sc2_cat1');
    my_add_line_item('tbl_sc2_cat2');
    my_add_line_item('tbl_sc2_cat3');
    my_add_line_item('tbl_sc2_cat4');    
    /*
     $('.form_datetime').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: false,
                autoclose: true,
                format: 'mm/dd/yyyy'
                });
        
      */
    jQuery('.meform_datetime').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true
    });

        jQuery('#btn_recs_sv_sc2').click(function() { 
            try {   
                __mysys_apps.mepreloader('mepreloaderme',true);
                var fld_sc2itemcode_s= jQuery('#fld_sc2itemcode_s').val();
                var fld_sc2_dtefrom = jQuery('#fld_sc2_dtefrom').val();
                var fld_sc2_dteto = jQuery('#fld_sc2_dteto').val();
                var fld_sc2branch = jQuery('#fld_sc2branch_s').val();
                var fld_sc2branch_id = jQuery('#fld_sc2branch_id_s').val();
                var fld_tap_sc2 = jQuery('#fld_tap_sc2').val();
                var fld_sc2desccode= jQuery('#fld_sc2desccode').val();
                var rowCount_sc2_prdc1 = jQuery('#tbl_sc2_cat1 tr').length - 1;
                var rowCount_sc2_prdc2 = jQuery('#tbl_sc2_cat2 tr').length - 1;
                var rowCount_sc2_prdc3 = jQuery('#tbl_sc2_cat3 tr').length - 1;
                var rowCount_sc2_prdc4 = jQuery('#tbl_sc2_cat4 tr').length - 1;
                var arr1 = [];
                var arr2 = [];
                var arr3 = [];
                var arr4 = [];
               
                for(aa = 1; aa < rowCount_sc2_prdc1; aa++) { 
                      var clonedRow = jQuery('#tbl_sc2_cat1 tr:eq(' + aa + ')').clone(); 
                      var _sc2_prdc1 = jQuery(clonedRow).find('input[type=text]').eq(0);
                      var _sc2_prdc1 = jQuery(_sc2_prdc1).attr('data-meitmdata');
                      arr1.push(_sc2_prdc1);

                }
                for(aa = 1; aa < rowCount_sc2_prdc2; aa++) { 
                      var clonedRow = jQuery('#tbl_sc2_cat2 tr:eq(' + aa + ')').clone(); 
                      var _sc2_prdc2 = jQuery(clonedRow).find('input[type=text]').eq(0);
                      var _sc2_prdc2 = jQuery(_sc2_prdc2).attr('data-meitmdata');
                      arr2.push(_sc2_prdc2);

                } 
                for(aa = 1; aa < rowCount_sc2_prdc3; aa++) { 
                      var clonedRow = jQuery('#tbl_sc2_cat3 tr:eq(' + aa + ')').clone(); 
                      var _sc2_prdc3 = jQuery(clonedRow).find('input[type=text]').eq(0);
                      var _sc2_prdc3 = jQuery(_sc2_prdc3).attr('data-meitmdata');
                      arr3.push(_sc2_prdc3);

                } 
                for(aa = 1; aa < rowCount_sc2_prdc4; aa++) { 
                      var clonedRow = jQuery('#tbl_sc2_cat4 tr:eq(' + aa + ')').clone(); 
                      var _sc2_prdc4 = jQuery(clonedRow).find('input[type=text]').eq(0);
                      var _sc2_prdc4 = jQuery(_sc2_prdc4).attr('data-meitmdata');
                      arr4.push(_sc2_prdc4);

                }
                
                if(jQuery.trim(fld_sc2_dtefrom) == '') { 
                    __mysys_apps.mepreloader('mepreloaderme',false);
                    jQuery('#memsgsalesoutdetl_bod').html('Date From is REQUIRED!!!');
                    jQuery('#memsgsalesoutdetl').modal('show');
                    return false;
                }
                if(jQuery.trim(fld_sc2_dteto) == '') { 
                    __mysys_apps.mepreloader('mepreloaderme',false);
                    jQuery('#memsgsalesoutdetl_bod').html('Date To is REQUIRED!!!');
                    jQuery('#memsgsalesoutdetl').modal('show');
                    return false;
                }
                
                var mparam ={
                    __hmtkn_prd_sc2_c1 : arr1,
                    __hmtkn_prd_sc2_c2 : arr2,
                    __hmtkn_prd_sc2_c3 : arr3,
                    __hmtkn_prd_sc2_c4 : arr4,
                    fld_tap: fld_tap_sc2,
                    fld_sc2itemcode_s:fld_sc2itemcode_s,
                    fld_sc2_dtefrom: fld_sc2_dtefrom,
                    fld_sc2_dteto: fld_sc2_dteto,
                    fld_sc2branch: fld_sc2branch,
                    fld_sc2branch_id: fld_sc2branch_id,
                    fld_sc2desccode: fld_sc2desccode,
                    mpages: 1 
               }
               //mytrx_sc/sc2_vw_2
                jQuery.ajax({ // default declaration of ajax parameters
                    url: '<?=site_url()?>sales-out-details-tab-daily-proc',
                    method:"POST",
                    context: document.body,
                    data: eval(mparam),
                    global: false,
                    cache: false,
                    success: function(data)  { //display html using divID
                        __mysys_apps.mepreloader('mepreloaderme',false);
                        jQuery('#meout-sales-out-defailts-daily').html(data);
                        return false;
                    },
                    error: function() { // display global error on the menu function
                        __mysys_apps.mepreloader('mepreloaderme',false);
                        alert('error loading page...');
                        return false;
                    }   
                }); 
            } catch (err) {
                __mysys_apps.mepreloader('mepreloaderme',false);
                var mtxt = 'There was an error on this page.\n';
                mtxt += 'Error description: ' + err.message;
                mtxt += '\nClick OK to continue.';
                alert(mtxt);
            } //end try
        }); 

        jQuery('#btn_itemized_abranch').click(function() { 
            try {   
                __mysys_apps.mepreloader('mepreloaderme',true);
                var fld_sc2itemcode_s = jQuery('#fld_sc2itemcode_s').val();
                var itemmtkn = jQuery('#fld_sc2itemcode_s').attr('data-memtkn');
                var fld_sc2_dtefrom = jQuery('#fld_sc2_dtefrom').val();
                var fld_sc2_dteto = jQuery('#fld_sc2_dteto').val();
                var fld_sc2branch = jQuery('#fld_sc2branch_s').val();
                var fld_sc2branch_id = jQuery('#fld_sc2branch_id_s').val();
                
                var mparam = { 
                    meitem:fld_sc2itemcode_s,
                    meitemtkn:itemmtkn,
                    medatef: fld_sc2_dtefrom,
                    medatet: fld_sc2_dteto,
                    mebranch: fld_sc2branch,
                    mebranchmtkn: fld_sc2branch_id,
                    mpages: 1 
               }

                jQuery.ajax({ // default declaration of ajax parameters
                    url: '<?=site_url()?>sales-out-itemized-abranch-proc',
                    method:"POST",
                    context: document.body,
                    data: eval(mparam),
                    global: false,
                    cache: false,
                    success: function(data)  { //display html using divID
                        __mysys_apps.mepreloader('mepreloaderme',false);
                        jQuery('#meout-sales-out-defailts-daily').html(data);
                        return false;
                    },
                    error: function() { // display global error on the menu function
                        __mysys_apps.mepreloader('mepreloaderme',false);
                        alert('error loading page...');
                        return false;
                    }   
                }); 
            } catch (err) {
                __mysys_apps.mepreloader('mepreloaderme',false);
                var mtxt = 'There was an error on this page.\n';
                mtxt += 'Error description: ' + err.message;
                mtxt += '\nClick OK to continue.';
                alert(mtxt);
            } //end try
        }); // end button btn_itemized_abranch 


        jQuery('#fld_sc2branch')
            // don't navigate away from the field on tab when selecting an item
            .bind( 'keydown', function( event ) {
                if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( 'autocomplete' ).menu.active ) {
                    event.preventDefault();
            }
            if( event.keyCode === jQuery.ui.keyCode.TAB ) {
                event.preventDefault();
            }
        })
            .autocomplete({
                minLength: 0,
                source: '<?= site_url(); ?>company-branch-ua/',
                focus: function() {
                        // prevent value inserted on focus
                        return false;
                    },
                    search: function(oEvent, oUi) {
                        var sValue = jQuery(oEvent.target).val();
                        //var comp = jQuery('#fld_Company').val();
                        //var comp = jQuery('#fld_Company').attr("data-id");
                        //mysearchdata/companybranch_v
                        jQuery(this).autocomplete('option', 'source', '<?=site_url();?>company-branch-ua'); 
                        //jQuery(oEvent.target).val('&mcocd=1' + sValue);

                    },
                    select: function( event, ui ) {
                        var terms = ui.item.value;
                        var mtkn_comp = ui.item.mtkn_comp;
                        var mtknr_rid = ui.item.mtknr_rid;
                        var mtkn_brnch = ui.item.mtkn_brnch;
                        jQuery('#fld_sc2branch').val(terms);
                        jQuery('#fld_sc2branch_id').val(mtknr_rid);
                        jQuery(this).autocomplete('search', jQuery.trim(terms));
                        return false;
                    }
                })
            .click(function() {
                    /*var comp = jQuery('#fld_Company').val();
                    var comp2 = this.value +'XOX'+comp;
                    var terms = comp2.split('XOX');//dto naq 4/25
                    */
                    var terms = this.value;
                    jQuery(this).autocomplete('search', jQuery.trim(terms));

        });

        jQuery('#fld_sc2branch_s')
            // don't navigate away from the field on tab when selecting an item
            .bind( 'keydown', function( event ) {
                if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( 'autocomplete' ).menu.active ) {
                    event.preventDefault();
            }
            if( event.keyCode === jQuery.ui.keyCode.TAB ) {
                event.preventDefault();
            }
        })
        .autocomplete({
                minLength: 0,
                source: '<?= site_url(); ?>company-branch-ua',
                focus: function() {
                        // prevent value inserted on focus
                        return false;
                    },
                    search: function(oEvent, oUi) {
                        var sValue = jQuery(oEvent.target).val();
                        //var comp = jQuery('#fld_Company').val();
                        //var comp = jQuery('#fld_Company').attr("data-id");
                        //mysearchdata/companybranch_v
                        jQuery(this).autocomplete('option', 'source', '<?=site_url();?>company-branch-ua'); 
                        //jQuery(oEvent.target).val('&mcocd=1' + sValue);

                    },
                    select: function( event, ui ) {
                        var terms = ui.item.value;
                        var mtkn_comp = ui.item.mtkn_comp;
                        var mtknr_rid = ui.item.mtknr_rid;
                        var mtkn_brnch = ui.item.mtkn_brnch;
                        jQuery('#fld_sc2branch_s').val(terms);
                        jQuery('#fld_sc2branch_id_s').val(mtknr_rid);
                        jQuery(this).autocomplete('search', jQuery.trim(terms));
                        return false;
                    }
                })
            .click(function() {
                /*var comp = jQuery('#fld_Company').val();
                var comp2 = this.value +'XOX'+comp;
                var terms = comp2.split('XOX');//dto naq 4/25
                */
                var terms = this.value;
                jQuery(this).autocomplete('search', jQuery.trim(terms));
        }); // end fld_sc2branch_s
        
        jQuery('#fld_sc2itemcode_s') 
            // don't navigate away from the field on tab when selecting an item
            //mysearchdata/mat_article/
           .bind( 'keypress', function( event ) {
                if (event.keyCode === jQuery.ui.keyCode.ENTER && jQuery( this ).data( 'autocomplete-ui' ).menu.active ) {
                    event.preventDefault();
                    }
                    if( event.keyCode === jQuery.ui.keyCode.ENTER ) {
                        event.preventDefault();
                    }
                    
                })
            .autocomplete({
                minLength: 0,
                source: '<?= site_url(); ?>mat-article-ua',
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                search: function(oEvent, oUi) { 
                    var sValue = jQuery(oEvent.target).val();
                    //jQuery(oEvent.target).val('&mcocd=1' + sValue);
                    //alert(sValue);
                },
                select: function( event, ui ) {
                    var terms = ui.item.value;
                    jQuery(this).attr('alt', jQuery.trim(ui.item.ART_CODE));
                    jQuery(this).attr('title', jQuery.trim(ui.item.ART_CODE));
                    jQuery(this).attr('src', jQuery.trim(ui.item.ART_IMG));
                    jQuery(this).attr('data-memtkn',ui.item.mtkn_rid);
                    
                    //console.log(wshe_id);
                    this.value = ui.item.ART_CODE;
                    
                    return false;
                }
            })
            .click(function() { 
                //jQuery(this).keydown(); 
                var terms = this.value.split('=>');
                //jQuery(this).autocomplete('search', '');
                jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
        });  //end fld_sc2itemcode_s

         $('#__mbtn_sc2_download').click(function() { 
            try {   
                var fld_sc2itemcode_s= $('#fld_sc2itemcode_s').val();
                var fld_sc2_dtefrom = $('#fld_sc2_dtefrom').val();
                var fld_sc2_dteto = $('#fld_sc2_dteto').val();
                var fld_sc2branch = $('#fld_sc2branch_s').val();
                var fld_sc2branch_id = $('#fld_sc2branch_id_s').val();
                var fld_tap_sc2 = $('#fld_tap_sc2').val();
                var fld_sc2desccode= $('#fld_sc2desccode').val();
                var rowCount_sc2_prdc1 = jQuery('#tbl_sc2_cat1 tr').length - 1;
                var rowCount_sc2_prdc2 = jQuery('#tbl_sc2_cat2 tr').length - 1;
                var rowCount_sc2_prdc3 = jQuery('#tbl_sc2_cat3 tr').length - 1;
                var rowCount_sc2_prdc4 = jQuery('#tbl_sc2_cat4 tr').length - 1;
                var arr1 = [];
                var arr2 = [];
                var arr3 = [];
                var arr4 = [];
               
                for(aa = 1; aa < rowCount_sc2_prdc1; aa++) { 
                      var clonedRow = jQuery('#tbl_sc2_cat1 tr:eq(' + aa + ')').clone(); 
                      var _sc2_prdc1 = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
                      arr1.push(_sc2_prdc1);

                }
                for(aa = 1; aa < rowCount_sc2_prdc2; aa++) { 
                      var clonedRow = jQuery('#tbl_sc2_cat2 tr:eq(' + aa + ')').clone(); 
                      var _sc2_prdc2 = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
                      arr2.push(_sc2_prdc2);

                } 
                for(aa = 1; aa < rowCount_sc2_prdc3; aa++) { 
                      var clonedRow = jQuery('#tbl_sc2_cat3 tr:eq(' + aa + ')').clone(); 
                      var _sc2_prdc3 = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
                      arr3.push(_sc2_prdc3);

                } 
                for(aa = 1; aa < rowCount_sc2_prdc4; aa++) { 
                      var clonedRow = jQuery('#tbl_sc2_cat4 tr:eq(' + aa + ')').clone(); 
                      var _sc2_prdc4 = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
                      arr4.push(_sc2_prdc4);

                }
                
                if($.trim(fld_sc2_dtefrom) == '') { 
                    jQuery('#myModSysMsgBod').css({
                        display: ''
                    });
                    jQuery('#myModSysMsgBod').html('Select Month!!!');
                    jQuery('#myModSysMsg').modal('show');
                    return false;
                }
                if($.trim(fld_sc2_dteto) == '') { 
                    jQuery('#myModSysMsgBod').css({
                        display: ''
                    });
                    jQuery('#myModSysMsgBod').html('Select Year!!!');
                    jQuery('#myModSysMsg').modal('show');
                    return false;
                }
                $.showLoading({name: 'line-pulse', allowHide: false });
                
                var mparam ={
                    __hmtkn_prd_sc2_c1 : arr1,
                    __hmtkn_prd_sc2_c2 : arr2,
                    __hmtkn_prd_sc2_c3 : arr3,
                    __hmtkn_prd_sc2_c4 : arr4,
                    fld_tap: fld_tap_sc2,
                    fld_sc2itemcode_s:fld_sc2itemcode_s,
                    fld_sc2_dtefrom: fld_sc2_dtefrom,
                    fld_sc2_dteto: fld_sc2_dteto,
                    fld_sc2branch: fld_sc2branch,
                    fld_sc2branch_id: fld_sc2branch_id,
                    fld_sc2desccode: fld_sc2desccode,
                    mpages: 1 
               }
                $.ajax({ // default declaration of ajax parameters
                    url: '<?=site_url()?>mytrx_sc/sc2_download_proc',
                    method:"POST",
                    context: document.body,
                    data: eval(mparam),
                    global: false,
                    cache: false,
                    success: function(data)  { //display html using divID
                        $.hideLoading();
                        jQuery('#mymodoutrecs_sc2').html(data);
                        return false;
                    },
                    error: function() { // display global error on the menu function
                        alert('error loading page...');
                        $.hideLoading();
                        return false;
                    }   
                }); 
            } catch (err) {
                var mtxt = 'There was an error on this page.\n';
                mtxt += 'Error description: ' + err.message;
                mtxt += '\nClick OK to continue.';
                $.hideLoading();
                alert(mtxt);
            } //end try
        });

        jQuery('#fld_tap_sc2')
            // don't navigate away from the field on tab when selecting an item
            //mysearchdata/companybranch_tap/
            .bind( 'keydown', function( event ) {
                if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( 'autocomplete' ).menu.active ) {
                    event.preventDefault();
            }
            if( event.keyCode === jQuery.ui.keyCode.TAB ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            minLength: 0,
            source: '<?= site_url(); ?>company-branch-tap-ua',
            focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                search: function(oEvent, oUi) {
                    var sValue = jQuery(oEvent.target).val();
                    //var comp = jQuery('#fld_Company').val();
                    //var comp = jQuery('#fld_Company').attr("data-id");
                    jQuery(this).autocomplete('option', 'source', '<?=site_url();?>company-branch-tap-ua'); 

                },
                select: function( event, ui ) {
                    var terms = ui.item.value;
                    var mtknr_rid = ui.item.mtknr_rid;
                    var mtkn_brnch = ui.item.mtkn_brnch;
                    jQuery('#fld_tap_sc2').val(terms);
                    jQuery(this).autocomplete('search', jQuery.trim(terms));
                    return false;
                }
            })
        .click(function() {
            var terms = this.value;
            jQuery(this).autocomplete('search', jQuery.trim(terms));
        }); 


        function __do_makeid()
        {
              var text = '';
              var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

              for( var i=0; i < 7; i++ )
                  text += possible.charAt(Math.floor(Math.random() * possible.length));

              return text;
        }
    function confirmalert(smuid,tbl_name){
        var userselection = confirm("Are you sure you want to remove this item permanently?");
        if (userselection == true){
            alert("Item deleted!");
            nullvalue(smuid,tbl_name);
          }
        else{
            alert("Item is not deleted!");
        }    
    }
    function nullvalue(muid,tbl_name) {
        jQuery(muid).parent().parent().remove();
        $( '#'+ tbl_name +' tr').each(function(i) { 
                $(this).find('td').eq(0).html(i);
        });
        

    }
    function my_add_line_item(tbl_name,_hmtknid,_hmtkn_sc2_prd) { 
         try {
          var rowCount = jQuery('#'+ tbl_name +' tr').length;
          var mid = __do_makeid() + (rowCount + 1);
          var clonedRow = jQuery('#'+ tbl_name +' tr:eq(' + (rowCount - 1) + ')').clone(); 
          jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id',_hmtknid + mid);
          jQuery(clonedRow).find('input[type=text]').eq(0).attr('id',_hmtkn_sc2_prd + mid);
         


          jQuery('#'+ tbl_name +' tr').eq(rowCount - 1).before(clonedRow);
          jQuery(clonedRow).css({'display':''});
          

          __my_sc2_cat1_lookup();
          __my_sc2_cat2_lookup();
          __my_sc2_cat3_lookup();
          __my_sc2_cat4_lookup();
          $( '#'+ tbl_name +' tr').each(function(i) { 
             $(this).find('td').eq(0).html(i);
          });
      } catch(err) { 
          var mtxt = 'There was an error on this page.\\n';
          mtxt += 'Error description: ' + err.message;
          mtxt += '\\nClick OK to continue.';
          alert(mtxt);
          return false;
          }  //end try 
  }

__my_sc2_cat1_lookup();
function __my_sc2_cat1_lookup() {  
      jQuery('.txttopprdcat1' ) 
        // don't navigate away from the field on tab when selecting an item
        //mysearchdata/mat_cg1/
        .bind( 'keydown', function( event ) {
          if ( event.keyCode === jQuery.ui.keyCode.TAB &&
              jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
              event.preventDefault();
        }
        if( event.keyCode === jQuery.ui.keyCode.TAB ) {
          event.preventDefault();
        }
        })
      .autocomplete({
          minLength: 0,
          source: '<?= site_url(); ?>mat-cg1/',
          focus: function() {
              // prevent value inserted on focus
          },
          search: function(oEvent, oUi) { 
              var sValue = jQuery(oEvent.target).val();
              //jQuery(oEvent.target).val('&mcocd=1' + sValue);
              //alert(sValue);
          },
          select: function( event, ui ) {
            var terms = ui.item.value.split('=>');
            jQuery(this).attr('alt', jQuery.trim(ui.item.value));
            jQuery(this).attr('title', jQuery.trim(ui.item.value));
            jQuery(this).attr('data-meitmdata', terms[0]);
            this.value = ui.item.value;
            }
        })
        .click(function() { 
          //jQuery(this).keydown(); 
          var terms = this.value.split('=>');
          jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
        }); //end txttopprdcat1
}  //end __my_sc2_cat1_lookup

__my_sc2_cat2_lookup();
function __my_sc2_cat2_lookup() {  
    jQuery('.txttopprdcat2' ) 
    // don't navigate away from the field on tab when selecting an item
    //mysearchdata/mat_cg2/
    .bind( 'keydown', function( event ) {
        if ( event.keyCode === jQuery.ui.keyCode.TAB &&
            jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
            event.preventDefault();
    }
    if( event.keyCode === jQuery.ui.keyCode.TAB ) {
        event.preventDefault();
    }
    })
    .autocomplete({
        minLength: 0,
        source: '<?= site_url(); ?>mat-cg2/',
        focus: function() {
            // prevent value inserted on focus
            return false;
        },
        search: function(oEvent, oUi) { 
            var sValue = jQuery(oEvent.target).val();
            //jQuery(oEvent.target).val('&mcocd=1' + sValue);
            //alert(sValue);
        },
        select: function( event, ui ) {
            var terms = ui.item.value.split('=>');
            jQuery(this).attr('alt', jQuery.trim(ui.item.value));
            jQuery(this).attr('title', jQuery.trim(ui.item.value));
            jQuery(this).attr('data-meitmdata', terms[0]);
            this.value = ui.item.value;
            return false;
        }
    })
    .click(function() { 
        //jQuery(this).keydown(); 
        var terms = this.value.split('=>');
        //jQuery(this).autocomplete('search', '');
        jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
    });        
} // end __my_sc2_cat2_lookup

   __my_sc2_cat3_lookup();
   function __my_sc2_cat3_lookup() {  
        jQuery('.txttopprdcat3' ) 
            // don't navigate away from the field on tab when selecting an item
            .bind( 'keydown', function( event ) {
                if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
                    event.preventDefault();
            }
            if( event.keyCode === jQuery.ui.keyCode.TAB ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            minLength: 0,
            source: '<?= site_url(); ?>mat-cg3/',
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            search: function(oEvent, oUi) { 
                var sValue = jQuery(oEvent.target).val();
                //jQuery(oEvent.target).val('&mcocd=1' + sValue);
                //alert(sValue);
            },
            select: function( event, ui ) {
                var terms = ui.item.value.split('=>');
                jQuery(this).attr('alt', jQuery.trim(ui.item.value));
                jQuery(this).attr('title', jQuery.trim(ui.item.value));
                jQuery(this).attr('data-meitmdata', terms[0]);
                this.value = ui.item.value;
                return false;
            }
        })
        .click(function() { 
            //jQuery(this).keydown(); 
            var terms = this.value.split('=>');
            jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
        });        
    }  //end __my_sc2_cat3_lookup

    __my_sc2_cat4_lookup();
    function __my_sc2_cat4_lookup() {  
        jQuery('.txttopprdcat4' ) 
            // don't navigate away from the field on tab when selecting an item
            .bind( 'keydown', function( event ) {
              if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                  jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
                  event.preventDefault();
            }
            if( event.keyCode === jQuery.ui.keyCode.TAB ) {
              event.preventDefault();
            }
        })
        .autocomplete({
          minLength: 0,
          source: '<?= site_url(); ?>mat-cg4/',
          focus: function() {
              // prevent value inserted on focus
              return false;
          },
          search: function(oEvent, oUi) { 
              var sValue = jQuery(oEvent.target).val();
              //jQuery(oEvent.target).val('&mcocd=1' + sValue);
              //alert(sValue);
          },
          select: function( event, ui ) {
            var terms = ui.item.value.split('=>');
            jQuery(this).attr('alt', jQuery.trim(ui.item.value));
            jQuery(this).attr('title', jQuery.trim(ui.item.value));
            jQuery(this).attr('data-meitmdata', terms[0]);
               
            this.value = ui.item.value;
            return false;
            }
        })
        .click(function() { 
          //jQuery(this).keydown(); 
          var terms = this.value.split('=>');
          //jQuery(this).autocomplete('search', '');
          jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
        });  //end txttopprdcat4
    } //end __my_sc2_cat4_lookup

    jQuery('#fld_sc2desccode') 
        // don't navigate away from the field on tab when selecting an item
        // mat-art-section2
        //ysearchdata/mat_art_section2
        .bind( 'keydown', function( event ) {
          if ( event.keyCode === jQuery.ui.keyCode.TAB &&
            jQuery( this ).data( 'autocomplete' ).menu.active ) {
            event.preventDefault();
        }
        if( event.keyCode === jQuery.ui.keyCode.TAB ) {
          event.preventDefault();
        }
    })
    .autocomplete({
      minLength: 0,
      autoFocus:true,
      source: '<?= site_url(); ?>mat-art-section2/',
      focus: function() {
            // prevent value inserted on focus
            return false;
        },
        search: function(oEvent, oUi) { 
          var sValue = jQuery(oEvent.target).val();
            //jQuery(oEvent.target).val('&mcocd=1' + sValue);
            //alert(sValue);
        },
        select: function( event, ui ) {
          var terms = ui.item.value;
          jQuery(this).attr('alt', jQuery.trim(ui.item.value));
          jQuery(this).attr('title', jQuery.trim(ui.item.value));
          this.value = ui.item.value;
            return false;
        }
    })
    .click(function() { 
        //jQuery(this).keydown(); 
        var terms = this.value;
        //jQuery(this).autocomplete('search', '');
        jQuery(this).autocomplete('search', jQuery.trim(terms));
    });  //end fld_sc2desccode

</script>
