<?php
/* =================================================
 * Author      : Oliver Sta Maria
 * Date Created: May 02, 2023
 * Module Desc : Inventory Cyle Count View Uploaded Records for Editing
 * File Name   : transactions/ho/inventory/mycycle-count-uploaded-view-editing.php
 * Revision    : Migration to Php8 Compatability 
*/

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myivty = model('App\Models\MyInventoryModel');

$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$meuatkn = $request->getVar('meuatkn');
$txtsearchedrec = '';
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

<div class="row mt-2 m-0 p-1">
	<div class="col-sm-6">
		<div class="input-group input-group-sm">
			<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
			<input type="text" class="form-control form-control-sm" name="mytxtsearchrec_edit_rec_cyc" placeholder="Search Control Number" id="mytxtsearchrec_edit_rec_cyc" aria-label="Search-Deposit" aria-describedby="mebtnGroupAddon" value="<?=$txtsearchedrec;?>" required/>
			<div class="invalid-feedback">Please fill out this field.</div>
			<button type="submit" class="btn btn-success btn-sm" id="mbtn_mesalesdepo_search"><i class="bi bi-search"></i></button>
		</div>
	</div>
</div>
<div class="row m-0 p-1">
	<div class="col-sm-8">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
	</div>	
</div>
<div class="row m-0 p-1">
			<div class="col-md-12 col-md-12 col-md-12">
				<div class="table-responsive">
					<table class="metblentry-font table-bordered" id="__metbl_upldrecs_edit">
						<thead>
							<tr>
								<th></th>
								<th>Transaction No</th>
								<th>Year</th>
								<th>Month</th>
								<th>Branch</th>
								<th>Item Code</th>
								<th>Item Description</th>
								<th>Item Cost</th>
								<th>Item Price</th>
								<th>Qty</th>
								<th>Total Cost</th>
								<th>Total SRP</th>
								<th>B/E Tag</th>
								<th>Encoded Date</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if($rlist !== ''):
								$nn = 1;
								foreach($rlist as $row): 
									$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
									$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
									$mtkn_trxno = hash('sha384', $row['recid'] . $mpw_tkn);
									$dateObj   = DateTime::createFromFormat('!m', $row['M_MONTHS']);
									$monthName = $dateObj->format('F');
								?>
								<tr style="background-color: <?=$bgcolor;?> !important;" <?=$on_mouse;?>>
									<td class="text-center" nowrap="nowrap">
										<button class="btn btn-success btn-sm p-1 pb-0 mebtnpt1" type="button" onclick="javascript:__cyc_changeqty_sv(this,'<?=$mtkn_trxno;?>','<?=$row['M_CTRLNO'];?>','<?=$row['M_ITEMC'];?>');"><i class="bi bi-save"></i></button>
									</td> 
									<td nowrap="nowrap"><?=$row['M_CTRLNO'];?></td>
									<td nowrap="nowrap"><?=$row['M_YEAR'];?></td>
									<td nowrap="nowrap"><?=$monthName;?></td>
									<td nowrap="nowrap"><?=$row['BRNCH_NAME'];?></td>
									<td nowrap="nowrap"><?=$row['M_ITEMC'];?></td>
									<td nowrap="nowrap"><?=$row['ART_DESC'];?></td>
									<td nowrap="nowrap" class="text-end"><?=number_format($row['ART_UCOST'],2,'.',',');?></td>
									<td nowrap="nowrap" class="text-end"><?=number_format($row['ART_UPRICE'],2,'.',',');?></td>
									<td nowrap="nowrap"><input type="text" id="fld_mqty_<?=$mtkn_trxno;?>" size="10"  class="text-end" onkeypress="return __meNumbersOnly(event)" value="<?=number_format($row['M_QTY'],2,'.',',');?>" /></td>
									<td nowrap="nowrap" class="text-end"><?=number_format($row['TCOST'],2,'.',',');?></td>
									<td nowrap="nowrap" class="text-end"><?=number_format($row['TSRP'],2,'.',',');?></td>
									<td nowrap="nowrap"><?=$row['M_TAG'];?></td>
									<td nowrap="nowrap"><?=$row['M_ENCD'];?></td>
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
						<tbody>
							
								<tr>
									<td colspan="9"><strong>GRAND TOTAL</strong></td>
									<td nowrap="nowrap" class="text-end"><strong><?=number_format($M_QTY,2,'.',',');?></strong></td>
									<td nowrap="nowrap" class="text-end"><strong><?=number_format($M_TCOST,2,'.',',');?></strong></td>
									<td nowrap="nowrap" class="text-end"><strong><?=number_format($M_TSRP,2,'.',',');?></strong></td>
									<td nowrap="nowrap"></td>
									<td nowrap="nowrap"></td>
								</tr>
							
						</tbody>
					</table> <!-- end table -->
				</div> <!-- end table-responsive -->
			</div>
