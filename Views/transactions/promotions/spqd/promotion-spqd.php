<?php

//VARIABLE DECLARATIONS
$myusermod = model('App\Models\MyUserModel');
$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mytrxfgpack = model('App\Models\MyPromoBuy1take1Model');
$mydataz = model('App\Models\MyDatumModel');
$this->dbx = $mylibzdb->dbx;
$this->db_erp = $mydbname->medb(0);
$mylibzsys = model('App\Models\MyLibzSysModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$cuserrema = $myusermod->mysys_userrema();
$mtkn_trxno = $request->getVar('mtkn_trxno');
$mmnhd_rid   ='';
$txtpout_typ = '';
$str_dis     = "";
$txt_branch  = '';
$trx_no      = '';
$startDate   = '';
$endDate     = '';
$mencd_date       = date("Y-m-d");  
$percentDisc = '';
$pesoDisc    ='checked';
$txt_branchID = '';
$str_style = " style=\"display:none;\"";
$btn_save = 'Save';
$post_tag = '';
$pd_stats = '';
$intText  = ''; //to disabled text
$endTime = date('23:59');
$startTime = date('08:00');
$total_promo = "";
$orig_srp = $request->getVar('orig_srp');
$spqd_trxno = $request->getVar('spqd_trxno');
$spqd_trxnodashb = $request->getVar('spqd_trxnodashb');
$spqd_trx_no = $request->getVar('spqd_trx_no');
$dashboard = $request->getVar('dashboard');
$dashboard1 = $request->getVar('dashboard1');
$for_approval = $request->getVar('for_approval');
$branch_name = '';
$branch_code = '';
$spqd_reason = '';
$new_total_srp = '';
$last_total_srp = '';
$total_qty = '';
$txt_spqd_trx_no = '';
$disable = '';
$disable_ifapproved = '';
$nporecs = 0;
$disablebranch= '';
$disable2 = '';
$isvalid = 'background-color: #EAEAEA;';
$promo_code_spqd='';
$txt_spromo = '';
$txtsearchedrec = $request->getVar('txtsearchedrec');
$tracing_no = $request->getVar('tracing_no');
$disable_plus = '';
$mtkn_recid = $request->getVar('mtkn_recid');
$ifvalidDisable = '';
if(!empty($tracing_no) || !empty($dashboard)|| !empty($for_approval))  {
	$disable_plus = 'disabled';

}


	
if(!empty($spqd_trxno) || !empty($for_approval)) {

	$str = "
	select distinct aa.`id`,
	aa.`branch_code`,
	aa.`spqd_reason`,
	aa.`spromo_code`,
	aa.`new_total_srp`,
	aa.`last_total_srp`,
	aa.`start_date`,
	aa.`start_time`,
	aa.`end_date`,
	aa.`end_time`,
	aa.`is_approved`,
	cc.`branch_name`,
	aa.`total_qty`,
	cc.`is_disable`,
	cc.`isvalid`,
	aa.`spqd_trx_no`
	from `trx_pos_promo_spqd_hd` aa 
	join `mst_companyBranch` bb
	on aa.`branch_code` = bb.`BRNCH_MBCODE`
	join `trx_pos_promo_spqd_dt` cc
	on aa.`spqd_trx_no` = cc.`spqd_trx_no`
	where 
	(aa.`spqd_trx_no` = '$spqd_trxno' OR '$spqd_trxno' = '')
    AND
    (aa.`spqd_trx_no` = '$for_approval' OR '$for_approval' = '');
	";

	$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	$rw = $q->getRowArray();
	$txt_spqd_trx_no = $rw['spqd_trx_no'];
	$branch_code = $rw['branch_code'];
	$spqd_reason = $rw['spqd_reason'];
	$new_total_srp = $rw['new_total_srp'];
	$last_total_srp = $rw['last_total_srp'];
	$total_qty = $rw['total_qty'];
	$branch_name = $rw['branch_name'];
	$startDate =$rw['start_date'];
	$startTime =$rw['start_time'];
	$endDate = $rw['end_date'];
	$endTime =$rw['end_time'];
	$txt_spromo = $rw['spromo_code'];
	$disable = ($rw['is_disable'] == 'Y' ? ' disabled ' : '');
	$disable2 = ($rw['is_disable'] == 'Y' ? ' readonly' : '');
	$disable_ifapproved  = ($rw['is_approved'] == 'Y' ? ' readonly' : '');
	$disablebranch= 'disabled';
	
  }

  if(!empty($dashboard)) {

	$str = "
	select *,SUM(qty_spqd)AS total_quantity,SUM(last_srp_amount)AS total_last_amount,SUM(new_srp_amount)AS total_new_amount 
	from `trx_pos_promo_spqd_dt` WHERE promo_code_spqd='$dashboard' GROUP BY promo_code_spqd
	";
	$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	$rw = $q->getRowArray();
	$new_total_srp = $rw['total_new_amount'];
	$last_total_srp = $rw['total_last_amount'];
	$total_qty = $rw['total_quantity'];
  }
  if(!empty($tracing_no)) {

	$str = "
	select *,SUM(qty_spqd)AS total_quantity,SUM(last_srp_amount)AS total_last_amount,SUM(new_srp_amount)AS total_new_amount 
	from `trx_pos_promo_spqd_dt` WHERE item_code='$tracing_no' 


	";

	$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	$rw = $q->getRowArray();

	$branch_code = $rw['branch_code'];

	$new_total_srp = $rw['total_new_amount'];
	$last_total_srp = $rw['total_last_amount'];
	$total_qty = $rw['total_quantity'];
	$branch_name = $rw['branch_name'];

	
  }

  if(!empty($dashboard1)) {

	$str = "
	select distinct aa.`id`,
	aa.`branch_code`,
	aa.`spqd_reason`,
	aa.`spromo_code`,
	aa.`new_total_srp`,
	aa.`last_total_srp`,
	aa.`start_date`,
	aa.`start_time`,
	aa.`end_date`,
	aa.`end_time`,
	cc.`branch_name`,
	aa.`total_qty`,
	cc.`is_disable`,
	cc.`isvalid`,
	aa.`spqd_trx_no`
	from `trx_pos_promo_spqd_hd` aa 
	join `mst_companyBranch` bb
	on aa.`branch_code` = bb.`BRNCH_MBCODE`
	join `trx_pos_promo_spqd_dt` cc
	on aa.`spqd_trx_no` = cc.`spqd_trx_no`
	where 
	aa.`spqd_trx_no` = '$spqd_trxno'

	";

	$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	$rw = $q->getRowArray();
	$txt_spqd_trx_no = $rw['spqd_trx_no'];
	$branch_code = $rw['branch_code'];
	$spqd_reason = $rw['spqd_reason'];
	$new_total_srp = $rw['new_total_srp'];
	$last_total_srp = $rw['last_total_srp'];
	$total_qty = $rw['total_qty'];
	$branch_name = $rw['branch_name'];
	$startDate =$rw['start_date'];
	$startTime =$rw['start_time'];
	$endDate = $rw['end_date'];
	$endTime =$rw['end_time'];
	$txt_spromo = $rw['spromo_code'];
	$disable = ($rw['is_disable'] == 'Y' ? ' disabled ' : '');
	$disable2 = ($rw['is_disable'] == 'Y' ? ' readonly' : '');
	$disablebranch= 'disabled';
	
  }








  
  

echo view('templates/meheader01');
?>

<style>
	 .custom-row {
    margin-left: 150px; /* Adjust the margin value as needed */
  }

  

</style>
        
<main id="main" class="main">
	<div class="pagetitle">
		<h1>Promotion </h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Home</a></li>
				<li class="breadcrumb-item">Sales</li>
				<li class="breadcrumb-item active">Promotion - SPROMO/QDAMAGE  </li>
			</ol>
		</nav>
	</div> <!-- End Page Title -->
	<section class="section">
		<div class="row metblentry-font">
			<div class="col-md-8">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span for="txt_spqd_trx_no">S-promo Discount No.:</span>
					</div>
					<div class="col-sm-9">
						<input class="form-control form-control-sm" name="txt_spqd_trx_no" id="txt_spqd_trx_no"  value = "<?=$txt_spqd_trx_no;?>" type="text" placeholder="Special Discount Transaction Number" readonly>
                        <input type="hidden" id="__hmpromotrxnoid" name="__hmpromotrxnoid" class="form-control form-control-sm" value="<?=$spqd_trxno;?>"/>
					</div>
				</div> <!-- end Promo Trx. No -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Branch:</span>
					</div>
					<div class="col-sm-9">
						<input class="form-control form-control-sm"data-id-brnch-name="<?=$branch_name;?>" name="branch_name" id="branch_name" type="text" placeholder="Branch Name" value="<?=$branch_name;?>"  required >
						<input type="hidden" data-id-brnch="<?=$branch_code;?>" placeholder="Branch Name" id="branch_code" name="branch_code" class="branch_code form-control form-control-sm " value="<?=$branch_code;?>" required/>     
					</div>
				</div> <!-- end Branch -->

				<div class="row mb-3">
					<label class="col-sm-3 form-label" for="txt_spromo">SPROMO</label>
						<div class="col-sm-3">
							<input type="text" class="form-control form-control-sm <?=$nporecs;?> mitemcode2" id="txt_spromo" onchange="refreshval(this)" <?=$disable;?> value="<?=$txt_spromo;?>" >
						</div>
				</div>


				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Reason:</span>
					</div>
					<div class="col-sm-9">
						<input class="form-control form-control-sm" name="reason_txt" id="reason_txt" type="text" placeholder="" value="<?=$spqd_reason;?>"  required>
					</div>
					<div class="row gy-2 offset-lg-3">
						<div class="col-sm-3">
							<input type="date"  id="start_date" name="start_date" class="start_date form-control form-control-sm " value="<?=$startDate;?>" required/>
							<label for="start_date">Start date</label>
						</div>

						<div class="col-sm-3">
							<input type="time" id="start_time" name="start_time" class="start_time form-control form-control-sm " value="<?=$startTime;?>"  required/>
							<label for="">Time</label>
						</div>

						<div class="col-sm-3">
							<input type="date" id="end_date" name="end_date" class="end_date form-control form-control-sm " value="<?=$endDate;?>"  required/>
							<label for="">End date</label>
						</div>

						<div class="col-sm-3">
							<input type="time" id="end_time" name="end_time" class="end_time form-control form-control-sm " value="<?=$endTime;?>"  required/>
							<label for="">Time</label>
						</div>
				</div> <!-- Total Qty-->
				<div class="row mb-3 custom-row justify-content-center">
					<div class="col-sm-1 ml-auto">
						<span>Total Qty:</span>
					</div>
					<div class="col-sm-1 ml-auto">
						<input class="form-control form-control-sm" name="total_qty" id="total_qty" type="text" placeholder="" value="<?=$total_qty;?>"  readonly>
					</div>
					<div class="col-sm-2 ml-auto">
						<span>Last SRP Total Amount:</span>
					</div>
					<div class="col-sm-2 ml-auto">
						<input class="form-control form-control-sm" name="total_last_srp" id="total_last_srp" type="text" placeholder="" value="<?=$last_total_srp;?>"  readonly>
					</div>
					<div class="col-sm-2 ml-auto">
						<span>New SRP Total Amount:</span>
					</div>
					<div class="col-sm-2 ml-auto">
						<input class="form-control form-control-sm" name="total_new_srp" id="total_new_srp" type="text" placeholder="" value="<?=$new_total_srp;?>"  readonly>
					</div>
				</div> <!-- end qty -->
			</div> <!-- end col-md-6 -->
			</div> <!-- end col-md-6 -->
		</div> <!-- end row metblentry-font -->
		<!-- table data entry -->
		
		<div class="row ">
			<div  id="tbl_items_ent">
				<div class="table-responsive">
					<table id="tbl_PayData" class="metblentry-font table-bordered" >  
						<thead class="text-center">
					
						<th  class="text-center">
							 <button type="button" class="btn btn-sm btn-success p-1 pb-0"<?=$disable_plus;?> <?=$disable;?> onclick="javascript:my_add_line_item();" >
								<i class="bi bi-plus-lg"></i>
							</button>
							
						<th id="item_sync" class=" fas fa-sync" class="text-center"> No.</th>
					
						</th>
						<?php if (!empty($tracing_no)|| !empty($dashboard)) { ?>
						
							
							<th>Spromo No.</th>
							<th>Spromo Code</th>
							<th>Qty</th>
							<th>Qty after Promo</th>
							<th>Disable</th>
						<?php } else {?>
							
							<th>Itemcode</th>
							<th>Description</th>
							<th>Qty</th>
							<th>Last SRP</th>
							<th>Amount</th>
							<th>New SRP</th>
							<th>Amount</th>
							<th>Sales Loss</th>
						<?php if (!empty($for_approval)) { ?>
							<th>Action</th>
							<?php }?>
							
							<?php }?>
							
							<th style ="display:none;" class="frmmitemcode">From Itemcode</th>
						</thead>
						<tbody id="contentDetlArea">

					

					
						<?php if(!empty($spqd_trxno)): //data retrieval if existing
                          
                          $str = "
                          select distinct
                          aa.`spqd_trx_no`,
						  aa.`spqd_reason`,
						  aa.`new_total_srp`,
						  aa.`last_total_srp`,
						  aa.`total_qty`,
						  cc.`qty_spqd`,
                          cc.`item_desc_spqd`,
                          cc.`last_srp_amount`,
						  cc.`promo_code_spqd`,
						  cc.`new_srp`,
						  cc.`new_srp_amount`,
                          cc.`profit_loss`,
						  cc.`last_srp`,
						  cc.`item_barcode`,
						  cc.`promo_barcode`,
						  cc.`isvalid`,
                          dd.`ART_CODE`,
                          dd.`ART_DESC`,
						  dd.`ART_BARCODE1`,
                          dd.`ART_UPRICE`


                          from `trx_pos_promo_spqd_hd` aa 
                          join `mst_companyBranch` bb
                          on aa.`branch_code` = bb.`BRNCH_MBCODE`
                          join `trx_pos_promo_spqd_dt` cc
                          on aa.`spqd_trx_no` = cc.`spqd_trx_no`
                          join `mst_article` dd
						  ON cc.`item_code` = dd.`ART_CODE`
                          where aa.`spqd_trx_no` = '$spqd_trxno' 
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                            $nporecs++;


							$qty_spqd = $data['qty_spqd'];
                            $last_srp_amount = $data['last_srp_amount'];
                            $promo_code_spqd = $data['promo_code_spqd'];
                            $new_srp = $data['new_srp'];
                            $new_srp_amount = $data['new_srp_amount'];
							$profit_loss = $data['profit_loss'];
                            $ART_CODE=$data['ART_CODE'];
                            $ART_DESC = $data['ART_DESC'];
							$ART_UPRICE = $data['ART_UPRICE'];
							$last_srp = $data['last_srp'];
							$promo_barcode = $data['promo_barcode'];
							$item_barcode = $data['item_barcode'];
							$isvalid = $data['isvalid'] == 'Y' ? '' : 'background-color:  rgba(212, 91, 91, 0.5);';

							$strq = "
										Select spqd_trx_no  from trx_pos_promo_spqd_dt where item_barcode = '$item_barcode'
							";
							$q1 = $mylibzdb->myoa_sql_exec($strq,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							$rv = $q1->getRowArray();
							
						
                            
                            ?>
                            
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->

                            <tr  >
                              
                              <td nowrap="nowrap" >
							 
                                <button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1"  onclick="$(this).closest('tr').remove();"><i class="bi bi-x"></i></button>
                                <input class="mitemrid" type="hidden" value=""/>
                                <input type="hidden" value=""/>
                              </td>
							  <td><?=$nporecs;?></td>
							<td>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm <?=$nporecs;?> mitemcode" style="border:none; padding-right: 0;" <?=$disable2;?> value="<?=$ART_CODE;?>">
								<?=anchor('mepromo-spqd-view/?tracing_no=' . $ART_CODE, '<i class="bi bi-info-circle-fill input-group-text" style="background-color: white !important; border: none;color:red; box-shadow: none;" data-toggle="tooltip" data-placement="top" title="Show Previous Promo" name="audit_trail" id="audit_trail" value=""></i>', 'class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm" style="margin-left: -10px; padding-left: 0; padding-right: 0;"');?>
							</div>


							</td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$ART_DESC;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$qty_spqd;?>" style="background-color: #EAEAEA;" ></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> " value="<?=$ART_UPRICE;?>"style="background-color: #EAEAEA;"readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$last_srp_amount;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td style="display: none; border: none; " nowrap="nowrap"><input type="hidden" class="form-control form-control-sm <?=$nporecs;?> mitemcode2"onchange="refreshval(this)" <?=$disable;?> value="<?=$promo_code_spqd;?>" ></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> spromo_price"  id="spromo_price" value="<?=$new_srp;?>" style="background-color: #EAEAEA;" autocomplete="off" readonly></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" id="new_srp_amount" value="<?=$new_srp_amount;?>" style="background-color: #EAEAEA;" autocomplete="off" readonly></td>
                              <td nowrap="nowrap"><input type="text"  class="form-control form-control-sm <?=$nporecs;?> text-end " value="<?=$profit_loss;?>" style="<?=$isvalid;?>"<?=$isvalid;?>autocomplete="off" readonly></td>
							  <td style="display: none; border: none; " nowrap="nowrap"><input type="text" class="<?=$nporecs;?>" value="<?=$item_barcode;?>" autocomplete="off" readonly></td>
							  <td style="display: none; border: none; " nowrap="nowrap"><input type="text" class="<?=$nporecs;?>" value="<?=$promo_barcode;?>"autocomplete="off" readonly></td>
                            </tr>
                            <?php 
                          
                          }
						  
                          
                          ?>
                        <?php endif;?>  

						<?php if(!empty($for_approval)): //data retrieval if existing
                        
							
					
						
                          $str = "
                          select distinct
                          aa.`spqd_trx_no`,
						  aa.`spqd_reason`,
						  aa.`new_total_srp`,
						  aa.`last_total_srp`,
						  aa.`total_qty`,
						  cc.`qty_spqd`,
                          cc.`item_desc_spqd`,
                          cc.`last_srp_amount`,
						  cc.`promo_code_spqd`,
						  cc.`new_srp`,
						  cc.`new_srp_amount`,
                          cc.`profit_loss`,
						  cc.`last_srp`,
						  cc.`item_barcode`,
						  cc.`promo_barcode`,
						  cc.`isvalid`,
						  cc.`id`,
                          dd.`ART_CODE`,
                          dd.`ART_DESC`,
						  dd.`ART_BARCODE1`,
                          dd.`ART_UPRICE`


                          from `trx_pos_promo_spqd_hd` aa 
                          join `mst_companyBranch` bb
                          on aa.`branch_code` = bb.`BRNCH_MBCODE`
                          join `trx_pos_promo_spqd_dt` cc
                          on aa.`spqd_trx_no` = cc.`spqd_trx_no`
                          join `mst_article` dd
						  ON cc.`item_code` = dd.`ART_CODE`
                          where aa.`spqd_trx_no` = '$for_approval' 
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                            $nporecs++;

							$spqd_trxno = $data['spqd_trx_no'];
							$qty_spqd = $data['qty_spqd'];
                            $last_srp_amount = $data['last_srp_amount'];
                            $promo_code_spqd = $data['promo_code_spqd'];
                            $new_srp = $data['new_srp'];
                            $new_srp_amount = $data['new_srp_amount'];
							$profit_loss = $data['profit_loss'];
                            $ART_CODE=$data['ART_CODE'];
                            $ART_DESC = $data['ART_DESC'];
							$ART_UPRICE = $data['ART_UPRICE'];
							$last_srp = $data['last_srp'];
							$promo_barcode = $data['promo_barcode'];
							$item_barcode = $data['item_barcode'];
							$isvalid = $data['isvalid'] == 'Y' ? '' : 'background-color:  rgba(212, 91, 91, 0.5);';
							$ifvalidDisable = $data['isvalid'] == 'Y' ? 'disabled' : '';
							$mtkn_recid = hash('sha384', $data['id'] . $mpw_tkn);
						
						
							$strq = "
										Select spqd_trx_no  from trx_pos_promo_spqd_dt where item_barcode = '$item_barcode'
							";
							$q1 = $mylibzdb->myoa_sql_exec($strq,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							$rv = $q1->getRowArray();
					
						
                            
                            ?>
                            
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->

                            <tr  >
                              
                              <td nowrap="nowrap" >
							 
                                <button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1"  onclick="$(this).closest('tr').remove();"><i class="bi bi-x"></i></button>
                                <input class="mitemrid" type="hidden" value=""/>
                                <input type="hidden" value=""/>
                              </td>
							  <td><?=$nporecs;?></td>
							<td>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm <?=$nporecs;?> mitemcode" style="border:none; padding-right: 0;" <?=$disable2;?> value="<?=$ART_CODE;?>">
								<?=anchor('mepromo-spqd-view/?tracing_no=' . $ART_CODE, '<i class="bi bi-info-circle-fill input-group-text" style="background-color: white !important; border: none;color:red; box-shadow: none;" data-toggle="tooltip" data-placement="top" title="Show Previous Promo" name="audit_trail" id="audit_trail" value=""></i>', 'class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm" style="margin-left: -10px; padding-left: 0; padding-right: 0;"');?>
							</div>


							</td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$ART_DESC;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$qty_spqd;?>" style="background-color: #EAEAEA;" ></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> " value="<?=$ART_UPRICE;?>"style="background-color: #EAEAEA;"readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$last_srp_amount;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td style="display: none; border: none; " nowrap="nowrap"><input type="hidden" class="form-control form-control-sm <?=$nporecs;?> mitemcode2"onchange="refreshval(this)" <?=$disable;?> value="<?=$promo_code_spqd;?>" ></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$new_srp;?>" style="background-color: #EAEAEA;" autocomplete="off" readonly></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$new_srp_amount;?>" style="background-color: #EAEAEA;" autocomplete="off" readonly></td>
                              <td nowrap="nowrap"><input type="text"  class="form-control form-control-sm <?=$nporecs;?> text-end " value="<?=$profit_loss;?>" style="<?=$isvalid;?>"<?=$isvalid;?>autocomplete="off" readonly></td>
							  <td style="display: none; border: none; " nowrap="nowrap"><input type="text" class="<?=$nporecs;?>" value="<?=$item_barcode;?>" autocomplete="off" readonly></td>
							  <td style="display: none; border: none; " nowrap="nowrap"><input type="text" class="<?=$nporecs;?>" value="<?=$promo_barcode;?>"autocomplete="off" readonly></td>
							  <td>
							  <button id="per_line_save" class="btn btn-dgreen btn-sm" Style="background-color: #167F92; color: #FFF; padding: 2px 6px 2px 6px; " onclick="javascript:save_for_approve_spqd_perline('<?=$mtkn_recid;?>', '<?=$spqd_trxno;?>', '<?=$ART_CODE;?>','<?=$item_barcode;?>');" <?=$ifvalidDisable;?>> <i  >Approved</i></button>
						  	  </td>
                            </tr>
                            <?php 
                          
                          }
						  
                          
                          ?>
                        <?php endif;?>  

						<?php if(!empty($tracing_no)): //data retrieval if existing
                          
                          $str = "
                          select distinct
                          aa.`spqd_trx_no`,
						  aa.`spqd_reason`,
						  aa.`new_total_srp`,
						  aa.`last_total_srp`,
						  aa.`total_qty`,
						  cc.`qty_spqd`,
                          cc.`item_desc_spqd`,
                          cc.`last_srp_amount`,
						  cc.`promo_code_spqd`,
						  cc.`is_disable`,
						  cc.`new_srp`,
						  cc.`new_srp_amount`,
                          cc.`profit_loss`,
						  cc.`last_srp`,
						  cc.`item_barcode`,
						  cc.`promo_barcode`,
                          dd.`ART_CODE`,
                          dd.`ART_DESC`,
						  dd.`ART_BARCODE1`,
                          dd.`ART_UPRICE`


                          from `trx_pos_promo_spqd_hd` aa 
                          join `mst_companyBranch` bb
                          on aa.`branch_code` = bb.`BRNCH_MBCODE`	
                          join `trx_pos_promo_spqd_dt` cc
                          on aa.`spqd_trx_no` = cc.`spqd_trx_no`
                          join `mst_article` dd
						  ON cc.`item_code` = dd.`ART_CODE`
                          where cc.`item_code` = '$tracing_no' ORDER BY 
						   cc.`is_disable` = 'N' DESC,
						   cc.`promo_code_spqd` DESC;
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                          

							$spqd_trxno = $data['spqd_trx_no'];
							$qty_spqd = $data['qty_spqd'];
                            $last_srp_amount = $data['last_srp_amount'];
                            $promo_code_spqd = $data['promo_code_spqd'];
                            $new_srp = $data['new_srp'];
                            $new_srp_amount = $data['new_srp_amount'];
							$profit_loss = $data['profit_loss'];
                            $ART_CODE=$data['ART_CODE'];
                            $ART_DESC = $data['ART_DESC'];
							$ART_UPRICE = $data['ART_UPRICE'];
							$last_srp = $data['last_srp'];
							$promo_barcode = $data['promo_barcode'];
							$item_barcode = $data['item_barcode'];
							$is_disable = $data['is_disable'];

							$strq = "
										Select spqd_trx_no  from trx_pos_promo_spqd_dt where item_barcode = '$item_barcode' 
							";
							$q1 = $mylibzdb->myoa_sql_exec($strq,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							$rv = $q1->getRowArray();
							
						
                            
                            ?>
                            
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->

                            <tr >
                              
                              <td nowrap="nowrap">
							  <?=anchor('mepromo-spqd-view/?spqd_trxno=' . $spqd_trxno, '<i class="bi bi bi-pencil" style="background-color:#167F92; color:#fff; padding: 3px 5px 3px;; border-radius: 5px;"></i>',' class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm"');?>
                                <input class="mitemrid" type="hidden" value=""/>
                                <input type="hidden" value=""/>
                              </td>
							  <td><?=$nporecs;?></td>
							
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$spqd_trxno;?>"  readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$promo_code_spqd;?>"readonly style="background-color: #EAEAEA;" ></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$qty_spqd;?>" readonly style="background-color: #EAEAEA;" ></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$qty_spqd;?>" readonly style="background-color: #EAEAEA;" ></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$is_disable;?>"readonly style="background-color: #EAEAEA;" ></td>
                              
                             
                            </tr>
                            <?php 
                          
                          }
						  
                          
                          ?>
                        <?php endif;?>  

						
						<?php if(!empty($dashboard)): //data retrieval if existing
                          
                          $str = "
                          select distinct
                          aa.`spqd_trx_no`,
						  aa.`spqd_reason`,
						  aa.`new_total_srp`,
						  aa.`last_total_srp`,
						  aa.`total_qty`,
						  cc.`qty_spqd`,
                          cc.`item_desc_spqd`,
                          cc.`last_srp_amount`,
						  cc.`promo_code_spqd`,
						  cc.`is_disable`,
						  cc.`new_srp`,
						  cc.`new_srp_amount`,
                          cc.`profit_loss`,
						  cc.`last_srp`,
						  cc.`item_barcode`,
						  cc.`promo_barcode`,
                          dd.`ART_CODE`,
                          dd.`ART_DESC`,
						  dd.`ART_BARCODE1`,
                          dd.`ART_UPRICE`


                          from `trx_pos_promo_spqd_hd` aa 
                          join `mst_companyBranch` bb
                          on aa.`branch_code` = bb.`BRNCH_MBCODE`	
                          join `trx_pos_promo_spqd_dt` cc
                          on aa.`spqd_trx_no` = cc.`spqd_trx_no`
                          join `mst_article` dd
						  ON cc.`item_code` = dd.`ART_CODE`
                          where cc.`promo_code_spqd` = '$dashboard' ORDER BY 
						   cc.`is_disable` = 'N' DESC,
						   cc.`promo_code_spqd` DESC;
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                          

							$spqd_trxno = $data['spqd_trx_no'];
							$qty_spqd = $data['total_qty'];
                            $last_srp_amount = $data['last_srp_amount'];
                            $promo_code_spqd = $data['promo_code_spqd'];
                            $new_srp = $data['new_srp'];
                            $new_srp_amount = $data['new_srp_amount'];
							$profit_loss = $data['profit_loss'];
                            $ART_CODE=$data['ART_CODE'];
                            $ART_DESC = $data['ART_DESC'];
							$ART_UPRICE = $data['ART_UPRICE'];
							$last_srp = $data['last_srp'];
							$promo_barcode = $data['promo_barcode'];
							$item_barcode = $data['item_barcode'];
							$is_disable = $data['is_disable'];

							$strq = "
										Select spqd_trx_no  from trx_pos_promo_spqd_dt where item_barcode = '$item_barcode' 
							";
							$q1 = $mylibzdb->myoa_sql_exec($strq,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							$rv = $q1->getRowArray();
							
						
                            
                            ?>
                            
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->

                            <tr >
                              
                              <td nowrap="nowrap">
							  <?=anchor('mepromo-spqd-view/?spqd_trxno=' . $spqd_trxno, '<i class="bi bi bi-pencil" style="background-color:#167F92; color:#fff; padding: 3px 5px 3px;; border-radius: 5px;"></i>',' class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm"');?>
                                <input class="mitemrid" type="hidden" value=""/>
                                <input type="hidden" value=""/>
                              </td>
							  <td><?=$nporecs;?></td>
						
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$spqd_trxno;?>"  readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$promo_code_spqd;?>"readonly style="background-color: #EAEAEA;" ></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$qty_spqd;?>" readonly style="background-color: #EAEAEA;" ></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$qty_spqd;?>" readonly style="background-color: #EAEAEA;" ></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$is_disable;?>"readonly style="background-color: #EAEAEA;" ></td>
                              
                             
                            </tr>
                            <?php 
                          
                          }
						  
                          
                          ?>
                        <?php endif;?>  
						<?php if(!empty($dashboard1)): //data retrieval if existing
                          
                          $str = "
                          select distinct
                          aa.`spqd_trx_no`,
						  aa.`spqd_reason`,
						  aa.`new_total_srp`,
						  aa.`last_total_srp`,
						  aa.`total_qty`,
						  cc.`qty_spqd`,
                          cc.`item_desc_spqd`,
                          cc.`last_srp_amount`,
						  cc.`promo_code_spqd`,
						  cc.`is_disable`,
						  cc.`new_srp`,
						  cc.`new_srp_amount`,
                          cc.`profit_loss`,
						  cc.`last_srp`,
						  cc.`item_barcode`,
						  cc.`promo_barcode`,
                          dd.`ART_CODE`,
                          dd.`ART_DESC`,
						  dd.`ART_BARCODE1`,
                          dd.`ART_UPRICE`


                          from `trx_pos_promo_spqd_hd` aa 
                          join `mst_companyBranch` bb
                          on aa.`branch_code` = bb.`BRNCH_MBCODE`	
                          join `trx_pos_promo_spqd_dt` cc
                          on aa.`spqd_trx_no` = cc.`spqd_trx_no`
                          join `mst_article` dd
						  ON cc.`item_code` = dd.`ART_CODE`
                          where cc.`spqd_trx_no` = '$dashboard1' ORDER BY 
						   cc.`is_disable` = 'N' DESC,
						   cc.`promo_code_spqd` DESC;
                          ";
                          
                          $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                          $rw = $q->getResultArray();
                          foreach ($rw as $data) {
                          

							$spqd_trxno = $data['spqd_trx_no'];
							$qty_spqd = $data['qty_spqd'];
                            $last_srp_amount = $data['last_srp_amount'];
                            $promo_code_spqd = $data['promo_code_spqd'];
                            $new_srp = $data['new_srp'];
                            $new_srp_amount = $data['new_srp_amount'];
							$profit_loss = $data['profit_loss'];
                            $ART_CODE=$data['ART_CODE'];
                            $ART_DESC = $data['ART_DESC'];
							$ART_UPRICE = $data['ART_UPRICE'];
							$last_srp = $data['last_srp'];
							$promo_barcode = $data['promo_barcode'];
							$item_barcode = $data['item_barcode'];
							$is_disable = $data['is_disable'];

							$strq = "
										Select spqd_trx_no  from trx_pos_promo_spqd_dt where item_barcode = '$item_barcode' 
							";
							$q1 = $mylibzdb->myoa_sql_exec($strq,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							$rv = $q1->getRowArray();
							
						
                            
                            ?>
                            
                            
                            <!-- DISPLAY ROW WITH VALUE BASE ON PROMO TRX -->

                            <tr >
                              
                              <td nowrap="nowrap">
							  <?=anchor('mepromo-spqd-view/?spqd_trxno=' . $spqd_trxno, '<i class="bi bi bi-pencil" style="background-color:#167F92; color:#fff; padding: 3px 5px 3px;; border-radius: 5px;"></i>',' class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm"');?>
                                <input class="mitemrid" type="hidden" value=""/>
                                <input type="hidden" value=""/>
                              </td>
							  <td><?=$nporecs;?></td>
							  <td>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm <?=$nporecs;?> mitemcode" style="border:none; padding-right: 0;" <?=$disable2;?> value="<?=$ART_CODE;?>">
								<?=anchor('mepromo-spqd-view/?tracing_no=' . $ART_CODE, '<i class="bi bi-info-circle-fill input-group-text" style="background-color: white !important; border: none;color:red; box-shadow: none;" data-toggle="tooltip" data-placement="top" title="Show Previous Promo" name="audit_trail" id="audit_trail" value=""></i>', 'class="btn btn-dgreen p-1 pb-0 mebtnpt1 btn-sm" style="margin-left: -10px; padding-left: 0; padding-right: 0;"');?>
							</div>


							</td>

							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$ART_DESC;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>"onchange="refreshval(this)" <?=$disable;?> value="<?=$qty_spqd;?>" style="background-color: #EAEAEA;" ></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?> " value="<?=$ART_UPRICE;?>"style="background-color: #EAEAEA;"readonly></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$last_srp_amount;?>" style="background-color: #EAEAEA;" readonly></td>
                              <td style="display: none; border: none; " nowrap="nowrap"><input type="hidden" class="form-control form-control-sm <?=$nporecs;?> mitemcode2"onchange="refreshval(this)" <?=$disable;?> value="<?=$promo_code_spqd;?>" ></td>
                              <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$new_srp;?>" style="background-color: #EAEAEA;" autocomplete="off" readonly></td>
							  <td nowrap="nowrap"><input type="text" class="form-control form-control-sm <?=$nporecs;?>" value="<?=$new_srp_amount;?>" style="background-color: #EAEAEA;" autocomplete="off" readonly></td>
                              <td nowrap="nowrap"><input type="text"  class="form-control form-control-sm <?=$nporecs;?> text-end " value="<?=$profit_loss;?>" style="<?=$isvalid;?>"<?=$isvalid;?>autocomplete="off" readonly></td>
							  <td style="display: none; border: none; " nowrap="nowrap"><input type="text" class="<?=$nporecs;?>" value="<?=$item_barcode;?>" autocomplete="off" readonly></td>
							  <td style="display: none; border: none; " nowrap="nowrap"><input type="text" class="<?=$nporecs;?>" value="<?=$promo_barcode;?>"autocomplete="off" readonly></td>
                             
                            </tr>
                            <?php 
                          
                          }
						  
                          
                          ?>
                        <?php endif;?>  
							<tr style="display:none;">
				
								
								<td>
								<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" onclick="jQuery(this).closest('tr').remove();" ><i class="bi bi-trash"></i></button>
									</button>
									<input type="hidden" value=""/>
									<input type="hidden" value=""/>
									<input type="hidden" value="Y"/>
									<input type="hidden" value=""/>
								</td>
								<td></td>
							
								<td><input type="text" size="15" class="mitemcode" value="" /></td> <!--itemcode-->
								<td><input type="text" size="40" value="" readonly /></td> <!--item desc-->
								<td><input type="text" size="10" value="" /></td> <!--QTY-->
								<!-- <td><input type="text" size="15" value=""  onkeypress="return __mysys_apps.numbersOnly(event)" onmouseover="javascript:__tamt_compute_totals();" onmouseout="javascript:__tamt_compute_totals();" onclick="javascript:__tamt_compute_totals();" onblur="javascript:__tamt_compute_totals();"/></td> ucost -->
								<td><input type="text" size="20" value="" id=""  /></td> <!--SRP-->
								<td><input type="text" size="20" value="" id="" readonly /></td> <!--Amount-->
								<td style="display: none; border: none; " nowrap="nowrap"><input type="text" size="17" value="" id="" class="mitemcode2"  /></td> <!--Promo Code-->
								<td><input type="text" size="20" value=""  class="spromo_price" id="spromo_price" readonly></td> <!--New Srp-->
								<td><input type="text" size="20" value="" id="" readonly /></td> <!--Amount-->
								<td><input type="text" class="text-end" size="20" value="" id="" readonly /></td> <!--Profit loss-->
								<td style="display: none;border:none;"><input type="text"  value="" id="" readonly /></td> <!--bar code-->
								<td style="display: none;border:none;"><input class="spromo_barcode" id="spromo_barcode"type="text"   value="" id="" readonly /></td> <!--promo bar code-->
								<td style="display: none;border:none;"><input type="text"  value="" id="" readonly /></td> <!--ucost-->
							</tr>      
			         
						</tbody>
					</table>
						<div class="row mt-2 mb-3"><!--  Save Records -->
								<div class="col-sm-4">
								<?php if(!empty($disable_ifapproved)):?>
									<button id="mbtn_mn_Save" type="button" style="background-color: #167F92; color: #FFF;" class="btn btn-dgreen btn-sm" disabled>Posted</button> 
								<?php else:?>
									<button id="mbtn_mn_Save" type="button" style="background-color: #167F92; color: #FFF;" class="btn btn-dgreen btn-sm" <?=$disable;?> <?=$disable_plus;?>>Save</button>   
								<?php endif;?>
								<button id="mbtn_mn_NTRX" type="button" class="btn btn-primary btn-sm">New Entry</button>
								</div>
						</div> <!-- end Save Records -->
				</div> <!-- end table-responsive -->
			</div> <!-- end tbl_items_ent -->
		</div>
		<div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title">Records</h6>
              <div class="pt-2 bg-dgreen mt-2" style="background-color: #167F92;"> 
               <nav class="nav nav-pills flex-column flex-sm-row  gap-1 px-2 fw-bold">
			   <a id="anchor-dashboard" class=" flex-sm-fill text-sm-center mytab-item  p-2 rounded-top active" href="#"><i class="bi bi-ui-radios"></i>Dashboard</a>
                <a id="anchor-list" class="flex-sm-fill text-sm-center mytab-item  p-2  rounded-top"  aria-current="page" href="#"> <i class="bi bi-ui-checks"> </i> Records</a>
				<a id="anchor-items" class=" flex-sm-fill text-sm-center mytab-item  p-2 rounded-top " href="#"><i class="bi bi-ui-radios" ></i> For Approval</a>
              </nav>
            </div>
            <!-- DISPLAY OF RECORDS AND APPROVAL -->
			<div id="packlist" class="text-center p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">

	</section> <!-- end section -->

	<?php
	  echo $mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
	  echo $mylibzsys->memypreloader01('mepreloaderme');
	  echo $mylibzsys->memsgbox1('memsgtestent','System Alert','...');
    ?>
</main>  <!-- end main -->

<script type="text/javascript"> 

__my_item_lookup2()

	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();
	});

	jQuery(document).ready(function() {
		jQuery('#tbl_PayData').on('change', 'tr.item-row input[type=text]', function() {
			refreshval(this);
			});
	});
   
  
	function refreshval(input) {
			var clonedRow = $(input).closest('tr'); // Use $(input) to access the element that triggered the function
			var xobjitemlastsrp = clonedRow.find('input[type=text]').eq(3).val();
			var xobjitemqty = clonedRow.find('input[type=text]').eq(2).val();
			var xobjitemamount = clonedRow.find('input[type=text]').eq(4);
			var xobjnewsrp = clonedRow.find('input[type=text]').eq(6).val();
			var xobjitemamount2 = clonedRow.find('input[type=text]').eq(7);
			var xobjprofitloss = clonedRow.find('input[type=text]').eq(8);
			xobjitemamount.val('');

			xobjitemamount2.val('');
			xobjprofitloss.val('');
			xobjitemamountval = xobjitemqty *xobjitemlastsrp;
			xobjitemamountval2 = xobjitemqty *xobjnewsrp;
			xobjprofitlossval = xobjitemamountval - xobjitemamountval2;
			xobjitemamount2.val(xobjitemamountval2);
			xobjitemamount.val(xobjitemamountval);
			xobjprofitloss.val(xobjprofitlossval);

		}
	


