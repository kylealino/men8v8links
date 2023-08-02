<?php 
/**
 *	File        : masterdata/md-qpr-employees-recs.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Dec 06, 2022
 * 	last update : Dec 06, 2022
 * 	description : QPR Employee Records Listing
 */
 
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');

$cuser = $mylibzdb->mysys_user();
$mpw_tkn = $mylibzdb->mpw_tkn();

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
<div class="row ms-1">
	<?=form_open('mymd-qpr-employees','class="row g-3 for-validation" id="myfrmsrec_qpremp" ');?>
		<div class="input-group input-group-sm">
			<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
			<input type="text" class="form-control form-control-sm" name="mytxtsearchrec" placeholder="Search Material Code/Description/Item Barcode" id="mytxtsearchrec" aria-label="Input group example" aria-describedby="mebtnGroupAddon" placeholder="Search Material Code/Description/Item Barcode" value="<?=$mytxtsearchrec;?>" required/>
			<div class="invalid-feedback">Please fill out this field.</div>
			<button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
			<?=anchor('mymd-qpr-employees', 'Reset',' class="btn btn-primary" ');?>
		</div>
	<?=form_close();?> <!-- end of ./form -->
</div>	
<div class="row ms-1 mt-1">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
</div>	
	<!-- <div class="box box-primary"> -->
		<!-- <div class="box-body"> -->
			<div class="table-responsive">
					<table class="memetable metblentry-font" id="metblqpremp">
						<thead>
							<tr>
								<th class="text-center">
									<?=anchor('mymd-qpr-employees/?maction=A_REC', '<i class="bi bi-plus-lg"></i>',' class="btn btn-success btn-sm p-1 pb-0 mebtnpt1" ');?>
								</th>
								<th>Employee No.</th>
								<th nowrap>Last Name</th>
								<th nowrap>First Name</th>
								<th nowrap>Middle Name</th>
								<th nowrap>Birth Date</th>
								<th nowrap>Hired Date</th>
								<th>Gender</th>
								<th>Adress</th>
								<th></th>
								<th></th>
								<th>Mobile No.</th>
								<th>Tel. No.</th>
								<th>e-Mail</th>
								<th>Other Contact Infos.</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if($rlist !== ''):
								$nn = 1;
								foreach($rlist as $row): 
									
									// $bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
									$on_mouse = " onmouseover=\"this.style.backgroundColor='#5cb85cb8';\" onmouseout=\"this.style.backgroundColor='';\"";	
									$mtkn_arttr = hash('sha384', $row['recid'] . $mpw_tkn);
								?>
								<tr <?=$on_mouse;?>>
									<td class="text-center" nowrap>
										<?=anchor('mymd-qpr-employees/?mtkn_etr=' . $mtkn_arttr, '<i class="bi bi-pencil-square"></i>',' class="btn btn-primary btn-sm p-1 pb-0 mebtnpt1" ');?>
									</td>
									<td nowrap><?=$row['EMPNUMB'];?></td>
									<td nowrap><?=$row['EMPLNAME'];?></td>
									<td nowrap><?=$row['EMPFNAME'];?></td>
									<td nowrap><?=$row['EMPMNAME'];?></td>
									<td nowrap><?=$row['EMPBDTE'];?></td>
									<td nowrap><?=$row['EMPHDTE'];?></td>
									<td nowrap><?=$row['EMPGNDR'];?></td>
									<td nowrap><?=$row['EMPADDR1'];?></td>
									<td nowrap><?=$row['EMPADDR2'];?></td>
									<td nowrap><?=$row['EMPADDR3'];?></td>
									<td nowrap><?=$row['EMPMOBN'];?></td>
									<td nowrap><?=$row['EMPTELN'];?></td>
									<td nowrap><?=$row['EMPEMAIL'];?></td>
									<td nowrap><?=$row['EMPOCNUM'];?></td>
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
					</table>
			</div>
		<!-- </div>  -->
		<!-- end box body -->
	<!-- </div>  -->
	<!-- end box --> 

	<div class="row" style="padding: 10px 10px 0px 15px !important;">
		<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
  </div>	

<script type="text/javascript"> 

	(function () {
		'use strict'

		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.querySelectorAll('.for-validation')
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
					//jQuery('html,body').scrollTop(0);
					//jQuery.showLoading({name: 'line-pulse', allowHide: false });
					var txtsearchedrec = jQuery('#mytxtsearchrec').val();
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
					url: '<?=site_url();?>search-mymd-qpr-employees',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
						success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#qpremplist').html(data);
								
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
			}, false)
		})
	})();


    function meSetCellPadding () {
        var metable = document.getElementById ("metblqpremp");
        metable.cellPadding = 5;
        metable.style.border = "1px solid #E1EFF2";
        var tabletd = metable.getElementsByTagName("td");
        //for(var i=0; i<tabletd.length; i++) {
        //    var td = tabletd[i];
        //    td.style.borderColor ="#F6F5F4";
        //}

    }
    meSetCellPadding();

	function __myredirected_rsearch(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			var mparam = {
				txtsearchedrec: txtsearchedrec,
				mpages: mobj 
			};	
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url()?>search-mymd-qpr-employees',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#qpremplist').html(data);
						
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
	
	
	jQuery('#mytxtsearchrec').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				var txtsearchedrec = jQuery('#mytxtsearchrec').val();
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
				url: '<?=site_url();?>search-mymd-qpr-employees',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							__mysys_apps.mepreloader('mepreloaderme',false);
							jQuery('#qpremplist').html(data);
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
