<?php

//VARIABLE DECLARATIONS
$this->myusermod = model('App\Models\MyUserModel');
$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$mytrxfgpack = model('App\Models\MyPromoBuy1take1Model');
$mydataz = model('App\Models\MyDatumModel');
$this->dbx = $mylibzdb->dbx;
$this->db_erp = $mydbname->medb(0);
$branch_code = '';
$mtkn_txt_branch = '';
$branch_name = '';
$mencd_date       = date("Y-m-d");  
$buy1take1_trxno = $request->getVar('buy1take1_trxno');
$mtkn_trxno = $request->getVar('mtkn_trxno');
$txt_promotrxno = '';
$recid = '';
$nporecs = 0;
$txt_buy1take1trxno = '';
$promo_name="";
$start_date = ''; 
$start_time = date('08:00');
$end_date = ''; 
$end_time = date('23:59');
$invalid_disc ='76';
$is_fixed_price ='';
$is_fixed_price_checked = '';
$is_discount_percent= '';
$is_discount_percent_checked = '';

$chkbox1 = '0';
$chkbox2 = '0';
$discount_value='';
$ART_DESC ='';
$ART_BARCODE1='';
$ART_UCOST='';
$cb_value = '';
$ART_UPRICE='';
$ART_CODE = '';
$txt_promoname = $request->getVar('txt_promodesc');

//CHECK IF THERE'S A FORM OF RETRIEVAL
$disable_ifapproved = '';

if(!empty($buy1take1_trxno)) {
  $str = "
  select aa.`buy1take1_trxno`,
  aa.`branch_code`,
  bb.`BRNCH_NAME`,
  aa.`start_date`,
  aa.`start_time`,
  aa.`end_date`,
  aa.`end_time`,
  aa.is_approved,
  if(aa.`is_fixed_price` = 1,1,2) p_is_fixed_price 
  from `trx_pos_promo_buy1take1_hd` aa 
  join `mst_companyBranch` bb
  on aa.`branch_code` = bb.`BRNCH_OCODE2`
  join `trx_pos_promo_buy1take1_dt` cc
  on aa.`buy1take1_trxno` = cc.`buy1take1_trxno`
  where aa.`buy1take1_trxno` = '$buy1take1_trxno' 
  ";

  $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
  $rw = $q->getRowArray();
  //$mtkn_trxno = $rw['mtkn_trxno'];

  $txt_buy1take1trxno = $rw['buy1take1_trxno'];
  $branch_code = $rw['branch_code'];
  $start_date = $rw['start_date'];
  $start_time = $rw['start_time'];
  $end_date = $rw['end_date'];
  $end_time = $rw['end_time'];
  $branch_name = $rw['BRNCH_NAME'];
  $is_fixed_price = ($rw['p_is_fixed_price'] == 1 ? 1 : 0);
  $is_fixed_price_checked = ($rw['p_is_fixed_price'] == 1 ? ' checked' : '');
  $is_discount_percent=  ($rw['p_is_fixed_price'] == 2 ? 2 : 0);
  $is_discount_percent_checked = ($rw['p_is_fixed_price'] == 2 ? ' checked' : '');
  $disable_ifapproved = ($rw['is_approved'] == 'Y' ? ' disabled ' : '');
  
}


