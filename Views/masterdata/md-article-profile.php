<?php
/**
 *	File        : masterdata/md-article-profile.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Apr 08, 2022
 * 	last update : Apr 08, 2022
 * 	description : Migrate into new UI
 */
 
$request = \Config\Services::request();

$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$mylibzsys = model('App\Models\MyLibzSysModel');

$mylibzdb = model('App\Models\MyLibzDBModel');

$mydatum = model('App\Models\MyDatumModel');
$aprodl = $mydatum->get_prod_line();
$cuser = $mylibzdb->mysys_user();
$mpw_tkn = $mylibzdb->mpw_tkn();

$maction = $request->getVar('maction');
$mtkn_etr = $request->getVar('mtkn_etr');
$mematcode = '';
$mematdesc = '';
$mepartno = '';
$mebarcode = '';
$meproldc = '';
$meunitc = '';
$meunitp = '';
$meunitpack = '';
$meuom = '';
$megweight = '';
$meconvf = '';
$meprodt = '';
$meprodcat = '';
$meprodscat = '';
$recactive = ' checked ';
if(!empty($mtkn_etr)) { 
	$str = "select * from {$db_erp}.`mst_article` aa WHERE sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr' ";
	$q =  $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	if($q->getNumRows() > 0) { 
		//$rrec = $q->getResultArray();
		$rw = $q->getRowArray();
		//foreach($rrec as $row):
		//endforeach;
		$mematcode = $rw['ART_CODE'];
		$mematdesc = $rw['ART_DESC'];
		$mepartno = $rw['ART_PARTNO'];
		$mebarcode = $rw['ART_BARCODE1'];
		$meproldc = $rw['ART_PRODL'];
		$meunitp = $rw['ART_UPPRICE'];
		$meunitc = $rw['ART_UCOST'];
		$meprodt = $rw['ART_HIERC2'];
		$meprodcat = $rw['ART_HIERC1'];
		$meprodscat = $rw['ART_HIERC3'];
		$meunitpack = $rw['ART_SKU'];
		$meuom = $rw['ART_UOM'];
		$recactive = ($rw['ART_ISDISABLE'] == 0 ? ' checked ' : '');
		$megweight = $rw['ART_GWEIHGT'];
		$meconvf = $rw['ART_NCONVF'];
	}
	$q->freeResult();
}

