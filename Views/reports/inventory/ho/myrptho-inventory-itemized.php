<div class="row mt-3 ms-1 me-1">
    <div class="col-md-8">
		<div class="row mb-3 ms-1 me-1">
			<div class="col-sm-3">
				<span class="fw-bold">Item Code</span>
			</div>
			<div class="col-sm-9">
				<input type="text" class="form-control form-control-sm" data-id="" id="meitemcode" name="meitemcode" data-memtkn="" value="" required/>
			</div>
		</div>
		<div class="row mb-3 ms-1 me-1">
			<div class="col-sm-6">
				<button class="btn btn-success btn-sm" id="btn_itemized_abranch" name="btn_itemized_abranch" type="submit">All Branches Itemized</button>
			</div>
		</div>
	</div>
</div> <!-- end div row -->
<div class="row mt-3 ms-1 me-1" id="mywg_ivty_itemized_abranch">
</div>
<script>
        jQuery('#meitemcode') 
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
                source: '<?= site_url(); ?>search-mat-article-ho',
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                search: function(oEvent, oUi) { 
                    var sValue = jQuery(oEvent.target).val();
                },
                select: function( event, ui ) {
                    var terms = ui.item.value;
                    jQuery(this).attr('alt', jQuery.trim(ui.item.ART_CODE));
                    jQuery(this).attr('title', jQuery.trim(ui.item.ART_CODE));
                    jQuery(this).attr('src', jQuery.trim(ui.item.ART_IMG));
                    jQuery(this).attr('data-memtkn',ui.item.mtkn_rid);
                    
                    //console.log(wshe_id);
                    this.value = ui.item.ART_CODE;
                    
                    return false;
                }
            })
            .click(function() { 
                //jQuery(this).keydown(); 
                var terms = this.value;
                jQuery(this).autocomplete('search', jQuery.trim(terms));
        });  //end meitemcode
        
        jQuery('#btn_itemized_abranch').click(function() { 
            try {   
                __mysys_apps.mepreloader('mepreloaderme',true);
                var meitemcode = jQuery('#meitemcode').val();
                var itemmtkn = jQuery('#meitemcode').attr('data-memtkn');
                
                var mparam = { 
                    meitem:meitemcode,
                    meitemtkn:itemmtkn,
                    mpages: 1 
               }

                jQuery.ajax({ // default declaration of ajax parameters
                    url: '<?=site_url()?>myreport-inventory-itemized-proc',
                    method:"POST",
                    context: document.body,
                    data: eval(mparam),
                    global: false,
                    cache: false,
                    success: function(data)  { //display html using divID
                        __mysys_apps.mepreloader('mepreloaderme',false);
                        jQuery('#mywg_ivty_itemized_abranch').html(data);
                        return false;
                    },
                    error: function() { // display global error on the menu function
                        __mysys_apps.mepreloader('mepreloaderme',false);
                        alert('error loading page...');
                        return false;
                    }   
                }); 
            } catch (err) {
                __mysys_apps.mepreloader('mepreloaderme',false);
                var mtxt = 'There was an error on this page.\n';
                mtxt += 'Error description: ' + err.message;
                mtxt += '\nClick OK to continue.';
                alert(mtxt);
            } //end try
        }); // end button btn_itemized_abranch         
</script>
