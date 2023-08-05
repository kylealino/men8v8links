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
                                <label class="col-sm-3 form-label" for="sub_item">Select Itemcode:</label>
                                <div class="col-sm-9">
                                    <input type="text" id="sub_item" name="sub_item" class="form-control form-control-sm bg-white" autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <div class="col-md-12 col-md-12 col-md-12">
                                <table class="table table-hover table-bordered text-center table-sm" id="tbl-bom">
                                    <thead>
                                        <tr>
                                            <th nowrap="nowrap">
                                                <button type="button" class="btn btn-success btn-sm" onclick="javascript:my_add_line_item_bom();" >
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </th>
                                            <th nowrap="nowrap">ITEM</th>
                                            <th nowrap="nowrap">UNIT</th>
                                            <th nowrap="nowrap">UOM</th>
                                            <th nowrap="nowrap">COST</th>
                                            <th nowrap="nowrap">COST NET OF VAT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- <tr>
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
                                        </tr> -->

                                        <tr style="display: none;">
                                            <td nowrap="nowrap">
                                            <button type="button" class="btn btn-xs btn-danger" style="font-size:15px; padding: 2px 6px 2px 6px; " onclick="$(this).closest('tr').remove();"><i class="bi bi-x"></i></button>
                                            <input class="mitemrid" type="hidden" value=""/>
                                            <input type="hidden" value=""/>
                                            </td>
                                            <td nowrap="nowrap"><input type="text" class="form-control form-control-sm mitemcode"></td>
                                            <td nowrap="nowrap"><input type="text" class="form-control form-control-sm"></td>
                                            <td nowrap="nowrap"><input type="text" class="form-control form-control-sm"></td>
                                            <td nowrap="nowrap"><input type="text" class="form-control form-control-sm" onmouseover="javascript:__pack_totals();" onmouseout="javascript:__pack_totals();"></td>
                                            <td nowrap="nowrap"><input type="text" class="form-control form-control-sm"></td>
                                        </tr>
                                    </tbody>
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
    __my_item_lookup();

    jQuery('#sub_item')
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
          source: '<?= site_url(); ?>get-main-itemc',
          focus: function() {
                // prevent value inserted on focus
                return false;
              },
              select: function( event, ui ) {
                var terms = ui.item.value;
                
                jQuery('#sub_item').val(terms);
                jQuery('#sub_item').attr("data-id-brnch-name",ui.item.mtkn_rid);

            
                jQuery(this).autocomplete('search', jQuery.trim(terms));

                return false;

        
              }
            })
        .click(function() {
          var terms = this.value;
          jQuery(this).autocomplete('search', jQuery.trim(terms));     
    }); 


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

    // function my_add_line_item_bom() {  
    //       try {
            
    //         var rowCount = jQuery('#tbl-bom tr').length;
    //         var mid =  (rowCount + 1);
    //         var clonedRow = jQuery('#tbl-bom tr:eq(' + (rowCount - 1) + ')').clone(); 

    //         jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','mitemcode_' + mid);
    //         jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','mitemdesc_' + mid);
    //         jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','mitembcode_' + mid);
    //         jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','mitemdisc_' + mid);
    //         jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','mitemprice_' + mid);
    //         jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','mitemdiscsrp_' + mid);
    //         jQuery(clonedRow).find('input[type=text]').eq(6).attr('id','mitemcost_' + mid);
    //         jQuery(clonedRow).find('input[type=text]').eq(7).attr('id','mitemdiscsrp_' + mid);
    //         jQuery(clonedRow).find('input[type=text]').eq(8).attr('id','mitemcost_' + mid);
    //         jQuery(clonedRow).find('input[type=text]').eq(9).attr('id','mitemcost_' + mid);
            
    //         jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id','mitemrid_' + mid);
            
            
    //         jQuery('#tbl-bom tr').eq(rowCount - 1).before(clonedRow);
    //         jQuery(clonedRow).css({'display':''});

    //         // __my_item_lookup();
    //         // __pack_totals();
            
    //       } catch(err) { 
    //         var mtxt = 'There was an error on this page.\\n';
    //         mtxt += 'Error description: ' + err.message;
    //         mtxt += '\\nClick OK to continue.';
    //         alert(mtxt);
    //         return false;
    //   }  //end try 
    // }

    function my_add_line_item_bom() {
		try {
			var rowCount = jQuery('#tbl-bom tr').length;

			var mid = (rowCount + 2);
            var clonedRow = jQuery('#tbl-bom tr:eq(' + (rowCount - 1) + ')').clone(); 

            jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','mitemcode_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','mitemdesc_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','mitembcode_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','mitemdisc_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','mitemprice_' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','mitemdiscsrp_' + mid);


            jQuery('#tbl-bom tr').eq(rowCount - 1).before(clonedRow);
            jQuery(clonedRow).css({'display':''});
            var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
            jQuery('#' + xobjArtItem).focus();

            __my_item_lookup();
            __pack_totals();

		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  
	}  //end rfp_addRows

    function __my_item_lookup(){
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
            source: '<?= site_url(); ?> get-sub-materials',
            focus: function() {
                  // prevent value inserted on focus
                  return false;
                },
                select: function( event, ui ) {
                  var terms = ui.item.value;
                  
                  jQuery(this).attr('alt', jQuery.trim(ui.item.value));
                  jQuery(this).attr('title', jQuery.trim(ui.item.value));
                  
                  this.value = ui.item.value;
                  

                  var clonedRow = jQuery(this).parent().parent().clone();
                  var indexRow = jQuery(this).parent().parent().index();
                  var xobjitemdesc = jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id'); //ID
                  var xobjitemuom = jQuery(clonedRow).find('input[type=text]').eq(2).attr('id');/*BCODE*/

                  $('#' + xobjitemdesc).val(ui.item.ART_CODE);
                  $('#' + xobjitemuom).val(ui.item.ART_UOM);

                  return false;
                }
              })
          .click(function() { 

              //jQuery(this).keydown(); 
              var terms = this.value;
              //jQuery(this).autocomplete('search', '');
              jQuery(this).autocomplete('search', jQuery.trim(terms));
            });  
        }

        function __pack_totals() { 

            try { 
            var rowCount1 = jQuery('#tbl-bom tr').length - 1;
            var adata1 = [];
            var adata2 = [];
            var mdata = '';
            var ninc = 0;
            var nTAmount = 0;
            var nTQty = 0;
            for(aa = 1; aa < rowCount1; aa++) { 
              var clonedRow = jQuery('#tbl-bom tr:eq(' + aa + ')').clone(); 
              var cost = jQuery(clonedRow).find('input[type=text]').eq(3).val();
              var xTAmntId = jQuery(clonedRow).find('input[type=text]').eq(4).attr('id');

              var ntotal = cost / 1.12;
              
              $('#' + xTAmntId).val(ntotal.toFixed(2));
  

            }  //end for 
            
          } catch(err) {
            var mtxt = 'There was an error on this page.\n';
            mtxt += 'Error description: ' + err.message;
            mtxt += '\nClick OK to continue.';
            alert(mtxt);
            $.hideLoading();
            return false;
        }  //end try            
          
      }

    
</script>