function getValue() {
			var totalQty = 0;
			var total_last_srp =0;
			var total_new_srp =0;
			var rowCount = jQuery('#tbl_PayData tr').length -1;
			for(aa = 1; aa < rowCount; aa++) { 
			var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone();
			var prof_loss = jQuery(clonedRow).find('input[type=text]').eq(8);
			var _qty = parseFloat(jQuery(clonedRow).find('input[type=text]').eq(2).val());
			var _srp = parseFloat(jQuery(clonedRow).find('input[type=text]').eq(3).val());
			var _amount = jQuery(clonedRow).find('input[type=text]').eq(4);
			var _total = (_qty * _srp)
			var _srp2 = parseFloat(jQuery(clonedRow).find('input[type=text]').eq(6).val());
			var _amount2 = jQuery(clonedRow).find('input[type=text]').eq(6);
			var _total2 = (_qty * _srp2)
			var _loss = (_total - _total2);

			_amount.val('');
			
			if (!isNaN(_qty)) {
				totalQty += _qty;
			}
			if (!isNaN(_srp)) {
				total_last_srp += _total;
			}if (!isNaN(_srp2)) {
				total_new_srp += _total2;
			}
			
			
			
			if (!isNaN(_qty)) {
				console.log(_amount);
				 _amount.val(_total);
				
				if (_srp2 !=0) {
					 _amount2.val(_total2);
					}
				
				prof_loss.val(_loss);
				$('#total_qty').val(totalQty);
				$('#total_last_srp').val(total_last_srp);
				$('#total_new_srp').val(total_new_srp);
				_amount.val('');
			}
				console.log(_srp2);
		
				} //end for
			} //end getvalue()


           
			$(function() {
				$("table").change(function(event) {
					getValue();
					});
				$("table").click(function(event) {
					getValue();	
					});
					$("table").mousedown(function(event) {
					getValue();	
					});
				
					
			})

			__my_item_lookup();
			__my_item_lookup2();

		
			$('#mbtn_mn_NTRX').click(function() { 
          var userselection = confirm("Are you sure you want to new transaction?");
          if (userselection == true){
            window.location = '<?=site_url();?>mypromo-spqd';
          }
          else{
            $.hideLoading();
            return false;
          } 
        });

	function my_add_line_item(){ 
		  try {
			var fld_area_id = jQuery('#branchName').data('mtknid');
			if(fld_area_id == ''){
			   alert('Please input Area Code/Branch first!!!');
			   $('#branchName').focus();
				return false;
			}
			
		   var rowCount = jQuery('#tbl_PayData tr').length;
		   var mid = __mysys_apps.__do_makeid(7) + (rowCount + 1);
		   var clonedRow = jQuery('#tbl_PayData tr:eq(' + (rowCount - 1) + ')').clone(); 
		   
		   jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id','mitemrid_' + mid);
		   jQuery(clonedRow).find('input[type=hidden]').eq(1).attr('id','mid_' + mid);
		   jQuery(clonedRow).find('input[type=hidden]').eq(2).attr('id','__me_tag' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','fld_mitemcode' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','fld_mitemdesc' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','fld_qty' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','fld_srp' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','fld_amount' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','fld_promocode' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(6).attr('id','fld_newsrp' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(7).attr('id','fld_newamount' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(8).attr('id','fld_profitloss' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(9).attr('id','fld_srp2' + mid);
		   jQuery(clonedRow).find('input[type=text]').eq(10).attr('id','fld_amount2' + mid);

		   jQuery('#tbl_PayData tr').eq(rowCount - 1).before(clonedRow);
		   jQuery(clonedRow).css({'display':''});
		   //__my_item_lookup();
		   //__my_promotim_lookup();
		   //__tamt_compute_totals();
		   var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
		   jQuery('#' + xobjArtItem).focus();
		   jQuery( '#tbl_PayData tr').each(function(i) { 
				   jQuery(this).find('td').eq(1).html(i);
		   });
           __my_item_lookup();
           __my_item_lookup2();
		   getValue();	
		   
	   } catch(err) { 
		   var mtxt = 'There was an error on this page.\\n';
		   mtxt += 'Error description: ' + err.message;
		   mtxt += '\\nClick OK to continue.';
		   alert(mtxt);
		   return false;
		   }  //end try 
	} //end my_add_line_item


    	
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

	$("#mbtn_mn_Save").click(function(e){
       
	   try { 
		getValue();	
		 //__mysys_apps.mepreloader('mepreloaderme',true);
		 var txt_spqd_trx_no = jQuery('#txt_spqd_trx_no').val();
		 var mtkn_mntr = jQuery('#__hmpromotrxnoid').val();
		 var branch_name = jQuery('#branch_name').val();
		 var data_mtknid = jQuery('#branch_name').attr('data-mtknid');
		 var rowCount = jQuery('#tbl_PayData tr').length -1;
		 var total_qty	 =	$('#total_qty').val();
		 var total_last_srp =$('#total_last_srp').val();
		 var total_new_srp	=$('#total_new_srp').val();
		 var startDate = jQuery('#start_date').val();
         var startTime = jQuery('#start_time').val();
         var endDate = jQuery('#end_date').val();
         var endTime = jQuery('#end_time').val();
		 var txt_spromo = jQuery('#txt_spromo').val();
		 var reason = $('#reason_txt').val();
		 var promo_disc = $('#txt_spromo').attr('txt_promo_price');
		 var adata1 = [];
		 var adata2 = [];

		 var mdata = '';
		 var ninc = 0;

			
		   for(aa = 1; aa < rowCount; aa++) { 
		   var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone();
		   var mitemc = jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
		   var mitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
		   var _qty = jQuery(clonedRow).find('input[type=text]').eq(2).val();
		   var _srp = jQuery(clonedRow).find('input[type=text]').eq(3).val();
		   var _amount = jQuery(clonedRow).find('input[type=text]').eq(4).val();
		   var _promocode = jQuery(clonedRow).find('input[type=text]').eq(5).val();
		   var _srp2 = jQuery(clonedRow).find('input[type=text]').eq(6).val();
		   var _amount2 = jQuery(clonedRow).find('input[type=text]').eq(7).val();
		   var _profitloss = jQuery(clonedRow).find('input[type=text]').eq(8).val();
		   var item_barcode = jQuery(clonedRow).find('input[type=text]').eq(9).val();
		   var promo_barcode = jQuery(clonedRow).find('input[type=text]').eq(10).val();
		   var mitemc_tkn = jQuery(clonedRow).find('input[type=text]').eq(0).attr('data-mtnkattr');  
		   var xobjitemucost = clonedRow.find('input[type=text]').eq(11).val();
	
		   
		  
		   mdata = mitemc + 'x|x' + mitemdesc + 'x|x' + _qty + 'x|x' + _srp + 'x|x' + _amount + 'x|x' + _promocode + 'x|x' + _srp2 + 'x|x' + _amount2 + 'x|x' + _profitloss + 'x|x'+  item_barcode + 'x|x' + promo_barcode + 'x|x'  + mitemc_tkn + 'x|x' + xobjitemucost;
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
			 startDate : startDate,
			 startTime : startTime,
			 endDate : endDate,
			 endTime : endTime,
			 txt_spqd_trx_no:txt_spqd_trx_no,
			 total_qty:total_qty,
			 total_last_srp:total_last_srp,
			 total_new_srp:total_new_srp,
			 mtkn_mntr: mtkn_mntr,
			 branch_name: branch_name,
			 mtkn_branch:data_mtknid,
			 item_barcode:item_barcode,
			 txt_spromo:txt_spromo,
			 reason: reason,
			 promo_disc:promo_disc,
			 adata1: adata1,
			 adata2: adata2
			 
			 
		   };  

		   console.log(mparam);
		   
		   $.ajax({ 
			 type: "POST",
			 url: '<?=site_url();?>me-spqd-save',
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
	 

	 __mysys_apps.mepreloader('mepreloaderme',false);
	jQuery('#anchor-list').on('click',function(){
		jQuery('#anchor-list').addClass('active');
		jQuery('#anchor-items').removeClass('active');
		jQuery('#anchor-dashboard').removeClass('active');
		
		var mtkn_whse = '';
		wg_trx_promo_view_recs(mtkn_whse);
	});

	function wg_trx_promo_view_recs(mtkn_whse){ 
		var ajaxRequest;
		ajaxRequest = jQuery.ajax({
			url: "<?=site_url();?>me-spqd-view",
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
	};  //end wg_trx_promo_view_recs
	
	$('#anchor-items').on('click',function(){
    $('#anchor-items').addClass('active');
	$('#anchor-dashboard').removeClass('active');
    $('#anchor-list').removeClass('active');
    var mtkn_whse = '';
    mypack_view_appr(mtkn_whse);

	

  });
  
		function mypack_view_appr(mtkn_whse){ 
			var ajaxRequest;

			ajaxRequest = jQuery.ajax({
			url: "<?=site_url();?>me-spqd-view-appr",
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



		//for dashboard
	$('#anchor-dashboard').on('click',function(){
    $('#anchor-dashboard').addClass('active');
	$('#anchor-items').removeClass('active');
    $('#anchor-list').removeClass('active');
    var mtkn_whse = '';
    mypack_dashboard_appr(mtkn_whse);

	

  });

		function mypack_dashboard_appr(mtkn_whse){ 
			var ajaxRequest;

			ajaxRequest = jQuery.ajax({
			url: "<?=site_url();?>me-spqd-dashboard",
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
			console.log(mtkn_whse);
		};


		function __my_item_lookup() {
			jQuery('.mitemcode')
				.bind('keydown', function(event) {
				if (
					event.keyCode === jQuery.ui.keyCode.TAB &&
					jQuery(this).data('autocomplete').menu.active
				) {
					event.preventDefault();
				}
				if (event.keyCode === jQuery.ui.keyCode.TAB) {
					event.preventDefault();
				}
				})
				.autocomplete({
				minLength: 0,
				source: '<?= site_url(); ?>get-promo-itemc',
				focus: function() {
					return false;
				},
				select: function(event, ui) {
					var terms = ui.item.value;
					jQuery(this).attr('alt', jQuery.trim(ui.item.value));
					jQuery(this).attr('title', jQuery.trim(ui.item.value));
					var ndisc = jQuery('#txt_promodiscval').val();
					this.value = ui.item.value;

					// Clear existing values
					var clonedRow = jQuery(this).closest('tr');
					var xobjitemdesc = clonedRow.find('input[type=text]').eq(1);
					var xobjitemquantity = clonedRow.find('input[type=text]').eq(2).val();
					var xobjitemprice = clonedRow.find('input[type=text]').eq(3);
					var xobjitemamount = clonedRow.find('input[type=text]').eq(4);
					var xobjitembarcode = clonedRow.find('input[type=text]').eq(9);
					var xobjitemucost = clonedRow.find('input[type=text]').eq(11);
				
					
					xobjitemdesc.val('');
					xobjitemprice.val('');
					xobjitembarcode.val('');
					xobjitemamount.val('');
					
					
					var xobjitemprice = clonedRow.find('input[type=text]').eq(3);
					var newprice = parseFloat(ui.item._UPRICE);
					var barcode_ = ui.item._BARCODE;
					jQuery(this).attr('data-mtnkattr', ui.item.mtkn_rid);
					xobjitemdesc.val(ui.item._DESC);
					xobjitembarcode.val(ui.item._BARCODE);
					xobjitemprice.val(ui.item._UPRICE);
					xobjitemucost.val(ui.item._UCOST);
					var _amountval = parseFloat(xobjitemquantity) * parseFloat(newprice);
					xobjitemamount.val(_amountval);
					xobjitemdesc.attr('data-barcode', barcode_);
					console.log(xobjitemquantity);
					console.log(xobjitemprice);

					return false;
				}
				})
				.click(function() {
				var terms = this.value;
				jQuery(this).autocomplete('search', jQuery.trim(terms));
				});
			}


			function __my_item_lookup2() {
				jQuery('.mitemcode2')
					.bind('keydown', function(event) {
			if (
				event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery(this).data('autocomplete').menu.active
			) {
				event.preventDefault();
			}
			if (event.keyCode === jQuery.ui.keyCode.TAB) {
				event.preventDefault();
			}
			})
			.autocomplete({
			minLength: 0,
			source: '<?= site_url(); ?>promo_search',
			focus: function() {
				return false;
			},
			select: function(event, ui) {
				var terms = ui.item.value;
				jQuery(this).attr('alt', jQuery.trim(ui.item.value));
				jQuery(this).attr('title', jQuery.trim(ui.item.value));
				var ndisc = jQuery('#txt_promodiscval').val();
				this.value = ui.item.value;

				// Clear existing values
				var rowCount = jQuery('#tbl_PayData tr').length -1;
				for(aa = 1; aa < rowCount; aa++) { 
				var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone();
				var itemqty = clonedRow.find('input[type=text]').eq(2).val();
				var xobjitemprice = clonedRow.find('input[type=text]').eq(5);
				var xobjitempriceval = clonedRow.find('input[type=text]').eq(5).val();
				var amount = clonedRow.find('input[type=text]').eq(4);
				var amount2 = clonedRow.find('input[type=text]').eq(6);
				var pfloss = clonedRow.find('input[type=text]').eq(7);
				var promobarcode = clonedRow.find('input[type=text]').eq(10);
				$('.spromo_price').val(ui.item.pro_code_disc)
				$('.spromo_barcode').val(ui.item.promo_barcode)
				jQuery('#txt_spromo').attr('txt_promo_price',ui.item.pro_code_disc);
	
		
				jQuery(this).attr('data-mtnkattr', ui.item.mtkn_rid);

		
				xobjitemprice.val(ui.item.pro_code_disc);
				promobarcode.val();
				amountval = itemqty * xobjitempriceval;
				pflossval = amount - amountval;
				getValue();
				return false;
				}
			}
			})
			.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
			});
			
		}

function save_for_approve_spqd(mtkn_recid,spqd_trxno,art_code,item_bcode){
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
		 let per_line_save = false;
		 var txt_spqd_trx_no = jQuery('#txt_spqd_trx_no').val();
		 var mtkn_mntr = jQuery('#__hmpromotrxnoid').val();
		 var branch_name = jQuery('#branch_name').val();
		 var data_mtknid = jQuery('#branch_name').attr('data-mtknid');
		 var rowCount = jQuery('#tbl_PayData tr').length -1;
		 var total_qty	 =	$('#total_qty').val();
		 var total_last_srp =$('#total_last_srp').val();
		 var total_new_srp	=$('#total_new_srp').val();
		 var startDate = jQuery('#start_date').val();
         var startTime = jQuery('#start_time').val();
         var endDate = jQuery('#end_date').val();
         var endTime = jQuery('#end_time').val();
		 var txt_spromo = jQuery('#txt_spromo').val();
		 var reason = $('#reason_txt').val();
		 var adata1 = [];
		 var adata2 = [];

		 var mdata = '';
		 var ninc = 0;

			
		   for(aa = 1; aa < rowCount; aa++) { 
		   var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone();
		   var mitemc = jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
		   var mitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
		   var _qty = jQuery(clonedRow).find('input[type=text]').eq(2).val();
		   var _srp = jQuery(clonedRow).find('input[type=text]').eq(3).val();
		   var _amount = jQuery(clonedRow).find('input[type=text]').eq(4).val();
		   var _promocode = jQuery(clonedRow).find('input[type=text]').eq(5).val();
		   var _srp2 = jQuery(clonedRow).find('input[type=text]').eq(6).val();
		   var _amount2 = jQuery(clonedRow).find('input[type=text]').eq(7).val();
		   var _profitloss = jQuery(clonedRow).find('input[type=text]').eq(8).val();
		   var item_barcode = jQuery(clonedRow).find('input[type=text]').eq(9).val();
		   var promo_barcode = jQuery(clonedRow).find('input[type=text]').eq(10).val();
		   var mitemc_tkn = jQuery(clonedRow).find('input[type=text]').eq(0).attr('data-mtnkattr');  
		   var xobjitemucost = clonedRow.find('input[type=text]').eq(11).val();
	
		   
		  
		   mdata = mitemc + 'x|x' + mitemdesc + 'x|x' + _qty + 'x|x' + _srp + 'x|x' + _amount + 'x|x' + _promocode + 'x|x' + _srp2 + 'x|x' + _amount2 + 'x|x' + _profitloss + 'x|x'+  item_barcode + 'x|x' + promo_barcode + 'x|x'  + mitemc_tkn + 'x|x' + xobjitemucost;
		   adata1.push(mdata);
		   var mdata = jQuery(clonedRow).find('input[type=hidden]').eq(0).val();
		   adata2.push(mitemc_tkn);
		   ninc = 1;
		   }
          
		
                    var mparam = {
                        mtkn_recid: mtkn_recid,
						item_bcode:item_bcode,
						spqd_trxno:spqd_trxno,
						per_line_save:per_line_save,
						startDate : startDate,
						startTime : startTime,
						endDate : endDate,
						endTime : endTime,
						txt_spqd_trx_no:txt_spqd_trx_no,
						total_qty:total_qty,
						total_last_srp:total_last_srp,
						total_new_srp:total_new_srp,
						mtkn_mntr: mtkn_mntr,
						branch_name: branch_name,
						mtkn_branch:data_mtknid,
						item_barcode:item_barcode,
						txt_spromo:txt_spromo,
						reason: reason,
						adata1: adata1,
						adata2: adata2
                    }; 
                   //console.log(mparam);
                  jQuery.ajax({ // default declaration of ajax parameters
                    type: "POST",
                    url: '<?=site_url();?>me-spqd-appr-save',
                    context: document.body,
                    data: eval(mparam),
                    global: false,
                    cache: false,

                    success: function(data)  { //display html using divID
                        __mysys_apps.mepreloader('mepreloaderme',false);
                        jQuery('#memsgtestent_bod').html(data);
           				jQuery('#memsgtestent').modal('show');
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
            alert(mtxt);
             __mysys_apps.mepreloader('mepreloaderme',false);
            return false;
        }  //end try            
	}
	function save_for_approve_spqd_perline(mtkn_recid,spqd_trxno,art_code,item_bcode){
		try { 
			let per_line_save = true;
			var txt_spqd_trx_no = jQuery('#txt_spqd_trx_no').val();
		 var mtkn_mntr = jQuery('#__hmpromotrxnoid').val();
		 var branch_name = jQuery('#branch_name').val();
		 var data_mtknid = jQuery('#branch_name').attr('data-mtknid');
		 var rowCount = jQuery('#tbl_PayData tr').length -1;
		 var total_qty	 =	$('#total_qty').val();
		 var total_last_srp =$('#total_last_srp').val();
		 var total_new_srp	=$('#total_new_srp').val();
		 var startDate = jQuery('#start_date').val();
         var startTime = jQuery('#start_time').val();
         var endDate = jQuery('#end_date').val();
         var endTime = jQuery('#end_time').val();
		 var txt_spromo = jQuery('#txt_spromo').val();
		 var reason = $('#reason_txt').val();
		 var adata1 = [];
		 var adata2 = [];

		 var mdata = '';
		 var ninc = 0;

			
		   for(aa = 1; aa < rowCount; aa++) { 
		   var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone();
		   var mitemc = jQuery(clonedRow).find('input[type=text]').eq(0).val(); 
		   var mitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); 
		   var _qty = jQuery(clonedRow).find('input[type=text]').eq(2).val();
		   var _srp = jQuery(clonedRow).find('input[type=text]').eq(3).val();
		   var _amount = jQuery(clonedRow).find('input[type=text]').eq(4).val();
		   var _promocode = jQuery(clonedRow).find('input[type=text]').eq(5).val();
		   var _srp2 = jQuery(clonedRow).find('input[type=text]').eq(6).val();
		   var _amount2 = jQuery(clonedRow).find('input[type=text]').eq(7).val();
		   var _profitloss = jQuery(clonedRow).find('input[type=text]').eq(8).val();
		   var item_barcode = jQuery(clonedRow).find('input[type=text]').eq(9).val();
		   var promo_barcode = jQuery(clonedRow).find('input[type=text]').eq(10).val();
		   var mitemc_tkn = jQuery(clonedRow).find('input[type=text]').eq(0).attr('data-mtnkattr');  
		   var xobjitemucost = clonedRow.find('input[type=text]').eq(11).val();
	
		   
		  
		   mdata = mitemc + 'x|x' + mitemdesc + 'x|x' + _qty + 'x|x' + _srp + 'x|x' + _amount + 'x|x' + _promocode + 'x|x' + _srp2 + 'x|x' + _amount2 + 'x|x' + _profitloss + 'x|x'+  item_barcode + 'x|x' + promo_barcode + 'x|x'  + mitemc_tkn + 'x|x' + xobjitemucost;
		   adata1.push(mdata);
		   var mdata = jQuery(clonedRow).find('input[type=hidden]').eq(0).val();
		   adata2.push(mitemc_tkn);
		   ninc = 1;
		   }
		
                    var mparam = {
                        mtkn_recid: mtkn_recid,
						art_code:art_code,
						item_bcode:item_bcode,
						spqd_trxno:spqd_trxno,
						per_line_save:per_line_save,
						startDate : startDate,
						startTime : startTime,
						endDate : endDate,
						endTime : endTime,
						txt_spqd_trx_no:txt_spqd_trx_no,
						total_qty:total_qty,
						total_last_srp:total_last_srp,
						total_new_srp:total_new_srp,
						mtkn_mntr: mtkn_mntr,
						branch_name: branch_name,
						mtkn_branch:data_mtknid,
						item_barcode:item_barcode,
						txt_spromo:txt_spromo,
						reason: reason,
						adata1: adata1,
						adata2: adata2
                    }; 
                   //console.log(mparam);
                  jQuery.ajax({ // default declaration of ajax parameters
                    type: "POST",
                    url: '<?=site_url();?>me-spqd-appr-save',
                    context: document.body,
                    data: eval(mparam),
                    global: false,
                    cache: false,

                    success: function(data)  { //display html using divID
                        __mysys_apps.mepreloader('mepreloaderme',false);
                        jQuery('#memsgtestent_bod').html(data);
           				jQuery('#memsgtestent').modal('show');
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
            alert(mtxt);
             __mysys_apps.mepreloader('mepreloaderme',false);
            return false;
        }  //end try            
	}
	
	

    
</script>