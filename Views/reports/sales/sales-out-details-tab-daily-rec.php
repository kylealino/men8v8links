<?php 
/**
 *	File        : sales/sales-out-details-tab-daily-rec.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Apr 22, 2022
 * 	last update : Apr 22, 2022
 * 	description : New UI Sales Out
 */
 
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');

$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

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
<div class="row mt-3 me-1 ms-1">
    <?=form_open('mytrx_sc/sc_view','class="needs-validation" id="myfrmsearchrec" ');?>
    <div class="col-md-6">
        <div class="input-group input-group-sm">
            <label class="input-group-text fw-bold" for="search">Search:</label>
            <input type="text" id="mytxtsearchrec" class="form-control form-control-sm" name="mytxtsearchrec" placeholder="Search Item Code" />
            <input type="hidden" id="fld_sc2_dtefrom" name="fld_sc2_dtefrom"  value="<?=$fld_sc2_dtefrom;?>"/>
            <input type="hidden" id="fld_sc2_dteto" name="fld_sc2_dteto" value="<?=$fld_sc2_dteto;?>"/>
            <input type="hidden" id="fld_sc2branch" name="fld_sc2branch" value="<?=$fld_sc2branch;?>"/>
			<input type="hidden" id="fld_sc2branch_id" name="fld_sc2branch_id" value="<?=$fld_sc2branch_id;?>"/>
			<input type="hidden" id="fld_sc2itemcode_s" name="fld_sc2itemcode_s" value="<?=$fld_sc2itemcode_s;?>"/>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <?=form_close();?> <!-- end of ./form -->

</div>	

<div class="row mt-1 me-1 ms-1">
	<div class="col-md-8">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
	</div>
</div>	

			<div class="table-responsive">
				<div class="col-md-12 col-md-12 col-md-12">
					<table class="metblentry-font table-bordered" id="tbldata_salesout">
						<thead>
							<tr class="metblhead-bg">
								<th>Branch</th>
								<th>Desc Code</th>
								<th>Item Code</th>
								<th>Item Description</th>
								<th>Qty</th>
								<th>SRP</th>
								<th>Gross</th>
								<th>Discount </th>
								<th>Net</th>
								<th>SO Tag</th>
								<th>Transaction Date</th>
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
									
								?>
								<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
									<td nowrap><?=$row['BRNCH_NAME'];?></td>
									<td nowrap><?=$row['ART_DESC_CODE'];?></td>
									<td nowrap><?=$row['SO_ITEMCODE'];?></td>
									<td nowrap><?=$row['ART_DESC'];?></td>
									<td nowrap class="metxt-right"><?=number_format($row['SO_QTY'],2,'.',',');?></td>
									<td nowrap class="metxt-right"><?=number_format($row['SO_SRP'],2,'.',',');?></td>
									<td nowrap class="metxt-right"><?=number_format($row['SO_GROSS'],2,'.',',');?></td>
									<td nowrap class="metxt-right"><?=number_format($row['SO_DISC_AMT'],2,'.',',');?></td>
									<td nowrap class="metxt-right"><?=number_format($row['SO_NET'],2,'.',',');?></td>
									<td nowrap><?=$row['SO_TAG'];?></td>
									<td nowrap><?= $mylibzsys->mydate_mmddyyyy($row['SO_DATE']);?></td>
								</tr>
								<?php 
								$nn++;
								endforeach;
							else:
								?>
								<tr>
									<td colspan="10">No data was found.</td>
								</tr>
							<?php 
							endif; ?>
						</tbody>
						<tbody>
							
								<tr>
									<td nowrap><strong>GRAND TOTAL</strong></td>
									<td nowrap></td>
									<td nowrap></td>
									<td nowrap></td>
									<td nowrap class="metxt-right"><strong><?=number_format(($SO_QTY === NULL ? 0 : $SO_QTY),2,'.',',');?></strong></td>
									<td nowrap></td>
									<td nowrap class="metxt-right"><strong><?=number_format(($SO_GROSS === NULL ? 0 : $SO_GROSS),2,'.',',');?></strong></td>
									<td nowrap class="metxt-right"><strong><?=number_format(($SO_DISC_AMT === NULL ? 0 : $SO_DISC_AMT),2,'.',',');?></strong></td>
									<td nowrap class="metxt-right"><strong><?=number_format(($SO_NET === NULL ? 0 : $SO_NET),2,'.',',');?></strong></td>
									<td nowrap></td>
									<td nowrap></td>
								</tr>
							
						</tbody>
					</table>
				</div>
			</div> <!-- end table-reponsive -->
	
