<?php
$request = \Config\Services::request();
$mymdarticle = model('App\Models\MyMDArticleModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');

$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$mtkn_etr = trim($request->getVar('mtkn_etr'));
$sysctrl_seqn = '';

?>
<div class="main">
    <div class="col-md-6">
        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="text-field">RFP Number:</label>
            </div>
            <div class="col-sm-9 mt-2">
                <input type="text" id="text-field" class="form-control form-control-sm">
            </div>
        </div>

        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="text-field">Company Name:</label>
            </div>
            <div class="col-sm-9">
                <input type="text" id="text-field" class="form-control form-control-sm">
            </div>
        </div>

        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="BRNCH_NAME">Branch:</label>
            </div>
            <div class="col-sm-9">
                <input type="text" id="BRNCH_NAME" class="form-control form-control-sm">
            </div>
        </div>

        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="expense-type">Type of expense:</label>
            </div>
            <div class="col-sm-9">
                <input type="text" id="expense-type" class="expense-type form-control form-control-sm" >
            </div>
        </div>

        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="text-field">Date requested:</label>
            </div>
            <div class="col-sm-9">
                <input type="date" id="text-field" class="form-control form-control-sm">
            </div>
        </div>
    </div>

    <div class="col-md-6" id="text-upld">
		<div class="alert alert-dark fade show" role="alert">
			<h4 class="alert-heading">Attached scanned Copy of Approved RFP</h4>
			<hr>
			<?php if(!empty($mtkn_etr) || $mtkn_etr != ""):
				
				$str = "SELECT `file` FROM {$db_erp}.`trx_ap_trns_deposit_hd_files` WHERE `ctrlno_hd` = '{$sysctrl_seqn}'";
				$qf = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				$chtml = "<p class=\"mb-2\" id=\"__mefilesme\">
					<ul class=\"list-group\">";
				
				foreach ($qf->getResultArray() as $key ) { 
					$mefiles_upath =  site_url() . 'uploads/medeposit_uploads/' . $key['file'];
					//$path = site_url().$root_dir.$key['file'];
					$chtml .= "<li class=\"list-group-item\"><a href=\"{$mefiles_upath}\"><i class=\"ri-eye-fill me-1 text-success\"></i> {$key['file']}</a></li>"; 
					//$this->zip->add_data($key['file'],file_get_contents($path));
				}
				$qf->freeResult();
				$chtml .= "
					</ul>
				</p>";
				echo $chtml;
			?>
				<p class="mb-2" id="__mefilesme">
				</p>
			<?php 
			else: ?>
				<p class="mb-2" id="__mefilesme"></p>
			<?php
			endif;?>
			<p class="mb-0">
				<div class="input-group input-group-sm">
				<?php if(!empty($mtkn_etr) || $mtkn_etr != ""):?>
					<form method='post' action='<?= base_url() ?>mysales-deposit-dload-zip-files'>
						<input type="hidden" name="data_01" value="<?=$sysctrl_seqn?>">
						<button type="submit" class="btn btn-sm btn-info" title = "Download files" value="<?=$sysctrl_seqn;?>"><i class="bi bi-cloud-download"></i> Download</button>
					</form>
				<?php endif; ?>
					<button class="btn btn-info btn-sm btn-danger" id="mebtn_saledepofile"><span class="bi bi-cloud-upload"> </span>Browse</button>
					<input data-id="__lbl01" accept="image/gif,image/jpeg,image/png,application/pdf" class="__upld_file_img01" id="__upld_file_img01" type="file" multiple name="images[]" style="display: none;" />
				</div>
			</p>
		</div>
	</div> <!-- end col-4 -->  

	<div class="col-md-6">
		<div class="table-responsive">
			<table class="metblentry-font" id="_tbl_deposit">
				<thead>
					<th class="text-center">
						<button type="button" id="__salesdepo_addrow" class="btn btn-sm btn-success p-1 pb-0 mebtnpt1" onclick="javascript: rfp_addRows();">
							<i class="bi bi-plus"></i>
						</button>
					</th>
					<th>Particulars</th>
					<th>Amount</th>

				</thead>
				<tbody id="_tbl_deposit_contentArea">
					<tr>
						<td >
							<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1 medelrecsaledepo"  data-rectype="dt" data-mtknid="" onClick="javascript:medelrec_saledepodt(this);">
								<i class="bi bi-trash"></i>
							</button>
						</td>

						<td >
							<input type="text" id="bankname" class="bankNameAcct form-control form-control-sm" size="30" title=""  data-mtnkattr="" />
						</td>
						<td>
							<input type="number" id="accountnumber" size="30" class="form-control form-control-sm" title="" data-mtnkattr=""  onblur="me_tamtrfp()" />
						</td>
					</tr> 
					<tr style="display:none;">
						<td>
							<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" data-rectype="dt" onClick="javascript:medelrec_saledepodt(this);" >
								<i class="bi bi-trash"></i>
							</button>
						</td>
						<td>
							<input type="text" class="bankNameAcct form-control form-control-sm" size="30" data-mtnkattr="" />
						</td>
						<td>
							<input type="number" id="_acct_name" class="form-control form-control-sm"  size="30" onblur="me_tamtrfp()" />
						</td>
					</tr>               
				</tbody>
				<tfoot>
					<tr style="background-color:#ababab78;" class="font-weight-bold">
						<td></td>
						<td colspan="1" style="padding:8px;" class="text-left">TOTAL AMOUNT:</td>
						<td class="text-left" id="__meTotalAmtrfp">
						</td>
					</tr>	
                </tfoot>
			</table>
			<br/>
		</div>
	</div>

</div>
<script>
    __mysys_apps.meTableSetCellPadding("_tbl_deposit",3,"1px solid #7F7F7F");

    jQuery('#mebtn_saledepofile').click(function() { 
		try { 
			jQuery('#__upld_file_img01').click();
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			
			return false;

		}  //end try					
	});  //end mebtn_saledepofile click event 
	
	jQuery('#__upld_file_img01').on('change',function() { 
		try { 
			//alert(this.files.length + ' ' + this.files[0].name);
			if(this.files.length > 0) { 
				var mefilecontent = "<ul class=\"list-group\">";
				for(aa = 0; aa < this.files.length; aa++) { 
					mefilecontent += "<li class=\"list-group-item\"><i class=\"bi bi-star me-1 text-success\"></i> " + this.files[aa].name + "</li>"; 
				}
				mefilecontent += "</ul";
				jQuery('#__mefilesme').html(mefilecontent);
			}
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			
			return false;
		}  //end try					
	}); //end __upld_file_img01 change event
</script>