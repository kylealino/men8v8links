<?php
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$myusermod = model('App\Models\MyUserModel');
$mydbname = model('App\Models\MyDBNamesModel');
$mysalesdepo = model('App\Models\MySalesDepositModel');

//date_default_timezone_set('Asia/Manila');

$db_erp = $mydbname->medb(0);
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$cuserrema = $myusermod->mysys_userrema();

?>
<div class="mt-2 ms-1 me-1">
    <div class="col-md-12">
        <div class="card">
			<div class="mb-3 mt-3 ms-1 me-1">
				<div class="col-sm-6 form form-control-sm">
					<label for="rfpcf-select">Please select:</label>
					<select id="rfpcf-select" class="form-control form-control-sm">
						<option value=""></option>
						<option value="rfp">Request for Payment Form</option>
						<option value="pcf">Request for PCF Replenishment Form</option>
					</select>
				</div>
			</div>
			<div id="rfpcf-view" class="text-left p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">
				<?php

				?> 
			</div>
    	</div>        
    </div> <!-- end col-8 -->    
</div> <!-- end row mt-1 ms-1 me-1 -->

<script type="text/javascript"> 
$(document).ready(function() {
	$('#rfpcf-select').on('change', function() {
	var selectedOption = $(this).val();
	switch (selectedOption) {
		case 'rfp':

			rfp_view();

			function rfp_view(){ 
			var ajaxRequest;

			ajaxRequest = jQuery.ajax({
			url: "<?=site_url();?>myrfp-view",
			type: "post",
			data: {
				
			}
			});

			// Deal with the results of the above ajax call
			__mysys_apps.mepreloader('mepreloaderme',true);
			ajaxRequest.done(function(response, textStatus, jqXHR) {
			jQuery('#rfpcf-view').html(response);
			__mysys_apps.mepreloader('mepreloaderme',false);
			});
		};
		break;
		case 'pcf':

		pcf_view();

		function pcf_view(){ 
			var ajaxRequest;

			ajaxRequest = jQuery.ajax({
				url: "<?=site_url();?>mypcf-view",
				type: "post",
				data: {
				}
			});

			// Deal with the results of the above ajax call
			__mysys_apps.mepreloader('mepreloaderme',true);
			ajaxRequest.done(function(response, textStatus, jqXHR) {
				jQuery('#rfpcf-view').html(response);
				__mysys_apps.mepreloader('mepreloaderme',false);
			});
		};
		break;
		case '':
		break;

		default:
		break;
		}
	});
});

    metamtpcf();
    function metamtpcf(){
        var totalsum=0;
        $('#_tbl_pcf tr').each(function(){
            $(this).find('td input[type=number]').eq(0).each(function(){
                var inputVal=$(this).val();
                if($.isNumeric(inputVal)){
                    totalsum += parseFloat(inputVal);
                }
                $('#__meTotalAmtpcf').html(__mysys_apps.oa_addCommas(totalsum.toFixed(2)));
            })
        });
    }
    
	
	me_tamtrfp();
	function me_tamtrfp(){
		var totalsum=0;
		jQuery('#_tbl_deposit tr').each(function() { 
			jQuery(this).find('td input[type=number]').eq(0).each(function() {
				var inputVal = jQuery(this).val();
				if(jQuery.isNumeric(inputVal)){
					totalsum += parseFloat(inputVal);
				}
				jQuery('#__meTotalAmtrfp').html(__mysys_apps.oa_addCommas(totalsum.toFixed(2)));
			})
		});
	}  //end me_tamtrfp
	
	jQuery('#mebtn_saledepofile').click(function() { 
		try { 
			jQuery('#__upld_file_img01').click();
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			
			return false;

		}  //end try					
	});  //end mebtn_saledepofile click event 
	
	jQuery('#__upld_file_img01').on('change',function() { 
		try { 
			//alert(this.files.length + ' ' + this.files[0].name);
			if(this.files.length > 0) { 
				var mefilecontent = "<ul class=\"list-group\">";
				for(aa = 0; aa < this.files.length; aa++) { 
					mefilecontent += "<li class=\"list-group-item\"><i class=\"bi bi-star me-1 text-success\"></i> " + this.files[aa].name + "</li>"; 
				}
				mefilecontent += "</ul";
				jQuery('#__mefilesme').html(mefilecontent);
			}
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			
			return false;
		}  //end try					
	}); //end __upld_file_img01 change event
	
	
	jQuery('.expense-type')
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
          source: '<?= site_url(); ?>get-expense-type',
          focus: function() {
                // prevent value inserted on focus
                return false;
              },
              select: function( event, ui ) {
                var terms = ui.item.value;
                
				jQuery('#expense-type').val(terms);
                jQuery('#expense-type').val(ui.item.Name);
				

                jQuery(this).autocomplete('search', jQuery.trim(terms));

                return false;

                
              }
            })
        .click(function() {
          var terms = this.value;
          jQuery(this).autocomplete('search', jQuery.trim(terms));
          
    }); //whse

	jQuery('.BRNCH_NAME')
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
          source: '<?= site_url(); ?>get-branch-name',
          focus: function() {
                // prevent value inserted on focus
                return false;
              },
              select: function( event, ui ) {
                var terms = ui.item.value;
                
				jQuery('#BRNCH_NAME').val(terms);
                jQuery('#BRNCH_NAME').val(ui.item.BRNCH_NAME);
				

                jQuery(this).autocomplete('search', jQuery.trim(terms));

                return false;

                
              }
            })
        .click(function() {
          var terms = this.value;
          jQuery(this).autocomplete('search', jQuery.trim(terms));
          
    }); //whse
	
</script>

