<?php
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$this->db_erp = $this->myusermod->mydbname->medb(0);
$this->cuser = $this->myusermod->mysys_user();
$mpw_tkn = $this->myusermod->mpw_tkn();

$memodule = "__ivtyitemized_abranch__";
$str = "select date_format(now(),'%m/%d/%Y %H:%i:%s') me_now,date_format(now(),'%m/01/%Y') medatef, date_format(now(),'%m/%d/%Y') medatet";
$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
$rw = $q->getRow();
$medatef = $rw->medatef;
$medatet = $rw->medatet;
$me_now = $rw->me_now;
$q->freeResult();

$ldownload = $this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070706');
if($ldownload) { 
	$dloadpath = ROOTPATH . 'public/downloads/me/';
	$cfilename = 'ivty_dtl_dload_itemized_branches_' . $this->mylibzsys->random_string(15) . '.xls';
	$mfile = $dloadpath . $cfilename;
	$cfilelink = site_url() . '/downloads/me/' . $cfilename;
	if (file_exists($mfile)) { 
		unlink($mfile);
	}
	$ncols = 11;
	$cmsexp =  "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
	<head>
	<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
	</head>
	<body>
	<table>
		<tr>
			<th colspan=\"{$ncols}\">Inventory Itemized Branches</th> 
		</tr>
		<tr>
			<th colspan=\"{$ncols}\">From: {$medatef} to {$medatet}</th> 
		</tr>
		<tr>
			<th colspan=\"{$ncols}\">Run Date: {$me_now} By:&nbsp;" . $this->cuser . "</th> 
		</tr>
		<tr>
			<th></th>
			<th>Branch Name</th>
			<th>Article Code</th>
			<th>Article Description</th>
			<th>Barcode</th>
			<th>Beginning Balance - QTY</th>
			<th>Receiving (Deliveries) - QTY</th>
			<th>Claims - QTY</th>
			<th>Receiving (IN from PO) - QTY</th>
			<th>Sales Out - QTY</th>
			<th>Ending Balance - QTY</th>
		</tr>
		
	";
	$fh = fopen($mfile, 'w');
	fwrite($fh, $cmsexp);
	fclose($fh); 
	chmod($mfile, 0755);	
	$chtml = "
	<div class=\"row mt-2\">
		<div class=\"col-md-4\">
			<a href=\"{$cfilelink}\">Download MS-Excel Format</a>
		</div>
	</div>
	";
	echo $chtml;
}  //end initial access download 


