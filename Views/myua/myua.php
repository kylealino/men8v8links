<?php
/* =================================================
 * Author      : Oliver Sta Maria
 * Date Created: April 14, 2023
 * Module Desc : User Management 
 * File Name   : myua/myua.php
 * Revision    : Migration to Php8 Compatability 
*/

$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$meuatkn = $request->getVar('meuatkn');
echo view('templates/meheader01');
?>
<main id="main" class="main">
	<div class="pagetitle">
		<h1>User Management</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?=site_url();?>">Home</a></li>
				<li class="breadcrumb-item active">User Access</li>
			</ol>
		</nav>
	</div> <!-- End Page Title -->
	<section class="section">
		<div class="row metblentry-font">
			<div class="col-md-6">
				<div class="row mb-12" id="wg_myuser_recs">
					<?php
					$data = $myusermod->view_recs(1,30,'');
					echo view('myua/myuser-recs',$data);
					?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row mb-12">
					<?php
					if(!empty($meuatkn)):
						$str = "select myusername,`myuserfulln` from {$db_erp}.myusers where sha2(concat(recid,'{$mpw_tkn}'),384) = '$meuatkn' LIMIT 1";
						$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->getNumRows() > 0): 
							$rw = $q->getRow();
							$chtml = "
							<div class=\"alert alert-warning\" role=\"alert\">
								<h4 class=\"alert-heading\">User Info</h4>
								<p>{$rw->myuserfulln}</p>
								<hr>
								<p class=\"mb-0\">User Name: {$rw->myusername}</p>
							</div>							
							";
							echo $chtml;
						endif;
						$q->freeResult();
					endif;
					?>
				</div>
			</div>
		</div> <!-- end row metblentry-font 1st-->
		<div class="row metblentry-font">
			<div class="col-lg-12 col-md-12 mb-md-0 mb-4">
				<div class="card">
					<ul class="nav nav-tabs nav-tabs-bordered" id="myTabUserAccess" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="uamodaccess-tab" data-bs-toggle="tab" data-bs-target="#uamodaccess" type="button" role="tab" aria-controls="uamodaccess" aria-selected="true">Modular Access</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="uamodbranch-tab" data-bs-toggle="tab" data-bs-target="#uamodbranch" type="button" role="tab" aria-controls="uamodbranch" aria-selected="false">Branch</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="uamodsupplier-tab" data-bs-toggle="tab" data-bs-target="#uamodsupplier" type="button" role="tab" aria-controls="uamodsupplier" aria-selected="false">Supplier</button>
						</li>
					</ul>
					<div class="tab-content" id="myTabUserAccessContent">
						<div class="tab-pane fade show active" id="uamodaccess" role="tabpanel" aria-labelledby="uamodaccess-tab">
						<?php
							echo view('myua/myua-module-access');
						?>
						</div>
						<div class="tab-pane fade" id="uamodbranch" role="tabpanel" aria-labelledby="uamodbranch-tab"></div>
						<div class="tab-pane fade" id="uamodsupplier" role="tabpanel" aria-labelledby="uamodsupplier-tab"></div>
					</div>
				</div>
			</div>			
		</div> <!-- end row metblentry-font 2nd-->
	</section> <!-- section -->
<?php
echo $mylibzsys->memypreloader01('mepreloaderme');
echo $mylibzsys->memsgbox3('memsgme','System Message','...');
?>
</main>
<?php
echo view('templates/mefooter01');
?>
<script type="text/javascript"> 
	__mysys_apps.mepreloader('mepreloaderme',false);
</script>
