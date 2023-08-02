<?php 

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$mytrxfgpack = model('App\Models\MyPromoDiscountModel');
$myusermod = model('App\Models\MyUserModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$mytxtsearchrec = $request->getVar('txtsearchedrec');
$fromspromo =$request->getVar('fromspromo');
$tospromo = $request->getVar('tospromo');
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
<style>
	table.memetable, th.memetable, td.memetable {
		border: 1px solid #F6F5F4;
		border-collapse: collapse;
	}
	thead.memetable, th.memetable, td.memetable {
		padding: 6px;
	}
</style>
<?=form_open('me-fg-packing-recs','class="needs-validation-search" id="myfrmsearchrec" ');?>
<div class="col-md-6 mb-3">
	<div class="input-group input-group-sm">
		<label class="input-group-text fw-bold" for="search">Search:</label>
		<input type="text" id="mytxtsearchrec" class="form-control form-control-sm" name="mytxtsearchrec" value="<?=$mytxtsearchrec;?>" placeholder="Item Code" />
		<button type="submit" class="btn btn-dgreen btn-sm" style="background-color:#167F92; color:#fff;"><i class="bi bi-search"></i></button>
	</div>
</div>

<div class="row mb-3">
    <div class="col-sm-2 d-flex align-items-center pr-0">
        <span>Search From:</span>
		<input class="form-control form-control-sm mitemcode2 " name="fromspromo" id="fromspromo"  type="text" value="<?=$fromspromo;?>"placeholder="SpromoCodeXX" value="" >
    </div>
	<div class="col-sm-2 d-flex align-items-center pr-0">
        <span>To:&nbsp;&nbsp;</span>
		<input class="form-control form-control-sm mitemcode2 " name="tospromo" id="tospromo"  type="text" value="<?=$tospromo;?>" placeholder="SpromoCodeXX" value="" >
    </div>
</div>
<?=form_close();?> <!-- end of ./form -->


<div class="col-md-8">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
</div>

<!-- START TABLE HEADER -->

<div class="table-responsive">
	<div class="col-md-12 col-md-12 col-md-12">
		<table class="table table-condensed table-hover table-bordered table-sm">
			<thead>
				<tr>
					<th class="text-center">	
					</th>
					<th>Spromo No.</th>
					<th>Spromo Code</th>
					<th>Branch Code</th>
                    <th>Item code</th>
					<th>Quantity Sold</th>
					<th>Quantity Remaining</th>
					<th>Total QTY</th>
					<th>Download</th>
				</tr>
			</thead>
			<!-- END TABLE HEADER	 -->
			<!-- START TABLE DETAILS VALUE -->
			<tbody>
				<!-- RETRIEVE RLIST DATA FROM VIEWING MODEL -->

				<?php 
				if($rlist !== ''):
					$nn = 1;
					foreach($rlist as $row): 
						
						$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
						$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
						
						$spqd_trx_no = ($row['spqd_trx_no']);
						
						
						?>
						<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
							<td class="text-center" nowrap>
							<?=anchor('mepromo-spqd-view/?spqd_trxnodashb=' . $spqd_trx_no, '<i class="bi bi bi-pencil" style="background-color:#167F92; color:#fff; padding: 3px 5px 3px;; border-radius: 5px;"></i>',' class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm"');?>
							</td>
							<td nowrap><?=$row['spqd_trx_no'];?></td>
							<td nowrap><?=$row['promo_code_spqd'];?></td>
							<td nowrap><?=$row['branch_code'];?></td>
							<td nowrap><?=$row['item_code'];?></td>
                            <td nowrap><?=$row['qty_spqd'];?></td>
							<td nowrap><?=$row['qty_spqd'];?></td>
							<td nowrap><?=$row['qty_spqd'];?></td>
							<td>
								<button class="btn btn-success btn-sm" onclick="javascript:__mbtn_promo_bdownload('<?=$row['spqd_trx_no'];?>');" > <i class="bi bi-print"></i> Download</button>
							</td>
						</tr>
						<?php 
						$nn++;
					endforeach;//end of foreach
					else:
					?>
					<tr>
						<td colspan="18">No data was found.</td>
					</tr>
				<?php endif; ?>
			</tbody>
			
			<!-- END DETAILS VALUE -->
		</table>
	</div> 
</div> 

<script type="text/javascript"> 

	__my_item_lookup2()

	function __myredirected_rsearch(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			var fromspromo = jQuery('#fromspromo').val();
			var tospromo = jQuery('#tomspromo').val();
			


            //mytrx_sc/mndt_sc2_recs
            var mparam = { 
            	txtsearchedrec: txtsearchedrec,
				fromspromo:fromspromo,
				tospromo:tospromo,
            	mpages: mobj 
            };	
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>me-spqd-dashboard-view',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					$('#packlist').html(data);
					
					return false;
				},
				error: function() { // display global error on the menu function
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					
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
	}	
	
	jQuery('#mytxtsearchrec').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				__mysys_apps.mepreloader('mepreloaderme',true);
				var txtsearchedrec = jQuery('#mytxtsearchrec').val();
				var fromspromo = jQuery('#fromspromo').val();
				var tospromo = jQuery('#tomspromo').val();

				var mparam = {
					txtsearchedrec: txtsearchedrec,
					fromspromo:fromspromo,
					tospromo:tospromo,
					mpages: 1 
				};	

				jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>me-spqd-dashboard-view',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
					success: function(data)  { //display html using divID
						jQuery('#packlist').html(data);
						__mysys_apps.mepreloader('mepreloaderme',false);
						return false;
					},
					error: function() { // display global error on the menu function
						__mysys_apps.mepreloader('mepreloaderme',false);
						alert('error loading page...');
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
			
		}
	});	
	

	(function () {
		'use strict'

		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.querySelectorAll('.needs-validation-search')
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


					//start here
					try { 
						__mysys_apps.mepreloader('mepreloaderme',true);
						var txtsearchedrec = jQuery('#mytxtsearchrec').val();
						var fromspromo = jQuery('#fromspromo').val();
						var tospromo = jQuery('#tospromo').val();

						var mparam = {
							txtsearchedrec: txtsearchedrec,
							fromspromo:fromspromo,
							tospromo:tospromo,
							mpages: 1 
						};	
						
						jQuery.ajax({ // default declaration of ajax parameters
							type: "POST",
							url: '<?=site_url();?>me-spqd-dashboard-view',
							context: document.body,
							data: eval(mparam),
							global: false,
							cache: false,
							success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#packlist').html(data);
								
							},
							error: function() { // display global error on the menu function
								__mysys_apps.mepreloader('mepreloaderme',false);
								alert('error loading page...');
								
							}	
						});			
						
					} catch(err) { 
						__mysys_apps.mepreloader('mepreloaderme',false);
						var mtxt = 'There was an error on this page.\n';
						mtxt += 'Error description: ' + err.message;
						mtxt += '\nClick OK to continue.';
						alert(mtxt);
					}  //end try

					//end here



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
	function fg_pack_bcode_gen(mtkn_fgpacktr){
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			
			var mparam = {
				mtkn_fgpacktr: mtkn_fgpacktr

			}; 
                   //console.log(mparam);
                  jQuery.ajax({ // default declaration of ajax parameters
                  	type: "POST",
                  	url: '<?=site_url();?>me-fg-packing-bar-generate',
                  	context: document.body,
                  	data: eval(mparam),
                  	global: false,
                  	cache: false,

                    success: function(data)  { //display html using divID
                    	__mysys_apps.mepreloader('mepreloaderme',false);
                    	jQuery('#memsgtestent_bod').html(data);
                    	jQuery('#memsgtestent').modal('show');
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
              	alert(mtxt);
              	__mysys_apps.mepreloader('mepreloaderme',false);
              	return false;
        }  //end try            
    }
    function __mbtn_promo_bdownload(promo_trxno){
    	try { 
    		__mysys_apps.mepreloader('mepreloaderme',true);
    		
    		var mparam = {
    			promo_trxno: promo_trxno

    		}; 
                   //console.log(mparam);
                  jQuery.ajax({ // default declaration of ajax parameters
                  	type: "POST",
                  	url: '<?=site_url();?>me-promo-barcode-dl',
                  	context: document.body,
                  	data: eval(mparam),
                  	global: false,
                  	cache: false,

                    success: function(data)  { //display html using divID
                    	__mysys_apps.mepreloader('mepreloaderme',false);
                    	jQuery('#memsgtestent_bod').html(data);
                    	jQuery('#memsgtestent').modal('show');
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
              	alert(mtxt);
              	__mysys_apps.mepreloader('mepreloaderme',false);
              	return false;
        }  //end try            
    }

	function __my_item_lookup2() {
		jQuery('.mitemcode2' ) 
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
			source: '<?= site_url(); ?>promo_search',
			focus: function() {
				  // prevent value inserted on focus
				  return false;
				},
				select: function( event, ui ) {
					var terms = ui.item.value;
					jQuery(this).attr('alt', jQuery.trim(ui.item.value));
					jQuery(this).attr('title', jQuery.trim(ui.item.value));
					var ndisc = jQuery('#txt_promodiscval').val();
					this.value = ui.item.value;
					var clonedRow = jQuery(this).parent().parent().clone();
					var indexRow = jQuery(this).parent().parent().index();
					
                    
					var xobjitemprice = jQuery(clonedRow).find('input[type=text]').eq(6).attr('id');  /*PRICE*/
					jQuery(this).attr('data-mtnkattr',ui.item.mtkn_rid);
					
					
                    
					jQuery('#' + xobjitemprice).val(ui.item.pro_code_disc);
					return false;
				}
			})
		.click(function() { 
			//jQuery(this).keydown(); 
			var terms = this.value;
			//jQuery(this).autocomplete('search', '');
			jQuery(this).autocomplete('search', jQuery.trim(terms));
		});  
	}  //end __my_item_lookup
    
    
</script>