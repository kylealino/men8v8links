<?php
/**
 *	File        : masterdata/md-qpr-employee-profile.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Nov 28, 2022
 * 	last update : Nov 28, 2022
 * 	description : QPR Employee Profile Entry
 */
 
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mydbname = model('App\Models\MyDBNamesModel');
$db_erp = $mydbname->medb(0);
$mylibzsys = model('App\Models\MyLibzSysModel');

$mydatum = model('App\Models\MyDatumModel');
$agender = $mydatum->get_mst_gender();


$cuser = $mylibzdb->mysys_user();
$mpw_tkn = $mylibzdb->mpw_tkn();
$maction = $request->getVar('maction');
$mtkn_etr = $request->getVar('mtkn_etr');

$recactive = ' checked ';

$meenumb = '';
$meelname = '';
$meefname = '';
$meemname = '';
$meegend = '';

$meebdte = '';
$meebplace = '';
$meectzns = '';
$meerelgn = '';
$meecs = '';

$meeaddr1 = '';
$meeaddr2 = '';
$meeaddr3 = '';

$meecpno = '';
$meetelno = '';
$meeemail = '';
$meecontinfo = '';

$meecpname = '';
$meecpdesgn = '';
$meecpcontno = '';
$meecpemail = '';
$meecprela = '';

$meetinno = '';
$meesssno = '';
$meehdmfno = '';
$meephilhno = '';

$medateh = '';
if(!empty($mtkn_etr)) { 
	$str = "select * from {$db_erp}.`mst_employee` aa WHERE sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mtkn_etr' ";
	$q =  $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	if($q->getNumRows() > 0) { 
		//$rrec = $q->getResultArray();
		$rw = $q->getRowArray();
		//foreach($rrec as $row):
		//endforeach;
		$meenumb = $rw['EMPNUMB'];
		$meelname = $rw['EMPLNAME'];
		$meefname = $rw['EMPFNAME'];
		$meemname = $rw['EMPMNAME'];
		$meebdte = $mylibzsys->mydate_mmddyyyy($rw['EMPBDTE']);
		$medateh = $mylibzsys->mydate_mmddyyyy($rw['EMPHDTE']);
		$meebplace = $rw['EMPBPLACE'];
		$meectzns = $rw['EMPCTZN'];
		$meegend = $rw['EMPGNDR'];
		$meerelgn = $rw['EMPRLGN'];
		$meecs = $rw['EMPCVLS'];
		$meeaddr1 = $rw['EMPADDR1'];
		$meeaddr2 = $rw['EMPADDR2'];
		$meeaddr3 = $rw['EMPADDR3'];
		$meecpno = $rw['EMPMOBN'];
		$meetelno = $rw['EMPTELN'];
		$meeemail = $rw['EMPEMAIL'];
		$meecontinfo = $rw['EMPOCNUM'];
		$meecpname = $rw['EMPCPNAME'];
		$meecpdesgn = $rw['EMPCPDESGN'];
		$meecpcontno = $rw['EMPCPCONTN'];
		$meecpemail = $rw['EMPCPEMAIL'];
		$meecprela = $rw['EMPCPRELA'];
		$meetinno = $rw['EMPTINNO'];
		$meehdmfno = $rw['EMPHDMFID'];
		$meephilhno = $rw['EMPPHILHID'];
		$meesssno = $rw['EMPSSSNO'];
		$recactive = ($rw['EMPRSTAT'] == 'A' ? ' checked ' : '');

	}
	$q->freeResult();
}

