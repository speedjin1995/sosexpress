<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.html";</script>';
}
else{
    $id = $_SESSION['userID'];
    $stmt = $db->prepare("SELECT * from customers where id = ?");
	$stmt->bind_param('s', $id);
	$stmt->execute();
	$result = $stmt->get_result();
    $customer_name = '';
    $short_name = '';
    $reg_no = '';
    $pic = '';
    $customer_address = '';
    $customer_phone = '';
    $email = '';
	
	if(($row = $result->fetch_assoc()) !== null){
        $customer_name = $row['customer_name'];
        $short_name = $row['short_name'];
        $reg_no = $row['reg_no'];
        $pic = $row['pic'];
        $customer_address = $row['customer_address'];
        $customer_phone = $row['customer_phone'];
        $email = $row['email'];
    }
}
?>

<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Company Profile</h1>
			</div>
		</div>
	</div>
</section>

<section class="content" style="min-height:700px;">
	<div class="card">
		<form role="form" id="profileForm" novalidate="novalidate">
			<div class="card-body">
                <input type="hidden" class="form-control" id="id" name="id" value="<?=$id ?>">
                <div class="form-group col-6">
                    <label for="name">Customer Name *</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Customer Name" value="<?=$customer_name ?>" required>
                </div>
                <div class="form-group col-6">
                    <label for="name">Short Name </label>
                    <input type="text" class="form-control" name="shortname" id="shortname" value="<?=$short_name ?>" placeholder="Enter Customer Short Name">
                </div>
                <div class="form-group col-6">
                    <label for="name">Registration No. *</label>
                    <input type="text" class="form-control" name="reg_no" id="reg_no" value="<?=$reg_no ?>" placeholder="Enter Customer Registration No." required>
                </div>
                <div class="form-group col-6">
                    <label for="name">P.I.C.</label>
                    <input type="text" class="form-control" name="pic" id="pic" value="<?=$pic ?>" placeholder="Enter Person In Charge">
                </div>
                <div class="form-group col-12"> 
                    <label for="address">Address *</label>
                    <textarea class="form-control" id="address" name="address" value="<?=$customer_address ?>" placeholder="Enter your address" required></textarea>
                </div>
                <div class="form-group col-6">
                    <label for="phone">Phone *</label>
                    <input type="text" class="form-control" name="phone" id="phone" value="<?=$customer_phone ?>" placeholder="01x-xxxxxxx" required>
                </div>
                <div class="form-group col-6"> 
                    <label for="email">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?=$email ?>" placeholder="Enter your email" required>
                </div>
			</div>
			
			<div class="card-footer">
				<button class="btn btn-success" id="saveProfile"><i class="fas fa-save"></i> Save</button>
			</div>
		</form>
	</div>
</section>

<script>
$(function () {
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/updateCompany.php', $('#profileForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    toastr["success"](obj.message, "Success:");
                    
                    $.get('company.php', function(data) {
                        $('#mainContents').html(data);
                        $('#spinnerLoading').hide();
                    });
        		}
        		else if(obj.status === 'failed'){
        		    toastr["error"](obj.message, "Failed:");
                    $('#spinnerLoading').hide();
                }
        		else{
        			toastr["error"]("Failed to update profile", "Failed:");
                    $('#spinnerLoading').hide();
        		}
            });
        }
    });
    
    $('#profileForm').validate({
        rules: {
            text: {
                required: true
            }
        },
        messages: {
            text: {
                required: "Please fill in this field"
            }
        },
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
</script>