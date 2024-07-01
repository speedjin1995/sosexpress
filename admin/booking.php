<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $stmt = $db->prepare("SELECT * from users where id = ?");
	$stmt->bind_param('s', $user);
	$stmt->execute();
	$result = $stmt->get_result();
  $role = 'NORMAL';
  
  if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
  }

  $customers = $db->query("SELECT * FROM customers WHERE deleted = '0'");
  $customers2 = $db->query("SELECT * FROM customers WHERE deleted = '0'");
  $customers3 = $db->query("SELECT * FROM customers WHERE deleted = '0'");
  $branch = $db->query("SELECT * FROM branch WHERE deleted = '0'");
  $branch2 = $db->query("SELECT * FROM branch WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $vehicles = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
  $vehicles2 = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
  $drivers = $db->query("SELECT * FROM drivers WHERE deleted = '0'");
  $outlet = $db->query("SELECT * FROM outlet WHERE deleted = '0'");
  $do_type = $db->query("SELECT * FROM do_type WHERE deleted = '0'");
  $zones = $db->query("SELECT * FROM zones WHERE deleted = '0'");
  $states = $db->query("SELECT * FROM states WHERE deleted = '0'");
  $hypermarket = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
}
?>

<style>
  @media screen and (min-width: 676px) {
    .modal-dialog {
      max-width: 1800px; /* New width for default modal */
    }
  }
</style>

<select class="form-control" style="width: 100%;" id="zoneHidden" style="display: none;">
  <option value="" selected disabled hidden>Please Select</option>
  <?php while($row3=mysqli_fetch_assoc($zones)){ ?>
    <option value="<?=$row3['id'] ?>" data-index="<?=$row3['states'] ?>"><?=$row3['zones'] ?></option>
  <?php } ?>
</select>

<select class="form-control" style="width: 100%;" id="branchHidden" style="display: none;">
  <option value="" selected disabled hidden>Please Select</option>
  <?php while($row2=mysqli_fetch_assoc($branch2)){ ?>
    <option value="<?=$row2['id'] ?>"><?=$row2['address'] ?></option>
  <?php } ?>
