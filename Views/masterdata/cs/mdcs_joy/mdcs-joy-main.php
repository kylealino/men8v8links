<?php
$request = \Config\Services::request();
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$recid = $request->getVar('recid');
$deleterecid = $request->getVar('deleterecid');
$lname = '';
$fname= '';
if(!empty($recid)){
    $str="
        SELECT `fname`, `lname` FROM table_joy WHERE recid = '$recid'
    ";
    $qry = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    $row = $qry->getRowArray();
    $lname = $row['lname'];
    $fname = $row['fname'];

}

if(!empty($deleterecid)){
    $str="
        SELECT `fname`, `lname` FROM table_joy WHERE recid = '$deleterecid'
    ";
    $qry = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    $row = $qry->getRowArray();
    $lname = $row['lname'];
    $fname = $row['fname'];

}

?>
<main id="main">

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header mb-3">
                    <h3 class="h4 mb-0"> <i class="bi bi-pencil-square"></i> Entry</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="row mb-3">
                                <label class="col-sm-3 form-label" for="fname">Enter your first name:</label>
                                <div class="col-sm-9">
                                    <input type="text" id="fname" name="fname" class="form-control form-control-sm bg-white" value="<?=$fname;?>" autocomplete="off"/>
                                    <input type="hidden" name="recid" id="recid" value="<?=$recid;?>">
                                    <input type="hidden" name="deleterecid" id="deleterecid" value="<?=$deleterecid;?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 form-label" for="lname">Enter your last name:</label>
                                <div class="col-sm-9">
                                    <input type="text" id="lname" name="lname" class="form-control form-control-sm bg-white" value="<?=$lname;?>" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <?php if(!empty($recid)):?>
                                    <div class="col-4">
                                    <button id="mbtn_mn_Update" type="submit" class="btn btn-success btn-sm">Update</button>
                                    <?=anchor('test-joy', '<i class="bi bi-arrow-repeat"></i>',' class="btn btn-outline-success btn-sm" ');?>
                                    </div>
                                <?php endif;?>

                                <?php if(!empty($deleterecid)):?>
                                    <div class="col-4">
                                        <button id="mbtn_mn_Delete" type="submit" class="btn btn-success btn-sm">Delete</button>
                                        <?=anchor('test-joy', '<i class="bi bi-arrow-repeat"></i>',' class="btn btn-outline-success btn-sm" ');?>
                                    </div>
                                <?php endif;?>

                                <?php if(empty($recid) && empty($deleterecid)):?>
                                    <div class="col-4">
                                        <button id="mbtn_mn_Save" type="submit" class="btn btn-success btn-sm">Save</button>
                                        <?=anchor('test-joy', '<i class="bi bi-arrow-repeat"></i>',' class="btn btn-outline-success btn-sm" ');?>
                                    </div>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header mb-3">
                    <h3 class="h4 mb-0"> <i class="bi bi-pencil-square"></i> Sub BOM Records</h3>
                </div>
                <div class="card-body">
                    <div id="joyrecs" class="text-center p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">
                        <?php
                           
                        ?> 
                    </div> 
                </div>
                </div> 
            </div>
        </div>
    </div> 
</main>
<?php
    echo $mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
    echo $mylibzsys->memypreloader01('mepreloaderme');
    echo $mylibzsys->memsgbox1('memsgtestent','System Alert','...');
    ?>  
<script>
$(document).ready(function(){
    test_joy_view_recs();
  });

    __mysys_apps.mepreloader('mepreloaderme',false);

    function test_joy_view_recs(){ 
    var ajaxRequest;

    ajaxRequest = jQuery.ajax({
        url: "<?=site_url();?>test-joy-recs",
        type: "post"
    });

    __mysys_apps.mepreloader('mepreloaderme',true);
      ajaxRequest.done(function(response, textStatus, jqXHR) {
          jQuery('#joyrecs').html(response);
          __mysys_apps.mepreloader('mepreloaderme',false);
      });
  };

  $("#mbtn_mn_Delete").click(function(e){
       
       try { 

           var deleterecid = jQuery('#deleterecid').val();

           var mparam = {
            deleterecid:deleterecid

           };  

           console.log(mparam);
           
           $.ajax({ 
             type: "POST",
             url: '<?=site_url();?>test-joy-delete',
             context: document.body,
             data: eval(mparam),
             global: false,
             cache: false,
             success: function(data)  { 
               $(this).prop('disabled', false);
          // $.hideLoading();
          jQuery('#memsgtestent_bod').html(data);
          jQuery('#memsgtestent').modal('show');
          return false;
        },
        error: function() {
         alert('error loading page...');
        // $.hideLoading();
        return false;
      } 
    });

         } catch(err) {
           var mtxt = 'There was an error on this page.\n';
           mtxt += 'Error description: ' + err.message;
           mtxt += '\nClick OK to continue.';
           alert(mtxt);
   }  //end try
   return false; 
 });
__mysys_apps.mepreloader('mepreloaderme',false);
$("#mbtn_mn_Save").click(function(e){
       
       try { 

         var fname = jQuery('#fname').val();
         var lname = jQuery('#lname').val();


           var mparam = {
                fname:fname,
                lname: lname
           };  

           console.log(mparam);
           
           $.ajax({ 
             type: "POST",
             url: '<?=site_url();?>test-joy-save',
             context: document.body,
             data: eval(mparam),
             global: false,
             cache: false,
             success: function(data)  { 
               $(this).prop('disabled', false);
          // $.hideLoading();
          jQuery('#memsgtestent_bod').html(data);
          jQuery('#memsgtestent').modal('show');
          return false;
        },
        error: function() {
         alert('error loading page...');
        // $.hideLoading();
        return false;
      } 
    });

         } catch(err) {
           var mtxt = 'There was an error on this page.\n';
           mtxt += 'Error description: ' + err.message;
           mtxt += '\nClick OK to continue.';
           alert(mtxt);
   }  //end try
   return false; 
 });


 $("#mbtn_mn_Update").click(function(e){
       
       try { 

         var fname = jQuery('#fname').val();
         var lname = jQuery('#lname').val();
         var recid = jQuery('#recid').val();


           var mparam = {
                fname:fname,
                lname: lname,
                recid: recid
           };  

           console.log(mparam);
           
           $.ajax({ 
             type: "POST",
             url: '<?=site_url();?>test-joy-update',
             context: document.body,
             data: eval(mparam),
             global: false,
             cache: false,
             success: function(data)  { 
               $(this).prop('disabled', false);
          // $.hideLoading();
          jQuery('#memsgtestent_bod').html(data);
          jQuery('#memsgtestent').modal('show');
          return false;
        },
        error: function() {
         alert('error loading page...');
        // $.hideLoading();
        return false;
      } 
    });

         } catch(err) {
           var mtxt = 'There was an error on this page.\n';
           mtxt += 'Error description: ' + err.message;
           mtxt += '\nClick OK to continue.';
           alert(mtxt);
   }  //end try
   return false; 
 });
</script>