<?php
/**
 *	File        : widgets/mywg-delvreg.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Feb 15, 2023
 * 	last update : Feb 15, 2023
 * 	description : Widget Figures for Deliveries Regular Transactions
 */
 
$request = \Config\Services::request();

$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$mylibzsys = model('App\Models\MyLibzSysModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$myusermod = model('App\Models\MyUserModel');
$mydatum = model('App\Models\MyDatumModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$meload = $request->getVar('meload');;
$scat = ($meload == 'grocery-sales-data' ? 'G' : 'R');
$mbrnch_id = '';
$str = "SELECT bb.recid mbrnch_id,CONCAT('E',TRIM(bb.BRNCH_OCODE2)) bcode_pos, 
DATE(CONCAT(YEAR(DATE_ADD(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1'),INTERVAL -1 DAY)),'-',MONTH(DATE_ADD(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1'),INTERVAL -1 DAY)),'-1')) p_mo_f,
DATE_ADD(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1'),INTERVAL -1 DAY) p_mo_t,
(DATEDIFF(DATE_ADD(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1'),INTERVAL -1 DAY), 
DATE(CONCAT(YEAR(DATE_ADD(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1'),INTERVAL -1 DAY)),'-',MONTH(DATE_ADD(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1'),INTERVAL -1 DAY)),'-1'))) + 1) p_mo_d,
MONTH(DATE_ADD(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1'),INTERVAL -1 DAY)) p_mo,
YEAR(DATE_ADD(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1'),INTERVAL -1 DAY)) p_yr,
DATE(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1')) c_mo_f,
LAST_DAY(CURRENT_DATE()) c_mo_t,
(DATEDIFF(LAST_DAY(CURRENT_DATE()), DATE(CONCAT(YEAR(CURRENT_DATE()),'-',MONTH(CURRENT_DATE()),'-1'))) + 1) c_mo_d,
MONTH(CURRENT_DATE()) c_mo,YEAR(CURRENT_DATE()) c_yr 
 FROM {$db_erp}.`myua_branch` aa JOIN {$db_erp}.mst_companyBranch bb ON(aa.myuabranch = bb.recid) WHERE aa.`myusername` = '$cuser' AND aa.ISACTIVE = 'Y' AND bb.BRNCH_MAT_FLAG = '$scat' limit 1";
$qry = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'User: ' . $cuser);
if($qry->getNumRows() > 0) { 
	$rw = $qry->getRowArray();
	$mbrnch_id = $rw['mbrnch_id'];
	$curldaymo = $rw['c_mo_d'];
	$curyr = $rw['c_yr'];
	$curmo = $rw['c_mo'];
	$bcode_pos = $rw['bcode_pos'];
	$qry->freeResult();
} else { 
	echo "INVALID USER!!!";
	die();
}

$mbeg_yr = $curyr;
$mbeg_mo = $curmo;
if($curmo == 1) { 
	$mbeg_yr = ($mbeg_yr - 1);
	$mbeg_mo = 12;
} else { 
	$mbeg_mo = ($curmo - 1);
}

$tbl_beg =  " {$db_erp}.`trx_E" . $bcode_pos . "_lb_dtld` where ((aa.`LB_YEAR` + 0) = '$mbeg_yr' AND (aa.`LB_MONTHS` + 0) = '$mbeg_mo' )";

$str = "
select IFNULL(sum(TQTY),0) TQTY,IFNULL(SUM(TCOST),0) TCOST,IFNULL(SUM(TSRP),0) TSRP,max(MLRCVDDTE) MLRCVDDTE,count(mat_code) nitem_cnt,count(*) nrec_cnt  from (
	SELECT mat_code,
	ifnull(SUM(bb.`qty_corrected`),0) TQTY,
	SUM(bb.`ucost` * IF(bb.`qty_corrected` = 0,bb.qty,bb.`qty_corrected`)) TCOST,
	SUM(bb.`ucost` * IF(bb.`qty_corrected` = 0,bb.qty,bb.`qty_corrected`)) TSRP,
	max(aa.`rcv_date`) MLRCVDDTE  
	FROM
	{$db_erp}.`trx_manrecs_hd` aa 
	JOIN
	{$db_erp}.`trx_manrecs_dt` bb
	ON (aa.`recid` =bb.`mrhd_rid`)
	WHERE aa.`flag` = 'R'
	AND (aa.`hd_sm_tags` = 'R')  and aa.branch_id = '$mbrnch_id' 
	and (YEAR(aa.`rcv_date`) = '$curyr' AND MONTH(aa.`rcv_date`) = '$curmo') GROUP BY bb.`mat_code` 
) oa 
";
$q =  $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
//echo $q->resultID->num_rows  . '<br/>';
if($q->getNumRows() > 0) { 
	//$rrec = $q->getResultArray();
	$rw = $q->getRowArray();
	$nitem_cnt = (empty($rw['nitem_cnt']) ? 0 : $rw['nitem_cnt']);
	$MLRCVDDTE = (!empty($rw['MLRCVDDTE']) ? $mylibzsys->mydate_mmddyyyy($rw['MLRCVDDTE']) : '');
	$chtml = "
	<table>
		<tr>
			<td>Qty</td>
			<td align=\"right\">" . number_format($rw['TQTY'],2,'.',',') . "</td>
		</tr>
		<tr>
			<td>Tot. SKU</td>
			<td align=\"right\">" . number_format($rw['nitem_cnt'],2,'.',',') . "</td>
		</tr>
		<tr>
			<td>Tot. Amount</td>
			<td align=\"right\">" . number_format($rw['TSRP'],2,'.',',') . "</td>
		</tr>
		<tr>
			<td>Last Received:</td>
			<td align=\"right\">{$MLRCVDDTE}</td>
		</tr>
		
	</table>
	";
	echo $chtml;
}
$q->freeResult();

?>
