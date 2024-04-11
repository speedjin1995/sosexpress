<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['custID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['custID'];
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

<script>
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
          return simplyShowCreatedDatetime2(row);
        }
      }
    ]       
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