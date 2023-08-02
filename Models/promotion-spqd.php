<?php

//VARIABLE DECLARATIONS
$myusermod = model('App\Models\MyUserModel');
$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mytrxfgpack = model('App\Models\MyPromoBuy1take1Model');
$mydataz = model('App\Models\MyDatumModel');
$this->dbx = $mylibzdb->dbx;
$this->db_erp = $mydbname->medb(0);
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$cuserrema = $myusermod->mysys_userrema();
$mtkn_trxno = $request->getVar('mtkn_trxno');
$mmnhd_rid   ='';
$txtpout_typ = '';
$str_dis     = "";
$txt_branch  = '';
$trx_no      = '';
$startDate   = '';
$endDate     = '';
$percentDisc = '';
$pesoDisc    ='checked';
$txt_branchID = '';
$str_style = " style=\"display:none;\"";
$btn_save = 'Save';
$post_tag = '';
$pd_stats = '';
$intText  = ''; //to disabled text
$endTime = date('23:59');
$startTime = date('08:00');
$total_promo = "";
$orig_srp = $request->getVar('orig_srp');
$spqd_trxno = $request->getVar('spqd_trxno');
$spqd_trx_no = $request->getVar('spqd_trx_no');
$branch_name = '';
$branch_code = '';
$spqd_reason = '';
$new_total_srp = '';
$last_total_srp = '';
$total_qty = '';
$txt_spqd_trx_no = '';
$nporecs = 0;


	
if(!empty($spqd_trxno)) {

	$str = "
	select aa.`id`,
	aa.`branch_code`,
	aa.`spqd_reason`,
	aa.`new_total_srp`,
	aa.`last_total_srp`,
	cc.`branch_name`,
	aa.`total_qty`,
	aa.`spqd_trx_no`
	from `trx_pos_promo_spqd_hd` aa 
	join `mst_companyBranch` bb
	on aa.`branch_code` = bb.`BRNCH_OCODE2`
	join `trx_pos_promo_spqd_dt` cc
	on aa.`spqd_trx_no` = cc.`spqd_trx_no`
	where aa.`spqd_trx_no` = '$spqd_trxno' 
	";

	$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	$rw = $q->getRowArray();
	$txt_spqd_trx_no = $rw['spqd_trx_no'];
	$branch_code = $rw['branch_code'];
	$spqd_reason = $rw['spqd_reason'];
	$new_total_srp = $rw['new_total_srp'];
	$last_total_srp = $rw['last_total_srp'];
	$total_qty = $rw['total_qty'];
	$branch_name = $rw['branch_name'];
	

	
  }
  

echo view('templates/meheader01');
?>

<style>
	 .custom-row {
    margin-left: 100px; /* Adjust the margin value as needed */
  }
</style>
        
