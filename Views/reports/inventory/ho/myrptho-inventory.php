<?php

$mymdarticle = model('App\Models\MyMDArticleModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

echo view('templates/meheader01');

if(!$myusermod->ua_mod_access_verify($db_erp,$cuser,'02','0004','000707')) { 
	echo "
	<main id=\"main\">
		<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
	</main>
	";
	
} else {

?>

<main id="main">

    <div class="pagetitle">
        <h1>Inventory Report</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=site_url();?>">Report</a></li>
                <li class="breadcrumb-item active">Inventory</li>
            </ol>
            </nav>
    </div><!-- End Page Title -->

    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-bordered" id="myRptInventory" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="mystockcard-tab" data-bs-toggle="tab" data-bs-target="#mystockcard" type="button" role="tab" aria-controls="mystockcard" aria-selected="true">Detailed Report</button>
                    </li>
                    <?php
                    if($myusermod->ua_mod_access_verify($db_erp,$cuser,'02','0004','00070705')):
                    ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ivtyitemized-tab" data-bs-toggle="tab" data-bs-target="#ivtyitemized" type="button" role="tab" aria-controls="ivtyitemized" aria-selected="false">Itemized</button>
                    </li>
                    <?php 
                    endif;
                    ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ivtysumma-tab" data-bs-toggle="tab" data-bs-target="#ivtysumma" type="button" role="tab" aria-controls="ivtysumma" aria-selected="false">Summary</button>
                    </li>
                </ul>
                <div class="tab-content" id="myRptInventoryContent">
                    <div class="tab-pane fade show active" id="mystockcard" role="tabpanel" aria-labelledby="mystockcard-tab">
                    </div>
                    <?php
                    if($myusermod->ua_mod_access_verify($db_erp,$cuser,'02','0004','00070705')):
                    ?>
                    <div class="tab-pane fade" id="ivtyitemized" role="tabpanel" aria-labelledby="ivtyitemized-tab">
                    <?php 
                    endif;
                    ?>
                    </div>
                    <div class="tab-pane fade" id="ivtysumma" role="tabpanel" aria-labelledby="ivtysumma-tab">
						<?=view('reports/inventory/ho/myrptho-inventory-summary')?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    echo $mylibzsys->memypreloader01('mepreloaderme');
    echo $mylibzsys->memsgbox3('memsgrptivty','System Alert','...');
    ?>

</div>  <!-- end main -->

<?php
} //end ua_mod_access_verify

echo view('templates/mefooter01');
?>

<script type="text/javascript"> 
	mywg_stockcard_scr_load();
    function mywg_stockcard_scr_load(mtkn_etr) { 
        var ajaxRequest;
        
        ajaxRequest = jQuery.ajax({
                url: "<?=site_url();?>myinventory-report-detailed",
                type: "post",
                data: { mtkn_etr: mtkn_etr}
            });

        // Deal with the results of the above ajax call
        ajaxRequest.done(function (response, textStatus, jqXHR) {
            jQuery('#mystockcard').html(response);

            // and do it again
            //setTimeout(get_if_stats, 5000);
        });
    }

    __mysys_apps.mepreloader('mepreloaderme',false);
    
    <?php
    if($myusermod->ua_mod_access_verify($db_erp,$cuser,'02','0004','00070705')):
    ?>
    mywg_ivty_itemized();
    function mywg_ivty_itemized() { 
		try { 
			var mparam = {me: 'n8v8'};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>myreport-inventory-itemized',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						jQuery('#ivtyitemized').html(data);
						return false;
				},
				error: function() { // display global error on the menu function 
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
	} // end mywg_salesout_daily_tally
	<?php
	endif;
	?>
	    
</script>