</select>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Booking</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="form-group col-3">
                <label>From Date:</label>
                <div class="input-group date" id="fromDatePicker" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#fromDatePicker" id="fromDate"/>
                  <div class="input-group-append" data-target="#fromDatePicker" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div></div>
                </div>
              </div>

              <div class="form-group col-3">
                <label>To Date:</label>
                <div class="input-group date" id="toDatePicker" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#toDatePicker" id="toDate"/>
                  <div class="input-group-append" data-target="#toDatePicker" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>Shipment Type</label>
                  <select class="form-control select2" id="pickupMethod" name="pickupMethod">
                    <!--option value="" selected disabled hidden>Please Select</option-->
                    <option value="" selected disabled hidden>Please Select</option>
                    <option value="SOS Pickup">SOS Pickup</option>
                    <option value="Outstation Pickup">Outstation Pickup</option>
                    <option value="Send By Own">Send By Own</option>
                  </select>
                </div>
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>Customer No</label>
                  <select class="form-control select2" id="customerNoFilter" name="customerNoFilter">
                    <option value="" selected disabled hidden>Please Select</option>
                    <?php while($rowCustomer2=mysqli_fetch_assoc($customers2)){ ?>
                      <option value="<?=$rowCustomer2['id'] ?>"><?=$rowCustomer2['customer_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-9"></div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm"  id="filterSearch">
                  <i class="fas fa-search"></i>
                  Search
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-4">Booking</div>
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="download">
                  <i class="fas fa-file-excel"></i>
                  Download Template
                </button>
              </div>
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="print">
                  <i class="fas fa-print"></i>
                  Print
                </button>
              </div>
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="updateStatus">
                  <i class="fas fa-pen"></i>
                  Update Status
                </button>
              </div>
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="newBooking">
                  <i class="fas fa-plus"></i>
                  New Booking
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                  <th>Customer</th>
                  <th>Booking Datetime</th>
                  <th>Description</th>
                  <th>Estimated Ctn</th>
                  <th>Actual Ctn</th>
                  <th>Pickup Method</th>
                  <th>Lorry No.</th>
                  <th></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="doModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">

      <form role="form" id="doForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Add New DO Request</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <input type="hidden" class="form-control" id="jsonDataField" name="jsonDataField">

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Booking Date *</label>
                  <div class='input-group date' id="bookingDate" data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" data-target="#bookingDate" id="booking_date" name="bookingDate" required/>
                    <div class="input-group-append" data-target="#bookingDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Delivery Date *</label>
                  <div class='input-group date' id="deliveryDate" data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" data-target="#deliveryDate" id="delivery_date" name="deliveryDate" required/>
                    <div class="input-group-append" data-target="#deliveryDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Cancellation Date *</label>
                  <div class='input-group date' id="cancellationDate" data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" data-target="#cancellationDate" id="cancellation_date" name="cancellationDate" required/>
                    <div class="input-group-append" data-target="#cancellationDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label class="labelStatus">Customer *</label>
                <select class="form-control" id="customerNo" name="customerNo" readonly>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowCustomer=mysqli_fetch_assoc($customers3)){ ?>
                    <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['customer_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label class="labelStatus">Hypermarket *</label>
                <select class="form-control" id="hypermarket" name="hypermarket" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowhypermarket=mysqli_fetch_assoc($hypermarket)){ ?>
                    <option value="<?=$rowhypermarket['id'] ?>"><?=$rowhypermarket['name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label class="labelStatus">States *</label>
                <select class="form-control" id="states" name="states" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowCustomer=mysqli_fetch_assoc($states)){ ?>
                    <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['states'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label for="rate">Zones *</label>
                <select class="form-control" style="width: 100%;" id="zones" name="zones" required></select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label for="rate">Outlet *</label>
                <select class="form-control" style="width: 100%;" id="outlets" name="outlets"></select>
                <select id="direct_store" name="direct_store"></select>
                <!--input class="form-control" type="text" placeholder="Outlet" id="direct_store" name="direct_store"-->
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label for="rate">DO Type *</label>
                <select class="form-control" id="do_type" name="do_type" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowdo_type=mysqli_fetch_assoc($do_type)){ ?>
                    <option value="<?=$rowdo_type['type'] ?>"><?=$rowdo_type['type'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>DO No.</label>
                <div class="input-group">
                  <input class="form-control" type="text" placeholder="DO No." id="do_no" name="do_no">
                  <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="button" id="openModalBtn">
                        <i class="fas fa-plus"></i>
                      </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>PO No.</label>
                <input class="form-control" type="text" placeholder="PO Number" id="po_no" name="po_no">
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Actual Carton *</label>
                <input class="form-control" type="number" placeholder="Actual Carton" id="actual_ctn" name="actual_ctn" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>On-Hold *</label>
                <select class="form-control" id="on_hold" name="on_hold" required>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
              </div>
            </div>
          </div>  
          <div class="col-8">
            <div class="form-group">
              <label class="labelStatus">Notes</label>
              <textarea class="form-control" id="description" name="description" placeholder="Enter your description"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="extendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">

      <form role="form" id="extendForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Add New Booking</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Booking Date *</label>
                <div class='input-group date' id="bookingDate" data-target-input="nearest">
                  <input type='text' class="form-control datetimepicker-input" data-target="#bookingDate" id="booking_date" name="bookingDate" required/>
                  <div class="input-group-append" data-target="#bookingDate" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label class="labelStatus">Pickup Method *</label>
                <select class="form-control select2" id="pickup_method" name="pickup_method" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="SOS Pickup">SOS Pickup</option>
                  <option value="Outstation Pickup">Outstation Pickup</option>
                  <option value="Send By Own">Send By Own</option>
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label class="labelStatus">Customer *</label>
                <select class="form-control select2" id="customerNo" name="customerNo" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowCustomer=mysqli_fetch_assoc($customers)){ ?>
                    <option value="<?=$rowCustomer['id'] ?>" data-address="<?=$rowCustomer['customer_address'] ?>"><?=$rowCustomer['customer_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>Pickup Address </label>
                <textarea class="form-control" id="address" name="address" placeholder="Enter your address"></textarea>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Enter your description"></textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Internal Notes</label>
                <textarea class="form-control" id="internal_notes" name="internal_notes" placeholder="Enter Internal Notes"></textarea>
              </div>
            </div>
            <div class="form-group col-2">
              <label>Extimated Ctn *</label>
              <input class="form-control" type="number" placeholder="Extimated Carton" id="extimated_ctn" name="extimated_ctn" min="0" required/>                        
            </div>
            <div class="form-group col-2">
              <label>Actual Ctn</label>
              <input class="form-control" type="number" placeholder="Actual Carton" id="actual_ctn" name="actual_ctn" min="0"/>                        
            </div>
            <div class="form-group col-4">
              <label>Gate</label>
              <input class="form-control" type="text" placeholder="Gate" id="gate" name="gate" />                        
            </div>
          </div>
          <div class="row">
            <div class="form-group col-4">
              <label>Checker</label>
              <select class="form-control" id="checker" name="checker">
                <option value="" selected disabled hidden>Please Select</option>
                <?php while($rowUser=mysqli_fetch_assoc($users)){ ?>
                  <option value="<?=$rowUser['id'] ?>"><?=$rowUser['name'] ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Vehicle No</label> 
                <select class="form-control" id="vehicleNoTxt" name="vehicleNoTxt">
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowvehicles=mysqli_fetch_assoc($vehicles)){ ?>
                    <option value="<?=$rowvehicles['veh_number'] ?>"><?=$rowvehicles['veh_number'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Pickup Form Number</label>
                <input class="form-control" type="text" placeholder="Pickup Form Number" id="form_no" name="form_no">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-2">
              <div class="form-group">
                <label class="labelStatus">Col Goods</label>
                <select class="form-control" id="col_goods" name="col_goods">
                  <option value="Yes">Yes</option>
                  <option value="No" selected>No</option>
                </select>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <label class="labelStatus">Col Chq</label>
                <select class="form-control" id="col_chk" name="col_chk">
                  <option value="Yes">Yes</option>
                  <option value="No"selected>No</option>
                </select>
              </div>
            </div>
          </div>  
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="updateModal">
  <div class="modal-dialog modal-xl" style="max-width: 50%;">
    <div class="modal-content">

      <form role="form" id="updateForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Update Status</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>Status *</label>
                <select class="form-control" id="status" name="status">
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="Picked">Picked</option>
                  <option value="Invoiced">Invoiced</option>
                </select>
              </div>
            </div>
          </div>  
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="printModal">
  <div class="modal-dialog modal-xl" style="max-width: 50%;">
    <div class="modal-content">

      <form role="form" id="printForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Assigned Driver</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>Driver Name *</label>
                <select class="form-control" id="driver" name="driver" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowdrivers2=mysqli_fetch_assoc($drivers)){ ?>
                    <option value="<?=$rowdrivers2['id'] ?>"><?=$rowdrivers2['name'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Lorry No *</label>
                <select class="form-control" id="lorry" name="lorry" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowvehicles2=mysqli_fetch_assoc($vehicles2)){ ?>
                    <option value="<?=$rowvehicles2['veh_number'] ?>"><?=$rowvehicles2['veh_number'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>  
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-xl" style="max-width: 95%;">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalLabel">DO Listing</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" id="modalContent"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" id="printButton">Print</button>
            <button type="button" class="btn btn-primary" id="confirmButton">Confirm</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="uploadModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="uploadForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Upload Excel File</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="file" id="fileInput">
          <button id="previewButton">Preview Data</button>
          <div id="previewTable"></div>
        </div>
        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="poModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="doModalLabel">Enter DO and PO Numbers</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
            <table class="table" id="doPoTable">
                <thead>
                    <tr>
                        <th scope="col">DO Number</th>
                        <th scope="col">PO Number</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be dynamically added here -->
                </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" id="addRowBtn">Add Row</button>
            <button type="button" class="btn btn-primary" id="saveRowsBtn">Save</button>
          </div>
      </div>
  </div>
</div>

<script>
$(function () {
  $("#zoneHidden").hide();
  $("#branchHidden").hide();
  const today = new Date();
  const tomorrow = new Date(today);
  const yesterday = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  yesterday.setDate(yesterday.getDate() - 1);

  $('.select2').select2({
    allowClear: true,
    placeholder: "Please Select"
  });
  
  $('#direct_store').select2({
    ajax: {
      url: 'php/getDirectStore.php',
      dataType: 'json',
      data: function (params) {
        var query = {
          search: params.term,
          states: $('#states').val() ? $('#states').val() : '',
          zones: $('#zones').val() ? $('#zones').val() : '',
          hyper: $('#hypermarket').val() ? $('#hypermarket').val() : '',
          type: 'public'
        };
        return query;
      },
      delay: 250,
      processResults: function (data) {
        var resultsArray = [];

        // Assuming data.message is an object
        for (var key in data.message) {
          if (data.message.hasOwnProperty(key)) {
            resultsArray.push({
              id: data.message[key].name, // Use the property key as the id
              text: data.message[key].name // Use the name property as the text
            });
          }
        }

        return {
          results: resultsArray
        };
      },
      cache: true,
    },
    minimumInputLength: 1,
    placeholder: 'Search for options...',
    tags: true,
    createTag: function (params) {
      if ($.trim(params.term) === '') {
        return null;
      }
      return {
        id: params.term,
        text: params.term
      };
    },
  });

  $('#direct_store').data('select2').$container.hide();

  //Date picker
  $('#fromDatePicker').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY',
    defaultDate: new Date
  });

  $('#toDatePicker').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY',
    defaultDate: new Date
  });

  $('#bookingDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY',
    minDate: yesterday
  });

  var fromDateI = $('#fromDate').val();
  var toDateI = $('#toDate').val();
  var pickupMethodI = $('#pickupMethod').val() ? $('#pickupMethod').val() : '';
  var customerNoI = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';

  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'order': [[ 1, 'asc' ]],
    'columnDefs': [ { orderable: false, targets: [0] }],
    /*'ajax': {
      'url':'php/loadBooking.php'
    },*/
    'ajax': {
      'type': 'POST',
      'url':'php/filterBooking.php',
      'data': {
        fromDate: fromDateI,
        toDate: toDateI,
        method: pickupMethodI,
        customer: customerNoI,
      } 
    },
    'columns': [
      {
        // Add a checkbox with a unique ID for each row
        data: 'id', // Assuming 'serialNo' is a unique identifier for each row
        className: 'select-checkbox',
        orderable: false,
        render: function (data, type, row) {
          return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
        }
      },
      { data: 'customer_name' },
      { data: 'booking_date' },
      { data: 'description' },
      { data: 'estimated_ctn' },
      {
        data: 'actual_ctn',
        render: function (data, type, row) {
          // Check if data is null
          if (data === null) {
            return ''; // Return empty string
          }

          // Generate HTML with a link
          return '<a href="#" class="actualCtnLink" data-id="' + row.id + '" data-booking-date="' + row.booking_date + '" data-customer-id="' + row.customer_id + '">' + data + '</a>';
        }
      },
      { data: 'pickup_method' },
      { data: 'vehicle_no' },
      { 
        className: 'dt-control',
        orderable: false,
        data: null,
        render: function ( data, type, row ) {
          return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
        }
      }
    ],
    "rowCallback": function( row, data, index ) {
      //$('td', row).css('background-color', '#E6E6FA');
    },        
  });

  $('#selectAllCheckbox').on('change', function() {
    var checkboxes = $('#weightTable tbody input[type="checkbox"]');
    checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
  });

  // Add event listener for opening and closing details
  $('#weightTable tbody').on('click', 'td.dt-control', function () {
    var tr = $(this).closest('tr');
    var row = table.row( tr );

    if ( row.child.isShown() ) {
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      row.child( format(row.data()) ).show();tr.addClass("shown");
    }
  });

  $('#weightTable tbody').on('click', 'a.actualCtnLink', function (e) {
    var rowId = $(this).data('id');
    var bookingDate = $(this).data('booking-date');
    var customerId = $(this).data('customer-id');
    loadModalContent(rowId, bookingDate, customerId);

    /*$.ajax({
        url: 'php/getActualCtnData.php',
        type: 'POST',
        data: { id: rowId, bookingDate: bookingDate, customerId: customerId },
        success: function (data) {
            var response = JSON.parse(data);

            if (response.status == 'success') {
                var tableContent = '<h2>'+response.message[0].customer_name+'</h2><table class="table" style="width:100%;">';
                tableContent += '<thead><tr><th></th><th>Outlet</th><th>Booking Date</th><th>Number of Carton</th><th>DO No.</th><th>PO No.</th><th>Notes</th><th></th></tr></thead><tbody>';

                // Loop through each item in the 'message' array
                for (var i = 0; i < response.message.length; i++) {
                    var rowData = response.message[i];

                    tableContent += '<tr>';

                    // Conditionally add checkbox only when status is 'Posted'
                    if (rowData.status === 'Posted') {
                        var checkboxHtml = '<td><input type="checkbox" class="postedCheckbox" id="checkbox_' + rowData.id + '"></td>';
                        tableContent += checkboxHtml;
                    } else {
                        // If status is not 'Posted', leave the cell empty
                        tableContent += '<td></td>';
                    }

                    tableContent += '<td>' + rowData.outlet + '</td>';
                    tableContent += '<td>' + rowData.booking_date + '</td>';
                    tableContent += '<td>' + rowData.actual_carton + '</td>';
                    tableContent += '<td>' + rowData.do_number + '</td>';
                    tableContent += '<td>' + rowData.po_number + '</td>';
                    tableContent += '<td>' + rowData.note + '</td>';
                    tableContent += '<td>';
                    tableContent += '<button class="btn btn-danger btn-sm deleteButton" data-id="' + rowData.id + '"><i class="fas fa-times"></i></button> ';
                    tableContent += '<button class="btn btn-primary btn-sm editButton" data-id="' + rowData.id + '"><i class="fas fa-pencil-alt"></i></button>';
                    tableContent += '</td>';
                    tableContent += '</tr>';
                }

                tableContent += '</tbody></table>';

                // You can replace the following line with your logic to display the data
                $('#modalContent').html(tableContent);

                // Attach click event to Confirm buttons (now the Confirm button is inside the loop)
                $('#confirmButton').on('click', function () {
                  confirmSelectedRows();
                });

                $('#printButton').on('click', function () {
                  printTable();
                });

                $('#myModal').modal('show');
            } else {
                console.error('Failed to retrieve valid data.');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error retrieving actual_ctn data:', error);
        }
    });*/
  });

  $('#myModal').on('click', '.deleteButton', function() {
    var id = $(this).data('id');
    var rowId = $(this).closest('tr').find('.details').data('id');
    var bookingDate = $(this).closest('tr').find('.details').data('book');
    var customerId = $(this).closest('tr').find('.details').data('cust');
    deleteRow(id, rowId, bookingDate, customerId);
  });

  $('#myModal').on('click', '.editButton', function() {
    var id = $(this).data('id');
    editRow(id);
  });
  
  $('#myModal').on('click', '.revertButton', function() {
    var id = $(this).data('id');
    var rowId = $(this).closest('tr').find('.details').data('id');
    var bookingDate = $(this).closest('tr').find('.details').data('book');
    var customerId = $(this).closest('tr').find('.details').data('cust');
    refreshRow(id, rowId, bookingDate, customerId);
  });

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#extendModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/booking.php', $('#extendForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#extendModal').modal('hide');
            toastr["success"](obj.message, "Success:");
            $('#weightTable').DataTable().ajax.reload();
          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when edit", "Failed:");
          }

          $('#spinnerLoading').hide();
        });
      }
      else if($('#updateModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/updateBooking.php', $('#updateForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#updateModal').modal('hide');
            toastr["success"](obj.message, "Success:");
            $('#weightTable').DataTable().ajax.reload();
          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when edit", "Failed:");
          }

          $('#spinnerLoading').hide();
        });
      }
      else if($('#printModal').hasClass('show')){
        $.post('php/print_picking.php', $('#printForm').serialize(), function(data){
          var obj = JSON.parse(data);
      
          if(obj.status === 'success'){
            $('#printModal').modal('hide');
            $('#weightTable').DataTable().ajax.reload();
            var printWindow = window.open('', '', 'height=400,width=800');
            printWindow.document.write(obj.message);
            printWindow.document.close();
            setTimeout(function(){
              printWindow.print();
              printWindow.close();
            }, 1000);
          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when pull data", "Failed:");
          }
        });
      }
      else if($('#doModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/doRequest.php', $('#doForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#doModal').modal('hide');
            toastr["success"](obj.message, "Success:");
            $('#weightTable').DataTable().ajax.reload();
          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when edit", "Failed:");
          }

          $('#spinnerLoading').hide();
        });
      }
      else if($('#uploadModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/uploadDO.php', $('#uploadForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#uploadModal').modal('hide');
            toastr["success"](obj.message, "Success:");
            $('#weightTable').DataTable().ajax.reload();
          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when edit", "Failed:");
          }

          $('#spinnerLoading').hide();
        });
      }
    }
  });

  $("#updateStatus").on("click", function () {
    var selectedIds = []; // An array to store the selected 'id' values

    $("#weightTable tbody input[type='checkbox']").each(function () {
      if (this.checked) {
        selectedIds.push($(this).val());
      }
    });

    if (selectedIds.length > 0) {
      $("#updateModal").find('#id').val(selectedIds);
      $("#updateModal").find('#status').val('');
      $("#updateModal").modal("show");

      $('#updateForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    } else {
      // Optionally, you can display a message or take another action if no IDs are selected
      alert("Please select at least one DO to update.");
    }
  });

  $('#newBooking').on('click', function(){
    var date = new Date();

    $('#extendModal').find('#id').val("");
    $('#extendModal').find('#booking_date').val(formatDate2(today));
    $('#extendModal').find('#pickup_method').select2('destroy').val('').select2();
    $('#extendModal').find('#customerNo').select2('destroy').val('').select2();
    $('#extendModal').find('#branch').val("");
    $('#extendModal').find('#address').val("");
    $('#extendModal').find('#description').val("");
    $('#extendModal').find('#internal_notes').val("");
    $('#extendModal').find('#extimated_ctn').val("");
    $('#extendModal').find('#actual_ctn').val("");
    $('#extendModal').find('#gate').val("");
    $('#extendModal').find('#checker').val("");
    $('#extendModal').find('#vehicleNoTxt').val("");
    $('#extendModal').find('#form_no').val("");
    $('#extendModal').find('#col_goods').val("No");
    $('#extendModal').find('#col_chk').val("No");
    $('#extendModal').modal('show');
    
    $('#extendForm').validate({
      errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      }
    });
  });

  $('#filterSearch').on('click', function(){
    //$('#spinnerLoading').show();

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var pickupMethod = $('#pickupMethod').val() ? $('#pickupMethod').val() : '';
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';

    //Destroy the old Datatable
    $("#weightTable").DataTable().clear().destroy();

    //Create new Datatable
    table = $("#weightTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'searching': false,
      'order': [[ 1, 'asc' ]],
      'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterBooking.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          method: pickupMethod,
          customer: customerNoFilter,
        } 
      },
      'columns': [
        {
          // Add a checkbox with a unique ID for each row
          data: 'id', // Assuming 'serialNo' is a unique identifier for each row
          className: 'select-checkbox',
          orderable: false,
          render: function (data, type, row) {
            return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
          }
        },
        { data: 'customer_name' },
        { data: 'booking_date' },
        { data: 'description' },
        { data: 'estimated_ctn' },
        {
          data: 'actual_ctn',
          render: function (data, type, row) {
            // Check if data is null
            if (data === null) {
              return ''; // Return empty string
            }

            // Generate HTML with a link
            return '<a href="#" class="actualCtnLink" data-id="' + row.id + '" data-booking-date="' + row.booking_date + '" data-customer-id="' + row.customer_id + '">' + data + '</a>';
          }
        },
        { data: 'pickup_method' },
        { data: 'vehicle_no' },
        { 
          className: 'dt-control',
          orderable: false,
          data: null,
          render: function ( data, type, row ) {
            return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
          }
        }
      ],
    });
  });

  $('#download').on('click', function () {
    window.open("php/exportTemplate.php");
  });

  $('#print').on('click', function () {
    var selectedIds = []; // An array to store the selected 'id' values
    var vehicle = [];

    $("#weightTable tbody input[type='checkbox']").each(function () {
      if (this.checked) {
        selectedIds.push($(this).val());
        vehicle.push($(this).closest('tr').find('td:nth-child(8)').text());
      }
    });

    if (selectedIds.length > 0) {
      $("#printModal").find('#id').val(selectedIds);
      $("#printModal").find('#driver').val('');
      $("#printModal").find('#lorry').val((vehicle.length > 0 ? vehicle[0] : ''));
      $("#printModal").modal("show");

      $('#printForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    } else {
      // Optionally, you can display a message or take another action if no IDs are selected
      alert("Please select at least one Booking to Pickup.");
    }
  });

  $('#uploadModal').find('#previewButton').on('click', function(){
    var fileInput = document.getElementById('fileInput');
    var file = fileInput.files[0];
    var reader = new FileReader();
    
    reader.onload = function(e) {
      var data = e.target.result;
      // Process data and display preview
      displayPreview(data);
    };

    reader.readAsBinaryString(file);
  });

  $('#doModal').find('#states').on('change', function(){
    $('#doModal').find('#zones').empty();
    var dataIndexToMatch = $(this).val();

    $('#zoneHidden option').each(function() {
      var dataIndex = $(this).data('index');

      if (dataIndex == dataIndexToMatch) {
        $('#doModal').find('#zones').append($(this).clone());
        $('#doModal').find('#zones').trigger('change');
      }
    });

    if($('#doModal').find('#states').val() && $('#doModal').find('#zones').val() && $('#doModal').find('#hypermarket').val() && $('#doModal').find('#hypermarket').val() != '0'){
      $('#doModal').find('#outlets').empty();
      $('#doModal').find("#direct_store").attr('required', false);
      $('#doModal').find('#outlets').attr('required', true);
      $('#doModal').find('#outlets').show();
      $('#doModal').find('#direct_store').data('select2').$container.hide();

      $.post('php/listOutlets.php', {states: $('#doModal').find('#states').val(), zones: $('#doModal').find('#zones').val(), hypermarket: $('#doModal').find('#hypermarket').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#doModal').find('#outlets').html('');
          for(var i=0; i<obj.message.length; i++){
            $('#doModal').find('#outlets').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
          }
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
        }
        else{
          toastr["error"]("Something wrong when pull data", "Failed:");
        }
        $('#spinnerLoading').hide();
      });
    }
    else{
      $('#doModal').find('#outlets').attr('required', false);
      $('#doModal').find('#outlets').hide();
      $('#doModal').find('#direct_store').data('select2').$container.show();
      $('#doModal').find("#direct_store").attr('required', true);
      $('#doModal').find("#direct_store").val('');
      //$('#doModal').find('.select2-container').show();
    }
  });

  $('#doModal').find('#zones').on('change', function(){
    if($('#doModal').find('#states').val() && $('#doModal').find('#zones').val() && $('#doModal').find('#hypermarket').val() && $('#doModal').find('#hypermarket').val() != '0'){
      $('#doModal').find('#outlets').empty();
      $('#doModal').find("#direct_store").attr('required', false);
      $('#doModal').find('#outlets').attr('required', true);
      $('#doModal').find('#outlets').show();
      $('#doModal').find('#direct_store').data('select2').$container.hide();
      //$('#doModal').find('.select2-container').hide();

      $.post('php/listOutlets.php', {states: $('#doModal').find('#states').val(), zones: $('#doModal').find('#zones').val(), hypermarket: $('#doModal').find('#hypermarket').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#doModal').find('#outlets').html('');
          for(var i=0; i<obj.message.length; i++){
            $('#doModal').find('#outlets').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
          }
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
        }
        else{
          toastr["error"]("Something wrong when pull data", "Failed:");
        }
        $('#spinnerLoading').hide();
      });
    }
    else{
      $('#doModal').find('#outlets').attr('required', false);
      $('#doModal').find('#outlets').hide();
      $('#doModal').find('#direct_store').data('select2').$container.show();
      $('#doModal').find("#direct_store").attr('required', true);
      $('#doModal').find("#direct_store").val('');
      //$('#doModal').find('.select2-container').show();
    }
  });

  $('#doModal').find('#hypermarket').on('change', function(){
    if($('#doModal').find('#states').val() && $('#doModal').find('#zones').val() && $('#doModal').find('#hypermarket').val() && $('#doModal').find('#hypermarket').val() != '0'){
      $('#doModal').find('#outlets').empty();
      $('#doModal').find("#direct_store").attr('required', false);
      $('#doModal').find('#outlets').attr('required', true);
      $('#doModal').find('#outlets').show();
      $('#doModal').find('#direct_store').data('select2').$container.hide();
      //$('#doModal').find('.select2-container').hide();

      $.post('php/listOutlets.php', {states: $('#doModal').find('#states').val(), zones: $('#doModal').find('#zones').val(), hypermarket: $('#doModal').find('#hypermarket').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#doModal').find('#outlets').html('');
          for(var i=0; i<obj.message.length; i++){
            $('#doModal').find('#outlets').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
          }
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
        }
        else{
          toastr["error"]("Something wrong when pull data", "Failed:");
        }
        $('#spinnerLoading').hide();
      });
    }
    else{
      $('#doModal').find('#outlets').attr('required', false);
      $('#doModal').find('#outlets').hide();
      $('#doModal').find('#direct_store').data('select2').$container.show();
      $('#doModal').find("#direct_store").attr('required', true);
      //$('#doModal').find('.select2-container').show();
      $('#doModal').find("#direct_store").val('');
    }
  });

  $('#openModalBtn').on('click', function () {
    if($('#doPoTable tbody tr').length > 0){
      $('#doModal').modal('show');
    }
    else{
      $('#doPoTable tbody').empty();
      addRow2($('#do_no').val(), $('#po_no').val()); // Pass default values to the addRow function
      $('#poModal').modal('show');
    }
  });

  $('#doPoTable').on('click', '.removeRowBtn', function () {
    $(this).closest('tr').remove();
  });

  $('#saveRowsBtn').on('click', function () {
    var rowsData = [];

    // Iterate through each row in the table
    $('#doPoTable tbody tr').each(function () {
      var doNumber = $(this).find('td:nth-child(1) input').val();
      var poNumber = $(this).find('td:nth-child(2) input').val();

      // Add row data to the array
      rowsData.push({ doNumber: doNumber, poNumber: poNumber });
    });

    // Convert the array to JSON
    var jsonData = JSON.stringify(rowsData);
    $('#jsonDataField').val(jsonData);
    $('#poModal').modal('hide');
  });

  $('#doModal').on('hidden.bs.modal', function() {
    location.reload();
  });
});

