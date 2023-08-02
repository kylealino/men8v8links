<?php
/**
 *	File        : masterdata/md-customer-profile.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Sept 02, 2022
 * 	last update : Sept 02, 2022
 * 	description : Customer Profile Entry
 */
 
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$mylibzsys = model('App\Models\MyLibzSysModel');
$cuser = $mylibzdb->mysys_user();
$mpw_tkn = $mylibzdb->mpw_tkn();
$maction = $request->getVar('maction');
$mtkn_etr = $request->getVar('mtkn_etr');

$mesplrcode = '';
$mesplrname = '';
$mesplrtinno = '';
$recactive = ' checked ';
$mesplraddr1 = '';
$mesplraddr2 = '';
$mesplraddr3 = '';
$mesplrtelno = '';
$mesplrfaxno = '';
$mesplremail = '';
$mesplrcpname = '';
$mesplrcpdesgn = '';
$mesplrcpcontno = '';
$mesplrcpemail = '';
$mesplrwsite = '';
$mesplrocontinfo = '';
if(!empty($mtkn_etr)) { 
	$str = "select * from {$db_erp}.`mst_supplier` aa WHERE sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr' ";
	$q =  $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	if($q->getNumRows() > 0) { 
		//$rrec = $q->getResultArray();
		$rw = $q->getRowArray();
		//foreach($rrec as $row):
		//endforeach;
		$mesplrcode = $rw['SPLR_CODE'];
		$mesplrname = $rw['SPLR_NAME'];
		$mesplrtinno = $rw['SPLR_TINNO'];
		$recactive = ($rw['SPLR_RFLAG'] == 'Y' ? ' checked ' : '');
		$mesplraddr1 = $rw['SPLR_ADDR1'];
		$mesplraddr2 = $rw['SPLR_ADDR2'];
		$mesplraddr3 = $rw['SPLR_ADDR3'];
		$mesplrtelno = $rw['SPLR_TELNO'];
		$mesplrfaxno = $rw['SPLR_FAXNO'];
		$mesplremail = $rw['SPLR_EMAIL'];
		$mesplrwsite = $rw['SPLR_WEBSITE'];
		$mesplrocontinfo = $rw['SPLR_OCONTINFO'];
		$mesplrcpname = $rw['SPLR_CPRSN'];
		$mesplrcpdesgn = $rw['SPLR_CPRSN_DESGN'];
		$mesplrcpcontno = $rw['SPLR_CPRSN_TELNO'];
		$mesplrcpemail = $rw['SPLR_CPRSN_EMAIL'];
		

	}
	$q->freeResult();
}

