<?php 
/**
 *	File        : myua/myuser-recs.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Apr 14, 2023
 * 	last update : Apr 14, 2023
 * 	description : User Records
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
<div class="row m-0 p-1">
	<div class="col-sm-12">
	<?=form_open('md-article/recs','class="row g-3 for-validation" id="myfrmsrec_user" ');?>
		<div class="input-group input-group-sm">
			<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
			<input type="text" class="form-control form-control-sm" name="mytxtsearchrec" placeholder="Search User Name / Full Name" id="mytxtsearchrec" aria-label="Input group example" aria-describedby="mebtnGroupAddon" placeholder="Search Material Code/Description/Item Barcode" value="<?=$mytxtsearchrec;?>" required/>
			<div class="invalid-feedback">Please fill out this field.</div>
			<button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
			<?=anchor('myua', 'Reset',' class="btn btn-primary" ');?>
		</div>
	<?=form_close();?> <!-- end of ./form -->
	</div>
</div>	
<div class="row m-0 p-1">
	<div class="col-sm-12">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
	</div>
</div>
<div class="row m-0 p-1">
	<div class="col-sm-12">
		<div class="table-responsive">
			<table class="metblentry-font table-bordered" id="metbluser">
				<thead>
					<tr>
						<th class="text-center">
							<?=anchor('myua', '<i class="bi bi-plus-lg"></i>',' class="btn btn-success btn-sm p-1 pb-0 mebtnpt1" ');?>
						</th>
						<th>User Name</th>
						<th nowrap>User Full Name</th>
						<th>Date Created</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if($rlist !== ''):
						$nn = 1;
						foreach($rlist as $row): 
							$bgcolor = (($nn % 2) ? "#EAEAEA" : "#F2FEFF");
							//$on_mouse = " onmouseover=\"this.style.backgroundColor='#5cb85cb8';\" onmouseout=\"this.style.backgroundColor='';\"";
							$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
							$mtkn_arttr = hash('sha384', $row['recid'] . $mpw_tkn);
						?>
						<tr style="background-color: <?=$bgcolor;?> !important;" <?=$on_mouse;?>>
							<td class="text-center" nowrap>
								<?=anchor('myua/?meuatkn=' . $mtkn_arttr, '<i class="bi bi-pencil-square"></i>',' class="btn btn-primary btn-sm p-1 pb-0 mebtnpt1" ');?>
							</td>
							<td nowrap><?=$row['myusername'];?></td>
							<td nowrap><?=$row['myuserfulln'];?></td>
							<td nowrap></td>
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
	</div>
</div>	
<div class="row m-0 p-1">
	<div class="col-sm-12">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
	</div>
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
					url: '<?=site_url();?>search-myuser',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
						success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#wg_myuser_recs').html(data);
								
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
			url: '<?=site_url()?>search-myuser',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#wg_myuser_recs').html(data);
						
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
	
	__mysys_apps.meTableSetCellPadding("metbluser",5,"1px solid #7F7F7F");
	
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
				url: '<?=site_url();?>search-myuser',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							__mysys_apps.mepreloader('mepreloaderme',false);
							jQuery('#wg_myuser_recs').html(data);
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
