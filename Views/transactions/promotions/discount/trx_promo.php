<?php

//VARIABLE DECLARATIONS
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$this->myfpdmod = model('App\Models\MyPromoDiscountModel');
$this->mydataz = model('App\Models\MyDatumModel');
$this->db_erp = $this->myusermod->mydbname->medb(0);
$cuser = $this->myusermod->mysys_user();
$mpw_tkn = $this->myusermod->mpw_tkn();

$branch_code = '';
$mtkn_txt_branch = '';
$branch_name = '';
$mencd_date       = date("Y-m-d");  
$mtkn_mntr = $this->myusermod->request->getVar('mtkn_mntr');
$recid = '';
$nporecs = 0;
$txt_promotrxno = '';
$txt_promoname = '';
$txt_promodiscval = 0;
$branch_code = '';
$start_date = ''; 
$start_time = date('08:00');
$end_date = ''; 
$end_time = date('23:59');
$invalid_disc ='76';
$is_fixed_price ='';
$is_fixed_price_checked = '';
$is_discount_percent= '';
$is_discount_percent_checked = '';
$chkbox1 = '0';
$chkbox2 = '0';
$discount_value='';
$ART_DESC ='';
$ART_BARCODE1='';
$ART_UCOST='';
$cb_value = '';
$ART_UPRICE='';
$ART_CODE = '';
$mtkn_brid = '';
//CHECK IF THERE'S A FORM OF RETRIEVAL
$chtml_br = '';
$disable_ifapproved = '';
$mehide_col_buttons = '';
if(!empty($mtkn_mntr)) {
  $str = "
  select aa.`promo_trxno`,
  aa.`promo_name`,
  aa.`branch_code`,
  cc.`BRNCH_NAME`,
  aa.`disc_value`,
  aa.`start_date`,
  aa.`start_time`,
  aa.`end_date`,
  aa.`end_time`,
  aa.is_approved,
  if(aa.`is_fixed_price` = 1,1,2) p_is_fixed_price,
  sha2(concat(cc.recid,'{$mpw_tkn}'),384) `mtkn_brid` 
  from {$this->db_erp }.`trx_pos_promo_fpd_hd` aa 
  join {$this->db_erp }.`trx_pos_promo_fpd_dt` bb 
  on aa.`promo_trxno` = bb.`promo_trxno`
  join {$this->db_erp }.`mst_companyBranch` cc
  on aa.`branch_code` = cc.`BRNCH_OCODE2`
  join {$this->db_erp }.`mst_article` dd
  on bb.`prod_barcode` = dd.`ART_BARCODE1` 
  where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_mntr'
  ";
  $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
  $rw = $q->getRowArray();
  if (!empty($rw['promo_trxno'])):
	  $txt_promotrxno = $rw['promo_trxno'];
	  $txt_promoname = $rw['promo_name'];
	  $branch_code = $rw['branch_code'];
	  $txt_promodiscval = $rw['disc_value'];
	  $start_date = $rw['start_date'];
	  $start_time = $rw['start_time'];
	  $end_date = $rw['end_date'];
	  $end_time = $rw['end_time'];
	  $branch_code = $rw['branch_code'];
	  $branch_name = $rw['BRNCH_NAME'];
	  $is_fixed_price = ($rw['p_is_fixed_price'] == 1 ? 1 : 0);
	  $is_fixed_price_checked = ($rw['p_is_fixed_price'] == 1 ? ' checked' : '');
	  $is_discount_percent=  ($rw['p_is_fixed_price'] == 2 ? 2 : 0);
	  $is_discount_percent_checked = ($rw['p_is_fixed_price'] == 2 ? ' checked' : '');
	  $mtkn_brid = $rw['mtkn_brid'];
	  $chtml_br = ' disabled ';
	  $disable_ifapproved = ($rw['is_approved'] == 'Y' ? ' disabled ' : '');
	  $mehide_col_buttons = ($rw['is_approved'] == 'Y' ? ' style="display:none" ' : '');
  endif;
  $q->freeResult();
}

$memodule = '_metrx_promo_fpd_';

