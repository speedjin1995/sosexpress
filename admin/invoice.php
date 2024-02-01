<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $role = $_SESSION['role'];
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $customers = $db->query("SELECT * FROM customers WHERE deleted = '0'");
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Invoices</h1>
			</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-9"></div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addPurchase">Create New Invoices</button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table id="tableforPurchase" class="table table-bordered table-striped">
            <thead>
                <tr>
                  <th>Invoice Info</th>
                  <th>Pricing</th>
                  <th>Actions</th>
                </tr>
              </thead>
            </table>
          </div><!-- /.card-body -->
        </div>
      </div>
    </div>
  </div>
</section><!-- /.content -->

<div class="modal fade" id="purchaseModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form role="form" id="purchaseForm">
        <div class="modal-header">
          <h4 class="modal-title">Create Invoices</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="card card-primary">
              <div class="card-body">
                <!--<input type="hidden" class="form-control" id="id" name="id">
                <input type="hidden" class="form-control" id="purchaseId" name="purchaseId">--->
                <div class="row">
                  <h4>General Informations</h4>
                </div>
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label for="inputJobNo">Invoice Number</label>
                      <input type="text" class="form-control" id="inputInvNo" name="inputInvNo" placeholder="<new>" readonly>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="form-group">
                      <label for="inputJobNo">Customer *</label>
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
                      <label>Date</label>
                      <div class="input-group date" id="inputDate" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" id="inputDate" name="inputDate" data-target="#inputDate" />
                        <div class="input-group-append" data-target="#inputDate" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="card card-primary">
              <div class="card-body">
                <div class="row">
                  <h4>Details</h4>
                  <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-row">Add Item</button>
                </div>
                <table style="width: 100%;">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Item</th>
                      <th>Price</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody class="TableId" name="TableId" id="TableId"></tbody>
                  <tfoot><th colspan="2">Total</th><th><input type="text" class="form-control" id="totalAmount" name="totalAmount" placeholder="0.00" readonly></th></tfoot>
                </table>
              </div>
            </div>
          </div><!-- /.container-fluid -->
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="submit" id="submitPurchase">Save Change</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>


<script type="text/html" id="addContentsPurchase">
  <tr class="details">
    <td><input id="purchaseId" type="text" class="form-control purchaseItemRow" readonly></td>
    <td><input id="itemName" type="text" class="form-control purchaseItemRow" required></td>
    <td><input type="number" class="form-control purchaseItemRow" id="itemPrice" name="itemPrice" placeholder="Enter Price" required/></td>
    <td><button class="btn btn-danger btn-sm  purchaseItemRow" id="remove"><i class="fa fa-times"></i></button></td>
  </tr>
</script>

<script>
var contentIndex = 0;
var size = $("#TableId").find(".details").length;
var jobId = "";
var jobStatus = "";

