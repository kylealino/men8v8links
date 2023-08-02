<?php 
/**
 *	File        : transactions/pullout/pullout-recs-encd.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Dec 22, 2022
 * 	last update : Dec 22, 2022
 * 	description : Pull Out Encoded Transactions 
 */
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mydatum = model('App\Models\MyDatumModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myusermod =  model('App\Models\MyUserModel');
$mydrtrx =  model('App\Models\MyDRTrxModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$cusergrp = $myusermod->mysys_usergrp();



$data = array();
$mpages = (empty($mylibzsys->oa_nospchar($request->getVar('mpages'))) ? 0 : $mylibzsys->oa_nospchar($request->getVar('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}
$str_style='';
$cuserrema=$myusermod->mysys_userrema();
if($cuserrema ==='B'){
    //$this->load->view('template/novo/header_br');
    $str_style=" style=\"display:none;\"";
}
else{
    //$this->load->view('template/novo/header');
    $str_style='';
}
?>
<div class="row">
	<div class="col-sm-12">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
	</div>
</div>
<div class="row m-0">
	<div class="col-sm-12">
		<div class="table-responsive">
			<table id="metbl_poutencd" class="metblentry-font table-striped table-hover table-bordered">
				<thead>
					<tr>
						<th colspan="2" class="text-center">
							<?=anchor('pullout-trx', '<i class="bi bi-plus-lg"></i>',' class="btn btn-success btn-sm" ');?>
						</th>
						<th>Transaction No</th>
						<th>Company</th>
						<th>Area Code</th>
						<th>Supplier</th>
						<th>Total Actual Qty</th>
						<th <?=$str_style;?>>Total Actual Cost</th>
						<th>Total Actual SRP</th>
						<th>PO No</th>
						<th>PO Date</th>
						<th>User</th>
						<th>D/F Tag</th>
						<th>Y/N Posted</th>
						<th>Remarks</th>
						<th>Encoded Date</th>
						<th>Print Form</th>
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
                            $potrx_no = $row['potrx_no'];
							$dis = ($row['post_tag'] == 'Y' || $row['df_tag'] == 'D' ? "disabled" : '');
							//$dis_edt = ($row['post_tag'] == 'Y' ? "isDisabled" : '');
						?>
						<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
							<td class="text-center" nowrap>
								<?=anchor('pullout-trx/?mtkn_trxno=' . $mtkn_trxno, '<i class="bi bi-pencil-fill"></i>',' class="btn text-warning btn-sm"');?>
							</td>
							<!-- <td class="text-center" nowrap>
								<button title="Hint: Ready for Posting when tag is Final.Draft or Already posted is disabled." id="post_<?=$row['recid'];?>" class="btn btn-info btn-xs" type="button" onclick="javascript:__sv_post_po('<?=$mtkn_trxno;?>','<?=$row['recid'];?>');" <?=$dis;?>><i class="fa fa-paper-plane"></i></button>
							</td> -->
							<td class="text-center" nowrap>
								<button class="btn text-danger btn-sm" type="button" onclick="javascript:__mndt_invent_crecs('<?=$mtkn_trxno;?>');"><i class="bi bi-x-circle"></i></button>
							</td>
							<td nowrap><?=$row['potrx_no'];?></td>
							<td nowrap><?=$row['COMP_NAME'];?></td>
							<td nowrap><?=$row['BRNCH_NAME'];?></td>
							<td nowrap><?=$row['VEND_NAME'];?></td>
							<td nowrap><?=number_format($row['hd_subtqty'],2,'.',',');?></td>
							<td <?=$str_style;?> nowrap><?=number_format($row['hd_subtcost'],2,'.',',');?></td>
							<td nowrap><?=number_format($row['hd_subtamt'],2,'.',',');?></td>
							<td nowrap><?=$row['po_no'];?></td>
							<td nowrap><?=$row['po_date'];?></td>
							<td nowrap><?=$row['muser'];?></td>
							<td nowrap><?=$row['df_tag'];?></td>
							<td nowrap><?=$row['post_tag'];?></td>
							<td nowrap><?=$row['rems'];?></td>
							<td nowrap><?=$mylibzsys->mydate_mmddyyyy($row['encd_date']);?></td>
							
							<td nowrap>
								<button onclick="window.open('<?= site_url() ?>mytrx_acct/rpts_print?potrx_no=<?=$potrx_no?>')" class="btn btn-sm bg-red"><i class="fa fa-print"> View/Print</i></button>
							</td>
							
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
	</div> <!-- end col-sm-12 -->
</div> <!-- end row -->
<script type="text/javascript"> 
	function __myredirected_rsearch(mobj) { 
		try {
			__mysys_apps.mepreloader('mepreloaderme',true); 
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			
			var mparam = {
				txtsearchedrec: txtsearchedrec,
				mpages: mobj 
			};
			
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",  //old from mytrx_acct/mndt_invent_po_recs
				url: '<?=site_url();?>search-pullout-trx',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#mymodoutrecs').html(data);
					
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
	} //end __myredirected_rsearch
	
	__mysys_apps.meSetCellPadding('metbl_poutencd');
</script>