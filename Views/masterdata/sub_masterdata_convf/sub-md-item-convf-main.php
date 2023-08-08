<?php 

/**
 *	File        : masterdata/sub_masterdata_bom/sub-md-item-convf-main.php
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


<main id="main">
    <div class="pagetitle">
    <h1>Sub Item Convertion to Main</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Sub Item Convertion to Main</li>
            </ol>
        </nav>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header mb-3">
                    <h3 class="h4 mb-0"> <i class="bi bi-pencil-square"></i> Entry</h3>
                </div>
                <div class="card-body">
                    <div class="pagetitle">
                        <h1>Current Sub Items in month of {August}</h1>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="row mb-3">
                                <div class="table-responsive">
                                    <div class="col-md-12 col-md-12 col-md-12">
                                        <table class="table table-condensed table-hover table-bordered table-sm " id="tbl_convf">
                                            <thead>
                                                <tr>
                                                    <th>Sub Itemcode</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Cost</th>
                                                    <th>Price</th>
                                                    <th>Convf</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    if($rlist != ""):
                                                    $nn = 1;
                                                    foreach($rlist as $row): 
														$ITEMC = $row['ITEMC'];
														$ITEM_DESC = $row['ITEM_DESC'];
														$MQTY_CORRECTED = $row['MQTY_CORRECTED'];
														$MCOST = $row['MCOST'];
														$MARTM_PRICE = $row['MARTM_PRICE'];
														$ART_NCONVF = $row['ART_NCONVF'];
                                                ?>
                                                <tr>
                                                    <td nowrap><input type="text" class="form-control form-control-sm itemc-input" value="<?=$ITEMC;?>"/></td>
                                                    <td nowrap><input type="text" class="form-control form-control-sm desc-input" value="<?=$ITEM_DESC;?>"/></td>
                                                    <td nowrap><input type="text" class="form-control form-control-sm qty-input" value="<?=$MQTY_CORRECTED;?>"/></td>
                                                    <td nowrap><input type="text" class="form-control form-control-sm cost-input" value="<?=$MCOST;?>"/></td>
                                                    <td nowrap><input type="text" class="form-control form-control-sm price-input" value="<?=$MARTM_PRICE;?>"/></td>
                                                    <td nowrap><input type="text" class="form-control form-control-sm convf-input" value="<?=$ART_NCONVF;?>"/></td>
                                                </tr>
                                                <?php
                                                    $nn++;
                                                    endforeach;
                                                    endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-8 d-flex justify-content-end">
                                    <?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <button id="mbtn_mn_Save" type="submit" class="btn btn-success btn-sm">Save</button>
                                    <?=anchor('sub-item-convf', '<i class="bi bi-arrow-repeat"></i>',' class="btn btn-outline-success btn-sm" ');?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div> 

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header mb-3">
                    <h3 class="h4 mb-0"> <i class="bi bi-pencil-square"></i> Sub BOM Records</h3>
                </div>
                <div class="card-body">
                    <div id="subitems" class="text-center p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">
                        <?php

                        ?> 
                    </div> 
                </div>
                </div> 
            </div>
        </div>
    </div> 

</main>
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
				url: '<?=site_url();?>sub-items-convf-vw',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					$('#subitems').html(data);
					
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
					url: '<?=site_url();?>sub-items-convf-vw',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
					success: function(data)  { //display html using divID
						jQuery('#subitems').html(data);
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
							url: '<?=site_url();?>sub-items-convf-vw',
							context: document.body,
							data: eval(mparam),
							global: false,
							cache: false,
							success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#subitems').html(data);
								
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

	__mysys_apps.mepreloader('mepreloaderme',false);

	$("#mbtn_mn_Save").click(function(e){
       
       try { 

         var rowCount1 = jQuery('#tbl_convf tr').length;
         var adata1 = [];

         for(aa = 1; aa < rowCount1; aa++) { 
           var clonedRow = jQuery('#tbl_convf tr:eq(' + aa + ')').clone(); 
           var mitemc = jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
           var mdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
           var mqty = jQuery(clonedRow).find('input[type=text]').eq(2).val();
           var mcost = jQuery(clonedRow).find('input[type=text]').eq(3).val(); 
           var mprice = jQuery(clonedRow).find('input[type=text]').eq(4).val(); 
		   var mconvf = jQuery(clonedRow).find('input[type=text]').eq(5).val(); 

           var mdata = mitemc + 'x|x' + mdesc + 'x|x' + mqty + 'x|x' + mcost + 'x|x' + mprice + 'x|x' + mconvf;
           adata1.push(mdata);
           }

           var mparam = {
             adata1: adata1
           };  

           console.log(mparam);
           
           $.ajax({ 
             type: "POST",
             url: '<?=site_url();?>sub-items-convf-save',
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

