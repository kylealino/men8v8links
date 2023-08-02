<?php
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$db_erp = $this->myusermod->mydbname->medb(0);
$cuser = $this->myusermod->mysys_user();
$mpw_tkn = $this->myusermod->mpw_tkn();

$memodname = '__ivtyrptsumma__';
$memodname2 = '__ivtyrptconso__';

?>
<div class="row mt-3 ms-1 me-1">
	<div class="col-md-8">
		<div class="row mb-3">
			<div class="col-sm-8">
				<button class="btn btn-success btn-sm" id="btn_<?=$memodname;?>proc" name="btn_<?=$memodname;?>proc" type="submit">Process Summary...</button>
				<button class="btn btn-success btn-sm" id="btn_<?=$memodname2;?>proc" name="btn_<?=$memodname2;?>proc" type="submit">Process Conso...</button>
			</div>
		</div>	
	</div>
</div>
<div class="row mt-2 ms-1 me-1">
	<div class="col-md-12">
		<div class="card" id="wgivtysummary">
		</div>
	</div>
</div>  <!-- end row mt-1 ms-1 me-1 2nd -->
<script type="text/javascript"> 
	jQuery('#btn_<?=$memodname;?>proc').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mparam = {
				medata: 'meako'
			}
			jQuery.ajax({ // default declaration of ajax parameters
				url: '<?=site_url()?>myinventory-report-summary',
				method:"POST",
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wgivtysummary').html(data);
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
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}
	});	
	
	jQuery('#btn_<?=$memodname2;?>proc').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mparam = {
				medata: 'meako'
			}
			jQuery.ajax({ // default declaration of ajax parameters
				url: '<?=site_url()?>myinventory-report-branch-conso',
				method:"POST",
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wgivtysummary').html(data);
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
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}
	});		
</script>
