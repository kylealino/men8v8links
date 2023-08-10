<?php 

/**
 *	File        : masterdata/sub_masterdata/sub-md-item-recs.php
 *  Auhtor      : Kyle Alino
 *  Date Created: Jul 28, 2023
 * 	last update : Jul 28, 2023
 * 	description : Sub Masterdata Records
 */
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$_branch_name = $request->getVar('branch_name');
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

<?=form_open('sub-inv-recs-vw','class="needs-validation-search" id="myfrmsearchrec" ');?>
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
	<input type="hidden" name="_branch_name" value="<?=$_branch_name;?>">
	<div class="table-responsive">
		<div class="col-md-12 col-md-12 col-md-12">
			<table class="table table-condensed table-hover table-bordered table-sm " id="tbl_sub_items_inv">
				<thead>
					<tr>
						<th class="text-center">
						</th>
						<th class="text-center">Itemcode</th>
						<th class="text-center">Item Qty</th>
						<th class="text-center">Item Barcode</th>
						<th class="text-center">Item Price</th>

					</tr>
				</thead>
				<tbody>
					<?php 
						if($rlist != ""):
						$nn = 1;
						foreach($rlist as $row): 
					?>
					<tr>
						<td class="text-center" nowrap>
							<?=$nn;?>
						</td>
						<td nowrap class="text-center"><input type="text" name="SO_ITEMCODE" id="SO_ITEMCODE" class="bg-white text-black text-center" style="border:none;" size="20" value="<?=$row['SO_ITEMCODE'];?>" disabled></td>
						<td nowrap class="text-center"><input type="text" name="SO_QTY" id="SO_QTY" class="bg-white text-black text-center" style="border:none;" size="10" value="<?=$row['SO_QTY'];?>" disabled></td>
						<td nowrap class="text-center"><input type="text" name="SO_BARCODE" id="SO_BARCODE" class="bg-white text-black text-center" style="border:none;" size="20" value="<?=$row['SO_BARCODE'];?>" disabled></td>
						<td nowrap class="text-center"><input type="text" name="SO_NET" id="SO_NET" class="bg-white text-black text-center" style="border:none;" size="10" value="<?=$row['SO_NET'];?>" disabled></td>
					</tr>
					<?php
							$nn++;
										endforeach;
						endif;
					?>
				</tbody>
				
			</table>
			<div class="form-row">
				<button type="button" class="btn bg-success btn-sm text-white" id="mbtn_mn_Save">Process Balance</button>  
				<?=anchor('sub-item-inv', '<i class="bi bi-arrow-repeat"></i>',' class="btn outlined-element border border-primary btn-sm" ');?>
			</div>
		</div>
	</div> <!-- end table-reponsive -->
    <?php
    echo $mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
    echo $mylibzsys->memypreloader01('mepreloaderme');
    echo $mylibzsys->memsgbox1('memsgtestent','System Alert','...');
    ?>  
<script type="text/javascript"> 

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
				url: '<?=site_url();?>sub-inv-recs-vw',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					$('#salesrecs').html(data);
					
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
					url: '<?=site_url();?>sub-inv-recs-vw',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
					success: function(data)  { //display html using divID
						jQuery('#salesrecs').html(data);
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
							url: '<?=site_url();?>sub-inv-recs-vw',
							context: document.body,
							data: eval(mparam),
							global: false,
							cache: false,
							success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#salesrecs').html(data);
								
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

	$("#mbtn_mn_Save").click(function(e){
    try { 

          var _branch_name = jQuery('#_branch_name').val();
          var rowCount1 = jQuery('#tbl_sub_items_inv tr').length;
          var adata1 = [];
          var mdata = '';
          var ninc = 0;

          for(aa = 1; aa < rowCount1; aa++) { 
                var clonedRow = jQuery('#tbl_sub_items_inv tr:eq(' + aa + ')').clone(); 
                var mitemc = jQuery(clonedRow).find('input[type=text]').eq(0).val();
                var mqty = jQuery(clonedRow).find('input[type=text]').eq(1).val();
                var mbrcde = jQuery(clonedRow).find('input[type=text]').eq(2).val();
                var mprice = jQuery(clonedRow).find('input[type=text]').eq(3).val();


                mdata = mitemc + 'x|x' + mqty + 'x|x' + mbrcde + 'x|x' + mprice;
                adata1.push(mdata);

			}  //end for

          var mparam = {
            adata1: adata1
          };  


      $.ajax({ 
        type: "POST",
        url: '<?=site_url();?>sub-inv-save',
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
</script>

