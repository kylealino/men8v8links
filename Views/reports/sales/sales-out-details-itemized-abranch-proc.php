<?php
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$medatef = $this->mylibzsys->mydate_mmddyyyy($this->myusermod->request->getVar('medatef'));
$medatet = $this->mylibzsys->mydate_mmddyyyy($this->myusermod->request->getVar('medatet'));
$memodule = "__saleout_itemized_abranch__";
?>
<div class="table-responsive">
	<div class="col-md-12 col-md-12 col-md-12">
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
					$chtml = "<tr>";
					foreach ($rfieldnames as $field) {
						$medata = $row[$field];
						$meclass = "";
						if (is_numeric($medata)): 
							$meclass = "class=\"text-end\"";
						endif;
						$chtml .= "<td {$meclass} nowrap>{$medata}</td>";
					}
					$chtml .= "</tr>";
					echo $chtml;
					$nn++;	
				endforeach;
			endif;
			?> 
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript"> 
	__mysys_apps.meTableSetCellPadding("__tbl_<?=$memodule;?>",5,'1px solid #7F7F7F');
</script>
