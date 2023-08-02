<?php
/* =================================================
 * Author      : Oliver Sta Maria
 * Date Created: October 13, 2022
 * Module Desc : JO-Quota Entry
 * File Name   : transactions/jo-quota.php
 * Revision    :
*/


$mtkn_trxno = ''; 
$adftag = '';
$txtdf_tag= '';
$txtgltrx_no = '';
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
$entTyp = '';
$entTyprid = '';

$txtpotobrnc='';
$txtimsno ='';
$txtgldate = '';


?>
<main id="main" class="main">
	<div class="pagetitle">
		<h1>JO Quota Entry</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Home</a></li>
				<li class="breadcrumb-item">Transaction</li>
				<li class="breadcrumb-item active">JO Quota</li>
			</ol>
		</nav>
	</div> <!-- End Page Title -->
	<section class="section">
		<div class="row">
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>JO Trx. No.</span>
					</div>
					<div class="col-sm-9">
						<input type="text" id="txtgltrx_no" name="txtgltrx_no" class="form-control form-control-sm meinput-sm-pad" value="<?=$txtgltrx_no;?>" readonly />
						<input type="hidden" name="__hmtkn_trxnoid" id="__hmtkn_trxnoid" value="<?= $mmnhd_rid;?>" />
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Name</span>
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
						<input data-id="__fl_upld" accept="image/gif,image/jpeg,image/png,application/pdf" class="form-control form-control-sm" size="5" id="__fl_upld" type="file" multiple name="__fl_upld[]">
					</div>
				</div>
			</div> <!-- end col-6 -->
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>JO Trx. Date:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm meinput-sm-pad form_datetime" data-id="" id="fld_gldate" name="fld_gldate" value="<?=$txtgldate;?>" disabled/>
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
					<table id="tbl_DRInData" class="metblentry-font">
						<thead>
							<th></th>
							<th class="text-center" nowrap>
								<button type="button" class="btn btn-primary btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item();" >
									<i class="bi bi-plus-lg"></i>
								</button>
							</th>
							<th>Cut No.</th>
							<th>JO No.</th>
							<th>Operation</th>
							<th>Qty</th>
							<th>Price/PC</th>
							<th>Total</th>
						</thead>
						<tbody id="contentArea">
							<tr style="display:none;">
								<td></td>
								<td>
									<button type="button" class="btn btn-danger btn-sm p-1 pb-0 mebtnpt1" onclick="javascript:confirmalert(this);">
										<i class="bi bi-x-circle-fill"></i>
									</button>
                                </td>
								<td><input type="text" size="15" /></td>
								<td><input type="text" size="15" /></td>
								<td><input type="text" class="meprocratequota" size="50" /></td>
								<td><input type="text" size="10" /></td>
								<td><input type="text" size="10" /></td>
								<td><input type="text" size="10" /></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="5"></th>
								<th>
									<input type="text" size="10"  id="fld_subtdeb" value="<?=$txtsubtdeb;?>" required readonly/>
								</th>
								<th></th>
								<th>
									<input type="text" size="10" id="fld_subtcre" value="<?=$txtsubtcre?>" required readonly/>
								</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div> <!-- end div row -->
		<!-- end table data entry -->
	</section>
		  	
</main>  <!-- end main -->
<script>
    __mysys_apps.mepreloader('mepreloaderme',false);
    function meSetCellPadding () {
        var metable = document.getElementById ("tbl_DRInData");
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
            var rowCount = jQuery('#tbl_DRInData tr').length;
            var mid = __do_makeid() + (rowCount + 1);
            var clonedRow = jQuery('#tbl_DRInData tr:eq(' + (rowCount - 2) + ')').clone(); 
            jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id','macctid_' + mid);
            jQuery(clonedRow).find('input[type=hidden]').eq(1).attr('id','mid_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','fld_matcode_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','fld_matdesc_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','fld_matpkg_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','fld_drqty_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','fld_draqty_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','fld_remk_' + mid);

            jQuery('#tbl_DRInData tr').eq(rowCount - 2).before(clonedRow);
            jQuery(clonedRow).css({'display':''});

            jQuery('.to_number').keypress(function(evt) { 
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode != 46 && charCode > 31 
                    && (charCode < 48 || charCode > 57))
                    return false;
                return true;
            });
            
            meset_lookup_process_rate_quota();
            //__my_branch_lookup();
            //__tamt_compute_totals();
            var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
            jQuery('#' + xobjArtItem).focus();
            jQuery( '#tbl_DRInData tr').each(function(i) { 
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
    
	function meset_lookup_process_rate_quota() { 
		jQuery('.meprocratequota' ) 
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
			source: '<?=site_url(); ?>search-proc-quota-rate/',
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
				this.value = ui.item.mdata;
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
