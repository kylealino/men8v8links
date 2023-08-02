<?php
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$mysalesdepo = model('App\Models\MySalesDepositModel');

//date_default_timezone_set('Asia/Manila');

$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$cuserrema = $myusermod->mysys_userrema();

$transhd_rid = '';
$mtkn_etr = trim($request->getVar('mtkn_etr'));
$sysctrl_seqn = '';
$comp_name = '';
$txtdf_tag = '';
$mtkn_attr_comp = '';
$mtkn_attr_bid = '';
$dterqst = '';
$trx_group = '';
$nrecs = 0;
if(!$myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0006','000101')) { 
	echo "
	<div class=\"row mt-1 ms-1 me-1\">
		<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
	</div>
	";
	
} else {
	$adftag = $mysalesdepo->lk_Active_BRDF($db_erp);
	if(!empty($mtkn_etr)) {
		//editing of records
		if(!$myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0006','000103')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted - [EDIT].<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		} 
		
		$str = "SELECT aa.`recid`,bb.`COMP_NAME`,cc.`BRNCH_NAME`,aa.`salesDate`,aa.`sysctrl_seqn`,aa.`df_tag`,aa.`groupTag`
				FROM {$db_erp}.`trx_ap_trns_deposit_hd` AS aa 
				JOIN {$db_erp}.`mst_company` AS bb ON bb.`recid` = aa.`comprid` 
				JOIN {$db_erp}.`mst_companyBranch` AS cc ON cc.`recid` = aa.`brnchrid`
				where (sha2(concat(aa.`recid`,'{$mpw_tkn}'),384)) = '{$mtkn_etr}'";
		$qry = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw           = $qry->getRowArray();
		$comp_name    = $rw['COMP_NAME'];
		$brnch_name   = $rw['BRNCH_NAME'];
		$sysctrl_seqn = $rw['sysctrl_seqn'];
		$dterqst      = substr($rw['salesDate'],0,10); 
		$transhd_rid  =  $rw['recid'];
		$txtdf_tag    =  $rw['df_tag'];
		$trx_group    =  $rw['groupTag'];
	} 
	else
	{
		if($cuserrema == 'B'):
			$ua_brnch = $myusermod->ua_brnch($db_erp,$cuser);
			$get_compBranch = "SELECT cc.`BRNCH_NAME`,bb.`COMP_NAME`,
			sha2(concat(bb.`recid`,'{$mpw_tkn}'),384) mtkn_attr_comp,
			sha2(concat(cc.`BRNCH_CODE`,'{$mpw_tkn}'),384) mtkn_attr_bid  
			FROM  {$db_erp}.`mst_company` AS bb
			JOIN {$db_erp}.`mst_companyBranch` AS cc ON cc.`COMP_ID` = bb.`recid` 
			WHERE cc.`recid` = 	'{$ua_brnch[0]}'";
			$qry = $mylibzdb->myoa_sql_exec($get_compBranch,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $qry->getRowArray();
			$brnch_name  = $rw['BRNCH_NAME'];
			$comp_name   = $rw['COMP_NAME'];
			$mtkn_attr_comp = $rw['mtkn_attr_comp'];
			$mtkn_attr_bid = $rw['mtkn_attr_bid'];
		endif;
	}	
	$qry->freeResult();
	
?>
<div class="row mt-2 ms-1 me-1">
    <div class="col-md-8">
        <div class="card">
            <div class="row mb-3 mt-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Control Number</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" name="_deposit_rid" id="_deposit_rid" value="<?=$sysctrl_seqn?>" data-mtkn_etr="<?=$mtkn_etr?>" readonly />
                </div>
            </div>
			<div class="row mb-3 ms-1 me-1">
				<div class="col-sm-3">
					<span class="fw-bold">Company Name</span>
				</div>
				<div class="col-sm-9">
					<input type="text" class="form-control form-control-sm" id="_trns_comp" data-mtknattr="<?=$mtkn_attr_comp;?>" value="<?=$comp_name?>"/>
				</div>
			</div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Branch</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" id="_trns_brnch" value="<?=$brnch_name?>" required data-mtknattr="<?=$mtkn_attr_bid;?>"/>
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Sales Date</span>
                </div>
                <div class="col-sm-9">
                    <input type="date" class="form-control form-control-sm" data-id="" id="_trns_DteRqst" name="_trns_DteRqst" value="<?=$dterqst;?>" required/>
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">D/F Tag</span>
                </div>
                <div class="col-sm-9">
                    <?=$mylibzsys->mypopulist_2($adftag,$txtdf_tag,'fld_dftag','class="form-control form-control-sm" ','','');?>
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Group</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" data-id="" id="_trns_group" name="_trns_group" value="<?=$trx_group;?>" required/>
                </div>
            </div>
			<?php if(!empty($transhd_rid)): ?>
			<div class="row mb-3 ms-1 me-1">
				<div class="col-sm-9">
					<div class="form-check form-switch">
						<input class="form-check-input" type="checkbox" id="flexSwitchChecksalesdeporeupload" name="_trxsaledep_reupld">
						<label class="form-check-label fw-bold" for="flexSwitchChecksalesdeporeupload">Re-upload</label>
					</div>
				</div>
			</div>
			<?php endif;?>
        </div>        
    </div> <!-- end col-8 -->    
    <div class="col-md-4">
		<div class="alert alert-dark fade show" role="alert">
			<h4 class="alert-heading">Attach Deposit Slips</h4>
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
</div> <!-- end row mt-1 ms-1 me-1 -->
<div class="row mt-2 ms-1 me-1">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="metblentry-font" id="_tbl_deposit">
				<thead>
					<?php if($txtdf_tag == '' || $txtdf_tag == 'D'):?>
					<th class="text-center">
						<button type="button" id="__salesdepo_addrow" class="btn btn-sm btn-success p-1 pb-0 mebtnpt1" onclick="javascript: rfp_addRows();">
							<i class="bi bi-plus"></i>
						</button>
					</th>
					<?php endif;?>
					<th nowrap>Bank Name</th>
					<th nowrap>Account Number</th>
					<th nowrap>Date of Actual Deposit</th>
					<th nowrap>Sales</th>
					<th nowrap>Shopeepay</th>
					<th nowrap>Expense</th>
					<th nowrap>Amount Deposited</th>
					<th nowrap hidden>Group</th>
					<th nowrap>Remarks</th>
				</thead>
				<tbody id="_tbl_deposit_contentArea">
				<?php
				$str = "SELECT 
				   sha2(concat(`recid`,'{$mpw_tkn}'),384) mtkn_dt_rid,
				   sha2(concat(`bankAcctID`,'{$mpw_tkn}'),384) mtkn_acct_rid,
				  `trnsrefrid`,
				  `bankName`,
				  `accountName`,
				  `dateDeposit`,
				  `sales`,
				  `expense`,
				 `shopeepay`,
				  `amountDeposited`,
				  `rems`,
				  `dep_group`,
				  `m_user`,
				  `m_encdte`
					FROM
					  {$db_erp}.`trx_ap_trns_deposit_dt` 
					where trnsrefrid = '{$transhd_rid}'";

				$qdt = $mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$nporecs = 0;
				foreach($qdt->getResultArray() as $rdt) { 
					$nporecs++;
					$nrecs++;
					$datedeposit = substr($rdt['dateDeposit'],0,10); 
				?>	
					<tr id="tr_rec<?=$nporecs;?>">
						<?php if($txtdf_tag == '' || $txtdf_tag == 'D'):?>
						<td >
							<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1 medelrecsaledepo" value="<?=$rdt['mtkn_dt_rid']?>" data-rectype="dt" data-mtknid="<?=$rdt['mtkn_dt_rid']?>" onClick="javascript:medelrec_saledepodt(this);">
								<i class="bi bi-trash"></i>
							</button>
						</td>
						<?php endif;?>
						<td >
							<input type="text" id="bankname_<?=$nporecs?>" class="bankNameAcct" size="30" title="<?=$rdt['bankName']?>" value="<?=$rdt['bankName']?>" data-mtnkattr="<?=$rdt['mtkn_acct_rid']?>" />
						</td>
						<td>
							<input type="text" id="accountnumber_<?=$nporecs?>" size="30" title="<?=$rdt['accountName']?>" value="<?=$rdt['accountName']?>" data-mtnkattr="<?=$rdt['mtkn_dt_rid']?>" readonly />
						</td>
						<td>
							<input type="date" name="_trns_dsDte_<?=$nporecs?>" id="_trns_dsDte_<?=$nporecs?>" placeholder="mm/dd/yyyy" value="<?=$datedeposit?>">
						</td>
						<td >
							<input type="number" name="rfp_trns_amt_<?=$nporecs?>" id="rfp_trns_amt_<?=$nporecs?>" class="me-running-total-rfp text-end" value="<?=$rdt['sales']?>"  onblur="me_tamtrfp()"/>
						</td>
							<td >
							<input type="number" name="rfp_trns_amt_<?=$nporecs?>" id="rfp_trns_amt_<?=$nporecs?>" class="me-running-total-rfp text-end" value="<?=$rdt['shopeepay']?>"  onblur="me_tamtrfp()"/>
						</td>
							<td >
							<input type="number" name="rfp_trns_amt_<?=$nporecs?>" id="rfp_trns_amt_<?=$nporecs?>" class="me-running-total-rfp text-end" value="<?=$rdt['expense']?>"  onblur="me_tamtrfp()"/>
						</td>
						<td >
							<input type="number" name="rfp_trns_amt_<?=$nporecs?>" id="rfp_trns_amt_<?=$nporecs?>" class="me-running-total-rfp text-end" value="<?=$rdt['amountDeposited']?>"  onblur="me_tamtrfp()"/>
						</td>
						<td hidden>
							<input type="text" name="_group<?=$nporecs?>" class="depositgroup" value="<?= $rdt['dep_group']?>">
						</td>
						<td >
							<input type="text" name="_rems<?=$nporecs?>" size="30" value="<?= $rdt['rems']?>">

						</td>
					</tr> 
					<?php 
					} //end foreach 
					$qdt->freeResult();
					?>
					<tr style="display:none;">
						<td>
							<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" data-rectype="dt" onClick="javascript:medelrec_saledepodt(this);" >
								<i class="bi bi-trash"></i>
							</button>
						</td>
						<td>
							<input type="text" class="bankNameAcct" size="30" data-mtnkattr="" />
						</td>
						<td>
							<input type="text" id="_acct_name" size="30" readonly />
						</td>
						<td><input type="date" class="dep_date" name="_trns_dsDte_" id="_dep_Date_" placeholder="mm/dd/yyyy" value=""></td>
						<td ><input type="number" class="me-running-total-rfp text-end" placeholder="0.00" onblur="javascript:me_tamtrfp();" /></td>
						<td ><input type="number" class="me-running-total-rfp text-end" placeholder="0.00" onblur="javascript:me_tamtrfp();" /></td>
						<td ><input type="number" class="me-running-total-rfp text-end" placeholder="0.00" onblur="javascript:me_tamtrfp();" /></td>
						<td ><input type="number" class="me-running-total-rfp text-end" placeholder="0.00" onblur="javascript:me_tamtrfp();" /></td>
						<td hidden><input type="text"  name=""></td>
						<td ><input type="text" name="" size="30"></td>
					</tr>               
				</tbody>
			</table>
			<br/>
		</div>
	</div>
</div>  <!-- end row mt-1 ms-1 me-1 2nd -->
<div class="row mt-2 mb-3 ms-1 me-1">
	<div class="col-sm-6">
		<?php if($txtdf_tag == '' || $txtdf_tag == 'D'):?>
		<button class="btn btn-success btn-sm" id="btn_salesdepo_save" name="btn_salesdepo_save" type="submit">Save</button>
		<?php endif; ?>
		<button class="btn btn-info btn-sm" id="btn_saledepo_newtrx" name="btn_saledepo_newtrx">New Transaction</button>
	</div>
</div>

<script type="text/javascript"> 
	<?php
	if($nrecs > 0) { 
		echo "__my_branchAcct_lookup();";
	}
	?>
	__mysys_apps.meTableSetCellPadding("_tbl_deposit",3,"1px solid #7F7F7F");
	
	jQuery('#_trns_brnch')
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
					jQuery('#_trns_brnch').val(terms);
					jQuery(this).attr('data-mtknattr',mtknr_rid);
					jQuery('#_trns_comp').attr('data-mtknattr',ui.item.mtkn_comp_rid);
					jQuery(this).autocomplete('search', jQuery.trim(terms));
					return false;
				} 
			})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	}); // end _trns_brnch
	
	
	jQuery('#_trns_group')
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
			source: '<?= site_url(); ?>mysales-deposit-get-group',
			focus: function() {
					// prevent value inserted on focus
					return false;
				},
				search: function(oEvent, oUi) {
					var sValue = jQuery(oEvent.target).val();
					jQuery(this).autocomplete('option', 'source', '<?=site_url();?>mysales-deposit-get-group'); 
				},
				select: function( event, ui ) {
					var terms = ui.item.value;
					jQuery('#_trns_group').val(terms);
					jQuery(this).autocomplete('search', jQuery.trim(terms));
					jQuery('#_trns_group').attr("disabled", true);
					return false;
				}
			})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	}); // end _trns_group
	
	jQuery('#btn_salesdepo_save').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var my_data = new FormData();
			var _deposit_rid = jQuery('#_deposit_rid').val();
			var bid_mtknattr = jQuery('#_trns_brnch').attr('data-mtknattr');
			var cid_mtknattr = jQuery('#_trns_comp').attr('data-mtknattr');
			var fld_stinqbrbranch = jQuery('#_trns_brnch').val();
			var _trns_comp = jQuery('#_trns_comp').val();
			var _trns_brnch = jQuery('#_trns_brnch').val();
			var _trns_DteRqst = jQuery('#_trns_DteRqst').val();
			var _trns_dftag = jQuery('#fld_dftag').val();
			var _trns_group = jQuery('#_trns_group').val();
			var _trns_reupload = jQuery('#flexSwitchCheckCheckedsalesdeporeupload').prop('checked');
			my_data.append('_deposit_rid',_deposit_rid);
			my_data.append('_trns_comp',_trns_comp);
			my_data.append('_trns_brnch',_trns_brnch);
			my_data.append('bid_mtknattr',bid_mtknattr);
			my_data.append('cid_mtknattr',cid_mtknattr);
			my_data.append('_trns_DteRqst',_trns_DteRqst);
			my_data.append('_trns_group',_trns_group);
			my_data.append('_trns_dftag',_trns_dftag);
			my_data.append('_trns_reupload',_trns_reupload);
			
			var mfileattach = '__upld_file_img01';
			var mfiles = document.getElementById(mfileattach);
			var filesCount = 0;
			if(mfiles.files.length > 0) { 
				jQuery.each(mfiles.files, function(k,file){ 
					my_data.append('mefiles[]', file);
					filesCount++;
				});
			}
			
			if(jQuery.trim(_trns_dftag) == '') {
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsg-salesdepo_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Please select D/F Tag.</strong></div>");
				jQuery('#memsg-salesdepo').modal('show');
				return false;
			}
			
			if(jQuery.trim(_trns_group) == '') {
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsg-salesdepo_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Please select Group.</strong></div>");
				jQuery('#memsg-salesdepo').modal('show');
				return false;
			}
			
			if(_trns_dftag == 'F' || _trns_reupload ) { 
				if (filesCount == 0) {
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsg-salesdepo_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Please select file attachment!!!</strong></div>");
					jQuery('#memsg-salesdepo').modal('show');
					return false;
				}
			}
			
			var _tbl_deposit = jQuery('#_tbl_deposit');
			var mearray = [];
			var i=0;
			var sep = '';
			var counts =_tbl_deposit.find('tr').length - 2;
			var counttrvalid = 0;
			var invalid = 0;
			_tbl_deposit.find('tr').each(function (i, el) { 
				var tds = jQuery(this).find('td'),
				tr = jQuery(this).find("input[type=text]"),
				trHid  = jQuery(this).find("input[type=hidden]"),
				trNum  = jQuery(this).find("input[type=number]"),
				trdtdt = jQuery(this).find("button"),
				trdate = jQuery(this).find("input[type=date]"),
				_bank_name_        = tr.eq(0).val(),
				_acct_name         = tr.eq(1).val(),
				_date_deposit_     = trdate.eq(0).val(),
				_group_            = tr.eq(2).val(),
				_remarks_          = tr.eq(3).val(),
				_sales_            = trNum.eq(0).val(),
				_shopeepay_        = trNum.eq(1).val(),
				_expense_          = trNum.eq(2).val(),
				_amount_deposited_ = trNum.eq(3).val(),
				_trdtdt            = trdtdt.eq(0).val(),
				_rid_sv            = tr.eq(1).attr('data-mtknattr'),
				_acct_rid          = tr.eq(0).attr('data-mtknattr');
				if(_acct_rid == '' && jQuery.trim(_bank_name_) !== '' ){
					invalid = 1;
				}
				//melem = document.getElementById(tr.eq(1).attr('id')).getAttribute('id');
								
				if(jQuery.trim(_bank_name_) !== '' && jQuery.trim(_acct_name) != 'undefined' && jQuery.trim(_amount_deposited_) != 'undefined'){ 
					counttrvalid++;
					if(jQuery.trim(_acct_name) == '' || jQuery.trim(_amount_deposited_) == '' || jQuery.trim(_date_deposit_) == '' ) {
					invalid = 1;
					}
					else{
						sep = (mearray != '') ?'x|' :'';
						mearray.push(sep+_bank_name_+'x|x'+_acct_name+'x|x'+_amount_deposited_+'x|x'+_rid_sv+'x|x'+_trdtdt+'x|x'+_date_deposit_+'x|x'+_remarks_+'x|x'+_group_+'x|x'+_acct_rid+'x|x'+_sales_+'x|x'+_shopeepay_+'x|x'+_expense_);
					}
					
				}
			});
			
			if(mearray.length == 0 || invalid == 1){ 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsg-salesdepo_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Invalid ENTRIES!!!</strong></div>");
				jQuery('#memsg-salesdepo').modal('show');
				return false;
			}	
			my_data.append('mearray',mearray);
			
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>mysales-deposit-save',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsg-salesdepo_bod').html(data);
					jQuery('#memsg-salesdepo').modal('show');
					return false;
				},
				error: function() { 
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
	
	me_tamtrfp();
	function me_tamtrfp(){
		var totalsum=0;
		jQuery('#_tbl_deposit tr').each(function() { 
			jQuery(this).find('td input[type=number]').eq(0).each(function() {
				var inputVal = jQuery(this).val();
				if(jQuery.isNumeric(inputVal)){
					totalsum += parseFloat(inputVal);
				}
				jQuery('#__meTotalAmtrfp').html(__mysys_apps.oa_addCommas(totalsum.toFixed(2)));
			})
		});
	}  //end me_tamtrfp
	
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
	
	
	function medelrec_saledepodt(cobj) { 
		try {
			//jQuery(cobj).parent().parent().remove();
			var trid = jQuery(cobj).parent().parent().attr('id');
			var data_mtknid = jQuery(cobj).attr('data-mtknid');
			var data_rectype = jQuery(cobj).attr('data-rectype');
			var mtkn_etr = '<?=$mtkn_etr;?>';
			if(jQuery.trim(mtkn_etr) == '') {
				mtkn_etr = jQuery('#_deposit_rid').attr('data-mtkn_etr');
			}
			var memsg = "<input type=\"hidden\" id=\"medelrecdata\" data-metrid=\"" + trid + "\" data-mtknid=\"" + data_mtknid + "\" data-mtkn_etr=\"" + mtkn_etr + "\" data-rectype=\"" + data_rectype + "\" />";
			memsg += "<div id=\"memsgdelrec\" style=\"display:none;\"></div>";
			jQuery('#meyn_salesdepo_bod').html('<span class=\"fw-bold text-danger\">Selected record is no longer available and permanently deleted...<br\>Proceed anyway?</span>' + memsg);
			jQuery('#staticBackdropmeyn_salesdepo').html('<span class=\"fw-bold\">Sales Deposit Record Detail Deletion...</span>');
			jQuery('#meyn_salesdepo_yes').prop('disabled',false);
			jQuery('#meyn_salesdepo_no').html('No');
			jQuery('#meyn_salesdepo').modal('show');
			
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
		}  //end try 
	} //end medelrec_saledepodt
	
	jQuery('#meyn_salesdepo_yes').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			jQuery('#memsgdelrec').css('display','');
			var medeltr = jQuery('#medelrecdata').attr('data-metrid');
			var data_mtknid = jQuery('#medelrecdata').attr('data-mtknid');
			var data_rectype = jQuery('#medelrecdata').attr('data-rectype');
			var mtkn_etr = jQuery('#medelrecdata').attr('data-mtkn_etr');
			var my_data = new FormData();
			my_data.append('data_mtknid',data_mtknid);
			my_data.append('data_rectype',data_rectype);
			my_data.append('medeltr',medeltr);
			my_data.append('mtkn_etr',mtkn_etr);
			if(data_rectype == 'dt') {  
				if(jQuery.trim(data_mtknid) == '' || jQuery.trim(data_mtknid) == 'undefined') { 
					var mobj = jQuery('#' + medeltr);
					var mearray = [];
					var invalid = 0;
					var tr = jQuery(mobj).find("input[type=text]"),
					trHid  = jQuery(mobj).find("input[type=hidden]"),
					trNum  = jQuery(mobj).find("input[type=number]"),
					trdtdt = jQuery(mobj).find("button"),
					trdate = jQuery(mobj).find("input[type=date]"),
					_bank_name_        = tr.eq(0).val(),
					_acct_name         = tr.eq(1).val(),
					_date_deposit_     = trdate.eq(0).val(),
					_group_            = tr.eq(2).val(),
					_remarks_          = tr.eq(3).val(),
					_sales_            = trNum.eq(0).val(),
					_shopeepay_        = trNum.eq(1).val(),
					_expense_          = trNum.eq(2).val(),
					_amount_deposited_ = trNum.eq(3).val(),
					_trdtdt            = trdtdt.eq(0).val(),
					_rid_sv            = tr.eq(1).attr('data-mtknattr'),
					_acct_rid          = tr.eq(0).attr('data-mtknattr');
					if(jQuery.trim(_bank_name_) !== '' && jQuery.trim(_acct_name) != 'undefined' && jQuery.trim(_amount_deposited_) != 'undefined'){ 
						if(jQuery.trim(_acct_name) == '' || jQuery.trim(_amount_deposited_) == '' || jQuery.trim(_date_deposit_) == '' ) {
							invalid = 1;
						}
						else{
							mearray.push(_bank_name_+'x|x'+_acct_name+'x|x'+_amount_deposited_+'x|x'+_rid_sv+'x|x'+_trdtdt+'x|x'+_date_deposit_+'x|x'+_remarks_+'x|x'+_group_+'x|x'+_acct_rid+'x|x'+_sales_+'x|x'+_shopeepay_+'x|x'+_expense_);
							my_data.append('mearray',mearray);
						}
					}
				} //end if 
			}  //end if 
			
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>mysales-deposit-delrec',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsgdelrec').html(data);
					jQuery('#meyn_salesdepo_yes').prop('disabled',true);
					return false;
				},
				error: function() { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 			
			jQuery('#meyn_salesdepo_no').html('Close');
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
		}  //end try 
	});  //end meyn_salesdepo_yes
	
	jQuery('#btn_saledepo_newtrx').click(function() { 
		try { 
			jQuery('#meyn2_salesdepo_bod').html('<span class=\"fw-bold text-danger\">Be sure to save all changes to prevent any data loss...<br\>Proceed anyway?</span>');
			jQuery('#staticBackdropmeyn2_salesdepo').html('<span class=\"fw-bold\">Sales Deposit...</span>');
			jQuery('#meyn2_salesdepo_yes').prop('disabled',false);
			jQuery('#meyn2_salesdepo_no').html('No');
			jQuery('#meyn2_salesdepo').modal('show');
		} catch(err) { 
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
		}  //end try 
	}); //end btn_saledepo_newtrx
	
	jQuery('#meyn2_salesdepo_yes').click(function() { 
		try { 
			window.location.href = '<?=site_url();?>mysales-deposit';
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
		}  //end try 
	});
	
</script>
<?php
}
?>
