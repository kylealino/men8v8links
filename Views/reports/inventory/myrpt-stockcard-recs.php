<?php 
/**
 *	File        : reports/inventory/myrpt-stockcard-recs.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: April 18, 2023
 * 	last update : April 18, 2023
 * 	description : Branch Stock Card Inventory on-hand
 */

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
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}

$mytxtsearchrec = $request->getVar('mytxtsearchrec');
$fld_stinqbritemcode_s = $request->getVar('fld_stinqbritemcode_s');
$fld_stinqbrbranch = $request->getVar('fld_stinqbrbranch');
$fld_stinqbrbranch_id = $request->getVar('fld_stinqbrbranch_id');


?>
<div class="row mt-3 m-0 p-1">
	<div class="col-sm-6">
	  <div class="alert alert-primary" role="alert"><strong>Last Sales Uploaded Date.<br/></strong><?=$mylibzsys->mydate_mmddyyyy($last_sales_date);?><br/></div>
	</div>
</div>
<div class="row m-0 p-1">
  <div class="col-sm-6">
	<div class="input-group input-group-sm">
		<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
		<input type="text" class="form-control form-control-sm" name="mytxtsearchrec" placeholder="Search Material Code" id="mytxtsearchrec" aria-label="Input group example" aria-describedby="mebtnGroupAddon" value="<?=$mytxtsearchrec;?>" required/>
		<div class="invalid-feedback">Please fill out this field.</div>
		<button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
		<?=anchor('myreport-inventory', 'Reset',' class="btn btn-success btn-sm" ');?>
	</div>
  </div>
</div>


<div class="row m-0 p-1">
	<div class="col-sm-8">
		<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch_stinqbr','');?>
	</div>
</div>
<div class="row m-0 p-1">
	<div class="table-responsive">
		<div class="col-md-12 col-md-12 col-md-12">
			<table class="metblentry-font table-bordered" id="tbldata_stockcard">
				<thead>
					<tr class="metblhead-bg">
						<th>ITEM CODE</th>
						<th>ITEM DESC</th>
						<th>BEG BALANCE QTY</th>
						<th>RCV BALANCE QTY</th>
						<th>RCV IN FR POUT BALANCE QTY</th>
						<th>POUT BALANCE QTY</th>
						<th>POUT TO OTHER BRNCH BALANCE QTY</th>
						<th>SALES OUT BALANCE QTY</th> 
						<th>BALANCE QTY</th>
						<th>SRP</th>
						<th>TOTAL SRP</th>
						<th>LAST DELIVERY DATE</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if($rlist !== ''):
						$nn = 1;
						foreach($rlist as $rr): 
							$fld_itemcode = $rr['ART_CODE'];
							$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
							$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
							
						?>
						<tr style="background-color: <?=$bgcolor;?> !important;" <?=$on_mouse;?>>
							<td nowrap="nowrap"><?=$rr['ART_CODE'];?></td>
							<td nowrap="nowrap"><?=$rr['ART_DESC'];?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($rr['LBBE_BALQTY'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($rr['LBRC_BALQTY'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($rr['LBRCPO_BALQTY'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($rr['LBPO_BALQTY'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($rr['LBPOBR_BALQTY'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($rr['LBSO_BALQTY'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($rr['BQTY'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($rr['ART_UPRICE'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($rr['TAMOUNT'],2,'.',',');?></td>
							<td nowrap="nowrap"><?=$mylibzsys->mydate_mmddyyyy($rr['LB_DELV_LDTETRX']);?></td>
						</tr>
						<?php 
						$nn++;
						endforeach;
						?>
						<tr>
							<td nowrap="nowrap" colspan="14"></td>
						</tr>
						<tr>
							<td colspan="2" nowrap="nowrap"><strong>GRAND TOTAL</strong></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td nowrap="nowrap" class="text-end"><strong><?=number_format($BQTY,2,'.',',');?></strong></td>
							<td></td>
							<td nowrap="nowrap" class="text-end"><strong><?=number_format($TAMOUNT,2,'.',',');?></strong></td>
						</tr>
				</tbody>
			<?php
			
		endif;
			?>
			</table>
		</div>
	</div> <!-- end table-responsive -->
</div>	
<script type="text/javascript"> 
	
	__mysys_apps.meTableSetCellPadding("tbldata_stockcard",5,"1px solid #7F7F7F");
	
	function __myredirected_rsearch_stinqbr(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			var mparam = {
				txtsearchedrec: txtsearchedrec,
				tbltemp: '<?=$tbltemp;?>',
				fld_stinqbrbranch: '<?=$fld_stinqbrbranch;?>',
				fld_stinqbrbranch_id: '<?=$fld_stinqbrbranch_id;?>',
				fld_stinqbritemcode_s: '<?=$fld_stinqbritemcode_s;?>',
				mpages: mobj 
			};	
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>myreport-stockcard-recs',
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
	
	 
	
	jQuery('#mytxtsearchrec').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				__mysys_apps.mepreloader('mepreloaderme',true);
				var txtsearchedrec = jQuery('#mytxtsearchrec').val();
				var mparam = {
					txtsearchedrec: txtsearchedrec,
					tbltemp: '<?=$tbltemp;?>',
					fld_stinqbrbranch: '<?=$fld_stinqbrbranch;?>',
					fld_stinqbrbranch_id: '<?=$fld_stinqbrbranch_id;?>',
					fld_stinqbritemcode_s: '<?=$fld_stinqbritemcode_s;?>',
					mpages: 1 
				};	
				jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>myreport-stockcard-recs',
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
	});
	
	
	
</script>