function format (row) {
  var returnString = '<div class="row"><div class="col-md-3"><p>Pickup Methode: '+row.pickup_method+
  '</p></div><div class="col-md-3"><p>Customer Name: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Pickup Location: '+row.pickup_location+
  '</p></div><div class="col-md-3"><p>Description: '+row.description+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Estimated Ctn: '+row.estimated_ctn+
  '</p></div><div class="col-md-3"><p>Actual Ctn: '+row.actual_ctn+
  '</p></div><div class="col-md-3"><p>Vehicle No: '+row.vehicle_no+
  '</p></div><div class="col-md-3"><p>Col Goods: '+row.col_goods+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Col Chq: '+row.col_chq+
  '</p></div><div class="col-md-3"><p>Form No: '+row.form_no+
  '</p></div><div class="col-md-3"><p>Gate: '+row.gate+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Checker: '+row.name+
  '</p></div><div class="col-md-3"><p>Status: '+row.status+
  '</p></div><div class="col-md-3">';
  
  if(row.status == 'Created'){
    returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" title="Picked" onclick="picked('+row.id+
  ')"><i class="fas fa-truck"></i></button></div><div class="col-3"><button type="button" class="btn btn-success btn-sm" title="Import Excel" onclick="importExcel('+row.id+
  ')"><i class="fas fa-file-excel"></i></button></div></div></div></div>';
  }
  else if(row.status == 'Picked'){
    returnString +='<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" title="Invoice" onclick="invoice('+row.id+
  ')"><i class="fas fa-receipt"></i></button></div><div class="col-3"><button type="button" class="btn btn-success btn-sm" title="Import Excel" onclick="importExcel('+row.id+
  ')"><i class="fas fa-file-excel"></i></button></div></div></div></div>';
  }
  
  
  return returnString;
}

function formatNormal (row) {
  return '<div class="row"><div class="col-md-3"><p>Customer Name: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Unit Weight: '+row.unit+
  '</p></div><div class="col-md-3"><p>Weight Status: '+row.status+
  '</p></div><div class="col-md-3"><p>MOQ: '+row.moq+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Address: '+row.customer_address+
  '</p></div><div class="col-md-3"><p>Batch No: '+row.batchNo+
  '</p></div><div class="col-md-3"><p>Weight By: '+row.userName+
  '</p></div><div class="col-md-3"><p>Package: '+row.packages+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Lot No: '+row.lots_no+
  '</p></div><div class="col-md-3"><p>Invoice No: '+row.invoiceNo+
  '</p></div><div class="col-md-3"><p>Unit Price: '+row.unitPrice+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Order Weight: '+row.supplyWeight+
  '</p></div><div class="col-md-3"><p>Delivery No: '+row.deliveryNo+
  '</p></div><div class="col-md-3"><p>Total Weight: '+row.totalPrice+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Contact No: '+row.customer_phone+
  '</p></div><div class="col-md-3"><p>Variance Weight: '+row.varianceWeight+
  '</p></div><div class="col-md-3"><p>Purchase No: '+row.purchaseNo+
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print('+row.id+
  ')"><i class="fas fa-print"></i></button></div><div class="col-3"><button type="button" class="btn btn-success btn-sm" onclick="portrait('+row.id+
  ')"><i class="fas fa-receipt"></i></button></div></div></div></div>'+
  '</div><div class="row"><div class="col-md-3"><p>Remark: '+row.remark+
  '</p></div><div class="col-md-3"><p>% Variance: '+row.variancePerc+
  '</p></div><div class="col-md-3"><p>Transporter: '+row.transporter_name+
  '</p></div></div>';
  ;
}

function loadModalContent(rowId, bookingDate, customerId) {
  $.ajax({
    url: 'php/getActualCtnData.php',
    type: 'POST',
    data: { id: rowId, bookingDate: bookingDate, customerId: customerId },
    success: function (data) {
      var response = JSON.parse(data);

      if (response.status == 'success') {
        var tableContent = '<h2>' + response.message[0].customer_name + ' ('+bookingDate+')</h2><table class="table" style="width:100%;">';
        tableContent += '<thead><tr><th></th><th>DO No.</th><th>PO No.</th><th>State</th><th>Hypermarket</th><th>Outlet</th><th>Quantity</th><th></th></tr></thead><tbody>';

        // Loop through each item in the 'message' array
        for (var i = 0; i < response.message.length; i++) {
            var rowData = response.message[i];

            tableContent += '<tr>';

            // Conditionally add checkbox only when status is 'Posted'
            if (rowData.status === 'Posted') {
                var checkboxHtml = '<td class="details" data-id="'+rowId+'" data-cust="'+customerId+'" data-book="'+bookingDate+'"><input type="checkbox" class="postedCheckbox" id="checkbox_' + rowData.id + '"></td>';
                tableContent += checkboxHtml;
            } else {
                // If status is not 'Posted', leave the cell empty
                tableContent += '<td class="details" data-id="'+rowId+'" data-cust="'+customerId+'" data-book="'+bookingDate+'"></td>';
            }

            tableContent += '<td>' + rowData.do_number + '</td>';
            tableContent += '<td>' + rowData.po_number + '</td>';
            tableContent += '<td>' + rowData.states + '</td>';
            tableContent += '<td>' + rowData.hypermarket + '</td>';
            tableContent += '<td>' + rowData.outlet + '</td>';
            tableContent += '<td>' + rowData.actual_carton + '</td>';
            
            tableContent += '<td>';
            tableContent += '<button class="btn btn-danger btn-sm deleteButton" data-id="' + rowData.id + '"><i class="fas fa-times"></i></button> ';
            tableContent += '<button class="btn btn-primary btn-sm editButton" data-id="' + rowData.id + '"><i class="fas fa-pencil-alt"></i></button>';
            
            if (rowData.status === 'Confirmed') {
                tableContent += '<button class="btn btn-warning btn-sm revertButton" data-id="' + rowData.id + '"><i class="fas fa-sync"></i></button>';
            }
            
            tableContent += '</td>';
            tableContent += '</tr>';
        }

        tableContent += '</tbody></table>';

        // Display the data
        $('#modalContent').html(tableContent);

        $('#confirmButton').on('click', function () {
            confirmSelectedRows();
        });

        $('#printButton').on('click', function () {
            printTable();
        });

        $('#myModal').modal('show');
      } 
      else {
        toastr["error"](response.message, "Error:");
      }
    },
    error: function (xhr, status, error) {
      toastr["error"]('Error retrieving actual_ctn data: ' + error, "Error:");
    }
  });
}

function printTable() {
  // Open a new window for printing
  var printWindow = window.open('', '_blank');
  
  // Construct the print content (use the same content as displayed in the modal)
  var printContent = '<html><head><title>Print</title></head><body>';
  printContent += $('#modalContent').html(); // Use the modal content
  printContent += '</body></html>';

  // Write the content to the new window
  printWindow.document.open();
  printWindow.document.write(printContent);
  printWindow.document.close();

  // Print the content
  printWindow.print();
}

function confirmSelectedRows() {
  var selectedRowIds = [];

  // Loop through the checkboxes to find selected rows
  $('.postedCheckbox:checked').each(function () {
    var rowId = $(this).attr('id').split('_')[1];
    selectedRowIds.push(rowId);
  });

  // Call the function to confirm the status for the selected rows
  confirmStatus(selectedRowIds);
}

function confirmStatus(rowIds) {
  // Update the status to Confirmed for the specified rows
  // You can make an AJAX request or perform any other action here

  // Example: Call an AJAX function to update the status
  $.ajax({
    url: 'php/confirmStatus.php',
    type: 'POST',
    data: { ids: rowIds },
    success: function (response) {
      var obj = JSON.parse(response);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
      }
      else{
        toastr["error"](obj.message, "Failed:");
      }

      $('#myModal').modal('hide');
    },
    error: function (xhr, status, error) {
      //$('#myModal').modal('hide');
      toastr["error"](error, "Failed:");
    }
  });
}

function importExcel(id) {
  $('#spinnerLoading').show();
  $.post('php/getBooking.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#uploadModal').modal('show');
      $('#uploadForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when pull data", "Failed:");
    }

    $('#spinnerLoading').hide();
  });
}

