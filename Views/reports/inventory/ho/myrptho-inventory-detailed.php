<?php
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myivty = model('App\Models\MyInventoryModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$txtyears = '';
$txtmonths = '';
$months = $myivty->lk_Active_Months($db_erp);
$years = $myivty->lk_Active_Year($db_erp);

$afilter = array();
$afilter[] = "M_CURRxOxCurrent";
$afilter[] = "M_PREVxOxPrevious";
$txtmefilter = '';
?>
<div class="row mt-3 ms-1 me-1">
    <div class="col-md-8">
		<div class="row mb-3">
			<div class="col-sm-12">
				<div class="alert alert-info" role="alert">
					<h4 class="alert-heading">Friendly Reminder:</h4>
					<hr>
					<p class="mb-0">This report generation only covered June 2023 onwards as start of data migration on the new Inventory module temporarily!!!</p>
				</div> 
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Branch</span>
			</div>
			<div class="col-sm-9">
				<input type="text" class="form-control form-control-sm" data-id="" id="fld_sc2branch_s" name="fld_sc2branch_s" value="" required data-mtknattr="">
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Filter:</span>
			</div>
			<div class="col-sm-9">
				<?=$mylibzsys->mypopulist_2($afilter,$txtmefilter,'txtmefilter','class="form-control form-control-sm" ','','');?>
			</div>
		</div> <!-- end Year -->
		<div class="row mb-3" id="me_style_yr">
			<div class="col-sm-3">
				<span class="fw-bold">Year:</span>
			</div>
			<div class="col-sm-9">
				<?=$mylibzsys->mypopulist_2($years,$txtyears,'fld_years','class="form-control form-control-sm" ','','');?>
			</div>
		</div> <!-- end Year -->
		<div class="row mb-3" id="me_style_mo">
			<div class="col-sm-3">
				<span class="fw-bold">Month:</span>
			</div>
			<div class="col-sm-9">
				<?=$mylibzsys->mypopulist_2($months,$txtmonths,'fld_months','class="form-control form-control-sm" ','','');?>
			</div>
		</div> <!-- end Month -->
		<div class="row mb-3" id="me_style_dt">
			<div class="col-sm-3">
				<span class="fw-bold">Date:</span>
			</div>
			<div class="col-sm-9">
				<input type="date" class="form-control form-control-sm" id="me_dater" name="me_dater" value="" >
			</div>
		</div> <!-- end Date -->
		<div class="row mb-3">
			<div class="col-sm-6">
				<div class="input-group input-group-sm">
					<button class="btn btn-success btn-sm" id="btn_stockcard_gen" name="btn_stockcard_gen" type="submit">Generate...</button>
					<?=anchor('myinventory-report', 'Reset',' class="btn btn-info btn-sm" ');?>
				</div>
			</div>
		</div>
    </div> <!-- end col-8 -->    
</div> <!-- end row mt-1 ms-1 me-1 -->
<div class="row mt-2 ms-1 me-1">
	<div class="col-md-12">
		<div class="card" id="wgstockcard">
		</div>
	</div>
</div>  <!-- end row mt-1 ms-1 me-1 2nd -->
<script type="text/javascript"> 
	me_show_options(false);
	function me_show_options(lshow) { 
		if(!lshow) { 
			jQuery('#me_style_yr').hide();
			jQuery('#me_style_mo').hide();
			jQuery('#me_style_dt').hide();
		} else { 
			jQuery('#me_style_yr').show();
			jQuery('#me_style_mo').show();
			jQuery('#me_style_dt').show();
		}
	}  // end me_show_options
	
	jQuery('#txtmefilter').change(function() { 
		var meval = jQuery(this).val();
		if(meval == 'M_PREV') { 
			me_show_options(true);
		} else { 
			me_show_options(false);
		}
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
					jQuery(this).attr('data-mtknattr',mtknr_rid);
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
	
	jQuery('#btn_stockcard_gen').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var bid_mtknattr = jQuery('#fld_sc2branch_s').attr('data-mtknattr');
			
			//jQuery('#dl_submit_btn_stinqbr').css({display:'none'});
			
			var mevalfilter = jQuery('#txtmefilter').val();
			
			jQuery('#btn_stockcard_gen').hide();
			var fld_branch = jQuery('#fld_sc2branch_s').val();
			var fld_years = jQuery('#fld_years').val();
			var fld_months = jQuery('#fld_months').val();
			var mparam = {
				bid_mtknattr:bid_mtknattr,
				fld_branch: fld_branch,
				ltodate: 1,
				mpages: 1 
		   }
		   
			if( mevalfilter == 'M_PREV') { 
				var me_dater = jQuery('#me_dater').val();
				mparam = {
					bid_mtknattr:bid_mtknattr,
					fld_branch: fld_branch,
					fld_years: fld_years,
					fld_months: fld_months,
					ltodate: 1,
					mdateinq: me_dater,
					mevalfilter: mevalfilter,
					mpages: 1 
				}
			}

			
			jQuery.ajax({ // default declaration of ajax parameters
				url: '<?=site_url()?>myinventory-report-detailed-gen',
				method:"POST",
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wgstockcard').html(data);
					return false;
				},
				error: function() { // display global error on the menu function
					alert('error loading page...');
					__mysys_apps.mepreloader('mepreloaderme',false);
					return false;
				}   
			}); 
			
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}  //end try 
	});  //end btn_stockcard_gen

	jQuery('#btn_stockcard_dl').click(function() { 
		try { 
			document.getElementsByName('mdl_me_branch').value = jQuery('#fld_sc2branch_s').val();
			var datametkntmp = jQuery('#mytxtsearchrec_ivty_gen_detl_recs').attr('data-metkntmp');
			if(datametkntmp == 'undefined') { 
				datametkntmp = '';
			}
			jQuery('#mdl_me_branch').val(jQuery('#fld_sc2branch_s').val());
			jQuery('#mdl_me_branch_mtkn').val(jQuery('#fld_sc2branch_s').attr('data-mtknattr'));
			jQuery('#mdl_metkntmp').val(datametkntmp);
			jQuery('#btn_stockcard_dl').hide();
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}  //end try
		
	}); //end btn_stockcard_dl
	
	(function () {
		'use strict'
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.querySelectorAll('#myfrm_ivty_dloadxxx')
		// Loop over them and prevent submission
		Array.prototype.slice.call(forms)
		.forEach(function (form) {
			form.addEventListener('submit', function (event) {
				try {
          			var mevalfilter = jQuery('#txtmefilter').val();

					var datametkntmp = '';
					
          			if( mevalfilter == 'M_PREV') { 
						datametkntmp = jQuery('#mytxtsearchrec_ivty_gen_detl_recs').attr('data-metkntmp');
						if(datametkntmp == 'undefined') { 
							event.preventDefault();
							event.stopPropagation();
							alert('dd');
							datametkntmp = '';
						}
					}
					
					document.getElementsByName('mdl_me_branch').value = jQuery('#fld_sc2branch_s').val();
					jQuery('#mdl_me_branch').val(jQuery('#fld_sc2branch_s').val());
					jQuery('#mdl_me_branch_mtkn').val(jQuery('#fld_sc2branch_s').attr('data-mtknattr'));
					jQuery('#mdl_metkntmp').val(datametkntmp);
					jQuery('#btn_stockcard_dl').hide();
			          			
				} catch(err) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					var mtxt = 'There was an error on this page.\n';
					mtxt += 'Error description: ' + err.message;
					mtxt += '\nClick OK to continue.';
					alert(mtxt);
					return false;
				}  //end try					
			}, false)
		})
	})(); 						
</script>
