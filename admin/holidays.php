<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Holidays</h1>
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
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addStatus">Add Holidays</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="statusTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Holiday Name</th>
									<th>Start</th>
                                    <th>End</th>
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

<div class="modal fade" id="statusModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="statusForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Holidays</h4>
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
    					<label for="status">Name *</label>
    					<input type="text" class="form-control" name="holiday" id="holiday" placeholder="Enter Name" required>
    				</div>
                    <div class="form-group">
    					<label for="prefix">Start Date *</label>
    					<div class='input-group date' id="startDate" data-target-input="nearest">
                            <input type='text' class="form-control datetimepicker-input" data-target="#startDate" id="start_date" name="startDate" required/>
                            <div class="input-group-append" data-target="#startDate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
    				</div>
                    <div class="form-group">
    					<label for="prefix">End Date *</label>
    					<div class='input-group date' id="endDate" data-target-input="nearest">
                            <input type='text' class="form-control datetimepicker-input" data-target="#endDate" id="end_date" name="endDate" required/>
                            <div class="input-group-append" data-target="#endDate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
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
    const today = new Date();

    $("#statusTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 1, 'asc' ]],
        'columnDefs': [ { orderable: false, targets: [0] }],
        'ajax': {
            'url':'php/loadHolidays.php'
        },
        'columns': [
            { data: 'holiday' },
            { data: 'start_date' },
            { data: 'end_date' },
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

    $('#startDate').datetimepicker({
      icons: { time: 'far fa-clock' },
      format: 'DD/MM/YYYY',
      minDate: new Date
    });

    $('#endDate').datetimepicker({
      icons: { time: 'far fa-clock' },
      format: 'DD/MM/YYYY',
      minDate: new Date
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/holidays.php', $('#statusForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#statusModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#statusTable').DataTable().ajax.reload();
                    $('#spinnerLoading').hide();
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

    $('#addStatus').on('click', function(){
        $('#statusModal').find('#id').val("");
        $('#statusModal').find('#holiday').val("");
        $('#statusModal').find('#start_date').val(formatDate2(today));
        $('#statusModal').find('#end_date').val(formatDate2(today));
        $('#statusModal').modal('show');
        
        $('#statusForm').validate({
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
    $.post('php/getHoliday.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#statusModal').find('#id').val(obj.message.id);
            $('#statusModal').find('#holiday').val(obj.message.holiday);
            $('#statusModal').find('#start_date').val(formatDate2(new Date(obj.message.start_date)));
            $('#statusModal').find('#end_date').val(formatDate2(new Date(obj.message.end_date)));
            $('#statusModal').modal('show');
            
            $('#statusForm').validate({
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
    if (confirm('Are you sure you want to delete this Holiday?')) {
        $('#spinnerLoading').show();
        $.post('php/deleteHoliday.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"](obj.message, "Success:");
                $('#statusTable').DataTable().ajax.reload();
                $('#spinnerLoading').hide();
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
}
</script>