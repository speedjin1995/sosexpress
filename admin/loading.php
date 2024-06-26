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
  $hypermarket = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
  $hypermarket2 = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
  $hypermarket3 = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
  $states = $db->query("SELECT * FROM states WHERE deleted = '0'");
  $states2 = $db->query("SELECT * FROM states WHERE deleted = '0'");
  $states3 = $db->query("SELECT * FROM states WHERE deleted = '0'");
  $zones = $db->query("SELECT * FROM zones WHERE deleted = '0'");
  $zones2 = $db->query("SELECT * FROM zones WHERE deleted = '0'");
  $outlet = $db->query("SELECT * FROM outlet WHERE deleted = '0'");
  $reasons = $db->query("SELECT * FROM reasons WHERE deleted = '0' AND category = 'REJECT'");
  $reasons2 = $db->query("SELECT * FROM reasons WHERE deleted = '0' AND category = 'INBACK'");
  $vehicles = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
  $vehicles2 = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
  $vehicles3 = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
  $drivers = $db->query("SELECT * FROM drivers WHERE deleted = '0'");
  $drivers2 = $db->query("SELECT * FROM drivers WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $units = $db->query("SELECT * FROM units WHERE deleted = '0'");
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

<select class="form-control" style="width: 100%;" id="unitHidden" style="display: none;">
  <?php while($row4=mysqli_fetch_assoc($units)){ ?>
    <option value="<?=$row4['id'] ?>"><?=$row4['units'] ?></option>
  <?php } ?>
</select>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Loading</h1>
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
                  <label>Customer No</label>
                  <select class="form-control select2" id="customerNoFilter" name="customerNoFilter">
                    <option value="" selected disabled hidden>Please Select</option>
                    <?php while($rowCustomer2=mysqli_fetch_assoc($customers2)){ ?>
                      <option value="<?=$rowCustomer2['id'] ?>"><?=$rowCustomer2['customer_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>States</label>
                  <select class="form-control select2" id="stateFilter" name="stateFilter" style="width: 100%;">
                    <option selected="selected">-</option>
                    <?php while($rowStatus2=mysqli_fetch_assoc($states2)){ ?>
                      <option value="<?=$rowStatus2['id'] ?>"><?=$rowStatus2['states'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-3">
                <label>Zones</label>
                <select class="form-control select2" id="zonesFilter" name="zonesFilter" style="width: 100%;"></select>
              </div>

              <div class="form-group col-3">
                <label>Hypermarket</label>
                <select class="form-control select2" id="hypermarketFilter" name="hypermarketFilter" style="width: 100%;">
                  <option selected="selected">-</option>
                  <?php while($rowhypermarket2=mysqli_fetch_assoc($hypermarket2)){ ?>
                    <option value="<?=$rowhypermarket2['id'] ?>"><?=$rowhypermarket2['name'] ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group col-3">
                <label>Outlets</label>
                <select class="form-control select2" id="outletsFilter" name="outletsFilter" style="width: 100%;"></select>
              </div>

              <div class="form-group col-3">
                <label>Status</label>
                <select class="form-control select2" id="statusFilter" name="statusFilter" style="width: 100%;">
                  <option selected="selected">-</option>
                  <option value="Posted" selected>Posted</option>
                  <option value="Printed">Printed</option>
                  <option value="Delivered">Delivered</option>
                  <option value="Invoiced">Invoiced</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-3">
                <div class="form-group">
                  <label>DO No.</label>
                  <input class="form-control" type="text" placeholder="DO Number" id="doSearch" name="doSearch">
                </div>
              </div>
              <div class="col-6"></div>
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
              <div class="col-6">DO Request</div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="printBigDo">
                  <i class="fas fa-file"></i>
                  Print Big DO
                </button>
              </div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="printLoading">
                  <i class="fas fa-newspaper"></i>
                  Print Loading Report
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                  <th>Status</th>
                  <th>Delivery<br>Date</th>
                  <th>Customer</th>
                  <th>Hypermarket</th>
                  <th>Outlet</th>
                  <th>No. of<br>Carton</th>
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

<div class="modal fade" id="extendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="extendForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Loading Details</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Booking Date *</label>
                <div class='input-group date' id="bookingDate" data-target-input="nearest">
                  <input type='text' class="form-control datetimepicker-input" data-target="#bookingDate" id="booking_date" name="bookingDate" />
                  <div class="input-group-append" data-target="#bookingDate" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Delivery Date *</label>
                  <div class='input-group date' id="deliveryDate" data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" data-target="#deliveryDate" id="delivery_date" name="deliveryDate" />
                    <div class="input-group-append" data-target="#deliveryDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Cancellation Date *</label>
                  <div class='input-group date' id="cancellationDate" data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" data-target="#cancellationDate" id="cancellation_date" name="cancellationDate" />
                    <div class="input-group-append" data-target="#cancellationDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label class="labelStatus">Customer *</label>
                <select class="form-control" id="customerNo" name="customerNo" >
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowCustomer=mysqli_fetch_assoc($customers)){ ?>
                    <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['customer_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label class="labelStatus">Hypermarket *</label>
                <select class="form-control" id="hypermarket" name="hypermarket" >
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowhypermarket=mysqli_fetch_assoc($hypermarket)){ ?>
                    <option value="<?=$rowhypermarket['id'] ?>"><?=$rowhypermarket['name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label class="labelStatus">States *</label>
                <select class="form-control" id="states" name="states" >
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowCustomer=mysqli_fetch_assoc($states)){ ?>
                    <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['states'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label for="rate">Zones *</label>
                <select class="form-control" style="width: 100%;" id="zones" name="zones" ></select>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label for="rate">Outlet *</label>
                <select class="form-control" style="width: 100%;" id="outlets" name="outlets" ></select>
                <input class="form-control" type="text" placeholder="DO No." id="direct_store" name="direct_store" >
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label for="rate">DO Type *</label>
                <select class="form-control" id="do_type" name="do_type" >
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="DO">DO</option>
                  <option value="Consignment">Consignment</option>
                  <option value="Non-trade">Non-trade</option>
                </select>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>DO No.</label>
                <input class="form-control" type="text" placeholder="DO No." id="do_no" name="do_no" >
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>PO No.</label>
                <input class="form-control" type="text" placeholder="PO Number" id="po_no" name="po_no" >
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Actual Carton *</label>
                <input class="form-control" type="number" placeholder="Actual Carton" id="actual_ctn" name="actual_ctn" >
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>On-Hold *</label>
                <select class="form-control" id="on_hold" name="on_hold" required>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Sent on Date </label>
                  <div class='input-group date' id="sentOnDate" data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" data-target="#sentOnDate" id="sent_on_date" name="sentOnDate"/>
                    <div class="input-group-append" data-target="#sentOnDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Back On Date </label>
                  <div class='input-group date' id="backOnDate" data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" data-target="#backOnDate" id="back_on_date" name="backOnDate"/>
                    <div class="input-group-append" data-target="#backOnDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>GRN Received </label>
                <input class="form-control" type="text" placeholder="GRN No." id="grn_received" name="grn_received">
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>GRN File</label>
                <input class="form-control" type="file" placeholder="GRN No." id="grn_files" name="grn_files">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Notes</label>
                <textarea class="form-control" id="notes" name="notes" placeholder="Note"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <h4>Particular</h4>
            <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-price">Add Items</button>
          </div>
          <table style="width: 100%;">
            <thead>
              <tr>
                <th>Notes</th>
                <th>Quantity</th>
                <th>Price/Size</th>
                <th style="display:none;">UOM</th>
                <th>Unit Price</th>
                <th>Amount</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody id="pricingTable"></tbody>
            <tfoot id="pricingFoot">
              <tr>
                <th colspan="4" style="text-align:right;">Total Amount</th>
                <th><input type="number" class="form-control" id="totalAmount" name="totalAmount" value="0.00" readonly></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
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
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="updateForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Reject Details</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <input type="hidden" class="form-control" id="customerNo" name="customerNo">
          <input type="hidden" class="form-control" id="hypermarket" name="hypermarket">
          <input type="hidden" class="form-control" id="outlets" name="outlets">

          <div class="row">
            <h4>Reject Item</h4>
            <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-reject">Add Items</button>
          </div>
          <table style="width: 100%;">
            <thead>
              <tr>
                <th>DO No.</th>
                <th>Carton</th>
                <th>Reason</th>
                <th>Warehouse</th>
                <th>Amount</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody id="pricingTable2"></tbody>
            <tfoot id="pricingFoot2">
              <tr>
                <th></th>
                <th><input type="number" class="form-control" id="totalCarton" name="totalCarton" value="0.00" readonly></th>
                <th colspan="2" style="text-align:right;">Total Amount</th>
                <th><input type="number" class="form-control" id="totalAmount2" name="totalAmount2" value="0.00" readonly></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="printDOModal">
  <div class="modal-dialog modal-xl" style="max-width: 50%;">
    <div class="modal-content">

      <form role="form" id="printDOForm">
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
              <div class="form-group">
                <label>Checker *</label>
                <select class="form-control" id="checker" name="checker" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowUser=mysqli_fetch_assoc($users)){ ?>
                    <option value="<?=$rowUser['id'] ?>"><?=$rowUser['name'] ?></option>
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

<div class="modal fade" id="printReportModal">
  <div class="modal-dialog modal-xl" style="max-width: 50%;">
    <div class="modal-content">

      <form role="form" id="printReportForm">
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
                <label>Driver Name </label>
                <select class="form-control" id="driver" name="driver" >
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowdrivers=mysqli_fetch_assoc($drivers2)){ ?>
                    <option value="<?=$rowdrivers['id'] ?>"><?=$rowdrivers['name'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Lorry No </label>
                <select class="form-control" id="lorry" name="lorry" >
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowvehicles=mysqli_fetch_assoc($vehicles)){ ?>
                    <option value="<?=$rowvehicles['veh_number'] ?>"><?=$rowvehicles['veh_number'] ?></option>
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

<div class="modal fade" id="reasonModal">
  <div class="modal-dialog modal-xl" style="max-width: 50%;">
    <div class="modal-content">

      <form role="form" id="reasonForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Enter Reason</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Reasons </label>
                <select class="form-control" id="reasons" name="reasons" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowreasons2=mysqli_fetch_assoc($reasons2)){ ?>
                    <option value="<?=$rowreasons2['id'] ?>"><?=$rowreasons2['type'] ?></option>
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

<div class="modal fade" id="viewModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form role="form" id="viewForm">
        <div class="modal-header">
          <h4 class="modal-title">View & upload files</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="card card-primary">
              <div class="card-body">
                <div class="row" id="imagesList"></div>
              </div>
            </div>
            <div class="card card-default" id="upload-zone">
              <div class="card-header">
                <h3 class="card-title">Upload files</h3>
              </div>
              <div class="card-body">
                <div id="actions" class="row">
                  <div class="col-lg-6">
                    <div class="btn-group w-100">
                      <span class="btn btn-success col fileinput-button">
                        <i class="fas fa-plus"></i>
                        <span>Add files</span>
                      </span>
                      <button type="submit" class="btn btn-primary col start">
                        <i class="fas fa-upload"></i>
                        <span>Start upload</span>
                      </button>
                      <button type="reset" class="btn btn-warning col cancel">
                        <i class="fas fa-times-circle"></i>
                        <span>Cancel upload</span>
                      </button>
                    </div>
                  </div>
                  <div class="col-lg-6 d-flex align-items-center">
                    <div class="fileupload-process w-100">
                      <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                        <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="table table-striped files" id="previews">
                  <div id="template" class="row mt-2">
                    <div class="col-auto">
                        <span class="preview"><img src="data:," alt="" data-dz-thumbnail /></span>
                    </div>
                    <div class="col d-flex align-items-center">
                        <p class="mb-0">
                          <span class="lead" data-dz-name></span>
                          (<span data-dz-size></span>)
                        </p>
                        <strong class="error text-danger" data-dz-errormessage></strong>
                    </div>
                    <div class="col-4 d-flex align-items-center">
                        <div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                        </div>
                    </div>
                    <div class="col-auto d-flex align-items-center">
                      <div class="btn-group">
                        <button class="btn btn-primary start">
                          <i class="fas fa-upload"></i>
                          <span>Start</span>
                        </button>
                        <button data-dz-remove class="btn btn-warning cancel">
                          <i class="fas fa-times-circle"></i>
                          <span>Cancel</span>
                        </button>
                        <!--button data-dz-remove class="btn btn-danger delete">
                          <i class="fas fa-trash"></i>
                          <span>Delete</span>
                        </button-->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /.container-fluid -->
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="submit" id="submitOrder">Save Change</button>
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<script type="text/html" id="pricingDetails">
  <tr class="details">
    <td>
      <input type="text" class="form-control" id="particular" placeholder="Enter Particular">
    </td>
    <td>
      <input type="number" class="form-control" id="quantity_in"  placeholder="Enter ..." required>
    </td>
    <td>
      <div class="input-group">
        <input type="text" class="form-control" id="size" required>
        <div class="input-group-append">
          <span class="input-group-text" id="exclamation-icon" data-toggle="tooltip" data-placement="top" title="Tooltip message">
            <i class="fas fa-exclamation-circle"></i>
          </span>
        </div>
        <button type="button" class="btn btn-warning similarRequests" id="similarRequestsButton" style="display: none;" onclick="handleSimilarRequests()"><i class="fas fa-exclamation-circle" style="color: red;"></i></button>
      </div>
    </td>
    <td style="display:none;">
      <input type="text" class="form-control" id="unit" required>
    </td>
    <td>
      <input class="form-control" type="number" placeholder="Unit Price" id="unit_price" required>
    </td>
    <td>
      <input type="number" class="form-control" id="price" placeholder="Enter ..." required>
    </td>
    <td><button type="button" class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button></td>
  </tr>
</script>

<script type="text/html" id="pricingDetails2">
  <tr class="details">
    <td>
      <input type="text" class="form-control" id="grn_no"  placeholder="Enter ...">
    </td>
    <td>
      <input type="number" class="form-control" id="carton"  placeholder="Enter ..." required>
    </td>
    <td>
      <select class="form-control" style="width: 100%;" id="reason" required>
        <?php while($row3=mysqli_fetch_assoc($reasons)){ ?>
          <option value="<?=$row3['type'] ?>"><?=$row3['type'] ?></option>
        <?php } ?>
      </select>
      <input class="form-control" type="text" placeholder="Other Reasons" id="other_reason">
    </td>
    <td>
      <input type="text" class="form-control" id="warehouse"  placeholder="Enter ...">
    </td>
    <td>
      <input type="number" class="form-control" id="price" placeholder="Enter ...">
    </td>
    <td><button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button></td>
  </tr>
</script>

<div class="modal fade" id="similarPricingModal">
  <div class="modal-dialog modal-xl">
    <form id="similarPricingForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="similarPricingModalLabel">Similar Pricings</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="similarPricingModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
var pricingCount = $("#pricingTable").find(".details").length;
var similarCount = 0;
var pricingCount2 = $("#pricingTable2").find(".details").length;
var pricingJSON = '[]';
var do_number = '';

$(function () {
  $("#zoneHidden").hide();
  $("#branchHidden").hide();
  $('#unitHidden').hide();
  $('#direct_store').hide();
  $('[data-toggle="tooltip"]').tooltip()

  $('#selectAllCheckbox').on('change', function() {
    var checkboxes = $('#weightTable tbody input[type="checkbox"]');
    checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
  });

  $('.select2').select2({
    allowClear: true,
    placeholder: "Please Select"
  });

  // DropzoneJS Demo Code Start
  Dropzone.autoDiscover = false

  // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
  var previewNode = document.querySelector("#template")
  previewNode.id = ""
  var previewTemplate = previewNode.parentNode.innerHTML
  previewNode.parentNode.removeChild(previewNode)

  var myDropzone = new Dropzone("#upload-zone", { // Make the whole body a dropzone
    url: "php/uploadPictures.php", // Set the url
    thumbnailWidth: 80,
    thumbnailHeight: 80,
    parallelUploads: 20,
    previewTemplate: previewTemplate,
    autoQueue: false, // Make sure the files aren't queued until manually added
    previewsContainer: "#previews", // Define the container to display the previews
    clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
  })

  myDropzone.on("addedfile", function(file) {
    // Hookup the start button
    file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file) }
  })

  // Update the total progress bar
  myDropzone.on("totaluploadprogress", function(progress) {
    document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
  })

  myDropzone.on("sending", function(file, xhr, formData) {
    // Show the total progress bar when upload starts
    formData.append("filename", file.upload.uuid);
    formData.append("jobID", jobId);
    formData.append("jobStatus", jobStatus);
    document.querySelector("#total-progress").style.opacity = "1";
    // And disable the start button
    file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
  })

  // Hide the total progress bar when nothing's uploading anymore
  myDropzone.on("queuecomplete", function(progress) {
    document.querySelector("#total-progress").style.opacity = "0";
    $('#viewModal').modal('hide');
    $('#tableforOrder').DataTable().ajax.reload();
  })

  // Setup the buttons for all transfers
  // The "add files" button doesn't need to be setup because the config
  // `clickable` has already been specified.
  document.querySelector("#actions .start").onclick = function() {
    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
  }

  document.querySelector("#actions .cancel").onclick = function() {
    myDropzone.removeAllFiles(true);
  }

  //Date picker
  $('#fromDatePicker').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY',
    defaultDate: ''
  });

  $('#toDatePicker').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY',
    defaultDate: ''
  });

  var fromDateI = $('#fromDate').val();
  var toDateI= $('#toDate').val();
  var stateI = $('#stateFilter').val() ? $('#stateFilter').val() : '';
  var customerNoI = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
  var zonesI = $('#zonesFilter').val() ? $('#zonesFilter').val() : '';
  var hypermarketI= $('#hypermarketFilter').val() ? $('#hypermarketFilter').val() : '';
  var outletsI = $('#outletsFilter').val() ? $('#outletsFilter').val() : '';
  var statusI = $('#statusFilter').val() ? $('#statusFilter').val() : '';
  var doSearchI = $('#doSearch').val() ? $('#doSearch').val() : '';

  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'order': [[ 1, 'asc' ]],
    'columnDefs': [ { orderable: false,  targets: [0] }],
    /*'ajax': {
      'url':'php/loadLoading.php'
    },*/
    'ajax': {
      'type': 'POST',
      'url':'php/filterLoadingRequest.php',
      'data': {
        fromDate: fromDateI,
        toDate: toDateI,
        state: stateI,
        customer: customerNoI,
        zones: zonesI,
        hypermarket: hypermarketI,
        outlets: outletsI,
        status: statusI,
        doNumber: doSearchI
      } 
    },
    'columns': [
      {
        // Add a checkbox with a unique ID for each row
        data: 'id', // Assuming 'serialNo' is a unique identifier for each row
        className: 'select-checkbox',
        orderable: false,
        render: function (data, type, row) {
            if (row.status == 'Posted' || row.status == 'Confirmed') { // Assuming 'isInvoiced' is a boolean field in your row data
              return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
            } 
            else {
              return ''; // Return an empty string or any other placeholder if the item is invoiced
            }
          //return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
        }
      },
      {
        data: 'status',
        render: function(data, type, row) {
          if (row.hold == 'No') {
            return data; // Show only the status
          } else {
            return data + ' (On-hold)'; // Show status with '(On-hold)'
          }
        }
      },
      { data: 'delivery_date' },
      { data: 'customer_name' },
      { data: 'hypermarket' },
      { data: 'outlet' },
      { data: 'actual_carton' },
      
      { 
        className: 'dt-control',
        orderable: false,
        data: null,
        render: function ( data, type, row ) {
          return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
        }
      }
    ],
    "initComplete": function () {
      // Calculate the total carton value
      var totalCarton = this.api().column(5).data().reduce(function (acc, val) {
        return acc + parseInt(val, 10);
      }, 0);
      
      // Update the "info" message with the total carton value
      var info = "Displaying _START_ to _END_ of _TOTAL_ DOs with Total Carton of " + totalCarton;
      $(this).DataTable().settings()[0].oLanguage.sInfo = info;
      $(this).DataTable().draw();
    },
    "rowCallback": function( row, data, index ) {
      //$('td', row).css('background-color', '#E6E6FA');
    },        
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

  $('#bookingDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    defaultDate: new Date
  });

  $('#deliveryDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    defaultDate: new Date
  });

  $('#cancellationDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    defaultDate: new Date
  });

  $('#sentOnDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'YYYY-MM-DD',
    defaultDate: new Date
  });

  $('#backOnDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'YYYY-MM-DD',
    defaultDate: new Date,
  });

  $('#stateFilter').on('change', function(){
    $('#zonesFilter').empty();
    var dataIndexToMatch = $(this).val();

    $('#zoneHidden option').each(function() {
      var dataIndex = $(this).data('index');

      if (dataIndex == dataIndexToMatch) {
        $('#zonesFilter').append($(this).clone());
        $('#zonesFilter').trigger('change');
      }
    });

    if($('#stateFilter').val() && $('#zonesFilter').val() && $('#hypermarketFilter').val()){
      $('#extendModal').find('#outlets').empty();

      $.post('php/listOutlets.php', {states: $('#stateFilter').val(), zones: $('#zonesFilter').val(), hypermarket: $('#hypermarketFilter').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#outletsFilter').html('');
          $('#outletsFilter').append('<option selected="selected">-</option>');
          for(var i=0; i<obj.message.length; i++){
            $('#outletsFilter').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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
  });

  $('#hypermarketFilter').on('change', function(){
    if($('#stateFilter').val() && $('#zonesFilter').val() && $('#hypermarketFilter').val()){
      $('#extendModal').find('#outlets').empty();

      $.post('php/listOutlets.php', {states: $('#stateFilter').val(), zones: $('#zonesFilter').val(), hypermarket: $('#hypermarketFilter').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#outletsFilter').html('');
          $('#outletsFilter').append('<option selected="selected">-</option>');
          for(var i=0; i<obj.message.length; i++){
            $('#outletsFilter').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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
  });

  $('#zonesFilter').on('change', function(){
    if($('#stateFilter').val() && $('#zonesFilter').val() && $('#hypermarketFilter').val()){
      $('#extendModal').find('#outlets').empty();

      $.post('php/listOutlets.php', {states: $('#stateFilter').val(), zones: $('#zonesFilter').val(), hypermarket: $('#hypermarketFilter').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#outletsFilter').html('');
          $('#outletsFilter').append('<option selected="selected">-</option>');
          for(var i=0; i<obj.message.length; i++){
            $('#outletsFilter').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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
  });

  $('#filterSearch').on('click', function(){
    //$('#spinnerLoading').show();
    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var stateFilter = $('#stateFilter').val() ? $('#stateFilter').val() : '';
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var zonesFilter = $('#zonesFilter').val() ? $('#zonesFilter').val() : '';
    var hypermarketFilter = $('#hypermarketFilter').val() ? $('#hypermarketFilter').val() : '';
    var outletsFilter = $('#outletsFilter').val() ? $('#outletsFilter').val() : '';
    var statusFilter = $('#statusFilter').val() ? $('#statusFilter').val() : '';
    var doSearchI = $('#doSearch').val() ? $('#doSearch').val() : '';

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
        'url':'php/filterLoadingRequest.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          state: stateFilter,
          customer: customerNoFilter,
          zones: zonesFilter,
          hypermarket: hypermarketFilter,
          outlets: outletsFilter,
          status: statusFilter,
          doNumber: doSearchI
        } 
      },
      'columns': [
        {
          data: 'id',
          className: 'select-checkbox',
          orderable: false,
          render: function (data, type, row) {
            if (row.status == 'Posted' || row.status == 'Confirmed') { // Assuming 'isInvoiced' is a boolean field in your row data
                return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
            } 
            else {
                return ''; // Return an empty string or any other placeholder if the item is invoiced
            }
            //return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
          }
        },
        {
          data: 'status',
          render: function(data, type, row) {
            if (row.hold == 'No') {
              return data; // Show only the status
            } else {
              return data + ' (On-hold)'; // Show status with '(On-hold)'
            }
          }
        },
        { data: 'delivery_date' },
        { data: 'customer_name' },
        { data: 'hypermarket' },
        { data: 'outlet' },
        { data: 'actual_carton' },
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
      }
    });
  });

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#extendModal').hasClass('show')){
        $('#spinnerLoading').show();
        var formData = new FormData($('#extendForm')[0]); // Create FormData object from the form
        formData.append('filename', $('#grn_files')[0].files[0]); // Append the image file to the FormData object

        $.ajax({
          url: 'php/loading.php',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          enctype: 'multipart/form-data',
          success: function(data) {
            var obj = JSON.parse(data); 

            if(obj.status === 'success'){
              $('#extendModal').modal('hide');
              toastr["success"](obj.message, "Success:");
              $('#weightTable').DataTable().ajax.reload();
            } 
            else if(obj.status === 'failed'){
              toastr["error"](obj.message, "Failed:");
            } 
            else {
              toastr["error"]("Something wrong when edit", "Failed:");
            }

            $('#spinnerLoading').hide();
          }
        });
      }
      else if($('#updateModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/reject.php', $('#updateForm').serialize(), function(data){
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
      else if($('#printDOModal').hasClass('show')){
        $.post('php/print_big_do.php', $('#printDOForm').serialize(), function(data){
          var obj = JSON.parse(data);
      
          if(obj.status === 'success'){
            $('#printDOModal').modal('hide');
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
      else if($('#printReportModal').hasClass('show')){
        $.post('php/print_loading_report.php', $('#printReportForm').serialize(), function(data){
          var obj = JSON.parse(data);
      
          if(obj.status === 'success'){
            $('#printReportModal').modal('hide');
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
      else if($('#reasonModal').hasClass('show')){
        $.post('php/doReason.php', $('#reasonForm').serialize(), function(data){
          var obj = JSON.parse(data);
      
          if(obj.status === 'success'){
            $.post('php/inbackDO.php', {userID: $('#reasonModal').find('#id').val()}, function(data){
            var obj = JSON.parse(data);

            if(obj.status === 'success'){
              $('#reasonModal').modal('hide');
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
          }
          else{
            toastr["error"]("Something wrong when pull data", "Failed:");
          }
        });
      }
      else if($('#similarPricingModal').hasClass('show')){
        $.post('php/doReason.php', $('#similarPricingForm').serialize(), function(data){
          var obj = JSON.parse(data);
      
          if(obj.status === 'success'){
            $.post('php/updatePricing.php', {userID: $('#similarPricingModal').find('#id').val()}, function(data){
            var obj = JSON.parse(data);

            if(obj.status === 'success'){
              $('#similarPricingModal').modal('hide');
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
          }
          else{
            toastr["error"]("Something wrong when pull data", "Failed:");
          }
        });
      }
    }
  });
  
  $('#states').on('change', function(){
    $('#zones').empty();
    var dataIndexToMatch = $(this).val();

    $('#zoneHidden option').each(function() {
      var dataIndex = $(this).data('index');

      if (dataIndex == dataIndexToMatch) {
        $('#zones').append($(this).clone());
        $('#zones').trigger('change');
      }
    });

    if($('#states').val() && $('#zones').val() && $('#hypermarket').val() && $('#hypermarket').val() != '0'){
      $('#extendModal').find('#outlets').empty();
      //$('#extendModal').find("#direct_store").attr('required', false);
      //$('#extendModal').find('#outlets').attr('required', true);
      $('#extendModal').find('#outlets').show();
      $('#extendModal').find("#direct_store").hide();

      $.post('php/listOutlets.php', {states: $('#states').val(), zones: $('#zones').val(), hypermarket: $('#hypermarket').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          for(var i=0; i<obj.message.length; i++){
            $('#extendModal').find('#outlets').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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
      $('#extendModal').find('#outlets').attr('required', false);
      $('#extendModal').find('#outlets').hide();
      $('#extendModal').find("#direct_store").show();
      //$('#extendModal').find("#direct_store").attr('required', true);
      $('#extendModal').find("#direct_store").val('');
    }
  });

  $('#zones').on('change', function(){
    if($('#states').val() && $('#zones').val() && $('#hypermarket').val() && $('#hypermarket').val() != '0'){
      $('#extendModal').find('#outlets').empty();
      $('#extendModal').find("#direct_store").attr('required', false);
      //$('#extendModal').find('#outlets').attr('required', true);
      $('#extendModal').find('#outlets').show();
      $('#extendModal').find("#direct_store").hide();

      $.post('php/listOutlets.php', {states: $('#states').val(), zones: $('#zones').val(), hypermarket: $('#hypermarket').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          for(var i=0; i<obj.message.length; i++){
            $('#extendModal').find('#outlets').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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
      $('#extendModal').find('#outlets').attr('required', false);
      $('#extendModal').find('#outlets').hide();
      $('#extendModal').find("#direct_store").show();
      //$('#extendModal').find("#direct_store").attr('required', true);
      $('#extendModal').find("#direct_store").val('');
    }
  });

  $('#hypermarket').on('change', function(){
    if($('#states').val() && $('#zones').val() && $('#hypermarket').val() && $('#hypermarket').val() != '0'){
      $('#extendModal').find('#outlets').empty();
      $('#extendModal').find("#direct_store").attr('required', false);
      //$('#extendModal').find('#outlets').attr('required', true);
      $('#extendModal').find('#outlets').show();
      $('#extendModal').find("#direct_store").hide();

      $.post('php/listOutlets.php', {states: $('#states').val(), zones: $('#zones').val(), hypermarket: $('#hypermarket').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          for(var i=0; i<obj.message.length; i++){
            $('#extendModal').find('#outlets').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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
      $('#extendModal').find('#outlets').attr('required', false);
      $('#extendModal').find('#outlets').hide();
      $('#extendModal').find("#direct_store").show();
      //$('#extendModal').find("#direct_store").attr('required', true);
      $('#extendModal').find("#direct_store").val('');
    }
  });

  $(".add-price").click(function(){
    var $addContents = $("#pricingDetails").clone();
    $("#pricingTable").append($addContents.html());

    $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
    $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
    $("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);
    $("#pricingTable").find('#exclamation-icon:last').attr("id", "exclamation-icon" + pricingCount);

    $("#pricingTable").find('#particular:last').attr('name', 'particular['+pricingCount+']').attr("id", "particular" + pricingCount);
    $("#pricingTable").find('#quantity_in:last').attr('name', 'quantity_in['+pricingCount+']').attr("id", "quantity_in" + pricingCount);
    $("#pricingTable").find('#size:last').attr('name', 'size['+pricingCount+']').attr("id", "size" + pricingCount);
    $("#pricingTable").find('#unit_price:last').attr('name', 'unit_price['+pricingCount+']').attr("id", "unit_price" + pricingCount);
    $("#pricingTable").find('#price').attr('name', 'price['+pricingCount+']').attr("id", "price" + pricingCount);
    $("#pricingTable").find('#unit').attr('name', 'unit['+pricingCount+']').attr("id", "unit" + pricingCount);

    var pricingJson = JSON.parse(pricingJSON);

    $("#exclamation-icon" + pricingCount).hover(function () {
      var tooltipContent = '<ul>';
      pricingJson.forEach(function (item) {
        tooltipContent += '<li>Size: ' + item.size + ', Price: ' + item.price + ', Notes: ' + (item.notes ? item.notes : 'N/A') + '</li>';
      });
      tooltipContent += '</ul>';
      $(this).attr('data-original-title', tooltipContent);
      $(this).tooltip({
          html: true, // Set html option to true
          placement: 'top', // Adjust tooltip placement if needed
      }).tooltip('show');
    }, function () {
      $(this).tooltip('hide');
    });

    $("#other_reason" + pricingCount).hide();
    pricingCount++;

    if (similarCount > 0) {
      $('.similarRequests').show();
    } 
    else {
      $('.similarRequests').hide();
    }
  });

  $("#pricingTable").on('click', 'button[id^="remove"]', function () {
    var index = $(this).parents('.details').attr('data-index');
    $("#pricingTable").append('<input type="hidden" name="deletedShip[]" value="'+index+'"/>');
    pricingCount--;
    $(this).parents('.details').remove();

    var totalAmount = 0;

    $('#pricingTable tr.details').each(function () {
      // Get the values of itemPrice and itemWeight for the current row
      var itemPrice = parseFloat($(this).find('input[name="price"]').val()) || 0;
      totalAmount += itemPrice;
      $('#totalAmount').val(parseFloat(totalAmount).toFixed(2));
    });
  });

  $("#pricingTable").on('change', 'input[id^="size"]', function(){
    var size = $(this).val() || '';
    var pricingJson = JSON.parse(pricingJSON);
    var element = $(this).parents('.details');
    pricingJson.forEach(function (item) {
      if(item.size.includes(size)){
        var optionText = $('#unitHidden option[value="' + item.unit + '"]').text();
        var formattedPrice = parseFloat(item.price || '0.00').toFixed(2);
        element.find('input[id^="unit"]').val(optionText || '');
        element.find('input[id^="unit_price"]').val(Number(formattedPrice));
        element.find('input[id^="unit_price"]').trigger('change');
      }
    });
  });

  $("#pricingTable").on('change', 'input[id^="quantity_in"]', function(){
    var totalAmount = 0;
    var itemPrice = parseFloat($(this).parents('.details').find('input[id^="unit_price"]').val()) || 0;
    var itemQuantity = parseFloat($(this).val()) || 0;
    totalAmount = itemPrice * itemQuantity;
    $(this).parents('.details').find('input[id^="price"]').val(parseFloat(totalAmount).toFixed(2));
    $(this).parents('.details').find('input[id^="price"]').trigger('change');
  });

  $("#pricingTable").on('change', 'input[id^="unit_price"]', function(){
    var totalAmount = 0;
    var itemPrice = parseFloat($(this).parents('.details').find('input[id^="quantity_in"]').val()) || 0;
    var itemQuantity = parseFloat($(this).val()) || 0;
    totalAmount = itemPrice * itemQuantity;
    $(this).parents('.details').find('input[id^="price"]').val(parseFloat(totalAmount).toFixed(2));
    $(this).parents('.details').find('input[id^="price"]').trigger('change');
  });

  $("#pricingTable").on('change', 'input[id^="price"]', function(){
    var totalAmount = 0;

    $('#pricingTable tr.details').each(function () {
      // Get the values of itemPrice and itemWeight for the current row
      var itemPrice = parseFloat($(this).find('input[id^="price"]').val()) || 0;
      totalAmount += itemPrice;
      $('#totalAmount').val(parseFloat(totalAmount).toFixed(2));
    });
  });

  $(".add-reject").click(function(){
    var $addContents = $("#pricingDetails2").clone();
    $("#pricingTable2").append($addContents.html());

    $("#pricingTable2").find('.details:last').attr("id", "detail" + pricingCount2);
    $("#pricingTable2").find('.details:last').attr("data-index", pricingCount2);
    $("#pricingTable2").find('#remove:last').attr("id", "remove" + pricingCount2);

    $("#pricingTable2").find('#grn_no:last').attr('name', 'grn_no['+pricingCount2+']').attr("id", "grn_no" + pricingCount2).val(do_number);
    $("#pricingTable2").find('#carton:last').attr('name', 'carton['+pricingCount2+']').attr("id", "carton" + pricingCount2);
    $("#pricingTable2").find('#reason:last').attr('name', 'reason['+pricingCount2+']').attr("id", "reason" + pricingCount2).val("1");
    $("#pricingTable2").find('#other_reason').attr('name', 'other_reason['+pricingCount2+']').attr("id", "other_reason" + pricingCount2);
    $("#pricingTable2").find('#warehouse:last').attr('name', 'warehouse['+pricingCount2+']').attr("id", "warehouse" + pricingCount2);
    $("#pricingTable2").find('#price:last').attr('name', 'price['+pricingCount2+']').attr("id", "price" + pricingCount2);
    
    $("#other_reason" + pricingCount2).hide();
    pricingCount2++;
  });

  $("#pricingTable2").on('click', 'button[id^="remove"]', function () {
    var index = $(this).parents('.details').attr('data-index');
    $("#pricingTable2").append('<input type="hidden" name="deletedreject[]" value="'+index+'"/>');
    pricingCount--;
    $(this).parents('.details').remove();

    var totalAmount = 0;

    $('#pricingTable tr.details').each(function () {
      // Get the values of itemPrice and itemWeight for the current row
      var itemPrice = parseFloat($(this).find('input[name="price"]').val()) || 0;
      totalAmount += itemPrice;
      $('#totalAmount').val(parseFloat(totalAmount).toFixed(2));
    });

    $('#pricingTable tr.details').each(function () {
      // Get the values of itemPrice and itemWeight for the current row
      var itemPrice = parseFloat($(this).find('input[id^="carton"]').val()) || 0;
      totalAmount += itemPrice;
      $('#totalCarton').val(totalAmount);
    });
  });

  $("#pricingTable2").on('change', 'input[id^="carton"]', function(){
    var totalAmount = 0;

    $('#pricingTable2 tr.details').each(function () {
      // Get the values of itemPrice and itemWeight for the current row
      var itemCarton= parseInt($(this).find('input[id^="carton"]').val()) || 0;
      totalAmount += itemCarton;
      $('#totalCarton').val(parseFloat(totalAmount));
    });
  });

  $("#pricingTable2").on('change', 'input[id^="price"]', function(){
    var totalAmount = 0;

    $('#pricingTable2 tr.details').each(function () {
      // Get the values of itemPrice and itemWeight for the current row
      var itemPrice = parseFloat($(this).find('input[id^="price"]').val()) || 0;
      totalAmount += itemPrice;
      $('#totalAmount2').val(parseFloat(totalAmount).toFixed(2));
    });
  });

  $('#printBigDo').on('click', function () {
    var selectedIds = []; // An array to store the selected 'id' values

    $("#weightTable tbody input[type='checkbox']").each(function () {
      if (this.checked) {
        selectedIds.push($(this).val());
      }
    });

    if (selectedIds.length > 0) {
      $("#printDOModal").find('#id').val(selectedIds);
      $("#printDOModal").find('#driver').val('');
      $("#printDOModal").find('#lorry').val('');
      $("#printDOModal").modal("show");

      $('#printDOForm').validate({
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
      alert("Please select at least one DO to Deliver.");
    }
  });

  $('#printLoading').on('click', function () {
    var selectedIds = []; // An array to store the selected 'id' values

    $("#weightTable tbody input[type='checkbox']").each(function () {
      if (this.checked) {
        selectedIds.push($(this).val());
      }
    });

    if (selectedIds.length > 0) {
      $("#printReportModal").find('#id').val(selectedIds);
      $("#printReportModal").find('#driver').val('');
      $("#printReportModal").find('#lorry').val('');
      $("#printReportModal").modal("show");

      $('#printReportForm').validate({
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
      alert("Please select at least one DO to Load.");
    }
  });
});

function format(row) {
  var returnString = '<div class="row"><div class="col-md-3"><p>Booking Date: ' + row.booking_date +
    '</p></div><div class="col-md-3"><p>Delivery Date: ' + row.delivery_date +
    '</p></div><div class="col-md-3"><p>Cancellation Date: ' + row.cancellation_date +
    '</p></div><div class="col-md-3"><p>Customer: ' + row.customer_name +
    '</p></div></div><div class="row"><div class="col-md-3"><p>States: ' + row.states +
    '</p></div><div class="col-md-3"><p>Zones: ' + row.zones +
    '</p></div><div class="col-md-3"><p>Hypermarket: ' + row.hypermarket +
    '</p></div><div class="col-md-3"><p>Outlets: ' + (row.direct_store != null ? row.direct_store : row.outlet) +
    '</p></div></div><div class="row"><div class="col-md-3"><p>DO Type: ' + row.do_type +
    '</p></div><div class="col-md-3"><p>DO No: ' + row.do_number +
    ' <i class="fas fa-exclamation-circle" id="exclamation-icon' + row.id + '"></i></p></div><div class="col-md-3"><p>PO No: ' + row.po_number +
    '</p></div><div class="col-md-3"><p>Actual Carton: ' + row.actual_carton +
    '</p></div></div><div class="row"><div class="col-md-3"><p>Loading Time: ' + row.loading_time +
    '</p></div><div class="col-md-3"><p>Status: ' + row.status +
    '</p></div><div class="col-md-3"></div><div class="col-md-3">';

  if (row.status == 'Created') {
    returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="edit(' + row.id +
      ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="deactivate(' + row.id +
      ')"><i class="fas fa-trash"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" title="Post for loading" onclick="picked(' + row.id +
      ')"><i class="fas fa-pallet"></i></button></div></div></div></div>';
  }
  else if (row.status == 'Posted') {
    returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="edit(' + row.id +
      ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="deactivate(' + row.id +
      ')"><i class="fas fa-trash"></i></button></div></div></div></div>';
  }
  else if (row.status == 'Confirmed') {
    returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="edit(' + row.id +
      ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="deactivate(' + row.id +
      ')"><i class="fas fa-trash"></i></button></div></div></div></div>';
  }
  else if (row.status == 'Printed') {
    returnString += '<div class="row"><div class="col-2"><button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="edit(' + row.id +
      ')"><i class="fas fa-pen"></i></button></div><div class="col-2"><button type="button" class="btn btn-danger btn-sm" title="Reject" onclick="reject(' + row.id +
      ')">RJ</button></div><div class="col-2"><button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="deactivate(' + row.id +
      ')"><i class="fas fa-trash"></i></button></div><div class="col-2"><button type="button" class="btn btn-info btn-sm" title="In Back" onclick="revert(' + row.id +
      ')"><i class="fas fa-sync"></i></button></div><div class="col-2"><button type="button" class="btn btn-primary btn-sm" title="Second Delivery" onclick="inback(' + row.id +
      ')"><i class="fas fa-pallet"></i></button></div><div class="col-2"><button type="button" class="btn btn-success btn-sm" title="Delivered" onclick="delivered(' + row.id +
      ')"><i class="fas fa-truck"></i></button></div></div></div></div>';
  }
  else if (row.status == 'Delivered') {
    returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" title="Invoicing" onclick="invoice(' + row.id +
      ')"><i class="fas fa-receipt"></i></button></div></div></div></div>';
  }
  else if (row.status == 'Invoiced') {
    returnString += '<div class="row"><div class="col-3"></div><div class="col-3"></div><div class="col-3"></div></div></div></div>';
  }

  returnString += '<div class="row"><div class="col-md-6"><p>Note: ' + row.note +
    '</p></div><div class="col-md-6"><p>Reason: ' + row.reason +
    '</p></div></div>';

  if (row.pricing_details.length > 0) {
    returnString += '<h4>Pricing</h4><table style="width: 100%;"><thead><tr><th>Notes</th><th>Quantity</th><th>Price/Size</th><th>Unit Price</th><th>Amount</th></tr></thead><tbody>'
    var total = 0;

    for (var i = 0; i < row.pricing_details.length; i++) {
      var item = row.pricing_details[i];
      returnString += '<tr><td>' + item.particular + '</td><td>' + item.quantity_in + '</td><td>' + item.size + '</td><td>' + item.unit_price + '</td><th>' + item.price + '</td></tr></thead><tbody>'
      total += parseFloat(item.price);
    }

    returnString += '</tbody><tfoot><th colspan="4" style="text-align:right;">Total Amount</th><th>' + total.toFixed(2) + '</th></tfoot></table>';
  }

  $("#exclamation-icon" + row.id).hover(function () {
    var tooltipContent = '<ul>';
    row.do_details.forEach(function (item) {
      tooltipContent += '<li>DO: ' + item.doNumber + ', PO: ' + item.poNumber + '</li>';
    });
    tooltipContent += '</ul>';
    $(this).attr('data-original-title', tooltipContent);
    $(this).tooltip({
        html: true, // Set html option to true
        placement: 'top', // Adjust tooltip placement if needed
    }).tooltip('show');
  }, function () {
    $(this).tooltip('hide');
  });

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

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getLoading.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      var isReadOnly = obj.message.status != 'Posted' && obj.message.status != 'Confirmed';

      setReadOnly('#extendModal #booking_date', isReadOnly);
      setReadOnly('#extendModal #delivery_date', isReadOnly);
      setReadOnly('#extendModal #cancellation_date', isReadOnly);
      setDisabled('#extendModal #customerNo', isReadOnly);
      setDisabled('#extendModal #hypermarket', isReadOnly);
      setDisabled('#extendModal #states', isReadOnly);
      setDisabled('#extendModal #on_hold', isReadOnly);
      setDisabled('#extendModal #zones', isReadOnly);
      setDisabled('#extendModal #do_type', isReadOnly);
      setDisabled('#extendModal #outlets', isReadOnly);
      setReadOnly('#extendModal #do_no', isReadOnly);
      setReadOnly('#extendModal #po_no', isReadOnly);
      setReadOnly('#extendModal #description', isReadOnly);
      setReadOnly('#extendModal #actual_ctn', isReadOnly);
      setReadOnly('#extendModal #need_grn', isReadOnly);
      setReadOnly('#extendModal #loadingTime', isReadOnly);
      setReadOnly('#extendModal #notes', isReadOnly);

      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#booking_date').val(formatDate2(new Date(obj.message.booking_date)));
      $('#extendModal').find('#delivery_date').val(formatDate2(new Date(obj.message.delivery_date)));
      $('#extendModal').find('#cancellation_date').val(formatDate2(new Date(obj.message.cancellation_date)));
      $('#extendModal').find('#customerNo').val(obj.message.customer);
      $('#extendModal').find('#hypermarket').val(obj.message.hypermarket);
      $('#extendModal').find('#states').val(obj.message.states);
      $('#extendModal').find('#on_hold').val(obj.message.hold);
      pricingJSON = obj.message.pricing;

      $('#extendModal').find('#zones').empty();
      var dataIndexToMatch = obj.message.states;

      $('#zoneHidden option').each(function() {
        var dataIndex = $(this).data('index');

        if (dataIndex == dataIndexToMatch) {
          $('#extendModal').find('#zones').append($(this).clone());
          $('#extendModal').find('#zones').val(obj.message.zone);
          $('#extendModal').find('#zones').trigger('change');
        }
      });

      $('#extendModal').find('#hypermarket').trigger('change');
      $('#extendModal').find('#do_type').val(obj.message.do_type);
      $('#extendModal').find('#do_no').val(obj.message.do_number);
      $('#extendModal').find('#po_no').val(obj.message.po_number);
      $('#extendModal').find('#description').val(obj.message.note);
      $('#extendModal').find('#actual_ctn').val(obj.message.actual_carton);
      $('#extendModal').find('#need_grn').val(obj.message.need_grn);
      $('#extendModal').find('#loadingTime').val(obj.message.loading_time);
      $('#extendModal').find('#notes').val(obj.message.note);

      if(obj.message.hypermarket == '0'){
        $('#extendModal').find('#hypermarket').trigger('change');
        $('#extendModal').find('#outlets').empty().val(obj.message.outlet);
        $('#extendModal').find('#outlets').attr('required', false);
        //$('#extendModal').find('#direct_store').attr('required', true);
        $('#extendModal').find('#direct_store').val(obj.message.direct_store);
        $('#extendModal').find('#outlets').hide();
        $('#extendModal').find("#direct_store").show();
        //$('#extendModal').find('.select2-container').show();
      }
      else{
        $('#extendModal').find('#hypermarket').trigger('change');
        //$('#extendModal').find('#zones').empty().val(obj.message.zone);
        //$('#extendModal').find('#outlets').attr('required', true);
        $('#extendModal').find('#outlets').show();
        $('#extendModal').find('#direct_store').val('');
        $('#extendModal').find("#direct_store").hide();
        //$('#extendModal').find('.select2-container').hide();
      }
      
      $('#extendModal').find('#pricingTable').html('');
      $('#extendModal').find('#totalAmount').val('0.00');
      pricingCount = 0;
      similarCount = 0;
      var total = 0;

      if(obj.message.pricing_details.length > 0){
        for(var i=0; i<obj.message.pricing_details.length; i++){
          var item = obj.message.pricing_details[i];
          var $addContents = $("#pricingDetails").clone();
          $("#pricingTable").append($addContents.html());

          $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
          $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
          $("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);
          $("#pricingTable").find('#exclamation-icon:last').attr("id", "exclamation-icon" + pricingCount);

          $("#pricingTable").find('#particular:last').attr('name', 'particular['+pricingCount+']').attr("id", "particular" + pricingCount).val(item.particular);
          $("#pricingTable").find('#quantity_in:last').attr('name', 'quantity_in['+pricingCount+']').attr("id", "quantity_in" + pricingCount).val(item.quantity_in);
          $("#pricingTable").find('#size:last').attr('name', 'size['+pricingCount+']').attr("id", "size" + pricingCount).val(item.size);
          $("#pricingTable").find('#unit_price:last').attr('name', 'unit_price['+pricingCount+']').attr("id", "unit_price" + pricingCount).val(item.unit_price);
          $("#pricingTable").find('#price').attr('name', 'price['+pricingCount+']').attr("id", "price" + pricingCount).val(item.price);
          $("#pricingTable").find('#unit').attr('name', 'unit['+pricingCount+']').attr("id", "unit" + pricingCount).val(item.unit);

          total += parseFloat(item.price);
          $('#extendModal').find('#totalAmount').val(total.toFixed(2));
          var pricingJ = JSON.parse(pricingJSON);

          $("#exclamation-icon" + pricingCount).hover(function () {
            var tooltipContent = '<ul>';
            pricingJ.forEach(function (item) {
              tooltipContent += '<li>Size: ' + item.size + ', Price: ' + item.price + ', Notes: ' + (item.notes ? item.notes : 'N/A') + '</li>';
            });
            tooltipContent += '</ul>';
            $(this).attr('data-original-title', tooltipContent);
            $(this).tooltip({
                html: true, // Set html option to true
                placement: 'top', // Adjust tooltip placement if needed
            }).tooltip('show');
          }, function () {
            $(this).tooltip('hide');
          });
        }
      }
      
      $('#extendModal').modal('show');

      similarCount = obj.message.similar_requests_count;
      if (obj.message.similar_requests_count > 0) {
        $('#similarRequestsButton').show();
      } 
      else {
        $('#similarRequestsButton').hide();
      }

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

function reject(id) {
  $('#spinnerLoading').show();
  $.post('php/getDO.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#updateModal').find('#id').val(obj.message.id);
      $('#updateModal').find('#customerNo').val(obj.message.customer);
      $('#updateModal').find('#hypermarket').val(obj.message.hypermarket);
      $('#updateModal').find('#outlets').val(obj.message.outlet);
      pricingCount2 = 0;
      $('#updateModal').find('#pricingTable2').html('');
      do_number = obj.message.do_number
      $('#updateModal').modal('show');
      
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

function deactivate(id) {
  if (confirm('Are you sure you want to delete this items?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteDO.php', {userID: id}, function(data){
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

function revert(id) {
  $('#spinnerLoading').show();
  $.post('php/revertLoading.php', {userID: id}, function(data){
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

function picked(id) {
  $('#spinnerLoading').show();
  $.post('php/loadedDO.php', {userID: id}, function(data){
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

function delivered(id) {
  $.post('php/deliveredDO.php', {userID: id}, function(data){
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

function inback(id) {
  $('#reasonModal').find('#id').val(id);
  $('#reasonModal').find('#reasons').val('');
  $('#reasonModal').modal('show');
  
  $('#reasonForm').validate({
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

function invoice(id) {
  $.post('php/receivedDO.php', {userID: id}, function(data){
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

function handleSimilarRequests() {
  var id = $('#extendModal').find('#id').val();

  $.ajax({
    url: 'php/getSimilarPricing.php', // Replace with your server script URL
    type: 'POST',
    data: { id: id },
    success: function(response) {
      // Handle the response from the server
      var data = JSON.parse(response);
      if (data.status === 'success') {
        // Process the list of similar pricing
        var similarPricingList = data.message;
        console.log('Similar Pricing List:', similarPricingList);
        
        // Example: Update UI with similar pricing data
        updateUI(similarPricingList);
      } else {
        // Handle error case
        console.error('Failed to retrieve similar pricing:', data.message);
        alert('Failed to retrieve similar pricing. Please try again.');
      }
    },
    error: function(xhr, status, error) {
      console.error('AJAX Error:', error);
      alert('Error communicating with the server. Please try again later.');
    }
  });
}

// Function to update UI with similar pricing data (example)
function updateUI(similarPricingList) {
  // Start building the table content
  var modalContent = `
    <h3>Similar Pricing List</h3>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>DO No.</th>
          <th>PO No.</th>
          <th>Size</th>
          <th>Price</th>
          <th>Particular</th>
        </tr>
      </thead>
      <tbody>`;
  
  // Loop through each item and create table rows
  similarPricingList.forEach(function(item) {
    modalContent += `
      <tr>
        <td>${item.do_no}</td>
        <td>${item.po_no}</td>
        <td>${item.size}</td>
        <td>
          <input type="hidden" name="id[]" value="${item.id}">
          <input type="text" name="price[]" value="${item.price}" class="form-control">
        </td>
        <td>${item.particular}</td>
      </tr>`;
  });

  modalContent += `
      </tbody>
    </table>`;

  // Set the modal content and show the modal
  $('#similarPricingModalBody').html(modalContent);
  $('#similarPricingModal').modal('show');
  
  $('#similarPricingForm').validate({
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

// Set or remove readonly attribute based on status
function setReadOnly(selector, readOnly) {
  $(selector).prop('readonly', readOnly);
}

function setDisabled(selector, disabled) {
  $(selector).attr('disabled', disabled);
}
</script>