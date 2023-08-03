<?php
/**
 *	File        : masterdata/sub_masterdata/sub-md-item-recs.php
 *  Auhtor      : Kyle Alino
 *  Date Created: Jul 28, 2023
 * 	last update : Jul 28, 2023
 * 	description : Sub Masterdata Main
 */

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');



$sub_itemc = $request->getVar('sub_itemc');
$main_itemc = '';
$cost = '';
$sub_desc = '';
$barcode = '';
$convf = '';
$uom = '';
$srp = '';
$recid = '';

if (!empty($sub_itemc)) {
    $str="
    SELECT
        `recid`,
        `ART_CODE` as main_itemc,
        `SUB_ART_CODE` as sub_itemc,
        `BARCODE` as barcode,
        `CONVF` as convf,
        `UOM` as uom,
        `SRP` as srp,
        `SUB_DESC` as sub_desc,
        `COST` as cost
    FROM
        `mst_cs_article`
    WHERE 
        `SUB_ART_CODE` = '$sub_itemc'
    ";
    $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    $rw = $q->getRowArray();    
    $main_itemc = $rw['main_itemc'];
    $barcode = $rw['barcode'];
    $convf = $rw['convf'];
    $uom = $rw['uom'];
    $srp = $rw['srp'];
    $sub_desc = $rw['sub_desc'];
    $cost = $rw['cost'];
    $recid = $rw['recid'];
}
?>
<main id="main">
    <div class="pagetitle">
    <h1>Sub Item Masterdata</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Sub Item Masterdata</li>
            </ol>
        </nav>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header mb-3">
                    <h3 class="h4 mb-0"> <i class="bi bi-pencil-square"></i> Entry</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="row mb-3">
                                <label class="col-sm-3 form-label" for="main_itemc">Main Itemcode:</label>
                                <div class="col-sm-9">
                                    <input type="text" id="main_itemc" name="main_itemc" class="form-control form-control-sm bg-white" value="<?=$main_itemc;?>" autocomplete="off"/>
                                    <input type="hidden" name="recid" id="recid" value="<?=$recid;?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 form-label" for="sub_itemc">Sub Itemcode:</label>
                                <div class="col-sm-9">
                                    <input type="text" id="sub_itemc" name="sub_itemc" class="form-control form-control-sm" value="<?=$sub_itemc;?>" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 form-label" for="sub_desc">Sub Itemcode Description:</label>
                                <div class="col-sm-9">
                                    <input type="text" id="sub_desc" name="sub_desc" class="form-control form-control-sm" value="<?=$sub_desc;?>" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 form-label" for="barcode">Barcode:</label>
                                <div class="col-sm-9">
                                    <input type="number" id="barcode" name="barcode" class="form-control form-control-sm" value="<?=$barcode;?>" maxlength="13" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 form-label" for="convf">Conversion Factor:</label>
                                <div class="col-sm-9">
                                    <input type="number" id="convf" name="convf" class="form-control form-control-sm" value="<?=$convf;?>" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 form-label" for="uom">UOM:</label>
                                <div class="col-sm-9">
                                    <input type="text" id="uom" name="uom" class="form-control form-control-sm bg-white" value="<?=$uom;?>" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="row mb-3">  
                                <label class="col-sm-3 form-label" for="cost">COST:</label>
                                <div class="col-sm-9">
                                    <input type="number" id="cost" name="cost" class="form-control form-control-sm" value="<?=$cost;?>" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="row mb-3">  
                                <label class="col-sm-3 form-label" for="srp">SRP:</label>
                                <div class="col-sm-9">
                                    <input type="number" id="srp" name="srp" class="form-control form-control-sm" value="<?=$srp;?>" autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">

                        </div>
                    </div> 
                    <div class="row gy-2 mb-3">
                        <div class="col-sm-4">
                            <?php if(!empty($sub_itemc)):?>
                                <button id="mbtn_mn_Update" type="submit" class="btn btn-success btn-sm">Update</button>
                                <?=anchor('sub-item-masterdata', '<i class="bi bi-arrow-repeat"></i>',' class="btn btn-outline-success btn-sm" ');?>
                            <?php else:?>
                                <button id="mbtn_mn_Save" type="submit" class="btn btn-success btn-sm">Save</button>
                                <?=anchor('sub-item-masterdata', '<i class="bi bi-arrow-repeat"></i>',' class="btn btn-outline-success btn-sm" ');?>
                            <?php endif;?>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div> 

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header mb-3">
                    <h3 class="h4 mb-0"> <i class="bi bi-pencil-square"></i> Records</h3>
                </div>
                <div class="card-body">
                    <div class="pt-2 bg-dgreen mt-2" style="background-color: #167F92;"> 
                        <nav class="nav nav-pills flex-column flex-sm-row  gap-1 px-2 fw-bold">
                            <a id="anchor-list" class="flex-sm-fill text-sm-center mytab-item active p-2  rounded-top" aria-current="page" href="#"> <i class="bi bi-ui-checks"> </i> List</a>
                            <a id="anchor-items" class=" flex-sm-fill text-sm-center mytab-item  p-2 rounded-top " href="#"><i class="bi bi-ui-radios"></i> Items</a>
                        </nav>
                    </div>
                    <div id="subitems" class="text-center p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">
                        <?php

                        ?> 
                    </div> 
                </div>
                </div> 
            </div>
        </div>
    </div> 

