@extends('layouts.admin_template')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Super Admin | Call Center | Profile
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Call Center Profile
@endsection
@section('variable_menu')
    <<li><a href="{{url('/super-admin/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
    <li><a href="{{url('employee')}}"><i class="fa fa-user-plus"></i> <span>Employee</span></a></li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-bank"></i><span>Roles</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            <ul class="treeview-menu">
                <li><a href="{{url('/super-admin/roles')}}">View Roles</a></li>
                <li><a href="/super-admin/permissions">View Permissions</a></li>
            </ul>
        </a>
    </li>
    <li class="active"><a href="{{url('/super-admin/callCenter')}}"><i class="fa fa-phone-square"></i> <span>Call Center</span></a></li>
    <li><a href="{{url('/super-admin/lgu')}}"><i class="fa fa-bank"></i> <span>LGUs</span></a></li>
@endsection

@section('main_content')
    <div class="row">
        <div class="col-lg-4">
            <div class="box">
                <div class="box-header">
                    <h2>{{ucfirst($callcenter['name'])}}</h2></div>
                <div class="box-body">
                    <b>Address:</b> {{ ucfirst($callcenter['street']).' '.$callcenter['city'].' '.$callcenter['state'].' '.$callcenter['postalcode'] }}
                </div>
            </div>

        </div>
        <div class="col-lg-4">
            <div class="box">
                <div class="box-header">
                    <h3>Date Started</h3></div>
                <div class="box-body">
                    {{ $callcenter['created_at']}}
                </div>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <h2>Agents</h2>
            <table id="agent-list" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="13%">Date Registered</th>
                    <th width="23%">Full Name</th>
                    <th>Username</th>
                    <th width="20%">Email</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                            <tr>
                                <td>{{$employee->created_at}}</td>
                                <td>{{ucfirst($employee->firstname).' '.ucfirst($employee->lastname)}}</td>
                                <td>{{$employee->username}}</td>
                                <td>{{$employee->email}}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>Date Registered</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('extra_script')
    <!-- DataTables -->
    <script src="{{asset('/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
    <!-- SlimScroll -->
    <script src="{{asset('/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>

    <!-- FastClick -->
    <script src="{{asset('/bower_components/fastclick/lib/fastclick.js')}}"></script>

    <script src="{{asset('/js/callcenter.js')}}"></script>

    <!-- growl notification -->
    <script src="{{asset('bower_components/remarkable-bootstrap-notify/bootstrap-notify.min.js')}}"></script>

    <script>
        $(function () {
            $('#agent-list').DataTable()
        })
    </script>
@endsection