echo view('templates/meheader01');
?>
<main id="main" class="main">
<div class="card">
  <div class="card-body">
    <br>
  <div class="pagetitle">
    <h1>Promotion</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Sales</li>
        <li class="breadcrumb-item active">Promotion - BUY 1 TAKE 1</li>
      </ol>
    </nav>
  </div> <!-- End Page Title -->

          <!-- START HEADER DATA -->

          <div class="row mb-3">
            <div class="col-lg-12">

              <div class="row mb-3">
                <label class="col-sm-3 form-label" for="txt_buy1take1trxno">Buy One Take One Code:</label>
                <div class="col-sm-9">
                  <input type="text" id="txt_buy1take1trxno" name="txt_buy1take1trxno" placeholder="Buy One Take One Code" style="background-color: #EAEAEA;" class="form-control form-control-sm" value="<?=$txt_buy1take1trxno;?>" readonly/>
                  <input type="hidden" id="__hmpromotrxnoid" name="__hmpromotrxnoid" class="form-control form-control-sm" value="<?=$buy1take1_trxno;?>"/>
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

          <!-- END HEADER DATA -->

          <!-- START DETAILS DATA -->
          <div class="row">
            
            <div class=" table-responsive">
              <table id="tbl-promo" class="metblentry-font table-bordered">
                
                <!-- TABLE HEADER -->

                <thead class="text-center">
                  <th class="text-center"><i id="tbl-promo" class="text-white fas fa-sync"> </i></th>
                  <th class="text-center">
                    <button type="button" class="btn btn-sm btn-success p-1 pb-0 mebtnpt1" onclick="javascript:my_add_line_item_promo();"<?=$disable_ifapproved;?> >
                      <i class="bi bi-plus-lg"></i>
                    </button>
                    </th>
                      <th nowrap="nowrap">Product Buy Code</th>
                      <th nowrap="nowrap">Product Buy Description</th>
                      <th nowrap="nowrap">Product Buy Barcode</th>
                      <th nowrap="nowrap">Product Take Code</th>
                      <th nowrap="nowrap">Product Take Description</th>
                      <th nowrap="nowrap">Product Take Barcode</th>
                      <th nowrap="nowrap">Quantity</th>
                      <th nowrap="nowrap">Take</th>
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
                    {$this->db_erp}.`gw_promo_dt` a
                    JOIN
                    {$this->db_erp}.`mst_article` b
                    ON
                    a.`mat_rid` = b.`recid`
                    JOIN
                    {$this->db_erp}.`gw_promo_hd` h
                    ON
                    a.`promohd_rid` = h.`recid`
                    WHERE
                    a.`promohd_rid` = '{$recid}'
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
                        <td nowrap="nowrap"><input type="text" id="mitemdesc_<?=$nporecs;?>"  class="form-control form-control-sm"  ></td>
                        <td nowrap="nowrap"><input type="text" id="mitembcode_<?=$nporecs;?>" class="form-control form-control-sm mitemcode2" ></td>
                        <td nowrap="nowrap"><input type="text" id="mitembcode_<?=$nporecs;?>" class="form-control form-control-sm " ></td>
                        <td nowrap="nowrap"><input type="text" id="mitemdisc_<?=$nporecs;?>"  class=" form-control form-control-sm" ></td>
                        <td nowrap="nowrap"><input type="text" id="mitemprice_<?=$nporecs;?>"  class="form-control form-control-sm"></td>
                        <td nowrap="nowrap"><input type="text" id="mitemdiscsrp_<?=$nporecs;?>" class="form-control form-control-sm"></td>
                      </tr>
                      <?php 
                      $nn++;
                      } 

                      $q->freeResult();
                      ?>
                      <!-- FOR RETRIEVAL OF EXISTING PROMO TRANSACTION NO. DATA -->

                      <?php if(!empty($buy1take1_trxno)): 
                          
                          $str = "
                          select
                          aa.`buy1take1_trxno`,
                          cc.`qty`,
                          cc.`take`,
                          cc.`prod_barcode_buy`,
                          cc.`prod_barcode_take`,
                          dd.`ART_CODE`,
                          dd.`ART_DESC`,
                          dd.`ART_BARCODE1`


                          from `trx_pos_promo_buy1take1_hd` aa 
                          join `mst_companyBranch` bb
                          on aa.`branch_code` = bb.`BRNCH_OCODE2`
                          join `trx_pos_promo_buy1take1_dt` cc
                          on aa.`buy1take1_trxno` = cc.`buy1take1_trxno`
                          join `mst_article` dd
                          on 
                          cc.`prod_barcode_buy` = dd.`ART_BARCODE1`
                          where aa.`buy1take1_trxno` = '$buy1take1_trxno' 
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                            $nporecs++;

                            $qty = $data['qty'];
                            $take = $data['take'];
                            $prod_barcode_take = $data['prod_barcode_take'];
                            $prod_barcode_buy = $data['prod_barcode_buy'];
                            $ART_CODE=$data['ART_CODE'];
                            $ART_DESC = $data['ART_DESC'];
                            $ART_BARCODE1 = $data['ART_BARCODE1'];
                           
                            ?>
                            
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->

                            <tr>
                              <td><?=$nporecs;?></td>
                              <td nowrap="nowrap">
                                <button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1"  onclick="$(this).closest('tr').remove();"<?=$disable_ifapproved;?>><i class="bi bi-x"></i></button>
                                <input class="mitemrid" type="hidden" value=""/>
                                <input type="hidden" value=""/>
                              </td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> mitemcode" value="<?=$ART_CODE;?>"></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$ART_DESC;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$prod_barcode_buy;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> mitemcode2" value="<?=$ART_CODE;?>"></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$ART_DESC;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$prod_barcode_take;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$qty;?>" autocomplete="off"></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$take;?>" autocomplete="off"></td> 
                              
                            </tr>
                            <?php 
                          
                          }
                          
                          ?>
                        <?php endif;?> 

                      <!-- TABLE ROW INSERTION -->

                      <tr style="display: none;">
                        <td></td>
                        <td nowrap="nowrap">
                        <button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" onclick="$(this).closest('tr').remove();"><i class="bi bi-trash"<?=$disable_ifapproved;?>></i></button>
                          <input class="mitemrid" type="hidden" value=""/>
                          <input type="hidden" value=""/>
                        </td>
                        <td nowrap="nowrap"><input type="text" size="15" class="form-control form-control-sm mitemcode" ></td>
                        <td nowrap="nowrap"><input type="text" size="15" class="form-control form-control-sm" style="background-color: #EAEAEA;" readonly></td>
                        <td nowrap="nowrap"><input type="text" size="15" class="form-control form-control-sm"  style="background-color: #EAEAEA;" readonly></td>
                        <td nowrap="nowrap"><input type="text" size="10" class="form-control form-control-sm mitemcode2" ></td>
                        <td nowrap="nowrap"><input type="text" size="15" class="form-control form-control-sm" style="background-color: #EAEAEA;" readonly></td>
                        <td nowrap="nowrap"><input type="text" size="15" class="form-control form-control-sm" style="background-color: #EAEAEA;" readonly></td>
                        <td nowrap="nowrap"><input type="text" size="10" class="form-control form-control-sm" autocomplete="off"></td>
                        <td nowrap="nowrap"><input type="text" size="10" class="form-control form-control-sm" autocomplete="off"></td> 
                        
                           

                      </tbody>
                    </table>
                  </div>
                </div>
              </div> 

              <!-- END DETAILS DATA -->
              
              <div class="row gy-2 mb-3">
                <div class="col-sm-4">
                  <?php if(!empty($disable_ifapproved)):?>
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
    __my_item_lookup2();
    

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

        
        $('#mbtn_mn_NTRX').click(function() { 
          var userselection = confirm("Are you sure you want to new transaction?");
          if (userselection == true){
            window.location = '<?=site_url();?>me-buy1take1-vw';
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
    var mid = rowCount + 1;
    var clonedRow = jQuery('#tbl-promo tr:eq(' + (rowCount - 1) + ')').clone();

    jQuery(clonedRow).find('input[type=text]').eq(0).attr('id', 'mitemcode_' + mid);
    jQuery(clonedRow).find('input[type=text]').eq(1).attr('id', 'mitemdesc_' + mid);
    jQuery(clonedRow).find('input[type=text]').eq(2).attr('id', 'mitembcode_' + mid);
    jQuery(clonedRow).find('input[type=text]').eq(3).attr('id', 'mitemdisc_' + mid);
    jQuery(clonedRow).find('input[type=text]').eq(4).attr('id', 'mitemprice_' + mid);
    jQuery(clonedRow).find('input[type=text]').eq(5).attr('id', 'mitemdiscsrp_' + mid);
    jQuery(clonedRow).find('input[type=text]').eq(6).attr('id', 'mitemcost_' + mid);

    jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id', 'mitemrid_' + mid);

    jQuery('#tbl-promo tr').eq(rowCount - 1).before(clonedRow);
    jQuery(clonedRow).css({ 'display': '' });
    var xobjArtItem = jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
    jQuery('#' + xobjArtItem).focus();
    $('#tbl-promo tr').each(function (i) {
      $(this).find('td').eq(0).html(i);
    });

    __my_item_lookup();
    __my_item_lookup2();



  } catch (err) {
    var mtxt = 'There was an error on this page.\n';
    mtxt += 'Error description: ' + err.message;
    mtxt += '\nClick OK to continue.';
    alert(mtxt);
    return false;
  }
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
              source: '<?= site_url(); ?>get-promo-itemc',
            focus: function() {
                  // prevent value inserted on focus
                  return false;
                },
                select: function( event, ui ) {
                  var terms = ui.item.value;
                  jQuery(this).attr('alt', jQuery.trim(ui.item.value));
                  jQuery(this).attr('title', jQuery.trim(ui.item.value));
                  var ndisc = jQuery('#txt_promodiscval').val();
                  this.value = ui.item.value;
                  var clonedRow = jQuery(this).parent().parent().clone();
                  var indexRow = jQuery(this).parent().parent().index();
                  var xobjitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).attr('id');  /*DESC*/
                  var xobjitembcode = jQuery(clonedRow).find('input[type=text]').eq(2).attr('id');  /*BCODE*/
                 
                  jQuery(this).attr('data-mtnkattr',ui.item.mtkn_rid);
                  jQuery('#' + xobjitemdesc).val(ui.item._DESC);
                  jQuery('#' + xobjitembcode).val(ui.item._BARCODE);
                  
                  return false;

                  

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

        function __my_item_lookup2(){
              jQuery('.mitemcode2' ) 
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
                select: function( event, ui ) {
                  var terms = ui.item.value;
                  jQuery(this).attr('alt', jQuery.trim(ui.item.value));
                  jQuery(this).attr('title', jQuery.trim(ui.item.value));
                  var ndisc = jQuery('#txt_promodiscval').val();
                  this.value = ui.item.value;
                  var clonedRow = jQuery(this).parent().parent().clone();
                  var indexRow = jQuery(this).parent().parent().index();
                  var xobjitemdesc = jQuery(clonedRow).find('input[type=text]').eq(4).attr('id');  /*DESC*/
                  var xobjitembcode = jQuery(clonedRow).find('input[type=text]').eq(5).attr('id');  /*BCODE*/
                 
                  jQuery(this).attr('data-mtnkattr',ui.item.mtkn_rid);
                  jQuery('#' + xobjitemdesc).val(ui.item._DESC);
                  jQuery('#' + xobjitembcode).val(ui.item._BARCODE);
                  
                  return false;

                  

                  return false;
                }
              })
          .click(function() { 

              //jQuery(this).keydown(); 
              var terms = this.value;
              //jQuery(this).autocomplete('search', '');
              jQuery(this).autocomplete('search', jQuery.trim(terms));
            });  
        }	  //end __my_item_lookup
        
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
              var qty = jQuery(clonedRow).find('input[type=text]').eq(4).val();
              var xTAmntId = jQuery(clonedRow).find('input[type=text]').eq(5).attr('id');

              var nqty = 0;
              var nprice = 0;
              
              if($.trim(qty) == '') { 
                nqty = 0;
              } else { 
                
                nqty = qty;
              }

              if($.trim(xTAmntId) == '') { 
                nprice2 = 0;
              } else { 
                nprice2 = xTAmntId;
              }
              
              var ntqty = parseFloat(nqty);
              if($('#' + xTAmntId).val()==''){
                var ntprice = parseFloat(ntqty * 1);
              }
              else{

                var ntprice = parseFloat(ntqty * 1);
              }

              if(!isNaN(ntprice) || ntprice > 0) { 
                $('#' + xTAmntId).val(ntprice);
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

 
    <?php
    if (empty($disable_ifapproved)): 
    ?>
      $("#mbtn_mn_Save").click(function(e){
       
        try { 
          //__mysys_apps.mepreloader('mepreloaderme',true);
          var promo_name = jQuery('#txt_promodesc').val();
          var mtkn_mntr = jQuery('#__hmpromotrxnoid').val();
          var txt_buy1take1trxno = jQuery('#txt_buy1take1trxno').val();
          var start_date = jQuery('#start_date').val();
          var start_time = jQuery('#start_time').val();
          var end_date = jQuery('#end_date').val();
          var end_time = jQuery('#end_time').val();
          var is_discount_percent = jQuery('#is_discount_percent').prop('checked');;
          var cb_fix_discount_percent_value = (is_discount_percent) ? (1) : (0);
          var is_fixed_price = jQuery('#is_fixed_price').prop('checked');
          // var cb_fix_value = (is_fixed_price) ? (1) : (0);
          var cb_fix_value = 1;
          var is_fixed_price_checked = jQuery('#is_fixed_price').val();
          var branch_name = jQuery('#branch_name').val();
          var data_mtknid = jQuery('#branch_name').attr('data-mtknid');
          var rowCount1 = jQuery('#tbl-promo tr').length - 1;
          var adata1 = [];
          var adata2 = [];

          var mdata = '';
          var ninc = 0;

          for(aa = 1; aa < rowCount1; aa++) { 
            var clonedRow = jQuery('#tbl-promo tr:eq(' + aa + ')').clone(); 
            var mitemc = jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
            var mdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
            var mbcode = jQuery(clonedRow).find('input[type=text]').eq(2).val();
            var mitemc2 = jQuery(clonedRow).find('input[type=text]').eq(3).val(); 
            var mdesc2 = jQuery(clonedRow).find('input[type=text]').eq(4).val(); 
            var mbcode2 = jQuery(clonedRow).find('input[type=text]').eq(5).val(); 
            var qty = jQuery(clonedRow).find('input[type=text]').eq(6).val(); 
            var take = jQuery(clonedRow).find('input[type=text]').eq(7).val(); 
            var mitemc_tkn = jQuery(clonedRow).find('input[type=text]').eq(0).attr('data-mtnkattr');  
            
            mdata = mitemc + 'x|x' + mdesc + 'x|x' + mbcode + 'x|x' + mitemc2 + 'x|x' + mdesc2 + 'x|x' + mbcode2 + 'x|x' + qty + 'x|x' + take + 'x|x' + mitemc_tkn;
            adata1.push(mdata);
            var mdata = jQuery(clonedRow).find('input[type=hidden]').eq(0).val();
            adata2.push(mitemc_tkn);
            ninc = 1;

            }  //end for
            if(ninc == 0) { 
              jQuery('#memsgtestent_bod').html('No Valid DATA!!!');
              jQuery('#memsgtestent').modal('show');
              return;
            }
            __mysys_apps.mepreloader('mepreloaderme',true);
            var mparam = {
              mtkn_mntr:mtkn_mntr,
              txt_buy1take1trxno:txt_buy1take1trxno,
              start_date: start_date,
              start_time: start_time,
              end_date: end_date,
              end_time: end_time,
              cb_fix_discount_percent_value:cb_fix_discount_percent_value,
              cb_fix_value:cb_fix_value,
              branch_name:branch_name,
              promo_name:promo_name,
              mtkn_branch:data_mtknid,
              adata1: adata1,
              adata2: adata2
            };  

            console.log(mparam);
            
            $.ajax({ 
              type: "POST",
              url: '<?=site_url();?>me-buy1take1-save',
              context: document.body,
              data: eval(mparam),
              global: false,
              cache: false,
              success: function(data)  { 
                __mysys_apps.mepreloader('mepreloaderme',false);
                jQuery(this).prop('disabled', false);
                jQuery('#memsgtestent_bod').html(data);
                jQuery('#memsgtestent').modal('show');
                return false;
				      },
              error: function() {
                jQuery('#memsgtestent_bod').html('<span class="fw-bolder text-danger">Error loading...</span>');
                jQuery('#memsgtestent').modal('show');
                __mysys_apps.mepreloader('mepreloaderme',false);
                return false;
				      } 
			});  //end jQuery POST

          } catch(err) {
            var mtxt = 'There was an error on this page.\n';
            mtxt += 'Error description: ' + err.message;
            mtxt += '\nClick OK to continue.';
            alert(mtxt);
          }  //end try
          return false; 
      });
    <?php
    endif;
    ?>
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
          url: "<?=site_url();?>me-buy1take1-view",
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
      url: "<?=site_url();?>me-buy1take1-view-appr",
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
    var is_fixed_price = jQuery('#is_fixed_price').prop('checked');
    var cb_value_fix = (is_fixed_price) ? (1) : (0);
    var is_discount_percent = jQuery('#is_discount_percent').prop('checked');;
    var cb_value_percent = (is_discount_percent) ? (1) : (0);
    checkboxes.forEach(cb =>  {
      
    })
    

  }

  
  
</script>













