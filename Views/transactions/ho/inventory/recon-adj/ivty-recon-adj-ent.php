<?php
$myivtyrecadj = model('App\Models\MyInventoryReconAdjModel');

$db_erp = $myivtyrecadj->mydbname->medb(0);
$cuser = $myivtyrecadj->myusermod->mysys_user();
$mpw_tkn = $myivtyrecadj->myusermod->mpw_tkn();

$mtknattr = $myivtyrecadj->request->getVar('mtknattr');
$cseqn = '';
$mtknattr_brid = '';
$me_fld_branch = '';
$lrec = 0;
$arecs = array();
$me_recadj_rmk = '';

//check user access entry 
if(!$myivtyrecadj->myusermod->ua_mod_access_verify($db_erp,$cuser,'02','0004','00040201')) { 
	echo "
	<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
	";
	die();
}

$csave_chtml = '';

if(!empty($mtknattr)) { 
	
	//check user access editing 
	if(!$myivtyrecadj->myusermod->ua_mod_access_verify($db_erp,$cuser,'02','0004','00040202')) { 
		echo "
		<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
		";
		die();
	}	
	$str = "SELECT aa.`recid`,aa.ira_branch_id,aa.`ira_hd_ctrlno`,sum(bb.`ira_ded_qty`) __ded_qty,sum(bb.`ira_add_qty`) __add_qty,aa.`muser`,cc.`BRNCH_NAME`,
	aa.`ira_posted`,
	sha2(concat(cc.`recid`,'{$mpw_tkn}'),384) mtknattr_brid,aa.`ira_remk`,
	date_format(aa.`mencd`,'%m/%d/%Y %h:%i:%s') __mencd FROM {$db_erp}.`trx_ivty_reconadj_hd` aa 
	JOIN {$db_erp}.`trx_ivty_reconadj_dt` bb ON aa.`recid` = bb.`ira_hd_id` 
	JOIN {$db_erp}.`mst_companyBranch` cc ON cc.`recid` = aa.`ira_branch_id` 
	where sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$mtknattr' 
	group by aa.`ira_hd_ctrlno`";
	$q = $myivtyrecadj->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	if($q->getNumRows() > 0):
		$rw = $q->getRowArray();
		$cseqn = $rw['ira_hd_ctrlno'];
		$mtknattr_brid = $rw['mtknattr_brid'];
		$me_fld_branch = $rw['BRNCH_NAME'];
		$ira_hd_id = $rw['recid'];
		$ira_branch_id = $rw['ira_branch_id'];
		$me_recadj_rmk = $rw['ira_remk'];
		if ($rw['ira_posted'] == 'Y'):
			$csave_chtml = "YES";
		endif;
		$lrec = 1;
	endif;
	$q->freeResult();
	
}

//need this to retreive some element IDs from Parent Module
$memodule = 'ivty_reconadj_';
?>
<div class="row mt-3 ms-1 me-1">
    <div class="col-md-8">
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Inventory Adjustment Ctrl No.:</span>
			</div>
			<div class="col-sm-9">
				<input type="text" class="form-control form-control-sm" data-id="" id="me_RecAdjCtrlNo" name="me_RecAdjCtrlNo" value="<?=$cseqn;?>" data-mtknattr="<?=$mtknattr;?>" disabled>
			</div>
		</div> <!-- end Inventory Adjustment Ctrl No.: -->
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Branch:</span>
			</div>
			<div class="col-sm-9">
				<input type="text" class="form-control form-control-sm" data-id="" id="me_fld_branch" name="me_fld_branch" value="<?=$me_fld_branch;?>" required data-mtknattr="<?=$mtknattr_brid;?>">
			</div>
		</div> <!-- end Branch --> 
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Attach File:</span>
			</div>
			<div class="col-sm-9">
				<button class="btn btn-sm btn-danger" id="mebtn_upld_myfile"><span class="bi bi-cloud-upload"> </span>Browse</button>
				<span id="__mefilesme">
					<?php
					if ($lrec):
						$str = "select `ira_filename` from {$db_erp}.`trx_ivty_reconadj_upld_files` where `ira_hd_ctrlno` = {$cseqn} ";
						$qr = $myivtyrecadj->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if ($qr->getNumRows() > 0):
						$chtml = "<ul class=\"list-group mt-2\">";
							foreach($qr->getResultArray() as $mrw):
								//$mefiles_path = ROOTPATH . 'public/uploads/mereconadj_uploads/';
								$mefiles_upath =  site_url() . 'uploads/mereconadj_uploads/' . $mrw['ira_filename'];
								$chtml .= "<li class=\"list-group-item\"><a href=\"{$mefiles_upath}\"><i class=\"ri-eye-fill me-1 text-success\"></i> {$mrw['ira_filename']}</a></li>"; 
							endforeach;
							$chtml .= "</ul>";
							echo $chtml;
						endif;
					endif;
					?>
				</span>
				<input accept="application/pdf" id="__upld_myfile" type="file" style="display: none;" />
			</div>
		</div> <!-- end Attach File -->
		<div class="row mb-3">
			<div class="col-sm-3">
				<span class="fw-bold">Remarks :</span>
			</div>
			<div class="col-sm-9">
				<textarea rows="5" id="me_recadj_rmk" name="me_recadj_rmk" class="form-control"><?=$me_recadj_rmk;?></textarea>
			</div>
		</div> <!-- end Branch --> 
	</div>