echo view('templates/meheader01');
?>
<main id="main" class="main">
	<div class="card">
		<div class="card-body">
			<br>
			<div class="pagetitle">
				<h1>Promotion</h1>
				<nav>
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.html">Home</a></li>
						<li class="breadcrumb-item">Sales</li>
						<li class="breadcrumb-item active">Promotion - DISCOUNT</li>
					</ol>
				</nav>
			</div> <!-- End Page Title -->
			<!-- START HEADER DATA -->
			<div class="row mb-3">
				<div class="col-lg-12">
					<div class="row mb-3">
						<label class="col-sm-3 form-label" for="branch_code">Branch:</label>
						<div class="col-sm-9">
							<input type="text" data-id-brnch-name="<?=$branch_name;?>" data-mtknid="<?=$mtkn_brid;?>" placeholder="Branch Name" id="branch_name" name="branch_name" class="form-control form-control-sm " value="<?=$branch_name;?>" <?=$chtml_br;?> required/>
						</div>
					</div>
					<div class="row mb-3">
						<label class="col-sm-3 form-label" for="txt_promotrxno">Promo Trx No.:</label>
						<div class="col-sm-9">
							<input type="text" id="txt_promotrxno" name="txt_promotrxno" placeholder="Promo Discount Transaction Number" data-mtknattr="<?=$mtkn_mntr;?>" class="form-control form-control-sm" value="<?=$txt_promotrxno;?>" readonly/>
						</div>
					</div> 
					<div class="row mb-3">
						<label class="col-sm-3 form-label" for="txt_promodesc">Promo Name:</label>
						<div class="col-sm-9">
							<input type="text" id="txt_promodesc" name="txt_promodesc" placeholder="Promo Description" class="form-control form-control-sm" readonly value="<?=$txt_promoname;?>" />
						</div>
					</div> 
					<div class="row mb-3">
						<label class="col-sm-3 form-label" for="txt_promodiscval">Promo Discount Value:</label>
						<div class="col-sm-9">
							<input type="text" id="txt_promodiscval" name="txt_promodiscval" placeholder="Promo Discount Value" class="form-control form-control-sm fw-bolder" value="<?=$txt_promodiscval;?>" />
						</div>
					</div> 
					<!-- group entries -->
					<div class="row gy-2 offset-lg-3">
						<div class="col-sm-3">
							<input type="date"  id="start_date" name="start_date" class="start_date form-control form-control-sm " value="<?=$start_date;?>" required/>
							<label for="start_date">Start date</label>
						</div>

						<div class="col-sm-3">
							<input type="time" id="start_time" name="start_time" class="start_time form-control form-control-sm " value="<?=$start_time;?>"  required/>
							<label for="">Time</label>
						</div>

						<div class="col-sm-3">
							<input type="date" id="end_date" name="end_date" class="end_date form-control form-control-sm " value="<?=$end_date;?>"  required/>
							<label for="">End date</label>
						</div>

						<div class="col-sm-3">
							<input type="time" id="end_time" name="end_time" class="end_time form-control form-control-sm " value="<?=$end_time;?>"  required/>
							<label for="">Time</label>
						</div>
					
						<div class="col-sm-3">
							<div class="form-check form-switch">
								<input class="is_fixed_price form-check-input" type="radio" name="cb" value="<?=$is_fixed_price;?>" id="is_fixed_price" onchange="selectCb(this)" <?=$is_fixed_price_checked;?>>
								<label class="form-check-label" for="is_fixed_price" id="fixedlbl">
								Fixed Price
								</label>
							</div>

							<div class="form-check form-switch">
								<input class="is_discount_percent form-check-input" type="radio" name="cb" value="<?=$is_discount_percent;?>"  id="is_discount_percent" onchange="selectCb(this)" <?=$is_discount_percent_checked;?>>
								<label class="form-check-label" for="is_discount_percent" id="discountlbl">
								Percentage
								</label>
							</div>
						</div>  
					</div> <!-- end group entries -->
				</div>  
			</div>
			<!-- END HEADER DATA -->

			<!-- START DETAILS DATA -->
			<div class="row">
				<div class=" table-responsive">
					<table id="__tbl_<?=$memodule;?>" class="metblentry-font table-bordered" >
						<!-- TABLE HEADER -->
						<thead class="text-center">
							<tr>
								<th class="text-center"><i id="me-tbl-promo" class="text-white fas fa-sync"> </i></th>
								<th class="text-center" <?=$mehide_col_buttons;?>>
									<button type="button" class="btn btn-sm btn-success p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item_promo();" <?=$disable_ifapproved;?>>
										<i class="bi bi-plus-lg"></i>
									</button>
								</th>
								<th nowrap="nowrap">Item Code</th>
								<th nowrap="nowrap">Description</th>
								<th nowrap="nowrap">Barcode</th>
								<th nowrap="nowrap">Orig SRP</th>
								<th nowrap="nowrap">Discounted SRP</th>
							</tr>
						</thead>
						<tbody>
							<!-- FOR RETRIEVAL OF EXISTING PROMO TRANSACTION NO. DATA -->

							<?php if (!empty($txt_promotrxno)): 
							  
								$str = "
								select 
									sha2(concat(cc.recid,'{$mpw_tkn}'),384) `mtkn_attr`, 
								bb.`discount_value`,
								bb.`discount_srp`,
								dd.`ART_CODE`,
								dd.`ART_DESC`,
								dd.`ART_BARCODE1`,
								dd.`ART_UCOST`,
								dd.`ART_UPRICE`,
									sha2(concat(dd.recid,'{$mpw_tkn}'),384) `mtkn_artmattr` 
								from {$this->db_erp }.`trx_pos_promo_fpd_hd` aa 
								join {$this->db_erp }.`trx_pos_promo_fpd_dt` bb 
								on aa.`promo_trxno` = bb.`promo_trxno`
								join {$this->db_erp }.`mst_companyBranch` cc 
								on aa.`branch_code` = cc.`BRNCH_OCODE2` 
								join {$this->db_erp }.`mst_article` dd
								on bb.`prod_barcode` = dd.`ART_BARCODE1`
								where aa.`promo_trxno` = '$txt_promotrxno' 
								";
							  
							  $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							  $rw = $q->getResultArray();
							  foreach ($rw as $data) {
								$nporecs++;
								$mid = $this->mylibzsys->random_string(10);
								$metr_rec = 'metr_rec_' . $mid;
								$discount_value = $data['discount_value'];
								$discount_srp = $data['discount_srp'];
								$ART_CODE=$data['ART_CODE'];
								$ART_DESC = $data['ART_DESC'];
								$ART_BARCODE1 = $data['ART_BARCODE1'];
								$ART_UCOST = $data['ART_UCOST'];
								$ART_UPRICE = $data['ART_UPRICE'];
								//DISPLAY ROW WITH VALUE BASE ON PROMO TRX 
							$chtml = "
							<tr id=\"{$metr_rec}\">
								<td>{$nporecs}</td>
								<td nowrap=\"nowrap\" {$mehide_col_buttons}>
									<button type=\"button\" class=\"btn btn-danger p-1 pb-0 mebtnpt1\" onclick=\"jQuery(this).closest('tr').remove();\" data-mtknattr=\"{$data['mtkn_attr']}\" {$disable_ifapproved}><i class=\"bi bi-trash\"></i></button>
								</td>
								<td nowrap=\"nowrap\"><input type=\"text\" size=\"30\" class=\"mitemcode\" value=\"{$ART_CODE}\" data-mtnkattr=\"{$data['mtkn_artmattr']}\" /></td> <!--0 ITEMC -->
								<td nowrap=\"nowrap\"><input type=\"text\" size=\"45\" value=\"{$ART_DESC}\" readonly /></td> <!--1 DESC -->
								<td nowrap=\"nowrap\"><input type=\"text\" size=\"25\" value=\"{$ART_BARCODE1}\" readonly /></td> <!--1 barcode -->
								<td nowrap=\"nowrap\"><input type=\"text\" id=\"meprice_{$mid}\" size=\"15\" value=\"{$ART_UPRICE}\" class=\"text-end\" onmouseover=\"javascript:__trx_ent_totals();\" readonly /></td> <!--5 TAMT -->
								<td nowrap=\"nowrap\"><input type=\"text\" id=\"metotdisc_{$mid}\" size=\"15\" value=\"{$discount_srp}\" class=\"text-end\" onmouseover=\"javascript:__trx_ent_totals();\"  readonly /></td>
							</tr>";
								echo $chtml;
							  }
							 endif;
							 //style="font-size:15px; padding: 2px 6px 2px 6px; " reduce button size
							 ?>
							<tr style="display: none;">
								<td></td>
								<td nowrap="nowrap" <?=$mehide_col_buttons;?>>
									<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" onclick="jQuery(this).closest('tr').remove();" <?=$disable_ifapproved;?>><i class="bi bi-trash"></i></button>
								</td>
								<td nowrap="nowrap"><input type="text" size="30" class="mitemcode" /></td> <!--0 ITEMC -->
								<td nowrap="nowrap"><input type="text" size="45" readonly /></td> <!--1 DESC -->
								<td nowrap="nowrap"><input type="text" size="25" readonly /></td> <!--1 barcode -->
								<td nowrap="nowrap"><input type="text" size="15" class="text-end" onmouseover="javascript:__trx_ent_totals();" readonly /></td> <!--5 TAMT -->
								<td nowrap="nowrap"><input type="text" size="15" class="text-end" onmouseover="javascript:__trx_ent_totals();" readonly /></td> <!--51 TAMT -->
							</tr>
						</tbody>
					</table>
				</div> <!-- end table responsive -->
			</div> 
			<!-- END DETAILS DATA -->
				  
			<div class="row mt-2 mb-3">
				<div class="col-sm-4">
				<?php if(!empty($disable_ifapproved)):?>
					<button id="mbtn_mn_Save" type="button" style="background-color: #167F92; color: #FFF;" class="btn btn-dgreen btn-sm" disabled>Posted</button> 
				  <?php else:?>
					<button id="mbtn_mn_Save" type="button" style="background-color: #167F92; color: #FFF;" class="btn btn-dgreen btn-sm">Save</button>   
				  <?php endif;?>
					<button id="mbtn_mn_NTRX" type="button" class="btn btn-primary btn-sm">New Entry</button>
				</div>
			</div> <!-- end Save Records -->
		</div> <!-- end card body -->
	</div> <!-- end card -->
        
        <!-- HEADER AND FOR APPROVAL PAGE TAB -->
	<div class="row ">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<h6 class="card-title">Records</h6>
					<div class="pt-2 bg-dgreen mt-2" style="background-color: #167F92;"> 
						<nav class="nav nav-pills flex-column flex-sm-row  gap-1 px-2 fw-bold">
							<a id="anchor-list" class="flex-sm-fill text-sm-center mytab-item active p-2 rounded-top"  aria-current="page" href="#"><i class="bi bi-ui-checks"> </i> Records</a>
							<a id="anchor-items" class="flex-sm-fill text-sm-center mytab-item  p-2 rounded-top" href="#"><i class="bi bi-ui-radios"> </i> For Approval</a>
						</nav>
					</div>
					<!-- DISPLAY OF RECORDS AND APPROVAL -->
					<div id="wg_trx_promo_content" class="text-center p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">
					</div> 
				</div>          
			</div> <!-- end card-body -->
		</div> <!-- end col -->
	</div> 
    <?php
    echo $this->mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
    echo $this->mylibzsys->memypreloader01('mepreloaderme');
    echo $this->mylibzsys->memsgbox1('memsgtestent','System Alert','...');
    ?>  