</div>
<script type="text/javascript"> 

 //<![CDATA[
 __mysys_apps.meTableSetCellPadding("__metbl_upldrecs_edit",3,"1px solid #7F7F7F");
	function __meNumbersOnly(e) {
		var code = (e.which) ? e.which : e.keyCode;
		//if (code > 31 && (code < 47 || code > 57)) {
		if(!((code > 47 && code < 58) || code == 46)) { 
			e.preventDefault();
		}
	} //end __meNumbersOnly
	
	function __myredirected_rsearch(mobj) { 
		try { 
			var txtsearchedrec =jQuery('#mytxtsearchrec_edit_rec_cyc').val();
			__mysys_apps.mepreloader('mepreloaderme',true);
			var fld_months = jQuery('#fld_months').val();
			var fld_years = jQuery('#fld_years').val();
			var fld_cycbranch = jQuery('#fld_cycbranch').val();
			var mtknbrid = jQuery('#fld_cycbranch').attr('data-mtknid');
			
			if(jQuery.trim(fld_cycbranch) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Branch is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
				
			}

			if(jQuery.trim(fld_years) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Year is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
			} 
			
			if(jQuery.trim(fld_months) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Month is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
			}
			var my_data = new FormData();
			
			my_data.append('fld_cycbranch', fld_cycbranch);
			my_data.append('mtknbrid', mtknbrid);
			my_data.append('fld_years', fld_years);
			my_data.append('fld_months', fld_months);
			my_data.append('mpages', mobj);
			my_data.append('txtsearchedrec', txtsearchedrec);
						
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-cycle-count-editing-uploaded-inquiry',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					jQuery('#__mycycure').html(data);
					__mysys_apps.mepreloader('mepreloaderme',false);
					return false;
				},
				error: function() { 
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
	}  //end __myredirected_rsearch
	
	 function __cyc_changeqty_sv(obj,mtkn_trxno,mtkn_ctrlno,mtkn_itemc) { 

                try { 
                    //$('html,body').scrollTop(0);
                    $.showLoading({name: 'line-pulse', allowHide: false });
                    var clonedRow = jQuery(obj).parent().parent();
                    var qty = $(clonedRow).find('input[type=text]').eq(0).val();
                    //console.log(qty);
                    //return false;
                    var mparam = {
                    	mtkn_itemc:mtkn_itemc,
                    	mtkn_ctrlno:mtkn_ctrlno,
                       mtkn_trxno: mtkn_trxno,
                       qty:qty

                    }; 

                $.ajax({ // default declaration of ajax parameters
                    type: "POST",
                    url: '<?=site_url();?>mytrx_acct/cyc_changeqty_sv',
                    context: document.body,
                    data: eval(mparam),
                    global: false,
                    cache: false,

                    success: function(data)  { //display html using divID
                        $.hideLoading();
	                    jQuery('#myModSysMsgBod').html(data);
				        jQuery('#myModSysMsg').modal('show');


                        return false;
                    },
                    error: function() { // display global error on the menu function
                        alert('error loading page...');
                        $.hideLoading();
                        return false;
                    }   
                }); 
            } catch(err) {
                var mtxt = 'There was an error on this page.\n';
                mtxt += 'Error description: ' + err.message;
                mtxt += '\nClick OK to continue.';
                alert(mtxt);
                $.hideLoading();
                return false;
            }  //end try            
        }
	
	
	jQuery('#mytxtsearchrec_edit_rec_cyc').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				__mysys_apps.mepreloader('mepreloaderme',true);
				var txtsearchedrec = jQuery('#mytxtsearchrec_edit_rec_cyc').val();
				var mparam = {
					txtsearchedrec: txtsearchedrec,
					myear:'<?=$myear;?>',
					mmonths:'<?=$mmonths;?>',
					fld_cycbranch: '<?=$fld_cycbranch;?>',
					mtknbrid: '<?=$mtknbrid;?>',
					mpages: 1 
				};	
				jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>myinventory-cycle-count-editing-uploaded-inquiry',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							jQuery('#__mycycure').html(data);
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
		} // end if event 
	});  //end mytxtsearchrec_edit_rec_cyc
	
	
</script>
