<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $todayStart = date('Y-m-d 00:00:00', strtotime('today'));
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
  $holiday = $db->query("SELECT * FROM holidays WHERE start_date <= '".$todayStart."' AND end_date >= '".$todayStart."' AND deleted = '0'");
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
                  <label>States</label>
                  <select class="form-control" id="stateFilter" name="stateFilter" style="width: 100%;">
                    <option selected="selected">-</option>
                    <?php while($rowStatus2=mysqli_fetch_assoc($states2)){ ?>
                      <option value="<?=$rowStatus2['id'] ?>"><?=$rowStatus2['states'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group col-3">
                <label>Zones</label>
                <select class="form-control" id="zonesFilter" name="zonesFilter" style="width: 100%;"></select>
              </div>
            </div>

            <div class="row">
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
              <div class="col-9">DO Request</div>
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
                  <th>No</th>
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
            <div class="col-4">
              <div class="form-group">
                <label for="rate">Zones *</label>
                <select class="form-control" style="width: 100%;" id="zones" name="zones" required></select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label for="rate">Outlet *</label>
                <select class="form-control" style="width: 100%;" id="outlets" name="outlets"></select>
                <!--select class="js-data-example-ajax" id="direct_store" name="direct_store"></select-->
                <input class="form-control" type="text" placeholder="Outlet" id="direct_store" name="direct_store">
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label for="rate">DO Type *</label>
                <select class="form-control" id="do_type" name="do_type" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="DO">DO</option>
                  <option value="Consignment">Consignment</option>
                  <option value="Non-trade">Non-trade</option>
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>DO No.</label>
                <input class="form-control" type="text" placeholder="DO No." id="do_no" name="do_no">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>PO No.</label>
                <input class="form-control" type="text" placeholder="PO Number" id="po_no" name="po_no">
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Estimated Carton *</label>
                <input class="form-control" type="number" placeholder="Actual Carton" id="actual_ctn" name="actual_ctn" required>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Need GRN *</label>
                <select class="form-control" id="need_grn" name="need_grn" required>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label class="labelStatus">Notes</label>
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

  <?php
    if($rowH=mysqli_fetch_assoc($holiday)){
      echo "$('#newBooking').hide();";
    }
  ?>

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
      { data: 'no' },
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
      format: 'DD/MM/YYYY HH:mm:ss A',
      defaultDate: new Date
  });

  $('#toDatePicker').datetimepicker({
      icons: { time: 'far fa-clock' },
      format: 'DD/MM/YYYY HH:mm:ss A',
      defaultDate: new Date
  });

  $('#bookingDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY HH:mm:ss A',
    defaultDate: new Date
  });

  $('#deliveryDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY HH:mm:ss A',
    defaultDate: new Date
  });

  $('#cancellationDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY HH:mm:ss A',
    defaultDate: new Date
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
    var fromDateValue = '';
    var toDateValue = '';

    if($('#fromDate').val()){
      var convert1 = $('#fromDate').val().replace(", ", " ");
      convert1 = convert1.replace(":", "/");
      convert1 = convert1.replace(":", "/");
      convert1 = convert1.replace(" ", "/");
      convert1 = convert1.replace(" pm", "");
      convert1 = convert1.replace(" am", "");
      convert1 = convert1.replace(" PM", "");
      convert1 = convert1.replace(" AM", "");
      var convert2 = convert1.split("/");
      var date  = new Date(convert2[2], convert2[1] - 1, convert2[0], convert2[3], convert2[4], convert2[5]);
      fromDateValue = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
    }
    
    if($('#toDate').val()){
      var convert3 = $('#toDate').val().replace(", ", " ");
      convert3 = convert3.replace(":", "/");
      convert3 = convert3.replace(":", "/");
      convert3 = convert3.replace(" ", "/");
      convert3 = convert3.replace(" pm", "");
      convert3 = convert3.replace(" am", "");
      convert3 = convert3.replace(" PM", "");
      convert3 = convert3.replace(" AM", "");
      var convert4 = convert3.split("/");
      var date2  = new Date(convert4[2], convert4[1] - 1, convert4[0], convert4[3], convert4[4], convert4[5]);
      toDateValue = date2.getFullYear() + "-" + (date2.getMonth() + 1) + "-" + date2.getDate() + " " + date2.getHours() + ":" + date2.getMinutes() + ":" + date2.getSeconds();
    }

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
          zones: zonesFilter,
          hypermarket: hypermarketFilter,
          outlets: outletsFilter
        } 
      },
      'columns': [
        { data: 'no' },
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
    $('#extendModal').find('#booking_date').val(date.toLocaleString('en-AU', { hour12: false }));
    $('#extendModal').find('#delivery_date').val(date.toLocaleString('en-AU', { hour12: false }));
    $('#extendModal').find('#cancellation_date').val(date.toLocaleString('en-AU', { hour12: false }));
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
    $('#extendModal').find('#need_grn').val("No");
    $('#extendModal').find('#loadingTime').val("M");
    $('#extendModal').find('#direct_store').val("");
    $('#extendModal').find('#outlets').attr('required', true);
    $('#extendModal').find('#outlets').show();
    $('#extendModal').find("#direct_store").hide();
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
});

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
    returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div></div></div></div>';
  }
  /*else if(row.status == 'Loaded'){
    returnString +='<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="delivered('+row.id+
  ')"><i class="fas fa-truck"></i></button></div></div></div></div>';
  }
  else if(row.status == 'Delivered'){
    returnString +='<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="invoice('+row.id+
  ')"><i class="fas fa-receipt"></i></button></div></div></div></div>';
  }*/
  
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
      $('#extendModal').find('#booking_date').val(obj.message.booking_date.toLocaleString('en-AU', { hour12: false }));
      $('#extendModal').find('#delivery_date').val(obj.message.delivery_date.toLocaleString('en-AU', { hour12: false }));
      $('#extendModal').find('#cancellation_date').val(obj.message.cancellation_date.toLocaleString('en-AU', { hour12: false }));
      $('#extendModal').find('#customerNo').val(obj.message.customer);
      $('#extendModal').find('#hypermarket').val(obj.message.hypermarket);
      $('#extendModal').find('#states').val(obj.message.states);
      $('#extendModal').find('#states').trigger('change');
      $('#extendModal').find('#zones').val(obj.message.zone);
      $('#extendModal').find('#hypermarket').trigger('change');
      $('#extendModal').find('#do_type').val(obj.message.do_type);
      $('#extendModal').find('#do_no').val(obj.message.do_number);
      $('#extendModal').find('#po_no').val(obj.message.po_number);
      $('#extendModal').find('#description').val(obj.message.note);
      $('#extendModal').find('#actual_ctn').val(obj.message.actual_carton);
      $('#extendModal').find('#need_grn').val(obj.message.need_grn);
      $('#extendModal').find('#loadingTime').val(obj.message.loading_time);

      if(obj.message.hypermarket == '0'){
        $('#extendModal').find('#outlets').empty().val(obj.message.outlet);
        $('#extendModal').find('#outlets').attr('required', false);
        $('#extendModal').find('#direct_store').attr('required', true);
        $('#extendModal').find('#direct_store').val(obj.message.direct_store);
        $('#extendModal').find('#outlets').hide();
        $('#extendModal').find("#direct_store").show();
        //$('#extendModal').find('.select2-container').show();
      }
      else{
        $('#extendModal').find('#zones').empty().val(obj.message.zone);
        $('#extendModal').find('#outlets').attr('required', true);
        $('#extendModal').find('#outlets').show();
        $('#extendModal').find('#direct_store').val('');
        $('#extendModal').find("#direct_store").hide();
        //$('#extendModal').find('.select2-container').hide();
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