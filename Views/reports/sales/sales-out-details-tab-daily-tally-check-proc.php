<?php
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$medatef = $this->mylibzsys->mydate_mmddyyyy($this->myusermod->request->getVar('medatef'));
$medatet = $this->mylibzsys->mydate_mmddyyyy($this->myusermod->request->getVar('medatet'));
$memodule = "__saleout_daily_tally__";
$ldispreconmodule = 0;
$metrxdate = $this->myusermod->request->getVar('medatef');
?>
<div class="row mt-2">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="metblentry-font table-bordered" id="__tbl_<?=$memodule;?>">
				<thead>
					<tr>
						<th colspan="5">Sales Dated: <?=$medatef;?> - <?=$medatet;?></th>
					</tr>
					<tr class="metblhead-bg">
						<th></th>
						<th>Branch Code</th>
						<th>Branch Name</th>
						<th>SO Net</th>
						<th>POS Tally Net</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				<?php 
				$nSO_Net = 0;
				$nPOST_Net = 0;
				if($rlist !== ''):
					$nn = 1;
					$reconmodulelink = "";
					foreach($rlist as $row): 
						
						$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
						$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
						if($ldispreconmodule) { 
							$reconmodulelink = "<input type=\"button\" data-mebcode=\"{$row['ME_BRANCH']}\" data-metrxdate=\"{$metrxdate}\" onclick=\"javascript:mesales_check_recon(this);\" class=\"btn btn-sm btn-info\" value=\"Process Sales Recon...\" />";
						} //end if
						$mestat = "<span class=\"fw-bolder text-danger\">NOT TALLY : " . round((round($row['ME_POST_NETSALE_AMT'],2) - round($row['MB_CODE_AMOUNT'],2)),2) . " {$reconmodulelink}</span>";
						
						if($row['ME_BRANCH_MAT_FLAG'] == 'R' && (round($row['ME_POST_NETSALE_AMT'],2) == round($row['MB_CODE_AMOUNT'],2))):
							$mestat = "TALLY";
						elseif ($row['ME_BRANCH_MAT_FLAG'] == 'G'): 
							$mestat = "";
						endif;
						
					?>
					<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
						<td nowrap><?=$nn;?></td>
						<td nowrap><?=$row['ME_BRANCH'];?></td>
						<td nowrap><?=$row['ME_BRANCH_NAME'];?></td>
						<td nowrap class="text-end"><?=number_format($row['ME_SO_NETSALE_AMT'],2,'.',',');?></td>
						<td nowrap class="text-end"><?=number_format($row['ME_POST_NETSALE_AMT'],2,'.',',');?></td>
						<td nowrap><?=$mestat;?></td>
					</tr>
					<?php 
					$nn++;
					$nSO_Net += $row['ME_SO_NETSALE_AMT'];
					$nPOST_Net += $row['ME_POST_NETSALE_AMT'];
					endforeach;
				else:
					?>
					<tr>
						<td colspan="6">No data was found.</td>
					</tr>
				<?php 
				endif; ?>
					<tr>
						<td colspan="3"></td>
						<td nowrap class="text-end fw-bolder"><?=number_format($nSO_Net,2,'.',',');?></td>
						<td nowrap class="text-end fw-bolder"><?=number_format($nPOST_Net,2,'.',',');?></td>
						<td></td>
					</tr>			
				</tbody>
			</table> 
		</div> <!-- end div -->
	</div> <!-- end div -->
</div>
<?php
if($ldispreconmodule) { 
	echo "
	<div class=\"row mt-2\" id=\"__sales_recon_msg__\">
	</div>
	";
} //endif
?>
<script type="text/javascript"> 
	__mysys_apps.meTableSetCellPadding("__tbl_<?=$memodule;?>",5,'1px solid #7F7F7F');
	
	function mesales_check_recon(cobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mebranch = jQuery(cobj).attr('data-mebcode');
			var medatetrx = jQuery(cobj).attr('data-metrxdate');
			var mparam = { medatetrx: medatetrx,
				mebranch: mebranch 
			};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>sales-out-recon-proc',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
						//jQuery('#__sales_recon_msg__').html(data);
						jQuery('#memsgsalesoutdetl_bod').html(data);
						jQuery('#memsgsalesoutdetl').modal('show');
						
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
	} //end mesales_recon
</script>