?>
<?=form_open('mymd-customer-profile-save','class="row needs-validation" id="myfrmsrec_customer" ');?>
    <div class="col-12 col-xl-4 mt-1">
        <div class="card h-100">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Employee Info</h6>
            </div>
            <div class="card-body p-3">
                <label>Employee No.:</label>
                <div class="mb-3">
                    <input type="text" name="meenumb" id="meenumb" class="form-control form-control-sm" placeholder="" aria-label="Employee Number" aria-describedby="Employee-Number-addon" value="<?=$meenumb;?>" required>
                </div>
                <label>Last Name:</label>
                <div class="mb-3">
                    <input type="text" name="meelname" id="meelname" class="form-control form-control-sm" placeholder="" aria-label="Employee Last Name" aria-describedby="Employee-Last-Name-addon" value="<?=$meelname;?>" required>
                </div>
                <label>First Name:</label>
                <div class="mb-3">
                    <input type="text" name="meefname" id="meefname" class="form-control form-control-sm" placeholder="" aria-label="Employee First Name" aria-describedby="Employee-First-Name-addon" value="<?=$meefname;?>" required>
                </div>
                <label>Middle Name:</label>
                <div class="mb-3">
                    <input type="text" name="meemname" id="meemname" class="form-control form-control-sm" placeholder="" aria-label="Employee Middle Name" aria-describedby="Employee-Middle-Name-addon" value="<?=$meemname;?>" required>
                </div>
                <label>Gender:</label>
                <div class="mb-3">
                    <?=$mylibzsys->mypopulist_2($agender,$meegend,'meegend',' class="form-control form-control-sm" required ');?>
                </div>
                <label>Birth Date:</label>
                <div class="mb-3">
                    <input type="text" name="meebdte" id="meebdte" class="form-control form-control-sm medatepicker" placeholder="mm/dd/yyyy" aria-label="Birth Date" aria-describedby="Birth-Date-addon" value="<?=$meebdte;?>" required>
                </div>
                <label>Birth Place:</label>
                <div class="mb-3">
                    <input type="text" name="meebplace" id="meebplace" class="form-control form-control-sm" placeholder="" aria-label="Birth Place" aria-describedby="Birth-Place-addon" value="<?=$meebplace;?>">
                </div>
                <label>Date Hired:</label>
                <div class="mb-3">
                    <input type="text" name="medateh" id="medateh" class="form-control form-control-sm medatepicker" placeholder="mm/dd/yyyy" aria-label="Date Hired" aria-describedby="Date-Hired-addon" value="<?=$medateh;?>">
                </div>
                <label>Citizenship:</label>
                <div class="mb-3">
                    <input type="text" name="meectzns" id="meectzns" class="form-control form-control-sm" placeholder="" aria-label="Citizenship" aria-describedby="Citizenship-addon" value="<?=$meectzns;?>" required>
                </div>
                <label>Religion:</label>
                <div class="mb-3">
                    <input type="text" name="meerelgn" id="meerelgn" class="form-control form-control-sm" placeholder="" aria-label="Religion" aria-describedby="Religion-addon" value="<?=$meerelgn;?>" required>
                </div>
                <label>Civil Status:</label>
                <div class="mb-3">
                    <input type="text" name="meecs" id="meecs" class="form-control form-control-sm" placeholder="" aria-label="Civil Status" aria-describedby="Civil-Status-addon" value="<?=$meecs;?>" required>
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
                    <input type="text" name="meeaddr1" id="meeaddr1" class="form-control form-control-sm" placeholder="" aria-label="Employee Address1" aria-describedby="Address1-addon" value="<?=$meeaddr1;?>">
                </div>
                <label>Town / Municipality / City</label>
                <div class="mb-3">
                    <input type="text" name="meeaddr2" id="meeaddr2" class="form-control form-control-sm" placeholder="" aria-label="Employee Address2" aria-describedby="Address2-addon" value="<?=$meeaddr2;?>">
                </div>
                <label>Province</label>
                <div class="mb-3">
                    <input type="text" name="meeaddr3" id="meeaddr3" class="form-control form-control-sm" placeholder="" aria-label="Employee Address1" aria-describedby="Address3-addon" value="<?=$meeaddr3;?>">
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
        <div class="card mt-1">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Conctact Info</h6>
            </div>
            <div class="card-body p-3">
                <label>Mobile No.:</label>
                <div class="mb-3">
                    <input type="text" name="meecpno" id="meecpno" class="form-control form-control-sm" placeholder="" aria-label="Employe Mobile/CP No." aria-describedby="employee-mobcp-addon" value="<?=$meecpno;?>">
                </div>
                <label>Tel No.:</label>
                <div class="mb-3">
                    <input type="text" name="meetelno" id="meetelno" class="form-control form-control-sm" placeholder="" aria-label="Employee Tel No" aria-describedby="employee-telno-addon" value="<?=$meetelno;?>">
                </div>
                <label>E-Mail:</label>
                <div class="mb-3">
                    <input type="text" name="meeemail" id="meeemail" class="form-control form-control-sm" placeholder="" aria-label="Employee e-Mail" aria-describedby="employee-email-addon" value="<?=$meeemail;?>">
                </div>
                <label alt="Instagram/Facebook/Youtube/Other Social Media" title="Instagram/Facebook/Youtube/Other Social Media">Other Contact Infos:</label>
                <div class="mb-3">
                    <textarea name="meecontinfo" id="meecontinfo" rows="5" class="form-control form-control-sm"><?=$meecontinfo;?></textarea>
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
                    <input type="text" name="meecpname" id="meecpname" class="form-control form-control-sm" placeholder="" aria-label="Employee Contact Person Name" aria-describedby="employee-contact-person-name-addon" value="<?=$meecpname;?>">
                </div>
                <label>Contact Designation:</label>
                <div class="mb-3">
                    <input type="text" name="meecpdesgn" id="meecpdesgn" class="form-control form-control-sm" placeholder="" aria-label="Employee Contact Person Designation" aria-describedby="employee-contact-person-designation-addon" value="<?=$meecpdesgn;?>">
                </div>
                <label>Contact Tel No./Mob. No.:</label>
                <div class="mb-3">
                    <input type="text" name="meecpcontno" id="meecpcontno" class="form-control form-control-sm" placeholder="" aria-label="Employee Contact Person Contact Nos" aria-describedby="employee-contact-person-contno-addon" value="<?=$meecpcontno;?>">
                </div>
                <label>Contact E-Mail:</label>
                <div class="mb-3">
                    <input type="text" name="meecpemail" id="meecpemail" class="form-control form-control-sm" placeholder="" aria-label="Employee Contact Person Email" aria-describedby="employee-contact-person-email-addon" value="<?=$meecpemail;?>">
                </div>
                <label>Relation Ship:</label>
                <div class="mb-3">
                    <input type="text" name="meecprela" id="meecprela" class="form-control form-control-sm" placeholder="" aria-label="Employee Contact Person Relation" aria-describedby="employee-contact-persion-relation-addon" value="<?=$meecprela;?>">
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
        <div class="card mt-1">
            <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Government ID/s</h6>
            </div>
            <div class="card-body p-3">
                <label>TIN No.:</label>
                <div class="mb-3">
                    <input type="text" name="meetinno" id="meetinno" class="form-control form-control-sm" placeholder="" aria-label="TIN Number" aria-describedby="TINNo-addon" value="<?=$meetinno;?>">
                </div>
                <label>SSS No.:</label>
                <div class="mb-3">
                    <input type="text" name="meesssno"  id="meesssno" class="form-control form-control-sm" placeholder="" aria-label="SSS Number" aria-describedby="SSSNo-addon" value="<?=$meesssno;?>">
                </div>
                <label>HDMF No.:</label>
                <div class="mb-3">
                    <input type="text" name="meehdmfno" id="meehdmfno" class="form-control form-control-sm" placeholder="" aria-label="Pag-Ibig Number" aria-describedby="PagIbig-Number-addon" value="<?=$meehdmfno;?>">
                </div>
                <label>PHIC No:</label>
                <div class="mb-3">
                    <input type="text" name="meephilhno" id="meephilhno" class="form-control form-control-sm" placeholder="" aria-label="PhilHealth Number" aria-describedby="PhilHealth-Number-addon" value="<?=$meephilhno;?>">
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-success btn-sm fw-bolder" title="Save Record..." alt="Save Record...">Save</button>
                    <button class="btn btn-danger btn-sm fw-bolder" id="mbtn_profcancel" title="Cancel and Closed" alt="Cancel and Closed">Cancel</button>
                    <?=anchor('mymd-qpr-employees/?maction=A_REC', 'New Record',' class="btn btn-info btn-sm fw-bolder" alt="New Record" title="New Record" ');?>
                </div>                              
            </div> <!-- end card-body -->
        </div> <!-- end card -->                
    </div> <!-- end article Pricing/Cost -->    
