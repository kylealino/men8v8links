<?php
/**
 *	File        : masterdata/md-quota-rate-profile.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: November 13, 2022
 * 	last update : November 13, 2022
 * 	description : Quota Piece Rate Profile Entry
 */
 
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$mylibzsys = model('App\Models\MyLibzSysModel');
$cuser = $mylibzdb->mysys_user();
$mpw_tkn = $mylibzdb->mpw_tkn();
$maction = $request->getVar('maction');
$mtkn_etr = $request->getVar('mtkn_etr');
$meprodserv = '';
$meprodoper = '';
$meproddesgnp = '';
$meprodsoper = '';
$meprodproc = '';
$meqpramt = '';
$recactive = ' checked ';

if(!empty($mtkn_etr)) { 
	$str = "select * from {$db_erp}.`mst_process_rate_amnt` aa WHERE sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr' ";
	$q =  $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	if($q->getNumRows() > 0) { 
		//$rrec = $q->getResultArray();
		$rw = $q->getRowArray();
		//foreach($rrec as $row):
		//endforeach;
		$meprodserv = $rw['PRODL_SERVICES'];
		$meprodoper = $rw['PROD_OPERATION'];
		$meproddesgnp = $rw['PROD_DESGNT'];
		$recactive = ($rw['PROD_RFLAG'] == 'A' ? ' checked ' : '');
		$meprodsoper = $rw['PROD_SUB_OPERATION'];
		$meprodproc = $rw['PROD_SUB_OPERATION_PROCESS'];
		$meqpramt = $rw['PROD_SOP_RATE_AMT'];
		

	}
	$q->freeResult();
}

?>

<?=form_open('mymd-qpr-save','class="row needs-validation" id="myfrmsrec_quotarate" ');?>
    <div class="col-12 col-xl-6 mt-1">
        <div class="card h-100">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Quota Piece Rate Entry</h6>
            </div>
            <div class="card-body p-3">
                <label>Product Services</label>
                <div class="mb-3">
                    <input type="text" name="meprodserv" id="meprodserv" class="form-control form-control-sm" placeholder="" aria-label="Product Services" aria-describedby="Product-Services-addon" value="<?=$meprodserv;?>" required>
                </div>
                <label>Operation</label>
                <div class="mb-3">
                    <input type="text" name="meprodoper" id="meprodoper" class="form-control form-control-sm" placeholder="" aria-label="Operation" aria-describedby="Operation-addon" value="<?=$meprodoper;?>" required>
                </div>
                <label>Designs/Pattern</label>
                <div class="mb-3">
                    <input type="text" name="meproddesgnp" id="meproddesgnp" class="form-control form-control-sm" placeholder="" aria-label="Design/Pattern" aria-describedby="Design-Pattern-addon" value="<?=$meproddesgnp;?>" required>
                </div>
                <label>Sub-Operation</label>
                <div class="mb-3">
                    <input type="text" name="meprodsoper" id="meprodsoper" class="form-control form-control-sm" placeholder="" aria-label="Sub-Operation" aria-describedby="Sub-Operation-addon" value="<?=$meprodsoper;?>" required>
                </div>
                <label>Process</label>
                <div class="mb-3">
                    <input type="text" name="meprodproc" id="meprodproc" class="form-control form-control-sm" placeholder="" aria-label="Process" aria-describedby="Process-addon" value="<?=$meprodproc;?>" required>
                </div>
                <label>Rate Amount</label>
                <div class="mb-3">
                    <input type="text" name="meqpramt" id="meqpramt" class="form-control form-control-sm" placeholder="" aria-label="Rate Amount" aria-describedby="Rate-Amount-addon" value="<?=$meqpramt;?>" required>
                </div>
              <ul class="list-group">
                <li class="list-group-item border-0 px-0">
                  <div class="form-check form-switch ps-0">
                    <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckrflag" <?=$recactive;?>>
                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="flexSwitchCheckrflag">Acive</label>
                  </div>
                </li>
              </ul>
                <div class="mb-3">
                    <button type="submit" class="btn btn-success btn-sm fw-bolder" title="Save Record..." alt="Save Record...">Save</button>
                    <button class="btn btn-danger btn-sm fw-bolder" id="mbtn_profcancel" title="Cancel and Closed" alt="Cancel and Closed">Cancel</button>
                    <?=anchor('mymd-quota-rate/?maction=A_REC', 'New Record',' class="btn btn-info btn-sm fw-bolder" alt="New Record" title="New Record" ');?>
                </div>
            </div> <!-- end card body -->
        </div> <!-- end article info -->
    </div> <!-- end col-12 --> 