?>
<?=form_open('mymd-customer-profile-save','class="row needs-validation" id="myfrmsrec_customer" ');?>
    <div class="col-12 col-xl-4 mt-1">
        <div class="card h-100">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Customer Info</h6>
            </div>
            <div class="card-body p-3">
                <label>Customer Code</label>
                <div class="mb-3">
                    <input type="text" name="mesplrcode" id="mesplrcode" class="form-control form-control-sm" placeholder="" aria-label="Customer Code" aria-describedby="Customer-Code-addon" value="<?=$mesplrcode;?>" required>
                </div>
                <label>Customer Name</label>
                <div class="mb-3">
                    <input type="text" name="mesplrname" id="mesplrname" class="form-control form-control-sm" placeholder="" aria-label="Customer Name" aria-describedby="Customer-Name-addon" value="<?=$mesplrname;?>" required>
                </div>
                <label>TIN No.</label>
                <div class="mb-3">
                    <input type="text" name="mesplrtinno" id="mesplrtinno" class="form-control form-control-sm" placeholder="" aria-label="Customer TINNO" aria-describedby="Customer-TINNO-addon" value="<?=$mesplrtinno;?>">
                </div>
              <ul class="list-group">
                <li class="list-group-item border-0 px-0">
                  <div class="form-check form-switch ps-0">
                    <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckrflag" <?=$recactive;?>>
                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="flexSwitchCheckrflag">Acive</label>
                  </div>
                </li>
              </ul>
            </div> <!-- end card body -->
        </div> <!-- end article info -->
    </div> <!-- end col-12 -->
    <div class="col-12 col-xl-4 mt-1">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Adress</h6>
            </div>
            <div class="card-body p-3">
                <label>Street / Barangay</label>
                <div class="mb-3">
                    <input type="text" name="mesplraddr1" id="mesplraddr1" class="form-control form-control-sm" placeholder="" aria-label="Customer Address1" aria-describedby="Customer-Address1-addon" value="<?=$mesplraddr1;?>">
                </div>
                <label>Town / Municipality / City</label>
                <div class="mb-3">
                    <input type="text" name="mesplraddr2" id="mesplraddr2" class="form-control form-control-sm" placeholder="" aria-label="Customer Address2" aria-describedby="Customer-Address2-addon" value="<?=$mesplraddr2;?>">
                </div>
                <label>Province</label>
                <div class="mb-3">
                    <input type="text" name="mesplraddr3" id="mesplraddr3" class="form-control form-control-sm" placeholder="" aria-label="Customer Address1" aria-describedby="Customer-Address3-addon" value="<?=$mesplraddr3;?>">
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
        <div class="card mt-1">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Conctact Info</h6>
            </div>
            <div class="card-body p-3">
                <label>Tel No.:</label>
                <div class="mb-3">
                    <input type="text" name="mesplrtelno" id="mesplrtelno" class="form-control form-control-sm" placeholder="" aria-label="Customer TelNo" aria-describedby="customer-telno-addon" value="<?=$mesplrtelno;?>">
                </div>
                <label>Fax No,:</label>
                <div class="mb-3">
                    <input type="text" name="mesplrfaxno"  id="mesplrfaxno" class="form-control form-control-sm" placeholder="" aria-label="Customer FaxNO" aria-describedby="customer-faxno-addon" value="<?=$mesplrfaxno;?>">
                </div>
                <label>E-Mail:</label>
                <div class="mb-3">
                    <input type="text" name="mesplremail" id="mesplremail" class="form-control form-control-sm" placeholder="" aria-label="Customer Email" aria-describedby="customer-email-addon" value="<?=$mesplremail;?>">
                </div>
                <label>Web Site:</label>
                <div class="mb-3">
                    <input type="text" name="mesplrwsite" id="mesplrwsite" class="form-control form-control-sm" placeholder="" aria-label="Customer Website" aria-describedby="customer-Website-addon" value="<?=$mesplrwsite;?>">
                </div>
                <label>Other Contact Info:</label>
                <div class="mb-3">
                    <textarea name="mesplrocontinfo" id="mesplrocontinfo" rows="5" class="form-control form-control-sm"><?=$mesplrocontinfo;?></textarea>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card -->        
    </div> <!-- end Adress -->
    
    <div class="col-12 col-xl-4 mt-1">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Contact Person</h6>
            </div>
            <div class="card-body p-3">
                <label>Contact Name:</label>
                <div class="mb-3">
                    <input type="text" name="mesplrcpname" id="mesplrcpname" class="form-control form-control-sm" placeholder="" aria-label="Contact Person Name" aria-describedby="customer-contact-person-name-addon" value="<?=$mesplrcpname;?>">
                </div>
                <label>Contact Designation:</label>
                <div class="mb-3">
                    <input type="text" name="mesplrcpdesgn" id="mesplrcpdesgn" class="form-control form-control-sm" placeholder="" aria-label="Contact Person Designation" aria-describedby="customer-contact-person-designation-addon" value="<?=$mesplrcpdesgn;?>">
                </div>
                <label>Contact Tel No./Mob. No.:</label>
                <div class="mb-3">
                    <input type="text" name="mesplrcpcontno" id="mesplrcpcontno" class="form-control form-control-sm" placeholder="" aria-label="Contact Person Contact Nos" aria-describedby="customer-contact-person-contno-addon" value="<?=$mesplrcpcontno;?>">
                </div>
                <label>Contact E-Mail:</label>
                <div class="mb-3">
                    <input type="text" name="mesplrcpemail" id="mesplrcpemail" class="form-control form-control-sm" placeholder="" aria-label="Contact Person Email" aria-describedby="email-addon" value="<?=$mesplrcpemail;?>">
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-success btn-sm fw-bolder" title="Save Record..." alt="Save Record...">Save</button>
                    <button class="btn btn-danger btn-sm fw-bolder" id="mbtn_profcancel" title="Cancel and Closed" alt="Cancel and Closed">Cancel</button>
                    <?=anchor('mymd-supplier/?maction=A_REC', 'New Record',' class="btn btn-info btn-sm fw-bolder" alt="New Record" title="New Record" ');?>
                </div>                
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end article Pricing/Cost -->    
<?=form_close();
echo $mylibzsys->memsgbox1('meprofsavemsg','System Message','...');
echo $mylibzsys->memsgbox_yesno1('meprofcancmsg','Closed and Cancel Material Profile Changes','Cancel changes made?');

