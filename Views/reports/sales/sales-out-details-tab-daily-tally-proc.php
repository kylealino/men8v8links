<?php
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$medate = $this->mylibzsys->mydate_mmddyyyy($this->myusermod->request->getVar('medate'));
$memodule = "__saleout_daily_tally__";
?>
<div class="table-responsive">
	<div class="col-md-12 col-md-12 col-md-12">
		<table class="metblentry-font table-bordered" id="__tbl_<?=$memodule;?>">
			<thead>
				<tr>
					<th colspan="5">Sales Dated: <?=$medate;?></th>
				</tr>
				<tr class="metblhead-bg">
					<th></th>
					<th>Branch Code</th>
					<th>Branch Name</th>
					<th>SO Net</th>
					<th>POS Tally Net</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			if($rlist !== ''):
				$nn = 1;
				$nSO_Net = 0;
				$nPOST_Net = 0;
				foreach($rlist as $row): 
					
					$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
					$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
					
				?>
				<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
					<td nowrap><?=$nn;?></td>
					<td nowrap><?=$row['ME_BRANCH'];?></td>
					<td nowrap><?=$row['ME_BRANCH_NAME'];?></td>
					<td nowrap class="text-end"><?=number_format($row['ME_SO_NETSALE_AMT'],4,'.',',');?></td>
					<td nowrap class="text-end"><?=number_format($row['ME_POST_NETSALE_AMT'],4,'.',',');?></td>
				</tr>
				<?php 
				$nn++;
				$nSO_Net += $row['ME_SO_NETSALE_AMT'];
				$nPOST_Net += $row['ME_POST_NETSALE_AMT'];
				endforeach;
			else:
				?>
				<tr>
					<td colspan="5">No data was found.</td>
				</tr>
			<?php 
			endif; ?>
				<tr>
					<td colspan="3"></td>
					<td nowrap class="text-end fw-bolder"><?=number_format($nSO_Net,4,'.',',');?></td>
					<td nowrap class="text-end fw-bolder"><?=number_format($nPOST_Net,4,'.',',');?></td>
				</tr>			
			</tbody>
		</table> 
	</div> <!-- end div -->
</div> <!-- end div -->
<script type="text/javascript"> 
	__mysys_apps.meTableSetCellPadding("__tbl_<?=$memodule;?>",5,'1px solid #7F7F7F');
</script>
