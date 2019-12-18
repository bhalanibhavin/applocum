@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Employees Lists</div>

                <div class="card-body">
                    <a class="btn btn-success mb-4" href="javascript:void(0)" id="createNewEmployees"> Create New Employees</a>
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
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
                                  <form id="employeesForm" name="employeesForm" class="form-horizontal">
                                     <input type="hidden" name="employees_id" id="employees_id">
                                      
                                      <div class="form-group">
                                          <label for="name" class="col-sm-4 control-label">Full Name</label>
                                          <div class="col-sm-12">
                                              <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Enter Full Name" value="" maxlength="50" required="">
                                          </div>
                                      </div>

                                      <div class="form-group">
                                          <label for="name" class="col-sm-4 control-label">Companies</label>
                                          <div class="col-sm-12">
                                              <select name="company_id" id="company_id" class="form-control" required="required">
                                                  <option value="">--- Select Company ---</option>
                                                  @if(Auth::guard('company')->check())
                                                  <option value="{{ Auth::guard('company')->user()->id }}" >{{ Auth::guard('company')->user()->name }}</option>
                                                  @else
                                                    @foreach ($companies as $key => $value)
                                                        <option value="{{ $key }}" >{{ $value }}</option>
                                                    @endforeach
                                                  @endif
                                              </select>
                                          </div>
                                      </div>
                       
                                      <div class="form-group">
                                          <label class="col-sm-4 control-label">Email</label>
                                          <div class="col-sm-12">
                                              <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" value="" maxlength="50" required="">
                                          </div>
                                      </div>

                                      <div class="form-group">
                                          <label class="col-sm-4 control-label">Phone</label>
                                          <div class="col-sm-12">
                                              <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone" value="" maxlength="50" required="">
                                          </div>
                                      </div>
                        
                                      <div class="col-sm-offset-2 col-sm-10">
                                       <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
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
        ajax: "{{ route('employees.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'full_name', name: 'full_name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
     
    $('#createNewEmployees').click(function () {
        $('#saveBtn').val("create-employees");
        $('#employees_id').val('');
        $('#employeesForm').trigger("reset");
        $('#modelHeading').html("Create New Employees");
        $('.error_val').html('');
        $('#employeesForm').trigger("reset");
        $('#ajaxModel').modal('show');
    });
    
    $('body').on('click', '.editEmployees', function () {
      $('.error_val').html('');
      var employees_id = $(this).data('id');
      $.get("{{ route('employees.index') }}" +'/' + employees_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Employees");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');
          $('#employees_id').val(data.id);
          $('#full_name').val(data.full_name);
          $('#email').val(data.email);
          $('#phone').val(data.phone);
          $('#company_id').val(data.company_id);
      })
   });
    
    $('#saveBtn').click(function (e) {
        e.preventDefault();
        // $(this).html('Sending..');
        $.ajax({
          data: $('#employeesForm').serialize(),
          url: "{{ route('employees.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
              // console.log(data);
              if(data.status == '200'){
                  $('#employeesForm').trigger("reset");
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
    
    $('body').on('click', '.deleteEmployees', function () {
     
        var employees_id = $(this).data("id");
        confirm("Are You sure want to delete !");
      
        $.ajax({
            type: "DELETE",
            url: "{{ route('employees.store') }}"+'/'+employees_id,
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