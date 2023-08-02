<?php
?>
<div class="row mt-2 ms-1 me-1">
	<div class="col-md-4">
		
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Branch</span>
			</div>
			<div class="col-sm-9">
				<input type="text" class="form-control form-control-sm" data-id="" id="sodailymeBranch" name="sodailymeBranch" data-mtknrid="" value="" required/>
			</div>
		</div>
		
		<div class="row mb-3">
			<div class="col-sm-3">
				<span>Date From:</span>
			</div>
			<div class="col-sm-9">
				<input type="date" id="txtmedatef" name="txtmedatef" class="form-control form-control-sm" />
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-sm-3">
				<span>Date To:</span>
			</div>
			<div class="col-sm-9">
				<input type="date" id="txtmedatet" name="txtmedatet" class="form-control form-control-sm" />
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-sm-9">
				<input type="button" id="mbtn_saleso_proc_daily_tally" class="btn btn-sm btn-success" value="Process..." />
				<input type="button" id="mbtn_saleso_proc_daily_check_tally" class="btn btn-sm btn-success" value="Check Daily..." />
			</div>
		</div>
	</div> <!-- end col -->
</div> <!-- end div -->
<div class="row mt-2 ms-1 me-1" id="mywg_salesout_daily_tally_cont">
</div>
<script type="text/javascript"> 
	jQuery('#mbtn_saleso_proc_daily_tally').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtmedatef = jQuery('#txtmedatef').val();
			var txtmedatet = jQuery('#txtmedatet').val();
			var mebranch = jQuery('#sodailymeBranch').val();
			var mebranch_mtkn = jQuery('#sodailymeBranch').attr('data-mtknrid');
			var mparam = {medatef: txtmedatef,
				medatet: txtmedatet,
				mebranch: mebranch,
				mebranch_mtkn: mebranch_mtkn
				};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>sales-out-tally-daily-proc',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mywg_salesout_daily_tally_cont').html(data);
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
	});  //end mbtn_saleso_proc_daily_tally
	
	jQuery('#mbtn_saleso_proc_daily_check_tally').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtmedatef = jQuery('#txtmedatef').val();
			var txtmedatet = jQuery('#txtmedatet').val();
			var mebranch = jQuery('#sodailymeBranch').val();
			var mebranch_mtkn = jQuery('#sodailymeBranch').attr('data-mtknrid');
			var mparam = {medatef: txtmedatef,
				medatet: txtmedatet,
				mebranch: mebranch,
				mebranch_mtkn: mebranch_mtkn
				};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>sales-out-tally-daily-check-proc',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mywg_salesout_daily_tally_cont').html(data);
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
	});  //end mbtn_saleso_proc_daily_check_tally
		
	jQuery('#sodailymeBranch')
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
					jQuery('#sodailymeBranch').val(terms);
					jQuery('#sodailymeBranch').attr('data-mtknrid',mtknr_rid);
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
	}); // end sodailymeBranch
        	
</script>