</div>
<div class="row mt-3 ms-1 me-1">
	<div class="col-md-12">
		<div class="table-responsive mb-2">
			<table class="metblentry-font" id="_tbl_IvtyReconAdjEntry">
				<thead>
					<th class="text-center">
						<button type="button" id="__IvtyReconAdj_addrow" class="btn btn-sm btn-success p-1 pb-0 mebtnpt1" >
							<i class="bi bi-plus"></i>
						</button>
					</th>
					<th nowrap>Item Code</th>
					<th nowrap>Description</th>
					<th nowrap>Inventory Balance</th>
					<th nowrap>Deduct</th>
					<th nowrap>Add</th>
					<th nowrap>Remarks</th>
				</thead>
				<tbody>
					<?php
					if($lrec):
						$str = "select aa.*,bb.ART_CODE,bb.ART_DESC,
						sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) `mtknid` 
						 from {$db_erp}.`trx_ivty_reconadj_dt` aa join
						{$db_erp}.`mst_article`  bb on(aa.`ira_artm_rid` = bb.`recid`) 
						 where `ira_hd_id` = '$ira_hd_id'";
						 
						$q = $myivtyrecadj->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if ($q->getNumRows() > 0):
							foreach ($q->getResultArray() as $row):
								$mid = $myivtyrecadj->mylibzsys->random_string(10);
								$arecs[] = $mid;
								$chtml = "
								<tr id=\"metr_rec_{$mid}\">
									<td>
										<button type=\"button\" class=\"btn btn-sm btn-danger p-1 pb-0 mebtnpt1\" data-rectype=\"dt\" data-mtknid=\"{$row['mtknid']}\"  name=\"mebtn_trashrec[]\" >
											<i class=\"bi bi-trash\"></i>
										</button>
									</td>
									<td>
										<input type=\"text\" class=\"__me_item_code\" id=\"_meitemc_{$mid}\" data-mtkndt=\"{$row['mtknid']}\" size=\"30\" value=\"{$row['ART_CODE']}\" />
									</td>
									<td>
										<input type=\"text\" size=\"40\" value=\"{$row['ART_DESC']}\" readonly />
									</td>
									<td>
										<input type=\"text\" size=\"10\" disabled />
									</td>
									<td><input type=\"number\" class=\"text-end fw-bold text-danger\" placeholder=\"0.00\" value=\"{$row['ira_ded_qty']}\" /></td>
									<td><input type=\"number\" class=\"text-end fw-bold\" placeholder=\"0.00\" value=\"{$row['ira_add_qty']}\" /></td>
									<td><input type=\"text\" size=\"35\" value=\"{$row['ira_item_remk']}\" /></td>
								</tr>
								";
								echo $chtml;
							endforeach;
						endif;
					endif;
					?>
					<tr style="display:none;">
						<td>
							<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" data-rectype="dt" name="mebtn_trashrec[]" >
								<i class="bi bi-trash"></i>
							</button>
						</td>
						<td>
							<input type="text" class="__me_item_code" data-mtkndt="" size="30" />
						</td>
						<td>
							<input type="text" size="40" readonly />
						</td>
						<td><input type="text" size="10" disabled /></td>
						<td><input type="number" class="text-end fw-bold text-danger" placeholder="0.00" /></td>
						<td><input type="number" class="text-end fw-bold" placeholder="0.00" /></td>
						<td><input type="text" size="35" /></td>
					</tr> 
				</tbody>
			</table> 
		</div>
	</div>
