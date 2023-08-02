<?php
$request = \Config\Services::request();
$mymdsupplier = model('App\Models\MyMDSupplierModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$maction = $request->getVar('maction');
$mtkn_etr = $request->getVar('mtkn_etr');
?>
<main id="main">
    <div class="pagetitle">
        <h1>Supplier Masterdata</h1>
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
                <ul class="nav nav-tabs nav-tabs-bordered" id="myTabSupplier" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="splrlist-tab" data-bs-toggle="tab" data-bs-target="#splrlist" type="button" role="tab" aria-controls="splrlist" aria-selected="true">Supplier Records</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="splrprofile-tab" data-bs-toggle="tab" data-bs-target="#splrprofile" type="button" role="tab" aria-controls="splrprofile" aria-selected="false">Supplier Profile</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabSupplierContent">
                    <div class="tab-pane fade show active" id="splrlist" role="tabpanel" aria-labelledby="splrlist-tab">
                    <?php
                        $data = $mymdsupplier->view_recs(1,20);
                        echo view('masterdata/md-supplier-recs',$data);
                    ?>
                    </div>
                    <div class="tab-pane fade" id="splrprofile" role="tabpanel" aria-labelledby="splrprofile-tab">
                </div>
            </div>
        </div>
    </div>
    <?php
    echo $mylibzsys->memypreloader01('mepreloaderme');
    ?>
</div>  <!-- end main -->

<script type="text/javascript"> 
    mywg_splr_profile_load('<?=$mtkn_etr;?>');
    function mywg_splr_profile_load(mtkn_etr) {
        var ajaxRequest;
        var maction = '<?=$maction;?>';
        ajaxRequest = jQuery.ajax({
                url: "<?=site_url();?>mymd-supplier-profile",
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                type: "post",
                data: { mtkn_etr: mtkn_etr, maction: maction}
            });

        // Deal with the results of the above ajax call
        ajaxRequest.done(function (response, textStatus, jqXHR) {
            jQuery('#splrprofile').html(response);

            // and do it again
            //setTimeout(get_if_stats, 5000);
        });
    }
    __mysys_apps.mepreloader('mepreloaderme',false);
    
    function set_splrprofile() { 
		jQuery('#splrlist').removeClass("show");
		jQuery('#splrlist').removeClass("active");
		jQuery('#splrlist-tab').removeClass("active");
		
		jQuery('#splrprofile').addClass("show");
		jQuery('#splrprofile').addClass("active");
		jQuery('#splrprofile-tab').addClass("active");
	} //end set_splrprofile
<?php
if($maction == 'A_REC' || !empty($mtkn_etr)) { 
?>
set_splrprofile();
<?php
}
?>    
</script>