?>
<div class="row mt-2">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="metblentry-font table-bordered" id="__tbl_<?=$memodule;?>">
				<thead>
					<tr>
						<th colspan="11">Branches Inventory Itemized</th>
					</tr>
					<?php
					echo "
						<tr>
							<th colspan=\"{$ncols}\">From: {$medatef} to {$medatet}</th> 
						</tr>";					
					?>
					<tr>
						<th colspan="11">as of <?=$me_now;?></th>
					</tr>
					<tr>
						<th></th>
						<th>Branch Name</th>
						<th>Article Code</th>
						<th>Article Description</th>
						<th>Barcode</th>
						<th>Beginning Balance - QTY</th>
						<th>Receiving (Deliveries) - QTY</th>
						<th>Claims - QTY</th>
						<th>Receiving (IN from PO) - QTY</th>
						<th>Sales Out - QTY</th>
						<th>Ending Balance - QTY</th>
					</tr>
				</thead>
				<tbody>
				<?php 
				if($rlist !== ''):
					$nn = 1;
					$ntQtyBalBeg = 0; $ntQtyRcvDelv = 0; $ntQtyClaims = 0; $ntQtyRcvINPO = 0; $ntQtySales = 0; $ntQtyEndBal = 0;
					foreach($rlist as $row): 
						$xdata = explode("x|x",$row['ME_DATA']);
						$nQtyBalBeg = 0; $nQtyRcvDelv = 0; $nQtyClaims = 0; $nQtyRcvINPO = 0; $nQtySales = 0; $nQtyEndBal = 0;
						if (count($xdata) > 1):
							list($nQtyBalBeg, $nQtyRcvDelv, $nQtyClaims,$nQtyRcvINPO,$nQtySales,$nQtyEndBal) = $xdata;
						endif;
						$cfwbold_BalBeg = (!($nQtyBalBeg == 0) ? ' fw-bold' : '');
						$cfwbold_RcvDelv = (!($nQtyRcvDelv == 0) ? ' fw-bold ' : '');
						$cfwbold_Claims = (!($nQtyClaims == 0) ? ' fw-bold ' : '');
						$cfwbold_RcvINPO = (!($nQtyRcvINPO == 0) ? ' fw-bold ' : '');
						$cfwbold_Sales = (!($nQtySales == 0) ? ' fw-bold text-danger' : '');
						
						$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
						$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
						$chtml =  "<tr style=\"background-color: {$bgcolor} !important;\" {$on_mouse}>
							<td nowrap>{$nn}</td>
							<td nowrap>{$row['ME_BRANCH_NAME']}</td>
							<td nowrap>{$row['ART_CODE']}</td>
							<td nowrap>{$row['ART_DESC']}</td>
							<td nowrap>{$row['ART_BCODE']}</td>
							<td nowrap class=\"text-end{$cfwbold_BalBeg}\">" . number_format($nQtyBalBeg,2,'.',',') . "</td>
							<td nowrap class=\"text-end{$cfwbold_RcvDelv}\">" . number_format($nQtyRcvDelv,2,'.',',') . "</td>
							<td nowrap class=\"text-end{$cfwbold_Claims}\">" . number_format($nQtyClaims,2,'.',',') . "</td>
							<td nowrap class=\"text-end{$cfwbold_RcvINPO}\">" . number_format($nQtyRcvINPO,2,'.',',') . "</td>
							<td nowrap class=\"text-end{$cfwbold_Sales}\">" . number_format($nQtySales,2,'.',',') . "</td>
							<td nowrap class=\"text-end fw-bold\">" . number_format($nQtyEndBal,2,'.',',') . "</td>
						</tr>";
						echo $chtml;
						$nn++;	
						$ntQtyBalBeg += $nQtyBalBeg;
						$ntQtyRcvDelv += $nQtyRcvDelv;
						$ntQtyClaims += $nQtyClaims;
						$ntQtyRcvINPO += $nQtyRcvINPO;
						$ntQtySales += $nQtySales;
						$ntQtyEndBal += $nQtyEndBal;
						
						// put content to downloadable format file 
						if($ldownload) { 
							$cmsexp = "<tr>
								<td nowrap>{$nn}</td>
								<td nowrap>{$row['ME_BRANCH_NAME']}</td>
								<td nowrap>'{$row['ART_CODE']}</td>
								<td nowrap>{$row['ART_DESC']}</td>
								<td nowrap>'{$row['ART_BCODE']}</td>
								<td nowrap>" . number_format($nQtyBalBeg,2,'.',',') . "</td>
								<td nowrap>" . number_format($nQtyRcvDelv,2,'.',',') . "</td>
								<td nowrap>" . number_format($nQtyClaims,2,'.',',') . "</td>
								<td nowrap>" . number_format($nQtyRcvINPO,2,'.',',') . "</td>
								<td nowrap>" . number_format($nQtySales,2,'.',',') . "</td>
								<td nowrap>" . number_format($nQtyEndBal,2,'.',',') . "</td>
							</tr>";
							file_put_contents ( $mfile , $cmsexp , FILE_APPEND | LOCK_EX ); 
						}  //end download 
						
					endforeach;
				endif;
				$chtml = "
				<tr>
					<td colspan=\"5\"></td>
					<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyBalBeg,2,'.',',') . "</td>
					<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyRcvDelv,2,'.',',') . "</td>
					<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyClaims,2,'.',',') . "</td>
					<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyRcvINPO,2,'.',',') . "</td>
					<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtySales,2,'.',',') . "</td>
					<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyEndBal,2,'.',',') . "</td>
				</tr>
				";
				echo $chtml;

				// put content to downloadable format file 
				if($ldownload) { 
					$cmsexp = "
						<tr>
							<td colspan=\"5\"></td>
							<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyBalBeg,2,'.',',') . "</td>
							<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyRcvDelv,2,'.',',') . "</td>
							<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyClaims,2,'.',',') . "</td>
							<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyRcvINPO,2,'.',',') . "</td>
							<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtySales,2,'.',',') . "</td>
							<td nowrap class=\"text-end fw-bold\">" . number_format($ntQtyEndBal,2,'.',',') . "</td>
						</tr>
					</tr>";
					file_put_contents ( $mfile , $cmsexp , FILE_APPEND | LOCK_EX ); 
				}  //end download 

				?> 
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
if($ldownload) { 
	$cmsexp = "</table>
	</body>
	</html>";
	file_put_contents( $mfile , $cmsexp , FILE_APPEND | LOCK_EX );
}
?>

<script type="text/javascript"> 
	__mysys_apps.meTableSetCellPadding("__tbl_<?=$memodule;?>",5,'1px solid #7F7F7F');
</script>
