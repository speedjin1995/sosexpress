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
  $branch = $db->query("SELECT * FROM branch WHERE deleted = '0'");
  $branch2 = $db->query("SELECT * FROM branch WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $vehicles = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
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
  <?php while($row3=mysqli_fetch_assoc($branch)){ ?>
    <option value="<?=$row3['id'] ?>" data-index="<?=$row3['customer_id'] ?>"><?=$row3['name'] ?></option>
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
                  <select class="form-control" id="pickupMethod" name="pickupMethod">
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
                  <select class="form-control" id="customerNoFilter" name="customerNoFilter">
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
              <div class="col-6">Booking</div>
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
                  <th></th>
                  <th>Customer</th>
                  <th>Booking Datetime</th>
                  <th>Description</th>
                  <th>Estimated Ctn</th>
                  <th>Actual Ctn</th>
                  <th>Pickup Method</th>
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
                <select class="form-control" id="pickup_method" name="pickup_method" required>
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
                <select class="form-control" id="customerNo" name="customerNo" required>
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
                <input class="form-control" type="text" placeholder="Driver Name" id="driver" name="driver" />
              </div>
              <div class="form-group">
                <label>Lorry No *</label>
                <input class="form-control" type="text" placeholder="Lorry Number" id="lorry" name="lorry" />
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
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
            <!-- Add additional buttons if needed -->
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
  tomorrow.setDate(tomorrow.getDate() + 1);

  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'order': [[ 1, 'asc' ]],
    'columnDefs': [ { orderable: false, targets: [0] }],
    'ajax': {
      'url':'php/loadBooking.php'
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

    $.ajax({
        url: 'php/getActualCtnData.php',
        type: 'POST',
        data: { id: rowId, bookingDate: bookingDate, customerId: customerId },
        success: function (data) {
            var response = JSON.parse(data);

            if (response.status == 'success') {
                var tableContent = '<table class="table">';
                tableContent += '<thead><tr><th></th><th>Outlet</th><th>Booking Date</th><th>Number of Carton</th><th>DO No.</th><th>PO No.</th><th>Notes</th></tr></thead><tbody>';

                // Loop through each item in the 'message' array
                for (var i = 0; i < response.message.length; i++) {
                    var rowData = response.message[i];

                    tableContent += '<tr>';

                    // Conditionally add checkbox only when status is 'Posted'
                    if (rowData.status === 'Posted') {
                        var checkboxHtml = '<td><input type="checkbox" class="postedCheckbox" id="checkbox_' + rowData.id + '" checked></td>';
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
    });
  });


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
    defaultDate: tomorrow,
    minDate: tomorrow
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
    $('#extendModal').find('#booking_date').val(formatDate2(tomorrow));
    $('#extendModal').find('#pickup_method').val("");
    $('#extendModal').find('#customerNo').val("");
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
  
  $('#customerNo').on('change', function(){
    var dataIndex = $(this).find(":selected").attr('data-address');

    if (dataIndex) {
      $('#address').val(dataIndex); // Set the selected branch's text (address) into the textarea
    } 
    else {
      $('#address').val(''); // Clear the textarea or provide a default value
    }

    if($('#booking_date').val() && $('#customerNo').val()){
      $.post('php/existBooking.php', {bookingDate: $(booking_date).val(), customerNo: $('#customerNo').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#saveButton').prop('disabled', false);
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
          $('#saveButton').prop('disabled', true);
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
        //$('#spinnerLoading').hide();
      },
      // "footerCallback": function ( row, data, start, end, display ) {
      //   var api = this.api();

      //   // Remove the formatting to get integer data for summation
      //   var intVal = function (i) {
      //     return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
      //   };

      //   // Total over all pages
      //   total = api.column(3).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total2 = api.column(4).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total3 = api.column(5).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total4 = api.column(6).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total5 = api.column(7).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total6 = api.column(8).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total7 = api.column(9).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );

      //   // Total over this page
      //   pageTotal = api.column(3, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal2 = api.column(4, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal3 = api.column(5, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal4 = api.column(6, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal5 = api.column(7, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal6 = api.column(8, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal7 = api.column(9, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );

      //   // Update footer
      //   $(api.column(3).footer()).html(pageTotal +' kg ( '+ total +' kg)');
      //   $(api.column(4).footer()).html(pageTotal2 +' kg ( '+ total2 +' kg)');
      //   $(api.column(5).footer()).html(pageTotal3 +' kg ( '+ total3 +' kg)');
      //   $(api.column(6).footer()).html(pageTotal4 +' kg ( '+ total4 +' kg)');
      //   $(api.column(7).footer()).html(pageTotal5 +' ('+ total5 +')');
      //   $(api.column(8).footer()).html('RM'+pageTotal6 +' ( RM'+ total6 +' total)');
      //   $(api.column(9).footer()).html('RM'+pageTotal7 +' ( RM'+ total7 +' total)');
      // }
    });
  });

  $('#print').on('click', function () {
    var selectedIds = []; // An array to store the selected 'id' values

    $("#weightTable tbody input[type='checkbox']").each(function () {
      if (this.checked) {
        selectedIds.push($(this).val());
      }
    });

    if (selectedIds.length > 0) {
      $("#printModal").find('#id').val(selectedIds);
      $("#printModal").find('#driver').val('');
      $("#printModal").find('#lorry').val('');
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
    returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="picked('+row.id+
  ')"><i class="fas fa-truck"></i></button></div></div></div></div>';
  }
  else if(row.status == 'Picked'){
    returnString +='<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="invoice('+row.id+
  ')"><i class="fas fa-receipt"></i></button></div></div></div></div>';
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
      $('#myModal').modal('hide');
      toastr["success"](obj.message, "Success:");
    },
    error: function (xhr, status, error) {
      //$('#myModal').modal('hide');
      toastr["error"](error, "Failed:");
    }
  });
}

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getBooking.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#booking_date').val(formatDate2(new Date(obj.message.booking_date)));
      $('#extendModal').find('#pickup_method').val(obj.message.pickup_method);
      $('#extendModal').find('#customerNo').val(obj.message.customer);
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