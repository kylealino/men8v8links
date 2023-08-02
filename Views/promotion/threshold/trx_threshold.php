<?php

//VARIABLE DECLARATIONS

$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$mytrxfgpack = model('App\Models\MyPromoSalesModel');
$mydataz = model('App\Models\MyDatumModel');
$this->dbx = $mylibzdb->dbx;
$this->db_erp = $mydbname->medb(0);
$myusermod = model('App\Models\MyUserModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$mtkn_txt_branch = '';
$branch_code = '';
$branch_name = '';
$mencd_date = date("Y-m-d");  
$recid = '';
$nporecs = 0;
$mtkn_fgpacktrr = $request->getVar('threshold_trxno');
$threshold_trxno = '';
$start_date = ''; 
$start_time = date('08:00');
$end_date = ''; 
$end_time = date('23:59');
$invalid_disc ='76';
$discount_value='';
$ART_DESC ='';
$ART_BARCODE1='';
$ART_CODE = '';

//CHECK IF THERE'S A FORM OF RETRIEVAL

if(!empty($mtkn_fgpacktrr)) {
  $str = "
  SELECT 
  aa.`threshold_trxno`,
  aa.`branch_code`,
  aa.`branch_name`,
  aa.`start_date`,
  aa.`start_time`,
  aa.`end_date`,
  aa.`end_time`
  FROM `gw_threshold_hd` aa
  WHERE aa.`threshold_trxno` = '$mtkn_fgpacktrr'


  ";

  $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
  $rw = $q->getRowArray();
  $threshold_trxno = $rw['threshold_trxno'];
  $branch_code = $rw['branch_code'];
  $branch_name = $rw['branch_name'];
  $start_date = $rw['start_date'];
  $start_time = $rw['start_time'];
  $end_date = $rw['end_date'];
  $end_time = $rw['end_time'];

}


?>
<main id="main">
  <div class="row mb-3 me-form-font">
    <span id="__me_numerate_wshe__" ></span>
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-pencil-square px-10"></i><span>  </span>Create Entry</h6>

          <!-- START HEADER DATA -->

          <div class="row mb-3">
            <div class="col-lg-12">

              <div class="row mb-3">
                <label class="col-sm-3 form-label" for="threshold_trxno">Promo Threshold Code:</label>
                <div class="col-sm-9">
                  <input type="text" id="threshold_trxno" name="threshold_trxno" placeholder="Promo Threshold Code" style="background-color: #EAEAEA;" class="form-control form-control-sm" value="<?=$threshold_trxno;?>" readonly/>
                  <input type="hidden" id="threshold_trxno" name="__hmpromotrxnoid" class="form-control form-control-sm" value="<?=$mtkn_fgpacktrr;?>"/>
                </div>
              </div> 

              <div class="row mb-3">
                <label class="col-sm-3 form-label" for="branch_code">Branch:</label>
                <div class="col-sm-9">
                  <input type="text" data-id-brnch-name="<?=$branch_name;?>" placeholder="Branch Code" id="branch_name" name="branch_name" class="branch_name form-control form-control-sm " value="<?=$branch_name;?>" required/>
                  <input type="hidden" data-id-brnch="<?=$branch_code;?>" placeholder="Branch Name" id="branch_code" name="branch_code" class="branch_code form-control form-control-sm " value="<?=$branch_code;?>" required/>     
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
            <div class="col-md-13">
              <div class=" table-responsive">
                <table class="table table-bordered table-hover table-sm text-center" id="tbl-promo">
                  
                  <!-- TABLE HEADER -->

                  <thead class="thead-light">
                    <tr>
                      <th nowrap="nowrap"></th>
                      <th nowrap="nowrap">
                        <button type="button" class="btn btn-dgreen btn-sm" onclick="javascript:my_add_line_item_promo();" >
                          <i class="bi bi-plus"></i>
                        </button>
                      </th>
                      <th nowrap="nowrap">Item Code</th>
                      <th nowrap="nowrap">Threshold Description</th>
                      <th nowrap="nowrap">Amount</th>
                      <th nowrap="nowrap">Discount</th>
                      <th nowrap="nowrap">Product Barcode</th>

                    </tr>
                  </thead>
                  <tbody id="gwpo-recs">

                      <!-- TABLE ROW INSERTION -->

                      <tr style="display: none;">
                        <td></td>
                        <td nowrap="nowrap">
                          <button type="button" class="btn btn-xs btn-danger" style="font-size:15px; padding: 2px 6px 2px 6px; " onclick="$(this).closest('tr').remove();"><i class="bi bi-x"></i></button>
                          <input class="mitemrid" type="hidden" value=""/>
                          <input type="hidden" value=""/>
                        </td>
                        <td nowrap="nowrap"><input type="text" class="form-control form-control-sm mitemcode" ></td> <!--0 ITEMC -->
                        <td nowrap="nowrap"><input type="text" class="form-control form-control-sm" style="background-color: #EAEAEA;"></td> <!--1 DESC -->
                        <td nowrap="nowrap"><input type="text" onmouseover="javascript:__pack_totals();" class="form-control form-control-sm" ></td> <!--4 disc -->
                        <td nowrap="nowrap"><input type="text" onmouseover="javascript:__pack_totals();" class="form-control form-control-sm" ></td> <!--4 disc -->
                        <td nowrap="nowrap"><input type="text" class="form-control form-control-sm" style="background-color: #EAEAEA;"></td> <!--1 barcode -->
                        
                        <!-- FOR RETRIEVAL OF EXISTING PROMO TRANSACTION NO. DATA -->
                          
                          <?php if(!empty($mtkn_fgpacktrr)): 
                          
                          $str = "
                          SELECT
                          bb.`amount`,
                          bb.`discount`,
                          bb.`prod_barcode`,
                          cc.`ART_CODE`,
                          cc.`ART_DESC`,
                          cc.`ART_BARCODE1`
                          
                          FROM `gw_threshold_dt` bb
                          JOIN `mst_article` cc
                          ON 
                          bb.`prod_barcode` = cc.`ART_BARCODE1`
                          WHERE bb.`threshold_trxno` = '$mtkn_fgpacktrr'

                          
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                            
                            
                            $ART_CODE = $data['ART_CODE'];
                            $ART_DESC = $data['ART_DESC'];
                            $amount = $data['amount'];
                            $discount = $data['discount'];
                            $prod_barcode = $data['ART_BARCODE1'];
                            
                            
                            ?>
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->
                              
                              <tr>
                              <td></td>
                              <td nowrap="nowrap">
                                <button type="button" class="btn btn-xs btn-danger" style="font-size:15px; padding: 2px 6px 2px 6px; " onclick="$(this).closest('tr').remove();"><i class="bi bi-x"></i></button>
                                <input class="mitemrid" type="hidden" value=""/>
                                <input type="hidden" value=""/>
                              </td>
                              
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm mitemcode" value="<?=$ART_CODE;?>" ></td> <!--0 ITEMC -->
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm" value="<?=$ART_DESC;?>" style="background-color: #EAEAEA;"></td> <!--1 DESC -->
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm" value="<?=$amount;?>"></td> <!--4 Price -->
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm" value="<?=$discount;?>"></td> <!--4 Price -->
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm" value="<?=$prod_barcode;?>"  style="background-color: #EAEAEA;"></td> <!--1 barcode -->
                              
                              
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
    
    __my_item_lookup();
    
    __pack_totals();
    
    //PARA SA TIMER NG TAMT TOTALS
    var tid = setInterval(myTamtTimer, 30000);
    function myTamtTimer() {
      __pack_totals();
      // do some stuff...
      // no need to recall the function (it's an interval, it'll loop forever)
    }

    jQuery('.branch_name')
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
          source: '<?= site_url(); ?>get-branch-list',
          focus: function() {
                // prevent value inserted on focus
                return false;
              },
              select: function( event, ui ) {
                var terms = ui.item.value;
                
                jQuery('#branch_name').val(terms);
                jQuery('#branch_name').attr("data-id-brnch-name",ui.item.mtkn_rid);
                jQuery('#branch_code').val(ui.item.BRNCH_OCODE2);

                jQuery(this).autocomplete('search', jQuery.trim(terms));

                return false;

                
              }
            })
        .click(function() {
          var terms = this.value;
          jQuery(this).autocomplete('search', jQuery.trim(terms));
          
    }); //whse


    
        
        $('#mbtn_mn_NTRX').click(function() { 
          var userselection = confirm("Are you sure you want to new transaction?");
          if (userselection == true){
            window.location = '<?=site_url();?>me-threshold-vw';
          }
          else{
            $.hideLoading();
            return false;
          } 
        });

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

            jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','ART_CODE' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','ART_DESC' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','amount' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','discount_value' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','ART_BARCODE1' + mid);
            
            
            
            jQuery('#tbl-promo tr').eq(rowCount - 1).before(clonedRow);
            jQuery(clonedRow).css({'display':''});
            var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
            jQuery('#' + xobjArtItem).focus();
            $( '#tbl-promo tr').each(function(i) { 
              $(this).find('td').eq(0).html(i);
            });
            
            __my_item_lookup();
            __pack_totals();
            
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
                  var xobjitemART_CODE = jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id'); //ID
                  var xobjitemART_DESC = jQuery(clonedRow).find('input[type=text]').eq(1).attr('id');/*DESC*/
                  var xobjitemamount = jQuery(clonedRow).find('input[type=text]').eq(2).attr('id');/*price*/
                  var xobjitemdiscount_value = jQuery(clonedRow).find('input[type=text]').eq(3).attr('id');/*discount*/
                  var xobjitemART_BARCODE1 = jQuery(clonedRow).find('input[type=text]').eq(4).attr('id');/*BCODE*/

                  
                  $('#' + xobjitemART_CODE).val(ui.item.ART_CODE);
                  $('#' + xobjitemART_DESC).val(ui.item.ART_DESC);
                  $('#' + xobjitemamount).val(ui.item.amount);
                  $('#' + xobjitemdiscount_value).val(ui.item.discount_value);
                  $('#' + xobjitemART_BARCODE1).val(ui.item.ART_BARCODE1);

                  
                  

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
            var rowCount1 = jQuery('#tbl-promo tr').length - 1;
            var adata1 = [];
            var adata2 = [];
            var mdata = '';
            var ninc = 0;
            var nTAmount = 0;
            var nTQty = 0;
            for(aa = 1; aa < rowCount1; aa++) { 
              var clonedRow = jQuery('#tbl-promo tr:eq(' + aa + ')').clone(); 
              var qty = jQuery(clonedRow).find('input[type=text]').eq().val();
              var price = jQuery(clonedRow).find('input[type=text]').eq().val();
              var xTAmntId = jQuery(clonedRow).find('input[type=text]').eq().attr('id');

              var nqty = 0;
              var nprice = 0;
              
              if($.trim(qty) == '') { 
                nqty = 0;
              } else { 
                
                nqty = qty;
              }
              if($.trim(price) == '') { 
                      nprice = 0;//COST
                    } else { 
                      nprice =price;
                    }

                    if($.trim(xTAmntId) == '') { 
                      nprice2 = 0;
                    } else { 
                      nprice2 = xTAmntId;
                    }
                    
                    var ntqty = parseFloat(nqty);
                    if($('#' + xTAmntId).val()==''){
                      var ntprice = parseFloat(nprice / ntqty);
                    }
                    else{

                      var ntprice = parseFloat(nprice / ntqty);
                    }

                    if(!isNaN(ntprice) || ntprice > 0) { 
                      $('#' + xTAmntId).val(ntprice.toFixed(2));
                    // console.log(xTAmntId);
                  }
                  

                  

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
          var threshold_trxno = jQuery('#threshold_trxno').val();
          var branch_code = jQuery('#branch_code').val();
          var branch_name = jQuery('#branch_name').val();
          var start_date = jQuery('#start_date').val();
          var start_time = jQuery('#start_time').val();
          var end_date = jQuery('#end_date').val();
          var end_time = jQuery('#end_time').val();
          var rowCount1 = jQuery('#tbl-promo tr').length - 1;
          var mtkn_branch = jQuery('#branch_name').attr("data-id-brnch-name");
          var adata1 = [];
          var adata2 = [];

          var mdata = '';
          var ninc = 0;

          for(aa = 1; aa < rowCount1; aa++) { 
            var clonedRow = jQuery('#tbl-promo tr:eq(' + aa + ')').clone(); 
            var ART_CODE = jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
            var ART_DESC = jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
            var amount = jQuery(clonedRow).find('input[type=text]').eq(2).val();
            var discount_value = jQuery(clonedRow).find('input[type=text]').eq(3).val(); 
            var ART_BARCODE1 = jQuery(clonedRow).find('input[type=text]').eq(4).val();
            var mitemc_tkn = jQuery(clonedRow).find('input[type=hidden]').eq(5).val(); 
            
            
            
            mdata = ART_CODE + 'x|x' + ART_DESC + 'x|x' + amount + 'x|x' + discount_value + 'x|x' + ART_BARCODE1 + 'x|x' + mitemc_tkn;
            adata1.push(mdata);
            var mdat = jQuery(clonedRow).find('input[type=hidden]').eq(0).val();
            adata2.push(mdat);

            }  //end for

            var mparam = {
              mtkn_mntr:mtkn_mntr,
              threshold_trxno:threshold_trxno,
              branch_code:branch_code,
              branch_name:branch_name,
              start_date: start_date,
              start_time: start_time,
              end_date: end_date,
              end_time: end_time,

              mtkn_branch:mtkn_branch,
              adata1: adata1,
              adata2: adata2
            };  

            console.log(mparam);
            
            $.ajax({ 
              type: "POST",
              url: '<?=site_url();?>me-threshold-save',
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
          url: "<?=site_url();?>me-threshold-view",
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
      url: "<?=site_url();?>me-threshold-view-appr",
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

  let selectedtxt = 'sampletext';
  const txt = document.getElementById('output')
  const selectCb = (cbElement) => {
    const checkboxes = document.getElementsByName('cb')
    var lblfix = document.getElementsByName('fixedlbl');
    var cb_value_fix = (is_fixed_price) ? (1) : (0);
    checkboxes.forEach(cb =>  {
      
    })
    

  }

  
  
</script>














