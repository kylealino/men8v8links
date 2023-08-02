<?php
/* =================================================
 * Author      : Oliver Sta Maria
 * Date Created: November 10, 2022
 * Module Desc : JO Delivery In
 * File Name   : transactions/jo-dr-in.php
 * Revision    : 
*/
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');

$mylibzdb = model('App\Models\MyLibzDBModel');

$mtkn_trxno = ''; 
$adftag = '';
$txtdf_tag= '';
$mtrx_no = '';
$txtcomp = '';
$txtarea_code = '';
$txtsupplier = '';
$txtpono = '';
$txtgldate = '';
$txtrems = '';
$mmnhd_rid ='';
$nmnrecs = 0;
$txtsubtdeb='';
$txtsubtcre='';
$rr_file_upld = '';
$COMP_NAME = '';
$BRNCH_NAME = '';
$VEND_NAME = '';
$CUST_NAME = '';
$entTyp = '';
$entTyprid = '';

$txtpotobrnc='';
$txtimsno ='';

$fld_jodrdate = '';
$fld_trxdate = $mylibzdb->getdate();


?>
<main id="main" class="main">
	<div class="pagetitle">
		<h1>JO Receiving In</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Home</a></li>
				<li class="breadcrumb-item">Transaction</li>
				<li class="breadcrumb-item active">JO DR-In</li>
			</ol>
		</nav>
	</div> <!-- End Page Title -->
	<section class="section">
		<div class="row metblentry-font">
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Trx. Date:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm meinput-sm-pad form_datetime"  id="fld_trxdate" name="fld_trxdate" value="<?=$fld_trxdate;?>" disabled/>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">JO Trx. No.</span>
					</div>
					<div class="col-sm-9">
						<input type="text" id="mtrx_no" name="mtrx_no" class="form-control form-control-sm meinput-sm-pad" value="<?=$mtrx_no;?>" readonly />
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Company</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm meinput-sm-pad" id="fld_Company" name="fld_Company" required>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Upload Attachment</span>
					</div>
					<div class="col-sm-9">
						<input data-id="__fl_upld" accept="image/gif,image/jpeg,image/png,application/pdf" class="form-control form-control-sm metblentry-font" size="5" id="__fl_upld" type="file" multiple name="__fl_upld[]">
					</div>
				</div>
			</div> <!-- end col-6 -->
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>JO DR Date:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm medatepicker" placeholder="mm/dd/yyyy" id="fld_jodrdate" name="fld_jodrdate" value="<?=$fld_jodrdate;?>" />
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Customer:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm meinput-sm-pad" data-custtknid="" id="fld_mcustno" value="<?=$CUST_NAME;?>"/>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Remarks:</span>
					</div>
					<div class="col-sm-9">
						<textarea type="text" class="form-control form-control-sm meinput-sm-pad" name="fld_rems" id="fld_rems"><?=$txtrems;?></textarea>
					</div>
				</div>
			</div> <!-- end col-6 2nd screen --> 
		</div> <!-- end row -->
		<!-- table data entry -->
		<div class="row mb-3">
			<div class="col-sm-12">
				<div class="table-responsive">
					<table id="tbl_JODRInData" class="metblentry-font">
						<thead>
							<th></th>
							<th class="text-center" nowrap>
								<button type="button" class="btn btn-primary btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item();" >
									<i class="bi bi-plus-lg"></i>
								</button>
							</th>
							<th>Cut No.</th>
							<th>Material Code</th>
							<th>Material Description</th>
							<th>Price</th>
							<th>DR Qty</th>
							<th>Actual Qty</th>
							<th>Remarks</th>
						</thead>
						<tbody>
							<tr style="display:none;">
								<td></td>
								<td>
									<button type="button" class="btn btn-danger btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:confirmalert(this);">
										<i class="bi bi-x-circle-fill"></i>
									</button>
                                </td>
                                <td><input type="text" size="15" /></td>
								<td><input type="text" class="meproditems" size="15" /></td>
								<td><input type="text" size="25" readonly /></td>
								<td><input type="text" size="10" class="text-end" /></td>
								<td><input type="text" size="10" class="text-end" /></td>
								<td><input type="text" size="10" class="text-end" /></td>
								<td><input type="text" size="30" /></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="6"></th>
								<th>
									<input type="text" size="10"  id="fld_subtdeb" value="<?=$txtsubtdeb;?>" required readonly/>
								</th>
								<th>
									<input type="text" size="10" id="fld_subtcre" value="<?=$txtsubtcre?>" required readonly/>
								</th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div> <!-- end div row -->
		<div class="row mb-3">
			<div class="col-sm-12">
				<button type="button" class="btn btn-success btn-sm" id="mbtn_JODRIN_save">
					Save
				</button>
				<button type="button" class="btn btn-danger btn-sm">
					Cancel
				</button>
			</div>
		</div>
		<!-- end table data entry -->
	<?php
	echo $mylibzsys->memypreloader01('mepreloaderme');
	echo $mylibzsys->memsgbox1('memsgme','System Alert','...');
	?>
	</section>
		  	
