@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Companies Lists</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a class="btn btn-success mb-4" href="javascript:void(0)" id="createNewCompanies"> Create New Companies</a>
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th width="280px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>



                    <div class="modal fade" id="ajaxModel" aria-hidden="true">
                      <div class="modal-dialog">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h4 class="modal-title" id="modelHeading"></h4>
                              </div>

                              @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                              @endif
                              <div class="modal-body">
                                  <ul class="error_val" style="color: red;"></ul>
                                  <form id="companiesForm" method="post" name="companiesForm" class="form-horizontal" enctype="multipart/form-data">
                                     <input type="hidden" name="companies_id" id="companies_id">
                                      <div class="form-group">
                                          <label for="name" class="col-sm-4 control-label">Name</label>
                                          <div class="col-sm-12">
                                              <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                                          </div>
                                      </div>
                       
                                      <div class="form-group">
                                          <label class="col-sm-4 control-label">Email</label>
                                          <div class="col-sm-12">
                                              <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" value="" maxlength="50" required="">
                                          </div>
                                      </div>

                                      <div class="form-group">
                                          <label class="col-sm-4 control-label">Password</label>
                                          <div class="col-sm-12">
                                              <input type="text" class="form-control" id="password" name="password" placeholder="Enter Password" value="" maxlength="250" required="">
                                          </div>
                                      </div>

                                      <div class="form-group">
                                          <label class="col-sm-4 control-label">Website</label>
                                          <div class="col-sm-12">
                                              <input type="text" class="form-control" id="website" name="website" placeholder="Enter Website" value="" maxlength="50" required="">
                                          </div>
                                      </div>

                                      <div class="form-group">
                                          <label class="col-sm-4 control-label">Profile</label>
                                          <div class="col-sm-12">
                                              <input type="file" class="form-control" id="logo" accept="image/*" name="logo">
                                              <span class="img_error" style="color: red;"></span>
                                          </div>
                                      </div>
                        
                                      <div class="col-sm-offset-2 col-sm-10">
                                       <button type="button" class="btn btn-primary" id="saveBtn" value="create">Save changes
                                       </button>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>
                    </div>




                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  $(function () {
     
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('companies.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
     
    $('#createNewCompanies').click(function () {
        $('#saveBtn').val("create-companies");
        $('#companies_id').val('');
        $('#companiesForm').trigger("reset");
        $('#modelHeading').html("Create New Companies");
        $('.error_val').html('');
        $('#ajaxModel').modal('show');
    });
    
    $('body').on('click', '.editCompanies', function () {
      $('.error_val').html('');
      var companies_id = $(this).data('id');
      $.get("{{ route('companies.index') }}" +'/' + companies_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Companies");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');
          $('#companies_id').val(data.id);
          $('#name').val(data.name);
          $('#email').val(data.email);
          $('#website').val(data.website);
      })
    });
    var _URL = window.URL || window.webkitURL;
    $("#logo").change(function (e) {
        var file, img;
        if ((file = this.files[0])) {
            img = new Image();
            img.onload = function () {
                //alert(this.width + " " + this.height);
                if(this.width <= 100 &&  this.height <= 100){
                  $('.img_error').text('');
                  $('#saveBtn').prop('disabled', false);
                }else{
                  $('#saveBtn').prop('disabled', true);
                  $('.img_error').text('please upload proper image');
                }
            };
            img.src = _URL.createObjectURL(file);
        }
    });

    
    $('#saveBtn').click(function (e) {
        e.preventDefault();
        // $(this).html('Sending..');    
        var formData = new FormData();
        formData.append( 'name', $('#name').val());
        formData.append( 'email', $('#email').val());
        formData.append( 'password', $('#password').val());
        formData.append( 'website', $('#website').val());
        formData.append( 'logo', $('#logo')[0].files[0]);

        $.ajax({
            // data: new FormData(this),
            type: "POST",
            url: "{{ route('companies.store') }}",
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            success: function (data) {

                if(data.status == '200'){
                    $('#companiesForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    table.draw();
                }else{
                  var errors = data.errors;
                  var html = '';
                  $.each(errors, function(key,val) {
                      html += '<li>'+val+'</li>';
                  });

                  $('.error_val').html(html);
                }
           
            },
            error: function (data) {
                console.log('Error:', data);
                $('#saveBtn').html('Save Changes');
            }
        });
    });
    
    $('body').on('click', '.deleteCompanies', function () {
     
        var companies_id = $(this).data("id");
        confirm("Are You sure want to delete !");
      
        $.ajax({
            type: "DELETE",
            url: "{{ route('companies.store') }}"+'/'+companies_id,
            success: function (data) {
                table.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
     
  });
</script>
@endsection