function addRow2(defaultDONumber, defaultPONumber) {
    var newRow = '<tr>' +
        '<td><input type="text" class="form-control" placeholder="Enter DO Number" value="' + defaultDONumber + '"></td>' +
        '<td><input type="text" class="form-control" placeholder="Enter PO Number" value="' + defaultPONumber + '"></td>' +
        '<td><button type="button" class="btn btn-danger removeRowBtn">Remove</button></td>' +
        '</tr>';
  
    // Append the new row to the table
    $('#doPoTable tbody').append(newRow);
    $('#doPoTable tbody tr:last-child td:first-child input').focus();
}

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getBooking.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#booking_date').val(formatDate2(new Date(obj.message.booking_date)));
      $('#extendModal').find('#pickup_method').val(obj.message.pickup_method).trigger('change');
      $('#extendModal').find('#customerNo').val(obj.message.customer).trigger('change');
      $('#extendModal').find('#branch').val(obj.message.branch);
      $('#extendModal').find('#address').val(obj.message.pickup_location);
      $('#extendModal').find('#description').val(obj.message.description);
      $('#extendModal').find('#extimated_ctn').val(obj.message.estimated_ctn);
      $('#extendModal').find('#actual_ctn').val(obj.message.actual_ctn);
      $('#extendModal').find('#gate').val(obj.message.gate);
      $('#extendModal').find('#checker').val(obj.message.checker);
      $('#extendModal').find('#vehicleNoTxt').val(obj.message.vehicle_no);
      $('#extendModal').find('#form_no').val(obj.message.form_no);
      $('#extendModal').find('#col_goods').val(obj.message.col_goods);
      $('#extendModal').find('#col_chk').val(obj.message.col_chq);
      $('#extendModal').find('#internal_notes').val(obj.message.internal_notes);

      $('#extendModal').modal('show');
      $('#extendForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when pull data", "Failed:");
    }
    $('#spinnerLoading').hide();
  });
}

