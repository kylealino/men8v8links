<?php
/* =================================================
 * Author      : Oliver Sta Maria 
 * Date Created: December 21, 2022
 * Module Desc : Branch Pullout Module
 * File Name   : transactions/pullout/pullout-trx.php
 * Revision    : Migration to Php8 Compatability 
*/
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');

$mylibzdb = model('App\Models\MyLibzDBModel');
$mydatum = model('App\Models\MyDatumModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myusermod = model('App\Models\MyUserModel');

$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();


$mtkn_trxno = $request->getVar('mtkn_trxno');
$adftag = $mydatum->lk_Active_DF($mydbname->medb(0));
$txtdf_tag= '';
$txtpotrx_no = '';
$txtcomp = '';
$txtarea_code = '';
$txtsupplier = '';
$txtpono = '';
$txtpodate = '';
$txtrems = '';
$mmnhd_rid ='';
$nmnrecs = 0;
$txtsubtqty='';
$txtsubtcost='';
$txtsubtamt='';
$apout_rson = $mydatum->lk_manrecs_pout_rson($mydbname->medb(0));
$txtpout_rson = '';

$apout_typ = $mydatum->lk_manrecs_pout_typ($mydbname->medb(0));
$txtpout_typ = '';

$txtpotobrnc='';
$txtimsno ='';
$txtarea_id ='';
$dis3 ='';

if(!empty($mtkn_trxno)) { 
    $str = "select aa.*,
        bb.COMP_NAME,
        cc.BRNCH_NAME,
        ee.BRNCH_NAME BRNCH_NAME2,
        dd.VEND_NAME,
        sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_trxtr 
        from {$mydbname->medb(0)}.`trx_manrecs_po_hd` aa
        LEFT JOIN {$mydbname->medb(0)}.`mst_company` bb
        ON (aa.`comp_id` = bb.`recid`)
        LEFT JOIN {$mydbname->medb(0)}.`mst_companyBranch` cc
        ON (aa.`branch_id` = cc.`recid`)
        LEFT JOIN {$mydbname->medb(0)}.`mst_companyBranch` ee
        ON (aa.`hd_pfrom_id` = ee.`recid`)
        LEFT JOIN {$mydbname->medb(0)}.`mst_vendor` dd
        ON (aa.`supplier_id` = dd.`recid`)
        where sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$mtkn_trxno' ";
    $qq = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    $rw = $qq->getRowArray();
    $mmnhd_rid = $rw['mtkn_trxtr'];
    $txtpotrx_no = $rw['potrx_no'];
    $txtcomp = $rw['COMP_NAME'];
    $txtarea_code = $rw['BRNCH_NAME'];
    $txtsupplier = $rw['VEND_NAME'];
    $txtdf_tag= $rw['df_tag'];
    $txtpono = $rw['po_no'];
    $txtimsno = $rw['ims_no'];
    $txtpodate = substr($rw['po_date'],0,10);
    $txtrems = $rw['rems'];
    $txtpout_rson = $rw['po_rsons_id'];
    $txtsubtqty= number_format($rw['hd_subtqty'],2,'.','');
    $txtsubtcost= number_format($rw['hd_subtcost'],2,'.','');
    $txtsubtamt= number_format($rw['hd_subtamt'],2,'.','');
    $txtpotobrnc = $rw['BRNCH_NAME2'];
    $dis3 = (($rw['post_tag'] == 'Y') ? "disabled" : '');
    $txtarea_id = $rw['branch_id'];
    $txtpout_typ = $rw['po_type'];
    $qq->freeResult();
    
}

$str_style='';
$view = $myusermod->get_Active_menus($mydbname->medb(0),$cuser,"myuaacct_id='65'","myua_acct");
$view2 = $myusermod->get_Active_menus($mydbname->medb(0),$cuser,"myuaacct_id='76'","myua_acct");
$cuserrema=$myusermod->mysys_userrema();
 
if($cuserrema ==='B') {
    $str_style=" style=\"display:none;\"";
    $str_dis="readonly";
}
elseif($view != 1){
    $str_style='';
    $str_dis="readonly";
}
else{
    $str_style='';
    $str_dis="";
}
$str_style=" style=\"display:none;\"";


?>
<main id="main" class="main">
	<div class="pagetitle">
		<h1>Pull Out</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Home</a></li>
				<li class="breadcrumb-item">Transaction</li>
				<li class="breadcrumb-item active">Pull Out</li>
			</ol>
		</nav>
	</div> <!-- End Page Title -->
	<section class="section">
		<?=form_open('pullout-trx','class="row needs-validation" id="myfrmsrec_pullout" ');?>
		<div class="row metblentry-font">
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>PO Transaction No.:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" id="txtpotrx_no" name="txtpotrx_no" class="form-control form-control-sm" value="<?=$txtpotrx_no;?>" readonly />
						<input type="hidden" name="__hmtkn_trxnoid" id="__hmtkn_trxnoid" value="<?= $mmnhd_rid;?>" />
					</div>
				</div> <!-- end PO Transaction No. -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Company Name:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" data-id="" id="fld_Company_po" name="fld_Company_po" value="<?=$txtcomp;?>" required />
					</div>
				</div> <!-- end Company Name -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Branch Name:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" id="fld_area_code_po" name="fld_area_code_po" value="<?=$txtarea_code;?>" required />
						<input type="hidden" id="fld_area_id" name="fld_area_id" value="<?=$txtarea_id;?>" />
					</div>
				</div> <!-- end Branch Name -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Supplier:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm fld_supplier_po" id="fld_supplier_po" name="fld_supplier_po" value="<?=$txtsupplier;?>" required/>
					</div>
				</div> <!-- end Supplier -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Reason:</span>
					</div>
					<div class="col-sm-9">
						<?=$mylibzsys->mypopulist_2($apout_rson,$txtpout_rson,'fld_rson','class="form-control form-control-sm" ','','');?>
					</div>
				</div> <!-- end Reason -->
				<!-- display totlals -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Total Actual Qty:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" name="fld_subtqty" id="fld_subtqty" value="<?=$txtsubtqty;?>" readonly />
					</div>
				</div> <!-- end Total Actual Qty -->
				<div class="row mb-3" <?=$str_style;?>>
					<div class="col-sm-3">
						<span class="fw-bold">Total Cost:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm input-sm" name="fld_subtcost" id="fld_subtcost" value="<?=$txtsubtcost;?>" readonly />
					</div>
				</div> <!-- end Total Cost -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Total SRP:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" name="fld_subtamt" id="fld_subtamt" value="<?=$txtsubtamt;?>" readonly />
					</div>
				</div> <!-- end Total SRP -->
				
			</div> <!-- end left layout entry -->
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Pull Out Type:</span>
					</div>
					<div class="col-sm-9">
						<?=$mylibzsys->mypopulist_2($apout_typ,$txtpout_typ,'fld_ptyp','class="form-control form-control-sm" ','','');?>
					</div>
				</div> <!-- end Pull Out Type -->
				<div id="POTOB" class="row mb-3" style ="display:none;">
					<div class="col-sm-3">
						<span>Pullout To Other Branch:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" id="fld_potobrnc" name="fld_potobrnc" value="<?=$txtpotobrnc;?>" />
					</div>
				</div> <!-- end Pullout To Other Branch -->
				<div id="hPOA" class="row mb-3">
					<div class="col-sm-3">
						<span>POA#:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" id="fld_pono" name="fld_pono" value="<?=$txtpono;?>"/>
					</div>
				</div> <!-- end POA# -->
				<div id="hIMS" class="row mb-3">
					<div class="col-sm-3">
						<span>IMS#:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" id="fld_imsno" name="fld_imsno" value="<?=$txtimsno;?>"/>
					</div>
				</div> <!-- end IMS# -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Pullout Date:</span>
					</div>
					<div class="col-sm-9">
						<input min="2015-01-01" placeholder="mm/dd/yyyy" type="date" class="form_datetime form-control form-control-sm" name="fld_podate" id="fld_podate" value="<?=$txtpodate;?>" required/>
					</div>
				</div> <!-- end Pullout Date -->
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Remarks:</span>
					</div>
					<div class="col-sm-9">
						<textarea class="form-control form-control-sm" rows="5" name="fld_rems" id="fld_rems"><?=$txtrems;?></textarea>
					</div>
				</div> <!-- end Remarks -->
				
			</div> <!-- end right layout entry --> 
		</div> <!-- end row metblentry-font-->
		<div class="row mb-3">
			<div class="col-sm-12" id="tbl_items_ent">
				<input type="hidden" id="metagged_itmremove" value=""/>
				<div class="table-responsive">
					<table id="tbl_PayData" class="table-striped table-hover table-bordered table-sm" style="font-size: 0.8rem !important;">  <!-- tbl_pullout -->
						<?php 
						//trade records 
						if(($txtpout_typ == 'T') || ($txtpout_typ == '')) { 
						?>
						<thead>
							<th></th>
							<th class="text-center">
								<button type="button" class="btn btn-sm text-success" onclick="javascript:my_add_line_item('<?=$txtpout_typ;?>');" >
									<i class="ri-add-box-fill"></i>
								</button>
							</th>
							<th>Itemcode</th>
							<th>Description</th>
							<th>Packaging</th>
							<th <?=$str_style;?>>Unit Cost</th>
							<th <?=$str_style;?>>Total Cost</th>
							<th>SRP</th>
							<th>Total SRP</th>
							<th>Quantity</th>
							<th>Particulars/Remarks</th>
							<th style ="display:none;" class="frmmitemcoderow">From Itemcode</th>
						</thead>
						<tbody id="contentArea"> 
						<?php 
						if(!empty($mmnhd_rid)) { 
						   $str = "
								SELECT
									a.*,
									SHA2(CONCAT(a.`recid`,'{$mpw_tkn}'),384) mtkn_mndttr,
									SHA2(CONCAT(b.`recid`,'{$mpw_tkn}'),384) mtkn_artmtr,
									SHA2(CONCAT(a.`frmmat_rid`,'{$mpw_tkn}'),384) mtkn_frmartmtr,
									IFNULL(b.`ART_CODE`,a.`mat_code`) ART_CODE, 
									IFNULL(b.`ART_DESC`,'') ART_DESC,
									IFNULL(b.`ART_SKU`,'') ART_SKU,
									IFNULL(b.`ART_UCOST`,'') ART_UCOST,
									IFNULL(b.`ART_UPRICE`,'') ART_UPRICE
								FROM
									{$mydbname->medb(0)}.`trx_manrecs_po_dt` a
								LEFT JOIN 
									{$mydbname->medb(0)}.`mst_article` b
								ON
									a.`mat_rid` = b.`recid`
							   WHERE
									sha2(concat(a.`mrhd_rid`,'{$mpw_tkn}'),384) = '{$mmnhd_rid}'
								ORDER BY 
									a.`recid`
							";
							$qdt = $mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							//var_dump($str);
							foreach($qdt->getResultArray() as $rdt) {  
								$nmnrecs++;
								$txtpout_rson = $rdt['pout_rson_rid'];
								//CONDITION PARA SA TRADE AT NON TRADE  YUNG TRADE KASI HINDI KASAMA SA INVENTORY KAYA IBA LAGAYAN NG QTY
								$txtpout_dates = $rw['po_date'];

								if(($txtpout_rson == 5) && ($txtpout_typ == 'T')){//TRADE
									
									$str = "SELECT DATE('$txtpout_dates') <= '2021-11-01' __DATE_VALID"; //OLD TRX
									$q = $mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
									if($q->getNumRows() > 0){
										$r = $q->getRowArray();
										$__DATE_VALID = $r['__DATE_VALID'];
										if($__DATE_VALID == '1'){
											$str_ptyp = $rdt['qty'];
										}
										else{
											$str_ptyp = $rdt['qty_encd'];
										}
									}///NUMROWA
									
								}///TRADE
								else if(($txtpout_rson != 5) && ($txtpout_typ == 'T')){//TRADE//NONTRADE
									$str_ptyp = $rdt['qty'];
								}
								else{
									 $str_ptyp = $rdt['qty_encd'];
								}
								?>
							<tr>
								<td><?=$nmnrecs;?></td>
								<td>
									<button class="btn btn-sm btn_remove text-danger" type="button" onclick="javascript:__mn_items_drecs('<?=$rdt['mtkn_mndttr'];?>','<?=$mmnhd_rid;?>');" <?=$dis3;?>>
										<i class="bi bi-trash" ></i>
									</button>
									<input type="hidden" id="mitemrid_<?=$nmnrecs;?>" value="<?=$rdt['mtkn_artmtr'];?>"/> <!--id-->
									<input type="hidden" id="mid_<?=$nmnrecs;?>" value="<?=$rdt['mtkn_mndttr'];?>"/>
									<input type="hidden" id="me_tag<?=$nmnrecs;?>" value="<?=$nmnrecs;?>"/>
									<input type="hidden" id="frmmitemrid_<?=$nmnrecs;?>" value="<?=$rdt['mtkn_frmartmtr'];?>"/>
								</td>
								<td><input type="text" id="fld_mitemcode_<?=$nmnrecs;?>" size="20" class="mitemcode form-control form-control-sm" value="<?=$rdt['ART_CODE'];?>" onchange="javascript:__my_item_onchange(this);"/></td> <!--itemcode-->
								<td><input type="text" id="fld_mitemdesc_<?=$nmnrecs;?>" size="40" class="form-control form-control-sm" value="<?=$rdt['ART_DESC'];?>" readonly /></td> <!--item desc-->
								<td><input type="text" id="fld_mitempkg_<?=$nmnrecs;?>" size="5" class="form-control form-control-sm" value="<?=$rdt['ART_SKU'];?>" readonly /></td> <!--packaging-->
								<td <?=$str_style;?>><input type="text" id="fld_ucost_<?=$nmnrecs;?>" size="15" class="form-control form-control-sm" value="<?=$rdt['ucost'];?>" onchange="javascript:__my_item_onchange(this);" <?=$str_dis;?>/></td> <!--ucost-->
								<td <?=$str_style;?>><input type="text" id="fld_mitemtcost_<?=$nmnrecs;?>" size="15"  class="form-control form-control-sm" value="<?=$rdt['tcost'];?>" readonly required/></td> <!--tcost-->
								<td><input type="text" id="fld_srp_<?=$nmnrecs;?>" size="15" class="form-control form-control-sm" value="<?=$rdt['uprice'];?>" onchange="javascript:__my_item_onchange(this);" <?=$str_dis;?>/></td> <!--srp-->
								<td><input type="text" id="fld_mitemtamt_<?=$nmnrecs;?>" size="15" class="form-control form-control-sm" value="<?=$rdt['tamt'];?>" readonly /></td> <!--tamt-->
								<td><input type="text" id="fld_mitemqty_<?=$nmnrecs;?>" size="15" class="form-control form-control-sm" value="<?=$str_ptyp;?>" onmouseover="javascript:__tamt_compute_totals();" onmouseout="javascript:__tamt_compute_totals();" onclick="javascript:__tamt_compute_totals();" onblur="javascript:__tamt_compute_totals();" onchange="javascript:__my_item_onchange(this);" /></td> <!--qty rcvd-->
								<td><input type="text" id="fld_remks" size="30" class="form-control form-control-sm" value="<?=$rdt['nremarks'];?>" onchange="javascript:__my_item_onchange(this);" /></td> <!--remks-->
								<td style ="display:none;" class="frmmitemcoderow"><input type="text" id="fld_frmmitemcode_<?=$nmnrecs;?>" size="20" class="frmmitemcode" value="<?=$rdt['frmmat_code'];?>" onchange="javascript:__my_item_onchange(this);"/></td> <!--frmitemcode-->
							</tr> 
								<?php
							} //end for
							$qdt->freeResult();
							} //end if mmnhd_rid validation 
						}  //end trade 
						elseif ($txtpout_typ == 'N') { //non-trade
						?>
						
						<thead>
							<th> </th>
							<th width="20px" class="text-center">
								 <button type="button" class="btn bg-blue btn-sm" onclick="javascript:my_add_line_item('<?=$txtpout_typ;?>');" >
									<i class="bi bi-plus-lg"></i>
								</button>
							</th>
							<th style ="display:none;">Itemcode</th>
							<th>Particulars/Remarks</th>
							<th>Quantity</th>
							<th style ="display:none;" class="frmmitemcode">From Itemcode</th>
						</thead>
						<tbody id="contentArea">
							<?php
								if(!empty($mmnhd_rid)) { 
								$str = "
									SELECT
										a.*,
										SHA2(CONCAT(a.`recid`,'{$mpw_tkn}'),384) mtkn_mndttr,
										SHA2(CONCAT(b.`recid`,'{$mpw_tkn}'),384) mtkn_artmtr,
										 SHA2(CONCAT(a.`frmmat_rid`,'{$mpw_tkn}'),384) mtkn_frmartmtr,
										IFNULL(b.`ART_CODE`,a.`mat_code`) ART_CODE, 
										IFNULL(b.`ART_DESC`,'') ART_DESC,
										IFNULL(b.`ART_SKU`,'') ART_SKU,
										IFNULL(b.`ART_UCOST`,'') ART_UCOST,
										IFNULL(b.`ART_UPRICE`,'') ART_UPRICE
									FROM
										{$mydbname->medb(0)}.`trx_manrecs_po_dt` a
									LEFT JOIN 
										{$mydbname->medb(0)}.`mst_article` b
									ON
										a.`mat_rid` = b.`recid`
								   WHERE
										sha2(concat(a.`mrhd_rid`,'{$mpw_tkn}'),384) = '{$mmnhd_rid}'
									ORDER BY 
										a.`recid`
								";
						
								$qdt = $mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
								//var_dump($str);
								foreach($qdt->getResultArray() as $rdt) { 
									$nmnrecs++;
									$txtpout_rson = $rdt['pout_rson_rid'];
									//CONDITION PARA SA TRADE AT NON TRADE  YUNG TRADE KASI HINDI KASAMA SA INVENTORY KAYA IBA LAGAYAN NG QTY
									$txtpout_dates = $rw['po_date'];

								   if(($txtpout_rson == 5) && ($txtpout_typ == 'T')){//TRADE
										
										$str = "SELECT DATE('$txtpout_dates') <= '2021-11-01' __DATE_VALID";//OLD TRX
										$q = $mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
										if($q->getNumRows() > 0){
											$r = $q->getRowArray();
											$__DATE_VALID = $r['__DATE_VALID'];
											if($__DATE_VALID == '1'){
												$str_ptyp = $rdt['qty'];
											}
											else{
												$str_ptyp = $rdt['qty_encd'];
											}

											
										}///NUMROWA
										
									}///TRADE
									else if(($txtpout_rson != 5) && ($txtpout_typ == 'T')){//TRADE//NONTRADE
										$str_ptyp = $rdt['qty'];
									}
									else{//NONTRADE
										$str_ptyp = $rdt['qty_encd'];
									}

								?>
							<tr>
								<td><?=$nmnrecs;?></td>
								<td>
									<button class="btn btn-sm btn_remove bg-red" type="button" onclick="javascript:__mn_items_drecs('<?=$rdt['mtkn_mndttr'];?>','<?=$mmnhd_rid;?>');" <?=$dis3;?>>
										<i class="fas fa-trash-alt" ></i>
									</button>
									<input type="hidden" id="mitemrid_<?=$nmnrecs;?>" value="<?=$rdt['mtkn_artmtr'];?>"/> <!--id-->
									<input type="hidden" id="mid_<?=$nmnrecs;?>" value="<?=$rdt['mtkn_mndttr'];?>"/>
									<input type="hidden" id="me_tag<?=$nmnrecs;?>" value="<?=$nmnrecs;?>"/>
									<input type="hidden" id="frmmitemrid_<?=$nmnrecs;?>" value="<?=$rdt['mtkn_frmartmtr'];?>"/>
								</td>
								<td style ="display:none;"><input  type="text" id="fld_mitemcode_<?=$nmnrecs;?>" size="20" value="<?=$rdt['ART_CODE'];?>"/></td> <!--itemcode-->
								<td><input type="text" id="fld_remks" size="60" value="<?=$rdt['nremarks'];?>" onchange="javascript:__my_item_onchange(this);" /></td> <!--remks-->
								<td><input type="text" id="fld_mitemqty_<?=$nmnrecs;?>" size="15" class="form-control form-control-sm" value="<?=$str_ptyp;?>" onmouseover="javascript:__tamt_compute_totals('<?=$txtpout_typ;?>');" onmouseout="javascript:__tamt_compute_totals('<?=$txtpout_typ;?>');" onclick="javascript:__tamt_compute_totals('<?=$txtpout_typ;?>');" onblur="javascript:__tamt_compute_totals('<?=$txtpout_typ;?>');" onchange="javascript:__my_item_onchange(this);" /></td> <!--qty rcvd-->
								<td style ="display:none;" class="frmmitemcoderow"><input type="text" id="fld_frmmitemcode_<?=$nmnrecs;?>" size="20" class="frmmitemcode" value="<?=$rdt['frmmat_code'];?>" onchange="javascript:__my_item_onchange(this);"/></td> <!--frmitemcode-->
							</tr>
							<?php 
							}  //end for 
							?>
							<?php 
							} //end non-trade
							$qdt->freeResult();	
							} //end if mmnhd_rid validation non-trade 
							?>						
							<tr style="display:none;">
								<td></td>
								<td>
									<button type="button" class="btn text-danger btn-sm btn_remove nullvaluethis" onclick="javascript:confirmalert(this);">
									<i class="bi bi-x-circle"></i>
									</button>
									<input type="hidden" value=""/>
									<input type="hidden" value=""/>
									<input type="hidden" value="Y"/>
									<input type="hidden" value=""/>
								</td>
								<td><input type="text" size="20" class="mitemcode form_cust fform_cust-sm" value="" /></td> <!--itemcode-->
								<td><input type="text" size="40" value="" readonly /></td> <!--item desc-->
								<td><input type="text" size="5" value="" readonly /></td> <!--packaging-->
								<td <?=$str_style;?>><input type="text" class="text-end" size="15" value="" <?=$str_dis;?>/></td> <!--ucost-->
								<td <?=$str_style;?>><input type="text" class="text-end" size="15" value="" readonly /></td> <!--tcost-->
								<td><input type="text" size="15" class="text-end" value="" <?=$str_dis;?>/></td> <!--srp-->
								<td><input type="text" size="15" class="text-end" value="" readonly /></td> <!--tamt-->
								<td><input type="text" size="7" class="text-end" value="" onkeypress="return __meNumbersOnly(event)" onmouseover="javascript:__tamt_compute_totals();" onmouseout="javascript:__tamt_compute_totals();" onclick="javascript:__tamt_compute_totals();" onblur="javascript:__tamt_compute_totals();" /></td> <!--qty-->
								<td><input type="text" size="30"  value="" /></td>   <!--remks onblur="javascript:my_add_line_item();"  -->
								<td style ="display:none;" class="frmmitemcoderow"><input type="text" size="20" class="frmmitemcode form_cust fform_cust-sm" value="" /></td> <!--frmitemcode-->
							</tr>         							
						</tbody>
					</table> <!-- end tbl_pullout -->
				</div>
			</div>
		</div> <!-- end div table entry -->
		<div class="row mb-3">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-success btn-sm" id="mbtn_po_Save">
					Save
				</button>
				<button type="button" class="btn btn-danger btn-sm" id="mbtn_po_cancel">
					Cancel
				</button>
				<button type="button" class="btn btn-warning btn-sm" id="mbtn_mn_NTRX">
					New Trx
				</button>
			</div>
		</div> <!-- end buttons -->
		<?=form_close();?>
		<!-- tabular module -->
		<div class="row mb-4">
			<div class="col-lg-12 col-md-12 mb-4">
				<div class="card">
					<ul class="nav nav-tabs nav-tabs-bordered" id="myTabArticle" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="nav-pout-rec-tab" data-bs-toggle="tab" data-bs-target="#nav-pout-rec" type="button" role="tab" aria-controls="nav-pout-rec" aria-selected="true">Record Listing...</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="droltdashboard-tab" data-bs-toggle="tab" data-bs-target="#droltdashboard" type="button" role="tab" aria-controls="droltdashboard" aria-selected="false">Pullout Request Dashboard</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="branchclimsdashboard-tab" data-bs-toggle="tab" data-bs-target="#branchclimsdashboard" type="button" role="tab" aria-controls="branchclimsdashboard" aria-selected="false">Branch Claims Dashboard</button>
						</li>
					</ul>
					<div class="tab-content" id="medrrecs">
						<div class="tab-pane fade show active" id="nav-pout-rec" role="tabpanel" aria-labelledby="nav-pout-rec-tab">
							<div class="row p-2">
								<div class="col-sm-12">
								   <div class="input-group input-group-sm">
									  <span class="input-group-text" id="basic-addon1">Search</span>
									  <input type="text" class="form-control" id="mytxtsearchrec" placeholder="Search Transaction/Company/Area/DR/Supplier" aria-label="mytxtsearchrec" aria-describedby="basic-addon1" required>
									  <button type="submit" class="btn btn-success btn-sm" id="mebtn_searchdr"><i class="bi bi-search"></i></button>
									  <?=anchor('pullout-trx', 'Reset',' class="btn btn-success btn-sm" ');?>  
									</div>
								</div>
							</div>
							<div class="row p-2" id="mymodoutrecs">
							</div>
						</div>
						<div class="tab-pane fade" id="droltdashboard" role="tabpanel" aria-labelledby="droltdashboard-tab">222</div>
						<div class="tab-pane fade" id="branchclimsdashboard" role="tabpanel" aria-labelledby="branchclimsdashboard-tab">333</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end tabular module --> 
	</section>
	<?php
	echo $mylibzsys->memypreloader01('mepreloaderme');
	echo $mylibzsys->memsgbox3('memsgme','System Message','...');
	echo $mylibzsys->memsgbox_yesno1('metrxPOcancmsg','Closed and Cancel DR IN Transaction Entry','Cancel changes made?');
	echo $mylibzsys->memsgbox_yesno1('metrxPOnewcmsg','Pull Out Transaction Entry','Are you sure you want to new transaction?');
	echo $mylibzsys->memsgbox_yesno1('metrxpoconfirm','Save Pull Out Transaction Entry','Save Transaction Entry?');
	?>
</main>  <!-- end main -->

<script type="text/javascript"> 
	__mysys_apps.mepreloader('mepreloaderme',false);
	(function () {
		'use strict'
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.querySelectorAll('.needs-validation')
		// Loop over them and prevent submission
		Array.prototype.slice.call(forms)
		.forEach(function (form) {
			form.addEventListener('submit', function (event) {
				if (!form.checkValidity()) {
					event.preventDefault()
					event.stopPropagation()
				}
				form.classList.add('was-validated');
				try {
					event.preventDefault();
          			event.stopPropagation();
					jQuery('#metrxpoconfirm').modal('show');
					return false;
				} catch(err) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					var mtxt = 'There was an error on this page.\n';
					mtxt += 'Error description: ' + err.message;
					mtxt += '\nClick OK to continue.';
					alert(mtxt);
					return false;
				}  //end try					
			}, false)
		})
	})();		
	
	jQuery('#fld_rson').on('change',function() {
	   vw_from();
	   jQuery("#fld_rson").attr("disabled", true);
	});  //end fld_rson
	
	function vw_from() {
		var fld_rson = jQuery('#fld_rson').val();
		if (fld_rson == '4') {
			jQuery('#POTOB').css('display', (fld_rson == '4') ? 'flex' : 'none');
		}
		else if(fld_rson == '5'){
			jQuery('#hPOA').css('display', (fld_rson == '5') ? 'none' : 'flex'); 
			jQuery('#hIMS').css('display', (fld_rson == '5') ? 'none' : 'flex'); 

		}
		else if(fld_rson == '8') {  //SALE ITEM 
			//jQuery('.frmmitemcode').css('display', (fld_rson == '8') ? 'flex' : 'none'); 
			jQuery('.frmmitemcoderow').css('display', (fld_rson == '8') ? '' : 'none'); 
			
		}
		else{
			jQuery('#POTOB').css('display', (fld_rson != '4') ? 'none' : 'flex');

			jQuery('#hPOA').css('display', (fld_rson != '5') ? 'flex' : 'none'); 
			jQuery('#hIMS').css('display', (fld_rson != '5') ? 'flex' : 'none');  
			//jQuery('.frmmitemcode').css('display', (fld_rson != '8') ? 'none' : 'flex'); 
			jQuery('.frmmitemcoderow').css('display', (fld_rson != '8') ? 'none' : ''); 
		}
	}  //end vw_from	
	
	jQuery('#metrxpoconfirm_yes').click(function() { 
		try { 
			jQuery('#metrxpoconfirm').modal('hide');
			__mysys_apps.mepreloader('mepreloaderme',true);
			
			var fld_txtpotrx_no = jQuery('#txtpotrx_no').val();
			var fld_Company_po = jQuery('#fld_Company_po').val();
			var fld_area_code_po = jQuery('#fld_area_code_po').val();
			var fld_supplier_po = jQuery('#fld_supplier_po').val();
			var fld_dftag = jQuery('#fld_dftag').val();
			var fld_pono = jQuery('#fld_pono').val();
			var fld_imsno = jQuery('#fld_imsno').val();
			var fld_podate = jQuery('#fld_podate').val();
			var fld_rems = jQuery('#fld_rems').val();
			var fld_rson = jQuery('#fld_rson').val();
			var trxno_id = jQuery('#__hmtkn_trxnoid').val();
			
			var fld_subtqty = jQuery('#fld_subtqty').val();
			var fld_subtcost = jQuery('#fld_subtcost').val();
			var fld_subtamt = jQuery('#fld_subtamt').val();
			var __pfrom =jQuery('#fld_potobrnc').val();

			var fld_ptyp = jQuery('#fld_ptyp').val();
			
			if(jQuery.trim(fld_podate) == '' || fld_podate=='00/00/0000' || fld_podate=='mm/dd/yyyy'){ 
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme_bod').html('PO Date is required!');
				jQuery('#memsgme').modal('show');
				return false;
			}
			var rowCount1 = jQuery('#tbl_PayData tr').length - 1;
			var adata1 = [];
			var adata2 = [];
			var mdata = '';
			var mdat ='';
			var fld_frmmndt_rid = '';
			var fld_frmmitemcode = '';
			for(aa = rowCount1; aa > 0; aa--) { 
				var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone(); 
				
				var fld_mitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); //desc
				var fld_mitempkg = jQuery(clonedRow).find('input[type=text]').eq(2).val(); //pkg
				var fld_ucost = jQuery(clonedRow).find('input[type=text]').eq(3).val(); //ucost
				var fld_mitemtcost = jQuery(clonedRow).find('input[type=text]').eq(4).val(); //ucost
				var fld_srp = jQuery(clonedRow).find('input[type=text]').eq(5).val(); //srp
				var fld_mitemtamt = jQuery(clonedRow).find('input[type=text]').eq(6).val(); //tamt
				var fld_ptyp_i = fld_ptyp;
				if (fld_ptyp_i != 'T') {
					var fld_mitemcode = aa;
					var fld_mitemqty = jQuery(clonedRow).find('input[type=text]').eq(8).val(); //qty r
					var fld_remks = jQuery(clonedRow).find('input[type=text]').eq(7).val(); //rems
				}
				else{
					var fld_mitemcode = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
					var fld_mitemqty = jQuery(clonedRow).find('input[type=text]').eq(7).val(); //qty r
					var fld_remks = jQuery(clonedRow).find('input[type=text]').eq(8).val(); //rems
				}
				var fld_poutrson_id = "";//$(clonedRow).find('select[name=txtpout_rson]').eq(0).attr('id');
				var fld_poutrson = "";//$('#' + fld_poutrson_id).val();
				var fld_mndt_rid = jQuery(clonedRow).find('input[type=hidden]').eq(1).val(); //mndt id
				var fld_mndt_tag = jQuery(clonedRow).find('input[type=hidden]').eq(2).val(); //mndt id

				var fld_frmmndt_rid =jQuery(clonedRow).find('input[type=hidden]').eq(3).val(); //mndt id
				var fld_frmmitemcode = jQuery(clonedRow).find('input[type=text]').eq(9).val();
				if(fld_mndt_tag == 'Y'){
					mdata = fld_mitemcode + 'x|x' + fld_mitemdesc + 'x|x' + fld_mitempkg + 'x|x' + fld_ucost + 'x|x' + fld_mitemtcost + 'x|x' + fld_srp + 'x|x' + fld_mitemtamt + 'x|x' + fld_mitemqty + 'x|x' + fld_remks + 'x|x' + fld_mndt_rid + 'x|x' + fld_poutrson + 'x|x' + fld_frmmndt_rid + 'x|x' + fld_frmmitemcode;
					adata1.push(mdata);
					mdat = $(clonedRow).find('input[type=hidden]').eq(0).val(); //icode
					adata2.push(mdat);
				}
				
			} //end for  
				
			var smparam = { 
				trxno_id: trxno_id,
				fld_txtpotrx_no: fld_txtpotrx_no,
				fld_Company_po: fld_Company_po,
				fld_area_code_po: fld_area_code_po,
				fld_supplier_po: fld_supplier_po,
				fld_dftag: fld_dftag,
				fld_pono: fld_pono,
				fld_imsno:fld_imsno,
				fld_podate: fld_podate,
				fld_rems: fld_rems,
				fld_rson:fld_rson,
				fld_subtqty: fld_subtqty,
				fld_subtcost: fld_subtcost,
				fld_subtamt: fld_subtamt,
				__pfrom:__pfrom,
				fld_ptyp:fld_ptyp,
				adata1: adata1,
				adata2: adata2
			}
			console.log(fld_ptyp);
			//mytrx_acct/man_recs_po_sv
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST", 
				url: '<?= site_url() ?>pullout-trx-save',
				context: document.body,
				data: eval(smparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsgme_bod').html(data);
					jQuery('#memsgme').modal('show');
				},
				error: function(data) { // display global error on the menu function 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}
			});	 
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
	});  //end metrxpoconfirm_yes
	
	function __do_makeid()
	{
		var text = '';
		var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		for( var i=0; i < 7; i++ )
			text += possible.charAt(Math.floor(Math.random() * possible.length));
		return text;
	}  //end __do_makeid
	
	function my_add_line_item(fld_ptyp_i) { 
		try {
			var rowCount = jQuery('#tbl_PayData tr').length;
			var mid = __do_makeid() + (rowCount + 1);
			var clonedRow = jQuery('#tbl_PayData tr:eq(' + (rowCount - 1) + ')').clone(); 
			jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id','mitemrid_' + mid);
			jQuery(clonedRow).find('input[type=hidden]').eq(1).attr('id','mid_' + mid);
			jQuery(clonedRow).find('input[type=hidden]').eq(2).attr('id','__me_tag' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','fld_mitemcode' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','fld_mitemdesc' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','fld_mitempkg' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','fld_ucost' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','fld_mitemtcost' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','fld_srp' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(6).attr('id','fld_mitemtamt' + mid);
			var fld_ptyp_i = jQuery('#fld_ptyp').val();
			if (fld_ptyp_i == 'N'){
				jQuery(clonedRow).find('input[type=text]').eq(8).attr('id','fld_mitemqty' + mid);
				jQuery(clonedRow).find('input[type=text]').eq(7).attr('id','fld_remks' + mid);
				jQuery(clonedRow).find('input[type=text]').eq(7).attr('size',60);
			}
			else{
				jQuery(clonedRow).find('input[type=text]').eq(7).attr('id','fld_mitemqty' + mid);
				jQuery(clonedRow).find('input[type=text]').eq(8).attr('id','fld_remks' + mid);
			}
			jQuery(clonedRow).find('input[type=hidden]').eq(3).attr('id','frmmitemrid_' + mid);
			jQuery(clonedRow).find('input[type=text]').eq(9).attr('id','fld_frmmitemtamt' + mid);
			jQuery('#tbl_PayData tr').eq(1).before(clonedRow);
			
			jQuery(clonedRow).css({'display':''});
			//AccName();
			__my_item_lookup();
			__my_frmitem_lookup();
			__tamt_compute_totals('<?=$txtpout_typ;?>');
			var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
			jQuery('#' + xobjArtItem).focus();
			jQuery('#tbl_PayData tr').each(function(i) { 
				jQuery(this).find('td').eq(0).html(i);
			});
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try 
	}	//end my_add_line_item

	function __meNumbersOnly(e) {
		var code = (e.which) ? e.which : e.keyCode;
		//if (code > 31 && (code < 47 || code > 57)) {
		if(!((code > 47 && code < 58) || code == 46)) { 
			e.preventDefault();
		}
	} //end __meNumbersOnly
                
	jQuery('#fld_ptyp').on('change',function() {
		jQuery("#fld_ptyp").attr("disabled", true);
		vw_ptype(this);
	});  //end fld_ptyp

	function vw_ptype(obj){
		try {
			var tbl = jQuery('tbl_PayData');
			var fld_ptyp_i = jQuery('#fld_ptyp').val();
			var fld_ptyp_i = jQuery(obj).val();
			var userrema = '<?=$cuserrema;?>';
			if (fld_ptyp_i != 'T') { //N 
				var aa = 0;
				jQuery( '#tbl_PayData tr').each(function(i) {  
					jQuery(this).find('input[type=text]').eq(8).attr('size',60);
				});
				
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(3)').hide();
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(4)').hide();
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(5)').hide();
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(6)').hide();
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(7)').hide();
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(8)').hide();
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(9)').hide();
				
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(3)').hide();
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(4)').hide();
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(5)').hide();
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(6)').hide();
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(7)').hide();
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(8)').hide();
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(9)').hide();
				
				jQuery( '#tbl_PayData tr').each(function(i) { 
					jQuery(this).children(":eq(10)").after(jQuery(this).children(":eq(9)"));
					jQuery(this).find('td').eq(5).find('input[type=text]').attr("readonly", false);
					jQuery(this).find('td').eq(7).find('input[type=text]').attr("readonly", false);
					
				});                        
			} else { //T
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(3)').show();
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(4)').show();
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(5)').show();

				if(userrema != 'B') {
					jQuery('#tbl_PayData').find('thead').find('th:nth-child(6)').show();
					jQuery('#tbl_PayData').find('thead').find('th:nth-child(7)').show();
					jQuery('#tbl_PayData').find('tr').find('td:nth-child(6)').show();
					jQuery('#tbl_PayData').find('tr').find('td:nth-child(7)').show();
				}
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(8)').show();
				jQuery('#tbl_PayData').find('thead').find('th:nth-child(9)').show();

				jQuery('#tbl_PayData').find('tr').find('td:nth-child(3)').show();
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(4)').show();
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(5)').show();

				jQuery('#tbl_PayData').find('tr').find('td:nth-child(8)').show();
				jQuery('#tbl_PayData').find('tr').find('td:nth-child(9)').show();
				
				jQuery( '#tbl_PayData tr').each(function(i) { 
					jQuery(this).find('td').eq(2).find('input[type=text]').addClass('mitemcode');
					jQuery(this).find('td').eq(5).find('input[type=text]').attr("readonly", true);
					jQuery(this).find('td').eq(7).find('input[type=text]').attr("readonly", true);
				});
				__my_item_lookup();
			} 
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try 
				
	} //end vw_ptype

	function __my_item_lookup() {  
		jQuery('.mitemcode' ) 
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
			source: '<?= site_url(); ?>search-mat-article/',
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) { 
				var sValue = jQuery(oEvent.target).val();
				//jQuery(oEvent.target).val('&mcocd=1' + sValue);
				//alert(sValue);
				var fld_ptyp_i = jQuery('#fld_ptyp').val();
				if(fld_ptyp_i == ''){
					alert('Please select Pullout Type first!!!');
					return false;
				}
				
				var fld_area_id = jQuery('#fld_area_id').val();
				if(fld_area_id == ''){
					alert('Please input Area Code/Branch first!!!');
					return false;
				}  //old from mysearchdata/mat_article
				jQuery(this).autocomplete('option', 'source', '<?=site_url();?>search-mat-article/?pbranchid=' + fld_area_id);
			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				jQuery(this).attr('alt', jQuery.trim(ui.item.ART_CODE));
				jQuery(this).attr('title', jQuery.trim(ui.item.ART_CODE));
				this.value = ui.item.ART_CODE;
				var clonedRow = jQuery(this).parent().parent().clone();
				var indexRow = jQuery(this).parent().parent().index();
				var xobjArtMDescId = jQuery(clonedRow).find('input[type=text]').eq(1).attr('id');
				var xobjArtMUOM = jQuery(clonedRow).find('input[type=text]').eq(2).attr('id');
				var xobjArtMUcost= jQuery(clonedRow).find('input[type=text]').eq(3).attr('id');
				var xobjArtMSRP = jQuery(clonedRow).find('input[type=text]').eq(5).attr('id');
				var xobjArtMQty = jQuery(clonedRow).find('input[type=text]').eq(7).attr('id');
				var xobjArtMrid = jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id');
				jQuery('#' + xobjArtMDescId).val(ui.item.ART_DESC);
				jQuery('#' + xobjArtMUOM).val(ui.item.ART_SKU);
				jQuery('#' + xobjArtMUcost).val(ui.item.ART_UCOST);
				jQuery('#' + xobjArtMSRP).val(ui.item.ART_UPRICE);
				jQuery('#' + xobjArtMrid).val(ui.item.mtkn_rid);
				jQuery('#' + xobjArtMQty).focus();                    
				return false;
			}
		})
		.click(function() { 
			//jQuery(this).keydown(); 
			var terms = this.value.split('=>');
			//jQuery(this).autocomplete('search', '');
			jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
		});        
	} //end __my_item_lookup 
	
	function __my_frmitem_lookup() {  
			jQuery('.frmmitemcode' ) 
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
				.autocomplete({  //old from mysearchdata/mat_article
					minLength: 0,
					source: '<?= site_url(); ?>search-mat-article/',
					focus: function() {
						// prevent value inserted on focus
						return false;
					},
					search: function(oEvent, oUi) { 
						var sValue = jQuery(oEvent.target).val();
						
						jQuery(this).autocomplete('option', 'source', '<?=site_url();?>search-mat-article/');
					},
					select: function( event, ui ) {
						var terms = ui.item.value;

						jQuery(this).attr('alt', jQuery.trim(ui.item.ART_CODE));
						jQuery(this).attr('title', jQuery.trim(ui.item.ART_CODE));

						this.value = ui.item.ART_CODE;

						var clonedRow = jQuery(this).parent().parent().clone();
						var indexRow = jQuery(this).parent().parent().index();
						//var xobjArtMFrom = jQuery(clonedRow).find('input[type=text]').eq(9).attr('id');

						var xobjArtMridFrom = jQuery(clonedRow).find('input[type=hidden]').eq(3).attr('id');
						jQuery('#' + xobjArtMridFrom).val(ui.item.mtkn_rid);

						return false;
					}
				})
				.click(function() { 
					//jQuery(this).keydown(); 
					var terms = this.value.split('=>');
					//jQuery(this).autocomplete('search', '');
					jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
		}); 
	} //end __my_frmitem_lookup
	
	jQuery('#fld_Company_po')
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
			source: '<?= site_url(); ?>search-company/',  //old from mysearchdata/company_search_v/
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
				var apv_id = ui.item._compcode;
				this.value = ui.item.value;
				var comp_id = ui.item.mtkn_recid;
				//console.log(comp_id);
				//jQuery('#apv_id').val('APV-'+ui.item._compcode+'-'+ui.item.cseqn);
				//jQuery('#comp_code_').val(ui.item._compcode);
				jQuery('#fld_Company_po').val(terms);
				jQuery('#fld_Company_po').attr("data-id",comp_id);
				return false;
			}
		})
		.click(function() {
			//jQuery(this).keydown();
			var terms = this.value;

		jQuery(this).autocomplete('search', jQuery.trim(terms));
	}); //end fld_Company_po	

	jQuery('#fld_area_code_po')
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
			source: '<?= site_url(); ?>search-area-company/',  //mysearchdata/companybranch_v
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) {
				var sValue = jQuery(oEvent.target).val();
				//var comp = jQuery('#fld_Company_po').val();
				var fld_ptyp_i = jQuery('#fld_ptyp').val();
				if(fld_ptyp_i == ''){
					alert('Please select Pullout Type first!!!');
					return false;
				}
				var comp = jQuery('#fld_Company_po').attr("data-id");
				jQuery(this).autocomplete('option', 'source', '<?=site_url();?>search-area-company/?mtkn_compid=' + comp); 
				//jQuery(oEvent.target).val('&mcocd=1' + sValue);
			   
			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				var mtkn_comp = ui.item.mtkn_comp;
				var mtkn_brnch = ui.item.mtkn_brnch;
				jQuery('#fld_area_id').val(mtkn_brnch);
				jQuery('#fld_area_code_po').val(terms);
				jQuery('#fld_Company_po').val(mtkn_comp);
				jQuery(this).autocomplete('search', jQuery.trim(terms));
				return false;
			}
		})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	});	//end fld_area_code_po
	
	jQuery('.fld_supplier_po' ) 
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
			source: '<?=site_url();?>search-vendor/',  //mysearchdata/vendor_ua/
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
			jQuery('#' + this.id).attr('alt', jQuery.trim(terms));
			jQuery('#' + this.id).attr('title', jQuery.trim(terms));
			this.value = ui.item.value; 

			return false;
		}
		})
		.click(function() { 
			var terms = this.value.split('|');
			jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
	});  //end fld_supplier_po    
	
	jQuery('#fld_potobrnc')
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
		.autocomplete({ //old from mysearchdata/companybranch_pout
			minLength: 0,
			source: '<?= site_url(); ?>search-rcv-frm-brnch-pullout/',
			focus: function() { 
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) { 
				var sValue = jQuery(oEvent.target).val(); 
				jQuery(this).autocomplete('option', 'source', '<?=site_url();?>search-rcv-frm-brnch-pullout/'); 
			},
			select: function( event, ui ) { 
				var terms = ui.item.value;
				var mtkn_comp = ui.item.mtkn_comp;
				jQuery('#fld_potobrnc').val(terms);
				jQuery(this).autocomplete('search', jQuery.trim(terms));
				return false;
			}
		})
		.click(function() { 
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));  
	}); //end fld_potobrnc    
    
    jQuery('#nav-pout-rec-tab').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mtkn_arttr = '<?=$mtkn_trxno;?>';
			var mparam = {
				mtkn_arttr: mtkn_arttr
			}
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>search-pullout-trx',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mymodoutrecs').html(data);
						return false;
				},
				error: function() { // display global error on the menu function 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}	
			});	
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try 		
	}); //end nav-pout-rec-tab
    
	function __tamt_compute_totals(fld_ptyp_i) { 
		try { 
			var rowCount1 = jQuery('#tbl_PayData tr').length - 1;
			var adata1 = [];
			var adata2 = [];
			var mdata = '';
			var ninc = 0;
			var nTAmount = 0;
			var nTAmountCost = 0;
			var nTQty = 0;
			var nTQtyItems = 0;
			for(aa = 1; aa < rowCount1; aa++) { 
				var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone(); 
				var mdat1 = jQuery(clonedRow).find('input[type=text]').eq(0).val();
				var mdat2 = jQuery(clonedRow).find('input[type=text]').eq(1).val();
				var mdat3 = jQuery(clonedRow).find('input[type=text]').eq(2).val();//uom/pkg
				var mdat4 = jQuery(clonedRow).find('input[type=text]').eq(3).val();//ucost
				var mdat5 = jQuery(clonedRow).find('input[type=text]').eq(4).val();//tcost
				var mdat6 = jQuery(clonedRow).find('input[type=text]').eq(5).val();//srp
				var mdat7 = jQuery(clonedRow).find('input[type=text]').eq(6).val();//tamt
				var fld_ptyp_i = jQuery('#fld_ptyp').val();
				console.log(fld_ptyp_i);
				if (fld_ptyp_i == 'N'){
					var mdat8 = jQuery(clonedRow).find('input[type=text]').eq(8).val();//qty rcvd
					var mdat9 = jQuery(clonedRow).find('input[type=text]').eq(7).val();//rems
				
				}
				else{
					var mdat8 = jQuery(clonedRow).find('input[type=text]').eq(7).val();//qty rcvd
					var mdat9 = jQuery(clonedRow).find('input[type=text]').eq(8).val();//rems
				}
				var xTAmntCostId = jQuery(clonedRow).find('input[type=text]').eq(4).attr('id');
				var xTAmntCostIdh = jQuery(clonedRow).find('input[type=hidden]').eq(4).attr('id');

				var xTQtyId = jQuery(clonedRow).find('input[type=text]').eq(7).attr('id');
				var xTQtyIdh = jQuery(clonedRow).find('input[type=hidden]').eq(7).attr('id');

				var xTAmntId = jQuery(clonedRow).find('input[type=text]').eq(6).attr('id');
				var xTAmntIdh = jQuery(clonedRow).find('input[type=hidden]').eq(6).attr('id');

				var nqty = 0;
				var nqtyc = 0;
				var nprice = 0;
				if(jQuery.trim(mdat3) == '') { //uom/pkg
					nuom = "BOX";
				} else { 
					nuom = mdat3;
				}
				if($.trim(mdat4) == '') { //ucost
					ncost = 0;
				} else { 
				   
					ncost = mdat4;
				}
				if(jQuery.trim(mdat6) == '') { //srp
					nsrp = 0;
				} else { 
				   
					nsrp = mdat6;
				}
			   
			   if(jQuery.trim(mdat8) == '') { //qty rcvd
					nqty = 0;
				} else { 
					
					nqty = mdat8;
				}
				if(jQuery.trim(xTAmntCostId) == '') { 
					nucost = 0;
				} else { 
					nucost = xTAmntCostId;
				}
				if(jQuery.trim(xTAmntId) == '') { 
					nprice2 = 0;
				} else { 
					nprice2 = xTAmntId;
				}
				if(jQuery.trim(xTQtyId) == '') { 
					nuqty = 0;
				} else { 
					nuqty = xTQtyId;
				}
				
				//console.log(mdat7);
				var ntqty = parseFloat(nqty);
				var ntqtyc = parseFloat(nqtyc);
				
				//TOTAL COST AMT
				if(jQuery('#' + xTAmntCostIdh).val()==''){
					var ntCost = parseFloat(ncost * ntqty);
				}
				else{

					var ntCost = parseFloat(ncost * ntqty);
				}
				//TOTAL AMT
				if(jQuery('#' + xTAmntIdh).val()==''){
					var ntprice = parseFloat(nsrp * ntqty);
				}
				else{

					var ntprice = parseFloat(nsrp * ntqty);
				}
				 //TOTAL QTY AMT
				if(jQuery('#' + xTQtyIdh).val()==''){
					var ntQty = parseFloat(nuqty);
				}
				else{
					var ntQty = parseFloat(nuqty);
				}
				 //TOTAL AMT COST
				if(!isNaN(ntCost) || ntCost > 0) { 
					jQuery('#' + xTAmntCostId).val(__mysys_apps.oa_addCommas(ntCost.toFixed(2)));
					// console.log(xTAmntId);
				}

				 //TOTAL AMT
				if(!isNaN(ntprice) || ntprice > 0) { 
					jQuery('#' + xTAmntId).val(__mysys_apps.oa_addCommas(ntprice.toFixed(2)));
				}
				 //TOTAL QTY COST
				if(!isNaN(ntQty) || ntQty > 0) { 
					jQuery('#' + xTQtyId).val(ntQty.toFixed(2));
				}
				nTAmount = (nTAmount + ntprice);
				nTAmountCost = (nTAmountCost + ntCost);
				nTQty = (nTQty + ntqty);
			}  //end for 
			if (!isNaN(nTAmount) || nTAmount < 0){
				jQuery('#fld_subtamt').val(__mysys_apps.oa_addCommas(nTAmount.toFixed(2)));
			}
			if (!isNaN(nTAmount) || nTAmount < 0){
				jQuery('#fld_subtcost').val(__mysys_apps.oa_addCommas(nTAmountCost.toFixed(2)));
			}
			if (!isNaN(nTQty) || nTQty < 0){
				jQuery('#fld_subtqty').val(__mysys_apps.oa_addCommas(nTQty.toFixed(2)));
			}
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
		}  //end try                
	} //__tamt_compute_totals
    
	jQuery('#mebtn_searchdr').click(function() { 
		try {
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();

			var mparam = {
				txtsearchedrec: txtsearchedrec,
				mpages: 1 
			};	
			__mysys_apps.mepreloader('mepreloaderme',true);
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>search-pullout-trx',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mymodoutrecs').html(data);
						return false;
				},
				error: function() { // display global error on the menu function 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}	
			});	
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
	});  //end mebtn_searchdr
	    
	    
	function __my_item_onchange(mtkn_tag) {  
		 var clonedRow = jQuery(mtkn_tag).parent().parent().clone();
		 var xobjArtMrid = jQuery(clonedRow).find('input[type=hidden]').eq(2).attr('id');
		 jQuery('#' + xobjArtMrid).val('Y');
	}  //end __my_item_onchange
	
	jQuery('#mbtn_mn_NTRX').click(function() { 
		try { 
			jQuery('#metrxdrinnewcmsg').modal('show');
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
		
	});	
	
	__my_item_lookup();
	__my_frmitem_lookup();    
	__tamt_compute_totals();
	<?php 
	if($nmnrecs == 0) { 
		echo "my_add_line_item('".$txtpout_typ."');";
	}
	if(!empty($mtkn_trxno)) {
		echo "vw_from();
		jQuery(\"#fld_ptyp\").attr(\"disabled\", true);
		";
	}
	?>    
</script>	