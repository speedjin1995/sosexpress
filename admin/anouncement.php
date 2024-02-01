
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Anouncement</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Anouncement</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div><!-- /.content-header -->

    <!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"></h3>
                <button type="button" class="btn btn-block btn-primary btn-sm" id="addBlog" style="width: 10%;float: right;">Add</button>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <!--th>Chinese Title</th-->
                        <th>Created Date</th>
                        <th>Action</th>
                    </tr>
                  </thead>
                </table>
              </div><!-- /.card-body -->
            </div><!-- /.card -->
          </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section><!-- /.content -->
  
<div class="modal fade" id="blogModal" style="overflow: auto;">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="blogForm" method="post" >
            <div class="modal-header">
              <h4 class="modal-title">Anouncement Details</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <input type="hidden" class="form-control" id="blogId" name="blogId">
              </div>
              <div class="form-group">
                <label for="keyCode">Title *</label>
                <input class="form-control" name="engTitle" id="engTitle" placeholder="Message Key Code" required>
              </div>
              <!--div class="form-group">
                <label for="keyCode">中文主题 *</label>
                <input class="form-control" name="chTitle" id="chTitle" placeholder="Message Key Code" required>
              </div-->
              <div class="form-group">
                <label for="engBlog">Content</label>
                <textarea class="form-control"  id="engBlog" name="engBlog" placeholder="Place some text here"></textarea>
              </div>
              <!--div class="form-group"> 
                <label for="chineseBlog">中文内容</label>
                <textarea class="form-control" id="chineseBlog" name="chineseBlog" placeholder="Place some text here"></textarea>
              </div-->
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit" id="submitBlog">Submit</button>
            </div>
        </form>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.blog-modal-->

<script>
$(function () {
    $("#example1").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 1, 'asc' ]],
        'columnDefs': [ { orderable: false, targets: [0] }],
        'ajax': {
            'url':'php/loadBlog.php'
        },
        'columns': [
            { data: 'counter' },
            { data: 'title_en' },
            { data: 'created_datetime' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deletes('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                }
            }
        ]       
    });

    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/blog.php', $('#blogForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#blogModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#example1').DataTable().ajax.reload();
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
    
    $('#addBlog').on('click', function(){
        $('#blogModal').find('#blogId').val('');
        $('#blogModal').find('#engTitle').val('');
        $('#blogModal').find('#engBlog').val('');
        $('#blogModal').modal('show');
        
        $('#blogForm').validate({
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
    $.post( "php/getblog.php", { messageId: id}, function( data ) {
        var decode = JSON.parse(data)
        
        if(decode.status === 'success'){
            $('#blogModal').find('#blogId').val(decode.message.id);
            $('#blogModal').find('#engTitle').val(decode.message.title_en);
            $('#blogModal').find('#engBlog').val(decode.message.content_en);
            $('#blogModal').modal('show');
            
            $('#blogForm').validate({
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
    });
}

function deletes(id){
    $('#spinnerLoading').show();
    $.post( "php/deleteblog.php", { messageId: id}, function( data ) {
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
            toastr["success"](obj.message, "Success:");
            $('#example1').DataTable().ajax.reload();
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
</script>
</body>
</html>
