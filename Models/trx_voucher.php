<?php

//VARIABLE DECLARATIONS

$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$mytrxfgpack = model('App\Models\MyPromoVoucherModel');
$mydataz = model('App\Models\MyDatumModel');
$myusermod = model('App\Models\MyUserModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$this->dbx = $mylibzdb->dbx;
$this->db_erp = $mydbname->medb(0);
$branch_code = '';
$branch_name = '';
$mtkn_txt_branch = '';
$mencd_date       = date("Y-m-d");  
$mtkn_fgpacktrr = $request->getVar('voucher_trxno');
$voucher_trxno = '';
$recid = '';
$start_date = ''; 
$start_time = date('08:00');
$end_date = ''; 
$end_time = date('23:59');
$discount_value=''; 
$voucher_code=''; 
$nporecs = 0;
$promo_name="";
$voucher_amount = 0;
$voucher_discount = 0;

$txt_promoname = $request->getVar('txt_promodesc');
$disable_ifapproved = '';


//CHECK IF THERE'S A FORM OF RETRIEVAL

if(!empty($mtkn_fgpacktrr)) {
  $str = "
  SELECT 
  aa.`voucher_trxno`,
  aa.`branch_code`,
  aa.`branch_name`,
  aa.`discount_value`,
  aa.`start_date`,
  aa.`start_time`,
  aa.`end_date`,
  aa.`is_approved`,
  aa.`end_time`
  FROM `gw_voucher_hd` aa
  WHERE aa.`voucher_trxno` = '$mtkn_fgpacktrr'


  ";

  $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
  $rw = $q->getRowArray();
  $voucher_trxno = $rw['voucher_trxno'];
  $branch_code = $rw['branch_code'];
  $branch_name = $rw['branch_name'];
  $discount_value=$rw['discount_value'];
  $start_date = $rw['start_date'];
  $start_time = $rw['start_time'];
  $end_date = $rw['end_date'];
  $end_time = $rw['end_time'];
  $txt_promoname = $rw['promo_name'];
  $disable_ifapproved = ($rw['is_approved'] == 'Y' ? ' disabled ' : '');

}
echo view('templates/meheader01');
?>
<main id="main" class="main">
<div class="card">
  <div class="card-body">
  <div class="pagetitle">
    <br>
    <h1>Promotion</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Sales</li>
        <li class="breadcrumb-item active">Promotion - VOUCHER</li>
      </ol>
    </nav>
  </div> <!-- End Page Title -->

          <!-- START HEADER DATA -->

          <div class="row mb-3">
            <div class="col-lg-12">

              <div class="row mb-3">
                  <label class="col-sm-3 form-label" for="voucher_trxno">Promo Voucher Code:</label>
                <div class="col-sm-9">
                  <input type="text" id="voucher_trxno" name="voucher_trxno" placeholder="Promo Voucher Code" style="background-color: #EAEAEA;" class="form-control form-control-sm" value="<?=$voucher_trxno;?>" readonly/>
                  <input type="hidden" id="voucher_trxno" name="__hmpromotrxnoid" class="form-control form-control-sm" value="<?=$mtkn_fgpacktrr;?>"/>
                </div>
              </div> 

              <div class="row mb-3">
                <label class="col-sm-3 form-label" for="branch_code1">Branch:</label>
                <div class="col-sm-9">
                  <input type="text" data-id-brnch-name="<?=$branch_name;?>" placeholder="Branch Name" id="branch_name" name="branch_name" class="form-control form-control-sm " value="<?=$branch_name;?>" required/>
                  <input type="hidden" data-id-brnch="<?=$branch_code;?>" placeholder="Branch Name" id="branch_code" name="branch_code" class="branch_code form-control form-control-sm " value="<?=$branch_code;?>" required/>     
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 form-label" for="txt_promodesc">Promo Name:</label>
                <div class="col-sm-9">
                  <input type="text" id="txt_promodesc" name="txt_promodesc" placeholder="Promo Description" class="form-control form-control-sm" value="<?=$txt_promoname;?>" />
                </div>
              </div> 
          
              <div class="row gy-2 offset-lg-3">
                <div class="col-sm-3">
                  <input type="date"  id="start_date" name="start_date" class="start_date form-control form-control-sm " value="<?=$start_date;?>" required/>
                  <label for="start_date">Start date</label>
                </div>

                <div class="col-sm-3">
                  <input type="time" id="start_time" name="start_time" class="start_time form-control form-control-sm " value="<?=$start_time;?>"  required/>
                  <label for="">Time</label>
                </div>

                <div class="col-sm-3">
                  <input type="date" id="end_date" name="end_date" class="end_date form-control form-control-sm " value="<?=$end_date;?>"  required/>
                  <label for="">End date</label>
                </div>

                <div class="col-sm-3">
                  <input type="time" id="end_time" name="end_time" class="end_time form-control form-control-sm " value="<?=$end_time;?>"  required/>
                  <label for="">Time</label>
                </div>

              </div>  
            </div> 
          </div>  
        </div>

          <!-- END HEADER DATA -->

          <!-- START DETAILS DATA -->
        <div class="d-inline p-4">
            <div class="row mb-3">
              <div class=" table-responsive">
               <table id="tbl-promo" class="metblentry-font table-bordered">
                  
                  <!-- TABLE HEADER -->

                  <thead class="text-center">
                    <th class="text-center"><i id="tbl-promo" class="text-white fas fa-sync"> </i></th>
                    <th  class="text-center">
                    <button type="button" class="btn btn-sm btn-success p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item_promo();"<?=$disable_ifapproved;?> >
                      <i class="bi bi-plus-lg"></i>
                      </button>
                    </th>
                      <th nowrap="nowrap">Voucher Code</th>
                      <th nowrap="nowrap">Amount</th>
                      <th nowrap="nowrap">Discount</th>

                    </tr>
                  </thead>


                      <!-- TABLE ROW INSERTION -->

                    <tr style="display: none;">
                        <td></td>
                        <td nowrap="nowrap">
                          <button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" onclick="$(this).closest('tr').remove();"><i class="bi bi-trash"></i></button>
                          <input class="mitemrid" type="hidden" value=""/>
                        </td>
                        <td nowrap="nowrap"><input type="text" size="35" onmouseover="javascript:__pack_totals();" class="form-control form-control-sm" ></td> <!--Voucher -->
                        <td nowrap="nowrap"><input type="text" size="35" onmouseover="javascript:__pack_totals();" class="form-control form-control-sm" ></td> <!--Amount -->
                        <td nowrap="nowrap"><input type="text" size="35" onmouseover="javascript:__pack_totals();" class="form-control form-control-sm" ></td> <!--Discount -->
                        
                        
                        
                        <!-- FOR RETRIEVAL OF EXISTING PROMO TRANSACTION NO. DATA -->
                          
                          <?php if(!empty($mtkn_fgpacktrr)): 
                          
                          $str = "
                          SELECT
                          bb.`voucher_code`
                          
                          FROM `gw_voucher_dt` bb
                          WHERE bb.`voucher_trxno` = '$mtkn_fgpacktrr'
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                          $nporecs++;
                            
                            
                          $voucher_code = $data['voucher_code'];
                            
                          ?>
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->
                              
                              <tr>
                              <td><?=$nporecs;?></td>
                              <td nowrap="nowrap">
                                <button type="button" class="btn btn-xs btn-danger" style="font-size:15px; padding: 2px 6px 2px 6px; " onclick="$(this).closest('tr').remove();"><i class="bi bi-x"></i></button>
                                <input class="mitemrid" type="hidden" value=""/>
                              </td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$voucher_code;?>"></td> <!--4 Price -->
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$voucher_amount;?>"></td> <!--4 Price -->
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$voucher_discount;?>"></td> <!--4 Price --> 
                              </tr>
                            <?php
                            }
                            ?>
                            <?php endif;?> 
                  </tbody>
                </table>
            </div>
          </div>
        </div> 

              <!-- END DETAILS DATA -->
              
              <div class="d-inline p-4">
                <div class="col-sm-4">
                  <?php if(!empty($mtkn_fgpacktrr)):?>
                    <button id="mbtn_mn_Save" type="button" style="background-color: #167F92; color: #FFF;" class="btn btn-dgreen btn-sm" disabled>Posted</button> 
                  <?php else:?>
                    <button id="mbtn_mn_Save" type="button" style="background-color: #167F92; color: #FFF;" class="btn btn-dgreen btn-sm">Save</button>   
                  <?php endif;?>
                  <button id="mbtn_mn_NTRX" type="button" class="btn btn-primary btn-sm">New Entry</button>
                </div>
              </div> <!-- end Save Records -->

      </div> <!-- end card-body -->
    </div>
  </div>
        

        <!-- HEADER AND FOR APPROVAL PAGE TAB -->

        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title">Records</h6>
              <div class="pt-2 bg-dgreen mt-2" style="background-color: #167F92;"> 
               <nav class="nav nav-pills flex-column flex-sm-row  gap-1 px-2 fw-bold">
                <a id="anchor-list" class="flex-sm-fill text-sm-center mytab-item active p-2  rounded-top"  aria-current="page" href="#"> <i class="bi bi-ui-checks"> </i> Records</a>
                <a id="anchor-items" class=" flex-sm-fill text-sm-center mytab-item  p-2 rounded-top " href="#"><i class="bi bi-ui-radios"></i> For Approval</a>
              </nav>
            </div>
            
            <!-- DISPLAY OF RECORDS AND APPROVAL -->

            <div id="packlist" class="text-center p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">
                <?php
                ?> 
            </div> 
          </div>
        </div>
      </div>
  </div> 
                <?php
                echo $mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
                echo $mylibzsys->memypreloader01('mepreloaderme');
                echo $mylibzsys->memsgbox1('memsgtestent','System Alert','...');
                ?>  
