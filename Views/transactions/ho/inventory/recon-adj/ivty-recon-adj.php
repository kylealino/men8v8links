<?php
$request = \Config\Services::request();
$mymdarticle = model('App\Models\MyMDArticleModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$mtknattr = $request->getVar('mtknattr');


echo view('templates/meheader01');
$memodule = 'ivty_reconadj_';

if(!$myusermod->ua_mod_access_verify($db_erp,$cuser,'02','0004','000402')) { 
	echo "
	<main id=\"main\">
		<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
	</main>
	";
	
} else {
?>

<main id="main">

    <div class="pagetitle">
        <h1>Inventory Recon/Adjustments</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=site_url();?>">Inventory</a></li>
                <li class="breadcrumb-item active">Recon/Adjustment</li>
            </ol>
		</nav>
    </div><!-- End Page Title -->
    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-bordered" id="myIvtyReconAdjTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="myIvtyReconAdj-tab" data-bs-toggle="tab" data-bs-target="#myIvtyReconAdj" type="button" role="tab" aria-controls="myIvtyReconAdj" aria-selected="true">Recon/Adjustments Entry</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#artcprofile" type="button" role="tab" aria-controls="artcprofile" aria-selected="false">Item History</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Dash Boards</button>
                    </li>
                </ul>
                <div class="tab-content" id="myIvtyReconAdjTabContent">
                    <div class="tab-pane fade show active" id="myIvtyReconAdj" role="tabpanel" aria-labelledby="myIvtyReconAdj-tab">
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
    echo $mylibzsys->memsgbox3('memsgreconadj','System Alert','...');
    echo $mylibzsys->memsgbox_yesno1('meyn1_' . $memodule,'','');
    echo $mylibzsys->memsgbox_yesno1('meyn2_' . $memodule,'','');
    ?>
</div>  <!-- end main -->
<?php
}
echo view('templates/mefooter01');
?>

<script type="text/javascript"> 
    __mysys_apps.mepreloader('mepreloaderme',false);

	function ivty_reconadj_delrow(eleIDRow) { 
		jQuery('#' + eleIDRow).remove();
	} //end ivty_reconadj_delrow

                  
	mywg_ivty_reconadj_ent_load('<?=$mtknattr;?>');
    function mywg_ivty_reconadj_ent_load(mtknattr) { 
		try {
			var mparam = {
				meako: 'BronOlexus',
				mtknattr: mtknattr 
			}
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?= site_url() ?>myinventory-recon-adj-entry',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID 
					jQuery('#myIvtyReconAdj').html(data);
					return false;
				},
				error: function(data) { // display global error on the menu function
					alert('error loading page...');
					return false;
				}
			});		        
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;

		}  //end try
			
    }  //end mywg_ivty_reconadj_ent_load
        
</script>
