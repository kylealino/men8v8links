<?php
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

if(!$myusermod->ua_mod_access_verify($db_erp,$cuser,'04','0007','000101')) { 
	echo "
	<div class=\"row mt-1 ms-1 me-1\">
		<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
	</div>
	";
	
} else {
?>
<div class="row mt-2 ms-1 me-1">
    <div class="col-md-8">
        <div class="card">
            <div class="row mb-3 mt-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Item Code</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" data-id="" id="fld_sc2itemcode_s" name="fld_sc2itemcode_s" value="" required/>
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Desc. Code</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" data-id="" id="fld_sc2desccode" name="fld_sc2desccode" value="" required/>
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-3">
                    <span class="fw-bold">Branch</span>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm" data-id="" id="fld_sc2branch_s" name="fld_sc2branch_s" value="" required data-mtknattr="">
                </div>
            </div>
            <div class="row mb-3 ms-1 me-1">
                <div class="col-sm-6">
                    <button class="btn btn-success btn-sm" id="btn_stockcard_gen" name="btn_stockcard_gen" type="submit">Generate</button>
                    <button class="btn btn-info btn-sm" id="btn_stockcard_dl" name="btn_stockcard_dl" type="submit">Download</button>
                    <?=anchor('myreport-inventory', 'Reset',' class="btn btn-success btn-sm" ');?>
                </div>
            </div>

        </div>        
    </div> <!-- end col-8 -->    
</div> <!-- end row mt-1 ms-1 me-1 -->
<div class="row mt-2 ms-1 me-1">
	<div class="col-md-12">
		<div class="card" id="wgstockcard">
		</div>
	</div>
</div>  <!-- end row mt-1 ms-1 me-1 2nd -->
<script type="text/javascript"> 
	jQuery('#fld_sc2branch_s')
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
					//var comp = jQuery('#fld_Company').val();
					//var comp = jQuery('#fld_Company').attr("data-id");
					//mysearchdata/companybranch_v
					jQuery(this).autocomplete('option', 'source', '<?=site_url();?>company-branch-ua'); 
					//jQuery(oEvent.target).val('&mcocd=1' + sValue);

				},
				select: function( event, ui ) {
					var terms = ui.item.value;
					var mtkn_comp = ui.item.mtkn_comp;
					var mtknr_rid = ui.item.mtknr_rid;
					var mtkn_brnch = ui.item.mtkn_brnch;
					jQuery('#fld_sc2branch_s').val(terms);
					jQuery(this).attr('data-mtknattr',mtknr_rid);
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
	}); // end fld_sc2branch_s
	
	jQuery('#fld_sc2itemcode_s') 
		// don't navigate away from the field on tab when selecting an item
		//mysearchdata/mat_article/
	   .bind( 'keypress', function( event ) {
			if (event.keyCode === jQuery.ui.keyCode.ENTER && jQuery( this ).data( 'autocomplete-ui' ).menu.active ) {
				event.preventDefault();
				}
				if( event.keyCode === jQuery.ui.keyCode.ENTER ) {
					event.preventDefault();
				}
				
			})
		.autocomplete({
			minLength: 0,
			source: '<?= site_url(); ?>mat-article-ua',
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
				jQuery(this).attr('alt', jQuery.trim(ui.item.ART_CODE));
				jQuery(this).attr('title', jQuery.trim(ui.item.ART_CODE));
				jQuery(this).attr('src', jQuery.trim(ui.item.ART_IMG));
				
				//console.log(wshe_id);
				this.value = ui.item.ART_CODE;
				
				return false;
			}
		})
		.click(function() { 
			//jQuery(this).keydown(); 
			var terms = this.value.split('=>');
			//jQuery(this).autocomplete('search', '');
			jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
	});  //end fld_sc2itemcode_s
	
	jQuery('#btn_stockcard_gen').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var bid_mtknattr = jQuery('#fld_sc2branch_s').attr('data-mtknattr');
			
			//jQuery('#dl_submit_btn_stinqbr').css({display:'none'});
			
			jQuery('#btn_stockcard_gen').hide();
			//jQuery('#__mbtn_stinqbr_download').css({display:'flex'});
			var fld_stinqbritemcode_s= jQuery('#fld_stinqbritemcode_s').val();
			var fld_stinqbrbranch = jQuery('#fld_sc2branch_s').val();
			var fld_stinqbrbranch_id = bid_mtknattr;
			var fld_stinqbr_dtefrom = '';
			var fld_stinqbr_dteto = '';
			var fld_tap_s = '';
			var mparam ={
				fld_stinqbritemcode_s:fld_stinqbritemcode_s,
				fld_stinqbrbranch: fld_stinqbrbranch,
				fld_stinqbrbranch_id: fld_stinqbrbranch_id,
				fld_stinqbr_dtefrom: fld_stinqbr_dtefrom,
				fld_stinqbr_dteto: fld_stinqbr_dteto,
				fld_tap_s: fld_tap_s,
				mpages: 1 
		   }
			
			jQuery.ajax({ // default declaration of ajax parameters
				url: '<?=site_url()?>myreport-stockcard-recs',
				method:"POST",
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wgstockcard').html(data);
					return false;
				},
				error: function() { // display global error on the menu function
					alert('error loading page...');
					__mysys_apps.mepreloader('mepreloaderme',false);
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
	
	//Description Code
	jQuery('#fld_sc2desccode') 
		// don't navigate away from the field on tab when selecting an item
		// mat-art-section2
		//ysearchdata/mat_art_section2
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
	  autoFocus:true,
	  source: '<?= site_url(); ?>mat-art-section2/',
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
		  this.value = ui.item.value;
			return false;
		}
	})
	.click(function() { 
		//jQuery(this).keydown(); 
		var terms = this.value;
		//jQuery(this).autocomplete('search', '');
		jQuery(this).autocomplete('search', jQuery.trim(terms));
	});  //end fld_sc2desccode	
	
</script>
<?php
}
?>
