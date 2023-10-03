<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $pricing_type = $db->query("SELECT * FROM pricing_type WHERE deleted = '0'");
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Customers</h1>
			</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
        <div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
              <div class="row">
                  <div class="col-9"></div>
                  <div class="col-3">
                      <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addCustomers">Add Customers</button>
                  </div>
              </div>
          </div>
					<div class="card-body">
						<table id="customerTable" class="table table-bordered table-striped">
							<thead>
								<tr>
                                    <th>Code</th>
									<th>Name</th>
									<th>Address</th>
									<th>Phone</th>
									<th>Email</th>
									<th>Actions</th>
								</tr>
							</thead>
						</table>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</section><!-- /.content -->

<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="customerForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Customers</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="card-body">
                <input type="hidden" class="form-control" id="id" name="id">
                <div class="row">
                    <div class="form-group col-6">
                        <label for="code">Username *</label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Enter User NAme" required>
                    </div>
                    <div class="form-group col-6">
                        <label for="code">Customer Code *</label>
                        <input type="text" class="form-control" name="code" id="code" placeholder="Enter Customer Code" required>
                    </div>
                    <div class="form-group col-6">
                        <label for="name">Customer Name *</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Customer Name" required>
                    </div>
                    <div class="form-group col-6">
                        <label for="name">Short Name </label>
                        <input type="text" class="form-control" name="shortname" id="shortname" placeholder="Enter Customer Short Name">
                    </div>
                    <div class="form-group col-6">
                        <label for="name">Registration No. *</label>
                        <input type="text" class="form-control" name="reg_no" id="reg_no" placeholder="Enter Customer Registration No." required>
                    </div>
                    <div class="form-group col-6">
                        <label for="name">P.I.C.</label>
                        <input type="text" class="form-control" name="pic" id="pic" placeholder="Enter Person In Charge">
                    </div>
                    <div class="form-group col-12"> 
                        <label for="address">Address *</label>
                        <textarea class="form-control" id="address" name="address" placeholder="Enter your address" required></textarea>
                    </div>
                    <div class="form-group col-6">
                        <label for="phone">Phone *</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="01x-xxxxxxx" required>
                    </div>
                    <div class="form-group col-6"> 
                        <label for="email">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group col-6"> 
                        <label for="payment_term">Payment Term *</label>
                        <select class="form-control" style="width: 100%;" id="payment_term" name="payment_term" required>
                            <option selected="selected">-</option>
                            <option value="Walk In">Walk In</option>
                            <option value="Cash">Cash</option>
                            <option value="Days">Days</option>
                        </select>
                    </div>
                    <div class="form-group col-6"> 
                        <label for="term">Terms</label>
                        <input type="text" class="form-control" id="term" name="term" placeholder="Payment Terms">
                    </div>
                    <div class="form-group col-6"> 
                        <label for="email">Rate *</label>
                        <select class="form-control" style="width: 100%;" id="rate" name="rate" required>
                            <option value="Fixed" selected="selected">Fixed</option>
                            <option value="Floating">Floating</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                  <h4>Branches</h4>
                  <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-branch">Add Branch</button>
                </div>
                <table style="width: 100%;">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Address</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody id="branchTable"></tbody>
                </table>
                <div class="row">
                  <h4>Pricing</h4>
                  <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-price">Add Price</button>
                </div>
                <table style="width: 100%;">
                  <thead>
                    <tr>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody id="pricingTable"></tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit" id="submitMember">Submit</button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="branchModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="unitForm">
            <div class="modal-header">
              <h4 class="modal-title">Edit Branch</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
    					<input type="hidden" class="form-control" id="id" name="id">
    				</div>
    				<div class="form-group">
    					<label for="units">Name *</label>
    					<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" required>
    				</div>
                    <div class="form-group">
    					<label for="desc">Address *</label>
                        <textarea class="form-control" id="address" name="address" placeholder="Enter your address" required></textarea>
    				</div>
    			</div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit" id="submitLot">Submit</button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script type="text/html" id="branchDetails">
  <tr class="details">
    <td>
        <input id="branch_name" type="text" class="form-control" placeholder="Enter ..." required>
    </td>
    <td>
        <textarea class="form-control" id="branch_Address" placeholder="Enter your address" required></textarea>
    </td>
    <td><button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button></td>
  </tr>
</script>