</div>
<div class="row mt-3 ms-1 me-1 mb-3">
	<div class="col-md-6">
		<?php
		if ($csave_chtml !== 'YES'):
		?>
		<button class="btn btn-sm btn-success" id="mebtn_save_trxent"><span class="bi bi-save"> </span>Save</button>
		<?php
		endif;
		?>
		<button class="btn btn-sm btn-warning" id="btn_<?=$memodule;?>newtrx"><span class="bi bi-journal-bookmark"> </span>New Trx</button>
		
	</div>
</div>
<div class="row mt-3 ms-1 me-1 mb-3">
	<div class="col-sm-12 col-md-12" > 
		<ul class="nav nav-tabs nav-tabs-bordered" id="myIvtyReconAdjRecTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="myIvtyReconAdjRec-tab" data-bs-toggle="tab" data-bs-target="#myIvtyReconAdjRec" type="button" role="tab" aria-controls="myIvtyReconAdjRec" aria-selected="true">Encoded Records</button>
			</li>
		</ul>
		<div class="tab-content" id="myIvtyReconAdjRecTabContent">
			<div class="tab-pane fade show active" id="myIvtyReconAdjRec" role="tabpanel" aria-labelledby="myIvtyReconAdjRec-tab">
				<div class="card" id="mywg_recon_adj_recs">
					<div class="row mt-3 ms-1 me-1 mb-3">
						<div class="col-sm-6 col-md-6" >
							<div class="input-group input-group-sm">
								<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
								<input type="text" class="form-control form-control-sm" name="mytxtsearchrec" placeholder="Search Control Number" id="txtsearchedrec" aria-label="Search-Deposit" aria-describedby="mebtnGroupAddon" required/>
								<div class="invalid-feedback">Please fill out this field.</div>
								<button type="submit" class="btn btn-success btn-sm" id="mbtn_recadj_rec_search"><i class="bi bi-search"></i></button>
								<button type="submit" class="btn btn-success btn-sm" id="mbtn_recadj_rec_reset"><i class="bi bi-bootstrap-reboot"></i> Reset</button>
							</div> <!-- end input-group -->
						</div>
					</div>
				</div>
			</div>
		</div> <!-- end ta-content -->
	</div>