</main>  <!-- end main -->
<script>
	
	jQuery('.medatepicker').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true		
		});
		
    __mysys_apps.mepreloader('mepreloaderme',false);
    function meSetCellPadding () {
        var metable = document.getElementById ("tbl_JODRInData");
        metable.cellPadding = 3;
        metable.style.border = "1px solid #F6F5F4";
        var tabletd = metable.getElementsByTagName("td");
        //for(var i=0; i<tabletd.length; i++) {
        //    var td = tabletd[i];
        //    td.style.borderColor ="#F6F5F4";
        //}

    }
    meSetCellPadding();
    
    function __do_makeid()
    {
        var text = '';
        var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for( var i=0; i < 7; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }
    function my_add_line_item() { 
        try {
            var rowCount = jQuery('#tbl_JODRInData tr').length;
            var mid = __do_makeid() + (rowCount + 1);
            var clonedRow = jQuery('#tbl_JODRInData tr:eq(' + (rowCount - 2) + ')').clone(); 
            jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','fldcutno_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','fldmatcode_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','fldmatdesc_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','fldmatprice_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','flddrqty_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','flddraqty_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(6).attr('id','fldremk_' + mid);

            jQuery('#tbl_JODRInData tr').eq(rowCount - 2).before(clonedRow);
            jQuery(clonedRow).css({'display':''});

            jQuery('.to_number').keypress(function(evt) { 
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode != 46 && charCode > 31 
                    && (charCode < 48 || charCode > 57))
                    return false;
                return true;
            });
            
            meset_lookup_prod_items();
            //__my_branch_lookup();
            //__tamt_compute_totals();
            var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
            jQuery('#' + xobjArtItem).focus();
            jQuery( '#tbl_JODRInData tr').each(function(i) { 
                jQuery(this).find('td').eq(0).html(i);
            });
        } catch(err) { 
            var mtxt = 'There was an error on this page.\\n';
            mtxt += 'Error description: ' + err.message;
            mtxt += '\\nClick OK to continue.';
            alert(mtxt);
            return false;
        }  //end try 
    }

    function deleteRow(cobj,mruid) {
        jQuery(cobj).parent().parent().remove();
    } 
    my_add_line_item();
    
    jQuery('#mbtn_JODRIN_save').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var fld_mcustno = jQuery('#fld_mcustno' ).val();
			var custtknid = jQuery('#fld_mcustno' ).attr('data-custtknid');
			
			var adata = [];
			var rowCount = jQuery('#tbl_JODRInData tr').length;
			var clonedRow = jQuery('#tbl_JODRInData tr:eq(' + (rowCount - 2) + ')').clone(); 
			for(aa = 1; aa < (rowCount - 2); aa++) {  
				var clonedRow  = jQuery('#tbl_JODRInData tr:eq(' + aa + ')').clone(); 
				var mcutno = jQuery(clonedRow).find('input[type=text]').eq(0).val();
				var mmatcode = jQuery(clonedRow).find('input[type=text]').eq(1).val();
				var mmatprice = jQuery(clonedRow).find('input[type=text]').eq(3).val();
				var mdrqty = jQuery(clonedRow).find('input[type=text]').eq(4).val();
				var mdraqty = jQuery(clonedRow).find('input[type=text]').eq(5).val();
				var mmremk = jQuery(clonedRow).find('input[type=text]').eq(6).val();
				var mdata = mcutno + 'x|x' + mmatcode + 'x|x' + mmatprice + 'x|x' + mdrqty + 'x|x' + mdraqty + 'x|x' + mmremk;
				adata.push(mdata);
			}
			var mparam = {
				custtknid: custtknid,
				fld_mcustno: fld_mcustno,
				adata: adata
			}
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>trx-jo-delv-in-sv',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID 
					__mysys_apps.mepreloader('mepreloaderme',false);
					return false;
				},
				error: function() { // display global error on the menu function 
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
		}  //end try 
		
	});
	
	
	jQuery('#fld_mcustno' ) 
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
		source: '<?=site_url(); ?>search-customer/',
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		search: function(oEvent, oUi) { 
			var sValue = jQuery(oEvent.target).val();
			//jQuery(oEvent.target).val('&mcocd=1' + sValue);
			//alert(sValue);
		},
		select: function( event, ui ) {
			var terms = ui.item.value;
			
			jQuery(this).attr('alt', jQuery.trim(ui.item.value));
			jQuery(this).attr('title', jQuery.trim(ui.item.value));
			jQuery(this).attr('data-custtknid', ui.item.mtkn_rid);
			
			this.value = ui.item.value;
			return false;
		}
	})
	.click(function() { 
		 //jQuery(this).keydown(); 
		
		//var terms = this.value +'xox'+jQuery('#txtimport_vendor').val()+'xox'+jQuery('#txt_po_cls').val();
		jQuery(this).autocomplete('search', this.value);
		//jQuery(this).autocomplete('search', jQuery.trim(terms));
	});
		
	function meset_lookup_prod_items() { 
		jQuery('.meproditems' ) 
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
			source: '<?=site_url(); ?>search-prod-items/',
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) { 
				var sValue = jQuery(oEvent.target).val();
				//jQuery(oEvent.target).val('&mcocd=1' + sValue);
				//alert(sValue);
			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				
				jQuery(this).attr('alt', jQuery.trim(ui.item.mehierc));
				jQuery(this).attr('title', jQuery.trim(ui.item.mehierc));
				this.value = ui.item.value;
				var meid = jQuery(this).attr('id').split('_');
				jQuery('#fldmatdesc_' + meid[1]).val(ui.item.proddesc);
				jQuery('#fldmatprice_' + meid[1]).val(ui.item.prodprice);
				return false;
			}
		})
		.click(function() { 
			 //jQuery(this).keydown(); 
			
			//var terms = this.value +'xox'+jQuery('#txtimport_vendor').val()+'xox'+jQuery('#txt_po_cls').val();
			jQuery(this).autocomplete('search', this.value);
			//jQuery(this).autocomplete('search', jQuery.trim(terms));
		});			
	} //end meset_lookup_process_rate_quota
        	
</script>
