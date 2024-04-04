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
  $reasons = $db->query("SELECT * FROM reasons WHERE deleted = '0'");
  $hypermarket = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
  $hypermarket2 = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $vehicles = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
  $drivers = $db->query("SELECT * FROM drivers WHERE deleted = '0'");
  $drivers2 = $db->query("SELECT * FROM drivers WHERE deleted = '0'");
}
?>

<style>
  @media screen and (min-width: 676px) {
    .modal-dialog {
      max-width: 1900px; /* New width for default modal */
    }
  }
</style>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Good Return</h1>
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
              <div class="form-group col-3">
                <label>Hypermarket</label>
                <select class="form-control" id="hypermarketFilter" name="hypermarketFilter" style="width: 100%;">
                  <option selected="selected">-</option>
                  <?php while($rowhypermarket2=mysqli_fetch_assoc($hypermarket2)){ ?>
                    <option value="<?=$rowhypermarket2['id'] ?>"><?=$rowhypermarket2['name'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group col-3">
                <label>Outlets</label>
                <select class="form-control" id="outletsFilter" name="outletsFilter" style="width: 100%;"></select>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <label>Driver</label>
                  <select class="form-control" id="pickupMethod" name="pickupMethod">
                    <option value="" selected disabled hidden>Please Select</option>
                    <?php while($rowdrivers22=mysqli_fetch_assoc($drivers2)){ ?>
                      <option value="<?=$rowdrivers22['name'] ?>"><?=$rowdrivers22['name'] ?></option>
                    <?php } ?>
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
              <div class="form-group col-3">
                <label>Status</label>
                <select class="form-control" id="statusFilter" name="statusFilter" style="width: 100%;">
                  <option selected="selected">-</option>
                  <option value="Created">Created</option>
                  <option value="Collected">Collected</option>
                  <option value="Invoiced">Invoiced</option>
                </select>
              </div>
              <div class="form-group col-3">
                <label>GR No.</label>
                <input type="text" class="form-control" id="grnFilter" name="grnFilter">
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
              <div class="col-6">Return</div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="updateStatus">
                  <i class="fas fa-pen"></i>
                  Update Status
                </button>
              </div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="newReturn">
                  <i class="fas fa-plus"></i>
                  New Return
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th></th>
                  <th>GR No.</th>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>Driver</th>
                  <th>Collection <br>Date</th>
                  <th>Collection <br>Type</th>
                  <th>Total <br>Carton</th>
                  <th>Locations</th>
                  <th>Status</th>
                  <th></th>
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
                  <option value="Collected">Collected</option>
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

<div class="modal fade" id="extendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">

      <form role="form" id="extendForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Add New Return</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Return Date *</label>
                  <div class='input-group date' id="returnDate" data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" data-target="#returnDate" id="return_date" name="returnDate" required/>
                    <div class="input-group-append" data-target="#returnDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
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
            <div class="form-group col-4">
              <label>Driver</label>
              <select class="form-control" id="driver" name="driver" >
                <option value="" selected disabled hidden>Please Select</option>
                <?php while($rowdrivers=mysqli_fetch_assoc($drivers)){ ?>
                  <option value="<?=$rowdrivers['name'] ?>"><?=$rowdrivers['name'] ?></option>
                <?php } ?>
              </select>                      
            </div>
          </div>
          <div class="row">
            <div class="form-group col-4">
              <label>Lorry No</label>
              <select class="form-control" id="lorry" name="lorry" >
                <option value="" selected disabled hidden>Please Select</option>
                <?php while($rowvehicles=mysqli_fetch_assoc($vehicles)){ ?>
                  <option value="<?=$rowvehicles['veh_number'] ?>"><?=$rowvehicles['veh_number'] ?></option>
                <?php } ?>
              </select>                      
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Collection Date </label>
                  <div class='input-group date' id="collectionDate" data-target-input="nearest">
                    <input type='text' class="form-control datetimepicker-input" data-target="#collectionDate" id="collection_date" name="collectionDate"/>
                    <div class="input-group-append" data-target="#collectionDate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label class="labelStatus">Collection Type </label>
                <select class="form-control" id="collectionType" name="collectionType">
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="Self Collect">Self Collect</option>
                  <option value="SOS Delivery">SOS Delivery</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <h4>Return Items</h4>
            <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-price">Add Items</button>
          </div>
          <table style="width: 100%;">
            <thead>
              <tr>
                <th>GRN/RTV No.</th>
                <th>Hypermarket</th>
                <th>Location</th>
                <th>Carton</th>
                <th>Reason</th>
                <th>Warehouse</th>
                <th>Amount</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody id="pricingTable"></tbody>
            <tfoot id="pricingFoot">
              <tr>
                <th colspan="3" style="text-align:right;">Total Cartons</th>
                <th><input type="number" class="form-control" id="totalCarton" name="totalCarton" readonly></th>
                <th colspan="2" style="text-align:right;">Total Amount</th>
                <th><input type="number" class="form-control" id="totalAmount" name="totalAmount" readonly></th>
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

<script type="text/html" id="pricingDetails">
  <tr class="details">
    <td>
      <input type="text" class="form-control" id="grn_no" placeholder="Enter GRN/RTV" required>
    </td>
    <td>
      <select class="form-control" style="width: 100%;" id="hypermarket" required>
        <?php while($row2=mysqli_fetch_assoc($hypermarket)){ ?>
          <option value="<?=$row2['id'] ?>"><?=$row2['name'] ?></option>
        <?php } ?>
      </select>
    </td>
    <td>
      <select class="form-control" style="width: 100%;" id="location" required></select>
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
      <input type="text" class="form-control" id="warehouse"  placeholder="Enter ..." required>
    </td>
    <td>
        <input type="number" class="form-control" id="price" placeholder="Enter ..." required>
    </td>
    <td><button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button></td>
  </tr>
</script>

<script>
var pricingCount = $("#pricingTable").find(".details").length;

$(function () {
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
      'url':'php/loadReturn.php'
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
      { data: 'GR_No' },
      { data: 'return_date' },
      { data: 'customer_name' },
      { data: 'driver' },
      { data: 'collection_date' },
      { data: 'collection_type' },
      { data: 'total_carton' },
      { 
        data: 'locations', // Change from 'return_type' to 'locations'
        render: function ( data, type, row ) {
          // Assuming data is an array, create a string with <br> between each location
          return Array.isArray(data) ? data.join('<br>') : data;
        }
      },
      { data: 'status' },
      {
        data: 'id',
        render: function (data, type, row) {
          var showPrintedOption = true;  // Add your condition to determine whether to show "Printed" or not
          var showInvoiceOption = row.collection_date !== '';
          var invoiceOption = showInvoiceOption ? '<option value="collect">Collect</option><option value="invoice">Invoice</option>' : '';

          // Check if 'warehouse' or 'price' is present in return_details
          if (row.return_details && Array.isArray(row.return_details)) {
            for (var i = 0; i < row.return_details.length; i++) {
              if (row.return_details[i].warehouse == null || row.return_details[i].warehouse == '' 
              || row.return_details[i].price == null || row.return_details[i].price == '') {
                // Don't show "Printed" option if 'warehouse' or 'price' is present
                showPrintedOption = false;
                break;  // No need to continue checking once condition is met
              }
            }
          }

          if(row.status == 'Invoiced'){
            return '';
          }
          else{
            return '<select id="actions' + data + '" class="form-select form-select-sm" onchange="performAction(' + data + ', this.value)">' +
            '<option value="" selected disabled>Action</option>' +
            '<option value="edit">Edit</option>' +
            '<option value="print" ' + (showPrintedOption ? '' : 'style="display:none;"') + '>Print</option>' +
            '<option value="deactivate">Deactivate</option>' +
            invoiceOption +
            '</select>';
          }
        }
      },
      /*{
        data: 'id',
        render: function (data, type, row) {
          return '<select id="actions' + data + '" class="form-select form-select-sm" onchange="performAction(' + data + ', this.value)">' +
              '<option value="" selected disabled>Action</option>' +
              '<option value="edit">Edit</option>' +
              '<option value="print">Print</option>' +
              '<option value="deactivate">Deactivate</option>' +
              '</select>';
        }
      },*/
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

  $('#returnDate').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'YYYY-MM-DD',
    defaultDate: tomorrow
  });

  $('#collectionDate').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'YYYY-MM-DD',
    minDate: tomorrow
  });

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#extendModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/return.php', $('#extendForm').serialize(), function(data){
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
        $.post('php/updateReturn.php', $('#updateForm').serialize(), function(data){
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
    }
  });

  $('#hypermarketFilter').on('change', function(){
    if($('#hypermarketFilter').val()){
      $('#extendModal').find('#outlets').empty();

      $.post('php/retrieveOutlets.php', {hypermarket: $('#hypermarketFilter').val()}, function(data){
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

  $('#newReturn').on('click', function(){
    var date = new Date();

    $('#extendModal').find('#id').val("");
    $('#extendModal').find('#return_date').val(formatDate(date));
    $('#extendModal').find('#customerNo').val("");
    $('#extendModal').find('#lorry').val("");
    $('#extendModal').find('#driver').val("");
    $('#extendModal').find('#collection_date').val("");
    $('#extendModal').find('#collectionType').val("");
    pricingCount = 0;
    $('#pricingTable').html('');
    $('#totalCarton').val("0");
    $('#totalAmount').val("0.00");
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

  $(".add-price").click(function(){
    var $addContents = $("#pricingDetails").clone();
    $("#pricingTable").append($addContents.html());

    $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
    $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
    $("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);

    $("#pricingTable").find('#grn_no:last').attr('name', 'grn_no['+pricingCount+']').attr("id", "grn_no" + pricingCount);
    $("#pricingTable").find('#hypermarket:last').attr('name', 'hypermarket['+pricingCount+']').attr("id", "hypermarket" + pricingCount);
    $("#pricingTable").find('#location:last').attr('name', 'location['+pricingCount+']').attr("id", "location" + pricingCount);
    $("#pricingTable").find('#carton:last').attr('name', 'carton['+pricingCount+']').attr("id", "carton" + pricingCount);
    $("#pricingTable").find('#reason:last').attr('name', 'reason['+pricingCount+']').attr("id", "reason" + pricingCount).val("1");
    $("#pricingTable").find('#other_reason').attr('name', 'other_reason['+pricingCount+']').attr("id", "other_reason" + pricingCount);
    $("#pricingTable").find('#warehouse:last').attr('name', 'warehouse['+pricingCount+']').attr("id", "warehouse" + pricingCount);
    $("#pricingTable").find('#price:last').attr('name', 'price['+pricingCount+']').attr("id", "price" + pricingCount);

    $("#other_reason" + pricingCount).hide();
    pricingCount++;
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

    $('#pricingTable tr.details').each(function () {
      // Get the values of itemPrice and itemWeight for the current row
      var itemPrice = parseFloat($(this).find('input[id^="carton"]').val()) || 0;
      totalAmount += itemPrice;
      $('#totalCarton').val(totalAmount);
    });
  });

  $("#pricingTable").on('change', 'select[id^="hypermarket"]', function(){
    var element = $(this).parents('.details').find('select[id^="location"]');
    element.html('');
    $.post('php/retrieveOutlets.php', {hypermarket: $(this).val()}, function(data){
      var obj = JSON.parse(data);
      
      if(obj.status === 'success'){
        for(var i=0; i<obj.message.length; i++){
          element.append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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
  });

  $("#pricingTable").on('change', 'select[id^="reason"]', function(){
    if($(this).val() == 'Others'){
      $(this).parents('.details').find('input[id^="other_reason"]').show();
      $(this).parents('.details').find('select[id^="reason"]').hide();
    }
    else{
      $(this).parents('.details').find('input[id^="other_reason"]').hide();
      $(this).parents('.details').find('select[id^="reason"]').show();
    }
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

  $("#pricingTable").on('change', 'input[id^="carton"]', function(){
    var totalAmount = 0;

    $('#pricingTable tr.details').each(function () {
      // Get the values of itemPrice and itemWeight for the current row
      var itemPrice = parseFloat($(this).find('input[id^="carton"]').val()) || 0;
      totalAmount += itemPrice;
      $('#totalCarton').val(totalAmount);
    });
  });

  $('#filterSearch').on('click', function(){
    //$('#spinnerLoading').show();

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var hypermarketValue = $('#hypermarketFilter').val() ? $('#hypermarketFilter').val() : '';
    var outletValue = $('#outletsFilter').val() ? $('#outletsFilter').val() : '';
    var driverValue= $('#pickupMethod').val() ? $('#pickupMethod').val() : '';
    var customerNoValue = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var statusValue = $('#statusFilter').val() ? $('#statusFilter').val() : '';
    var grnValue = $('#grnFilter').val() ? $('#grnFilter').val() : '';

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
        'url':'php/filterReturn.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          hypermarket: hypermarketValue,
          outlet: outletValue,
          driver: driverValue,
          customer: customerNoValue,
          status: statusValue,
          grn: grnValue
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
        { data: 'GR_No' },
        { data: 'return_date' },
        { data: 'customer_name' },
        { data: 'driver' },
        { data: 'collection_date' },
        { data: 'collection_type' },
        { data: 'total_carton' },
        { 
          data: 'locations', // Change from 'return_type' to 'locations'
          render: function ( data, type, row ) {
            // Assuming data is an array, create a string with <br> between each location
            return Array.isArray(data) ? data.join('<br>') : data;
          }
        },
        { data: 'status' },
        {
          data: 'id',
          render: function (data, type, row) {
            var showPrintedOption = true;  // Add your condition to determine whether to show "Printed" or not
            var showInvoiceOption = row.collection_date !== '';
            var invoiceOption = showInvoiceOption ? '<option value="invoice">Invoice</option>' : '';

            // Check if 'warehouse' or 'price' is present in return_details
            if (row.return_details && Array.isArray(row.return_details)) {
              for (var i = 0; i < row.return_details.length; i++) {
                if (row.return_details[i].warehouse == null || row.return_details[i].warehouse == '' 
                || row.return_details[i].price == null || row.return_details[i].price == '') {
                  // Don't show "Printed" option if 'warehouse' or 'price' is present
                  showPrintedOption = false;
                  break;  // No need to continue checking once condition is met
                }
              }
            }

            if(row.status == 'Invoiced'){
              return '';
            }
            else{
              return '<select id="actions' + data + '" class="form-select form-select-sm" onchange="performAction(' + data + ', this.value)">' +
              '<option value="" selected disabled>Action</option>' +
              '<option value="edit">Edit</option>' +
              '<option value="collect">Collect</option>' +
              '<option value="print" ' + (showPrintedOption ? '' : 'style="display:none;"') + '>Print</option>' +
              '<option value="deactivate">Deactivate</option>' +
              invoiceOption +
              '</select>';
            }
          }
        },
        /*{
          data: 'id',
          render: function (data, type, row) {
            return '<select id="actions' + data + '" class="form-select form-select-sm" onchange="performAction(' + data + ', this.value)">' +
                '<option value="" selected disabled>Action</option>' +
                '<option value="edit">Edit</option>' +
                '<option value="print">Print</option>' +
                '<option value="deactivate">Deactivate</option>' +
                '</select>';
          }
        },*/
        { 
          className: 'dt-control',
          orderable: false,
          data: null,
          render: function ( data, type, row ) {
            return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
          }
        }
      ]
    });
  });
});

function format(row) {
    var returnString = '<table class="table table-bordered">';
    returnString += '<thead><tr><th>GRN No</th><th>Hypermarket</th><th>Location</th><th>Carton</th><th>Warehouse</th><th>Price</th><th>Reason</th><th>Other Reason</th></tr></thead>';
    returnString += '<tbody>';

    for (var i = 0; i < row.return_details.length; i++) {
        returnString += '<tr>';
        returnString += '<td>' + row.return_details[i].grn_no + '</td>';
        returnString += '<td>' + row.return_details[i].hypermarket + '</td>';
        returnString += '<td>' + row.return_details[i].location + '</td>';
        returnString += '<td>' + row.return_details[i].carton + '</td>';
        returnString += '<td>' + row.return_details[i].warehouse + '</td>';
        returnString += '<td>' + parseFloat(row.return_details[i].price).toFixed(2).toString() + '</td>';
        returnString += '<td>' + row.return_details[i].reason + '</td>';
        returnString += '<td>' + row.return_details[i].other_reason + '</td>';
        returnString += '</tr>';
    }

    returnString += '</tbody></table>';

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

function performAction(data, selectedValue) {
  switch (selectedValue) {
    case 'edit':
      edit(data);
      break;
    case 'print':
      print(data);
      break;
    case 'deactivate':
      deactivate(data);
      break;
    case 'collect':
      collect(data);
      break;
    case 'invoice':
      invoice(data);
      break;
    default:
      break;
  }
}

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getReturn.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#return_date').val(obj.message.return_date);
      $('#extendModal').find('#customerNo').val(obj.message.customer);
      $('#extendModal').find('#lorry').val(obj.message.vehicle);
      $('#extendModal').find('#driver').val(obj.message.driver);
      $('#extendModal').find('#collection_date').val(obj.message.collection_date);
      $('#extendModal').find('#collectionType').val(obj.message.collection_type);
      $('#pricingTable').html('');
      pricingCount = 0;
      $('#totalCarton').val(obj.message.total_carton);
      $('#totalAmount').val(obj.message.total_amount);

      var details = obj.message.return_details;
      for(var i=0; i<details.length; i++){
        var $addContents = $("#pricingDetails").clone();
        $("#pricingTable").append($addContents.html());

        $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
        $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
        $("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);

        $("#pricingTable").find('#grn_no:last').attr('name', 'grn_no['+pricingCount+']').attr("id", "grn_no" + pricingCount).val(details[i].grn_no);
        $("#pricingTable").find('#hypermarket:last').attr('name', 'hypermarket['+pricingCount+']').attr("id", "hypermarket" + pricingCount).val(details[i].hypermarket);
        $('#pricingTable').find("#hypermarket" + pricingCount).trigger('change');
        $("#pricingTable").find('#location:last').attr('name', 'location['+pricingCount+']').attr("id", "location" + pricingCount).val(details[i].location);
        $("#pricingTable").find('#carton:last').attr('name', 'carton['+pricingCount+']').attr("id", "carton" + pricingCount).val(details[i].carton);
        $("#pricingTable").find('#reason:last').attr('name', 'reason['+pricingCount+']').attr("id", "reason" + pricingCount).val(details[i].reason);
        $("#pricingTable").find('#other_reason').attr('name', 'other_reason['+pricingCount+']').attr("id", "other_reason" + pricingCount).val(details[i].other_reason);
        $("#pricingTable").find('#warehouse:last').attr('name', 'warehouse['+pricingCount+']').attr("id", "warehouse" + pricingCount).val(details[i].warehouse);
        $("#pricingTable").find('#price:last').attr('name', 'price['+pricingCount+']').attr("id", "price" + pricingCount).val(details[i].price);

        $("#other_reason" + pricingCount).hide();
        pricingCount++;
      }

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

function print(id){
  if (confirm('Are you sure you want to print this items?')) {
    $('#spinnerLoading').show();
    $.post('php/print_return.php', {userID: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        //toastr["success"](obj.message, "Success:");
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
        toastr["error"]("Something wrong when activate", "Failed:");
      }
      $('#spinnerLoading').hide();
    });
  }
}

function deactivate(id) {
  if (confirm('Are you sure you want to delete this items?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteReturn.php', {userID: id}, function(data){
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
  $.post('php/invoiceReturn.php', {userID: id}, function(data){
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