function refreshRow(id, rowId, bookingDate, customerId){
  $('#spinnerLoading').show();
  $.post('php/revertDO.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      toastr["success"]('Row reverted successfully.', "Success:");
      $('#weightTable').DataTable().ajax.reload();
      loadModalContent(rowId, bookingDate, customerId);
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when pull data", "Failed:");
    }
    $('#spinnerLoading').hide();
  });
}

function editRow(id) {
  //$('#spinnerLoading').show();
  $.post('php/getDO.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#myModal').hide();
      $('#doModal').find('#id').val(obj.message.id);
      $('#doModal').find('#booking_date').val(formatDate2(new Date(obj.message.booking_date)));
      $('#doModal').find('#delivery_date').val(formatDate2(new Date(obj.message.delivery_date)));
      $('#doModal').find('#cancellation_date').val(formatDate2(new Date(obj.message.cancellation_date)));
      $('#doModal').find('#customerNo').val(obj.message.customer).trigger('change');
      $('#doModal').find('#hypermarket').val(obj.message.hypermarket);
      $('#doModal').find('#states').val(obj.message.states);
      
      $('#zones').empty();
      var dataIndexToMatch = obj.message.states;

      $('#zoneHidden option').each(function() {
        var dataIndex = $(this).data('index');

        if (dataIndex == dataIndexToMatch) {
          $('#doModal').find('#zones').append($(this).clone());
          $('#doModal').find('#zones').val(obj.message.zone);
          $('#doModal').find('#zones').trigger('change');
        }
      });
      
      $('#doModal').find('#hypermarket').trigger('change');
      $('#doModal').find('#do_type').val(obj.message.do_type);
      $('#doModal').find('#do_no').val(obj.message.do_number);
      $('#doModal').find('#po_no').val(obj.message.po_number);
      $('#doModal').find('#description').val(obj.message.note);
      $('#doModal').find('#actual_ctn').val(obj.message.actual_carton);
      $('#doModal').find('#need_grn').val(obj.message.need_grn);
      $('#doModal').find('#loadingTime').val(obj.message.loading_time);

      if(obj.message.hypermarket == '0'){
        // Define the value and text for the new option
        var newOption = new Option(obj.message.direct_store, obj.message.direct_store, false, false);
        
        // Append the new option to the select element
        $('#doModal').find('#hypermarket').trigger('change');
        $('#doModal').find('#outlets').empty().val(obj.message.outlet);
        $('#doModal').find('#outlets').attr('required', false);
        $('#doModal').find('#direct_store').attr('required', true);
        $('#doModal').find('#direct_store').data('select2').$container.show();
        $('#doModal').find('#direct_store').append(newOption).trigger('change');
      }
      else{
        $('#doModal').find('#hypermarket').trigger('change');
        //$('#doModal').find('#zones').empty().val(obj.message.zone);
        $('#doModal').find('#outlets').attr('required', true);
        $('#doModal').find('#outlets').show();
        $('#doModal').find('#direct_store').val('');
        $('#doModal').find('#direct_store').data('select2').$container.hide();
        //$('#doModal').find('.select2-container').hide();
      }

      var doDetails = obj.message.do_details || []; // Assuming do_details is an array of objects
      $('#jsonDataField').val(JSON.stringify(doDetails));
      $('#doPoTable tbody').empty();

      // Populate doPoTable with data from doDetails
      for (var i = 0; i < doDetails.length; i++) {
        var newRow = '<tr>' +
          '<td><input type="text" class="form-control" value="' + doDetails[i].doNumber + '"></td>' +
          '<td><input type="text" class="form-control" value="' + doDetails[i].poNumber + '"></td>' +
          '<td><button type="button" class="btn btn-danger removeRowBtn">Remove</button></td>' +
          '</tr>';
        $('#doPoTable tbody').append(newRow);
      }
      
      $('#doModal').modal('show');

      $('#doForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when pull data", "Failed:");
    }
    //$('#spinnerLoading').hide();
  });
}

