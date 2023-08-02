<?php

//VARIABLE DECLARATIONS

$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$mytrxfgpack = model('App\Models\MyPromoWholesaleModel');
$mydataz = model('App\Models\MyDatumModel');
$this->dbx = $mylibzdb->dbx;
$this->db_erp = $mydbname->medb(0);
$myusermod = model('App\Models\MyUserModel');
$this->myusermod = model('App\Models\MyUserModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$mtkn_txt_branch = '';
$branch_name = '';
$branch_code = '';
$mencd_date       = date("Y-m-d");  
$mtkn_fgpacktrr = $request->getVar('wholesale_trxno');
$wholesale_trxno = '';
$branch_code = '';
$recid = '';
$nporecs = 0;
$start_date = ''; 
$start_time = date('08:00');
$end_date = ''; 
$end_time = date('23:59');
$invalid_disc ='76';
$is_peso_discount = '';
$is_peso_discount_checked = '';
$is_percent_discount= '';
$is_percent_discount_checked = '';
$chkbox1 = '0';
$chkbox2 = '0';
$discount_value='';
$ART_DESC ='';
$ART_BARCODE1='';
$ART_CODE = '';
$ART_QUANTITY= '';
$is_approved='';
$date_approved='';
$is_disable='';
$quantity='';
$cb_value = '';
$ART_UPRICE='';
$txt_quantity = '';
$txt_discount = '';
$discount ='';
$disable_ifapproved = '';
$txt_promoname = $request->getVar('txt_promodesc');


//CHECK IF THERE'S A FORM OF RETRIEVAL

if(!empty($mtkn_fgpacktrr)) {
  $str = "
  SELECT
  aa.`wholesale_trxno`,
  aa.`branch_code`,
  bb.`BRNCH_NAME`,
  aa.`start_date`,
  aa.`start_time`,
  aa.`promo_name`,
  aa.`end_date`,
  aa.`end_time`,
  aa.`is_approved`,
  cc.`quantity`,
  cc.`discount_value`,

  if(cc.`is_peso_discount` = 1,1,2) p_is_peso_discount
  from `gw_wholesale_hd` aa 
  join `mst_companyBranch` bb
  on aa.`branch_code` = bb.`BRNCH_MBCODE`
  join `gw_wholesale_dt` cc
  on aa.`wholesale_trxno` = cc.`wholesale_trxno`
  where aa.`wholesale_trxno` = '$mtkn_fgpacktrr' 
  
  ";

  $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
  $rw = $q->getRowArray();
  $wholesale_trxno = $rw['wholesale_trxno'];
  $branch_code = $rw['branch_code'];
  $branch_name = $rw['BRNCH_NAME'];
  $start_date = $rw['start_date'];
  $start_time = $rw['start_time'];
  $end_date = $rw['end_date'];
  $end_time = $rw['end_time'];
  $quantity=$rw['quantity'];
  $discount=$rw['discount_value'];
  $is_peso_discount = ($rw['p_is_peso_discount'] == 1 ? 1 : 0);
  $is_peso_discount_checked = ($rw['p_is_peso_discount'] == 1 ? ' checked' : '');
  $is_percent_discount=  ($rw['p_is_peso_discount'] == 2 ? 2 : 0);
  $is_percent_discount_checked = ($rw['p_is_peso_discount'] == 2 ? ' checked' : '');
  $disable_ifapproved = ($rw['is_approved'] == 'Y' ? ' disabled ' : '');
  $txt_promoname = $rw['promo_name'];
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
        <li class="breadcrumb-item active">Promotion - WHOLESALE</li>
      </ol>
    </nav>
  </div> <!-- End Page Title -->

          <!-- START HEADER DATA -->

          <div class="row mb-3">
            <div class="col-lg-12">

              <div class="row mb-3">
                <label class="col-sm-3 form-label" for="wholesale_trxno">Wholesale:</label>
                <div class="col-sm-9">
                  <input type="text" id="wholesale_trxno" name="wholesale_trxno" placeholder="Wholesale code" style="background-color: #EAEAEA;" class="form-control form-control-sm" value="<?=$wholesale_trxno;?>" readonly/>
                  <input type="hidden" id="__hmpromotrxnoid" name="__hmpromotrxnoid" class="form-control form-control-sm" value="<?=$mtkn_fgpacktrr;?>"/>
                </div>
              </div> 

              <div class="row mb-3">
                <label class="col-sm-3 form-label" for="branch_code">Branch:</label>
                <div class="col-sm-9">
                  <input type="text" data-id-brnch-name="<?=$branch_name;?>" placeholder="Branch Name" id="branch_name" name="branch_name" class="branch_name form-control form-control-sm " value="<?=$branch_name;?>" required/>
                  <input type="hidden" data-id-brnch="<?=$branch_code;?>" placeholder="Branch Name" id="branch_code" name="branch_code" class="branch_code form-control form-control-sm " value="<?=$branch_code;?>" required/>     
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 form-label" for="txt_promodesc">Promo Name:</label>
                <div class="col-sm-9">
                  <input type="text" id="txt_promodesc" name="txt_promodesc" placeholder="Promo Description" class="form-control form-control-sm"readonly value="<?=$txt_promoname;?>" />
                </div>
              </div>  

              <div class="row mb-3">
                <label class="col-sm-3 form-label" for="txt_promodesc">Quantity and Discount</label>
                <div class="col-sm-3">
                  <input type="text" id="txt_quantity" name="txt_quantity" placeholder="Quantity" onkeypress="return isNumeric(event)"class="form-control form-control-sm" value="<?=$quantity?>" />
                </div>
                <div class="col-sm-3">
                  <input type="text" id="txt_discount" name="txt_discount" placeholder="Discount" onkeypress="return isNumeric(event)" class="form-control form-control-sm" value="<?=$discount;?>" />
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
                <div class="col-sm-3">
                  <div class="form-check form-switch">
                    <input class="is_peso_discount form-check-input" type="radio" name="cb" value="<?=$is_peso_discount;?>" id="is_peso_discount" onchange="selectCb(this)" <?=$is_peso_discount_checked;?>>
                    <label class="form-check-label" for="is_peso_discount" id="fixedlbl">
                      Fix
                    </label>
                  </div>

                  <div class="form-check form-switch">
                    <input class="is_percent_discount form-check-input" type="radio" name="cb" value="<?=$is_percent_discount;?>"  id="is_percent_discount" onchange="selectCb(this)" <?=$is_percent_discount_checked;?>>
                    <label class="form-check-label" for="is_percent_discount" id="discountlbl">
                      Percentage
                    </label>
                  </div>
                </div>
              </div> 
            </div>  
          </div>

          <!-- END HEADER DATA -->

          <!-- START DETAILS DATA -->
          <div class="row">
            <div class="row mb-3">
              <div class=" table-responsive">
                <table id="tbl_promo" class="mb-3 table-striped table-hover table-bordered table-sm" style="font-size: 0.8rem !important;">
                  
                  <!-- TABLE HEADER -->

                  <thead class="text-center">
                    <th class="text-center"><i id="tbl-promo" class="text-white fas fa-sync"> </i></th>
                    <th class="text-center">
                    <button type="button" class="btn btn-sm btn-success p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item_promo();"<?=$disable_ifapproved;?> >
                        <i class="bi bi-plus-lg"></i>
                      </button>
                    </th>
                      <th nowrap="nowrap">Store Code</th>
                      <th nowrap="nowrap">Description</th>
                      <th nowrap="nowrap">SRP</th>
                      <th nowrap="nowrap">Product Barcode</th>
                    </tr>
                  </thead>
                  <tbody id="gwpo-recs">
                    <?php
                    $nn=1;
                    $str = "
                    SELECT
                    a.*,
                    b.`ART_DESC`,
                    b.`ART_BARCODE1`,
                    b.`ART_UCOST`,
                    b.`ART_UPRICE`
                    FROM
                    {$this->db_erp}.`gw_wholesale_dt` a
                    JOIN
                    {$this->db_erp}.`mst_article` b
                    ON
                    a.`recid` = b.`recid`
                    JOIN
                    {$this->db_erp}.`gw_wholesale_hd` h
                    ON
                    a.`recid` = h.`recid`
                    WHERE
                    a.`recid` = '{$recid}'
                    ORDER BY 
                    a.`recid`
                    ";
                        //var_dump($str);
                        //die();
                        $q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                        
                    $bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
                    $on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\""; 
                    $rrec = $q->getResultArray();
                    foreach($rrec as $rdt){
                      $bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
                      $on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
                      $nporecs++;
              
                      $mid = $this->mylibzsys->random_string(10);
                      $metr_rec = 'metr_rec_' . $mid;
                      $discount_value = $data['discount_value'];
                      $discount_srp = $data['discount_srp'];
                      $ART_CODE=$data['ART_CODE'];
                      $ART_DESC = $data['ART_DESC'];
                      $ART_BARCODE1 = $data['ART_BARCODE1'];
                      $ART_UCOST = $data['ART_UCOST'];
                      $ART_UPRICE = $data['ART_UPRICE'];
                      
                      ?>
                      
                      <!-- TABLE VALUE ON SELECT OF ITEM -->

                      <tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
                        <td><?=$nporecs;?></td>
                        <td nowrap="nowrap">
                          <input class = "mitemrid" type="hidden" value="<?=$rdt['mtkn_artmtr'];?>"/>
                          <input type="hidden" value="<?=$rdt['mtkn_podttr'];?>"/>
                          
                        </td>
                        <td nowrap="nowrap"><input type="text" id="fld_mitemcode_<?=$nporecs;?>" class="form-control form-control-sm mitemcode" ></td>
                        <td nowrap="nowrap"><input type="text" id="mitemdesc_<?=$nporecs;?>" class="form-control form-control-sm"  ></td>
                        <td nowrap="nowrap"><input type="text" id="barcode<?=$nporecs;?>" class="form-control form-control-sm " ></td>
                    
                      <?php 
                      $nn++;
                      } 

                      $q->freeResult();
                      ?>
                  
                      <!-- TABLE ROW INSERTION -->

                      <tr style="display: none;">
                        <td></td>
                        <td nowrap="nowrap">
                        <button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" onclick="$(this).closest('tr').remove();"><i class="bi bi-trash"<?=$disable_ifapproved;?>></i></button>
                          <input class="mitemrid" type="hidden" value=""/>
                          <input type="hidden" value=""/>
                        </td>
                        <td nowrap="nowrap"><input type="text" size="20" class="form-control form-control-sm mitemcode" ></td> <!--0 ITEMC -->
                        <td nowrap="nowrap"><input type="text" size="20" class="form-control form-control-sm" style="background-color: #EAEAEA;"></td> <!--1 DESC -->
                        <td nowrap="nowrap"><input type="text" size="20" class="form-control form-control-sm" style="background-color: #EAEAEA;"></td> <!--1 DESC -->
                        <td nowrap="nowrap"><input type="text" size="20" class="form-control form-control-sm" style="background-color: #EAEAEA;"></td> <!--1 barcode -->
                        
                        <!-- FOR RETRIEVAL OF EXISTING PROMO TRANSACTION NO. DATA -->

                        <?php if(!empty($mtkn_fgpacktrr)): 
                          
                          $str = "
                          SELECT
                          cc.`quantity`,
                          cc.`discount_value`,
                          cc.`prod_barcode`,
                          dd.`ART_CODE`,
                          dd.`ART_DESC`,
                          dd.`ART_BARCODE1`,
                          dd.`ART_UPRICE`


                          FROM `gw_wholesale_dt` cc
                          JOIN `mst_article` dd
                          ON
                          cc. `prod_barcode`= dd.`ART_BARCODE1`
                          WHERE cc.`wholesale_trxno` = '$mtkn_fgpacktrr'
                          
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                            $nporecs++;

                            $ART_CODE = $data['ART_CODE'];
                            $ART_DESC = $data['ART_DESC'];
                            $quantity = $data['quantity'];
                            $discount_value = $data['discount_value'];
                            $ART_BARCODE1 = $data['ART_BARCODE1'];
                            $ART_UPRICE = $data['ART_UPRICE'];

                            
                            ?>
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->

                            <tr>
                              <td><?=$nporecs;?></td>
                              <td nowrap="nowrap">
                              <button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1"  onclick="$(this).closest('tr').remove();"<?=$disable_ifapproved;?>><i class="bi bi-x"></i></button>
                                <input class="mitemrid" type="hidden" value=""/>
                                <input type="hidden" value=""/>
                              </td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> mitemcode" value="<?=$ART_CODE;?>" ></td> <!--0 ITEMC -->
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$ART_DESC;?>" style="background-color: #EAEAEA;"></td> <!--1 DESC -->
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$ART_UPRICE;?>" style="background-color: #EAEAEA;"></td> <!--1 DESC -->
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$ART_BARCODE1;?>"  style="background-color: #EAEAEA;"></td> <!--1 barcode -->
                              
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
    
    function isNumeric(event) {
        var keyCode = event.which ? event.which : event.keyCode;
        var isValid = (keyCode >= 48 && keyCode <= 57) || keyCode === 8 || keyCode === 9;
        return isValid;
    }
  

    __my_item_lookup();
    
    __pack_totals();
    
    //PARA SA TIMER NG TAMT TOTALS
    var tid = setInterval(myTamtTimer, 30000);
    function myTamtTimer() {
      __pack_totals();
      // do some stuff...
      // no need to recall the function (it's an interval, it'll loop forever)
    }

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


    jQuery('.company_name')
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
          source: '<?= site_url(); ?>getcompany_list',
          focus: function() {
                // prevent value inserted on focus
                return false;
              },
              select: function( event, ui ) {
                var terms = ui.item.value;
                
                jQuery('#company_name').val(terms);

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
            window.location = '<?=site_url();?>me-wholesale-vw';
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
            
            var rowCount = jQuery('#tbl_promo tr').length;
            var mid =  (rowCount + 1);
            var clonedRow = jQuery('#tbl_promo tr:eq(' + (rowCount - 1) + ')').clone(); 

            
            jQuery(clonedRow).find('input[type=text]').val('');
            jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','ART_CODE' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','ART_DESC' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','amount' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','discount_value' + mid);
            jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','ART_BARCODE1' + mid);
            
            
            
            jQuery('#tbl_promo tr').eq(rowCount - 1).after(clonedRow);
            jQuery(clonedRow).css({'display':''});
            var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
            jQuery('#' + xobjArtItem).focus();
            $( '#tbl_promo tr').each(function(i) { 
                $(this).find('td').eq(0).html(i -1 );
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
                
    
    function __my_item_lookup() { 
		jQuery('.mitemcode' ) 
		.unbind('keydown')
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
			source: '<?= site_url(); ?>get-promo-itemc',
			focus: function() {
				  // prevent value inserted on focus
				  return false;
				},
			search: function(oEvent, oUi) { 
				var sValue = jQuery(oEvent.target).val();
				
				var mtkn_branch = jQuery('#branch_name').attr('data-mtknid');
				var branch_name = jQuery('#branch_name').val();
				
				if(jQuery.trim(branch_name) == '') {
					alert('Branch should be selected first!!!');
					return false;
				}
				
				jQuery(this).autocomplete('option', 'source', '<?=site_url();?>get-promo-itemc/?mtkn_branch=' + mtkn_branch + '&branch_name=' + branch_name);
			},                
				select: function( event, ui ) {
					var terms = ui.item.value;
					jQuery(this).attr('alt', jQuery.trim(ui.item.value));
					jQuery(this).attr('title', jQuery.trim(ui.item.value));
					var ndisc = jQuery('#txt_promodiscval').val();
					this.value = ui.item.value;
					// var clonedRow = jQuery(this).parent().parent().clone();
					var clonedRow = jQuery(this).closest('tr');
					var indexRow = jQuery(this).parent().parent().index();
					var xobjitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).attr('id');  /*DESC*/
					var xobjitembcode = jQuery(clonedRow).find('input[type=text]').eq(3).attr('id');  /*BCODE*/
					var xobjitemprice = jQuery(clonedRow).find('input[type=text]').eq(2).attr('id');  /*PRICE*/
					jQuery(this).attr('data-mtnkattr',ui.item.mtkn_rid);
					jQuery('#' + xobjitemdesc).val(ui.item._DESC);
					jQuery('#' + xobjitembcode).val(ui.item._BARCODE);
					jQuery('#' + xobjitemprice).val(ui.item._UPRICE);
					return false;
				}
			})
		.click(function() { 
			//jQuery(this).keydown(); 
			var terms = this.value;
			//jQuery(this).autocomplete('search', '');
			jQuery(this).autocomplete('search', jQuery.trim(terms));
		});  
	}  //end __my_item_lookup

        
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
              var price = jQuery(clonedRow).find('input[type=text]').eq(2).val();
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
          var wholesale_trxno = jQuery('#wholesale_trxno').val();
          var branch_code = jQuery('#branch_code').val();
          var branch_name = jQuery('#branch_name').val();
          var start_date = jQuery('#start_date').val();
          var start_time = jQuery('#start_time').val();
          var end_date = jQuery('#end_date').val();
          var end_time = jQuery('#end_time').val();
          var is_percent_discount = jQuery('#is_percent_discount').prop('checked');;
          var cb_fix_percent_discount_value = (is_percent_discount) ? (1) : (0);
          var is_peso_discount = jQuery('#is_peso_discount').prop('checked');
          var cb_fix_value = (is_peso_discount) ? (1) : (0);
          var is_peso_discount_checked = jQuery('#is_peso_discount').val();
          var mtkn_branch = jQuery('#branch_name').attr("data-mtknid");
          var rowCount1 = jQuery('#tbl-promo tr').length - 1;
          var adata1 = [];
          var adata2 = [];
          var txt_quantity = jQuery('#txt_quantity').val();
          var txt_discount = jQuery('#txt_discount').val();
          var mdata = '';
          var ninc = 0;
          var promo_name = jQuery('#txt_promodesc').val();

          for(aa = 1; aa < rowCount1; aa++) { 
            var clonedRow = jQuery('#tbl-promo tr:eq(' + aa + ')').clone(); 
            var ART_CODE = jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
            var ART_DESC = jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
            var ART_UPRICE = jQuery(clonedRow).find('input[type=text]').eq(2).val(); 
            var ART_QUANTITY = jQuery(clonedRow).find('input[type=text]').eq(3).val();
            var discount_value = jQuery(clonedRow).find('input[type=text]').eq(4).val(); 
            var ART_BARCODE1 = jQuery(clonedRow).find('input[type=text]').eq(5).val(); 
            
            
            
            
            mdata = ART_CODE + 'x|x' + ART_DESC + 'x|x' + ART_UPRICE + 'x|x' + ART_QUANTITY + 'x|x' + discount_value + 'x|x' + ART_BARCODE1;
            adata1.push(mdata);
            var mdat = jQuery(clonedRow).find('input[type=hidden]').eq(0).val();
            adata2.push(mdat);

            }  //end for

            var mparam = {
              mtkn_mntr:mtkn_mntr,
              wholesale_trxno:wholesale_trxno,
              branch_code:branch_code,
              branch_name:branch_name,
              start_date: start_date,
              start_time: start_time,
              end_date: end_date,
              end_time: end_time,
              cb_fix_percent_discount_value:cb_fix_percent_discount_value,
              cb_fix_value:cb_fix_value,
              mtkn_branch:mtkn_branch,
              txt_quantity:txt_quantity,
              txt_discount:txt_discount,
              promo_name:promo_name,
              adata1: adata1,
              adata2: adata2
            };  

            console.log(mparam);
            
            $.ajax({ 
              type: "POST",
              url: '<?=site_url();?>me-wholesale-save',
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
          url: "<?=site_url();?>me-wholesale-view",
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
      url: "<?=site_url();?>me-wholesale-view-appr",
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
    var is_peso_discount = jQuery('#is_peso_discount').prop('checked');
    var cb_value_fix = (is_peso_discount) ? (1) : (0);
    var is_percent_discount = jQuery('#is_percent_discount').prop('checked');;
    var cb_value_percent = (is_percent_discount) ? (1) : (0);
    checkboxes.forEach(cb =>  {
      
    })
    

  }

  $(document).ready(function() {
		$('#is_peso_discount').click(function(){
			var discount = $('#txt_discount').val();
            var quantity = $('#txt_quantity').val();
			var is_peso_discount = jQuery('#is_peso_discount').prop('checked');;
			var is_peso_discount_val = (is_peso_discount) ? (1) : (0);
			var is_percent_discount = jQuery('#is_percent_discount').prop('checked');
			var is_percent_discount_val = (is_percent_discount) ? (1) : (0);

			if (is_percent_discount_val == 1){
				var promo_name =  'Avail '+quantity +' Item Get Disc ' +discount +'%' ;
			$('#txt_promodesc').val(promo_name);
			}
			if (is_peso_discount_val == 1){
			var promo_name =  'Avail '+quantity +' Item Get Disc '+ discount +' peso OFF' ;
			$('#txt_promodesc').val(promo_name);
			}
            console.log(is_peso_discount_val+'off');
		})
		$('#is_percent_discount').click(function(){
            var discount = $('#txt_discount').val();
            var quantity = $('#txt_quantity').val();
			var is_peso_discount = jQuery('#is_peso_discount').prop('checked');;
			var is_peso_discount_val = (is_peso_discount) ? (1) : (0);
			var is_percent_discount = jQuery('#is_percent_discount').prop('checked');
			var is_percent_discount_val = (is_percent_discount) ? (1) : (0);

            if (is_percent_discount_val == 1){
				var promo_name =  'Avail '+quantity +' Item Get Disc ' +discount + '%' ;
			$('#txt_promodesc').val(promo_name);
			}
			if (is_peso_discount_val == 1){
			var promo_name =  'Avail '+quantity +' Item Get Disc '+ discount +' peso OFF' ;
			$('#txt_promodesc').val(promo_name);
			}
			console.log(is_percent_discount_val+'discount');
		})
		$('#txt_quantity').change(function(){
            var discount = $('#txt_discount').val();
            var quantity = $('#txt_quantity').val();
			var is_peso_discount = jQuery('#is_peso_discount').prop('checked');;
			var is_peso_discount_val = (is_peso_discount) ? (1) : (0);
			var is_percent_discount = jQuery('#is_percent_discount').prop('checked');
			var is_percent_discount_val = (is_percent_discount) ? (1) : (0);

            if (is_percent_discount_val == 1){
				var promo_name =  'Avail '+quantity +' Item Get Disc ' +discount +'%' ;
			$('#txt_promodesc').val(promo_name);
			}
			if (is_peso_discount_val == 1){
			var promo_name =  'Avail '+quantity +' Item Get Disc '+ discount +' peso OFF' ;
			$('#txt_promodesc').val(promo_name);
			}
			
		})
        $('#txt_discount').change(function(){
            var discount = $('#txt_discount').val();
            var quantity = $('#txt_quantity').val();
			var is_peso_discount = jQuery('#is_peso_discount').prop('checked');;
			var is_peso_discount_val = (is_peso_discount) ? (1) : (0);
			var is_percent_discount = jQuery('#is_percent_discount').prop('checked');
			var is_percent_discount_val = (is_percent_discount) ? (1) : (0);

            if (is_percent_discount_val == "1"){
				var promo_name =  'Avail '+quantity +' Item Get Disc ' +discount + '%' ;
			$('#txt_promodesc').val(promo_name);
			}
			if (is_peso_discount_val == "1"){
			var promo_name =  'Avail '+quantity +' Item Get Disc '+ discount +' peso OFF' ;
			$('#txt_promodesc').val(promo_name);
			}
			
		})
	})
  
  
</script>












