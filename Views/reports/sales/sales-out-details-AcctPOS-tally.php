<?php
?>
<div class="row mt-2 ms-1 me-1">
	<div class="col-md-4">		
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Branch</span>
			</div>
			<div class="col-sm-9">
				<input type="text" class="form-control form-control-sm" data-id="" id="AcctPOSTallymeBranch" name="AcctPOSTallymeBranch" data-mtknrid="" value="" required/>
			</div>
		</div>
		
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Date From:</span>
			</div>
			<div class="col-sm-9">
				<input type="date" id="AcctPOSTallymedatef" name="AcctPOSTallymedatef" class="form-control form-control-sm" />
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Date To:</span>
			</div>
			<div class="col-sm-9">
				<input type="date" id="AcctPOSTallymedatet" name="AcctPOSTallymedatet" class="form-control form-control-sm" />
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-sm-9">
				<input type="button" id="mbtn_saleso_AcctPOS_tally_proc" class="btn btn-sm btn-success" value="Process..." />
				<input type="button" id="mbtn_saleso_AcctPOSTAXR_tally_proc" class="btn btn-sm btn-success" value="Process TAXR..." />
				<?=anchor('sales-out-details', 'Reset',' class="btn btn-warning btn-sm" ');?>  
			</div>
		</div>
	</div> <!-- end col -->
</div> <!-- end div -->
<div class="row mt-2 ms-1 me-1" id="mywg_salesout_AcctPOS_tally_cont">
</div>
<script type="text/javascript"> 
	jQuery('#mbtn_saleso_AcctPOS_tally_proc').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtmedatef = jQuery('#AcctPOSTallymedatef').val();
			var txtmedatet = jQuery('#AcctPOSTallymedatet').val();
			var mebranch = jQuery('#AcctPOSTallymeBranch').val();
			var mebranch_mtkn = jQuery('#AcctPOSTallymeBranch').attr('data-mtknrid');
			var mparam = { medatef: txtmedatef,
				medatet: txtmedatet,
				mebranch: mebranch,
				mebranch_mtkn: mebranch_mtkn
			};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>sales-out-Acct-POS-tally-proc',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mywg_salesout_AcctPOS_tally_cont').html(data);
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
	
	jQuery('#mbtn_saleso_AcctPOSTAXR_tally_proc').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtmedatef = jQuery('#AcctPOSTallymedatef').val();
			var txtmedatet = jQuery('#AcctPOSTallymedatet').val();
			var mebranch = jQuery('#AcctPOSTallymeBranch').val();
			var mebranch_mtkn = jQuery('#AcctPOSTallymeBranch').attr('data-mtknrid');
			var mparam = {medatef: txtmedatef,
				medatet: txtmedatet,
				mebranch: mebranch,
				mebranch_mtkn: mebranch_mtkn
			};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>sales-out-Acct-POS-TAXR-proc',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mywg_salesout_AcctPOS_tally_cont').html(data);
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
	
	jQuery('#AcctPOSTallymeBranch')
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
					jQuery(this).autocomplete('option', 'source', '<?=site_url();?>company-branch-ua'); 
				},
				select: function( event, ui ) {
					var terms = ui.item.value;
					var mtkn_comp = ui.item.mtkn_comp;
					var mtknr_rid = ui.item.mtknr_rid;
					var mtkn_brnch = ui.item.mtkn_brnch;
					jQuery('#AcctPOSTallymeBranch').val(terms);
					jQuery('#AcctPOSTallymeBranch').attr('data-mtknrid',mtknr_rid);
					jQuery(this).autocomplete('search', jQuery.trim(terms));
					return false;
				}
			})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	}); // end AcctPOSTallymeBranch
		
</script>
