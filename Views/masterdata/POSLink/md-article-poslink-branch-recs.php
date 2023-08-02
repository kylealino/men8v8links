<?php
$this->mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$this->myposconn = model('App\Models\MyPOSConnModel');
$this->db_erp = $this->myusermod->mydbname->medb(0);
$this->cuser = $this->myusermod->mysys_user();
$this->mpw_tkn = $this->myusermod->mpw_tkn();

$me_branch = '';
$bid_mtknattr = '';
$metkntmp = '';
$memodule = "__martm_branches__";
$metoday = $this->myusermod->mylibzdb->getdate();
$txtsearchedrec = $this->myusermod->request->getVar('txtsearchedrec');
?>
<div class="row mt-2 m-0 p-1">
	<div class="col-sm-6">
		<div class="input-group input-group-sm">
			<span class="input-group-text fw-bold" id="mebtnGroupAddon">Search:</span>
			<input type="text" class="form-control form-control-sm" name="mytxtsearchrec_<?=$memodule;?>" placeholder="Search Item Code / Barcode / Item Description" id="mytxtsearchrec_<?=$memodule;?>" aria-label="Search-Item Code" aria-describedby="mebtnGroupAddon" value="<?=$txtsearchedrec;?>" required/>
			<div class="invalid-feedback">Please fill out this field.</div>
			<button type="button" class="btn btn-success btn-sm" id="mbtn_<?=$memodule;?>_search"><i class="bi bi-search"></i></button>
			<button type="button" class="btn btn-success btn-sm" id="mbtn_<?=$memodule;?>_reset"><i class="bi bi-bootstrap-reboot"></i> Reset</button>
			<?php
			//user download access if permitted
			if($this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'04','0001','000104')) { 
			?>
			<form method="post" action="<?=base_url();?>mymd-article-master-poslink-download" id="myfrm_poslinkmdata_dload">
				<button class="btn btn-info btn-sm" id="btn_stockcard_dl" type="submit">Download</button>
				<input type="hidden" name="mdl_me_branch" id="mdl_me_branch" value="<?=$me_branch;?>" />
				<input type="hidden" name="mdl_me_branch_mtkn" id="mdl_me_branch_mtkn" value="<?=$bid_mtknattr;?>" />
				<input type="hidden" name="mdl_metkntmp" id="mdl_metkntmp" value="<?=$metkntmp;?>" />
			</form>
			<?php
			}
			?> 
		</div>
	</div>
</div>
<div class="row mt-2 m-0 p-1">
	<div class="col-sm-8">
		<?=$this->mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_' . $memodule,'');?>
	</div>
</div>

<div class="table-responsive">
	<div class="col-md-12 col-md-12 col-md-12">
		<table class="metblentry-font table-bordered" id="__tbl_<?=$memodule;?>">
			<thead>
				<?php
				if($rlist !== ''):
					$ncolspan = count($rfieldnames);
					$chtml = "
					<tr>
						<th colspan=\"$ncolspan\">Branch Product Master</th>
					</tr>
					<th colspan=\"{$ncolspan}\">Run Date: " . $this->mylibzsys->mydate_mmddyyyy($metoday) . ' ' . substr($metoday,11,8)  . "   By:&nbsp;" . $this->cuser . "</th> 
					<tr>";
					foreach ($rfieldnames as $field) {
						$chtml .= "<th>{$field}</th>";
					} 
					$chtml .= "</tr>";
					echo $chtml;
				endif;
				?>
			</thead>
			<tbody>
			<?php 
			if($rlist !== ''):
				$nn = 1;
				$ntQty = 0; $ntNet = 0;
				foreach($rlist as $row): 
					$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
					$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";
					$chtml = "<tr>";
					$B_CODE = $row['Branch Code'];
					$POS_PRODID = $row['POS_MPRODID'];
					foreach ($rfieldnames as $field) { 
						
						$meclass = "";
						$medata = $row[$field];
						if($field == "POS SRP"):
							$meprice = $this->myposconn->POS_Check_Branch_Price($row['Branch Code'],$row['POS_MPRODID']);
							if(round($medata,2) !== round($meprice,2)): 
								$meclass = "class=\"text-end fw-bold text-danger\"";
								$medata = $meprice;
							endif;
						endif;
						if (is_numeric($medata)): 
							$meclass = "class=\"text-end\"";
						endif;
						$chtml .= "<td {$meclass} nowrap>{$medata}</td>";
					}
					$chtml .= "</tr>";
					echo $chtml;
					$nn++;	
				endforeach;
				$ncolspan = count($rfieldnames);
			endif;
			?> 
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript"> 
	__mysys_apps.meTableSetCellPadding("__tbl_<?=$memodule;?>",5,'1px solid #7F7F7F');
	
	function __myredirected_<?=$memodule;?>(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
			var meartmbranch = jQuery('#meartmbranch').val();
			var mparam = { mebcode: meartmbranch,
				txtsearchedrec: txtsearchedrec,
				mpages: mobj 
			};

			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>mymd-article-master-poslink-branch-recs',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data) {
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wg_artm_branch_recs').html(data);
					return false;
				},
				error: function() {
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 

		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		} 
	} //end __myredirected_rsearch	
		
	jQuery('#mbtn_<?=$memodule;?>_search').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
			var meartmbranch = jQuery('#meartmbranch').val();
			var mparam = { mebcode: meartmbranch,
				txtsearchedrec: txtsearchedrec,
				mpages: 1 
			};

			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>mymd-article-master-poslink-branch-recs',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data) {
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wg_artm_branch_recs').html(data);
					return false;
				},
				error: function() {
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 

		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		} 		
	}); //click search 
	
	jQuery('#mbtn_<?=$memodule;?>_reset').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
			var meartmbranch = jQuery('#meartmbranch').val();
			var mparam = { mebcode: meartmbranch,
				txtsearchedrec: '',
				mpages: 1 
			};

			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>mymd-article-master-poslink-branch-recs',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data) {
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#wg_artm_branch_recs').html(data);
					return false;
				},
				error: function() {
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 

		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		} 		
	}); //click reset	
	
	jQuery('#mytxtsearchrec_<?=$memodule;?>').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				var txtsearchedrec = jQuery('#mytxtsearchrec_<?=$memodule;?>').val();
				var meartmbranch = jQuery('#meartmbranch').val();
				var mparam = { mebcode: meartmbranch,
					txtsearchedrec: txtsearchedrec,
					mpages: 1 
				};
				if(jQuery.trim(txtsearchedrec) == '') {
					return false;
				}
				
				__mysys_apps.mepreloader('mepreloaderme',true);
				
				jQuery.ajax({ 
					type: "POST",
					url: '<?=site_url()?>mymd-article-master-poslink-branch-recs',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
					success: function(data) {
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#wg_artm_branch_recs').html(data);
						return false;
					},
					error: function() {
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
			
		}
	});	 //end keypress event 		
</script>