?>
<?=form_open('mymd-item-materials-profile-save','class="row needs-validation" id="myfrmsrec_artm" ');?>
    <div class="col-12 col-xl-4 mt-1">
        <div class="card h-100">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Item/Material Info</h6>
            </div>
            <div class="card-body p-3">
                <label>Item/Material Code</label>
                <div class="mb-3">
                    <input type="text" name="mematcode" id="mematcode" class="form-control form-control-sm" placeholder="Item Material Code" aria-label="Item Material Code" aria-describedby="Item Material Code" value="<?=$mematcode;?>" required>
                </div>
                <label>Item/Material Description</label>
                <div class="mb-3">
                    <input type="text" name="mematdesc" id="mematdesc" class="form-control form-control-sm" placeholder="" aria-label="Item/Material Description" aria-describedby="Item-Material-Description" value="<?=$mematdesc;?>" required>
                </div>
                <label>Part Number</label>
                <div class="mb-3">
                    <input type="text" name="mepartnumber" id="mepartnumber" class="form-control form-control-sm" placeholder="" aria-label="Part Number" aria-describedby="Part-Number" value="<?=$mepartno;?>">
                </div>
                <label>Barcode</label>
                <div class="mb-3">
                    <input type="text" name="mebarcode" id="mebarcode" class="form-control form-control-sm" placeholder="" aria-label="Barcode" aria-describedby="Barcode" value="<?=$mebarcode;?>">
                </div>
              <ul class="list-group">
                <li class="list-group-item border-0 px-0">
                  <div class="form-check form-switch ps-0">
                    <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckArtRecActive" <?=$recactive;?>>
                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="flexSwitchCheckArtRecActive">Acive</label>
                  </div>
                </li>
              </ul>
                <div class="mb-3">
                    <button type="submit" class="btn btn-success btn-sm fw-bolder">Save</button>
                    <button class="btn btn-danger btn-sm fw-bolder" id="mbtn_profcancel">Cancel</button>
                    <?=anchor('mymd-item-materials/?maction=A_REC', 'New Record',' class="btn btn-info btn-sm fw-bolder" ');?>
                </div>
            </div> <!-- end card body -->
        </div> <!-- end article info -->
    </div> <!-- end col-12 -->
	<div class="col-12 col-xl-4 mt-1">
		<div class="card h-100">
			<div class="card-header pb-0 p-3">
				<h6 class="mb-0">Item/Materia Hierarchy</h6>
			</div>
			<div class="card-body p-3">
				<label>Product Line</label>
				<div class="mb-3">
					<?=$mylibzsys->mypopulist_2($aprodl,$meproldc,'meprodlc',' class="form-control form-control-sm" required ');?>
				</div>
				<label>Product Type</label>
				<div class="mb-3">
					<input type="text" name="meprodt" id="meprodt" class="form-control form-control-sm" placeholder="" aria-label="Product Type" aria-describedby="Product-Type" value="<?=$meprodt;?>" required>
				</div>
				<label>Category</label>
				<div class="mb-3">
					<input type="text" name="meprodcat" id="meprodcat" class="form-control form-control-sm" placeholder="" aria-label="Category" aria-describedby="Category" value="<?=$meprodcat;?>" required>
				</div>
				<label>Sub-Category</label>
				<div class="mb-3">
					<input type="text" name="meprodscat" id="meprodscat" class="form-control form-control-sm" placeholder="" aria-label="Sub-Category" aria-describedby="Sub-Category" value="<?=$meprodscat;?>">
				</div>
			</div> <!-- end card-body -->
		</div>
	</div> <!-- end article Hierarchy -->
	<div class="col-12 col-xl-4 mt-1">
		<div class="card">
			<div class="card-header pb-0 p-3">
				<h6 class="mb-0">Pricing/Cost</h6>
			</div>
			<div class="card-body p-3">
				<label>Unit Cost</label>
				<div class="mb-3">
					<input type="text" name="meunitc" id="meunitc" class="form-control form-control-sm" placeholder="" aria-label="Unit Cost" aria-describedby="Unit-Cost" value="<?=$meunitc;?>" required>
				</div>
				<label>Unit Price</label>
				<div class="mb-3">
					<input type="text" name="meunitp" id="meunitp" class="form-control form-control-sm" placeholder="" aria-label="Unit Price" aria-describedby="Unit-Price" value="<?=$meunitp;?>" required>
				</div>
			</div> <!-- end card-body -->
		</div> <!-- end card -->
		<div class="card mt-1">
			<div class="card-header pb-0 p-3">
				<h6 class="mb-0">Stocking</h6>
			</div>
			<div class="card-body p-3">
				<label>Packaging</label>
				<div class="mb-3">
					<input type="text" name="meunitpack" id="meunitpack" class="form-control form-control-sm" placeholder="" aria-label="Packaging" aria-describedby="Packaging" value="<?=$meunitpack;?>" required> 
				</div>
				<label>UoM</label>
				<div class="mb-3">
					<input type="text" name="meuom" id="meuom" class="form-control form-control-sm" placeholder="" aria-label="Unit of Measure" aria-describedby="Unit-Measure" value="<?=$meuom;?>">
				</div>
				<label>Gross Weight</label>
				<div class="mb-3">
					<input type="text" name="megweight" id="megweight" class="form-control form-control-sm" placeholder="" aria-label="Gross Weight" aria-describedby="Gross-Weight" value="<?=$megweight;?>">
				</div>
				<label>Conversion Factor</label>
				<div class="mb-3">
					<input type="text" name="meconvf" id="meconvf" class="form-control form-control-sm" placeholder="" aria-label="Conversion Factor" aria-describedby="Conversion-Factor" value="<?=$meconvf;?>">
				</div>
			</div> <!-- end card-body -->
		</div> <!-- end card -->
    </div> <!-- end article Pricing/Cost -->
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
					var meprodlc = jQuery('#meprodlc').val();
					if(jQuery.trim(meprodlc) == '') { 
						jQuery('#meprofsavemsg_bod').html('Product Line is required!!!');
						jQuery('#meprofsavemsg').modal('show');
						__mysys_apps.mepreloader('mepreloaderme',false);
						return false;
					}
					var mematcode = jQuery('#mematcode').val();
					var mtkn_etr = '<?=$mtkn_etr;?>';
					var mematdesc = jQuery('#mematdesc').val();
					var mepartnumber = jQuery('#mepartnumber').val();
					var mebarcode = jQuery('#mebarcode').val();
					var flexSwitchCheckArtRecActive = (jQuery('#flexSwitchCheckArtRecActive').is(':checked') ? 0 : 1);
					var meprodt = jQuery('#meprodt').val();
					var meprodcat = jQuery('#meprodcat').val();
					var meprodscat = jQuery('#meprodscat').val();
					var meunitc = jQuery('#meunitc').val();
					var meunitp = jQuery('#meunitp').val();
					var meunitpack = jQuery('#meunitpack').val();
					var meuom = jQuery('#meuom').val();
					var megweight = jQuery('#megweight').val();
					var meconvf = jQuery('#meconvf').val();
					var maction = '<?=$maction;?>';
					
					var mparam = {
						mtkn_etr: mtkn_etr,
						meprodlc: meprodlc,
						mematcode: mematcode,
						mematdesc: mematdesc,
						mepartnumber: mepartnumber,
						mebarcode: mebarcode,
						flexSwitchCheckArtRecActive: flexSwitchCheckArtRecActive,
						meprodt: meprodt,
						meprodcat: meprodcat,
						meprodscat: meprodscat,
						meunitc: meunitc,
						meunitp: meunitp,
						meunitpack: meunitpack,
						meuom: meuom,
						megweight: megweight,
						meconvf: meconvf,
						maction: maction,
					};
					
					jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>mymd-item-materials-profile-save',
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
	
	jQuery('#meprodt' ) 
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
		source: '<?=site_url(); ?>search-prod-type/',
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
	
	jQuery('#meprodcat' ) 
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
		source: '<?=site_url(); ?>search-prod-category/',
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
	
	jQuery('#meprodscat' ) 
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
		source: '<?=site_url(); ?>search-prod-sub-category/',
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
	
	jQuery('#meunitpack' ) 
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
		source: '<?=site_url(); ?>search-prod-items-packaging/',
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
	
	jQuery('#meuom' ) 
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
		source: '<?=site_url(); ?>search-prod-items-uom/',
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
			window.location.href = '<?=site_url();?>mymd-item-materials';
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
