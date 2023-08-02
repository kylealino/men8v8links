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

$mecustcode = '';
$mecustname = '';
$mecusttinno = '';
$recactive = ' checked ';
$mecustaddr1 = '';
$mecustaddr2 = '';
$mecustaddr3 = '';
$mecusttelno = '';
$mecustfaxno = '';
$mecustemail = '';
$mecustcpname = '';
$mecustcpdesgn = '';
$mecustcpcontno = '';
$mecustcpemail = '';
$mecustwsite = '';
$mecustocontinfo = '';
if(!empty($mtkn_etr)) { 
	$str = "select * from {$db_erp}.`mst_customer` aa WHERE sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr' ";
	$q =  $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	if($q->getNumRows() > 0) { 
		//$rrec = $q->getResultArray();
		$rw = $q->getRowArray();
		//foreach($rrec as $row):
		//endforeach;
		$mecustcode = $rw['CUST_CODE'];
		$mecustname = $rw['CUST_NAME'];
		$mecusttinno = $rw['CUST_TINNO'];
		$recactive = ($rw['CUST_RFLAG'] == 'Y' ? ' checked ' : '');
		$mecustaddr1 = $rw['CUST_ADDR1'];
		$mecustaddr2 = $rw['CUST_ADDR2'];
		$mecustaddr3 = $rw['CUST_ADDR3'];
		$mecusttelno = $rw['CUST_TELNO'];
		$mecustfaxno = $rw['CUST_FAXNO'];
		$mecustemail = $rw['CUST_EMAIL'];
		$mecustwsite = $rw['CUST_WEBSITE'];
		$mecustocontinfo = $rw['CUST_OCONTINFO'];
		$mecustcpname = $rw['CUST_CPRSN'];
		$mecustcpdesgn = $rw['CUST_CPRSN_DESGN'];
		$mecustcpcontno = $rw['CUST_CPRSN_TELNO'];
		$mecustcpemail = $rw['CUST_CPRSN_EMAIL'];
		

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
                    <input type="text" name="mecustcode" id="mecustcode" class="form-control form-control-sm" placeholder="" aria-label="Customer Code" aria-describedby="Customer-Code-addon" value="<?=$mecustcode;?>" required>
                </div>
                <label>Customer Name</label>
                <div class="mb-3">
                    <input type="text" name="mecustname" id="mecustname" class="form-control form-control-sm" placeholder="" aria-label="Customer Name" aria-describedby="Customer-Name-addon" value="<?=$mecustname;?>" required>
                </div>
                <label>TIN No.</label>
                <div class="mb-3">
                    <input type="text" name="mecusttinno" id="mecusttinno" class="form-control form-control-sm" placeholder="" aria-label="Customer TINNO" aria-describedby="Customer-TINNO-addon" value="<?=$mecusttinno;?>">
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
                    <input type="text" name="mecustaddr1" id="mecustaddr1" class="form-control form-control-sm" placeholder="" aria-label="Customer Address1" aria-describedby="Customer-Address1-addon" value="<?=$mecustaddr1;?>">
                </div>
                <label>Town / Municipality / City</label>
                <div class="mb-3">
                    <input type="text" name="mecustaddr2" id="mecustaddr2" class="form-control form-control-sm" placeholder="" aria-label="Customer Address2" aria-describedby="Customer-Address2-addon" value="<?=$mecustaddr2;?>">
                </div>
                <label>Province</label>
                <div class="mb-3">
                    <input type="text" name="mecustaddr3" id="mecustaddr3" class="form-control form-control-sm" placeholder="" aria-label="Customer Address1" aria-describedby="Customer-Address3-addon" value="<?=$mecustaddr3;?>">
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
                    <input type="text" name="mecusttelno" id="mecusttelno" class="form-control form-control-sm" placeholder="" aria-label="Customer TelNo" aria-describedby="customer-telno-addon" value="<?=$mecusttelno;?>">
                </div>
                <label>Fax No,:</label>
                <div class="mb-3">
                    <input type="text" name="mecustfaxno"  id="mecustfaxno" class="form-control form-control-sm" placeholder="" aria-label="Customer FaxNO" aria-describedby="customer-faxno-addon" value="<?=$mecustfaxno;?>">
                </div>
                <label>E-Mail:</label>
                <div class="mb-3">
                    <input type="text" name="mecustemail" id="mecustemail" class="form-control form-control-sm" placeholder="" aria-label="Customer Email" aria-describedby="customer-email-addon" value="<?=$mecustemail;?>">
                </div>
                <label>Web Site:</label>
                <div class="mb-3">
                    <input type="text" name="mecustwsite" id="mecustwsite" class="form-control form-control-sm" placeholder="" aria-label="Customer Website" aria-describedby="customer-Website-addon" value="<?=$mecustwsite;?>">
                </div>
                <label>Other Contact Info:</label>
                <div class="mb-3">
                    <textarea name="mecustocontinfo" id="mecustocontinfo" rows="5" class="form-control form-control-sm"><?=$mecustocontinfo;?></textarea>
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
                    <input type="text" name="mecustcpname" id="mecustcpname" class="form-control form-control-sm" placeholder="" aria-label="Contact Person Name" aria-describedby="customer-contact-person-name-addon" value="<?=$mecustcpname;?>">
                </div>
                <label>Contact Designation:</label>
                <div class="mb-3">
                    <input type="text" name="mecustcpdesgn" id="mecustcpdesgn" class="form-control form-control-sm" placeholder="" aria-label="Contact Person Designation" aria-describedby="customer-contact-person-designation-addon" value="<?=$mecustcpdesgn;?>">
                </div>
                <label>Contact Tel No./Mob. No.:</label>
                <div class="mb-3">
                    <input type="text" name="mecustcpcontno" id="mecustcpcontno" class="form-control form-control-sm" placeholder="" aria-label="Contact Person Contact Nos" aria-describedby="customer-contact-person-contno-addon" value="<?=$mecustcpcontno;?>">
                </div>
                <label>Contact E-Mail:</label>
                <div class="mb-3">
                    <input type="text" name="mecustcpemail" id="mecustcpemail" class="form-control form-control-sm" placeholder="" aria-label="Contact Person Email" aria-describedby="email-addon" value="<?=$mecustcpemail;?>">
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-success btn-sm fw-bolder" title="Save Record..." alt="Save Record...">Save</button>
                    <button class="btn btn-danger btn-sm fw-bolder" id="mbtn_profcancel" title="Cancel and Closed" alt="Cancel and Closed">Cancel</button>
                    <?=anchor('mymd-customer/?maction=A_REC', 'New Record',' class="btn btn-info btn-sm fw-bolder" alt="New Record" title="New Record" ');?>
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
					var mecustcode = jQuery('#mecustcode').val();
					var mecustname = jQuery('#mecustname').val();
					var mecusttinno = jQuery('#mecusttinno').val();
					var mtkn_etr = '<?=$mtkn_etr;?>';
					var merecflag = (jQuery('#flexSwitchCheckrflag').is(':checked') ? 'Y' : 'N');
					var maction = '<?=$maction;?>';
					var mecustaddr1 = jQuery('#mecustaddr1').val();
					var mecustaddr2 = jQuery('#mecustaddr2').val();
					var mecustaddr3 = jQuery('#mecustaddr3').val();
					var mecusttelno = jQuery('#mecusttelno').val();
					var mecustfaxno = jQuery('#mecustfaxno').val();
					var mecustemail = jQuery('#mecustemail').val();
					var mecustcpname = jQuery('#mecustcpname').val();
					var mecustcpdesgn = jQuery('#mecustcpdesgn').val();
					var mecustcpcontno = jQuery('#mecustcpcontno').val();
					var mecustcpemail = jQuery('#mecustcpemail').val();
					var mecustwsite = jQuery('#mecustwsite').val();
					var mecustocontinfo = jQuery('#mecustocontinfo').val();
					var mparam = {
						mtkn_etr: mtkn_etr,
						mecustcode: mecustcode,
						mecustname: mecustname,
						merecflag: merecflag,
						mecusttinno: mecusttinno,
						maction: maction,
						mecustaddr1: mecustaddr1,
						mecustaddr2: mecustaddr2,
						mecustaddr3: mecustaddr3,
						mecusttelno: mecusttelno,
						mecustfaxno: mecustfaxno,
						mecustemail: mecustemail,
						mecustcpname: mecustcpname,
						mecustcpdesgn: mecustcpdesgn,
						mecustcpcontno: mecustcpcontno,
						mecustcpemail: mecustcpemail,
						mecustwsite: mecustwsite,
						mecustocontinfo: mecustocontinfo
					};
					
					jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>mymd-customer-profile-save',
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
			window.location.href = '<?=site_url();?>mymd-customer';
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
