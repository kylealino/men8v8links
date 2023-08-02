<?php
$request = \Config\Services::request();
$mymdcustomer = model('App\Models\MyMDCustomerModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$maction = $request->getVar('maction');
$mtkn_etr = $request->getVar('mtkn_etr');
?>
<main id="main">
    <div class="pagetitle">
        <h1>Customer Masterdata</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=site_url();?>">Home</a></li>
                <li class="breadcrumb-item active">Master Data</li>
            </ol>
            </nav>
    </div><!-- End Page Title -->
    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-bordered" id="myTabCustomer" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="custlist-tab" data-bs-toggle="tab" data-bs-target="#custlist" type="button" role="tab" aria-controls="custlist" aria-selected="true">Customer Records</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="custprofile-tab" data-bs-toggle="tab" data-bs-target="#custprofile" type="button" role="tab" aria-controls="custprofile" aria-selected="false">Customer Profile</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabCustomerContent">
                    <div class="tab-pane fade show active" id="custlist" role="tabpanel" aria-labelledby="custlist-tab">
                    <?php
                        $data = $mymdcustomer->view_recs(1,20);
                        echo view('masterdata/md-customer-recs',$data);
                    ?>
                    </div>
                    <div class="tab-pane fade" id="custprofile" role="tabpanel" aria-labelledby="custprofile-tab">
                </div>
            </div>
        </div>
    </div>
    <?php
    echo $mylibzsys->memypreloader01('mepreloaderme');
    ?>
</div>  <!-- end main -->

<script type="text/javascript"> 
    mywg_cust_profile_load('<?=$mtkn_etr;?>');
    function mywg_cust_profile_load(mtkn_etr) {
        var ajaxRequest;
        var maction = '<?=$maction;?>';
        ajaxRequest = jQuery.ajax({
                url: "<?=site_url();?>mymd-customer-profile",
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                type: "post",
                data: { mtkn_etr: mtkn_etr, maction: maction}
            });

        // Deal with the results of the above ajax call
        ajaxRequest.done(function (response, textStatus, jqXHR) {
            jQuery('#custprofile').html(response);

            // and do it again
            //setTimeout(get_if_stats, 5000);
        });
    }
    __mysys_apps.mepreloader('mepreloaderme',false);
    
    function set_custprofile() { 
		jQuery('#custlist').removeClass("show");
		jQuery('#custlist').removeClass("active");
		jQuery('#custlist-tab').removeClass("active");
		
		jQuery('#custprofile').addClass("show");
		jQuery('#custprofile').addClass("active");
		jQuery('#custprofile-tab').addClass("active");
	} //end set_custprofile
<?php
if($maction == 'A_REC' || !empty($mtkn_etr)) { 
?>
set_custprofile();
<?php
}
?>    
</script>