<main id="main" class="main">
	<div class="pagetitle">
		<h1>Promotion</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Home</a></li>
				<li class="breadcrumb-item">Sales</li>
				<li class="breadcrumb-item active">Promotion - SPROMO/QDAMAGE</li>
			</ol>
		</nav>
	</div> <!-- End Page Title -->
	<section class="section">
		<div class="row metblentry-font">
			<div class="col-md-8">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span for="txt_spqd_trx_no">S-promo Discount No.:</span>
					</div>
					<div class="col-sm-9">
						<input class="form-control form-control-sm" name="txt_spqd_trx_no" id="txt_spqd_trx_no"  value = "<?=$txt_spqd_trx_no;?>" type="text" placeholder="Special Discount Transaction Number" readonly>
                        <input type="hidden" id="__hmpromotrxnoid" name="__hmpromotrxnoid" class="form-control form-control-sm" value="<?=$spqd_trxno;?>"/>
					</div>
				</div> <!-- end Promo Trx. No -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Branch:</span>
					</div>
					<div class="col-sm-9">
						<input class="form-control form-control-sm"data-id-brnch-name="<?=$branch_name;?>" name="branch_name" id="branch_name" type="text" placeholder="Branch Name" value="<?=$branch_name;?>"  required>
						<input type="hidden" data-id-brnch="<?=$branch_code;?>" placeholder="Branch Name" id="branch_code" name="branch_code" class="branch_code form-control form-control-sm " value="<?=$branch_code;?>" required/>     
					</div>
				</div> <!-- end Branch -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Reason:</span>
					</div>
					<div class="col-sm-9">
						<input class="form-control form-control-sm" name="reason_txt" id="reason_txt" type="text" placeholder="" value="<?=$spqd_reason;?>"  required>
					</div>
				</div> <!-- Total Qty-->
				<div class="row mb-3 custom-row justify-content-center">
					<div class="col-sm-1 ml-auto">
						<span>Total Qty:</span>
					</div>
					<div class="col-sm-1 ml-auto">
						<input class="form-control form-control-sm" name="total_qty" id="total_qty" type="text" placeholder="" value="<?=$total_qty;?>"  readonly>
					</div>
					<div class="col-sm-2 ml-auto">
						<span>Last SRP Total Amount:</span>
					</div>
					<div class="col-sm-1 ml-auto">
						<input class="form-control form-control-sm" name="total_last_srp" id="total_last_srp" type="text" placeholder="" value="<?=$last_total_srp;?>"  readonly>
					</div>
					<div class="col-sm-2 ml-auto">
						<span>New SRP Total Amount:</span>
					</div>
					<div class="col-sm-1 ml-auto">
						<input class="form-control form-control-sm" name="total_new_srp" id="total_new_srp" type="text" placeholder="" value="<?=$new_total_srp;?>"  readonly>
					</div>
				</div> <!-- end qty -->
			</div> <!-- end col-md-6 -->
			</div> <!-- end col-md-6 -->
		</div> <!-- end row metblentry-font -->
		
		
		<!-- table data entry -->
		
		<div class="row ">
			<div  id="tbl_items_ent">
				<div class="table-responsive">
					<table id="tbl_PayData" class="metblentry-font table-bordered" >  
						<thead class="text-center">
						<th class="text-center"><i id="item_sync" class="text-white fas fa-sync"> </i></th>
						<th  class="text-center">
							 <button type="button" class="btn btn-sm btn-success p-1 pb-0" onclick="javascript:my_add_line_item();" >
								<i class="bi bi-plus-lg"></i>
							</button>
						</th>
							<th>Itemcode</th>
							<th>Description</th>
							<th>Qty</th>
							<th>Last SRP</th>
							<th>Amount</th>
							<th>Promo Code</th>
							<th>New SRP</th>
							<th>Amount</th>
							<th>Profit Loss Amount</th>
							
							<th style ="display:none;" class="frmmitemcode">From Itemcode</th>
						</thead>
						<tbody id="contentDetlArea">
						<?php if(!empty($spqd_trxno)): //data retrieval if existing
                          
                          $str = "
                          select
                          aa.`spqd_trx_no`,
						  aa.`spqd_reason`,
						  aa.`new_total_srp`,
						  aa.`last_total_srp`,
						  aa.`total_qty`,
						  cc.`qty_spqd`,
                          cc.`item_desc_spqd`,
                          cc.`last_srp_amount`,
						  cc.`promo_code_spqd`,
						  cc.`new_srp`,
						  cc.`new_srp_amount`,
                          cc.`profit_loss`,
						  cc.`last_srp`,
                          dd.`ART_CODE`,
                          dd.`ART_DESC`,
                          dd.`ART_UPRICE`


                          from `trx_pos_promo_spqd_hd` aa 
                          join `mst_companyBranch` bb
                          on aa.`branch_code` = bb.`BRNCH_OCODE2`
                          join `trx_pos_promo_spqd_dt` cc
                          on aa.`spqd_trx_no` = cc.`spqd_trx_no`
                          join `mst_article` dd
						  ON cc.`item_code` = dd.`ART_CODE`
                          where aa.`spqd_trx_no` = '$spqd_trxno' 
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                            $nporecs++;


							$qty_spqd = $data['qty_spqd'];
                            $last_srp_amount = $data['last_srp_amount'];
                            $promo_code_spqd = $data['promo_code_spqd'];
                            $new_srp = $data['new_srp'];
                            $new_srp_amount = $data['new_srp_amount'];
							$profit_loss = $data['profit_loss'];
                            $ART_CODE=$data['ART_CODE'];
                            $ART_DESC = $data['ART_DESC'];
							$ART_UPRICE = $data['ART_UPRICE'];
							$last_srp = $data['last_srp']
                           
                            ?>
                            
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->

                            <tr >
                              <td><?=$nporecs;?></td>
                              <td nowrap="nowrap">
                                <button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1"  onclick="$(this).closest('tr').remove();"><i class="bi bi-x"></i></button>
                                <input class="mitemrid" type="hidden" value=""/>
                                <input type="hidden" value=""/>
                              </td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> mitemcode" value="<?=$ART_CODE;?>"></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$ART_DESC;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$qty_spqd;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> " value="<?=$ART_UPRICE;?>"style="background-color: #EAEAEA;"readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$last_srp_amount;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>mitemcode2" value="<?=$promo_code_spqd;?>"  readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$new_srp;?>" style="background-color: #EAEAEA;" autocomplete="off" readonly></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$new_srp_amount;?>" style="background-color: #EAEAEA;" autocomplete="off" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> text-end" value="<?=$profit_loss;?>" style="background-color: #EAEAEA;"autocomplete="off" readonly></td> 
                            </tr>
                            <?php 
                          
                          }
                          
                          ?>
                        <?php endif;?>  
							<tr style="display:none;">
				
								<td></td>
								<td>
								<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" onclick="jQuery(this).closest('tr').remove();" ><i class="bi bi-trash"></i></button>
									</button>
									<input type="hidden" value=""/>
									<input type="hidden" value=""/>
									<input type="hidden" value="Y"/>
									<input type="hidden" value=""/>
								</td>
								<td><input type="text" size="20" class="mitemcode" value="" /></td> <!--itemcode-->
								<td><input type="text" size="40" value="" readonly /></td> <!--item desc-->
								<td><input type="text" size="20" value="" /></td> <!--QTY-->
								<!-- <td><input type="text" size="15" value=""  onkeypress="return __mysys_apps.numbersOnly(event)" onmouseover="javascript:__tamt_compute_totals();" onmouseout="javascript:__tamt_compute_totals();" onclick="javascript:__tamt_compute_totals();" onblur="javascript:__tamt_compute_totals();"/></td> ucost -->
								<td><input type="text" size="15" value="" id="" readonly /></td> <!--SRP-->
								<td><input type="text" size="15" value="" id="" readonly /></td> <!--Amount-->
								<td><input type="text" size="15" value="" id="" class="mitemcode2"  /></td> <!--Promo Code-->
								<td><input type="text" size="15" value="" id="" readonly></td> <!--New Srp-->
								<td><input type="text" size="15" value="" id="" readonly /></td> <!--Amount-->
								<td><input type="text" class="text-end" size="15" value="" id="" readonly /></td> <!--Profit loss-->
							</tr>      
			         
						</tbody>
					</table>
						<div class="row mt-2 mb-3"><!--  Save Records -->
								<div class="col-sm-4">
									<button id="mbtn_mn_Save" type="button" style="background-color: #167F92; color: #FFF;" class="btn btn-dgreen btn-sm">Save</button>   
									<button id="mbtn_mn_NTRX" type="button" class="btn btn-primary btn-sm">Download</button>
								</div>
						</div> <!-- end Save Records -->
				</div> <!-- end table-responsive -->
			</div> <!-- end tbl_items_ent -->
		</div>
		<div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title">Records</h6>
              <div class="pt-2 bg-dgreen mt-2" style="background-color: #167F92;"> 
               <nav class="nav nav-pills flex-column flex-sm-row  gap-1 px-2 fw-bold">
                <a id="anchor-list" class="flex-sm-fill text-sm-center mytab-item active p-2  rounded-top"  aria-current="page" href="#"> <i class="bi bi-ui-checks"> </i> Records</a>
                <a id="anchor-items" class=" flex-sm-fill text-sm-center mytab-item  p-2 rounded-top " href="#"><i class="bi bi-ui-radios"></i> For Approval</a>
              </nav>
            </div>
            <!-- DISPLAY OF RECORDS AND APPROVAL -->
			<div id="packlist" class="text-center p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">

	</section> <!-- end section -->

	<?php
	  echo view('templates/mefooter01');
    ?>
