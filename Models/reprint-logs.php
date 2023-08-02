<?php

//VARIABLE DECLARATIONS

$request = \Config\Services::request();
$mylibzdb2 = model('App\Models\MYPOSConnModel');
$branch_name = '';
$name = '';
$start_date = ''; 
$end_date = ''; 


echo view('templates/meheader01');
?>
<main id="main" class="main">
<div class="card">
  <div class="card-body">
  <div class="pagetitle">
    <br>
    <h1>Promotion</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=site_url();?>">Home</a></li>
        <li class="breadcrumb-item">Sales</li>
        <li class="breadcrumb-item active">Promotion - REPRINT</li>
      </ol>
    </nav>
  </div> <!-- End Page Title -->
          <!-- START HEADER DATA -->

          <div class="row mb-3">
            <div class="col-lg-12">
              <div class="row mb-3">
                <label class="col-sm-2 form-label" for="branch_name">Branch:</label>
                <div class="col-sm-10">
                  <input type="text" data-id-brnch-name="<?=$branch_name;?>" placeholder="Branch Name" id="branch_name" name="branch_name" class="branch_name form-control form-control-sm " value="<?=$branch_name;?>" required/>
                  <input type="hidden" data-id-name="<?=$name;?>" placeholder="Branch" id="name" name="name" class="name form-control form-control-sm " value="<?=$name;?>" required/>  
                </div>
              </div>

              <div class="row gy-2 offset-lg-2">
                <div class="col-sm-6">
                  <input type="date"  id="start_date" name="start_date" class="start_date form-control form-control-sm " value="<?=$start_date;?>" required/>
                  <label for="start_date">Start Date</label>
                </div>
                <div class="col-sm-6">
                  <input type="date"  id="end_date" name="end_date" class="end_date form-control form-control-sm " value="<?=$end_date;?>" required/>
                  <label for="start_date">End Date</label>
                </div>       
              </div>  
            </div> 
          </div>  
        </div>
        <hr class="prettyline shadow">

          <!-- END HEADER DATA -->

        <div class="d-inline p-4">
          <div class="table-responsive">
            <table class="table table-bordered table-sm text-center " id="table">     
              <thead>
                <tr>
                  <th><div class="col-12" id="id" style="font-size: 20px;">No.</div> </th>
                  <th><div class="col-12" id="date" style="font-size: 20px;">Date_Reprinted</div> </th>
                  <th><div class="col-12" id="branch_name" style="font-size: 20px;">Branch_name</div> </th>
                  <th><div class="col-12" id="name" style="font-size: 20px;">POS_number</div> </th>
                  <th><div class="col-12" id="sales_id" style="font-size: 20px;">SI#</div> </th>
                  <th><div class="col-12" id="user_name" style="font-size: 20px;">ApprovedBy</div> </th>
                  <th><div class="col-12" id="last_name" style="font-size: 20px;">Cashier On Duty</div> </th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>













