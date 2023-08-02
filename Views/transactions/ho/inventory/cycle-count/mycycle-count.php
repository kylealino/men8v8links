<?php
/* =================================================
 * Author      : Oliver Sta Maria
 * Date Created: April 27, 2023
 * Module Desc : Inventory Cyle Count
 * File Name   : transactions/ho/inventory/mycycle-count.php
 * Revision    : Migration to Php8 Compatability 
*/

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myivty = model('App\Models\MyInventoryModel');

$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$meuatkn = $request->getVar('meuatkn');
echo view('templates/meheader01');
$txtcyctag = '';
$txtyears = '';
$txtmonths = '';
$months = $myivty->lk_Active_Months($db_erp);
$years = $myivty->lk_Active_Year($db_erp);
$cyc_tag = $myivty->lk_Active_Cycle_Tag($db_erp);

?>
<main id="main" class="main">
	<div class="pagetitle">
		<h1>Inventory Management - Physical Count</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?=site_url();?>">Home</a></li>
				<li class="breadcrumb-item">Inventory</li>
				<li class="breadcrumb-item active">Cycle Count</li>
			</ol>
		</nav>
		<hr>
	</div> <!-- End Page Title -->
	<section class="section">
		<div class="row">
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Count Tag:</span>
					</div>
					<div class="col-sm-9">
						<?=$mylibzsys->mypopulist_2($cyc_tag,$txtcyctag,'fld_upld_cyctag','class="form-control form-control-sm" ','','');?>
					</div>
				</div> <!-- end Count Tag -->
				
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Branch:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" data-mtknid="" id="fld_cycbranch" name="fld_cycbranch" value="" required />
					</div>
				</div> <!-- end branch -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Year:</span>
					</div>
					<div class="col-sm-9">
						<?=$mylibzsys->mypopulist_2($years,$txtyears,'fld_years','class="form-control form-control-sm" ','','');?>
					</div>
				</div> <!-- end Year -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Month:</span>
					</div>
					<div class="col-sm-9">
						<?=$mylibzsys->mypopulist_2($months,$txtmonths,'fld_months','class="form-control form-control-sm" ','','');?>
					</div>
				</div> <!-- end Month -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Date:</span>
					</div>
					<div class="col-sm-9">
						<input type="date" class="form-control form-control-sm" name="fld_cycdate" id="fld_cycdate" value="" required/>
					</div>
				</div> <!-- end Date -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Valid File:</span>
					</div>
					<div class="col-sm-9">
						<button class="btn btn-info btn-sm btn-danger" id="mebtn_cycfile"><span class="bi bi-cloud-upload"> </span>Browse</button>
						<span id="__mefilesme"></span>
						<input accept="text/plain,.csv,.txt" id="__upld_file_cyc" type="file" style="display: none;" />
					</div>
				</div> <!-- end Valid File -->
				<div class="row mb-3">
					<div class="col-sm-12">
						<input class="btn btn-success btn-sm" type="button" id="__mbtn_cyc_simpleupld" value="Upload/Process">
						<input class="btn btn-warning btn-sm" type="button" id="__cyc_delete" value="Remove Previously Uploaded">
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-12">
						<input class="btn btn-success btn-sm" type="button" id="__mbtn_cyc_uploaded_editing" value="Uploaded Editing Records...">
						<button class="btn btn-success btn-sm" id="__mbtn_proc_bal" name="__mbtn_proc_bal" type="button">Process Balance</button>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="alert alert-warning" role="alert">
					<h4 class="alert-heading">Reminder:</h4>
					<p>Upload a valid .csv/.txt file and tab delimited only.</p>
					<hr>
					<p class="mb-0"><input class="btn btn-success btn-sm" type="button" id="__cyc_format" value="Template Excel File..."></p>
				</div>							
			</div> <!-- end 2nd col-md-6 -->
		</div> <!-- end row metblentry-font 1st-->
		<div class="row">
			<div class="col-lg-12 col-md-12 mb-md-0 mb-4">
				<div class="card">
					<ul class="nav nav-tabs nav-tabs-bordered" id="myTabCycleCount" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="__mycycure-tab" data-bs-toggle="tab" data-bs-target="#__mycycure" type="button" role="tab" aria-controls="__mycycure" aria-selected="true">Uploaded Records...</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="__mycycpurc-tab" data-bs-toggle="tab" data-bs-target="#__mycycpurc" type="button" role="tab" aria-controls="__mycycpurc" aria-selected="false">Posting Uploaded Records</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="__mycycpur-tab" data-bs-toggle="tab" data-bs-target="#__mycycpur" type="button" role="tab" aria-controls="__mycycpur" aria-selected="false">Posted Uploaded Records</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="__myprocbal-tab" data-bs-toggle="tab" data-bs-target="#__myprocbal" type="button" role="tab" aria-controls="__myprocbal" aria-selected="false">Processed Balance</button>
						</li>
					</ul>
					<div class="tab-content" id="myTabCycleCountContent">
						<div class="tab-pane fade show active" id="__mycycure" role="tabpanel" aria-labelledby="__mycycure-tab">
						</div>
						<div class="tab-pane fade" id="__mycycpurc" role="tabpanel" aria-labelledby="__mycycpurc-tab"></div>
						<div class="tab-pane fade" id="__mycycpur" role="tabpanel" aria-labelledby="__mycycpur-tab"></div>
						<div class="tab-pane fade" id="__myprocbal" role="tabpanel" aria-labelledby="__myprocbal-tab"></div>
					</div>
				</div>
			</div>			
		</div> <!-- end row metblentry-font 2nd-->
	</section> <!-- section -->
