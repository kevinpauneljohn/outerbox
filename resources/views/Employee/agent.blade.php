@extends('layouts.adminDashboardTemplate')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Admin | Agent
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Agent
@endsection
@section('main_content')
    <div class="box">
        <div class="box-body">
            <table id="agents-list" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="10%">Date Registered</th>
                    <th width="20%">Full Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Call Center</th>
                    <th width="15%">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td>{{$employee->created_at}}</td>
                        <td>
                            {{ucfirst($employee->firstname).' '.ucfirst($employee->lastname)}}
                        </td>
                        <td>{{$employee->email}}</td>
                        <td>{{$employee->username}}</td>
                        <td></td>
                        <td></td>
                        <td>
                            <a href="{{url('/employee/profile/'.$employee->id)}}"><button type="button" class="btn btn-success edit-btn" title="View"><i class="fa fa-eye"></i></button></a>
                            <button type="button" class="btn btn-primary edit-employee" title="Edit" data-toggle="modal" data-target="#edit_employee" value="{{$employee->id}}"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-danger delete-employee-btn" title="Delete" data-toggle="modal" data-target="#delete_employee" value="{{$employee->id}}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th width="10%">Date Registered</th>
                    <th width="20%">Full Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Call Center</th>
                    <th width="15%">Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection

@section('extra_script')
    <!-- DataTables -->
    <script src="{{asset('/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
    <!-- SlimScroll -->
    <script src="{{asset('/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>

    <!-- FastClick -->
    <script src="{{asset('/bower_components/fastclick/lib/fastclick.js')}}"></script>

    <!-- growl notification -->
    <script src="{{asset('bower_components/remarkable-bootstrap-notify/bootstrap-notify.min.js')}}"></script>

    <script src="{{asset('/js/employee.js')}}"></script>

    <script>
        $(function () {
            $('#agents-list').DataTable()
        })
    </script>
@endsection