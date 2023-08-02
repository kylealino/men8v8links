<?php
$request = \Config\Services::request();
$mymdarticle = model('App\Models\MyMDArticleModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$mysalesdepo = model('App\Models\MySalesDepositModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$mtkn_etr = trim($request->getVar('mtkn_etr'));

$vrecs = trim($request->getVar('vrecs'));

echo view('templates/meheader01');


if(!$myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0006','0001')) { 
	echo "
	<main id=\"main\">
		<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
	</main>
	";
	
} else {

$mysalesdepoent_tab = ' show active';
$mysalesdepoent_navlink = ' active';
$mysalesdeporecs_tab = '';
$mysalesdeporecs_navlink = '';

if($vrecs == 'yes') {  
	$mysalesdepoent_tab = '';
	$mysalesdepoent_navlink = '';
	$mysalesdeporecs_tab = ' show active';
	$mysalesdeporecs_navlink = ' active';
}

?>

<main id="main">

    <div class="pagetitle">
        <h1>Sales Deposit</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=site_url();?>">Sales</a></li>
                <li class="breadcrumb-item active">Deposit Entry</li>
            </ol>
            </nav>
    </div><!-- End Page Title -->

    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-bordered" id="mySalesDeposit" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link<?=$mysalesdepoent_navlink;?>" id="mysalesdepoent-tab" data-bs-toggle="tab" data-bs-target="#mysalesdepoent" type="button" role="tab" aria-controls="mysalesdepoent" aria-selected="true">Entry</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link<?=$mysalesdeporecs_navlink;?>" id="mysalesdeporecs-tab" data-bs-toggle="tab" data-bs-target="#mysalesdeporecs" type="button" role="tab" aria-controls="mysalesdeporecs" aria-selected="false">Records</button>
                    </li>
                </ul>
                <div class="tab-content" id="mySalesDepositContent">
                    <div class="tab-pane fade<?=$mysalesdepoent_tab?>" id="mysalesdepoent" role="tabpanel" aria-labelledby="mysalesdepoent-tab">
                    </div>
                    <div class="tab-pane fade<?=$mysalesdeporecs_tab?>" id="mysalesdeporecs" role="tabpanel" aria-labelledby="mysalesdeporecs-tab">
						<?php
							$data = $mysalesdepo->deposit_recs_branch(1,25,'');
							echo view('transactions/sales/mysalesdeposit-recs',$data);
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    echo $mylibzsys->memypreloader01('mepreloaderme');
    echo $mylibzsys->memsgbox3('memsg-salesdepo','System Alert','...');
    echo $mylibzsys->memsgbox_yesno1('meyn_salesdepo','','');
    echo $mylibzsys->memsgbox_yesno1('meyn2_salesdepo','','');
} //end ua_mod_access_verify
    ?>

</main>  <!-- end main -->

<?php
echo view('templates/mefooter01');
?>

<script type="text/javascript"> 
	__mysys_apps.mepreloader('mepreloaderme',false);
	mywg_salesdepoent_scr_load('<?=$mtkn_etr;?>');
    function mywg_salesdepoent_scr_load(mtkn_etr) { 
		try {
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mparam = {
				mtkn_etr: mtkn_etr 
			}
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?= site_url() ?>mysales-deposit-entry',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#mysalesdepoent').html(data);
					return false;
				},
				error: function(data) { // display global error on the menu function
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
			
    }  //end 


	function rfp_addRows() {
		try {
			var _trns_group = jQuery('#_trns_group').val();
			var rowCount = jQuery('#_tbl_deposit tr').length;
			if(_trns_group == ''){
				jQuery('#memsg-salesdepo_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Please select Group.</strong></div>");
				jQuery('#memsg-salesdepo').modal('show');
				return false;
			}
			
			if(_trns_group == 'Sales' && rowCount >= 3) {
				jQuery('#memsg-salesdepo_bod').html("<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Adding rows on Sales are limit to one row only.</strong></div>");
				jQuery('#memsg-salesdepo').modal('show');
				return false;
			}
			console.log(_trns_group);
			var mid = __mysys_apps.__do_makeid(5) + (rowCount + 1);
			var clonedRow = jQuery('#_tbl_deposit tr:eq(' + (rowCount - 1) + ')').clone(); 
			jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','_bank_name_' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','_acct_name' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(1).attr('data-mtknattr','.');
			jQuery(clonedRow).find('input[type=date]').eq(2).attr('id','_date_deposit_' + mid);
			jQuery(clonedRow).find('input[type=number]').eq(0).attr('id','__sales' + mid);
			jQuery(clonedRow).find('input[type=number]').eq(1).attr('id','__shopeepay' + mid);
			jQuery(clonedRow).find('input[type=number]').eq(2).attr('id','__expense' + mid);
			jQuery(clonedRow).find('input[type=number]').eq(3).attr('id','_amount_deposited_' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','_group_' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','_remarks_' + mid);

			jQuery('#_tbl_deposit tr').eq(rowCount - 1).before(clonedRow);
			jQuery(clonedRow).css({'display':''});
			jQuery(clonedRow).attr('id','tr_rec_' + mid);

			var _bank_name_ = jQuery(clonedRow).find('input[type=number]').eq(0).attr('id');
	
			jQuery('#' + _bank_name_).focus();
			__my_branchAcct_lookup();
			//__my_group_lookup();
			//__meRunningAmt();
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  
	}  //end rfp_addRows

	function salesdepo_delrow(eleIDRow) { 
		jQuery('#' + eleIDRow).remove();
	} //end salesdepo_delrow

	function __my_group_lookup() {  
		jQuery('.depositgroup' ) 
			// don't navigate away from the field on tab when SELECTing an item
		  .bind( 'keypress', function( event ) {
				if (event.keyCode === jQuery.ui.keyCode.ENTER && jQuery( this ).data( 'autocomplete-ui' ).menu.active ) {
					event.preventDefault();
					}
				if( event.keyCode === jQuery.ui.keyCode.TAB ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				autoFocus: true,
				source: '<?=site_url();?>mysearchdata/getdepositGroup',
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
					var m_uname = ui.item.m_uname;
					var m_ufulln = ui.item.m_ufulln;
					jQuery(this).attr('alt', $.trim(m_ufulln));
					jQuery(this).attr('title', $.trim(m_ufulln));
					this.value = m_ufulln;
				}
			})
			.click(function() { 
				var terms = this.value.split('=>');
				jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
			});		
	}
	__my_branchAcct_lookup();
	function __my_branchAcct_lookup() {  
		jQuery('.bankNameAcct') 
			// don't navigate away from the field on tab when SELECTing an item
		  .bind( 'keypress', function( event ) {
				if (event.keyCode === jQuery.ui.keyCode.ENTER && jQuery( this ).data( 'autocomplete-ui' ).menu.active ) {
					event.preventDefault();
					}
				if( event.keyCode === jQuery.ui.keyCode.TAB ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				autoFocus: true,
				source: '<?=site_url();?>mysales-deposit-get-Deposit-BranchAcct/?compName=' + jQuery('#_trns_comp').val() + '&branchName=' + jQuery('#_trns_brnch').val(),
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				search: function(oEvent, oUi) { 
				var sValue = jQuery(oEvent.target).val();
				var compName = jQuery('#_trns_comp').val();
				var branchName = jQuery('#_trns_brnch').val();
				jQuery(this).autocomplete('option', 'source', '<?=site_url();?>mysales-deposit-get-Deposit-BranchAcct/?compName='+compName+'&branchName='+branchName);

				},
				select: function( event, ui ) { 
					var terms = ui.item.value;
					this.value = ui.item.bankName;
					jQuery(this).attr('alt', jQuery.trim(ui.item.bankName));
					jQuery(this).attr('title', jQuery.trim(ui.item.bankName));
					var clonedRow = jQuery(this).parent().parent().clone();
					var txtacctNo = jQuery(clonedRow).find('input[type=text]').eq(1).attr('id');
					jQuery('#' + txtacctNo).val(ui.item.acctNo);
					jQuery(this).attr('data-mtknattr',ui.item.mtkn_rid);
					return false;
				}
			})
			.click(function() { 
				var terms = this.value;
				jQuery(this).autocomplete('search', jQuery.trim(terms));
		});		
	} //end __my_branchAcct_lookup
</script>
