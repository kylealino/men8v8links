<?php 

/**
 *	File        : masterdata/sub_masterdata_bom/sub-md-item-bom-recs.php
 *  Auhtor      : Kyle Alino
 *  Date Created: Jul 28, 2023
 * 	last update : Jul 28, 2023
 * 	description : Sub Masterdata Records
 */
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
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
<style>
	table.memetable, th.memetable, td.memetable {
		border: 1px solid #F6F5F4;
		border-collapse: collapse;
	}
	thead.memetable, th.memetable, td.memetable {
		padding: 6px;
	}
</style>

<?=form_open('test-joy-recs-vw','class="needs-validation-search" id="myfrmsearchrec" ');?>

    <div class="col-md-6 mb-1">
        <div class="input-group input-group-sm">
            <label class="input-group-text fw-bold" for="search">Search:</label>
            <input type="text" id="mytxtsearchrec" class="form-control form-control-sm" name="mytxtsearchrec" placeholder="Search" />
            <button type="submit" class="btn btn-dgreen btn-sm" style="background-color:#167F92; color:#fff;"><i class="bi bi-search"></i></button>
        </div>
    </div>
<?=form_close();?> <!-- end of ./form -->

    <div class="col-md-8">
        <?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
    </div>
	<div class="table-responsive">
		<div class="col-md-12 col-md-12 col-md-12">
			<table class="table table-condensed table-hover table-bordered table-sm " id="tbl_sub_items_bom">
				<thead>
					<tr>
                        <th><i class="bi bi bi-gear"></i></th>
                        <th><i class="bi bi bi-gear"></i></th>
                        <th>First Name</th>
                        <th>Last Name</th>

					</tr>
				</thead>
				<tbody>
                    <?php 
                        if($rlist != ""):
                        $nn = 1;
                        foreach($rlist as $row): 
                        $recid = $row['recid'];
                        $lname = $row['lname'];
                        
                    ?>
                    <tr>
                        <td class="text-center" nowrap>
							<?=anchor('test-joy/?recid=' . $recid, '<i class="bi bi bi-pencil"></i> Update ',' class="btn btn-outline-success p-1 pb-0 mebtnpt1 btn-sm"');?>
						</td>
                        <td class="text-center" nowrap>
                            <?=anchor('test-joy/?deleterecid=' . $recid, '<i class="bi bi bi-pencil"></i> Delete ',' class="btn btn-danger p-1 pb-0 mebtnpt1 btn-sm"');?>
						</td>
                        <td nowrap><?=$row['fname']?></td>
                        <td nowrap><?=$row['lname']?></td>
                        
                    </tr>
                    <?php
                        $nn++;
                        endforeach;
                        endif;
                    ?>
				</tbody>
			</table>
		</div>
	</div> <!-- end table-reponsive -->


<script type="text/javascript"> 

$("#mbtn_mn_Delete").click(function(e){
       
       try { 
           var recid = jQuery(this).attr('data-recid'); 
           var recid_txt = jQuery('#recid_txt').val();

           var mparam = {
                recid:recid,
                recid_txt:recid_txt
           };  

           console.log(mparam);
           
           $.ajax({ 
             type: "POST",
             url: '<?=site_url();?>test-joy-delete',
             context: document.body,
             data: eval(mparam),
             global: false,
             cache: false,
             success: function(data)  { 
               $(this).prop('disabled', false);
          // $.hideLoading();
          jQuery('#memsgtestent_bod').html(data);
          jQuery('#memsgtestent').modal('show');
          return false;
        },
        error: function() {
         alert('error loading page...');
        // $.hideLoading();
        return false;
      } 
    });

         } catch(err) {
           var mtxt = 'There was an error on this page.\n';
           mtxt += 'Error description: ' + err.message;
           mtxt += '\nClick OK to continue.';
           alert(mtxt);
   }  //end try
   return false; 
 });

function __myredirected_rsearch(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			


            //mytrx_sc/mndt_sc2_recs
            var mparam = { 
            	txtsearchedrec: txtsearchedrec,
            	mpages: mobj 
            };	
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>test-joy-recs-vw',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					$('#joyrecs').html(data);
					
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

				var mparam = {
					txtsearchedrec: txtsearchedrec,
					mpages: 1 
				};	

				jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>test-joy-recs-vw',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
					success: function(data)  { //display html using divID
						jQuery('#joyrecs').html(data);
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
						var txtsearchedrec = jQuery('#mytxtsearchrec').val();

						var mparam = {
							txtsearchedrec: txtsearchedrec,
							mpages: 1 
						};	
						
						jQuery.ajax({ // default declaration of ajax parameters
							type: "POST",
							url: '<?=site_url();?>test-joy-recs-vw',
							context: document.body,
							data: eval(mparam),
							global: false,
							cache: false,
							success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#joyrecs').html(data);
								
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

