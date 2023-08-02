<?php

$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');

$mylibzdb = model('App\Models\MyLibzDBModel');
$mydatum = model('App\Models\MyDatumModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myusermod = model('App\Models\MyUserModel');

$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$cuserrema = $myusermod->mysys_userrema();
$mtkn_trxno = $request->getVar('mtkn_trxno');
$mmnhd_rid   ='';
$txtpout_typ = '';
//$str_style   ='';
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
$sw_transac_no = $request->getVar('sw_transac_no');
$branch_name = '';
$branch_code = '';
if(!empty($sw_transac_no)) {
	$str = "
	select aa.`id`,
	aa.`branch_code`,
	aa.`branch_name`,
	aa.`spqd_trx_no`
	from `trx_pos_promo_spqd_dt` aa 
	where aa.`spqd_trx_no` = '$sw_transac_no' 
	";
	$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	$rw = $q->getRowArray();
	$txt_sw_transac_no = $rw['spqd_trx_no'];
	$branch_code = $rw['branch_code'];
	$branch_name = $rw['branch_name'];
	
  }
  

echo view('templates/meheader01');
?>

        
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
						<span>S-promo Discount No.:</span>
					</div>
					<div class="col-sm-9">
                        <input type="hidden" id="__hmpromotrxnoid" name="__hmpromotrxnoid" class="form-control form-control-sm" value="<?=$sw_transac_no;?>"/>
						<input class="form-control form-control-sm" name="sw_transac_no" id="sw_transac_no" data-mtkn="<?=$mtkn_trxno?>" value = "<?=$trx_no?>" type="text" placeholder="Special Discount Transaction Number" readonly>
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
						<input class="form-control form-control-sm" name="reason_txt" id="reason_txt" type="text" placeholder="" value="<?=$branch_name;?>"  required>
					</div>
				</div> <!-- end Branch -->
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
							<th class="column-itemcode">Itemcode</th>
							<th>Description</th>
							<th>Qty</th>
							<th>UOM</th>
							<th>SRP</th>
							<th>Amount</th>
							<th>Switch to</th>
							<th>Qty</th>
							<th>UOM</th>
							<th>SRP</th>
							<th>Amount</th>
							<th style ="display:none;" class="frmmitemcode">From Itemcode</th>
						</thead>
						<tbody id="contentDetlArea">
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
								<td><input type="text" size="20" value="" class="orig_qty"/></td> <!--QTY-->
								<!-- <td><input type="text" size="15" value=""  onkeypress="return __mysys_apps.numbersOnly(event)" onmouseover="javascript:__tamt_compute_totals();" onmouseout="javascript:__tamt_compute_totals();" onclick="javascript:__tamt_compute_totals();" onblur="javascript:__tamt_compute_totals();"/></td> ucost -->
								<td><input type="text" size="15" value="" id="" readonly /></td> <!--UOM-->
								<td><input type="text" size="15" value="" id="" readonly /></td> <!--SRP-->
								<td><input type="text" size="15" value="" id="" readonly /></td> <!--Amount-->
								<td><input type="text" size="15" value="" id="" class="mitemcode2" readonly /></td> <!--Switch to-->
								<td><input type="text" size="15" value="" id="" /></td> <!--QTY-->
								<td><input type="text" size="15" value="" id="" readonly /></td> <!--UOM-->
								<td><input type="text" size="15" value="" id="" readonly /></td> <!--SRP-->
								<td><input type="text" size="15" value="" id="" readonly /></td> <!--Amount-->
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
		
	</section> <!-- end section -->
</main>  <!-- end main -->
<?php
echo view('templates/mefooter01');
?>

<script type="text/javascript"> 

	function getValue() {
			var rowCount = jQuery('#tbl_PayData tr').length;
			for(aa = 1; aa < rowCount; aa++) { 
			var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone();
			var _qty = jQuery(clonedRow).find('input[type=text]').eq(2).val();
			var _srp = jQuery(clonedRow).find('input[type=text]').eq(4).val();
			var _amount = jQuery(clonedRow).find('input[type=text]').eq(5).attr('id');
			var _total = (_qty * _srp)

			var _qty2 = jQuery(clonedRow).find('input[type=text]').eq(7).val();
			var _srp2 = jQuery(clonedRow).find('input[type=text]').eq(9).val();
			var _amount2 = jQuery(clonedRow).find('input[type=text]').eq(10).attr('id');
			var _total2 = (_qty2 * _srp2)


			jQuery('#' + _amount).val(_total);
			jQuery('#' + _amount2).val(_total2);
			}
	}

            // var input = document.getElementsByClassName("orig_qty")[0];
            // var value = input.value;
            // alert("The input value is: " + value);}
			$(function() {
			// Event handler for keypress event
			$("table").change(function(event) {

				getValue();
				// Call your function or perform any desired actions

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
		   jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','fld_uom' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','fld_srp' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','fld_amount' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(6).attr('id','fld_switchto' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(7).attr('id','fld_qty2' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(8).attr('id','fld_uom2' + mid);
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
		 var transac_name = jQuery('#sw_transac_no').val();
		 var mtkn_mntr = jQuery('#__hmpromotrxnoid').val();
		 var branch_name = jQuery('#branch_name').val();
		 var data_mtknid = jQuery('#branch_name').attr('data-mtknid');
		 var rowCount = jQuery('#tbl_PayData tr').length;
		 var adata1 = [];
		 var adata2 = [];

		 var mdata = '';
		 var ninc = 0;

			
		   for(aa = 1; aa < rowCount; aa++) { 
		   var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone();
		   var _qty = jQuery(clonedRow).find('input[type=text]').eq(2).val();
		   var _srp = jQuery(clonedRow).find('input[type=text]').eq(4).val();
		   var _amount = jQuery(clonedRow).find('input[type=text]').eq(5).val();
		   var _qty2 = jQuery(clonedRow).find('input[type=text]').eq(7).val();
		   var _srp2 = jQuery(clonedRow).find('input[type=text]').eq(9).val();
		   var _amount2 = jQuery(clonedRow).find('input[type=text]').eq(10).val();
		   var mitemc_tkn = jQuery(clonedRow).find('input[type=text]').eq(0).attr('data-mtnkattr');  
		
	
		   
		   mdata = _qty + 'x|x' + _srp + 'x|x' + _amount + 'x|x' + _qty2 + 'x|x' + _srp2 + 'x|x' + _amount2 +  'x|x' + mitemc_tkn;
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
			transac_name:mtkn_mntr,
			 mtkn_mntr: mtkn_mntr,
			 branch_name: branch_name,
			 mtkn_branch:data_mtknid,
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
                    var xobjitemuom = jQuery(clonedRow).find('input[type=text]').eq(3).attr('id'); 
					var xobjitemprice = jQuery(clonedRow).find('input[type=text]').eq(4).attr('id');  /*PRICE*/
					jQuery(this).attr('data-mtnkattr',ui.item.mtkn_rid);
					jQuery('#' + xobjitemdesc).val(ui.item._DESC);
					jQuery('#' + xobjitemuom).val(ui.item._UOM);
                    
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
					
                    var xobjitemuom = jQuery(clonedRow).find('input[type=text]').eq(8).attr('id'); 
					var xobjitemprice = jQuery(clonedRow).find('input[type=text]').eq(9).attr('id');  /*PRICE*/
					jQuery(this).attr('data-mtnkattr',ui.item.mtkn_rid);
					
					jQuery('#' + xobjitemuom).val(ui.item._UOM);
                    
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

    
</script>