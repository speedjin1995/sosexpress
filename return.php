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
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
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
    <!--div class="row">
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
    </div-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-8">Booking</div>
              <div class="col-4">
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
                  <th>GR No.</th>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>Driver</th>
                  <th>Collection <br>Date</th>
                  <th>Collection <br>Type</th>
                  <th>Total <br>Carton</th>
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
              <label>Driver *</label>
              <input class="form-control" type="text" placeholder="Driver" id="driver" name="driver" required/>                        
            </div>
          </div>
          <div class="row">
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
                <label class="labelStatus">Collection Type *</label>
                <select class="form-control" id="collectionType" name="collectionType" required>
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
      { data: 'GR_No' },
      { data: 'return_date' },
      { data: 'customer_name' },
      { data: 'driver' },
      { data: 'collection_date' },
      { data: 'collection_type' },
      { data: 'total_carton' },
      { data: 'return_type' },
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
      icons: { time: 'far fa-calendar' },
      defaultDate: new Date
  });

  $('#toDatePicker').datetimepicker({
      icons: { time: 'far fa-calendar' },
      defaultDate: new Date
  });

  $('#returnDate').datetimepicker({
    icons: { time: 'far fa-calendar' },
    defaultDate: tomorrow,
    minDate: tomorrow
  });

  $('#collectionDate').datetimepicker({
    icons: { time: 'far fa-calendar' },
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
    }
  });

  $('#newReturn').on('click', function(){
    var date = new Date();

    $('#extendModal').find('#id').val("");
    $('#extendModal').find('#return_date').val(formatDate(date));
    $('#extendModal').find('#customerNo').val("");
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
        { data: 'description' },
        { data: 'estimated_ctn' },
        {
          data: 'actual_ctn',
          render: function (data, type, row) {
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
        returnString += '<td>' + row.return_details[i].price + '</td>';
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

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getReturn.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#return_date').val(obj.message.return_date);
      $('#extendModal').find('#customerNo').val(obj.message.customer);
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