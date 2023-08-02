<?php
/* =================================================
 * Author      : Oliver Sta Maria
 * Date Created: Sept 02, 2022
 * Module Desc : Delivery In
 * File Name   : inventory/dr-in/dr-in.php
 * Revision    :
*/


$mtkn_trxno = ''; 
$adftag = '';
$txtdf_tag= '';
$txtctltrx_no = '';
$txtcomp = '';
$txtarea_code = '';
$txtsupplier = '';
$txtpono = '';
$fld_trxdate = '';
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

$medrrefno = '';

?>
<main id="main" class="main">
	<div class="pagetitle">
		<h1>Delivery Receiving In</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Home</a></li>
				<li class="breadcrumb-item">Inventory</li>
				<li class="breadcrumb-item active">DR-In</li>
			</ol>
		</nav>
	</div> <!-- End Page Title -->
	<section class="section">
		<div class="row">
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Trx. No.</span>
					</div>
					<div class="col-sm-9">
						<input type="text" id="txtctltrx_no" name="txtctltrx_no" class="form-control form-control-sm" data-trxtkn="<?=$mtkn_trxno;?>" value="<?=$txtctltrx_no;?>" readonly />
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Company</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" id="fld_Company" name="fld_Company" required>
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
						<span>Trx. Date:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm meinput-sm-pad form_datetime" data-id="" id="fld_trxdate" name="fld_trxdate" value="<?=$fld_trxdate;?>" disabled/>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Supplier:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm meinput-sm-pad" id="fld_supnme" value="<?= $VEND_NAME ?>"/>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>DR NO/Ref Doc No.:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm meinput-sm-pad" id="medrrefno" value="<?= $medrrefno ?>"/>
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
							<th>Material Code</th>
							<th>Material Description</th>
							<th>PKG</th>
							<th>DR Qty</th>
							<th>Actual Qty</th>
							<th>Remarks</th>
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
								<td><input type="text" size="25" /></td>
								<td><input type="text" size="10" /></td>
								<td><input type="text" size="10" /></td>
								<td><input type="text" size="10" /></td>
								<td><input type="text" size="30" /></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="5"></th>
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
            
            //__my_item_lookup();
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
</script>