?> <!-- end of ./form -->
<script type="text/javascript"> 
	
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
					__mysys_apps.mepreloader('mepreloaderme',true);
					var mesplrcode = jQuery('#mesplrcode').val();
					var mesplrname = jQuery('#mesplrname').val();
					var mesplrtinno = jQuery('#mesplrtinno').val();
					var mtkn_etr = '<?=$mtkn_etr;?>';
					var merecflag = (jQuery('#flexSwitchCheckrflag').is(':checked') ? 'Y' : 'N');
					var maction = '<?=$maction;?>';
					var mesplraddr1 = jQuery('#mesplraddr1').val();
					var mesplraddr2 = jQuery('#mesplraddr2').val();
					var mesplraddr3 = jQuery('#mesplraddr3').val();
					var mesplrtelno = jQuery('#mesplrtelno').val();
					var mesplrfaxno = jQuery('#mesplrfaxno').val();
					var mesplremail = jQuery('#mesplremail').val();
					var mesplrcpname = jQuery('#mesplrcpname').val();
					var mesplrcpdesgn = jQuery('#mesplrcpdesgn').val();
					var mesplrcpcontno = jQuery('#mesplrcpcontno').val();
					var mesplrcpemail = jQuery('#mesplrcpemail').val();
					var mesplrwsite = jQuery('#mesplrwsite').val();
					var mesplrocontinfo = jQuery('#mesplrocontinfo').val();
					var mparam = {
						mtkn_etr: mtkn_etr,
						mesplrcode: mesplrcode,
						mesplrname: mesplrname,
						merecflag: merecflag,
						mesplrtinno: mesplrtinno,
						maction: maction,
						mesplraddr1: mesplraddr1,
						mesplraddr2: mesplraddr2,
						mesplraddr3: mesplraddr3,
						mesplrtelno: mesplrtelno,
						mesplrfaxno: mesplrfaxno,
						mesplremail: mesplremail,
						mesplrcpname: mesplrcpname,
						mesplrcpdesgn: mesplrcpdesgn,
						mesplrcpcontno: mesplrcpcontno,
						mesplrcpemail: mesplrcpemail,
						mesplrwsite: mesplrwsite,
						mesplrocontinfo: mesplrocontinfo
					};
					
					jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>mymd-supplier-profile-save',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
						success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#meprofsavemsg_bod').html(data);
								jQuery('#meprofsavemsg').modal('show');
								
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
			}, false)
		})
	})();
	
	jQuery('#mbtn_profcancel').click(function() { 
		try { 
			jQuery('#meprofcancmsg').modal('show');
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
	
	jQuery('#meprofcancmsg_yes').click(function() { 
		try { 
			window.location.href = '<?=site_url();?>mymd-supplier';
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
</script>