</main>   


<script type="text/javascript">
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
		  source: '<?= site_url(); ?>company-branch-ua',
		  focus: function() {
				// prevent value inserted on focus
				return false;
			  },
				search: function(oEvent, oUi) {
					var sValue = jQuery(oEvent.target).val();
					jQuery(this).autocomplete('option', 'source', '<?=site_url();?>company-branch-ua'); 
				},
			  select: function( event, ui ) {
				var terms = ui.item.value;
				var mtkn_comp = ui.item.mtkn_comp;
				var mtknr_rid = ui.item.mtknr_rid;
				var mtkn_brnch = ui.item.mtkn_brnch;
				jQuery('#branch_name').val(terms);
				jQuery('#branch_name').attr('data-mtknid',mtknr_rid);
				jQuery(this).autocomplete('search', jQuery.trim(terms));
				jQuery(this).prop('disabled',true);
				return false;                

				
			  }
			})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	}); //end branch_name 


        function __do_makeid(){
          var text = '';
          var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

          for( var i=0; i < 7; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));
          return text;
        }


        function my_add_line_item_promo() {  
          try {
            
            var rowCount = jQuery('#tbl-promo tr').length;
            var mid =  (rowCount + 1);
            var clonedRow = jQuery('#tbl-promo tr:eq(' + (rowCount - 1) + ')').clone(); 

            jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','voucher_code' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','voucher_amount' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','voucher_discount' + mid);
            
            jQuery('#tbl-promo tr').eq(rowCount - 1).before(clonedRow);
            jQuery(clonedRow).css({'display':''});
            var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
     
            jQuery('#' + xobjArtItem).focus();
            $( '#tbl-promo tr').each(function(i) { 
              $(this).find('td').eq(0).html(i);
            });
            
            __my_item_lookup();
            
          } catch(err) { 
            var mtxt = 'There was an error on this page.\\n';
            mtxt += 'Error description: ' + err.message;
            mtxt += '\\nClick OK to continue.';
            alert(mtxt);
            return false;
          }  //end try 
        } 
    
    
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
            source: '<?= site_url(); ?> get-promo-itemc',
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
                  var xobjitemvoucher_code = jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');/*discount*/


                  $('#' + xobjitemvoucher_code).val(ui.item.voucher_code);

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

        $('#tbl-promo').on('keydown', "input", function(e) { 
          switch(e.which) {
          case 37: // left 
          break;

          case 38: // up
          var nidx_rw = jQuery(this).parent().parent().index();
          var nidx_td = $(this).parent().index();
          if(nidx_td == 3) { 
          } else { 
            var clonedRow = jQuery('#tbl-promo  tr:eq(' + (nidx_rw) + ')').clone(); 
            var el_id = jQuery(clonedRow).find('td').eq(nidx_td).find('input[type=text]').eq(0).attr('id');
            $('#' + el_id).focus();
          }
          
          break;

          case 39: // right
          break;

          case 40: // down
          var nidx_rw = jQuery(this).parent().parent().index();
          var nidx_td = $(this).parent().index();
          if(nidx_td == 3) { 
          } else { 
            var clonedRow = jQuery('#tbl-promo  tr:eq(' + (nidx_rw + 2) + ')').clone(); 
            var el_id = jQuery(clonedRow).find('td').eq(nidx_td).find('input[type=text]').eq(0).attr('id');
                  //alert(nidx_rw + ':' + nidx_td + ':' + el_id);
                  $('#' + el_id).focus();
                }
                
                break;
          default: return; // exit this handler for other keys
          }
          //e.preventDefault(); // prevent the default action (scroll / move caret)
        });

      

        $("#mbtn_mn_Save").click(function(e){
       
        try { 
          //__mysys_apps.mepreloader('mepreloaderme',true);
          var mtkn_mntr = jQuery('#__hmpromotrxnoid').val();
          var voucher_trxno = jQuery('#voucher_trxno').val();
          var branch_code = jQuery('#branch_code').val();
          var branch_name = jQuery('#branch_name').val();
          var discount_value = jQuery('#discount_value').val();
          var start_date = jQuery('#start_date').val();
          var start_time = jQuery('#start_time').val();
          var end_date = jQuery('#end_date').val();
          var end_time = jQuery('#end_time').val();
          var rowCount1 = jQuery('#tbl-promo tr').length - 1;
          var mtkn_branch = jQuery('#branch_name').attr("data-id-brnch-name");
          var data_mtknid = jQuery('#branch_name').attr('data-mtknid');
          var adata1 = [];
          var adata2 = [];

          var mdata = '';
          var ninc = 0;

          for(aa = 1; aa < rowCount1; aa++) { 
            var clonedRow = jQuery('#tbl-promo tr:eq(' + aa + ')').clone(); 
            var voucher_code= jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
            var voucher_amount= jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
            var voucher_discount= jQuery(clonedRow).find('input[type=text]').eq(2).val(); 

            
            mdata = voucher_code + 'x|x' + voucher_amount + 'x|x' + voucher_discount;
            adata1.push(mdata);
            var mdat = jQuery(clonedRow).find('input[type=hidden]').eq(0).val();
            adata2.push(mdat);

          }  //end for

            var mparam = {
              mtkn_mntr:mtkn_mntr,
              voucher_trxno:voucher_trxno,
              branch_code:branch_code,
              branch_name:branch_name,
              discount_value:discount_value,
              start_date: start_date,
              start_time: start_time,
              end_date: end_date,
              end_time: end_time,
              mtkn_branch:data_mtknid,
             
              adata1: adata1,
              adata2: adata2
            };  

            console.log(mparam);
            
            $.ajax({ 
              type: "POST",
              url: '<?=site_url();?>me-voucher-save',
              context: document.body,
              data: eval(mparam),
              global: false,
              cache: false,
              success: function(data)  { 
                $(this).prop('disabled', false);
           // $.hideLoading();
           jQuery('#memsgtestent_bod').html(data);
           jQuery('#memsgtestent').modal('show');
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

      __mysys_apps.mepreloader('mepreloaderme',false);

      $('#anchor-list').on('click',function(){
        $('#anchor-list').addClass('active');
        $('#anchor-items').removeClass('active');
        var mtkn_whse = '';
        mypack_view_recs(mtkn_whse);

      });

      function mypack_view_recs(mtkn_whse){ 
        var ajaxRequest;

        ajaxRequest = jQuery.ajax({
          url: "<?=site_url();?>me-voucher-view",
          type: "post",
          data: {
            mtkn_whse: mtkn_whse
          }
        });

      // Deal with the results of the above ajax call
      __mysys_apps.mepreloader('mepreloaderme',true);
      ajaxRequest.done(function(response, textStatus, jqXHR) {
      jQuery('#packlist').html(response);
      __mysys_apps.mepreloader('mepreloaderme',false);
      });
      };

  $('#anchor-items').on('click',function(){
    $('#anchor-items').addClass('active');
    $('#anchor-list').removeClass('active');
    var mtkn_whse = '';
    mypack_view_appr(mtkn_whse);

  });

  function mypack_view_appr(mtkn_whse){ 
    var ajaxRequest;

    ajaxRequest = jQuery.ajax({
      url: "<?=site_url();?>me-voucher-view-appr",
      type: "post",
      data: {
        mtkn_whse: mtkn_whse
      }
    });

    // Deal with the results of the above ajax call
    __mysys_apps.mepreloader('mepreloaderme',true);
    ajaxRequest.done(function(response, textStatus, jqXHR) {
      jQuery('#packlist').html(response);
      __mysys_apps.mepreloader('mepreloaderme',false);
    });
  };


  

  
  
</script>