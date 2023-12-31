<?php
/**
 *	File        : masterdata/sub_masterdata_inv/sub-md-item-inv-main.php
 *  Auhtor      : Kyle Alino
 *  Date Created: Jul 28, 2023
 * 	last update : Jul 28, 2023
 * 	description : Sub Masterdata Main
 */

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$cuser = $myusermod->mysys_user();
$active_branch = '';
$str="
    SELECT 
        a.`myusername`,
        a.`myuabranch`,
        b.`BRNCH_NAME`
    FROM
        myua_branch a
    JOIN
        mst_companyBranch b
    ON
        a.`myuabranch` = b.`recid`
    WHERE
        a.`myusername` = '$cuser'
";
$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
$rw = $q->getRowArray();
$active_branch = $rw['BRNCH_NAME'];

?>
<main id="main">
    <div class="pagetitle">
    <h1>Convertion</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Convertion</li>
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
                        <div class="col-lg-3 mb-3">
                            <div class="col-sm-12">
                                <h6 class="card-title p-0">Select branch:</h6>
                                <div class="input-group input-group-sm ">
                                    <input type="text"  placeholder="Branch Name" id="branch_name" name="branch_name" class="branch_name form-control form-control-sm " required autocomplete="off" aria-describedby="basic-addon1" value="<?=$active_branch;?>" readonly/>
                                    <input type="hidden"  placeholder="Branch Name" id="branch_code" name="branch_code" class="branch_code form-control form-control-sm " required/>
                                    <div class="input-group-prepend" id="basic-addon1">
                                        <button type="button" id="mbtn_vw_recs" name="mbtn_vw_recs" class="btn btn-primary btn-sm m-0 rounded-0 rounded-end mbtn_vw_recs" ><i class="bi bi-search"></i> View</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="row gy-2 mb-3">
                        <div id="salesrecs">
                            <div class="text-center p-2 rounded-3  mt-2 border-dotted bg-light col-lg-12  p-4">
                                <h5><i class="bi bi-info-circle-fill text-dgreen"></i> Sales record will display here.</h5> 
                            </div>
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
                    <h3 class="h4 mb-0"> <i class="bi bi-pencil-square"></i> Current Inventory Records</h3>
                </div>
                <div class="card-body">
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

<script>
$(document).ready(function(){
    sub_items_view_convf();
});

function sub_items_view_convf(){ 
    var ajaxRequest;

    ajaxRequest = jQuery.ajax({
        url: "<?=site_url();?>cs-sub-convf-cur-recs",
        type: "post"
    });

    __mysys_apps.mepreloader('mepreloaderme',true);
      ajaxRequest.done(function(response, textStatus, jqXHR) {
          jQuery('#subitems').html(response);
          __mysys_apps.mepreloader('mepreloaderme',false);
      });
  };

    jQuery('#branch_name')
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
			source: '<?= site_url(); ?>get-branch/',
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) {
				var sValue = jQuery(oEvent.target).val();

			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				jQuery('#branch_name').val(terms);
				jQuery(this).autocomplete('search', jQuery.trim(terms));
				return false;
			}
		})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	});	//end branch_name


    $("#mbtn_vw_recs").click(function(e){
    try { 

          var branch_name = jQuery('#branch_name').val();

          var mparam = {
            branch_name:branch_name,
          };  


      $.ajax({ 
        type: "POST",
        url: '<?=site_url();?>cs-sub-convf-recs',
        context: document.body,
        data: eval(mparam),
        global: false,
        cache: false,
        success: function(data)  { //display html using divID
            __mysys_apps.mepreloader('mepreloaderme',false);
            jQuery('#salesrecs').html(data);
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

    // $("#branch_name").change(function() {
    //     sub_items_view();
    // });

    // $('#anchor-list').on('click',function(){
    // $('#anchor-list').addClass('active');
    // $('#anchor-items').removeClass('active');
    // var mtkn_whse = '';
    // sub_items_view();

    // });

    // function sub_items_view(){ 
    //     var ajaxRequest;

    //     ajaxRequest = jQuery.ajax({
    //         url: "<?=site_url();?>cs-sub-convf-recs",
    //         type: "post"
    //     });

    //     __mysys_apps.mepreloader('mepreloaderme',true);
    //     ajaxRequest.done(function(response, textStatus, jqXHR) {
    //         jQuery('#salesrecs').html(response);
    //         __mysys_apps.mepreloader('mepreloaderme',false);
    //     });
    // };
</script>