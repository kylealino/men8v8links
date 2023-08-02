<?php
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$this->db_erp = $this->myusermod->mydbname->medb(0);
$this->cuser = $this->myusermod->mysys_user();

$medatef = $this->mylibzsys->mydate_mmddyyyy($this->myusermod->request->getVar('medatef'));
$medatet = $this->mylibzsys->mydate_mmddyyyy($this->myusermod->request->getVar('medatet'));
$memodule = "__saleout_AcctPOS_TAXR__";

//if the user has download access
$ldownload = $this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'04','0007','000208');
if($ldownload) { 
	$metoday = $this->myusermod->mylibzdb->getdate();
	$dloadpath = ROOTPATH . 'public/downloads/me/';
	$cfilename = 'ivty_dtl_dload_postaxr_' . $this->mylibzsys->random_string(15) . '.xls';
	$mfile = $dloadpath . $cfilename;
	$cfilelink = site_url() . '/downloads/me/' . $cfilename;
	if (file_exists($mfile)) { 
		unlink($mfile);
	}
	$ncols = count($rfieldnames);
	$cmsexp =  "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
	<head>
	<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
	</head>
	<body>
	<table>
		<tr>
			<th colspan=\"{$ncols}\">Branch POS TAXR</th> 
		</tr>
		<tr>
			<th colspan=\"{$ncols}\">From {$medatef} to {$medatet}</th> 
		</tr>
		<tr>
			<th colspan=\"{$ncols}\">Run Date: " . $this->mylibzsys->mydate_mmddyyyy($metoday) . ' ' . substr($metoday,11,8)  . "   By:&nbsp;" . $this->cuser . "</th> 
		</tr>
	";
	$cmsexp .= "<tr>";
	foreach ($rfieldnames as $field) {
		$cmsexp .= "<th>{$field}</th>";
	} 
	$cmsexp .= "</tr>";
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
					<?php
					$chtml = "
					<tr>
						<th colspan=\"{$ncols}\">Branch POS TAXR</th> 
					</tr>
					<tr>
						<th colspan=\"{$ncols}\">From {$medatef} to {$medatet}</th> 
					</tr>
					<tr>
						<th colspan=\"{$ncols}\">Run Date: " . $this->mylibzsys->mydate_mmddyyyy($metoday) . ' ' . substr($metoday,11,8)  . "   By:&nbsp;" . $this->cuser . "</th> 
					</tr>
					<tr>";
					foreach ($rfieldnames as $field) {
						$chtml .= "<th>{$field}</th>";
					} 
					$chtml .= "</tr>";
					echo $chtml;
					?>
				</thead>
				<tbody>
				<?php 
				if($rlist !== ''):
					$nn = 1;
					foreach($rlist as $row): 
						$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
						$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
						$chtml = "<tr style=\"background-color: {$bgcolor} !important;\" {$on_mouse}>";
						foreach ($rfieldnames as $field) {
							$medata = $row[$field];
							$meclass = "";
							if (is_numeric($medata)): 
								$meclass = "class=\"text-end\"";
								if($field == 'Beg. S.I' || $field == 'End S.I' || $field == 'S.I. Count'):
									$medata = number_format($medata,0,'',',');
								elseif ( $field == 'Qty Sold'): 
									$medata = number_format($medata,4,'.',',');
								elseif ( $field == 'M.I.N.'): 
								else:
									$medata = number_format($medata,2,'.',',');
								endif;
							endif;
							$chtml .= "<td {$meclass} nowrap>{$medata}</td>";
						}
						$chtml .= "</tr>";
						echo $chtml;
						
						// put content to downloadable format file 
						if($ldownload) { 
							$cmsexp = "<tr>";
							foreach ($rfieldnames as $field) { 
								$medata = $row[$field];
								$meclass = "";
								if (is_numeric($medata)): 
									if($field == 'Beg. S.I' || $field == 'End S.I' || $field == 'S.I. Count'):
										$medata = number_format($medata,0,'',',');
									elseif ( $field == 'Qty Sold'): 
										$medata = number_format($medata,4,'.',',');
									elseif ( $field == 'M.I.N.'): 
										$medata = "'" . $row[$field];
									else:
										$medata = number_format($medata,2,'.',',');
									endif;
								endif;
								$cmsexp .= "<td>{$medata}</td>";
							}  //endforeach 
							$cmsexp .= "</tr>";			
							file_put_contents ( $mfile , $cmsexp , FILE_APPEND | LOCK_EX ); 
						}  //end if ldownload 	
						$nn++;	
					endforeach;
				endif;
				?> 
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript"> 
	__mysys_apps.meTableSetCellPadding("__tbl_<?=$memodule;?>",5,'1px solid #7F7F7F');
</script>
