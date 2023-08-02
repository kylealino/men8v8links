<?php
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$this->db_erp = $this->myusermod->mydbname->medb(0);
$this->cuser = $this->myusermod->mysys_user();
$mpw_tkn = $this->myusermod->mpw_tkn();
$metoday = $this->myusermod->mylibzdb->getdate();
$memodule = "__rptivtybranchconso_";

//if the user has download access
$ldownload = $this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070708');
if($ldownload) { 
	$dloadpath = ROOTPATH . 'public/downloads/me/';
	$cfilename = 'ivty_dtl_dload_branc_conso_' . $this->mylibzsys->random_string(15) . '.xls';
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
			<td colspan=\"{$ncols}\">Inventory Conso</td> 
		</tr>
		<tr>
			<td colspan=\"{$ncols}\">Run Date: " . $this->mylibzsys->mydate_mmddyyyy($metoday) . ' ' . substr($metoday,11,8)  . "   By:&nbsp;" . $this->cuser . "</td> 
		</tr>
	";
	$cmsexp .= "<tr>";
	foreach ($rfieldnames as $field) {
		$cmsexp .= "<td>{$field}</td>";
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
					<tr>
					<?php
					$chtml = "";
					foreach ($rfieldnames as $field) {
						$chtml .= "<th>{$field}</th>";
					} 
					echo $chtml;
					?>
					</tr>
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
								if($field == 'Cost Amount' || $field == 'SRP Amount'):
									$medata = number_format($medata,2,'.',',');
									$meclass = "class=\"text-end fw-bold\"";
								else:
									if($row[$field] != 0): 
										$meclass = "class=\"text-end fw-bold\"";
									endif;
									$medata = number_format($medata,4,'.',',');
								endif;
							endif;
							$chtml .= "<td {$meclass} nowrap>{$medata}</td>";
						} //end foreach
						$chtml .= "</tr>";
						echo $chtml;
						
						// put content to downloadable format file 
						if($ldownload) { 
							$cmsexp = "<tr>";
							foreach ($rfieldnames as $field) { 
								$medata = $row[$field];
								$meclass = "";
								if (is_numeric($medata)): 
									if($field == 'Cost Amount' || $field == 'SRP Amount'):
										$medata = number_format($medata,2,'.',',');
									else:
										$medata = number_format($medata,4,'.',',');
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