<script type="text/html" id="pricingDetails">
  <tr class="details">
    <td>
        <select class="form-control" style="width: 100%;" id="type" name="type" required>
            <?php while($row3=mysqli_fetch_assoc($pricing_type)){ ?>
                <option value="<?=$row3['type'] ?>"><?=$row3['type'] ?></option>
            <?php } ?>
        </select>
    </td>
    <td>
        <input id="size" type="text" class="form-control" placeholder="Enter ..." required>
    </td>
    <td>
        <input id="price" type="number" class="form-control" placeholder="Enter ..." required>
    </td>
    <td><button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button></td>
  </tr>
</script>

<script>
var contentIndex = 0;
var branchCount = $("#branchTable").find(".details").length;
var pricingCount = $("#pricingTable").find(".details").length;

$(function () {
    var table = $("#customerTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/loadCustomers.php'
        },
        'columns': [
            { data: 'customer_code' },
            { data: 'customer_name' },
            { data: 'customer_address' },
            { data: 'customer_phone' },
            { data: 'customer_email' },
            /*{ 
                data: 'id',
                render: function ( data, type, row ) {
                    return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
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
            $('td', row).css('background-color', '#E6E6FA');
        },        
    });

    $('#customerTable tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            row.child( format(row.data()) ).show();tr.addClass("shown");
        }
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            if($('#addModal').hasClass('show')){
                $.post('php/customers.php', $('#customerForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    
                    if(obj.status === 'success'){
                        $('#addModal').modal('hide');
                        toastr["success"](obj.message, "Success:");
                        
                        $.get('customers.php', function(data) {
                            $('#mainContents').html(data);
                            $('#spinnerLoading').hide();
                        });
                    }
                    else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                        $('#spinnerLoading').hide();
                    }
                    else{
                        toastr["error"]("Something wrong when edit", "Failed:");
                        $('#spinnerLoading').hide();
                    }
                });
            }
            else{
                $.post('php/branch.php', $('#unitForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    
                    if(obj.status === 'success'){
                        $('#addModal').modal('hide');
                        toastr["success"](obj.message, "Success:");
                        
                        $.get('customers.php', function(data) {
                            $('#mainContents').html(data);
                            $('#spinnerLoading').hide();
                        });
                    }
                    else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                        $('#spinnerLoading').hide();
                    }
                    else{
                        toastr["error"]("Something wrong when edit", "Failed:");
                        $('#spinnerLoading').hide();
                    }
                });
            }
        }
    });

    $('#addCustomers').on('click', function(){
        $('#addModal').find('#id').val("");
        $('#addModal').find('#username').val("");
        $('#addModal').find('#code').val("");
        $('#addModal').find('#name').val("");
        $('#addModal').find('#shortname').val("");
        $('#addModal').find('#reg_no').val("");
        $('#addModal').find('#pic').val("");
        $('#addModal').find('#address').val("");
        $('#addModal').find('#phone').val("");
        $('#addModal').find('#email').val("");
        $('#addModal').find('#payment_term').val("");
        $('#addModal').find('#term').val("");
        $('#addModal').find('#rate').val("");
        $('#addModal').find('#branchTable').html('');
        $('#addModal').find('#pricingTable').html('');
        branchCount = 0;
        pricingCount = 0;
        $('#addModal').modal('show');
        
        $('#customerForm').validate({
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

    $('#payment_term').on('change', function(){
        if($(this).val() == 'Days'){
            $('#term').attr("required", true);
        }
        else{
            $('#term').attr("required", false);
        }
    });

    $(".add-branch").click(function(){
        var $addContents = $("#branchDetails").clone();
        $("#branchTable").append($addContents.html());

        $("#branchTable").find('.details:last').attr("id", "detail" + branchCount);
        $("#branchTable").find('.details:last').attr("data-index", branchCount);
        $("#branchTable").find('#remove:last').attr("id", "remove" + branchCount);

        $("#branchTable").find('#branch_name:last').attr('name', 'branch_name['+branchCount+']').attr("id", "branch_name" + branchCount);
        $("#branchTable").find('#branch_Address:last').attr('name', 'branch_Address['+branchCount+']').attr("id", "branch_Address" + branchCount);
        
        branchCount++;
    });

    $("#branchTable").on('click', 'button[id^="remove"]', function () {
        var index = $(this).parents('.details').attr('data-index');
        $("#branchTable").append('<input type="hidden" name="deletedBranch[]" value="'+index+'"/>');
        branchCount--;
        $(this).parents('.details').remove();
    });

    $(".add-price").click(function(){
        var $addContents = $("#pricingDetails").clone();
        $("#pricingTable").append($addContents.html());

        $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
        $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
        $("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);

        $("#pricingTable").find('#type:last').attr('name', 'type['+pricingCount+']').attr("id", "type" + pricingCount);
        $("#pricingTable").find('#size:last').attr('name', 'size['+pricingCount+']').attr("id", "size" + pricingCount);
        $("#pricingTable").find('#price:last').attr('name', 'price['+pricingCount+']').attr("id", "price" + pricingCount);

        pricingCount++;
    });

    $("#pricingTable").on('click', 'button[id^="remove"]', function () {
        var index = $(this).parents('.details').attr('data-index');
        $("#pricingTable").append('<input type="hidden" name="deletedShip[]" value="'+index+'"/>');
        pricingCount--;
        $(this).parents('.details').remove();
    });
});

function format (row) {
  var returnString = '<div class="row"><div class="col-md-3"><p>Company Name: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Short Name: '+row.short_name+
  '</p></div><div class="col-md-3"><p>B.R. Number: '+row.reg_no+
  '</p></div><div class="col-md-3"><p>P.I.C: '+row.pic+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Payment Terms: '+row.payment_term+
  '</p></div><div class="col-md-3"><p>Terms: '+row.payment_details+
  '</p></div><div class="col-md-3"><p>Rate: '+row.rate+
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div></div></div>';

  if(row.branches != null){
    returnString += '<p>Branch</p><table style="width: 100%;"><thead><tr><th>Name</th><th>Address</th><th>Action</th></tr></thead><tbody>'
    
    for(var i=0; i<row.branches.length; i++){
        returnString += '<tr><td>'+row.branches[i].name+'</td><td>'+
        row.branches[i].address+'</td><td><div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="editBranch('+row.branches[i].id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" onclick="deactivateBranch('+row.branches[i].id+
  ')"><i class="fas fa-trash"></i></button></div></div></td></tr>';
    }
    
    returnString += '</tbody></table>';
  }

  if(row.pricing != null){
    returnString += '<p>Pricing</p><table style="width: 100%;"><thead><tr><th>Type</th><th>Size</th><th>Price</th></tr></thead><tbody>'
    
    for(var i=0; i<row.pricing.length; i++){
        returnString += '<tr><td>'+row.pricing[i].type+'</td><td>'+row.pricing[i].size+'</td><td>'+row.pricing[i].price+'</td></tr>';
    }
    
    returnString += '</tbody></table>';
  }

  return returnString;
}

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getCustomer.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#code').val(obj.message.customer_code);
            $('#addModal').find('#name').val(obj.message.customer_name);
            $('#addModal').find('#address').val(obj.message.customer_address);
            $('#addModal').find('#phone').val(obj.message.customer_phone);
            $('#addModal').find('#email').val(obj.message.customer_email);
            $('#addModal').modal('show');
            
            $('#customerForm').validate({
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
            toastr["error"]("Something wrong when activate", "Failed:");
        }
        $('#spinnerLoading').hide();
    });
}

function deactivate(id){
    $('#spinnerLoading').show();
    $.post('php/deleteCustomer.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            toastr["success"](obj.message, "Success:");
            $.get('customers.php', function(data) {
                $('#mainContents').html(data);
                $('#spinnerLoading').hide();
            });
        }
        else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
            $('#spinnerLoading').hide();
        }
        else{
            toastr["error"]("Something wrong when activate", "Failed:");
            $('#spinnerLoading').hide();
        }
    });
}

function editBranch(id){
    $('#spinnerLoading').show();
    $.post('php/getBranch.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#branchModal').find('#id').val(obj.message.id);
            $('#branchModal').find('#name').val(obj.message.name);
            $('#branchModal').find('#address').val(obj.message.address);
            $('#branchModal').modal('show');
            
            $('#unitForm').validate({
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
            toastr["error"]("Something wrong when activate", "Failed:");
        }
        $('#spinnerLoading').hide();
    });
}

function deactivateBranch(id){
    $('#spinnerLoading').show();
    $.post('php/deleteBranch.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            toastr["success"](obj.message, "Success:");
            $.get('customers.php', function(data) {
                $('#mainContents').html(data);
                $('#spinnerLoading').hide();
            });
        }
        else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
            $('#spinnerLoading').hide();
        }
        else{
            toastr["error"]("Something wrong when activate", "Failed:");
            $('#spinnerLoading').hide();
        }
    });
}
</script>