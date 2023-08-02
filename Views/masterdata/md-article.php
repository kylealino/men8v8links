<?php
$request = \Config\Services::request();
$mymdarticle = model('App\Models\MyMDArticleModel');
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
                <li class="breadcrumb-item active">Item/Materials Master Data</li>
            </ol>
            </nav>
    </div><!-- End Page Title -->

    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-bordered" id="myTabArticle" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="artclist-tab" data-bs-toggle="tab" data-bs-target="#artclist" type="button" role="tab" aria-controls="artclist" aria-selected="true">Item/Materials Records</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="artprofile-tab" data-bs-toggle="tab" data-bs-target="#artcprofile" type="button" role="tab" aria-controls="artcprofile" aria-selected="false">Item/Materials Profile</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabArticleContent">
                    <div class="tab-pane fade show active" id="artclist" role="tabpanel" aria-labelledby="artclist-tab">
                    <?php
                        $data = $mymdarticle->view_recs(1,20);
                        echo view('masterdata/md-article-recs',$data);
                    ?>
                    </div>
                    <div class="tab-pane fade" id="artcprofile" role="tabpanel" aria-labelledby="artprofile-tab">
                </div>
            </div>
        </div>
    </div>

    <?php
    echo $mylibzsys->memypreloader01('mepreloaderme');
    ?>

</div>  <!-- end main -->

<script type="text/javascript"> 
    mywg_art_profile_load('<?=$mtkn_etr;?>');
    function mywg_art_profile_load(mtkn_etr) {
        var ajaxRequest;
        var maction = '<?=$maction;?>';
        ajaxRequest = jQuery.ajax({
                url: "<?=site_url();?>mymd-item-materials-profile",
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                type: "post",
                data: { mtkn_etr: mtkn_etr, maction: maction}
            });

        // Deal with the results of the above ajax call
        ajaxRequest.done(function (response, textStatus, jqXHR) {
            jQuery('#artcprofile').html(response);

            // and do it again
            //setTimeout(get_if_stats, 5000);
        });
    }
    __mysys_apps.mepreloader('mepreloaderme',false);
    
    function set_artcprofile() { 
		jQuery('#artclist').removeClass("show");
		jQuery('#artclist').removeClass("active");
		jQuery('#artclist-tab').removeClass("active");
		
		jQuery('#artcprofile').addClass("show");
		jQuery('#artcprofile').addClass("active");
		jQuery('#artprofile-tab').addClass("active");
	} //end set_artcprofile
<?php
if($maction == 'A_REC' || !empty($mtkn_etr)) { 
?>
set_artcprofile();
<?php
}
?>

</script>
