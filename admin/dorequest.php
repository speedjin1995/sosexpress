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
  $hypermarket = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
  $hypermarket2 = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
  $states = $db->query("SELECT * FROM states WHERE deleted = '0'");
  $states2 = $db->query("SELECT * FROM states WHERE deleted = '0'");
  $zones = $db->query("SELECT * FROM zones WHERE deleted = '0'");
  $zones2 = $db->query("SELECT * FROM zones WHERE deleted = '0'");
  $outlet = $db->query("SELECT * FROM outlet WHERE deleted = '0'");
  $do_type = $db->query("SELECT * FROM do_type WHERE deleted = '0'");
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

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">DO Request</h1>
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
                  <select class="form-control" id="customerNoFilter" name="customerNoFilter">
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
                  <select class="form-control" id="stateFilter" name="stateFilter" style="width: 100%;">
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
                <select class="form-control" id="zonesFilter" name="zonesFilter" style="width: 100%;"></select>
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
              <div class="col-6">DO Request</div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="updateStatus">
                  <i class="fas fa-pen"></i>
                  Update Status
                </button>
              </div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="newBooking">
                  <i class="fas fa-plus"></i>
                  New DO REquest
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th></th>
                  <!--th>No</th-->
                  <th>Customer</th>
                  <th>Hypermarket</th>
                  <th>Outlet</th>
                  <th>Delivery Date</th>
                  <th>Number of Carton</th>
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
                <select class="form-control" id="customerNo" name="customerNo" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowCustomer=mysqli_fetch_assoc($customers)){ ?>
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
                  <option value="Posted">Post to Loading</option>
                  <!--option value="Loaded">Loaded</option-->
                  <option value="Delivered">Delivered</option>
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

<div class="modal fade" id="doModal" tabindex="-1" role="dialog" aria-labelledby="doModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="doModalLabel">Enter DO and PO Numbers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <button type="button" class="btn btn-success" id="addRowBtn">Add Row</button>
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
              <button type="button" class="btn btn-primary" id="saveRowsBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
var rowCounter = 0;

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
    'columnDefs': [ { orderable: false,  targets: [0] }],
    'ajax': {
      'url':'php/loadDO.php'
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
      //{ data: 'no' },
      { data: 'customer_name' },
      { data: 'hypermarket' },
      { data: 'outlet' },
      { data: 'delivery_date' },
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
  });

  $('#deliveryDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY',
    minDate: tomorrow
  });

  $('#cancellationDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY',
    minDate: tomorrow
  });

  $('#bookingDate').on('dp.change', function (e) {
    if($('#booking_date').val() && $('#customerNo').val()){
      $.post('php/checkBooking.php', {bookingDate: $(booking_date).val(), customerNo: $('#customerNo').val()}, function(data){
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

  $('#customerNo').on('change', function(){
    if($('#booking_date').val() && $('#customerNo').val()){
      $.post('php/checkBooking.php', {bookingDate: $(booking_date).val(), customerNo: $('#customerNo').val()}, function(data){
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
            $('#outletsFilter').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>');
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
    var outletsFilter = $('#batchFilter').val() ? $('#outletsFilter').val() : '';

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
        'url':'php/filterDORequest.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          state: stateFilter,
          customer: customerNoFilter,
          zones: zonesFilter,
          hypermarket: hypermarketFilter,
          outlets: outletsFilter
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
        //{ data: 'no' },
        { data: 'customer_name' },
        { data: 'hypermarket' },
        { data: 'outlet' },
        { data: 'delivery_date' },
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
        $.post('php/doRequest.php', $('#extendForm').serialize(), function(data){
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
        $.post('php/updateRequest.php', $('#updateForm').serialize(), function(data){
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

  $('#direct_store').select2({
    ajax: {
      url: 'php/getDirectStore.php',
      dataType: 'json',
      data: function (params) {
        var query = {
          search: params.term,
          states: $('#states').val(),
          zones: $('#zones').val(),
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
    $('#extendModal').find('#delivery_date').val(formatDate2(tomorrow));
    $('#extendModal').find('#cancellation_date').val(formatDate2(tomorrow));
    $('#extendModal').find('#customerNo').val("");
    $('#extendModal').find('#hypermarket').val("");
    $('#extendModal').find('#states').val("");
    $('#extendModal').find('#zones').empty().val("");
    $('#extendModal').find('#outlets').empty().val("");
    $('#extendModal').find('#do_type').val("DO");
    $('#extendModal').find('#do_no').val("");
    $('#extendModal').find('#po_no').val("");
    $('#extendModal').find('#description').val("");
    $('#extendModal').find('#actual_ctn').val("");
    $('#extendModal').find('#on_hold').val("No");
    $('#extendModal').find('#need_grn').val("No");
    $('#extendModal').find('#loadingTime').val("M");
    $('#extendModal').find('#direct_store').val("");
    $('#extendModal').find('#outlets').attr('required', true);
    $('#extendModal').find('#outlets').show();
    $('#extendModal').find('#direct_store').data('select2').$container.hide();
    $('#doPoTable tbody').empty();
    //$('#extendModal').find('.select2-container').hide();
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
      $('#extendModal').find("#direct_store").attr('required', false);
      $('#extendModal').find('#outlets').attr('required', true);
      $('#extendModal').find('#outlets').show();
      $('#extendModal').find('#direct_store').data('select2').$container.hide();

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
      $('#extendModal').find('#direct_store').data('select2').$container.show();
      $('#extendModal').find("#direct_store").attr('required', true);
      $('#extendModal').find("#direct_store").val('');
      //$('#extendModal').find('.select2-container').show();
    }
  });

  $('#zones').on('change', function(){
    if($('#states').val() && $('#zones').val() && $('#hypermarket').val() && $('#hypermarket').val() != '0'){
      $('#extendModal').find('#outlets').empty();
      $('#extendModal').find("#direct_store").attr('required', false);
      $('#extendModal').find('#outlets').attr('required', true);
      $('#extendModal').find('#outlets').show();
      $('#extendModal').find('#direct_store').data('select2').$container.hide();
      //$('#extendModal').find('.select2-container').hide();

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
      $('#extendModal').find('#direct_store').data('select2').$container.show();
      $('#extendModal').find("#direct_store").attr('required', true);
      $('#extendModal').find("#direct_store").val('');
      //$('#extendModal').find('.select2-container').show();
    }
  });

  $('#hypermarket').on('change', function(){
    if($('#states').val() && $('#zones').val() && $('#hypermarket').val() && $('#hypermarket').val() != '0'){
      $('#extendModal').find('#outlets').empty();
      $('#extendModal').find("#direct_store").attr('required', false);
      $('#extendModal').find('#outlets').attr('required', true);
      $('#extendModal').find('#outlets').show();
      $('#extendModal').find('#direct_store').data('select2').$container.hide();
      //$('#extendModal').find('.select2-container').hide();

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
      $('#extendModal').find('#direct_store').data('select2').$container.show();
      $('#extendModal').find("#direct_store").attr('required', true);
      //$('#extendModal').find('.select2-container').show();
      $('#extendModal').find("#direct_store").val('');
    }
  });

  $('#do_type').on('change', function(){
    if($(this).val() != 'DO'){
      $('#po_no').val($(this).val());
    }
    else{
      $('#po_no').val('');
    }
  });

  /*$('.js-data-example-ajax').select2({
    ajax: {
      url: 'php/searchOutlets.php',
      data: function (params) {
        var query = {
          search: params.term
        }

        return query;
      },
      processResults: function (data) {
        // Transforms the top-level key of the response object from 'items' to 'results'
        return {
          results: data.results
        };
      }
    }
  });*/

  $('#openModalBtn').on('click', function () {
    $('#doPoTable tbody').empty();
    addRow2($('#do_no').val(), $('#po_no').val()); // Pass default values to the addRow function
    $('#doModal').modal('show');
  });

  $('#addRowBtn').on('click', function () {
    addRow();
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
    $('#doModal').modal('hide');
  });
});

function addRow() {
  var newRow = '<tr>' +
      '<td><input type="text" class="form-control" placeholder="Enter DO Number"></td>' +
      '<td><input type="text" class="form-control" placeholder="Enter PO Number"></td>' +
      '<td><button type="button" class="btn btn-danger removeRowBtn">Remove</button></td>' +
      '</tr>';
  
  // Append the new row to the table
  $('#doPoTable tbody').append(newRow);
}

function addRow2(defaultDONumber, defaultPONumber) {
    var newRow = '<tr>' +
        '<td><input type="text" class="form-control" placeholder="Enter DO Number" value="' + defaultDONumber + '"></td>' +
        '<td><input type="text" class="form-control" placeholder="Enter PO Number" value="' + defaultPONumber + '"></td>' +
        '<td><button type="button" class="btn btn-danger removeRowBtn">Remove</button></td>' +
        '</tr>';
  
    // Append the new row to the table
    $('#doPoTable tbody').append(newRow);
}

function format (row) {
  var returnString = '<div class="row"><div class="col-md-3"><p>Booking Date: '+row.booking_date+
  '</p></div><div class="col-md-3"><p>Delivery Date: '+row.delivery_date+
  '</p></div><div class="col-md-3"><p>Cancellation Date: '+row.cancellation_date+
  '</p></div><div class="col-md-3"><p>Customer: '+row.customer_name+
  '</p></div></div><div class="row"><div class="col-md-3"><p>States: '+row.states+
  '</p></div><div class="col-md-3"><p>Zones: '+row.zones+
  '</p></div><div class="col-md-3"><p>Hypermarket: '+row.hypermarket+
  '</p></div><div class="col-md-3"><p>Outlets: '+(row.direct_store != null ? row.direct_store:row.outlet)+
  '</p></div></div><div class="row"><div class="col-md-3"><p>DO Type: '+row.do_type+
  '</p></div><div class="col-md-3"><p>DO No: '+row.do_number+
  '</p></div><div class="col-md-3"><p>PO No: '+row.po_number+
  '</p></div><div class="col-md-3"><p>Actual Carton: '+row.actual_carton+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Loading Time: '+row.loading_time+
  '</p></div><div class="col-md-3"><p>Status: '+row.status+
  '</p></div><div class="col-md-3"><p>Note: '+row.note+
  '</p></div><div class="col-md-3">';
  
  if(row.status == 'Created'){
    returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" title="Edit" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" title="Post to Loading" onclick="picked('+row.id+
  ')"><i class="fas fa-pallet"></i></button></div></div></div></div>';
  }
  else if(row.status == 'Posted'){
    returnString +='<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" title="Delivered" onclick="delivered('+row.id+
  ')"><i class="fas fa-truck"></i></button></div></div></div></div>';
  }
  else if(row.status == 'Confirmed'){
    returnString +='<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" title="Delivered" onclick="delivered('+row.id+
  ')"><i class="fas fa-truck"></i></button></div></div></div></div>';
  }
  else if(row.status == 'Delivered'){
    returnString +='<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" title="Invoice" onclick="invoice('+row.id+
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

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getDO.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#booking_date').val(formatDate2(new Date(obj.message.booking_date)));
      $('#extendModal').find('#delivery_date').val(formatDate2(new Date(obj.message.delivery_date)));
      $('#extendModal').find('#cancellation_date').val(formatDate2(new Date(obj.message.cancellation_date)));
      $('#extendModal').find('#customerNo').val(obj.message.customer);
      $('#extendModal').find('#hypermarket').val(obj.message.hypermarket);
      $('#extendModal').find('#states').val(obj.message.states);
      
      $('#zones').empty();
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

      if(obj.message.hypermarket == '0'){
        $('#extendModal').find('#hypermarket').trigger('change');
        $('#extendModal').find('#outlets').empty().val(obj.message.outlet);
        $('#extendModal').find('#outlets').attr('required', false);
        $('#extendModal').find('#direct_store').attr('required', true);
        $('#extendModal').find('#direct_store').val(obj.message.direct_store);
        $('#extendModal').find('#outlets').hide();
        $('#extendModal').find('#direct_store').data('select2').$container.show();
        //$('#extendModal').find('.select2-container').show();
      }
      else{
        $('#extendModal').find('#hypermarket').trigger('change');
        //$('#extendModal').find('#zones').empty().val(obj.message.zone);
        $('#extendModal').find('#outlets').attr('required', true);
        $('#extendModal').find('#outlets').show();
        $('#extendModal').find('#direct_store').val('');
        $('#extendModal').find('#direct_store').data('select2').$container.hide();
        //$('#extendModal').find('.select2-container').hide();
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
</script>