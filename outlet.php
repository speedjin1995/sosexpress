<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $states = $db->query("SELECT * FROM states WHERE deleted = '0'");
  $hypermarket = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
  $zones = $db->query("SELECT * FROM zones WHERE deleted = '0'");
}
?>
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
				<h1 class="m-0 text-dark">Outlets</h1>
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
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addUnits">Add Currency</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="unitTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No.</th>
									<th>Hypermarket</th>
                                    <th>Outlets</th>
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

<div class="modal fade" id="unitModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="unitForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Outlet</h4>
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
    					<input type="text" class="form-control" name="names" id="names" placeholder="Enter Outlet" required>
    				</div>
                    <div class="form-group">
    					<label for="desc">Hypermarket *</label>
    					<select class="form-control" style="width: 100%;" id="hypermarket" name="hypermarket" required>
                            <option selected="selected">-</option>
                            <?php while($row=mysqli_fetch_assoc($hypermarket)){ ?>
                                <option value="<?=$row['id'] ?>"><?=$row['name'] ?></option>
                            <?php } ?>
                        </select>
    				</div>
                    <div class="form-group">
    					<label for="rate">States *</label>
    					<select class="form-control" style="width: 100%;" id="states" name="states" required>
                            <option selected="selected">-</option>
                            <?php while($row2=mysqli_fetch_assoc($states)){ ?>
                                <option value="<?=$row2['id'] ?>"><?=$row2['states'] ?></option>
                            <?php } ?>
                        </select>
    				</div>
                    <div class="form-group">
    					<label for="rate">Zones *</label>
    					<select class="form-control" style="width: 100%;" id="zones" name="zones" required></select>
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

<script>
$(function () {
    $("#unitTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 1, 'asc' ]],
        'columnDefs': [ { orderable: false, targets: [0] }],
        'ajax': {
            'url':'php/loadOutlet.php'
        },
        'columns': [
            { data: 'counter' },
            { data: 'hypermarket' },
            { data: 'name' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                }
            }
        ],
        "rowCallback": function( row, data, index ) {
            $('td', row).css('background-color', '#E6E6FA');
        },
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/currency.php', $('#unitForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#unitModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    
                    $.get('currency.php', function(data) {
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
    });

    $('#addUnits').on('click', function(){
        $('#unitModal').find('#id').val("");
        $('#unitModal').find('#names').val("");
        $('#unitModal').find('#hypermarket').val("");
        $('#unitModal').find('#states').val("");
        $('#unitModal').find('#zones').val("");
        $('#unitModal').modal('show');
        
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
    });
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getOutlet.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#unitModal').find('#id').val(obj.message.id);
            $('#unitModal').find('#names').val(obj.message.name);
            $('#unitModal').find('#hypermarket').val(obj.message.hypermarket);
            $('#unitModal').find('#states').val(obj.message.states);
            $('#unitModal').find('#zones').val(obj.message.zones);
            $('#unitModal').modal('show');
            
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

function deactivate(id){
    $('#spinnerLoading').show();
    $.post('php/deleteOutlet.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            toastr["success"](obj.message, "Success:");
            $.get('outlet.php', function(data) {
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