</div>
<script type="text/javascript"> 
	
	__mysys_apps.meTableSetCellPadding("_tbl_IvtyReconAdjEntry",3,"1px solid #7F7F7F");
	
	jQuery('#me_fld_branch')
		// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) { 
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).data( 'autocomplete' ).menu.active ) { 
				event.preventDefault();
		}
		if( event.keyCode === jQuery.ui.keyCode.TAB ) { 
			event.preventDefault();
		}
	})
	.autocomplete({
			minLength: 0,
			source: '<?= site_url(); ?>company-branch-ua',
			focus: function() {
					// prevent value inserted on focus
					return false;
				},
				search: function(oEvent, oUi) {
					var sValue = jQuery(oEvent.target).val();
					jQuery(this).autocomplete('option', 'source', '<?=site_url();?>company-branch-ua'); 
				},
				select: function( event, ui ) {
					var terms = ui.item.value;
					var mtkn_comp = ui.item.mtkn_comp;
					var mtknr_rid = ui.item.mtknr_rid;
					var mtkn_brnch = ui.item.mtkn_brnch;
					jQuery('#me_fld_branch').val(terms);
					jQuery('#me_fld_branch').attr('data-mtknattr',mtknr_rid);
					jQuery(this).autocomplete('search', jQuery.trim(terms));
					return false;
				}
			})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	}); // end me_fld_branch 
	
	jQuery('#__IvtyReconAdj_addrow').click(function() { 
		try {
			IvtyReconAdj_addRows();
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  				
	});  //end __IvtyReconAdj_addrow
	
	
	IvtyReconAdj_addRows();
	function IvtyReconAdj_addRows() { 
		try {
			var rowCount = jQuery('#_tbl_IvtyReconAdjEntry tr').length;
			var mid = __mysys_apps.__do_makeid(5) + (rowCount + 1);
			var clonedRow = jQuery('#_tbl_IvtyReconAdjEntry tr:eq(' + (rowCount - 1) + ')').clone(); 
			jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','_meitemc_' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','_meitemdesc' + mid);

			jQuery('#_tbl_IvtyReconAdjEntry tr').eq(rowCount - 1).before(clonedRow);
			jQuery(clonedRow).css({'display':''});
			jQuery(clonedRow).attr('id','metr_rec_' + mid);
			__me_item_code_event('#_meitemc_' + mid);
			me_trashrec();
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  		
	} //end IvtyReconAdj_addRows
	
	function __me_item_code_event(celemID) { 
		jQuery(celemID)
			// don't navigate away from the field on tab when selecting an item
			.bind( 'keydown', function( event ) {
				if ( event.keyCode === jQuery.ui.keyCode.TAB &&
					jQuery( this ).data( 'autocomplete' ).menu.active ) {
					event.preventDefault();
			}
			if( event.keyCode === jQuery.ui.keyCode.TAB ) {
				event.preventDefault();
			}
		})
		.autocomplete({
				minLength: 0,
				source: '<?= site_url(); ?>myinventory-recon-adj-search-mat/?data-mtknattr=' + jQuery('#me_fld_branch').attr('data-mtknattr'),
				focus: function() {
						// prevent value inserted on focus
						return false;
					},
					search: function(oEvent, oUi) {
						var sValue = jQuery(oEvent.target).val();
						jQuery(this).autocomplete('option', 'source', '<?=site_url();?>myinventory-recon-adj-search-mat/?data-mtknattr=' + jQuery('#me_fld_branch').attr('data-mtknattr')); 
					},
					select: function( event, ui ) {
						var terms = ui.item.value;
						jQuery(this).val(terms);
						jQuery(this).autocomplete('search', jQuery.trim(terms));
						var clonedRow = jQuery(this).parent().parent().clone();
						var xobjArtMDescId = jQuery(clonedRow).find('input[type=text]').eq(1).attr('id');
						jQuery('#' + xobjArtMDescId).val(ui.item.ART_DESC);
						jQuery('#' + xobjArtMDescId).attr('alt',ui.item.ART_DESC);
						jQuery('#' + xobjArtMDescId).attr('title',ui.item.ART_DESC);
						return false;
					}
				})
			.click(function() {
				var terms = this.value;
				jQuery(this).autocomplete('search', jQuery.trim(terms));
		}); // end __me_item_code class mapping
	} //end __me_item_code_event        
	
	jQuery('#mebtn_upld_myfile').click(function() { 
		try { 
			jQuery('#__upld_myfile').click();
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			
			return false;

		}  //end try					
	});  //end mebtn_cycfile click event 
		
	jQuery('#__upld_myfile').on('change',function() { 
		try { 
			//alert(this.files.length + ' ' + this.files[0].name);
			if(this.files.length > 0) { 
				var mefilecontent = "<ul class=\"list-group mt-2\">";
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
	}); //end __upld_file_cyc change event
	
	jQuery('#mebtn_save_trxent').click(function() { 
		try {
			__mysys_apps.mepreloader('mepreloaderme',true);
			var me_fld_branch = jQuery('#me_fld_branch').val();
			var br_mtknattr = jQuery('#me_fld_branch').attr('data-mtknattr');
			var _tbl_reconadj = jQuery('#_tbl_IvtyReconAdjEntry');
			var me_recadj_rmk = jQuery('#me_recadj_rmk');
			var cseqn_mtknattr = jQuery('#me_RecAdjCtrlNo').attr('data-mtknattr');
			var me_recadj_rmk = jQuery('#me_recadj_rmk').val();
			var my_data = new FormData();
			var mearray = [];
			var i=0;
			var sep = '';
			var counts =_tbl_reconadj.find('tr').length - 2;
			var counttrvalid = 0;
			var invalid = 0;
			_tbl_reconadj.find('tr').each(function (i, el) { 
				var trtxt = jQuery(this).find("input[type=text]"),
				trNum  = jQuery(this).find("input[type=number]"),
				_itemc_        = trtxt.eq(0).val(),
				_itemc_ded_    = trNum.eq(0).val(),
				_itemc_add_    = trNum.eq(1).val(),
				_itemc_rmk_     = trtxt.eq(3).val(),
				_itemc_dtattr = trtxt.eq(0).attr('data-mtkndt');
				if(jQuery.trim(_itemc_) !== '' && (_itemc_ded_ != 0 || _itemc_add_ != 0)) { 
					counttrvalid++;
					my_data.append('mearray[]',_itemc_ + 'x|x' + _itemc_ded_ + 'x|x' + _itemc_add_ + 'x|x' + _itemc_rmk_ + 'x|x' + _itemc_dtattr);
				} 
			});	
			my_data.append('fld_branch',me_fld_branch);
			my_data.append('br_mtknattr',br_mtknattr);
			my_data.append('cseqn_mtknattr',cseqn_mtknattr);
			my_data.append('me_recadj_rmk',me_recadj_rmk);
			
			if(counttrvalid == 0) { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgreconadj_bod').html("<span class=\"fw-bold text-danger mb-0\">No valid entries to proceed!!!</span>");
				jQuery('#memsgreconadj').modal('show');
				return false;
			}
			
			
			var mfileattach = '__upld_myfile';
			var mfiles = document.getElementById(mfileattach);
			var filesCount = 0;
			<?php
			if ($lrec):
				echo "filesCount = 1;";
			endif;
			?>
			
			if(mfiles.files.length > 0) { 
				jQuery.each(mfiles.files, function(k,file){ 
					my_data.append('mefiles[]', file);
					filesCount++;
				});
			} 
			
			if(filesCount == 0) { 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgreconadj_bod').html("<span class=\"fw-bold text-danger mb-0\">Valid File Attachment is REQUIRED!!!</span>");
				jQuery('#memsgreconadj').modal('show');
				return false;
			}
			
			
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-recon-adj-entry-sv',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsgreconadj_bod').html(data);
					jQuery('#memsgreconadj').modal('show');
					return false;
				},
				error: function() { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 
			
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}  				
	});  //end mebtn_save_trxent
	
	jQuery('#btn_<?=$memodule;?>newtrx').click(function() { 
		try { 
			jQuery('#meyn1_<?=$memodule;?>_bod').html('<span class=\"fw-bold text-danger\">Be sure to save all changes to prevent any data loss...<br\>Proceed anyway?</span>');
			jQuery('#staticBackdropmeyn1_<?=$memodule;?>').html('<span class=\"fw-bold\">Inventory Recon/Adj...</span>');
			jQuery('#meyn1_<?=$memodule;?>_yes').prop('disabled',false);
			jQuery('#meyn1_<?=$memodule;?>_no').html('No');
			jQuery('#meyn1_<?=$memodule;?>').modal('show');
		} catch(err) { 
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
		}  //end try 
	}); //end btn_saledepo_newtrx
	
	jQuery('#meyn1_<?=$memodule;?>_yes').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			window.location.href = '<?=site_url();?>myinventory-recon-adj';
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
		}  //end try 
	});
		
	
	jQuery('#mbtn_recadj_rec_search').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var my_data = new FormData();
			my_data.append('me','LexaLexieLexus');
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-recon-adj-recs',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#mywg_recon_adj_recs').html(data);
					return false;
				},
				error: function() { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 
			
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}  
	});  //end mbtn_recadj_rec_search
	
	me_trashrec();
	function me_trashrec() {
		jQuery('[name="mebtn_trashrec[]"]').click(function() { 
			try { 
				var trid = jQuery(this).parent().parent().attr('id');
				var data_mtknid = jQuery(this).attr('data-mtknid');
				var data_rectype = jQuery(this).attr('data-rectype');
				var mtkn_etr = '<?=$mtknattr;?>';
				if(jQuery.trim(mtkn_etr) == '') {
					mtkn_etr = jQuery('#me_RecAdjCtrlNo').attr('data-mtknattr');
				}
				
				var mobj = jQuery('#' + trid);
				var trtxt = jQuery(mobj).find("input[type=text]"),
				meitemc__  = trtxt.eq(0).val();
				
				var memsg = "<input type=\"hidden\" id=\"medelrecdata\" data-itemvalue=\"" + meitemc__ + "\" data-metrid=\"" + trid + "\" data-mtknid=\"" + data_mtknid + "\" data-mtkn_etr=\"" + mtkn_etr + "\" data-rectype=\"" + data_rectype + "\" />";
				memsg += "<div id=\"memsgdelrec\" style=\"display:none;\"></div>";

				jQuery('#meyn2_<?=$memodule;?>_bod').html('<span class=\"fw-bold text-danger\">Selected record [' + meitemc__ + '] is no longer available and permanently deleted...<br\>Proceed anyway?</span>' + memsg);
				jQuery('#staticBackdropmeyn2_<?=$memodule;?>').html('<span class=\"fw-bold\">Inventory Recon/Adj Entry</span>');
				jQuery('#meyn2_<?=$memodule;?>_yes').prop('disabled',false);
				jQuery('#meyn2_<?=$memodule;?>_no').html('No');
				jQuery('#meyn2_<?=$memodule;?>').modal('show');
				
				
			} catch(err) { 
				var mtxt = 'There was an error on this page.\\n';
				mtxt += 'Error description: ' + err.message;
				mtxt += '\\nClick OK to continue.';
				__mysys_apps.mepreloader('mepreloaderme',false);
				alert(mtxt);
				return false;
			}  
			
		}); //end mebtn_trashrec
	} //end me_trashrec
	
	jQuery('#meyn2_<?=$memodule;?>_yes').click(function() { 
		try { 
			//__mysys_apps.mepreloader('mepreloaderme',true);
			jQuery('#memsgdelrec').css('display','');
			var medeltr = jQuery('#medelrecdata').attr('data-metrid');
			var data_mtknid = jQuery('#medelrecdata').attr('data-mtknid');
			var data_rectype = jQuery('#medelrecdata').attr('data-rectype');
			var mtkn_etr = jQuery('#medelrecdata').attr('data-mtkn_etr');
			var data_itemvalue = jQuery('#medelrecdata').attr('data-itemvalue');
			var my_data = new FormData();
			my_data.append('data_mtknid',data_mtknid);
			my_data.append('data_rectype',data_rectype);
			my_data.append('medeltr',medeltr);
			my_data.append('mtkn_etr',mtkn_etr);
			if(data_rectype == 'dt') {  
				if(jQuery.trim(data_mtknid) == '' || jQuery.trim(data_mtknid) == 'undefined') { 
					var mobj = jQuery('#' + medeltr);
					var trtxt = jQuery(mobj).find("input[type=text]"),
					trNum  = jQuery(mobj).find("input[type=number]"),
					_itemc_        = trtxt.eq(0).val(),
					_itemc_ded_    = trNum.eq(0).val(),
					_itemc_add_    = trNum.eq(1).val(),
					_itemc_rmk_     = trtxt.eq(3).val(),
					_itemc_dtattr = trtxt.eq(0).attr('data-mtkndt');
					if(jQuery.trim(_itemc_) !== '' && (_itemc_ded_ != 0 || _itemc_add_ != 0)) { 
						my_data.append('mearray[]',_itemc_ + 'x|x' + _itemc_ded_ + 'x|x' + _itemc_add_ + 'x|x' + _itemc_rmk_ + 'x|x' + _itemc_dtattr);
					} 
					
				}
			}
			
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>myinventory-recon-adj-delrec',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsgdelrec').html(data);
					jQuery('#meyn2_<?=$memodule;?>_yes').prop('disabled',true);
					return false;
				},
				error: function() { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 			
			jQuery('#meyn2_<?=$memodule;?>_no').html('Close');
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}  
		
	}); //end delete detail recs 
				
	
	<?php
	if (count($arecs) > 0):
		for($xx = 0;$xx < count($arecs); $xx++):
			$chtml = "
			__me_item_code_event('#_meitemc_{$arecs[$xx]}');
			";
			echo $chtml;
		endfor;
	endif;
	?>
</script>