<?=form_close();
echo $mylibzsys->memsgbox1('meprofsavemsg','System Message','...');
echo $mylibzsys->memsgbox_yesno1('meprofcancmsg','Closed and Cancel Material Profile Changes','Cancel changes made?');

?> <!-- end of ./form -->
<script type="text/javascript"> 
	jQuery('.medatepicker').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true		
		});
	
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
					
					var medateh = jQuery('#medateh').val();
					
					var meenumb = jQuery('#meenumb').val();
					var meelname = jQuery('#meelname').val();
					var meefname = jQuery('#meefname').val();
					var meemname = jQuery('#meemname').val();
					var meegend = jQuery('#meegend').val();
					var meebdte = jQuery('#meebdte').val();
					var medateh = jQuery('#medateh').val();
					var meebplace = jQuery('#meebplace').val();
					var meectzns = jQuery('#meectzns').val();
					var meerelgn = jQuery('#meerelgn').val();
					var meecs = jQuery('#meecs').val();
					var meeaddr1 = jQuery('#meeaddr1').val();
					var meeaddr2 = jQuery('#meeaddr2').val();
					var meeaddr3 = jQuery('#meeaddr3').val();
					var meecpno = jQuery('#meecpno').val();
					var meetelno = jQuery('#meetelno').val();
					var meeemail = jQuery('#meeemail').val();
					var meecontinfo = jQuery('#meecontinfo').val();
					var mtkn_etr = '<?=$mtkn_etr;?>';
					var merecflag = (jQuery('#flexSwitchCheckrflag').is(':checked') ? 'A' : 'N');
					var maction = '<?=$maction;?>';
					var meecpname = jQuery('#meecpname').val();
					var meecpdesgn = jQuery('#meecpdesgn').val();
					var meecpcontno = jQuery('#meecpcontno').val();
					var meecpemail = jQuery('#meecpemail').val();
					var meecprela = jQuery('#meecprela').val();
					var meetinno = jQuery('#meetinno').val();
					var meesssno = jQuery('#meesssno').val();
					var meehdmfno = jQuery('#meehdmfno').val();
					var meephilhno = jQuery('#meephilhno').val();
					var mparam = {
						mtkn_etr: mtkn_etr,
						meenumb: meenumb,
						meelname: meelname,
						merecflag: merecflag,
						meefname: meefname,
						maction: maction,
						meemname: meemname,
						meeaddr1: meeaddr1,
						meeaddr2: meeaddr2,
						meeaddr3: meeaddr3,
						meegend: meegend,
						meebdte: meebdte,
						meebplace: meebplace,
						medateh: medateh,
						meectzns: meectzns,
						meerelgn: meerelgn,
						meecs: meecs,
						meecpno: meecpno,
						meetelno: meetelno,
						meeemail: meeemail,
						meecontinfo: meecontinfo,
						meecpname: meecpname,
						meecpdesgn: meecpdesgn,
						meecpcontno: meecpcontno,
						meecpemail: meecpemail,
						meecprela: meecprela,
						meetinno: meetinno,
						meesssno: meesssno,
						meehdmfno: meehdmfno,
						meephilhno: meephilhno
					};
					
					jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>mymd-qpr-employees-save',
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
			window.location.href = '<?=site_url();?>mymd-qpr-employees';
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