</main>
<?php
    echo $mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
    echo $mylibzsys->memypreloader01('mepreloaderme');
    echo $mylibzsys->memsgbox1('memsgtestent','System Alert','...');
    ?> 
<script>

$(document).ready(function(){
    sub_items_view();
    document.getElementById('barcode').addEventListener('input', function() {
    if (this.value.length > this.maxLength) {
        this.value = this.value.slice(0, this.maxLength);
    }
    });
});

$("#mbtn_mn_Save").click(function(e){
    try { 
          //__mysys_apps.mepreloader('mepreloaderme',true);
          var main_itemc = jQuery('#main_itemc').val();
          var sub_itemc = jQuery('#sub_itemc').val();
          var sub_desc = jQuery('#sub_desc').val();
          var barcode = jQuery('#barcode').val();
          var convf = jQuery('#convf').val();
          var uom = jQuery('#uom').val();
          var cost = jQuery('#cost').val();
          var srp = jQuery('#srp').val();

          var mparam = {
            main_itemc:main_itemc,
            sub_itemc:sub_itemc,
            sub_desc:sub_desc,
            barcode:barcode,
            convf:convf,
            uom:uom,
            cost:cost,
            srp:srp,
          };  


      $.ajax({ 
        type: "POST",
        url: '<?=site_url();?>sub-items-save',
        context: document.body,
        data: eval(mparam),
        global: false,
        cache: false,
        success: function(data)  { 
            $(this).prop('disabled', false);
           // $.hideLoading();
            jQuery('#memsgtestent_bod').html(data);
            jQuery('#memsgtestent').modal('show');
            return false;
        },
        error: function() {
          alert('error loading page...');
         // $.hideLoading();
          return false;
        } 
      });

    } catch(err) {
      var mtxt = 'There was an error on this page.\n';
      mtxt += 'Error description: ' + err.message;
      mtxt += '\nClick OK to continue.';
      alert(mtxt);
    }  //end try
    return false; 
  });

  __mysys_apps.mepreloader('mepreloaderme',false);

$('#anchor-list').on('click',function(){
    $('#anchor-list').addClass('active');
    $('#anchor-items').removeClass('active');
    var mtkn_whse = '';
    sub_items_view();

});

function sub_items_view(){ 
    var ajaxRequest;

    ajaxRequest = jQuery.ajax({
        url: "<?=site_url();?>sub-items-recs",
        type: "post"
    });

    __mysys_apps.mepreloader('mepreloaderme',true);
      ajaxRequest.done(function(response, textStatus, jqXHR) {
          jQuery('#subitems').html(response);
          __mysys_apps.mepreloader('mepreloaderme',false);
      });
  };

  jQuery('#main_itemc')
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
          source: '<?= site_url(); ?>get-main-itemc',
          focus: function() {
                // prevent value inserted on focus
                return false;
              },
              select: function( event, ui ) {
                var terms = ui.item.value;
                
                jQuery('#main_itemc').val(terms);
                jQuery('#main_itemc').attr("data-id-brnch-name",ui.item.mtkn_rid);

            
                jQuery(this).autocomplete('search', jQuery.trim(terms));

                return false;

        
              }
            })
        .click(function() {
          var terms = this.value;
          jQuery(this).autocomplete('search', jQuery.trim(terms));     
    }); 

    jQuery('#uom')
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
          source: '<?= site_url(); ?>get-uom',
          focus: function() {
                // prevent value inserted on focus
                return false;
              },
              select: function( event, ui ) {
                var terms = ui.item.value;
                
                jQuery('#uom').val(terms);
                jQuery('#uom').attr("data-id-brnch-name",ui.item.mtkn_rid);

            
                jQuery(this).autocomplete('search', jQuery.trim(terms));

                return false;

        
              }
            })
        .click(function() {
          var terms = this.value;
          jQuery(this).autocomplete('search', jQuery.trim(terms));     
    }); 

    $("#mbtn_mn_Update").click(function(e){

    try { 
          //__mysys_apps.mepreloader('mepreloaderme',true);
          var main_itemc = jQuery('#main_itemc').val();
          var sub_itemc = jQuery('#sub_itemc').val();
          var sub_desc = jQuery('#sub_desc').val();
          var barcode = jQuery('#barcode').val();
          var convf = jQuery('#convf').val();
          var uom = jQuery('#uom').val();
          var cost = jQuery('#cost').val();
          var srp = jQuery('#srp').val();
          var recid = jQuery('#recid').val();

          var mparam = {
            main_itemc:main_itemc,
            sub_itemc:sub_itemc,
            sub_desc:sub_desc,
            barcode:barcode,
            convf:convf,
            uom:uom,
            cost:cost,
            srp:srp,
            recid:recid
          };   


      $.ajax({ 
        type: "POST",
        url: '<?=site_url();?>sub-items-update',
        context: document.body,
        data: eval(mparam),
        global: false,
        cache: false,
        success: function(data)  { 
            $(this).prop('disabled', false);
           // $.hideLoading();
            jQuery('#memsgtestent_bod').html(data);
            jQuery('#memsgtestent').modal('show');
            return false;
        },
        error: function() {
          alert('error loading page...');
         // $.hideLoading();
          return false;
        } 
      });

    } catch(err) {
      var mtxt = 'There was an error on this page.\n';
      mtxt += 'Error description: ' + err.message;
      mtxt += '\nClick OK to continue.';
      alert(mtxt);
    }  //end try
    return false; 

  });
</script>