</main>    
  <script type="text/javascript">

   
	$(document).ready(function() {
		$('#is_discount_percent').click(function(){
			var discount = $('#txt_promodiscval').val();
			var is_discount_percent = jQuery('#is_discount_percent').prop('checked');;
			var cb_fix_discount_percent_value = (is_discount_percent) ? (1) : (0);
			var is_fixed_price = jQuery('#is_fixed_price').prop('checked');
			var cb_fix_value = (is_fixed_price) ? (1) : (0);

			if (cb_fix_discount_percent_value == "1"){
				var promo_name =  'Less ' + discount +'%' ;
			$('#txt_promodesc').val(promo_name);
			}
			if (cb_fix_value == "1"){
			var promo_name =  'Evertying @ ' + discount  ;
			$('#txt_promodesc').val(promo_name);
			}
		
		})
		$('#is_fixed_price').click(function(){
			var discount = $('#txt_promodiscval').val();
			var is_discount_percent = jQuery('#is_discount_percent').prop('checked');;
			var cb_fix_discount_percent_value = (is_discount_percent) ? (1) : (0);
			var is_fixed_price = jQuery('#is_fixed_price').prop('checked');
			var cb_fix_value = (is_fixed_price) ? (1) : (0);

			if (cb_fix_discount_percent_value == "1"){
				var promo_name =  'Less ' + discount +'%' ;
			$('#txt_promodesc').val(promo_name);
			}
			if (cb_fix_value == "1"){
			var promo_name =  'Evertying @ ' + discount  ;
			$('#txt_promodesc').val(promo_name);
			}
			
		})
		$('#txt_promodiscval').change(function(){
			var discount = $('#txt_promodiscval').val();
			var is_discount_percent = jQuery('#is_discount_percent').prop('checked');;
			var cb_fix_discount_percent_value = (is_discount_percent) ? (1) : (0);
			var is_fixed_price = jQuery('#is_fixed_price').prop('checked');
			var cb_fix_value = (is_fixed_price) ? (1) : (0);

			if (cb_fix_discount_percent_value == "1"){
				var promo_name =  'Less ' + discount +'%' ;
			$('#txt_promodesc').val(promo_name);
			}
			if (cb_fix_value == "1"){
			var promo_name =  'Evertying @ ' + discount  ;
			$('#txt_promodesc').val(promo_name);
			}
			
		})
	})

	__mysys_apps.meTableSetCellPadding("__tbl_<?=$memodule;?>",3,"1px solid #7F7F7F");
	__my_item_lookup();
	__trx_ent_totals();
	//PARA SA TIMER NG TAMT TOTALS
	var tid = setInterval(myTamtTimer, 30000); 
	function myTamtTimer() {
		__trx_ent_totals();
		// do some stuff...
		// no need to recall the function (it's an interval, it'll loop forever)
	}

	jQuery('#branch_name')
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
					jQuery('#branch_name').val(terms);
					jQuery('#branch_name').attr('data-mtknid',mtknr_rid);
					jQuery(this).autocomplete('search', jQuery.trim(terms));
					jQuery(this).prop('disabled',true);
					return false;
				}
			})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	}); // end branch_name 	
	
        
        jQuery('#mbtn_mn_NTRX').click(function() { 
          var userselection = confirm("Are you sure you want to new transaction?");
          if (userselection == true){
            window.location = '<?=site_url();?>me-promo-vw';
          }
          else{
            return false;
          } 
        });  //end mbtn_mn_NTRX

        function __do_makeid(){
          var text = '';
          var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

          for( var i=0; i < 7; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));

          return text;
        }

		function my_add_line_item_promo() {
    		try {
        var rowCount = jQuery('#__tbl_<?=$memodule;?> tr').length;
        var mid = rowCount + 1;

        var clonedRow = jQuery('#__tbl_<?=$memodule;?> tr:eq(' + (rowCount - 1) + ')').clone();

        jQuery(clonedRow).find('input[type=text]').eq(0).attr('id', 'mitemcode_' + mid);
        jQuery(clonedRow).find('input[type=text]').eq(1).attr('id', 'mitemdesc_' + mid);
        jQuery(clonedRow).find('input[type=text]').eq(2).attr('id', 'mitembcode_' + mid);
        jQuery(clonedRow).find('input[type=text]').eq(3).attr('id', 'mitemprice_' + mid);
        jQuery(clonedRow).find('input[type=text]').eq(4).attr('id', 'mitemdiscsrp_' + mid);

        jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id', 'mitemrid_' + mid);

        jQuery('#__tbl_<?=$memodule;?> tr').eq(rowCount - 1).before(clonedRow);
        jQuery(clonedRow).css({ 'display': '' });
        jQuery(clonedRow).attr('id', 'metr_rec_' + mid);

        // Store the current mid value in the data attribute of the cloned row
        jQuery(clonedRow).data('current-mid', mid);

        var xobjArtItem = jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
        jQuery('#' + xobjArtItem).focus();

        // Update the index for each row
        jQuery('#__tbl_<?=$memodule;?> tr').each(function (i) {
            jQuery(this).find('td').eq(0).html(i);
            var rowMid = jQuery(this).data('current-mid');
            if (rowMid && rowMid !== (i + 1)) {
                // Update the IDs using the correct index (i + 1)
                jQuery(this).find('input[type=text]').eq(0).attr('id', 'mitemcode_' + (i + 1));
                jQuery(this).find('input[type=text]').eq(1).attr('id', 'mitemdesc_' + (i + 1));
                jQuery(this).find('input[type=text]').eq(2).attr('id', 'mitembcode_' + (i + 1));
                jQuery(this).find('input[type=text]').eq(3).attr('id', 'mitemprice_' + (i + 1));
                jQuery(this).find('input[type=text]').eq(4).attr('id', 'mitemdiscsrp_' + (i + 1));
                jQuery(this).find('input[type=hidden]').eq(0).attr('id', 'mitemrid_' + (i + 1));
                jQuery(this).attr('id', 'metr_rec_' + (i + 1));
                jQuery(this).data('current-mid', (i + 1));
            }
        });

        __my_item_lookup();
        __trx_ent_totals();
    } catch (err) {
        var mtxt = 'There was an error on this page.\\n';
        mtxt += 'Error description: ' + err.message;
        mtxt += '\\nClick OK to continue.';
        alert(mtxt);
        return false;
    }
}
	function __my_item_lookup() { 
		jQuery('.mitemcode' ) 
		.unbind('keydown')
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
			source: '<?= site_url(); ?>get-promo-itemc',
			focus: function() {
				  // prevent value inserted on focus
				  return false;
				},
			search: function(oEvent, oUi) { 
				var sValue = jQuery(oEvent.target).val();
				
				var mtkn_branch = jQuery('#branch_name').attr('data-mtknid');
				var branch_name = jQuery('#branch_name').val();
				
				if(jQuery.trim(branch_name) == '') {
					alert('Branch should be selected first!!!');
					return false;
				}
				
				jQuery(this).autocomplete('option', 'source', '<?=site_url();?>get-promo-itemc/?mtkn_branch=' + mtkn_branch + '&branch_name=' + branch_name);
			},                
				select: function( event, ui ) {
					var terms = ui.item.value;
					jQuery(this).attr('alt', jQuery.trim(ui.item.value));
					jQuery(this).attr('title', jQuery.trim(ui.item.value));
					var ndisc = jQuery('#txt_promodiscval').val();
					this.value = ui.item.value;
					// var clonedRow = jQuery(this).parent().parent().clone();
					var clonedRow = jQuery(this).closest('tr').clone(); // Get the closest row (new row)
					var indexRow = jQuery(this).parent().parent().index();
					var xobjitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).attr('id');  /*DESC*/
					var xobjitembcode = jQuery(clonedRow).find('input[type=text]').eq(2).attr('id');  /*BCODE*/
					var xobjitemprice = jQuery(clonedRow).find('input[type=text]').eq(3).attr('id');  /*PRICE*/
					jQuery(this).attr('data-mtnkattr',ui.item.mtkn_rid);
					jQuery('#' + xobjitemdesc).val(ui.item._DESC);
					jQuery('#' + xobjitembcode).val(ui.item._BARCODE);
					jQuery('#' + xobjitemprice).val(ui.item._UPRICE);
					return false;
				}
			})
		.click(function() { 
			//jQuery(this).keydown(); 
			var terms = this.value;
			//jQuery(this).autocomplete('search', '');
			jQuery(this).autocomplete('search', jQuery.trim(terms));
		});  
	}  //end __my_item_lookup

        
	function __trx_ent_totals() { 
		var is_discount_percent = jQuery('#is_discount_percent').prop('checked');;
		var cb_fix_discount_percent_value = (is_discount_percent) ? (1) : (0);
		var is_fixed_price = jQuery('#is_fixed_price').prop('checked');
		var cb_fix_value = (is_fixed_price) ? (1) : (0);
		var txt_promodiscval = jQuery('#txt_promodiscval').val();
		var ndisc = parseFloat(txt_promodiscval);
		if(isNaN(ndisc)) { 
			ndisc = 0;
		}
		
		var rowCount1 = jQuery('#__tbl_<?=$memodule;?> tr').length - 1;
		
		for(aa = 1; aa < rowCount1; aa++) { 
			var clonedRow = jQuery('#__tbl_<?=$memodule;?> tr:eq(' + aa + ')').clone(); 
			var nprice = jQuery(clonedRow).find('input[type=text]').eq(3).val();
			var xiddiscsrp = jQuery(clonedRow).find('input[type=text]').eq(4).attr('id');
			
			nprice = parseFloat(nprice);
			if(isNaN(nprice)) { 
				nprice = 0.00;
			}
			
			var ndiscsrp = 0.00;
			
			if (cb_fix_value == '1' && ndisc > 0 && nprice > 0 ) { 
				ndiscsrp = parseFloat(nprice - ndisc);
			}
			
			if (cb_fix_discount_percent_value == '1'  && ndisc > 0 && nprice > 0) { 
				ndiscsrp = (nprice - parseFloat((ndisc/100) * nprice));
			}
			
			jQuery('#' + xiddiscsrp).val(ndiscsrp.toFixed(2));
			// console.log(xTAmntId);
		}  //end for 
	}  //end __trx_ent_totals

	jQuery('#__tbl_<?=$memodule;?>').on('keydown', "input", function(e) { 
		switch(e.which) {
			case 37: // left 
				break;
			case 38: // up
				var nidx_rw = jQuery(this).parent().parent().index();
				var nidx_td = jQuery(this).parent().index();
				if(nidx_td == 2) { 
					
				} else { 
					var clonedRow = jQuery('#__tbl_<?=$memodule;?> tr:eq(' + (nidx_rw) + ')').clone(); 
					var el_id = jQuery(clonedRow).find('td').eq(nidx_td).find('input[type=text]').eq(0).attr('id');
					jQuery('#' + el_id).focus();
				}
				break;
			case 39: // right
				break;
			case 40: // down
				var nidx_rw = jQuery(this).parent().parent().index();
				var nidx_td = jQuery(this).parent().index();
				if(nidx_td == 2) { 
				} else { 
					var clonedRow = jQuery('#__tbl_<?=$memodule;?>  tr:eq(' + (nidx_rw + 2) + ')').clone(); 
					var el_id = jQuery(clonedRow).find('td').eq(nidx_td).find('input[type=text]').eq(0).attr('id');
						jQuery('#' + el_id).focus();
				}
				break;
			default:
				return; // exit this handler for other keys 
		}
	  //e.preventDefault(); // prevent the default action (scroll / move caret)
	});

      
	<?php
	if (empty($disable_ifapproved)): 
	?>
	jQuery("#mbtn_mn_Save").click(function(e){ 
		try { 
			//__mysys_apps.mepreloader('mepreloaderme',true);
			var mtkn_mntr = jQuery('#txt_promotrxno').attr('data-mtknattr');
			var txt_promotrxno = jQuery('#txt_promotrxno').val();
			var txt_promodesc = jQuery('#txt_promodesc').val();
			var ndiscvalue = jQuery('#txt_promodiscval').val();
			var branch_code = jQuery('#branch_code').val();
			var start_date = jQuery('#start_date').val();
			var start_time = jQuery('#start_time').val();
			var end_date = jQuery('#end_date').val();
			var end_time = jQuery('#end_time').val();
			var is_discount_percent = jQuery('#is_discount_percent').prop('checked');;
			var cb_fix_discount_percent_value = (is_discount_percent) ? (1) : (0);
			var is_fixed_price = jQuery('#is_fixed_price').prop('checked');
			var cb_fix_value = (is_fixed_price) ? (1) : (0);

			var is_fixed_price_checked = jQuery('#is_fixed_price').val();
			var branch_code = jQuery('#branch_code').val();
			var mtkn_branch = jQuery('#branch_name').attr('data-mtknid');
			var branch_name = jQuery('#branch_name').val();
			var rowCount1 = jQuery('#__tbl_<?=$memodule;?> tr').length - 1;
			var adata1 = [];
			var adata2 = [];

			var mdata = '';
			var ninc = 0;

			for(aa = 1; aa < rowCount1; aa++) { 
				var clonedRow = jQuery('#__tbl_<?=$memodule;?> tr:eq(' + aa + ')').clone(); 
				var mitemc = jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
				var dtrecid_tkn = jQuery(clonedRow).find('button[type=button]').eq(0).attr('data-mtnkattr'); 
				var mitemc_tkn = jQuery(clonedRow).find('input[type=text]').eq(0).attr('data-mtnkattr'); 
				var mdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
				var mbcode = jQuery(clonedRow).find('input[type=text]').eq(2).val();
				var morigsrp = jQuery(clonedRow).find('input[type=text]').eq(3).val(); 
				var mdiscsrp = jQuery(clonedRow).find('input[type=text]').eq(4).val(); 
				if(jQuery.trim(mitemc) == '' || parseFloat(ndiscvalue) == 0) { 
				} else {
					mdata = mitemc + 'x|x' + mdesc + 'x|x' + mbcode + 'x|x' + morigsrp + 'x|x' + mdiscsrp + 'x|x' + dtrecid_tkn;
					adata1.push(mdata);
					adata2.push(mitemc_tkn);
					ninc = 1;
					}
			}  //end for
				
			if(ninc == 0) { 
				jQuery('#memsgtestent_bod').html('No Valid DATA!!!');
				jQuery('#memsgtestent').modal('show');
				return;
			}
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mparam = {
				mtkn_mntr:mtkn_mntr,
				txt_promotrxno:txt_promotrxno,
				txt_promodesc: txt_promodesc,
				ndiscvalue: ndiscvalue,
				start_date: start_date,
				start_time: start_time,
				end_date: end_date,
				end_time: end_time,
				cb_fix_discount_percent_value:cb_fix_discount_percent_value,
				cb_fix_value:cb_fix_value,
				branch_code:branch_code,
				branch_name:branch_name,
				mtkn_branch:mtkn_branch,
				adata1: adata1,
				adata2: adata2
			};  
			
			//console.log(mparam);
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url();?>me-promo-save',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery(this).prop('disabled', false);
					jQuery('#memsgtestent_bod').html(data);
					jQuery('#memsgtestent').modal('show');
					return false;
				},
				error: function() {
					jQuery('#memsgtestent_bod').html('<span class="fw-bolder text-danger">Error loading...</span>');
					jQuery('#memsgtestent').modal('show');
					__mysys_apps.mepreloader('mepreloaderme',false);
					return false;
				} 
			});  //end jQuery POST
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
		}  //end try
		return false; 
	});
	<?php
	endif;
	?>
	__mysys_apps.mepreloader('mepreloaderme',false);
	jQuery('#anchor-list').on('click',function(){
		jQuery('#anchor-list').addClass('active');
		jQuery('#anchor-items').removeClass('active');
		var mtkn_whse = '';
		wg_trx_promo_view_recs(mtkn_whse);
	});

	function wg_trx_promo_view_recs(mtkn_whse){ 
		var ajaxRequest;
		ajaxRequest = jQuery.ajax({
			url: "<?=site_url();?>me-promo-view",
			type: "post",
			data: {
				mtkn_whse: mtkn_whse
			}
		});
		// Deal with the results of the above ajax call
		__mysys_apps.mepreloader('mepreloaderme',true);
		ajaxRequest.done(function(response, textStatus, jqXHR) {
			jQuery('#wg_trx_promo_content').html(response);
			__mysys_apps.mepreloader('mepreloaderme',false);
		});
	};  //end wg_trx_promo_view_recs

	jQuery('#anchor-items').on('click',function() {
		jQuery('#anchor-items').addClass('active');
		jQuery('#anchor-list').removeClass('active');
		var mtkn_whse = '';
		wg_trx_promo_view_appr(mtkn_whse);
	});

	function wg_trx_promo_view_appr(mtkn_whse){ 
		var ajaxRequest;
		ajaxRequest = jQuery.ajax({
		  url: "<?=site_url();?>me-promo-view-appr",
		  type: "post",
		  data: {
			mtkn_whse: mtkn_whse
		  }
		});

		// Deal with the results of the above ajax call
		__mysys_apps.mepreloader('mepreloaderme',true);
		ajaxRequest.done(function(response, textStatus, jqXHR) {
		  jQuery('#wg_trx_promo_content').html(response);
		  __mysys_apps.mepreloader('mepreloaderme',false);
		});
	};

  let selectedtxt = 'sampletext';
  const txt = document.getElementById('output')
  const selectCb = (cbElement) => {
    const checkboxes = document.getElementsByName('cb')
    var lblfix = document.getElementsByName('fixedlbl');
    var is_fixed_price = jQuery('#is_fixed_price').prop('checked');
    var cb_value_fix = (is_fixed_price) ? (1) : (0);
    var is_discount_percent = jQuery('#is_discount_percent').prop('checked');;
    var cb_value_percent = (is_discount_percent) ? (1) : (0);
    checkboxes.forEach(cb =>  {
      
    })
    

  }
  
  my_add_line_item_promo();

  
  
</script>