$(function () {
  var table = $("#tableforPurchase").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'searching': false,
    'serverMethod': 'post',
    'ordering': false,
    'ajax': {
      'url':'php/loadInvoices.php'
    },
    'columns': [
      { 
        data: 'invoice_id',
        render: function ( data, type, row ) {
          return simplyShowId(row);
        }
      },
      { 
        data: 'invoice_id',
        render: function ( data, type, row ) {
          return details(row);
        }
      },
      { 
        data: 'invoice_id',
        render: function ( data, type, row ) {
          <?php
            if($role == 'ADMIN'){
              echo 'return simplyShowCreatedDatetime(row);';
            }
            else{
              echo 'return simplyShowCreatedDatetime2(row);';
            }
          ?>  
        }
      }
    ]       
  });

  $('[data-mask]').inputmask();

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#purchaseModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/invoice.php', $('#purchaseForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#purchaseModal').modal('hide');
            toastr["success"](obj.message, "Success:");
            $('#tableforPurchase').DataTable().ajax.reload();
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

  $(".search").click(function(){
    $('#spinnerLoading').show();

    var jobId = $('#purchaseModal').find('#inputJobNo').val();

    $('#purchaseModal').find('#search').attr("disabled", "disabled");
  

    $.post('php/getPurchases.php', {jobId: jobId}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){

      obj.message = obj.message.replace("\\\"", "\"");
      var items = JSON.parse(obj.message);


      for(var i=0; i<items.length; i++) {
        var $addContents = $("#addContentsPurchase").clone();
        $("#TableId").append($addContents.html());

        $("#TableId").find('.details:last').attr("id", "detail" + size);
        $("#TableId").find('.details:last').attr("data-index", size);
        $("#TableId").find('#remove:last').attr("id", "remove" + size);

        $("#TableId").find('#purchaseId:last').attr('name', 'purchaseId['+size+']').attr("id", "purchaseId" + size).val((size+1).toString());
        $("#TableId").find('#itemName:last').val(items[i].extraChargesName);
        $("#TableId").find('#itemName:last').attr('name', 'itemName['+size+']').attr("id", "itemName" + size);
        $("#TableId").find('#itemPrice:last').attr('name', 'itemPrice['+size+']').attr("id", "itemPrice" + size);

        size++;
      }
    }
    else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
    }
    else{
        toastr["error"]("Something wrong when activate", "Failed:");
    }
    
    });
    
    $('#spinnerLoading').hide();
  });

  $(".add-row").click(function(){
    var $addContents = $("#addContentsPurchase").clone();
    $("#TableId").append($addContents.html());

    $("#TableId").find('.details:last').attr("id", "detail" + size);
    $("#TableId").find('.details:last').attr("data-index", size);
    $("#TableId").find('#remove:last').attr("id", "remove" + size);

    $("#TableId").find('#purchaseId:last').attr('name', 'purchaseId['+size+']').attr("id", "purchaseId" + size).val((size+1).toString());
    $("#TableId").find('#itemName:last').attr('name', 'itemName['+size+']').attr("id", "itemName" + size);
    $("#TableId").find('#itemPrice:last').attr('name', 'itemPrice['+size+']').attr("id", "itemPrice" + size);

    size++;
  });

  $('#addPurchase').on('click', function(){
    size=0;
    $('#purchaseModal').find('#inputDate').val(new Date);
    $('#purchaseModal').find('#customerNo').val("");
    $('#purchaseModal').find('#totalAmount').val("0.00")
    $('#purchaseModal').modal('show');
    $("#purchaseModal").find('.purchaseItemRow').remove();
    
    $('#purchaseForm').validate({
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

  $('#inputDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'YYYY-MM-DD HH:mm:ss',
    defaultDate: new Date
  });

  $("#TableId").on('click', 'button[id^="remove"]', function () {
    var index = $(this).parents('.details').attr('data-index');
    size--;
    //$("#TableId").append('<input type="hidden" name="deleted[]" value="'+index+'"/>');
    $(this).parents('.details').remove();
  });

  $("#TableId").on('change', 'input[id^="itemPrice"]', function () {
    var index = $(this).val();
    var total = $('#totalAmount').val();
    var total = parseFloat(total) + parseFloat(index);
    $('#totalAmount').val(parseFloat(total).toFixed(2));
  });
});

function simplyShowId(row) {
  //var weightData = JSON.parse(row.route);
  var returnString = '<div class="row"><div class="col-12">' + row.invoice_no + '</div></div><br>';
  returnString += '<div class="row"><div class="col-12">Customer Name: ' + row.customer_name 
  + '</div></div><div class="row"><div class="col-12">Created By: '+ row.name 
  + '</div></div><div class="row"><div class="col-12">Created Datetime: ' + row.created_datetime 
  + '</div></div>';

  return returnString;
}

function simplyShowCreatedDatetime(row) {
  //var weightData = JSON.parse(row.route);
  var returnString = '<div class="row"><div class="col-12">'+row.created_datetime+'</div></div><br>';

  returnString += '<p><small>Action:</small></p>';

  returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="printQuote('+row.id+
  ')"><i class="fas fa-print"></i></button></div><div class="col-3"><button type="button" onclick="cancel('+
  row.id+')" class="btn btn-danger btn-sm"><i class="fas fa fa-times"></i></button></div></div>';

  return returnString;
}

function simplyShowCreatedDatetime2(row) {
  //var weightData = JSON.parse(row.route);
  var returnString = '<div class="row"><div class="col-12">'+row.created_datetime+'</div></div><br>';

  returnString += '<p><small>Action:</small></p>';

  returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="printQuote('+row.id+
  ')"><i class="fas fa-print"></i></button></div></div>';

  return returnString;
}

function details(row) {
  var returnString = "";
  returnString += '<div class="row"><div class="col-8">Items</div><div class="col-4">Amount</div></div><hr>';

  //var itemsData = JSON.parse();

  for(var i=0; i<row.cart.length; i++){
    returnString += '<div class="row"><div class="col-8">' + row.cart[i].items + '</div><div class="col-4">' + parseFloat(row.cart[i].amount).toFixed(2)  + '</div></div>';
  }

  returnString += '<hr><div class="row"><div class="col-8">Total Amount</div><div class="col-4">' + parseFloat(row.total_amount).toFixed(2) + '</div></div><hr>';

  return returnString;
}

function cancel(id) {
  $('#spinnerLoading').show();
  $.post('php/cancelPurchases.php', {purchasesID: id}, function(data){
    var obj = JSON.parse(data); 
    
    if(obj.status === 'success'){
      toastr["success"](obj.message, "Success:");
      $('#tableforPurchase').DataTable().ajax.reload();
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

function printQuote(id) {
  $('#spinnerLoading').show();
  $.post('php/generateReportPurchases.php', {purchasesID: id}, function(data){
    var obj = JSON.parse(data); 
    
    if(obj.status === 'success'){
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
      toastr["error"]("Something wrong when edit", "Failed:");
    }

    $('#spinnerLoading').hide();
  });
}
</script>