<?php
echo $mylibzsys->memypreloader01('mepreloaderme');
echo $mylibzsys->memsgbox3('memsgme','System Message','...');
?>
</main>
<?php
echo view('templates/mefooter01');
?>
<script type="text/javascript"> 
	__mysys_apps.mepreloader('mepreloaderme',false);
	
	jQuery('#fld_cycbranch')
		// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).data( 'autocomplete' ).menu.active ) { 
				event.preventDefault();
		}
		if( event.keyCode === jQuery.ui.keyCode.TAB ) {
			event.preventDefault();
		}
	})
	.autocomplete({
			minLength: 0,
			source: '<?= site_url(); ?>company-branch-ua',
			focus: function() {
					// prevent value inserted on focus
					return false;
				},
				search: function(oEvent, oUi) {
					var sValue = jQuery(oEvent.target).val();
					jQuery(this).autocomplete('option', 'source', '<?=site_url();?>company-branch-ua'); 
				},
				select: function( event, ui ) {
					var terms = ui.item.value;
					var mtkn_comp = ui.item.mtkn_comp;
					var mtknr_rid = ui.item.mtknr_rid;
					var mtkn_brnch = ui.item.mtkn_brnch;
					jQuery('#fld_cycbranch').val(terms);
					jQuery('#fld_cycbranch').attr('data-mtknid',mtknr_rid);
					jQuery(this).autocomplete('search', jQuery.trim(terms));
					return false;
				}
			})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	}); // end fld_sc2branch_s	
	
	jQuery('#mebtn_cycfile').click(function() { 
		try { 
			jQuery('#__upld_file_cyc').click();
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			
			return false;

		}  //end try					
	});  //end mebtn_cycfile click event 
		
	jQuery('#__upld_file_cyc').on('change',function() { 
		try { 
			//alert(this.files.length + ' ' + this.files[0].name);
			if(this.files.length > 0) { 
				var mefilecontent = "<ul class=\"list-group mt-2\">";
				for(aa = 0; aa < this.files.length; aa++) { 
					mefilecontent += "<li class=\"list-group-item\"><i class=\"bi bi-star me-1 text-success\"></i> " + this.files[aa].name + "</li>"; 
				}
				mefilecontent += "</ul";
				jQuery('#__mefilesme').html(mefilecontent);
			}
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			
			return false;
		}  //end try					
	}); //end __upld_file_cyc change event
		
	
	jQuery('#__mbtn_cyc_simpleupld').on('click',function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var fld_months = jQuery('#fld_months').val();
			var fld_years = jQuery('#fld_years').val();
			var fld_cycbranch = jQuery('#fld_cycbranch').val();
			var mtknbrid = jQuery('#fld_cycbranch').attr('data-mtknid');
			var fld_upld_cyctag = jQuery('#fld_upld_cyctag').val();
			var fld_cycdate = jQuery('#fld_cycdate').val();
			var my_data = new FormData();
			var mfileattach = '__upld_file_cyc';
			var mfiles = document.getElementById(mfileattach);
			var filesCount = 0;
			if(mfiles.files.length > 0) { 
				jQuery.each(mfiles.files, function(k,file){ 
					my_data.append('mefiles[]', file);
					filesCount++;
				});
			}
			
			if(jQuery.trim(fld_upld_cyctag) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Cycle Count Tag is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
				
			}

			if(jQuery.trim(fld_cycbranch) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Branch is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
				
			}

			if(jQuery.trim(fld_years) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Year is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
			} 
			
			if(jQuery.trim(fld_months) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Month is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
			}
			               
			if(filesCount == 0) { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Valid File is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
				
			}
			
			my_data.append('fld_upld_cyctag', fld_upld_cyctag);
			my_data.append('fld_cycbranch', fld_cycbranch);
			my_data.append('mtknbrid', mtknbrid);
			my_data.append('fld_years', fld_years);
			my_data.append('fld_months', fld_months);
			my_data.append('fld_cycdate', fld_cycdate);
			
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-cycle-count-proc-upld-files',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsgme_bod').html(data);
					jQuery('#memsgme').modal('show');
					return false;
				},
				error: function() { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 			
			jQuery('#meyn_salesdepo_no').html('Close');
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
		}  //end try 
	});  //end meyn_salesdepo_yes
	
	wg_mycycpurc();
	function wg_mycycpurc() { 
		try { 
			var my_data = new FormData();
			my_data.append('meako','BronOLexus');
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-cycle-count-posting-uploaded',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					jQuery('#__mycycpurc').html(data);
					return false;
				},
				error: function() { 
					alert('error loading page...');
					return false;
				} 
			}); 
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: wg_mycycpurc ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
		}  //end try 
	} // end wg_mycycpurc

	jQuery('#__mbtn_cyc_uploaded_editing').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var fld_months = jQuery('#fld_months').val();
			var fld_years = jQuery('#fld_years').val();
			var fld_cycbranch = jQuery('#fld_cycbranch').val();
			var mtknbrid = jQuery('#fld_cycbranch').attr('data-mtknid');
			
			if(jQuery.trim(fld_cycbranch) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Branch is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
				
			}

			if(jQuery.trim(fld_years) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Year is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
			} 
			
			if(jQuery.trim(fld_months) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Month is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
			}
			var my_data = new FormData();
			
			my_data.append('fld_cycbranch', fld_cycbranch);
			my_data.append('mtknbrid', mtknbrid);
			my_data.append('fld_years', fld_years);
			my_data.append('fld_months', fld_months);
			
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-cycle-count-editing-uploaded-inquiry',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					jQuery('#__mycycure').html(data);
					__mysys_apps.mepreloader('mepreloaderme',false);
					return false;
				},
				error: function() { 
					__mysys_apps.mepreloader('mepreloaderme',false);					
					alert('error loading page...');
					return false;
				} 
			}); 
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: wg_mycycpurc ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
		}  //end try 
	});  //end __mbtn_cyc_uploaded_editing

	//wg_mycycure();
	function wg_mycycure() { 
		try { 
			var my_data = new FormData();
			my_data.append('meako','BronOLexus');
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-cycle-count-editing-uploaded-inquiry',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					jQuery('#__mycycure').html(data);
					return false;
				},
				error: function() { 
					alert('error loading page...');
					return false;
				} 
			}); 
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: wg_mycycpurc ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
		}  //end try 
	} // end wg_mycycpurc
	
	jQuery('#__mbtn_proc_bal').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var my_data = new FormData();
			
			var fld_branch = jQuery('#fld_cycbranch').val();
			var bid_mtknattr = jQuery('#fld_cycbranch').attr('data-mtknid');
			
			if(jQuery.trim(fld_branch) == '') { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Branch is required!!!</strong></div>");
				jQuery('#memsgme').modal('show');
				return false;
				
			}
			
			my_data.append('meako','BronOLexus');
			my_data.append('fld_branch',fld_branch);
			my_data.append('bid_mtknattr',bid_mtknattr);
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-proc-balance',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					jQuery('#__myprocbal').html(data);
					__mysys_apps.mepreloader('mepreloaderme',false);
					return false;
				},
				error: function() { 
					alert('error loading page...');
					return false;
				} 
			}); 			
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: wg_mycycpurc ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
		}  //end try 
		
	}); //end process balance
	
</script>