<?=form_close();
echo $mylibzsys->memsgbox1('meprofsavemsg','System Message','...');
echo $mylibzsys->memsgbox_yesno1('meprofcancmsg','Closed and Cancel Material Profile Changes','Cancel changes made?');
?> <!-- end of ./form -->
<script type="text/javascript"> 
	
	
	(function () {
		'use strict'
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.querySelectorAll('.needs-validation')
		// Loop over them and prevent submission
		Array.prototype.slice.call(forms)
		.forEach(function (form) {
			form.addEventListener('submit', function (event) {
				if (!form.checkValidity()) {
					event.preventDefault()
					event.stopPropagation()
				}
				form.classList.add('was-validated');
				try {
					event.preventDefault();
          			event.stopPropagation();
					__mysys_apps.mepreloader('mepreloaderme',true);
					var meprodserv = jQuery('#meprodserv').val();
					var meprodoper = jQuery('#meprodoper').val();
					var meproddesgnp = jQuery('#meproddesgnp').val();
					var mtkn_etr = '<?=$mtkn_etr;?>';
					var merecflag = (jQuery('#flexSwitchCheckrflag').is(':checked') ? 'A' : 'N');
					var maction = '<?=$maction;?>';
					var meprodsoper = jQuery('#meprodsoper').val();
					var meprodproc = jQuery('#meprodproc').val();
					var meqpramt = jQuery('#meqpramt').val();
					var mparam = {
						mtkn_etr: mtkn_etr,
						meprodserv: meprodserv,
						meprodoper: meprodoper,
						merecflag: merecflag,
						meproddesgnp: meproddesgnp,
						maction: maction,
						meprodsoper: meprodsoper,
						meprodproc: meprodproc,
						meqpramt: meqpramt,
					};
					
					jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>mymd-qpr-save',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
						success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#meprofsavemsg_bod').html(data);
								jQuery('#meprofsavemsg').modal('show');
								
								return false;
						},
						error: function() { // display global error on the menu function 
							__mysys_apps.mepreloader('mepreloaderme',false);
							alert('error loading page...');
							return false;
						}	
					});	
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
	
	
	jQuery('#meprodserv' ) 
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
		source: '<?=site_url(); ?>search-mymd-qpr-prod-services/',
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
		jQuery(this).autocomplete('search', this.value);
	});			
		
		
	jQuery('#meprodoper' ) 
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
		source: '<?=site_url(); ?>search-mymd-qpr-prod-operation/',
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
		jQuery(this).autocomplete('search', this.value);
	});					
		
	jQuery('#meproddesgnp' ) 
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
		source: '<?=site_url(); ?>search-mymd-qpr-prod-design-pattern/',
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
		jQuery(this).autocomplete('search', this.value);
	});							
		
		
	jQuery('#meprodsoper' ) 
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
		source: '<?=site_url(); ?>search-mymd-qpr-prod-sub-operation/',
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
		jQuery(this).autocomplete('search', this.value);
	});							
	
			
	jQuery('#mbtn_profcancel').click(function() { 
		try { 
			jQuery('#meprofcancmsg').modal('show');
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
		
	});	
	
	
	jQuery('#meprodproc' ) 
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
		source: '<?=site_url(); ?>search-mymd-qpr-prod-processes/',
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
		jQuery(this).autocomplete('search', this.value);
	});							
	
			
	jQuery('#mbtn_profcancel').click(function() { 
		try { 
			jQuery('#meprofcancmsg').modal('show');
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
		
	});		
	
	jQuery('#meprofcancmsg_yes').click(function() { 
		try { 
			window.location.href = '<?=site_url();?>mymd-quota-rate';
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
		
	});			
</script>
