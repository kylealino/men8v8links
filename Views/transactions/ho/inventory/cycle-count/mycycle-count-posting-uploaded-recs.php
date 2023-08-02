<?php

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');

$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$data = array();
$mpages = (empty($mylibzsys->oa_nospchar($request->getVar('mpages'))) ? 0 : $tmylibzsys->oa_nospchar($request->getVar('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}


$txtsearchedrec = '';
?>
<div class="row mt-2 ms-1 me-1">
</div>
<div class="row m-0 p-1">
	<div class="col-md-12 col-md-12 col-md-12">
		<div class="table-responsive">
			<table class="metblentry-font table-bordered" id="__tbl_mycycpurc_recs">
				<thead>
					<tr>
						<th colspan="12">LOGS UPLOADED CYCLE COUNT</th>
					</tr>
				</thead>
				<thead>
					<tr>
						
						<th>Control No</th>
						<th>Branch Name</th>
						<th>Cycle Count Date</th>
						<th>Total Uploaded Records </th>
						<th>Total Posted Records </th>
						<th>Uploaded User</th>
						<th>Uploaded Date</th>
						<th>Posted?Y/N</th>
						<th>Posted User</th>
						<th>Posted Date</th>
						<th>POSTING</th>
				</thead>
				<tbody>
					<?php 
					if($rlist !== ''):
						$nn = 1;
						foreach($rlist as $row): 
							$mtknattr = hash('sha384', $row['ML_CTRLNO'] . $mpw_tkn); 
							$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
							$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
							//$mtkn_trxno = hash('sha384', $row['recid'] . $mpw_tkn);
							$mtkn_trxno = $row['ML_CTRLNO'];
							$mtkn_temp = $row['ML_TEMPTBL'];
							$dis = ($row['ML_ISPOSTED'] == 'Y' ? "disabled" : '');
							$dis2 = ''; 
							$fld_cycbranch_id = $row['ML_BRANCH_ID'];
							$fld_cycdate = $row['ML_CYCDATE'];
							$fld_upld_cyctag = $row['ML_CYCTAG'];
							$fld_months = $row['ML_MONTH'];
							$fld_years = $row['ML_YEAR'];
						?>
						
							<tr style="background-color: <?=$bgcolor;?> !important;" <?=$on_mouse;?>>
							<td nowrap="nowrap"><?=$row['ML_CTRLNO'];?></td>
							<td nowrap="nowrap"><?=$row['BRNCH_NAME'];?></td>
							<td nowrap="nowrap"><?=$mylibzsys->mydate_mmddyyyy($row['ML_CYCDATE']);?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($row['ML_RECS'],2,'.',',');?></td>
							<td nowrap="nowrap" class="text-end"><?=number_format($row['ML_PROC_RECS'],2,'.',',');?></td>
							<td nowrap="nowrap"><?=$row['ML_USER'];?></td>
							<td nowrap="nowrap"><?=$mylibzsys->mydate_mmddyyyy($row['ML_ENCD']);?></td>
							<td nowrap="nowrap"><?=$row['ML_ISPOSTED'];?></td>
							<td nowrap="nowrap"><?=$row['ML_POST_USER'];?></td>
							<td nowrap="nowrap"><?=$mylibzsys->mydate_mmddyyyy($row['ML_POST_ENCD']);?></td>
							<td class="text-center" nowrap="nowrap">
								<button class="btn btn-sm p-1 text-success pb-0 mebtnpt1 mevw_mycycpurc_recs" type="button" data-mtknattr="<?=$mtknattr;?>" <?=$dis2;?>><i title="View Records"  class="bi bi-search"></i></button>
								<button class="btn btn-sm p-1 text-danger pb-0 mebtnpt1" type="button" data-mtknattr="<?=$mtknattr;?>" <?=$dis2;?>><i title="Download" class="bi bi-download"></i></button>
								<button class="btn btn-sm p-1 text-warning pb-0 mebtnpt1 mepost_mycycpurc_recs" type="button" data-mtknattr="<?=$mtknattr;?>" <?=$dis;?>><i title="Post" class="bx bxs-paper-plane"></i></button>
							</td>
							
						</tr>
						<?php 
						$nn++;
						endforeach;
					else:
						?>
						<tr>
							<td colspan="16">No data was found.</td>
						</tr>
					<?php 
					endif; ?>
				</tbody>
			</table>
		</div> <!-- end table responsive --> 
	</div>
</div>
<div class="row m-0 p-1">
</div>
<script type="text/javascript"> 
	__mysys_apps.meTableSetCellPadding("__tbl_mycycpurc_recs",3,"1px solid #7F7F7F");
	
	jQuery('.mepost_mycycpurc_recs').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var data_mtknattr = jQuery(this).attr('data-mtknattr');
			
			var mparam = {
				mtknattr: data_mtknattr
			};	
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>myinventory-cycle-count-post-uploaded',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#memsgme_bod').html(data);
						jQuery('#memsgme').modal('show');
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
	});  //end mepost_mycycpurc_recs
	
	jQuery('.mevw_mycycpurc_recs').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var data_mtknattr = jQuery(this).attr('data-mtknattr');
			
			var mparam = {
				mtknattr: data_mtknattr
			};	
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>myinventory-cycle-count-uploaded-view',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#memsgme_bod').html(data);
						jQuery('#memsgme').modal('show');
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
	});  //end mepost_mycycpurc_recs
	
</script>
