<?php
/**
 *	File        : masterdata/sub_masterdata_bom/sub-md-item-bom-main.php
 *  Auhtor      : Kyle Alino
 *  Date Created: Jul 28, 2023
 * 	last update : Jul 28, 2023
 * 	description : Sub Masterdata Main
 */


?>
<main id="main">
    <div class="pagetitle">
    <h1>Sub Item BOM</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Sub Item BOM</li>
            </ol>
        </nav>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header mb-3">
                    <h3 class="h4 mb-0"> <i class="bi bi-pencil-square"></i> Entry</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="row mb-3">
                                <label class="col-sm-3 form-label" for="main_itemc">Select Itemcode:</label>
                                <div class="col-sm-9">
                                    <input type="text" id="main_itemc" name="main_itemc" class="form-control form-control-sm bg-white" autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <div class="col-md-12 col-md-12 col-md-12">
                                <table class="table table-light table-hover table-bordered text-center table-sm">
                                    <thead>
                                        <tr>
                                            <th  nowrap="nowrap" colspan="6" class="text-center">RAW PRODUCT COST AND YIELD</th>
                                            <th  nowrap="nowrap" colspan="4" class="text-center">PRODUCT COST</th>
                                        </tr>
                                        <tr>
                                            <th nowrap="nowrap">
                                                <button type="button" class="btn btn-success btn-sm" onclick="javascript:my_add_line_item_fgpack();" >
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </th>
                                            <th nowrap="nowrap">ITEM</th>
                                            <th nowrap="nowrap">COST</th>
                                            <th nowrap="nowrap">YIELD</th>
                                            <th nowrap="nowrap">UOM</th>
                                            <th nowrap="nowrap">COST/YIELD</th>
                                            <th nowrap="nowrap">UNIT</th>
                                            <th nowrap="nowrap">UOM</th>
                                            <th nowrap="nowrap">COST</th>
                                            <th nowrap="nowrap">COST NET OF VAT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td  nowrap="nowrap">
                                                <button type="button" class="btn btn-xs btn-danger" style="font-size:15px; padding: 2px 6px 2px 6px; " onclick="$(this).closest('tr').remove();"><i class="bi bi-x"></i></button>
                                            </td>
                                            <td  nowrap="nowrap">test</td>
                                            <td  nowrap="nowrap">test</td>
                                            <td  nowrap="nowrap">test</td>
                                            <td  nowrap="nowrap">test</td>
                                            <td  nowrap="nowrap">test</td>
                                            <td  nowrap="nowrap">test</td>
                                            <td  nowrap="nowrap">test</td>
                                            <td  nowrap="nowrap">test</td>
                                            <td  nowrap="nowrap">test</td>
                                        </tr>
                                    </tbody>
                                    <tfoot >
                                        <tr>
                                            <td colspan="8" class="text-end">EST. SPOILAGE/WASTAGE(2% TOTAL FOOD COST)</td>
                                            <td class="text-right" id="total"></td>
                                            <td class="text-right" id="total"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-end">FOOD COST</td>
                                            <td class="text-right" id="total"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-end">ADD: VAT</td>
                                            <td class="text-right" id="total"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-end">TOTAL FOOD COST</td>
                                            <td class="text-right" id="total"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-end">Opex 20%</td>
                                            <td class="text-right" id="total"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-end">TOTAL</td>
                                            <td class="text-right" id="total"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-end">Profit 40%</td>
                                            <td class="text-right" id="total"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" class="text-end">SRP</td>
                                            <td class="text-right" id="total"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div> 
                        </div> 
                    </div> 
                    <div class="row gy-2 mb-3">

                    </div>
                </div> 
            </div>
        </div>
    </div> 

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header mb-3">
                    <h3 class="h4 mb-0"> <i class="bi bi-pencil-square"></i> Sub BOM Records</h3>
                </div>
                <div class="card-body">
                    <div id="subitems" class="text-center p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">
                        <?php

                        ?> 
                    </div> 
                </div>
                </div> 
            </div>
        </div>
    </div> 

</main>

<script>

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
			source: '<?= site_url(); ?>get-branch/',
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) {
				var sValue = jQuery(oEvent.target).val();

			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				jQuery('#branch_name').val(terms);
				jQuery(this).autocomplete('search', jQuery.trim(terms));
				return false;
			}
		})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	});	//end branch_name


    $("#mbtn_vw_recs").click(function(e){
    try { 

          var branch_name = jQuery('#branch_name').val();

          var mparam = {
            branch_name:branch_name,
          };  


      $.ajax({ 
        type: "POST",
        url: '<?=site_url();?>sub-inv-recs-vw',
        context: document.body,
        data: eval(mparam),
        global: false,
        cache: false,
        success: function(data)  { //display html using divID
            __mysys_apps.mepreloader('mepreloaderme',false);
            jQuery('#salesrecs').html(data);
            return false;
          },
        error: function() {
          alert('error loading page...');
         // $.hideLoading();
          return false;
        } 
      });

    } catch(err) {
      var mtxt = 'There was an error on this page.\n';
      mtxt += 'Error description: ' + err.message;
      mtxt += '\nClick OK to continue.';
      alert(mtxt);
    }  //end try
    return false; 
  });

    // $("#branch_name").change(function() {
    //     sub_items_view();
    // });

    // $('#anchor-list').on('click',function(){
    // $('#anchor-list').addClass('active');
    // $('#anchor-items').removeClass('active');
    // var mtkn_whse = '';
    // sub_items_view();

    // });

    // function sub_items_view(){ 
    //     var ajaxRequest;

    //     ajaxRequest = jQuery.ajax({
    //         url: "<?=site_url();?>sub-inv-recs",
    //         type: "post"
    //     });

    //     __mysys_apps.mepreloader('mepreloaderme',true);
    //     ajaxRequest.done(function(response, textStatus, jqXHR) {
    //         jQuery('#salesrecs').html(response);
    //         __mysys_apps.mepreloader('mepreloaderme',false);
    //     });
    // };
</script>