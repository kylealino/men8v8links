<?php
$request = \Config\Services::request();
$mymdquotarate = model('App\Models\MyMDQuotaRateModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
//var_dump($mymdarticle);
$maction = $request->getVar('maction');
$mtkn_etr = $request->getVar('mtkn_etr');

?>

<main id="main">

    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Quota Rate Master Data</li>
            </ol>
            </nav>
    </div><!-- End Page Title -->

    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-bordered" id="myTabArticle" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="qprlist-tab" data-bs-toggle="tab" data-bs-target="#qprlist" type="button" role="tab" aria-controls="qprlist" aria-selected="true">Quota Piece Rate Records</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="qprprofile-tab" data-bs-toggle="tab" data-bs-target="#qprprofile" type="button" role="tab" aria-controls="qprprofile" aria-selected="false">Quota Piece Rate Entry</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="qprlist" role="tabpanel" aria-labelledby="qprlist-tab">
                    <?php
                        $data = $mymdquotarate->view_recs(1,20);
                        echo view('masterdata/md-quota-rate-recs',$data);
                    ?>
                    </div>
                    <div class="tab-pane fade" id="qprprofile" role="tabpanel" aria-labelledby="qprprofile-tab">
                </div>
            </div>
        </div>
    </div>

    <?php
    echo $mylibzsys->memypreloader01('mepreloaderme');
    ?>

</div>  <!-- end main -->

<script type="text/javascript"> 
    mywg_qpr_profile_load('<?=$mtkn_etr;?>');
    function mywg_qpr_profile_load(mtkn_etr) {
        var ajaxRequest;
        var maction = '<?=$maction;?>';
        ajaxRequest = jQuery.ajax({
                url: "<?=site_url();?>mymd-qpr-profile",
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                type: "post",
                data: { mtkn_etr: mtkn_etr, maction: maction}
            });

        // Deal with the results of the above ajax call
        ajaxRequest.done(function (response, textStatus, jqXHR) {
            jQuery('#qprprofile').html(response);

            // and do it again
            //setTimeout(get_if_stats, 5000);
        });
    }
    __mysys_apps.mepreloader('mepreloaderme',false);
    
    function set_qprrofile() { 
		jQuery('#qprlist').removeClass("show");
		jQuery('#qprlist').removeClass("active");
		jQuery('#qprlist-tab').removeClass("active");
		
		jQuery('#qprprofile').addClass("show");
		jQuery('#qprprofile').addClass("active");
		jQuery('#qprprofile-tab').addClass("active");
	} //end set_qprrofile
<?php
if($maction == 'A_REC' || !empty($mtkn_etr)) { 
?>
set_qprrofile();
<?php
}
?>

</script>