<script type="text/javascript"> 

    function meSetCellPadding () {
        var metable = document.getElementById ("tbldata_salesout");
        metable.cellPadding = 6;
        metable.style.border = "1px solid #C0BCB6";
        var tabletd = metable.getElementsByTagName("td");
    }
    meSetCellPadding();

	function __myredirected_rsearch(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			var rowCount_sc_prdc1 = jQuery('#tbl_sc_cat1 tr').length - 1;
            var rowCount_sc_prdc2 = jQuery('#tbl_sc_cat2 tr').length - 1;
            var rowCount_sc_prdc3 = jQuery('#tbl_sc_cat3 tr').length - 1;
            var rowCount_sc_prdc4 = jQuery('#tbl_sc_cat4 tr').length - 1;
            var arr1 = [];
            var arr2 = [];
            var arr3 = [];
            var arr4 = [];
           
            for(aa = 1; aa < rowCount_sc_prdc1; aa++) { 
                  var clonedRow = jQuery('#tbl_sc_cat1 tr:eq(' + aa + ')').clone(); 
                  var _sc_prdc1 = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
                  arr1.push(_sc_prdc1);

            }
            for(aa = 1; aa < rowCount_sc_prdc2; aa++) { 
                  var clonedRow = jQuery('#tbl_sc_cat2 tr:eq(' + aa + ')').clone(); 
                  var _sc_prdc2 = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
                  arr2.push(_sc_prdc2);

            } 
            for(aa = 1; aa < rowCount_sc_prdc3; aa++) { 
                  var clonedRow = jQuery('#tbl_sc_cat3 tr:eq(' + aa + ')').clone(); 
                  var _sc_prdc3 = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
                  arr3.push(_sc_prdc3);

            } 
            for(aa = 1; aa < rowCount_sc_prdc4; aa++) { 
                  var clonedRow = jQuery('#tbl_sc_cat4 tr:eq(' + aa + ')').clone(); 
                  var _sc_prdc4 = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
                  arr4.push(_sc_prdc4);

            } 


            //mytrx_sc/mndt_sc2_recs
			var mparam = { 
				__hmtkn_prd_sc_c1 : arr1,
                __hmtkn_prd_sc_c2 : arr2,
                __hmtkn_prd_sc_c3 : arr3,
				__hmtkn_prd_sc_c4 : arr4,
				txtsearchedrec: txtsearchedrec,
				fld_sc2_dtefrom:'<?=$fld_sc2_dtefrom;?>',
				fld_sc2_dteto:'<?=$fld_sc2_dteto;?>',
				fld_sc2branch: '<?=$fld_sc2branch;?>',
				fld_sc2branch_id: '<?=$fld_sc2branch_id;?>',
				fld_sc2itemcode_s: '<?=$fld_sc2itemcode_s;?>',
				fld_tap: '<?=$fld_tap;?>',
				fld_sc2desccode: '<?=$fld_sc2desccode;?>',
				mpages: mobj 
			};	
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>sales-out-details-tab-daily-rec',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						$('#meout-sales-out-defailts-daily').html(data);
						
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
				var rowCount_sc_prdc1 = jQuery('#tbl_sc_cat1 tr').length - 1;
	            var rowCount_sc_prdc2 = jQuery('#tbl_sc_cat2 tr').length - 1;
	            var rowCount_sc_prdc3 = jQuery('#tbl_sc_cat3 tr').length - 1;
	            var rowCount_sc_prdc4 = jQuery('#tbl_sc_cat4 tr').length - 1;
	            var arr1 = [];
	            var arr2 = [];
	            var arr3 = [];
	            var arr4 = [];
	           
	            for(aa = 1; aa < rowCount_sc_prdc1; aa++) { 
	                  var clonedRow = jQuery('#tbl_sc_cat1 tr:eq(' + aa + ')').clone(); 
	                  var _sc_prdc1 = jQuery(clonedRow).find('input[type=text]').eq(0);
	                  var _sc_prdc1 = jQuery(_sc_prdc1).attr('data-meitmdata');
	                  arr1.push(_sc_prdc1);

	            }
	            for(aa = 1; aa < rowCount_sc_prdc2; aa++) { 
	                  var clonedRow = jQuery('#tbl_sc_cat2 tr:eq(' + aa + ')').clone(); 
	                  var _sc_prdc2 = jQuery(clonedRow).find('input[type=text]').eq(0);
	                  var _sc_prdc2 = jQuery(_sc_prdc2).attr('data-meitmdata');
	                  arr2.push(_sc_prdc2);

	            } 
	            for(aa = 1; aa < rowCount_sc_prdc3; aa++) { 
	                  var clonedRow = jQuery('#tbl_sc_cat3 tr:eq(' + aa + ')').clone(); 
	                  var _sc_prdc3 = jQuery(clonedRow).find('input[type=text]').eq(0);
	                  var _sc_prdc3 = jQuery(_sc_prdc3).attr('data-meitmdata');
	                  arr3.push(_sc_prdc3);

	            } 
	            for(aa = 1; aa < rowCount_sc_prdc4; aa++) { 
	                  var clonedRow = jQuery('#tbl_sc_cat4 tr:eq(' + aa + ')').clone(); 
	                  var _sc_prdc4 = jQuery(clonedRow).find('input[type=text]').eq(0);
	                  var _sc_prdc4 = jQuery(_sc_prdc4).attr('data-meitmdata');
	                  arr4.push(_sc_prdc4);

	            } 


	            //mytrx_sc/mndt_sc2_recs

			var mparam = {
					__hmtkn_prd_sc_c1 : arr1,
	                __hmtkn_prd_sc_c2 : arr2,
	                __hmtkn_prd_sc_c3 : arr3,
	                __hmtkn_prd_sc_c4 : arr4,
					txtsearchedrec: txtsearchedrec,
					fld_sc2_dtefrom:'<?=$fld_sc2_dtefrom;?>',
					fld_sc2_dteto:'<?=$fld_sc2_dteto;?>',
					fld_sc2branch: '<?=$fld_sc2branch;?>',
					fld_sc2branch_id: '<?=$fld_sc2branch_id;?>',
					fld_sc2itemcode_s: '<?=$fld_sc2itemcode_s;?>',
					fld_tap: '<?=$fld_tap;?>',
					fld_sc2desccode: '<?=$fld_sc2desccode;?>',
					mpages: 1 
				};	

				jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>sales-out-details-tab-daily-rec',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							jQuery('#meout-sales-out-defailts-daily').html(data);
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
		var forms = document.querySelectorAll('.needs-validation')
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

						
						var rowCount_sc_prdc1 = jQuery('#tbl_sc_cat1 tr').length - 1;
			            var rowCount_sc_prdc2 = jQuery('#tbl_sc_cat2 tr').length - 1;
			            var rowCount_sc_prdc3 = jQuery('#tbl_sc_cat3 tr').length - 1;
			            var rowCount_sc_prdc4 = jQuery('#tbl_sc_cat4 tr').length - 1;
			            var arr1 = [];
			            var arr2 = [];
			            var arr3 = [];
			            var arr4 = [];
			           
			            for(aa = 1; aa < rowCount_sc_prdc1; aa++) { 
			                  var clonedRow = jQuery('#tbl_sc_cat1 tr:eq(' + aa + ')').clone(); 
			                  var _sc_prdc1 = jQuery(clonedRow).find('input[type=text]').eq(0);
			                  var _sc_prdc1 = jQuery(_sc_prdc1).attr('data-meitmdata');
			                  arr1.push(_sc_prdc1);

			            }
			            for(aa = 1; aa < rowCount_sc_prdc2; aa++) { 
			                  var clonedRow = jQuery('#tbl_sc_cat2 tr:eq(' + aa + ')').clone(); 
			                  var _sc_prdc2 = jQuery(clonedRow).find('input[type=text]').eq(0);
			                  var _sc_prdc2 = jQuery(_sc_prdc2).attr('data-meitmdata');
			                  arr2.push(_sc_prdc2);

			            } 
			            for(aa = 1; aa < rowCount_sc_prdc3; aa++) { 
			                  var clonedRow = jQuery('#tbl_sc_cat3 tr:eq(' + aa + ')').clone(); 
			                  var _sc_prdc3 = jQuery(clonedRow).find('input[type=text]').eq(0);
			                  var _sc_prdc3 = jQuery(_sc_prdc3).attr('data-meitmdata');
			                  arr3.push(_sc_prdc3);

			            } 
			            for(aa = 1; aa < rowCount_sc_prdc4; aa++) { 
			                  var clonedRow = jQuery('#tbl_sc_cat4 tr:eq(' + aa + ')').clone(); 
			                  var _sc_prdc4 = jQuery(clonedRow).find('input[type=text]').eq(0);
			                  var _sc_prdc4 = jQuery(_sc_prdc4).attr('data-meitmdata');
			                  arr4.push(_sc_prdc4);

			            } 

						var mparam = {
							__hmtkn_prd_sc_c1 : arr1,
			                __hmtkn_prd_sc_c2 : arr2,
			                __hmtkn_prd_sc_c3 : arr3,
			                __hmtkn_prd_sc_c4 : arr4,
							txtsearchedrec: txtsearchedrec,
							fld_sc2_dtefrom:'<?=$fld_sc2_dtefrom;?>',
							fld_sc2_dteto:'<?=$fld_sc2_dteto;?>',
							fld_sc2branch: '<?=$fld_sc2branch;?>',
							fld_sc2branch_id: '<?=$fld_sc2branch_id;?>',
							fld_sc2itemcode_s: '<?=$fld_sc2itemcode_s;?>',
							fld_tap: '<?=$fld_tap;?>',
							fld_sc2desccode: '<?=$fld_sc2desccode;?>',
							mpages: 1 
						};	
						
						jQuery.ajax({ // default declaration of ajax parameters
						type: "POST",
						url: '<?=site_url();?>sales-out-details-tab-daily-rec',
						context: document.body,
						data: eval(mparam),
						global: false,
						cache: false,
							success: function(data)  { //display html using divID
									__mysys_apps.mepreloader('mepreloaderme',false);
									jQuery('#meout-sales-out-defailts-daily').html(data);
									
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
	
</script>
