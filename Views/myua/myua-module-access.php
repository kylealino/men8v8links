<?php
/* =================================================
 * Author      : Oliver Sta Maria
 * Date Created: April 14, 2023
 * Module Desc : User Management Module Access
 * File Name   : myua/myua-module-access.php
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


?>
<div class="row mt-1 ms-1 me-1">
	<div class="col-md-3">
		<div class="row mb-3">
			<?php
			for($xx = 0; $xx < 30; $xx++) { 
			?>
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked<?=$xx;?>" checked>
				<label class="form-check-label" for="flexSwitchCheckChecked<?=$xx;?>">Checked switch checkbox input<?=$xx;?></label>
			</div>
			<?php
			}
			?>
		</div>
	</div>
	<div class="col-md-3">
		<div class="row mb-3">
			2
		</div>
	</div>
	<div class="col-md-3">
		<div class="row mb-3 me-1 ms-1"> <!-- Inventory HO Module -->
			<?php
			$str = "select aa.*,DATE_FORMAT(NOW(),'%H%i%s') metimeme from {$db_erp}.`mod_sec_menus` aa where trim(`MSA_SYS`) = '02' and trim(`MSA_MODULE`) != '' and trim(`MSA_SMODULE`) != '' and `MSA_MRK` = 'Y' order by `MSA_MODULE`,`MSA_SMODULE`";
			$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$xx = 0;
			$nn = 1;
			foreach($q->getResultArray() as $row) { 
				$xx = $row['MSA_SYS'] . $row['MSA_MODULE'] . $row['MSA_SMODULE'] . $row['metimeme'];
				$mtkn_arttr = hash('sha384', $row['recid'] . $mpw_tkn);
				$str = "select `recid`,`MSA_MRK` from {$db_erp}.mod_sec_accs where `MSA_USER` = (select myusername from {$db_erp}.myusers where sha2(concat(recid,'{$mpw_tkn}'),384) = '$meuatkn' LIMIT 1) and 
				`MSA_SYS` = '{$row['MSA_SYS']}' and `MSA_MODULE` = '{$row['MSA_MODULE']}' and `MSA_SMODULE` = '{$row['MSA_SMODULE']}'";
				$qa = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$mechecked = '';
				if($qa->resultID->num_rows > 0) { 
					$rwa = $qa->getRowArray();
					$mechecked = ($rwa['MSA_MRK'] == 'Y' ? 'checked' : '');
				}
				$qa->freeResult();
				
				$bgcolor = (($nn % 2) ? "#EAEAEA" : "#F2FEFF");
				$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
				
			?>
			<div class="form-check form-switch" style="background-color: <?=$bgcolor;?> !important;" <?=$on_mouse;?>>
				<input class="form-check-input" type="checkbox" id="fsc_ivtyho<?=$xx;?>" name="MYMESYSMODULE[]" data-mtknid="<?=$mtkn_arttr;?>" data-meuatkn="<?=$meuatkn;?>" <?=$mechecked;?>>
				<label class="form-check-label" for="fsc_ivtyho<?=$xx;?>"><?=$row['MSA_MODULE_NAME'] . ' - ' . $row['MSA_SMODULE_NAME'];?></label>
			</div>
			<?php
				$nn++;
			} //end foreach
			?>
		</div>
	</div>
	<div class="col-md-3">
		<div class="row mb-3">
			<?php
			$str = "select aa.*,DATE_FORMAT(NOW(),'%H%i%s') metimeme from {$db_erp}.`mod_sec_menus` aa where trim(`MSA_SYS`) = '04' and trim(`MSA_MODULE`) != '' and trim(`MSA_SMODULE`) != '' and `MSA_MRK` = 'Y' order by `MSA_MODULE`,`MSA_SMODULE`";
			$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$xx = 0;
			$nn = 1;
			foreach($q->getResultArray() as $row) { 
				$xx = $row['MSA_SYS'] . $row['MSA_MODULE'] . $row['MSA_SMODULE'] . $row['metimeme'];
				$mtkn_arttr = hash('sha384', $row['recid'] . $mpw_tkn);
				$str = "select `recid`,`MSA_MRK` from {$db_erp}.mod_sec_accs where `MSA_USER` = (select myusername from {$db_erp}.myusers where sha2(concat(recid,'{$mpw_tkn}'),384) = '$meuatkn'  LIMIT 1) and 
				`MSA_SYS` = '{$row['MSA_SYS']}' and `MSA_MODULE` = '{$row['MSA_MODULE']}' and `MSA_SMODULE` = '{$row['MSA_SMODULE']}'";
				$qa = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$mechecked = '';
				if($qa->resultID->num_rows > 0) { 
					$rwa = $qa->getRowArray();
					$mechecked = ($rwa['MSA_MRK'] == 'Y' ? 'checked' : '');
				}
				$qa->freeResult();
				
				$bgcolor = (($nn % 2) ? "#EAEAEA" : "#F2FEFF");
				$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
				
			?>
			<div class="form-check form-switch" style="background-color: <?=$bgcolor;?> !important;" <?=$on_mouse;?>>
				<input class="form-check-input" type="checkbox" id="flexSwitchCheck<?=$xx;?>" name="MYMESYSMODULE[]" data-mtknid="<?=$mtkn_arttr;?>" data-meuatkn="<?=$meuatkn;?>" <?=$mechecked;?>>
				<label class="form-check-label" for="flexSwitchCheck<?=$xx;?>"><?=$row['MSA_MODULE_NAME'] . ' - ' . $row['MSA_SMODULE_NAME'];?></label>
			</div>
			<?php
				$nn++;
			} //end foreach
			?>
		</div>
	</div>
</div>	
<script type="text/javascript"> 
	jQuery('[name="MYMESYSMODULE[]"]').change(function(e) { 
		try { 
			var mcheck = (this.checked ? 'Y' : 'N');
			var mtkn_uatr = jQuery(this).attr('data-meuatkn');
			var mtkn_arttr = jQuery(this).attr('data-mtknid');
			__mysys_apps.mepreloader('mepreloaderme',true);
			if(jQuery.trim(mtkn_uatr) == '') { 
				alert('User is required!!!');
				__mysys_apps.mepreloader('mepreloaderme',false);
				return false;
			}
			
			var muadat = this.value;
			var mparam = {
				mtkn_uatr: mtkn_uatr,
				mcheck: mcheck,
				mtkn_arttr: mtkn_arttr,
				muadat: muadat
			}
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url()?>myua-module-save',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data) { //display html using divID 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsgme_bod').html(data);
					jQuery('#memsgme').modal('show');
				},
				error: function() { // display global error on the menu function
					__mysys_apps.mepreloader('mepreloaderme',false);
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
	});	
</script>