function deactivate(id) {
  if (confirm('Are you sure you want to delete this items?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteBooking.php', {userID: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#weightTable').DataTable().ajax.reload();
      }
      else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
      }
      else{
        toastr["error"]("Something wrong when activate", "Failed:");
      }
      $('#spinnerLoading').hide();
    });
  }
}

function deleteRow(id, rowId, bookingDate, customerId) {
  if (confirm('Are you sure you want to delete this DO?')) {
    $('#spinnerLoading').show();
    $.ajax({
      url: 'php/deleteDO.php', // Your PHP script to delete the row
      type: 'POST',
      data: { userID: id },
      success: function (response) {
        var data = JSON.parse(response);
        if (data.status == 'success') {
          toastr["success"]('Row deleted successfully.', "Success:");
          $('#weightTable').DataTable().ajax.reload();
          loadModalContent(rowId, bookingDate, customerId); // Reload modal content
          $('#spinnerLoading').hide();
        } 
        else {
          toastr["error"]('Failed to delete the row.', "Error:");
          $('#spinnerLoading').hide();
        }
      },
      error: function (xhr, status, error) {
        toastr["error"]('Error deleting row: ' + error, "Error:");
        $('#spinnerLoading').hide();
      }
    });
  }
}

// Function to display preview table
function displayPreview(data) {
  // Parse the Excel data
  var workbook = XLSX.read(data, { type: 'binary' });

  // Get the first sheet
  var sheetName = workbook.SheetNames[0];
  var sheet = workbook.Sheets[sheetName];

  // Convert the sheet to an array of objects
  var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

  // Get the headers
  var headers = jsonData[0];

  // Create HTML table headers
  var htmlTable = '<table style="width:100%;"><thead><tr>';
  headers.forEach(function(header) {
      htmlTable += '<th>' + header + '</th>';
  });
  htmlTable += '</tr></thead><tbody>';

  // Iterate over the data and create table rows
  for (var i = 1; i < jsonData.length; i++) {
      htmlTable += '<tr>';
      var rowData = jsonData[i];
      rowData.forEach(function(cellData, index) {
          htmlTable += '<td><input type="text" id="'+headers[index]+i+'" name="'+headers[index]+'['+(i-1)+']" value="' + cellData + '" /></td>';
      });
      htmlTable += '</tr>';
  }

  htmlTable += '</tbody></table>';

  var previewTable = document.getElementById('previewTable');
  previewTable.innerHTML = htmlTable;
}

function picked(id) {
  $.post('php/checkingFormNo.php', {userID: id}, function(data){
    var obj = JSON.parse(data);

    if(obj.status === 'success'){
      $('#spinnerLoading').show();
      $.post('php/pickedBooking.php', {userID: id}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
          toastr["success"](obj.message, "Success:");
          $('#weightTable').DataTable().ajax.reload();
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
        }
        else{
          toastr["error"]("Something wrong when activate", "Failed:");
        }
        $('#spinnerLoading').hide();
      });
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
      edit(id);
    }
    else{
      toastr["error"]("Something wrong when activate", "Failed:");
    }
  });
}

function invoice(id) {
  $.post('php/invoiceBooking.php', {userID: id}, function(data){
    var obj = JSON.parse(data);

    if(obj.status === 'success'){
      toastr["success"](obj.message, "Success:");
      $('#weightTable').DataTable().ajax.reload();
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when activate", "Failed:");
    }
    $('#spinnerLoading').hide();
  });
}
</script>