<?php

$this->request = \Config\Services::request();
$this->mylibzdb = model('App\Models\MyLibzDBModel');
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');

$db_erp = $this->myusermod->mydbname->medb(0);
$cuser = $this->myusermod->mysys_user();
$mpw_tkn = $this->myusermod->mpw_tkn();

$data = array();
$mpages = (empty($this->mylibzsys->oa_nospchar($this->request->getVar('mpages'))) ? 0 : $this->mylibzsys->oa_nospchar($this->request->getVar('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}

$memodule = 'ivty_gen_detl_recs';
$txtsearchedrec = $this->request->getVar('txtsearchedrec');
$bid_mtknattr = $this->request->getVar('bid_mtknattr');
$me_branch = '';
if(!empty($bid_mtknattr)) {
	$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
	from {$db_erp}.`mst_companyBranch` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$bid_mtknattr'";
	$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	if($q->getNumRows() > 0) { 
		$rw = $q->getRowArray();
		$me_branch = $rw['BRNCH_NAME'];
	}
	$q->freeResult();
}
?>
<div class="row mt-2 m-0 p-1">
	<div class="col-sm-6">
		<div class="input-group input-group-sm">
			<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
			<input type="text" class="form-control form-control-sm" name="mytxtsearchrec_<?=$memodule;?>" placeholder="Search Item Code / Barcode / Item Description" id="mytxtsearchrec_<?=$memodule;?>" data-metkntmp="<?=$metkntmp;?>" aria-label="Search-Item Code" aria-describedby="mebtnGroupAddon" value="<?=$txtsearchedrec;?>" required/>
			<div class="invalid-feedback">Please fill out this field.</div>
			<button type="button" class="btn btn-success btn-sm" id="mbtn_<?=$memodule;?>_search"><i class="bi bi-search"></i></button>
			<button type="button" class="btn btn-success btn-sm" id="mbtn_<?=$memodule;?>_reset"><i class="bi bi-bootstrap-reboot"></i> Reset</button>
			<?php
			//user download access if permitted
			if($this->myusermod->ua_mod_access_verify($db_erp,$cuser,'02','0004','00070702')) { 
			?>
			<form method="post" action="<?=base_url();?>myinventory-report-detailed-download" id="myfrm_ivty_dload">
				<button class="btn btn-info btn-sm" id="btn_stockcard_dl" type="submit">Download</button>
				<input type="hidden" name="mdl_me_branch" id="mdl_me_branch" value="<?=$me_branch;?>" />
				<input type="hidden" name="mdl_me_branch_mtkn" id="mdl_me_branch_mtkn" value="<?=$bid_mtknattr;?>" />
				<input type="hidden" name="mdl_metkntmp" id="mdl_metkntmp" value="<?=$metkntmp;?>" />
			</form>
			<?php
			}
			?>			
		</div>
	</div>
</div>
<div class="row mt-2 m-0 p-1">
	<div class="col-sm-8">
		<?=$this->mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_' . $memodule,'');?>
	</div>
</div>
<?php
if ($G_NOBAL_ITEMS > 0): 
	echo "
	<div class=\"row mt-2 m-0 p-1\">
		<div class=\"col-sm-6\">
			<div class=\"alert alert-danger\" role=\"alert\">
				<h4 class=\"alert-heading\">Inventory Alert</h4>
				<p>Detected NEGATIVE Inventory in <span class=\"fw-bold\">" . number_format($G_NOBAL_ITEMS,0,'',',') . "</span> items!!!</p>
				<hr>
				<p class=\"mb-0\">Please Download file and check <span class=\"fw-bold\">Ending Balance QTY</span></p>
			</div> 
		</div>
	</div>
	";
endif;
?>
<div class="row m-0 p-1">
	<div class="col-md-12 col-md-12 col-md-12">
		<div class="table-responsive">
			<table class="metblentry-font table-bordered" id="__tbl_<?=$memodule;?>">
				<thead>
					<tr>
						<th>Item Code</th>
						<th>Barcode</th>
						<th>Item Description</th>
						<th>Beginning Balance</th>
						<th>Gen. Inventory Count</th>
						<th>Gen. Inventory Discrepancy</th>
						<th>Adjusted Cycle Counting</th>
						<th>Receiving (Deliveries)</th>
						<th>Claims</th>
						<th>Receiving (Store Use)</th>
						<th>Receiving (Membership)</th>
						<th>Receiving (Change Price)</th>
						<th>Receiving (Rcv in frm PO)</th>
						<th>Sales Out</th>
						<th>Pull Out (Buy1Take1)</th>
						<th>Pull Out (Dispose)</th>
						<th>Pull Out (For Bargain)</th>
						<th>Pull Out (Giveaways)</th>
						<th>Pull Out (Inventory Transfer Out)</th>
						<th>Pull Out (Pull Out to Other Branch)</th>
						<th>Pull Out (Return to Mapulang Lupa)</th>
						<th>Pull Out (Store-Use)</th>
						<th>Pull Out (Others)</th>
						<th>Ending Balance</th>
						<th>Cost Amount</th>
						<th>SRP Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if($rlist !== ''):
						$nn = 1;
						foreach($rlist as $row): 
							$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
							$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
							$BEG_QTY = $row['BEG_QTY'];
							$cfwbold_BEG_QTY = (!($row['BEG_QTY'] == 0) ? ' fw-bold' : '');
							$cfwbold_GEN_IVTYC = (!($row['GEN_IVTYC'] == 0) ? ' fw-bold' : '');
							$cfwbold_GEN_IVTYC_DIFF = (($row['GEN_IVTYC_DIFF'] < 0) ? ' fw-bold text-danger' : (($row['GEN_IVTYC_DIFF'] > 0) ? ' fw-bold' : ''));
							$cfwbold_CYC_ADJ_QTY = (!($row['CYC-ADJ_QTY'] == 0) ? ' fw-bold' : '');
							$cfwbold_CYC_ADJ_QTY .= (($row['CYC-ADJ_QTY'] < 0) ? ' text-danger': '');
							$cfwbold_RCV_QTY = (!($row['RCV_QTY'] == 0) ? ' fw-bold' : '');
							$cfwbold_CLM_QTY = (!($row['CLM_QTY'] == 0) ? ' fw-bold' : '');
							$cfwbold_RCV_S_QTY = (!($row['RCV-S_QTY'] == 0) ? ' fw-bold' : '');
							$cfwbold_RCV_M_QTY = (!($row['RCV-M_QTY'] == 0) ? ' fw-bold' : '');
							$cfwbold_RCV_C_QTY = (!($row['RCV-C_QTY'] == 0) ? ' fw-bold' : '');
							$cfwbold_RCV_R_QTY = (!($row['RCV-R_QTY'] == 0) ? ' fw-bold' : '');
							$cfwbold_SALES_QTY = (!($row['SALES_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_B1T1_QTY = (!($row['B1T1_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_DSP_QTY = (!($row['DSP_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_BRG_QTY = (!($row['BRG_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_GVA_QTY = (!($row['GVA_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_TO_QTY = (!($row['TO_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_TOB_QTY = (!($row['TOB_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_RTML_QTY = (!($row['RTML_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_POSU_QTY = (!($row['POSU_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_POOTH_QTY = (!($row['POOTH_QTY'] == 0) ? ' fw-bold text-danger' : '');
							$cfwbold_END_BAL_QTY = (($row['END_BAL_QTY'] < 0) ? ' fw-bold text-danger' : ' fw-bold ');
						?>
						<tr style="background-color: <?=$bgcolor;?> !important;" <?=$on_mouse;?>>
							<td nowrap><?=$row['ITEMC'];?></td>
							<td nowrap><?=$row['ITEM_BARCODE'];?></td>
							<td nowrap><?=$row['ART_DESC'];?></td>
							<td nowrap class="text-end<?=$cfwbold_BEG_QTY;?>"><?=number_format($row['BEG_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_GEN_IVTYC;?>"><?=number_format($row['GEN_IVTYC'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_GEN_IVTYC_DIFF;?>"><?=number_format($row['GEN_IVTYC_DIFF'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_CYC_ADJ_QTY;?>"><?=number_format($row['CYC-ADJ_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_RCV_QTY;?>"><?=number_format($row['RCV_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_CLM_QTY;?>"><?=number_format($row['CLM_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_RCV_S_QTY;?>"><?=number_format($row['RCV-S_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_RCV_M_QTY;?>"><?=number_format($row['RCV-M_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_RCV_C_QTY;?>"><?=number_format($row['RCV-C_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_RCV_R_QTY;?>"><?=number_format($row['RCV-R_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_SALES_QTY;?>"><?=number_format($row['SALES_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end"<?=$cfwbold_B1T1_QTY;?>><?=number_format($row['B1T1_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_DSP_QTY;?>"><?=number_format($row['DSP_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_BRG_QTY;?>"><?=number_format($row['BRG_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_GVA_QTY;?>"><?=number_format($row['GVA_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_TO_QTY;?>"><?=number_format($row['TO_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_TOB_QTY;?>"><?=number_format($row['TOB_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_RTML_QTY;?>"><?=number_format($row['RTML_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_POSU_QTY;?>"><?=number_format($row['POSU_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_POOTH_QTY;?>"><?=number_format($row['POOTH_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end<?=$cfwbold_END_BAL_QTY;?>"><?=number_format($row['END_BAL_QTY'],2,'.',',');?></td>
							<td nowrap class="text-end fw-bold"><?=number_format($row['ITEM_AMT_COST'],2,'.',',');?></td>
							<td nowrap class="text-end fw-bold"><?=number_format($row['ITEM_AMT_PRICE'],2,'.',',');?></td>
						</tr>
						<?php 
						$nn++;
						endforeach;
					endif;
					?>
					<tr class="fw-bold">
						<td colspan="3" nowrap>Grand Total:</td>
						<td nowrap class="text-end"><?=number_format($G_BEG_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_GEN_IVTYC,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_GEN_IVTYC_DIFF,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_CYC_ADJ_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_RCV_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_CLM_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_RCV_S_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_RCV_M_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_RCV_C_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_RCV_R_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_SALES_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_B1T1_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_DSP_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_BRG_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_GVA_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_TO_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_TOB_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_RTML_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_POSU_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_POOTH_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_END_BAL_QTY,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_ITEM_AMT_COST,2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($G_ITEM_AMT_PRICE,2,'.',',');?></td>
					</tr>
				</tbody>
			</table>
		</div> <!-- end table reponsive -->
	</div> <!-- end col -->
</div> <!-- end row --> 
<script type="text/javascript"> 
	__mysys_apps.meTableSetCellPadding("__tbl_<?=$memodule;?>",3,"1px solid #7F7F7F");
	
	function __myredirected_<?=$memodule;?>(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
			var datametkntmp = jQuery('#mytxtsearchrec_<?=$memodule;?>').attr('data-metkntmp');
			var bid_mtknattr = jQuery('#fld_sc2branch_s').attr('data-mtknattr');
			var fld_branch = jQuery('#fld_sc2branch_s').val();
			var fld_years = jQuery('#fld_years').val();
			var fld_months = jQuery('#fld_months').val();
			var mparam ={
				bid_mtknattr:bid_mtknattr,
				fld_branch: fld_branch,
				fld_years: fld_years,
				fld_months: fld_months,
				txtsearchedrec: txtsearchedrec,
				metkntmp: datametkntmp,
				mpages: mobj 
		   }
		   	
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-report-detailed-gen',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data) {
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wgstockcard').html(data);
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
			var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
			var datametkntmp = jQuery('#mytxtsearchrec_<?=$memodule;?>').attr('data-metkntmp');
			var bid_mtknattr = jQuery('#fld_sc2branch_s').attr('data-mtknattr');
			var fld_branch = jQuery('#fld_sc2branch_s').val();
			var fld_years = jQuery('#fld_years').val();
			var fld_months = jQuery('#fld_months').val();
			var mparam ={
				bid_mtknattr:bid_mtknattr,
				fld_branch: fld_branch,
				fld_years: fld_years,
				fld_months: fld_months,
				txtsearchedrec: txtsearchedrec,
				metkntmp: datametkntmp,
				mpages: 1 
		   }
		   	
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-report-detailed-gen',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data) {
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wgstockcard').html(data);
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
		
	});  //end button reset
	
	jQuery('#mbtn_<?=$memodule;?>_reset').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var datametkntmp = jQuery('#mytxtsearchrec_<?=$memodule;?>').attr('data-metkntmp');
			var bid_mtknattr = jQuery('#fld_sc2branch_s').attr('data-mtknattr');
			var fld_branch = jQuery('#fld_sc2branch_s').val();
			var fld_years = jQuery('#fld_years').val();
			var fld_months = jQuery('#fld_months').val();
			var mparam ={
				bid_mtknattr:bid_mtknattr,
				fld_branch: fld_branch,
				fld_years: fld_years,
				fld_months: fld_months,
				metkntmp: datametkntmp,
				mpages: 1 
		   }
		   	
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-report-detailed-gen',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data) {
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wgstockcard').html(data);
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
		
	});  //end button reset
	
	jQuery('#mytxtsearchrec_<?=$memodule;?>').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
				var datametkntmp = jQuery('#mytxtsearchrec_<?=$memodule;?>').attr('data-metkntmp');
				if(jQuery.trim(txtsearchedrec) == '') {
					return false;
				}
				
				__mysys_apps.mepreloader('mepreloaderme',true);
				
				var bid_mtknattr = jQuery('#fld_sc2branch_s').attr('data-mtknattr');
				var fld_branch = jQuery('#fld_sc2branch_s').val();
				var fld_years = jQuery('#fld_years').val();
				var fld_months = jQuery('#fld_months').val();
				var mparam ={
					bid_mtknattr:bid_mtknattr,
					fld_branch: fld_branch,
					fld_years: fld_years,
					fld_months: fld_months,
					txtsearchedrec: txtsearchedrec,
					metkntmp: datametkntmp,
					mpages: 1 
			   }
				
				jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url()?>myinventory-report-detailed-gen',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							__mysys_apps.mepreloader('mepreloaderme',false);
							jQuery('#wgstockcard').html(data);
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
	});	 //end keypress event 	
</script>
