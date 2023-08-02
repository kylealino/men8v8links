<?php
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myivty = model('App\Models\MyInventoryModel');

$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$txtsearchedrec = '';
?>
<div class="row mt-2 ms-1 me-1">
    <div class="col-md-8">
		<div class="input-group input-group-sm">
			<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
			<input type="text" class="form-control form-control-sm" name="mytxtsearchrec" placeholder="Search Control Number" id="txtsearchedrec" aria-label="Search-Deposit" aria-describedby="mebtnGroupAddon" value="<?=$txtsearchedrec;?>" required/>
			<div class="invalid-feedback">Please fill out this field.</div>
			<button type="submit" class="btn btn-success btn-sm" id="mbtn_mycycpurc_recs_search"><i class="bi bi-search"></i></button>
			<button type="submit" class="btn btn-success btn-sm" id="mbtn_mesalesdepo_search">Reset</button>
		</div>
	</div>
</div>
<div class="row mt-2 ms-1 me-1" id="__mycycpurc_recs">
</div>
<script type="text/javascript"> 
	jQuery('#mbtn_mycycpurc_recs_search').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var my_data = new FormData();
			my_data.append('meako','BronOLexus');
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-cycle-count-posting-uploaded-recs',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					jQuery('#__mycycpurc_recs').html(data);
					__mysys_apps.mepreloader('mepreloaderme',false);
					return false;
				},
				error: function() { 
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
		}  //end try 
	});
</script>

