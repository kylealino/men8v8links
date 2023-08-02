<?php 
/**
 *	File        : masterdata/md-quota-rate-recs.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Nov 11, 2022
 * 	last update : Nov 11, 2022
 * 	description : Quota Piece Rate Records
 */
 
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');

$cuser = $mylibzdb->mysys_user();
$mpw_tkn = $mylibzdb->mpw_tkn();

$mytxtsearchrec = $request->getVar('txtsearchedrec');

$_meprodserv = $request->getVar('_meprodserv');
$_meprodoper = $request->getVar('_meprodoper');
$_meproddesgnp = $request->getVar('_meproddesgnp');
$_meprodsoper = $request->getVar('_meprodsoper');

$data = array();
$mpages = (empty($mylibzsys->oa_nospchar($request->getVar('mpages'))) ? 0 : $mylibzsys->oa_nospchar($request->getVar('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}

?>

<div class="row ms-1">
	<?=form_open('md-quota-rate','class="row g-3 for-validation" id="myfrmsrec_artm" ');?>
		<div class="input-group input-group-sm">
			<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
			<input type="text" class="form-control form-control-sm" name="mytxtsearchrec" placeholder="Search Customer Code/Name" id="mytxtsearchrec" aria-label="Input group Customer" aria-describedby="mebtnGroupAddon" placeholder="Search Customer Code/Namee" value="<?=$mytxtsearchrec;?>" required/>
			<div class="invalid-feedback">Please fill out this field.</div>
			<button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
			<?=anchor('mymd-quota-rate', 'Reset',' class="btn btn-primary" ');?>
		</div>
	<?=form_close();?> <!-- end of ./form -->
</div>
<div class="row ms-1 mt-1">
    <div class="col-12 col-xl-6 mt-1">
        <div class="card">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Quota Piece Rate Custom Search</h6>
            </div>
            <div class="card-body p-3">
                <label>Product Services</label>
                <div class="mb-3">
                    <input type="text" name="_meprodserv" id="_meprodserv" class="form-control form-control-sm meprodserv" placeholder="" aria-label="Product Services" aria-describedby="Product-Services-addon" value="<?=$_meprodserv;?>">
                </div>
                <label>Operation</label>
                <div class="mb-3">
                    <input type="text" name="_meprodoper" id="_meprodoper" class="form-control form-control-sm meprodoper" placeholder="" aria-label="Operation" aria-describedby="Operation-addon" value="<?=$_meprodoper;?>">
                </div>
                <label>Designs/Pattern</label>
                <div class="mb-3">
                    <input type="text" name="_meproddesgnp" id="_meproddesgnp" class="form-control form-control-sm meproddesgnp" placeholder="" aria-label="Design/Pattern" aria-describedby="Design-Pattern-addon" value="<?=$_meproddesgnp;?>">
                </div>
                <label>Sub-Operation</label>
                <div class="mb-3">
                    <input type="text" name="_meprodsoper" id="_meprodsoper" class="form-control form-control-sm meprodsoper" placeholder="" aria-label="Sub-Operation" aria-describedby="Sub-Operation-addon" value="<?=$_meprodsoper;?>">
                </div>
            </div> <!-- end card body -->
        </div> <!-- end article info -->
    </div> <!-- end col-12 --> 	
</div>
<div class="row ms-1 mt-1">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
</div>	
	<!-- <div class="box box-primary"> -->
		<!-- <div class="box-body"> -->
			<div class="table-responsive">
					<table class="metblentry-font table-bordered" id="metblarticle">
						<thead>
							<tr>
								<th class="text-center">
									<?=anchor('mymd-quota-rate/?maction=A_REC', '<i class="bi bi-plus-lg"></i>',' class="btn btn-success btn-sm p-1 pb-0 mebtnpt1" ');?>
								</th>
								<th>Services</th>
								<th nowrap>Operation</th>
								<th nowrap>Designs</th>
								<th nowrap>Sub-Operation</th>
								<th nowrap>Processes</th>
								<th nowrap>Piece Rate</th>
								<th>User</th>
								<th>Date Created</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if($rlist !== ''):
								$nn = 1;
								foreach($rlist as $row): 
									
									// $bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
									$on_mouse = " onmouseover=\"this.style.backgroundColor='#5cb85cb8';\" onmouseout=\"this.style.backgroundColor='';\"";	
									$mtkn_arttr = hash('sha384', $row['recid'] . $mpw_tkn);
								?>
								<tr <?=$on_mouse;?>>
									<td class="text-center" nowrap>
										<?=anchor('mymd-quota-rate/?mtkn_etr=' . $mtkn_arttr, '<i class="bi bi-pencil-square"></i>',' class="btn btn-primary btn-sm p-1 pb-0 mebtnpt1" ');?>
									</td>
									<td nowrap><?=$row['PRODL_SERVICES'];?></td>
									<td nowrap><?=$row['PROD_OPERATION'];?></td>
									<td nowrap><?=$row['PROD_DESGNT'];?></td>
									<td nowrap><?=$row['PROD_SUB_OPERATION'];?></td>
									<td nowrap><?=$row['PROD_SUB_OPERATION_PROCESS'];?></td>
									<td nowrap><?=$row['PROD_SOP_RATE_AMT'];?></td>
									<td nowrap><?=$row['MUSER'];?></td>
									<td nowrap><?=$row['ENCD'];?></td>
								</tr>
								<?php 
								$nn++;
								endforeach;
							else:
								?>
								<tr>
									<td colspan="9">No data was found.</td>
								</tr>
							<?php 
							endif; ?>
						</tbody>
					</table>
			</div>
		<!-- </div>  -->
		<!-- end box body -->
	<!-- </div>  -->
	<!-- end box --> 

	<div class="row" style="padding: 10px 10px 0px 15px !important;">
		<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
  </div>	

<script type="text/javascript"> 

	(function () {
		'use strict'

		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.querySelectorAll('.for-validation')
		// Loop over them and prevent submission
		Array.prototype.slice.call(forms)
		.forEach(function (form) {
			form.addEventListener('submit', function (event) {
				if (!form.checkValidity()) {
					event.preventDefault()
					event.stopPropagation()
				}
				form.classList.add('was-validated') 

				try {
					event.preventDefault();
          			event.stopPropagation();
					//jQuery('html,body').scrollTop(0);
					//jQuery.showLoading({name: 'line-pulse', allowHide: false });
					var txtsearchedrec = jQuery('#mytxtsearchrec').val();
					if(jQuery.trim(txtsearchedrec) == '') {
						return false;
					}
					__mysys_apps.mepreloader('mepreloaderme',true);
					
					var _meprodserv = jQuery('#_meprodserv').val();
					var _meprodoper = jQuery('#_meprodoper').val();
					var _meproddesgnp = jQuery('#_meproddesgnp').val();
					var _meprodsoper = jQuery('#_meprodsoper').val();
					
					var mparam = {
						txtsearchedrec: txtsearchedrec,
						_meprodserv: _meprodserv,
						_meprodoper: _meprodoper,
						_meproddesgnp: _meproddesgnp,
						_meprodsoper: _meprodsoper,
						mpages: 1 
					};	
					jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>search-mymd-quota-rate',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
						success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#qprlist').html(data);
								
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


    function meSetCellPadding () {
        var metable = document.getElementById ("metblarticle");
        metable.cellPadding = 5;
        metable.style.border = "1px solid #F6F5F4";
        var tabletd = metable.getElementsByTagName("td");
        //for(var i=0; i<tabletd.length; i++) {
        //    var td = tabletd[i];
        //    td.style.borderColor ="#F6F5F4";
        //}

    }
    meSetCellPadding();

	function __myredirected_rsearch(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			var _meprodserv = jQuery('#_meprodserv').val();
			var _meprodoper = jQuery('#_meprodoper').val();
			var _meproddesgnp = jQuery('#_meproddesgnp').val();
			var _meprodsoper = jQuery('#_meprodsoper').val();
			
			var mparam = {
				_meprodserv: _meprodserv,
				_meprodoper: _meprodoper,
				_meproddesgnp: _meproddesgnp,
				_meprodsoper: _meprodsoper,
				txtsearchedrec: txtsearchedrec,
				mpages: mobj 
			};	
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url()?>search-mymd-quota-rate',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#qprlist').html(data);
						
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
	}	
	
	
	jQuery('#mytxtsearchrec').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				var txtsearchedrec = jQuery('#mytxtsearchrec').val();
				if(jQuery.trim(txtsearchedrec) == '') {
					return false;
				}

				__mysys_apps.mepreloader('mepreloaderme',true);
				var mparam = {
					txtsearchedrec: txtsearchedrec,
					mpages: 1 
				};	
				jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>search-mymd-quota-rate',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							__mysys_apps.mepreloader('mepreloaderme',false);
							jQuery('#qprlist').html(data);
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
			
		}
	});
	
	jQuery('.meprodserv' ) 
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
		
		
	jQuery('.meprodoper' ) 
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
		
	jQuery('.meproddesgnp' ) 
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
		
		
	jQuery('.meprodsoper' ) 
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
	


</script>
