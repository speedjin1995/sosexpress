<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['custID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.html";</script>';
}
else{
  $todayStart = date('Y-m-d 00:00:00', strtotime('today'));
  $user = $_SESSION['custID'];
  $branch = $db->query("SELECT * FROM branch WHERE customer_id = '".$user."' AND deleted = '0'");
  $hypermarket = $db->query("SELECT * FROM hypermarket WHERE deleted = '0'");
  $states = $db->query("SELECT * FROM states WHERE deleted = '0'");
  $zones = $db->query("SELECT * FROM zones WHERE deleted = '0'");
  $zones2 = $db->query("SELECT * FROM zones WHERE deleted = '0'");
  $outlet = $db->query("SELECT * FROM outlet WHERE deleted = '0'");
  $address = $db->query("SELECT pickup_address FROM customers WHERE id = '".$user."'");
  $holiday = $db->query("SELECT * FROM holidays WHERE start_date <= '".$todayStart."' AND end_date >= '".$todayStart."' AND deleted = '0'");
}
?>

<style>
  .large-wording {
    font-size: 24px; /* Adjust the font size as needed */
    font-weight: bold; /* Make the text bold */
    color: #333; /* Set the text color as needed */
  }
</style>

<select class="form-control" style="width: 100%;" id="zoneHidden" style="display: none;">
  <option value="" selected disabled hidden>Please Select</option>
  <?php while($row3=mysqli_fetch_assoc($zones)){ ?>
    <option value="<?=$row3['id'] ?>" data-index="<?=$row3['states'] ?>"><?=$row3['zones'] ?></option>
  <?php } ?>
</select>

<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Request</h1>
			</div>
		</div>
	</div>
</section>

<section class="content" style="min-height:700px;">
    <form role="form" id="profileForm" novalidate="novalidate">
	    <div class="card">
        <div class="card-header">
          <h4 class="m-0 text-dark">Booking</h4>
        </div>
        <div class="card-body">
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
                    <label>Pickup Address *</label>
                    <textarea class="form-control" id="address" name="address" placeholder="Enter your address"><?php if($rowA=mysqli_fetch_assoc($address)){echo $rowA['pickup_address'];} ?></textarea>
                </div>
              </div>
              <div class="col-4">
                  <div class="form-group">
                      <label>Description</label>
                      <textarea class="form-control" id="description" name="description" placeholder="Enter your description"></textarea>
                  </div>
              </div>
          </div>
          <div class="row">
            <div class="form-group col-4">
                <label>Extimated Ctn *</label>
                <input class="form-control" type="number" placeholder="Extimated Carton" id="extimated_ctn" name="extimated_ctn" min="0" required/>                        
            </div>
          </div>
        </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <h4>DO Requests</h4>
                    <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-row">Add DO</button>
                </div>
            </div>
        <div class="card-body" id="TableId"></div>
        </div>
        <button class="btn btn-success" id="saveProfile"><i class="fas fa-save"></i> Save</button>
    </form>
    <div class="card" id="errorCard" style="width: 300px; margin: auto; margin-top: 50px; text-align: center; padding: 20px;">
      <div class="large-wording">
        You are not allow to make any booking after 3pm. Please contact our admin to make booking.
      </div>
    </div>
    <?php
      if($rowH=mysqli_fetch_assoc($holiday)){
        echo '<div class="card" id="holidayCard" style="width: 300px; margin: auto; margin-top: 50px; text-align: center; padding: 20px;">
        <div class="large-wording">
          We are in '.$rowH['holiday'].' from '.substr($rowH['start_date'], 0, 10).' until '.substr($rowH['end_date'], 0, 10).'. Please make order after this.
        </div>
      </div>';
      }
    ?>
</section>

<script type="text/html" id="addContents">
<div class="row details">
  <div class="col-4">
    <div class="form-group">
      <label>Booking Date *</label>
        <div class='input-group date' id="bookingDate" data-target-input="nearest">
          <input type='text' class="form-control datetimepicker-input" data-target="#bookingDate" id="booking_date" name="booking_date" required/>
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
          <input type='text' class="form-control datetimepicker-input" data-target="#deliveryDate" id="delivery_date" name="delivery_date" required/>
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
          <input type='text' class="form-control datetimepicker-input" data-target="#cancellationDate" id="cancellation_date" name="cancellation_date" required/>
          <div class="input-group-append" data-target="#cancellationDate" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
          </div>
        </div>
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
      <!--select class="js-data-example-ajax" id="direct_store" name="direct_store"></select-->
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
  <div class="col-4">
    <div class="form-group">
      <label>Need GRN *</label>
      <select class="form-control" id="need_grn" name="need_grn" required>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>
    </div>
  </div>
  <div class="col-9">
    <div class="form-group">
      <label class="labelStatus">Notes</label>
      <textarea class="form-control" id="description" name="description" placeholder="Enter your description"></textarea>
    </div>
  </div>
  <div class="col-3">
    <button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button>
  </div>
