<?php

$mymdarticle = model('App\Models\MyMDArticleModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();


if(!$myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0007','0002')) { 
	echo "
	<main id=\"main\">
		<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
	</main>
	";
	
} else {

?>

<main id="main">

    <div class="pagetitle">
        <h1>Sales</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=site_url();?>">Sales</a></li>
                <li class="breadcrumb-item active">Sales Out Details</li>
            </ol>
            </nav>
    </div><!-- End Page Title -->

    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-bordered" id="myTabArticle" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sales-daily-itemized-tab" data-bs-toggle="tab" data-bs-target="#sales-daily-itemized" type="button" role="tab" aria-controls="sales-daily-itemized" aria-selected="true">Daily Itemized</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#artcprofile" type="button" role="tab" aria-controls="artcprofile" aria-selected="false">Monthly Itemized</button>
                    </li>
					<?php
					if($myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0007','000205')) { 
					?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sales-daily-tally-tab" data-bs-toggle="tab" data-bs-target="#sales-daily-tally" type="button" role="tab" aria-controls="sales-daily-tally" aria-selected="false">Daily Tally</button>
                    </li>
                    <?php
					} //end ua_mod_access_verify '04','0007','000205'
                    ?>

					<?php
					if($myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0007','000207')) { 
					?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sales-AccPOS-tally-tab" data-bs-toggle="tab" data-bs-target="#sales-AccPOS-tally" type="button" role="tab" aria-controls="sales-AccPOS-tally" aria-selected="false">Accounting POS Tally</button>
                    </li>
                    <?php
					} //end ua_mod_access_verify '04','0007','000207'
                    ?>
                    
                </ul>
                <div class="tab-content" id="myTabArticleContent">
                    <div class="tab-pane fade show active" id="sales-daily-itemized" role="tabpanel" aria-labelledby="sales-daily-itemized-tab">
                    </div>
                    <div class="tab-pane fade" id="artcprofile" role="tabpanel" aria-labelledby="profile-tab">
                    </div>
					<?php
					if($myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0007','000205')) { 					
					?>
                    <div class="tab-pane fade" id="sales-daily-tally" role="tabpanel" aria-labelledby="sales-daily-tally-tab"></div>
                    <?php
					} //end ua_mod_access_verify '04','0007','000205' content tab 
                    ?>
					<?php
					if($myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0007','000207')) { 	
					?>
                    <div class="tab-pane fade" id="sales-AccPOS-tally" role="tabpanel" aria-labelledby="sales-AccPOS-tally-tab"></div>
                    <?php
					} //end ua_mod_access_verify '04','0007','000207' content tab 
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    echo $mylibzsys->memypreloader01('mepreloaderme');
    echo $mylibzsys->memsgbox1('memsgsalesoutdetl','System Alert','...');
} //end ua_mod_access_verify
    ?>

</div>  <!-- end main -->

<script type="text/javascript"> 
    mywg_salesout_scr_load();
    function mywg_salesout_scr_load(mtkn_etr) { 
        var ajaxRequest;
        
        ajaxRequest = jQuery.ajax({
                url: "<?=site_url();?>sales-out-details-tab-daily",
                type: "post",
                data: { mtkn_etr: mtkn_etr}
            });

        // Deal with the results of the above ajax call
        ajaxRequest.done(function (response, textStatus, jqXHR) {
            jQuery('#sales-daily-itemized').html(response);

            // and do it again
            //setTimeout(get_if_stats, 5000);
        });
    }
    
    <?php
    if ($myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0007','000205')):
    ?>
    mywg_salesout_daily_tally();
    function mywg_salesout_daily_tally() { 
		try { 
			var mparam = {me: 'n8v8'};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>sales-out-tally-daily',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						jQuery('#sales-daily-tally').html(data);
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
	
    <?php
    if ($myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0007','000207')):
    ?>
    mywg_AcctPOS_tally();
    function mywg_AcctPOS_tally() { 
		try { 
			var mparam = {me: 'n8v8'};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>sales-out-Acct-POS-tally',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						jQuery('#sales-AccPOS-tally').html(data);
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
	
    __mysys_apps.mepreloader('mepreloaderme',false);
</script>
