<?php
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');

$db_erp = $myusermod->mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$txtsearchedrec = $myusermod->request->getVar('txtsearchedrec');
$memodule = 'ivty_recon_adj_srecs';

$mpages = (empty($mylibzsys->oa_nospchar($request->getVar('mpages'))) ? 0 : $mylibzsys->oa_nospchar($request->getVar('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}

$_rposted = '';
$_rnotposted = '';
$_rall = '';
$merecinq = $myusermod->request->getVar('merecinq'); 
if ($merecinq == 'POSTED'): 
	$_rposted = ' checked ';
elseif ($merecinq == 'NOTPOSTED'): 
	$_rnotposted = ' checked ';
endif;

?>
<div class="row mt-3 ms-1 me-1">
    <div class="col-md-6">
		<div class="input-group input-group-sm">
			<span class="input-group-text fw-bold" id="mebtnGroupAddon_<?=$memodule;?>">Search:</span>
			<input type="text" class="form-control form-control-sm" id="mytxtsearchrec_<?=$memodule;?>" placeholder="Search Control Number" name="mytxtsearchrec_<?=$memodule;?>" aria-label="Search-IvtyReconAdj" aria-describedby="mebtnGroupAddon_<?=$memodule;?>" value="<?=$txtsearchedrec;?>" required/>
			<div class="invalid-feedback">Please fill out this field.</div>
			<button type="submit" class="btn btn-success btn-sm" id="mbtn_<?=$memodule;?>_search"><i class="bi bi-search"></i></button>
			<button type="submit" class="btn btn-success btn-sm" id="mbtn_<?=$memodule;?>_reset"><i class="bi bi-bootstrap-reboot"></i> Reset</button>
		</div>
	</div>
</div>
<div class="row mt-2 m-0 p-1">
	<div class="col-md-6">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios_Posted" value="option1" <?=$_rposted;?>>
			<label class="form-check-label" for="gridRadios_Posted">
			Posted
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios_NotPosted" value="option2" <?=$_rnotposted;?>>
			<label class="form-check-label" for="gridRadios_NotPosted">
			Not Posted
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios_AllRecs" value="option">
			<label class="form-check-label" for="gridRadios_AllRecs">
			All Records
			</label>
		</div>		
	</div>
</div>
<div class="row mt-2 m-0 p-1">
	<div class="col-sm-8">
		<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_' . $memodule,'');?>
	</div>
</div>
<div class="row mt-2 ms-1 me-1">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="metblentry-font table-bordered" id="__tbl_<?=$memodule;?>">
				<thead>
					<tr>
						<th colspan="2"></th>
						<th>Control No</th>
						<th>Branch Name</th>
						<th>Deduction Adj</th>
						<th>Additional Adj</th>
						<th>User</th>
						<th>Date Encoded</th>
						<th>Posted Date</th>
				</thead>
				<tbody>
					<?php 
					if($rlist !== ''):
						$nn = 1;
						
						foreach($rlist as $row): 
							$mtknattr = hash('sha384', $row['recid'] . $mpw_tkn); 
							$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
							$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
							//$mtkn_trxno = hash('sha384', $row['recid'] . $mpw_tkn);
							$mdisabled_but_post = ($row['__mpostrmk'] == 'YES' ? ' disabled ' : '');
							$mprevent = ($row['__mpostrmk'] == 'YES' ? 'nonono' : '');
						?>
						<tr style="background-color: <?=$bgcolor;?> !important;" <?=$on_mouse;?>>
							<td nowrap="nowrap"><button class="btn btn-sm p-1 text-success pb-0 mebtnpt1 mbtn_vw_<?=$memodule;?>" type="button" data-mtknattr="<?=$mtknattr;?>" ><i title="View Records" class="bi bi-search"></i></button></td>
							<td nowrap="nowrap"><button class="btn btn-sm p-1 text-danger pb-0 mebtnpt1" type="button" data-mtknattr="<?=$mtknattr;?>" data-mevalue="<?=$row['ira_hd_ctrlno'];?>" name="mbtn_posting_<?=$memodule . $mprevent;?>[]" <?=$mdisabled_but_post;?>><i title="Post Records" alt="Post Records" class="bi bi-bookmark-x-fill"></i></button></td>
							<td nowrap="nowrap"><?=$row['ira_hd_ctrlno'];?></td>
							<td nowrap="nowrap"><?=$row['BRNCH_NAME'];?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($row['__ded_qty'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($row['__add_qty'],2,'.',',');?></td>
							<td nowrap="nowrap"><?=$row['muser'];?></td>
							<td nowrap="nowrap"><?=$row['__mencd'];?></td>
							<td nowrap="nowrap"><?=$row['__mdateposted'];?></td>
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
		</div> <!-- end table-responsive -->
	</div>
</div>
<?=$mylibzsys->memsgbox_yesno1('meyn3_' . $memodule,'','');?>

<script type="text/javascript"> 
	__mysys_apps.meTableSetCellPadding("__tbl_<?=$memodule;?>",3,"1px solid #7F7F7F");
	
	function __myredirected_<?=$memodule;?>(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var my_data = new FormData();
			var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
			var gridRadios_Posted = document.getElementById('gridRadios_Posted').checked;
			var gridRadios_NotPosted = document.getElementById('gridRadios_NotPosted').checked;
			var gridRadios_AllRecs = document.getElementById('gridRadios_AllRecs').checked;
			
			if(gridRadios_Posted) { 
				my_data.append('merecinq','POSTED');
			}
			
			if(gridRadios_NotPosted) { 
				my_data.append('merecinq','NOTPOSTED');
			}
			
			my_data.append('txtsearchedrec',txtsearchedrec);
			my_data.append('mpages',mobj);
			
			
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-recon-adj-recs',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#mywg_recon_adj_recs').html(data);
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
		} 
	} //end __myredirected_rsearch		
	
	jQuery('#mbtn_<?=$memodule;?>_search').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var my_data = new FormData();
			var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
			var gridRadios_Posted = document.getElementById('gridRadios_Posted').checked;
			var gridRadios_NotPosted = document.getElementById('gridRadios_NotPosted').checked;
			var gridRadios_AllRecs = document.getElementById('gridRadios_AllRecs').checked;
			
			if(gridRadios_Posted) { 
				my_data.append('merecinq','POSTED');
			}
			
			if(gridRadios_NotPosted) { 
				my_data.append('merecinq','NOTPOSTED');
			}
			
			my_data.append('me','LexaLexieLexus');
			my_data.append('txtsearchedrec',txtsearchedrec);
			my_data.append('mpages',1);
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-recon-adj-recs',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#mywg_recon_adj_recs').html(data);
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
		}  //end try 
	}); //end button search 
	
	jQuery('#mbtn_<?=$memodule;?>_reset').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var my_data = new FormData();
			
			my_data.append('me','LexaLexieLexus');
			my_data.append('mpages',1);
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-recon-adj-recs',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#mywg_recon_adj_recs').html(data);
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
		}  //end try 
	}); //end button reset 
	
	
	jQuery('#mytxtsearchrec_<?=$memodule;?>').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
				if(jQuery.trim(txtsearchedrec) == '') {
					return false;
				}
				
				var my_data = new FormData();
				var gridRadios_Posted = document.getElementById('gridRadios_Posted').checked;
				var gridRadios_NotPosted = document.getElementById('gridRadios_NotPosted').checked;
				var gridRadios_AllRecs = document.getElementById('gridRadios_AllRecs').checked;
				
				if(gridRadios_Posted) { 
					my_data.append('merecinq','POSTED');
				}
				
				if(gridRadios_NotPosted) { 
					my_data.append('merecinq','NOTPOSTED');
				}
				
				my_data.append('me','LexaLexieLexus');
				my_data.append('txtsearchedrec',txtsearchedrec);
				my_data.append('mpages',1);
				
				__mysys_apps.mepreloader('mepreloaderme',true);
				
				jQuery.ajax({ 
					type: "POST",
					url: '<?=site_url()?>myinventory-recon-adj-recs',
					context: document.body,
					data: my_data,
					contentType: false,
					global: false,
					cache: false,
					processData: false,
					success: function(data) { 
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mywg_recon_adj_recs').html(data);
						return false;
					},
					error: function() { 
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
	});	 //end keypress event 		
	
	
	jQuery('.mbtn_vw_<?=$memodule;?>').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mtknattr = jQuery(this).attr('data-mtknattr');
			window.location.href = '<?=site_url()?>myinventory-recon-adj/?mtknattr=' + mtknattr;
		} catch(err) { 
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
		}  //end try 
		
	}); 
	
	jQuery('[name="mbtn_posting_<?=$memodule;?>[]"]').click(function() { 
		try { 
			var data_mtknattr = jQuery(this).attr('data-mtknattr');
			var data_mevalue = jQuery(this).attr('data-mevalue');
			
			var memsg = "<input type=\"hidden\" id=\"merecdatame\" data-mtknattr=\"" + data_mtknattr + "\" data-mevalue=\"" + data_mevalue + "\"  />";
			memsg += "<div id=\"memsgrecme\" style=\"display:none;\"></div>";

			jQuery('#meyn3_<?=$memodule;?>_bod').html('<span class=\"fw-bold\">Selected record [' + data_mevalue + '] will be posted to Inventory Balance.<br\>Proceed anyway?</span>' + memsg);
			jQuery('#staticBackdropmeyn3_<?=$memodule;?>').html('<span class=\"fw-bold\">Inventory Recon/Adj Record Posting</span>');
			jQuery('#meyn3_<?=$memodule;?>_yes').prop('disabled',false);
			jQuery('#meyn3_<?=$memodule;?>_no').html('No');
			jQuery('#memsgrecme').hide();
			jQuery('#meyn3_<?=$memodule;?>').modal('show');
			
			
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}  
		
	}); //end button for posting 
	
	jQuery('#meyn3_<?=$memodule;?>_yes').click(function() { 
		try {
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mtknattr = jQuery('#merecdatame').attr('data-mtknattr');
			var my_data = new FormData();
			my_data.append('mtknattr',mtknattr);
			
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-recon-adj-post-rec',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsgrecme').html(data);
					return false;
				},
				error: function() { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 			
			
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}  
		
	});  // posting yes button 
	
	
	
</script>

