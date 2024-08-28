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
  $states = $db->query("SELECT * FROM states WHERE deleted = '0'");
  $zones = $db->query("SELECT * FROM zones WHERE deleted = '0'");
  $outlet = $db->query("SELECT * FROM outlet WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $vehicles = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
  $drivers = $db->query("SELECT * FROM drivers WHERE deleted = '0'");
}
?>

<style>
  @media screen and (min-width: 676px) {
    .modal-dialog {
      max-width: 1900px; /* New width for default modal */
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
        <h1 class="m-0 text-dark">Tasks</h1>
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
                <label>Type</label>
                <select class="form-control" id="invNoinput" name="invNoinput" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="GRN Collection">GRN Collection</option>
                  <option value="Reject Collection">Reject Collection</option>
                  <option value="Goods Return">Goods Return</option>
                  <option value="General">General</option>
                </select>
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
              <div class="col-8">Tasks</div>
              <div class="col-4">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="newReturn">
                  <i class="fas fa-plus"></i>
                  New Tasks
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th>Customer</th>
                  <th>Vehicle</th>
                  <th>Driver</th>
                  <th>Locations</th>
                  <th>Date</th>
                  <th>Type</th>
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

<div class="modal fade" id="extendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">

      <form role="form" id="extendForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Add New Task</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Type *</label>
                <select class="form-control" id="taskType" name="taskType" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="GRN Collection">GRN Collection</option>
                  <option value="Reject Collection">Reject Collection</option>
                  <option value="Goods Return">Goods Return</option>
                  <option value="General">General</option>
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label class="labelStatus">Customer</label>
                <select class="form-control" id="customerNo" name="customerNo" >
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
                <label class="labelStatus">Hypermarket </label>
                <select class="form-control" id="hypermarket" name="hypermarket" >
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowhypermarket=mysqli_fetch_assoc($hypermarket)){ ?>
                    <option value="<?=$rowhypermarket['id'] ?>"><?=$rowhypermarket['name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label class="labelStatus">States </label>
                <select class="form-control" id="states" name="states" >
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
                <label for="rate">Zones </label>
                <select class="form-control" style="width: 100%;" id="zones" name="zones"></select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label for="rate">Outlet </label>
                <select class="form-control" style="width: 100%;" id="outlets" name="outlets"></select>
                <!--select class="js-data-example-ajax" id="direct_store" name="direct_store"></select-->
                <input class="form-control" type="text" placeholder="Outlet" id="direct_store" name="direct_store">
              </div>
            </div>
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
          </div>
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label for="rate">RTV/GRN No.</label>
                <input class="form-control" type="text" placeholder="RTV / GRN" id="rtvgrn" name="rtvgrn">
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <label class="labelStatus">Remark</label>
                <textarea class="form-control" id="description" name="description" placeholder="Enter your description"></textarea>
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

<script>
$(function () {
  $("#zoneHidden").hide();
  $("#branchHidden").hide();
  $('#direct_store').hide();

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
      'url':'php/loadTasks.php'
    },
    'columns': [
      { data: 'customerName' },
      { data: 'vehicle_no' },
      { data: 'driver_name' },
      { data: 'outletName' },
      { data: 'booking_date' },
      { data: 'type' },
      { 
        data: 'id',
        render: function ( data, type, row ) {
          return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
        }
      },
      { 
        className: 'dt-control',
        orderable: false,
        data: null,
        render: function ( data, type, row ) {
          return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.id+'"><i class="fas fa-angle-down"></i></td>';
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

  $('#bookingDate').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: tomorrow,
    minDate: tomorrow
  });

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#extendModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/task.php', $('#extendForm').serialize(), function(data){
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
    }
  });

  $('#filterSearch').on('click', function(){
    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var invoiceFilter = $('#invNoinput').val() ? $('#invNoinput').val() : '';

    //Destroy the old Datatable
    $("#weightTable").DataTable().clear().destroy();

    //Create new Datatable
    table = $("#weightTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'order': [[ 1, 'asc' ]],
      'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterTask.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          customer: customerNoFilter,
          invoice: invoiceFilter
        } 
      },
      'columns': [
        { data: 'customerName' },
        { data: 'vehicle_no' },
        { data: 'driver_name' },
        { data: 'outletName' },
        { data: 'booking_date' },
        { data: 'type' },
        { 
          data: 'id',
          render: function ( data, type, row ) {
            return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
          }
        },
        { 
          className: 'dt-control',
          orderable: false,
          data: null,
          render: function ( data, type, row ) {
            return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.id+'"><i class="fas fa-angle-down"></i></td>';
          }
        }
      ],
      "rowCallback": function( row, data, index ) {
        //$('td', row).css('background-color', '#E6E6FA');
      },        
    });
  });

  $('#newReturn').on('click', function(){
    var date = new Date();

    $('#extendModal').find('#id').val("");
    $('#extendModal').find('#taskType').val("");
    $('#extendModal').find('#customerNo').val("");
    $('#extendModal').find('#driver').val("");
    $('#extendModal').find('#lorry').val("");
    $('#extendModal').find('#hypermarket').val("");
    $('#extendModal').find('#states').val("");
    $('#extendModal').find('#zones').empty().val("");
    $('#extendModal').find('#outlets').empty().val("");
    $('#extendModal').find('#booking_date').val(formatDate2(date));
    $('#extendModal').find('#rtvgrn').val("");
    $('#extendModal').find('#description').val("");
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
      $('#extendModal').find("#direct_store").hide();
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
      $('#extendModal').find("#direct_store").show();
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
      $('#extendModal').find("#direct_store").hide();
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
      $('#extendModal').find("#direct_store").show();
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
      $('#extendModal').find("#direct_store").hide();
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
      $('#extendModal').find("#direct_store").show();
      $('#extendModal').find("#direct_store").attr('required', true);
      //$('#extendModal').find('.select2-container').show();
      $('#extendModal').find("#direct_store").val('');
    }
  });
});

