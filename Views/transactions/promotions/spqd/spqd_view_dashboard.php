<?php 

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$mytrxfgpack = model('App\Models\MyPromoDiscountModel');
$myusermod = model('App\Models\MyUserModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$fromspromo =$request->getVar('fromspromo');
$tospromo = $request->getVar('tospromo');
$ifcheckval = $request->getVar('ifcheckvalue');
$ifcheck = ($ifcheckval == 1) ? 'checked' : '';
$myusermod = model('App\Models\MyUserModel');
$mytxtsearchrec = $request->getVar('txtsearchedrec');


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
	<div class="col-sm-2 d-flex align-items-center pr-0 form-check" >
		<input class="form-check-input" type="checkbox" value="" id="ActiveCheckbox" name="ActiveCheckbox" <?=$ifcheck;?>>
		<label class="form-check-label" for="ActiveCheckbox">
			Active Only
		</label>
		
	</div>
	<div class="row mt-2 mb-3 d-flex align-items-center">
		<div class="col-4">
			<button type="button" class="btn btn-primary btn-sm w-50" onclick="clear_val();" style="background-color:#167F92; color:#fff;">Clear</button>
		</div>
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
					<?php if(!empty($mytxtsearchrec)){
						?>
					<th>Spromo No.</th>
					<th>Spromo Code</th>
					<th>Branch Code</th>
                    <th>Item bar code</th>
					<th>Quantity</th>
					<th>Quantity Remaining</th>
					<th>Disable</th>
					<th>Download</th>
						<?php }else{?>
					<!-- <th>Spromo No.</th> -->
					<th>Spromo Code</th>
					<th>Branch Code</th>
					<th>Quantity</th>
					<th>Last SRP Total</th>
					<th>New SRP Total</th>
					<th>Sales Loss</th>
					<th>No.of Active</th>
					<!-- <th>Download</th> -->
					<?php } ?>
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
						$promo_code = ($row['promo_code_spqd']);
						$str2 = "
						select spqd_trx_no from `trx_pos_promo_spqd_dt` WHERE promo_code_spqd='$promo_code' and is_disable = 'N'
						";
						$q2 = $mylibzdb->myoa_sql_exec($str2,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						$numActive = $q2->getNumRows();
					
						$str = "
						select *,SUM(qty_spqd)AS total_qty,SUM(last_srp_amount)AS total_last_amount,SUM(new_srp_amount)AS total_new_amount from `trx_pos_promo_spqd_dt` WHERE promo_code_spqd='$promo_code' GROUP BY promo_code_spqd
						";
						$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					
						if ($q->getNumRows() > 0) {
							$data = $q->getRowArray(); // Get the row as an array
							$qty_spqd = $data['total_qty'];
							$last_srp_amount = $data['total_last_amount'];
							$total_new_amount= $data['total_new_amount'];
							$sales_loss = $last_srp_amount - $total_new_amount;
						
						
						
						?>
						
						<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
						<?php if(!empty($mytxtsearchrec)){
							?>
							<td class="text-center" nowrap>
							<?=anchor('mepromo-spqd-view/?dashboard1=' . $spqd_trx_no , '<i class="bi bi bi-pencil" style="background-color:#167F92; color:#fff; padding: 3px 5px 3px;; border-radius: 5px;"></i>',' class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm"');?>
							</td>
							
						<td nowrap><?=$row['spqd_trx_no'];?></td>
							<td nowrap><?=$row['promo_code_spqd'];?></td>
							<td nowrap><?=$row['branch_code'];?></td>
							<td nowrap><?=$row['item_barcode'];?></td>
                            <td nowrap><?=$row['qty_spqd'];?></td>
							<td nowrap><?=$row['qty_spqd'];?></td>
							<td nowrap><?=$row['is_disable'];?></td>
							<td>
								<button class="btn btn-success btn-sm" onclick="javascript:__mbtn_promo_bdownload('<?=$row['spqd_trx_no'];?>');" > <i class="bi bi-print"></i> Download</button>
							</td>
							<?php }else{?>
								<td class="text-center" nowrap>
							<?=anchor('mepromo-spqd-view/?dashboard=' . $promo_code , '<i class="bi bi bi-pencil" style="background-color:#167F92; color:#fff; padding: 3px 5px 3px;; border-radius: 5px;"></i>',' class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm"');?>
								</td>
							<td nowrap><?=$row['promo_code_spqd'];?></td>
							<td nowrap><?=$row['branch_code'];?></td>
                            <td nowrap><?=$qty_spqd;?></td>
							<td nowrap><?=$last_srp_amount;?></td>
							<td nowrap><?=$total_new_amount;?></td>
							<td nowrap><?=$sales_loss;?></td>
							<td nowrap><?=$numActive;?></td>
							<!-- <td>
								<button class="btn btn-success btn-sm" onclick="javascript:__mbtn_promo_bdownload('//');" > <i class="bi bi-print"></i> Download</button>
							</td> -->
						</tr>
						<?php 
						}
					}
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

function clear_val() {
    document.getElementById("mytxtsearchrec").value = "";
    document.getElementById("fromspromo").value = "";
    document.getElementById("tospromo").value = "";
    document.getElementById("ActiveCheckbox").checked = false;
}

	__my_item_lookup2()

	function __myredirected_rsearch(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			var fromspromo = jQuery('#fromspromo').val();
			var tospromo = jQuery('#tospromo').val();
			var checkbox = document.getElementById("ActiveCheckbox");
			var ifcheckvalue = checkbox.checked ? "1" : "0";
			


            //mytrx_sc/mndt_sc2_recs
            var mparam = { 
            	txtsearchedrec: txtsearchedrec,
				fromspromo:fromspromo,
				tospromo:tospromo,
            	mpages: mobj,
				ifcheckvalue : ifcheckvalue
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
				var tospromo = jQuery('#tospromo').val();
				var checkbox = document.getElementById("ActiveCheckbox");
				var ifcheckvalue = checkbox.checked ? "1" : "0";

				var mparam = {
					txtsearchedrec: txtsearchedrec,
					fromspromo:fromspromo,
					tospromo:tospromo,
					ifcheckvalue : ifcheckvalue,
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
						var checkbox = document.getElementById("ActiveCheckbox");
						var ifcheckvalue = checkbox.checked ? "1" : "0";

						var mparam = {
							txtsearchedrec: txtsearchedrec,
							fromspromo:fromspromo,
							tospromo:tospromo,
							ifcheckvalue : ifcheckvalue,
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
    function __mbtn_promo_bdownload(spqd_trx_no){
    	try { 
    		__mysys_apps.mepreloader('mepreloaderme',true);
    		
    		var mparam = {
    			spqd_trx_no: spqd_trx_no

    		}; 
                   //console.log(mparam);
                  jQuery.ajax({ // default declaration of ajax parameters
                  	type: "POST",
                  	url: '<?=site_url();?>me-spqd-barcode-dl',
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