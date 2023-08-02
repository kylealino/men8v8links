<?php
$request = \Config\Services::request();
$mymdqpr = model('App\Models\MyMDQprEmployeesModel');
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
                <li class="breadcrumb-item active">Quota Piece Rate Employees Master Data</li>
            </ol>
            </nav>
    </div><!-- End Page Title -->

    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-bordered" id="myTabQPREmp" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="qpremplist-tab" data-bs-toggle="tab" data-bs-target="#qpremplist" type="button" role="tab" aria-controls="qpremplist" aria-selected="true">QPR Employee Records</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="qprempprofile-tab" data-bs-toggle="tab" data-bs-target="#qprempprofile" type="button" role="tab" aria-controls="qprempprofile" aria-selected="false">QPR Employee Profile</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabQPREmpContent">
                    <div class="tab-pane fade show active" id="qpremplist" role="tabpanel" aria-labelledby="qpremplist-tab">
                    <?php
                        $data = $mymdqpr->view_recs(1,20);
                        echo view('masterdata/md-qpr-employees-recs',$data);
                    ?>
                    </div>
                    <div class="tab-pane fade" id="qprempprofile" role="tabpanel" aria-labelledby="qprempprofile-tab">
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
            jQuery('#qprempprofile').html(response);

            // and do it again
            //setTimeout(get_if_stats, 5000);
        });
    }
    __mysys_apps.mepreloader('mepreloaderme',false);
    
    function set_qprprofile() { 
		jQuery('#qpremplist').removeClass("show");
		jQuery('#qpremplist').removeClass("active");
		jQuery('#qpremplist-tab').removeClass("active");
		
		jQuery('#qprempprofile').addClass("show");
		jQuery('#qprempprofile').addClass("active");
		jQuery('#qprempprofile-tab').addClass("active");
	} //end set_artcprofile
<?php
if($maction == 'A_REC' || !empty($mtkn_etr)) { 
?>
set_qprprofile();
<?php
}
?>

</script>