</div>  
</script>

<script>
var size = $("#TableId").find(".details").length

$(function () {
    $('#zoneHidden').hide();

    var currentTime = moment();
    var threePm = moment().set({ hour: 15, minute: 0, second: 0, millisecond: 0 });

    if ($('#holidayCard').length) {
      // Show the error card
      $('#profileForm').hide();
      $('#errorCard').hide();
    }
    else{
      if (currentTime.isBefore(threePm)) {
        // Show the form
        $('#profileForm').show();
        $('#errorCard').hide();
      } else {
        // Show the error card
        $('#profileForm').hide();
        $('#errorCard').show();
      }
    }
    

    $('#bookingDate').datetimepicker({
        icons: { time: 'far fa-clock' },
        format: 'DD/MM/YYYY HH:mm:ss A',
        defaultDate: new Date
    });

    $.validator.setDefaults({
        submitHandler: function () {
            //$('#spinnerLoading').show();
            $.post('php/updateProfile.php', $('#profileForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    toastr["success"](obj.message, "Success:");
                    window.location.reload();
        		}
        		else if(obj.status === 'failed'){
        		    toastr["error"](obj.message, "Failed:");
                    //$('#spinnerLoading').hide();
                }
        		else{
        			toastr["error"]("Failed to update profile", "Failed:");
                    //$('#spinnerLoading').hide();
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

    $(".add-row").click(function(){
        var $addContents = $("#addContents").clone();
        $("#TableId").append($addContents.html());

        $("#TableId").find('.details:last').attr("id", "detail" + size);
        $("#TableId").find('.details:last').attr("data-index", size);
        $("#TableId").find('#remove:last').attr("id", "remove" + size);

        $("#TableId").find('#booking_date:last').attr('name', 'booking_date['+size+']').attr("id", "booking_date" + size).attr("data-target", "#bookingDate" + size);
        $("#TableId").find('#delivery_date:last').attr('name', 'delivery_date['+size+']').attr("id", "delivery_date" + size).attr("data-target", "#deliveryDate" + size);
        $("#TableId").find('#cancellation_date:last').attr('name', 'cancellation_date['+size+']').attr("id", "cancellation_date" + size).attr("data-target", "#cancellationDate" + size);
        $("#TableId").find('#hypermarket:last').attr('name', 'hypermarket['+size+']').attr("id", "hypermarket" + size);
        $("#TableId").find('#states:last').attr('name', 'states['+size+']').attr("id", "states" + size);
        $("#TableId").find('#zones:last').attr('name', 'zones['+size+']').attr("id", "zones" + size);
        $("#TableId").find('#outlets:last').attr('name', 'outlets['+size+']').attr("id", "outlets" + size);
        $("#TableId").find('#do_type:last').attr('name', 'do_type['+size+']').attr("id", "do_type" + size);
        $("#TableId").find('#do_no:last').attr('name', 'do_no['+size+']').attr("id", "do_no" + size);
        $("#TableId").find('#po_no:last').attr('name', 'po_no['+size+']').attr("id", "po_no" + size);
        $("#TableId").find('#actual_ctn:last').attr('name', 'actual_ctn['+size+']').attr("id", "actual_ctn" + size);
        $("#TableId").find('#need_grn:last').attr('name', 'need_grn['+size+']').attr("id", "need_grn" + size);
        $("#TableId").find('#description:last').attr('name', 'description['+size+']').attr("id", "description" + size);
        
        $("#TableId").find('#bookingDate:last').attr("id", "bookingDate" + size);
        $("#TableId").find("#bookingDate" + size).find('.input-group-append').attr("data-target", "#bookingDate" + size);
        $("#TableId").find('#deliveryDate:last').attr("id", "deliveryDate" + size);
        $("#TableId").find("#deliveryDate" + size).find('.input-group-append').attr("data-target", "#deliveryDate" + size);
        $("#TableId").find('#cancellationDate:last').attr("id", "cancellationDate" + size);
        $("#TableId").find("#cancellationDate" + size).find('.input-group-append').attr("data-target", "#cancellationDate" + size);

        $("#bookingDate" + size).datetimepicker({
            icons: { time: 'far fa-clock' },
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        $("#deliveryDate" + size).datetimepicker({
            icons: { time: 'far fa-clock' },
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        $("#cancellationDate" + size).datetimepicker({
            icons: { time: 'far fa-clock' },
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        size++;
    });

    $("#TableId").on('change', 'select[id^="do_type"]', function(){
      if($(this).val() == 'Consignment'){
        $(this).parents('.details').find('input[id^="do_no"]').val('Consignment');
        $(this).parents('.details').find('input[id^="po_no"]').val('Consignment');
      }
      else if($(this).val() == 'Non-trade'){
        $(this).parents('.details').find('input[id^="do_no"]').val('Non-trade');
        $(this).parents('.details').find('input[id^="po_no"]').val('Non-trade');
      }
      else{
        $(this).parents('.details').find('input[id^="do_no"]').val('');
        $(this).parents('.details').find('input[id^="po_no"]').val('');
      }
    });

    $("#TableId").on('change', 'select[id^="states"]', function(){
        $('#zones').empty();
        var dataIndexToMatch = $(this).val();
        var zones = $(this).parents('.details').find('select[id^="zones"]');

        $('#zoneHidden option').each(function() {
            var dataIndex = $(this).data('index');

            if (dataIndex == dataIndexToMatch) {
                zones.append($(this).clone());
                zones.trigger('change');
            }
        });

        if($(this).parents('.details').find('select[id^="states"]').val() && $(this).parents('.details').find('select[id^="zones"]').val() && $(this).parents('.details').find('select[id^="hypermarket"]').val()){
            $(this).parents('.details').find('select[id^="outlets"]').empty();
            $(this).parents('.details').find('select[id^="outlets"]').attr('required', true);
            $(this).parents('.details').find('select[id^="outlets"]').show();
            var outlet = $(this).parents('.details').find('select[id^="outlets"]');

            $.post('php/listOutlets.php', {states: $(this).parents('.details').find('select[id^="states"]').val(), zones: $(this).parents('.details').find('select[id^="zones"]').val(), hypermarket: $(this).parents('.details').find('select[id^="hypermarket"]').val()}, function(data){
                var obj = JSON.parse(data);
                
                if(obj.status === 'success'){
                    for(var i=0; i<obj.message.length; i++){
                        outlet.append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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

    $("#TableId").on('change', 'select[id^="zones"]', function(){
        if($(this).parents('.details').find('select[id^="states"]').val() && $(this).parents('.details').find('select[id^="zones"]').val() && $(this).parents('.details').find('select[id^="hypermarket"]').val()){
            $(this).parents('.details').find('select[id^="outlets"]').empty();
            $(this).parents('.details').find('select[id^="outlets"]').attr('required', true);
            $(this).parents('.details').find('select[id^="outlets"]').show();
            var zones = $(this).parents('.details').find('select[id^="outlets"]');

            $.post('php/listOutlets.php', {states: $(this).parents('.details').find('select[id^="states"]').val(), zones: $(this).parents('.details').find('select[id^="zones"]').val(), hypermarket: $(this).parents('.details').find('select[id^="hypermarket"]').val()}, function(data){
                var obj = JSON.parse(data);
                
                if(obj.status === 'success'){
                    for(var i=0; i<obj.message.length; i++){
                        zones.append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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

    $("#TableId").on('change', 'select[id^="hypermarket"]', function(){
        if($(this).parents('.details').find('select[id^="states"]').val() && $(this).parents('.details').find('select[id^="zones"]').val() && $(this).parents('.details').find('select[id^="hypermarket"]').val()){
            $(this).parents('.details').find('select[id^="outlets"]').empty();
            $(this).parents('.details').find('select[id^="outlets"]').attr('required', true);
            $(this).parents('.details').find('select[id^="outlets"]').show();
            var zones = $(this).parents('.details').find('select[id^="outlets"]');

            $.post('php/listOutlets.php', {states: $(this).parents('.details').find('select[id^="states"]').val(), zones: $(this).parents('.details').find('select[id^="zones"]').val(), hypermarket: $(this).parents('.details').find('select[id^="hypermarket"]').val()}, function(data){
                var obj = JSON.parse(data);
                
                if(obj.status === 'success'){
                    for(var i=0; i<obj.message.length; i++){
                        zones.append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
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

    $("#TableId").on('click', 'button[id^="remove"]', function () {
        var index = $(this).parents('.details').attr('data-index');
        size--;
        $(this).parents('.details').remove();
    });
});
</script>