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
$endTime = '';
$startTime = '';

echo view('templates/meheader01');
?>
<main id="main" class="main">
	<div class="pagetitle">
		<h1>POS</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Home</a></li>
				<li class="breadcrumb-item">Sales</li>
				<li class="breadcrumb-item active">POS Re-printing Logs</li>
			</ol>
		</nav>
	</div> <!-- End Page Title -->
	<section class="section">
		<div class="row metblentry-font">
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Branch:</span>
					</div>
					<div class="col-sm-9">
						<input class="form-control form-control-sm" name="branchName" id="branchName" type="text" placeholder="Branch Name" value="<?=$txt_branch;?>" data-mtknid="<?=$txt_branchID;?>" <?=$intText;?> required>
					</div>
				</div> <!-- end Branch -->
				<div class="row mb-3">
					<div class="col-sm-9">
						<button type="submit" class="btn btn-success btn-sm" id="mbtn_getrecs">
							Get Records
						</button>
					</div>
				</div>
			</div> <!-- end col-md-6 -->
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-lg-6 col-md-6 col-sm-6">
						<input class="form-control form-control-sm" name="startDate" id="startDate" value="<?=$startDate;?>" type="date" required <?=$intText;?>>
						<strong class="form-text">Start Date </strong>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-lg-6 col-md-6 col-sm-6">
						<input class="form-control form-control-sm" name="endDate" id="endDate" value="<?=$endDate;?>" type="date" <?=$intText;?> required>
						<strong class="form-text">End Date </strong>
					</div>
				</div>
			</div> <!-- end col-md-6 -->
		</div> <!-- end row metblentry-font -->
		<!-- table data entry -->
		<div class="row mb-3">
			<div class="col-sm-12" id="tbl_items_ent">
			</div> <!-- end tbl_items_ent -->
		</div>
		
	</section> <!-- end section -->
</main>  <!-- end main -->
<?php
echo $mylibzsys->memypreloader01('mepreloaderme');
echo view('templates/mefooter01');
?>

<script type="text/javascript"> 
	
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
		   jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','fld_mitempromo' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','fld_qty' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','fld_srp' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','fld_ucost' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(6).attr('id','fld_promosrp' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(7).attr('id','fld_promototalsrp' + mid);

		   jQuery('#tbl_PayData tr').eq(1).before(clonedRow);
		   jQuery(clonedRow).css({'display':''});
		   //__my_item_lookup();
		   //__my_promotim_lookup();
		   //__tamt_compute_totals();
		   var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
		   jQuery('#' + xobjArtItem).focus();
		   jQuery( '#tbl_PayData tr').each(function(i) { 
				   jQuery(this).find('td').eq(0).html(i);
		   });
	   } catch(err) { 
		   var mtxt = 'There was an error on this page.\\n';
		   mtxt += 'Error description: ' + err.message;
		   mtxt += '\\nClick OK to continue.';
		   alert(mtxt);
		   return false;
		   }  //end try 
	} //end my_add_line_item
    	
	jQuery('#branchName')
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
		source: '<?= site_url(); ?>search-area-company/',
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		search: function(oEvent, oUi) {
			var sValue = jQuery(oEvent.target).val();
			jQuery(this).autocomplete('option', 'source', '<?=site_url();?>search-area-company/'); 
		},
		select: function( event, ui ) {
			var terms = ui.item.value;
			var mtkn_comp = ui.item.mtkn_comp;
			var mtkn_brnch = ui.item.mtkn_brnch;
			jQuery(this).val(terms);
			jQuery(this).attr('data-mtknid',ui.item.mtkn_rid);
			//jQuery('#branchName').attr("data-id",mtkn_brnch);
			jQuery(this).autocomplete('search', jQuery.trim(terms));
			return false;
		}
		
	})
	.click(function() {
		/*var comp = jQuery('#fld_Company').val();
		var comp2 = this.value +'XOX'+comp;
		var terms = comp2.split('XOX');//dto naq 4/25
		*/
		var terms = this.value;
		jQuery(this).autocomplete('search', jQuery.trim(terms));
	  
	}); //fld_area_code	
	
	jQuery('#mbtn_getrecs').click(function() { 
		try { 
			var mtnkid = jQuery('#branchName').attr('data-mtknid');
			var startDate = jQuery('#startDate').val();
			var endDate = jQuery('#endDate').val();
			var mparam =  {
				mtnkid: mtnkid,
				startDate: startDate,
				endDate: endDate 
			};
			__mysys_apps.mepreloader('mepreloaderme',true);
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST", 
				url: '<?= site_url() ?>mypos-reprint-recs-logs',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#tbl_items_ent').html(data);
				},
				error: function(data) { // display global error on the menu function 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}
			});	 			
		} catch(err) { 
		   var mtxt = 'There was an error on this page.\\n';
		   mtxt += 'Error description: ' + err.message;
		   mtxt += '\\nClick OK to continue.';
		   alert(mtxt);
		   return false;
		   }  //end try 		
	});
	__mysys_apps.mepreloader('mepreloaderme',false);
</script>
