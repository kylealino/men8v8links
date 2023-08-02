<?php

$mymdarticle = model('App\Models\MyMDArticleModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

echo view('templates/meheader01');
if(!$myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0001','000103')) { 
	echo "
	<main id=\"main\">
		<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
	</main>
	";
	
} else {

?>

<main id="main">
    <div class="pagetitle">
        <h1>Master Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=site_url();?>">Master Data</a></li>
                <li class="breadcrumb-item active">Article Master POS Link</li>
            </ol>
            </nav>
    </div><!-- End Page Title -->

    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-bordered" id="myTabArticle" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="artm-poslink-tab" data-bs-toggle="tab" data-bs-target="#artm-poslink" type="button" role="tab" aria-controls="artm-poslink" aria-selected="true">Branch Product Master List</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabArticleContent">
                    <div class="tab-pane fade show active" id="artm-poslink" role="tabpanel" aria-labelledby="artm-poslink-tab">
						
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    echo $mylibzsys->memypreloader01('mepreloaderme');
    echo $mylibzsys->memsgbox1('memsgsalesoutdetl','System Info','...');
} //end ua_mod_access_verify
    ?>

</div>  <!-- end main -->
<?php
echo view('templates/mefooter01');
?>

<script type="text/javascript"> 
    mywg_artmbranches_scr_load();
    function mywg_artmbranches_scr_load(mtkn_etr) { 
		try { 
			var mparam = { me: 'N8V8' };
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>mymd-article-master-poslink-branch',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						jQuery('#artm-poslink').html(data);
						return false;
				},
				error: function() { // display global error on the menu function
					alert('error loading page...');
					return false;
				}	
			});				
		} catch (err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
		} //end try		
    } //end mywg_artmbranches_scr_load
    	
    __mysys_apps.mepreloader('mepreloaderme',false);
</script>
