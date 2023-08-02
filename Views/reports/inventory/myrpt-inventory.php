<?php

$mymdarticle = model('App\Models\MyMDArticleModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();


if(!$myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0007','0001')) { 
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
                        <button class="nav-link active" id="mystockcard-tab" data-bs-toggle="tab" data-bs-target="#mystockcard" type="button" role="tab" aria-controls="mystockcard" aria-selected="true">Stock Card</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#artcprofile" type="button" role="tab" aria-controls="artcprofile" aria-selected="false">Monthly Itemized</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Contact</button>
                    </li>
                </ul>
                <div class="tab-content" id="myRptInventoryContent">
                    <div class="tab-pane fade show active" id="mystockcard" role="tabpanel" aria-labelledby="mystockcard-tab">
                    </div>
                    <div class="tab-pane fade" id="artcprofile" role="tabpanel" aria-labelledby="profile-tab">

                    </div>
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>
                </div>
            </div>
        </div>
    </div>

    <?php
    echo $mylibzsys->memypreloader01('mepreloaderme');
    echo $mylibzsys->memsgbox3('memsgrptivty','System Alert','...');
} //end ua_mod_access_verify
    ?>

</div>  <!-- end main -->

<script type="text/javascript"> 
	mywg_stockcard_scr_load();
    function mywg_stockcard_scr_load(mtkn_etr) { 
        var ajaxRequest;
        
        ajaxRequest = jQuery.ajax({
                url: "<?=site_url();?>myreport-stockcard",
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
</script>