</main>  <!-- end main -->

<script type="text/javascript"> 


	function getValue() {
			var totalQty = 0;
			var total_last_srp =0;
			var total_new_srp =0;
			var rowCount = jQuery('#tbl_PayData tr').length;
			for(aa = 1; aa < rowCount; aa++) { 
			var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone();
			var prof_loss = jQuery(clonedRow).find('input[type=text]').eq(8).attr('id');
			var _qty = parseFloat(jQuery(clonedRow).find('input[type=text]').eq(2).val());
			var _srp = parseFloat(jQuery(clonedRow).find('input[type=text]').eq(3).val());
			var _amount = jQuery(clonedRow).find('input[type=text]').eq(4).attr('id');
			var _total = (_qty * _srp)
			var _srp2 = parseFloat(jQuery(clonedRow).find('input[type=text]').eq(6).val());
			var _amount2 = jQuery(clonedRow).find('input[type=text]').eq(7).attr('id');
			var _total2 = (_qty * _srp2)
			var _loss = (_total - _total2);

			
			if (!isNaN(_qty)) {
				totalQty += _qty;
			}
			if (!isNaN(_srp)) {
				total_last_srp += _total;
			}if (!isNaN(_srp2)) {
				total_new_srp += _total2;
			}
			
			console.log(totalQty);
			
			if (!isNaN(_qty)) {
				jQuery('#' + _amount).val(_total);
				
				if (_srp2 !=0) {
					jQuery('#' + _amount2).val(_total2);
					}
				
				jQuery('#' + prof_loss).val(_loss);
				$('#total_qty').val(totalQty);
				$('#total_last_srp').val(total_last_srp);
				$('#total_new_srp').val(total_new_srp);
			}
			
		
				} //end for
			} //end getvalue()

           
			$(function() {
				$("table").change(function(event) {
					getValue();
					});
				$("table").click(function(event) {
					getValue();	
					});
					$("table").mousedown(function(event) {
					getValue();	
					});
				$("table").hover(function() {
					getValue();	
					},
					function(){
					getValue();	
					});
					
			})

			__my_item_lookup();
			__my_item_lookup2();
		
	
	function my_add_line_item(){ 
		  try {
			var fld_area_id = jQuery('#branchName').data('mtknid');
			if(fld_area_id == ''){
			   alert('Please input Area Code/Branch first!!!');
			   $('#branchName').focus();
				return false;
			}
			
		   var rowCount = jQuery('#tbl_PayData tr').length;
		   var mid = __mysys_apps.__do_makeid(7) + (rowCount + 1);
		   var clonedRow = jQuery('#tbl_PayData tr:eq(' + (rowCount - 1) + ')').clone(); 
		   
		   jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id','mitemrid_' + mid);
		   jQuery(clonedRow).find('input[type=hidden]').eq(1).attr('id','mid_' + mid);
		   jQuery(clonedRow).find('input[type=hidden]').eq(2).attr('id','__me_tag' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','fld_mitemcode' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','fld_mitemdesc' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','fld_qty' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','fld_srp' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','fld_amount' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','fld_promocode' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(6).attr('id','fld_newsrp' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(7).attr('id','fld_newamount' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(8).attr('id','fld_profitloss' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(9).attr('id','fld_srp2' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(10).attr('id','fld_amount2' + mid);

		   jQuery('#tbl_PayData tr').eq(rowCount - 1).before(clonedRow);
		   jQuery(clonedRow).css({'display':''});
		   //__my_item_lookup();
		   //__my_promotim_lookup();
		   //__tamt_compute_totals();
		   var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
		   jQuery('#' + xobjArtItem).focus();
		   jQuery( '#tbl_PayData tr').each(function(i) { 
				   jQuery(this).find('td').eq(0).html(i);
		   });
           __my_item_lookup();
           __my_item_lookup2();
		   
	   } catch(err) { 
		   var mtxt = 'There was an error on this page.\\n';
		   mtxt += 'Error description: ' + err.message;
		   mtxt += '\\nClick OK to continue.';
		   alert(mtxt);
		   return false;
		   }  //end try 
	} //end my_add_line_item


    	
	jQuery('#branch_name')
		// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) {
		  if ( event.keyCode === jQuery.ui.keyCode.TAB &&
			jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
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
	}); //end branch_name 

	$("#mbtn_mn_Save").click(function(e){
       
	   try { 
		 //__mysys_apps.mepreloader('mepreloaderme',true);
		 var txt_spqd_trx_no = jQuery('#txt_spqd_trx_no').val();
		 var mtkn_mntr = jQuery('#__hmpromotrxnoid').val();
		 var branch_name = jQuery('#branch_name').val();
		 var data_mtknid = jQuery('#branch_name').attr('data-mtknid');
		 var rowCount = jQuery('#tbl_PayData tr').length -1;
		 var total_qty	 =	$('#total_qty').val();
		 var total_last_srp =$('#total_last_srp').val();
		 var total_new_srp	=$('#total_new_srp').val();
		 var reason = $('#reason_txt').val();
		 var adata1 = [];
		 var adata2 = [];

		 var mdata = '';
		 var ninc = 0;

			
		   for(aa = 1; aa < rowCount; aa++) { 
		   var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone();
		   var mitemc = jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
		   var mitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
		   var _qty = jQuery(clonedRow).find('input[type=text]').eq(2).val();
		   var _srp = jQuery(clonedRow).find('input[type=text]').eq(3).val();
		   var _amount = jQuery(clonedRow).find('input[type=text]').eq(4).val();
		   var _promocode = jQuery(clonedRow).find('input[type=text]').eq(5).val();
		   var _srp2 = jQuery(clonedRow).find('input[type=text]').eq(6).val();
		   var _amount2 = jQuery(clonedRow).find('input[type=text]').eq(7).val();
		   var _profitloss = jQuery(clonedRow).find('input[type=text]').eq(8).val();
		   var mitemc_tkn = jQuery(clonedRow).find('input[type=text]').eq(0).attr('data-mtnkattr');  
		
	
		   
		   mdata = mitemc +'x|x'+ mitemdesc +'x|x' + _qty + 'x|x' + _srp + 'x|x' + _amount + 'x|x' + _promocode + 'x|x' + _srp2 + 'x|x' + _amount2+  'x|x' + _profitloss +  'x|x' + mitemc_tkn;
		   adata1.push(mdata);
		   var mdata = jQuery(clonedRow).find('input[type=hidden]').eq(0).val();
		   adata2.push(mitemc_tkn);
		   ninc = 1;

		   }  //end for
		   if(ninc == 0) { 
			 jQuery('#memsgtestent_bod').html('No Valid DATA!!!');
			 jQuery('#memsgtestent').modal('show');
			 return;
		   }
		   __mysys_apps.mepreloader('mepreloaderme',true);
		   var mparam = {
			 txt_spqd_trx_no:txt_spqd_trx_no,
			 total_qty:total_qty,
			 total_last_srp:total_last_srp,
			 total_new_srp:total_new_srp,
			 mtkn_mntr: mtkn_mntr,
			 branch_name: branch_name,
			 mtkn_branch:data_mtknid,
			 reason: reason,
			 adata1: adata1,
			 adata2: adata2
			 
		   };  

		   console.log(mparam);
		   
		   $.ajax({ 
			 type: "POST",
			 url: '<?=site_url();?>me-spqd-save',
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
		   alert(mtxt);
		 }  //end try
		 return false; 
	 });
	 

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
			url: "<?=site_url();?>me-spqd-view",
			type: "post",
			data: {
				mtkn_whse: mtkn_whse
			}
		});
		// Deal with the results of the above ajax call
		__mysys_apps.mepreloader('mepreloaderme',true);
		ajaxRequest.done(function(response, textStatus, jqXHR) {
			jQuery('#packlist').html(response);
			__mysys_apps.mepreloader('mepreloaderme',false);
		});
	};  //end wg_trx_promo_view_recs

    function __my_item_lookup() { 
		jQuery('.mitemcode' ) 
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
				select: function( event, ui ) {
					var terms = ui.item.value;
					jQuery(this).attr('alt', jQuery.trim(ui.item.value));
					jQuery(this).attr('title', jQuery.trim(ui.item.value));
					var ndisc = jQuery('#txt_promodiscval').val();
					this.value = ui.item.value;
					var clonedRow = jQuery(this).parent().parent().clone();
					var indexRow = jQuery(this).parent().parent().index();
					var xobjitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).attr('id');  /*DESC*/
					var xobjitemprice = jQuery(clonedRow).find('input[type=text]').eq(3).attr('id');  /*PRICE*/
					jQuery(this).attr('data-mtnkattr',ui.item.mtkn_rid);
					jQuery('#' + xobjitemdesc).val(ui.item._DESC);
                    
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

    function __my_item_lookup2() {
		jQuery('.mitemcode2' ) 
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
			source: '<?= site_url(); ?>promo_search',
			focus: function() {
				  // prevent value inserted on focus
				  return false;
				},
				select: function( event, ui ) {
					var terms = ui.item.value;
					jQuery(this).attr('alt', jQuery.trim(ui.item.value));
					jQuery(this).attr('title', jQuery.trim(ui.item.value));
					var ndisc = jQuery('#txt_promodiscval').val();
					this.value = ui.item.value;
					var clonedRow = jQuery(this).parent().parent().clone();
					var indexRow = jQuery(this).parent().parent().index();
					
                    
					var xobjitemprice = jQuery(clonedRow).find('input[type=text]').eq(6).attr('id');  /*PRICE*/
					jQuery(this).attr('data-mtnkattr',ui.item.mtkn_rid);
					
					
                    
					jQuery('#' + xobjitemprice).val(ui.item.pro_code_disc);
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

    
</script>