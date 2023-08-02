
<?php

//VARIABLE DECLARATIONS

$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$myusermod = model('App\Models\MyUserModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$this->dbx = $mylibzdb->dbx;
$this->db_erp = $mydbname->medb(0);
$branch_code = '';
$mtkn_txt_branch = '';
$branch_name = '';
$start_date = ''; 
$mencd_date = date("Y-m-d"); 

echo view('templates/meheader01');
?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Promotion</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Sales</li>
        <li class="breadcrumb-item active">Promotion - POSTALLY UPLOAD</li>
      </ol>
    </nav>
  </div> <!-- End Page Title -->

        <!-- START HEADER DATA -->
                <div class="card-body">
                    <form id="upload-form" action="<?php echo base_url('upload/do_upload'); ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                        <label for="userfile" class="form-label">Choose File</label>
                        <input type="file" name="userfile" class="form-control" id="userfile"></div>
                        <div class="mb-3">
                        <input type="text" data-id-brnch-name="<?=$branch_name;?>" placeholder="Branch Name" id="branch_name" name="branch_name" class="branch_name form-control form-control-sm " value="<?=$branch_name;?>" required/>
                        <input type="hidden" data-id-brnch="<?=$branch_code;?>" placeholder="branch_code" id="branch_code" name="branch_code" class="branch_code form-control form-control-sm " value="<?=$branch_code;?>" required/></div>
                        <div class="col-sm-2">
                        <input type="date"  id="start_date" name="start_date" class="start_date form-control form-control-sm " value="<?=$start_date;?>" required/></div>
                        <div class="d-inline p-4">
                            <div class="col-md-5 mb-2">
                            <button type="button" id="upload-file-btn" class="btn btn-primary">UPLOAD FILE</button>
                            <button id="mbtn_mn_NTRX" type="button" class="btn btn-secondary">New Entry</button> </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-13">
        <div class="card-body">
            <table class="table table-bordered table-sm text-center" id="mytable"> <h1>Uploaded Files</h1>
                <br>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>File Name</th>
                        <th>Branch Name</th>
                        <th>Date</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($files as $file) { ?>
                    <tr>
                        <td><?php echo $file->id; ?></td>
                        <td><?php echo $file->filename; ?></td>
                        <td><?php echo $file->branch_name; ?></td>
                        <td><?php echo $file->start_date; ?></td>
                        <td><?php echo date('M j, Y h:i A', strtotime($file->created_at)); ?></td>
                            <td>
                            <button type="button" class="btn btn-primary view-file-btn" data-bs-toggle="modal" data-bs-target="#view-file-modal" data-id="<?= $file->id; ?>">View</button>
                            <button type="button" class="btn btn-danger delete-file-btn" data-id="<?= $file->id; ?>">Delete</button>
                            </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

<!-- View file modal -->
    <div class="modal fade" id="view-file-modal" tabindex="-1" aria-labelledby="view-file-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="view-file-modal-label">File Details</h5>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">X</button></div>
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <td>ID</td>
                            <td id="file-id"></td>
                        </tr>
                        <tr>
                            <td>File Name</td>
                            <td id="file-name"></td>
                        </tr>
                        <tr>
                            <td>Branch Name</td>
                            <td id="file-branch_name"></td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td id="file-start_date"></td>
                        </tr>
                        <tr>
                            <div class="externalFiles">
                                <td>Uploaded File</td>
                                <td><iframe src="" id="pdf_file_src" alt="view_file" width="600" height="480"></iframe></td></div>
                        </tr>
                    </table>
                </div>
            </div>
        </div> 
    </div>
</main>


<script>
    jQuery('.branch_name')
        // don't navigate away from the field on tab when selecting an item
        .bind( 'keydown', function( event ) {
            if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
                event.preventDefault();
                }
            if( event.keyCode === jQuery.ui.keyCode.TAB ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            minLength: 0,
            source: '<?= site_url(); ?>get-branch-list',
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function( event, ui ) {
                var terms = ui.item.value;
                
                jQuery('#branch_name').val(terms);
                jQuery('#branch_name').attr("data-id-brnch-name",ui.item.mtkn_rid);
                jQuery('#branch_code').val(ui.item.BRNCH_OCODE2);
                jQuery(this).autocomplete('search', jQuery.trim(terms));

                return false; 
              }
        })
        .click(function() {
                var terms = this.value;
                jQuery(this).autocomplete('search', jQuery.trim(terms)); 
        });
</script>

<script>
    jQuery(document).ready(function() {
        $('#upload-file-btn').click(function(event) {
        event.preventDefault();
        var form = $('#upload-form')[0];
        var url = "<?php echo site_url('upload-do_upload'); ?>";
        var formData = new FormData();

        formData.append('userfile', $('#userfile')[0].files[0]);
        formData.append('branch_name', $('#branch_name').val());
        formData.append('start_date', $('#start_date').val());
        
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('File uploaded successfully!');
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                alert('Error uploading file.');
            }
        });
        });
    });

    jQuery('#mbtn_mn_NTRX').click(function() { 
          var userselection = confirm("Are you sure you want to new transaction?");
          if (userselection == true){
            window.location = '<?=site_url();?>postally_upload';
          }
          else{
            $.hideLoading();
            return false;
          } 
        });


    jQuery(document).on('click', '.delete-file-btn', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this file?')) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("upload-delete_file"); ?>',
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    // Reload the table to remove the deleted row
                    location.reload();
                }
            }
        });
        }
    });


    jQuery(document).on('click', '.view-file-btn', function() {
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("upload-view_file"); ?>',
            data: { id: id },
            success: function(response) {
                $('#file-id').text(response.id);
                $('#file-name').text(response.filename);
                $('#file-branch_name').text(response.branch_name);
                $('#file-start_date').text(response.start_date);
                $('#pdf_file_src').attr('src','<?php echo base_url("./uploads/meuploadhehe/");?>'+'/'+response.filename);
                $('#view-file-modal').modal('show');
            }
        });
    });
</script>

<script type="text/javascript">
    
    $(document).ready(function(){
var table = $('#myTable').DataTable();
   //DataTable custom search field
    $('#custom-filter').keyup( function() {
      table.search( this.value ).draw();
    } );
});

</script>


<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.3.min.js"></script> 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript">
    $(document).ready(function(){
    $("#mytable").DataTable();                 
    });
</script>