function format(row) {
  return '<div class="row"><div class="col-md-3"><p>Customer Name: '+row.customerName+
  '</p></div><div class="col-md-3"><p>Hypermarket: '+row.hypermarketName+
  '</p></div><div class="col-md-3"><p>States: '+row.statesName+
  '</p></div><div class="col-md-3"><p>Zones: '+row.zonesName+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Outlet: '+row.outletName+
  '</p></div><div class="col-md-3"><p>Vehicle No: '+row.vehicle_no+
  '</p></div><div class="col-md-3"><p>Driver Name: '+row.driver_name+
  '</p></div><div class="col-md-3"><p>Date: '+row.booking_date+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Code: '+row.code+
  '</p></div><div class="col-md-3"><p>Status: '+row.remark+
  '</p></div><div class="col-md-3"><p>Source: '+row.source+
  '</p></div></div>';
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
}

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getTask.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#taskType').val(obj.message.type);
      $('#extendModal').find('#customerNo').val(obj.message.customer);
      $('#extendModal').find('#driver').val(obj.message.driver_name);
      $('#extendModal').find('#lorry').val(obj.message.vehicle_no);
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
      $('#extendModal').find('#booking_date').val(formatDate2(new Date(obj.message.booking_date)));
      $('#extendModal').find('#rtvgrn').val(obj.message.code);
      $('#extendModal').find('#description').val(obj.message.remark);

      if(obj.message.hypermarket == '0'){
        $('#extendModal').find('#hypermarket').trigger('change');
        $('#extendModal').find('#outlets').empty().val(obj.message.outlet);
        $('#extendModal').find('#direct_store').val(obj.message.direct_store);
        $('#extendModal').find('#outlets').hide();
        $('#extendModal').find("#direct_store").show();
      }
      else{
        $('#extendModal').find('#hypermarket').trigger('change');
        $('#extendModal').find('#outlets').val(obj.message.outlet);
        $('#extendModal').find('#outlets').show();
        $('#extendModal').find('#direct_store').val('');
        $('#extendModal').find("#direct_store").hide();
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
    $.post('php/deleteTasks.php', {userID: id}, function(data){
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