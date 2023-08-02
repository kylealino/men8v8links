<?php
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$data = array();
$mpages = (empty($mylibzsys->oa_nospchar($request->getVar('mpages'))) ? 0 : $mylibzsys->oa_nospchar($request->getVar('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
$mtbtn    = '';
$mtbtndis = '';
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}

$txtsearchedrec = trim($request->getVar('txtsearchedrec'));
$nodr = 0;

?>


<div class="row mt-2 m-0 p-1">
	<div class="col-sm-6">
		<div class="input-group input-group-sm">
			<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
			<input type="text" class="form-control form-control-sm" name="mytxtsearchrec" placeholder="Search Control Number" id="txtsearchedrec" aria-label="Search-Deposit" aria-describedby="mebtnGroupAddon" value="<?=$txtsearchedrec;?>" required/>
			<div class="invalid-feedback">Please fill out this field.</div>
			<button type="submit" class="btn btn-success btn-sm" id="mbtn_mesalesdepo_search"><i class="bi bi-search"></i></button>
			<?=anchor('mysales-deposit/?vrecs=yes', 'Reset',' class="btn btn-success btn-sm" ');?>
		</div>
	</div>
</div>					
<div class="row m-0 p-1">
	<div class="col-sm-8">
		<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch_salesdepobr','');?>
	</div>
</div>
<div class="row m-0 p-1">
	<div class="col-sm-12">
		<div class="table-responsive">	
			<table class="metblentry-font table-bordered table-stripped" id="_tbl_deposit_recs">
				<thead class="metblhead-bg">
					<tr>
						<th style="text-align: center;"><span class="fa fa-cog"></span></th>
						<th style="text-align: center;">Control Number</th>
						<th style="text-align: center;">Sales Date</th>
						<th style="text-align: center;">Group</th>
						<th style="text-align: center;">Company</th>
						<th style="text-align: center;">Branch</th>
						<th style="text-align: center;">User</th>
					</tr>
				</thead>
				<tbody id="myTable">
					<?php 
					if($rlist != ''):
						$nn = 1;
						foreach ($rlist as $row ): 
							$mtkn_IN = hash('sha384', $row['recid'] . $mpw_tkn); 
							$nodr++;
							$trns_id     = $row['sysctrl_seqn'];
							$dterqst     = $row['salesDate'];
							$compName    = $row['COMP_NAME'];
							$branchname  = $row['BRNCH_NAME'];
							$groupTag    = $row['groupTag'];
							$bgcolor = (($nn % 2) ? "#F2F2F2" : "#F2FEFF");
							$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
							
							?>
							<tr id="tr_recme_<?=$nodr;?>" style="background-color: <?=$bgcolor;?> !important;" <?=$on_mouse;?>>
								<td nowrap>
									<a href="<?=site_url();?>mysales-deposit/?mtkn_etr=<?=$mtkn_IN?>" class="btn text-warning btn-sm p-1 pb-0 mebtnpt1" title = "Edit/View" ><i class="bi bi-pencil"></i></a>
									<button class=" btn btn-sm text-danger btn_del_trns p-1 pb-0 mebtnpt1" value="<?=$mtkn_IN?>" data-rectype="hd" data-ctrlseqn="<?=$trns_id;?>" data-merowid="<?=$nodr;?>" title="Delete" id='btn_del_trns'> <span class="bi bi-trash"></span></button>
								</td>
								<td nowrap><?= $trns_id ?></td>
								<td nowrap><?= $dterqst ?></td>
								<td nowrap><?= $groupTag ?></td>
								<td nowrap><?= $compName ?></td>
								<td nowrap><?= $branchname?></td>
								<td nowrap><?= $row['m_user'] ?></td>
							</tr>
							<?php
							$nn++;
						endforeach;	
					else:
						?>
						<tr><td colspan="6"> No Records Found! </td></tr>
						<?php
					endif;
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	__mysys_apps.meTableSetCellPadding("_tbl_deposit_recs",3,"1px solid #7F7F7F");
	function __myredirected_rsearch_salesdepobr(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#txtsearchedrec').val();
			var mparam = {
				txtsearchedrec: txtsearchedrec,
				mpages: mobj 
			}; 
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>mysales-deposit-recs',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data) {
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#mysalesdeporecs').html(data);
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
	
	jQuery('.btn_del_trns').on('click',function(){
		try{
			//jQuery(cobj).parent().parent().remove();
			var trid = jQuery(this).parent().parent().attr('id');
			var mtkn_etr = jQuery(this).val();
			var data_mtknid = jQuery(this).attr('data-mtknid');
			var data_rectype = jQuery(this).attr('data-rectype');
			var ctrlseqn = jQuery(this).attr('data-ctrlseqn');
			var memsg = "<input type=\"hidden\" id=\"medelrecdata\" data-metrid=\"" + trid + "\" data-mtknid=\"" + data_mtknid + "\" data-mtkn_etr=\"" + mtkn_etr + "\" data-rectype=\"" + data_rectype + "\" />";
			memsg += "<div id=\"memsgdelrec\" style=\"display:none;\"></div>";
			jQuery('#meyn_salesdepo_bod').html('<span class=\"fw-bold text-danger\">Selected record [' + ctrlseqn + '] is no longer available and permanently deleted...<br\>Proceed anyway?</span>' + memsg);
			jQuery('#staticBackdropmeyn_salesdepo').html('<span class=\"fw-bold\">Sales Deposit Record Deletion...</span>');
			jQuery('#meyn_salesdepo_yes').prop('disabled',false);
			jQuery('#meyn_salesdepo_no').html('No');
			jQuery('#meyn_salesdepo').modal('show');
		} catch(err){
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
		}
		return false;
	});
	
	
	jQuery('#mbtn_mesalesdepo_search').on('click',function(){
		try{
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#txtsearchedrec').val();
			if(jQuery.trim(txtsearchedrec) == '') {
				return false;
			}
			
			var mparam = {
				txtsearchedrec: txtsearchedrec,
				mpages: 1 
			};	
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>mysales-deposit-recs',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mysalesdeporecs').html(data);
						return false;
				},
				error: function() { // display global error on the menu function
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}	
			});	
		} catch(err){
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
		}
		return false;
	});  //end mbtn_mesalesdepo_search
	
	jQuery('#txtsearchedrec').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				var txtsearchedrec = jQuery('#txtsearchedrec').val();
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
				url: '<?=site_url();?>mysales-deposit-recs',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							__mysys_apps.mepreloader('mepreloaderme',false);
							jQuery('#mysalesdeporecs').html(data);
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
</script>
