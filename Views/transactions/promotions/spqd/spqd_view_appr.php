<?php 

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$mytrxpurch = model('App\Models\MyPromoBuy1take1Model');
$myusermod = model('App\Models\MyUserModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$mytxtsearchrec_appr = $request->getVar('txtsearchedrec');
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
<style>
table.memetable, th.memetable, td.memetable {
  border: 1px solid #F6F5F4;
  border-collapse: collapse;
}
thead.memetable, th.memetable, td.memetable {
  padding: 6px;
}
</style>
<?=form_open('','class="needs-validation-search" id="myfrmsearchrec_appr" ');?>
    <div class="col-md-6 mb-1">
        <div class="input-group input-group-sm">
            <label class="input-group-text fw-bold" for="search">Search:</label>
            <input type="text" id="mytxtsearchrec_appr" class="form-control form-control-sm" name="mytxtsearchrec_appr" placeholder="Search" />
           	<button type="submit" class="btn btn-dgreen btn-sm"><i class="bi bi-search"></i></button>
        </div>
    </div>
<?=form_close();?> <!-- end of ./form -->


<div class="col-md-8">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch_appr','');?>
</div>

<!-- START APPROVAL HEADER -->

<div class="table-responsive">
	<div class="col-md-12 col-md-12 col-md-12">
		<table class="table table-condensed table-hover table-bordered table-sm">
			<thead>
				<tr>
					<th class="text-center">
						Status
					</th>
					<th>SPromo Discount No.</th>
					<th>Branch Code</th>
					<th>Reason</th>
					<th>Last Total SRP</th>
					<th>New Total SRP</th>
					<th>Total QTY</th>
					
				</tr>
			</thead>

			<!-- END HEADER  -->

			<!-- START DETAILS VALUE -->

			<tbody>
				<?php 
				if($rlist !== ''):
					$nn = 1;
					foreach($rlist as $row): 
						
						$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
						$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
						$mtkn_recid = hash('sha384', $row['id'] . $mpw_tkn);
						$dis = ($row['is_approved'] == 'Y' ? "disabled" : '');
						$spqd_trxno = $row['spqd_trx_no'];
						
					?>
					<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
						<td>
						<?=anchor('mepromo-spqd-view/?for_approval=' . $spqd_trxno , '<i class="bi bi bi-search" style="background-color:#167F92; color:#fff; padding: 3px 5px 3px;; border-radius: 5px;"></i>',' class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm"');?>
							
						</td>
						<td nowrap><?=$row['spqd_trx_no'];?></td>
						<td nowrap><?=$row['branch_code'];?></td>
						<td nowrap><?=$row['spqd_reason'];?></td>
                        <td nowrap><?=$row['last_total_srp'];?></td>
                        <td nowrap><?=$row['new_total_srp'];?></td>
                        <td nowrap><?=$row['total_qty'];?></td>
						
					</tr>
					<?php 
						$nn++;
						endforeach;
						else:
					?>
					<tr>
						<td colspan="18">No data was found.</td>
					</tr>
				<?php endif; ?>
			</tbody>
			
			<!-- END DETAILS VALUE -->

		</table>
	</div>
</div>
	
<script type="text/javascript"> 

	function __myredirected_rsearch_appr(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec_appr').val();
			
            //mytrx_sc/mndt_sc2_recs
			var mparam = { 
				txtsearchedrec: txtsearchedrec,
				mpages: mobj 
			};	
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>me-spqd-appr',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						$('#packlist').html(data);
						
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
	
	
	jQuery('#mytxtsearchrec_appr').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				__mysys_apps.mepreloader('mepreloaderme',true);
				var txtsearchedrec = jQuery('#mytxtsearchrec_appr').val();

				var mparam = {
					txtsearchedrec: txtsearchedrec,
					mpages: 1 
				};	

				jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>me-spqd-appr',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							jQuery('#packlist').html(data);
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
		var forms = document.querySelectorAll('.needs-validation-search')
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
						var txtsearchedrec = jQuery('#mytxtsearchrec_appr').val();

						var mparam = {
							txtsearchedrec: txtsearchedrec,
							mpages: 1 
						};	
						
						jQuery.ajax({ // default declaration of ajax parameters
						type: "POST",
						url: '<?=site_url();?>me-spqd-appr-save',
						context: document.body,
						data: eval(mparam),
						global: false,
						cache: false,
							success: function(data)  { //display html using divID
									__mysys_apps.mepreloader('mepreloaderme',false);
									jQuery('#packlist').html(data);
									
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
	function save_for_approve_buy1take1(mtkn_recid,spqd_trxno){
		try { 
            __mysys_apps.mepreloader('mepreloaderme',true);
            
                    var mparam = {
                        mtkn_recid: mtkn_recid,
						spqd_trxno:spqd_trxno

                    }; 
                   //console.log(mparam);
                  jQuery.ajax({ // default declaration of ajax parameters
                    type: "POST",
                    url: '<?=site_url();?>me-spqd-appr-save',
                    context: document.body,
                    data: eval(mparam),
                    global: false,
                    cache: false,

                    success: function(data)  { //display html using divID
                        __mysys_apps.mepreloader('mepreloaderme',false);
                        jQuery('#memsgtestent_bod').html(data);
           				jQuery('#memsgtestent').modal('show');
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
	
</script>