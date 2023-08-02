<?php
$this->mymdarticle = model('App\Models\MyMDArticleModel');
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$this->db_erp = $this->myusermod->mydbname->medb(0);
$this->cuser = $this->myusermod->mysys_user();
$this->mpw_tkn = $this->myusermod->mpw_tkn();
$abranch = $this->mymdarticle->Artm_Branches();
$meartmbranch = '';
?>
<div class="row mt-2 ms-1 me-1">
	<div class="col-md-4">		
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Branch</span>
			</div>
			<div class="col-sm-9">
				<?=$this->mylibzsys->mypopulist_2($abranch,$meartmbranch,'meartmbranch','class="form-control form-control-sm" ','','');?>
			</div>
		</div>
	</div>
</div>
<div class="row mt-2 ms-1 me-1" id="wg_artm_branch_recs">
</div>
<script type="text/javascript"> 
	jQuery('#meartmbranch').change(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var meartmbranch = jQuery(this).val();
			var mparam = { mebcode: meartmbranch };
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>mymd-article-master-poslink-branch-recs',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wg_artm_branch_recs').html(data);
					return false;
				},
				error: function() { // display global error on the menu function
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}	
			});				
		} catch (err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
		} //end try				
	});
</script>
