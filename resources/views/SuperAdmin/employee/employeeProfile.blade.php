@extends('layouts.admin_template')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Super Admin | Employee | Profile
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
    <style type="text/css" rel="stylesheet">
        .edit-employee{
            border:none;
            background:none;
            visibility:hidden;
        }
    </style>
@endsection
@section('page_header')
    Employee Profile
@endsection
@section('variable_menu')
    <li><a href="{{url('/super-admin/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
    <li class="active"><a href="{{url('employee')}}"><i class="fa fa-user-plus"></i> <span>Employee</span></a></li>
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
    <li><a href="{{url('/super-admin/callCenter')}}"><i class="fa fa-phone-square"></i> <span>Call Center</span></a></li>
    <li><a href="{{url('/super-admin/lgu')}}"><i class="fa fa-bank"></i> <span>LGUs</span></a></li>
    <li><a href="{{url('/super-admin/activity')}}"><i class="fa fa-list"></i> <span>Activity</span></a></li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-pie-chart"></i><span>Reports</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            <ul class="treeview-menu">
                <li><a href="{{url('/super-admin/performance-eval')}}">Performance Evaluation</a></li>
                <li><a href="/super-admin/user-management">User Management</a></li>
                <li><a href="/super-admin/forecast">Forecast</a></li>
            </ul>
        </a>
    </li>
@endsection

@section('main_content')
    <div class="row">
        <div class="col-lg-3">
            <div class="box profile">
                <div class="box-header"><button class="edit-employee pull-right" data-toggle="modal" data-target="#edit_employee" value="{{$user->id}}"><small id="edit_profile" title="Edit Profile"><i class="fa fa-edit fa-2x"></i></small></button></div>
                <div class="box-body">

                    <div class="row">

                            <div align="center">
                                <img src="http://outerbox.biz/tmc/vendor/adminLTE/img/default-avatar.png" class="img-circle" alt="User" style="max-height:70px;margin:15px;">
                                <br/><small class="label bg-yellow">{{ucfirst($user->roles[0]->name)}}</small>
                            </div>
                            <p>
                            <table style="margin-left:30px !important;" class="table table-bordered">
                                <tr>
                                    <td><b>Date Registered </b>&nbsp;</td>
                                    <td>{{$user->created_at}}</td>
                                </tr>
                                <tr>
                                    <td><b>Full Name </b>&nbsp;</td>
                                    <td>{{ucfirst($user->firstname)}} {{ ($user->middlename) ? ucfirst($user->middlename): ''}} {{ucfirst($user->lastname)}}</td>
                                </tr>
                                <tr>
                                    <td><b>Username</b> &nbsp;</td>
                                    <td>{{$user->username}}</td>
                                </tr>
                                <tr>
                                    <td><b>Last seen online </b>&nbsp;</td>
                                    <td>{!!($active->count() > 0) ? '<small class="label bg-green">Online</small>' : '<small class="label bg-black">Offline</small>'!!}</td>
                                </tr>
                                <tr>
                                    <td><b>Call Center</b>&nbsp; </td>
                                    <td>{{$callCenterUser->name}}</td>
                                </tr>
                            </table>
                            </p>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>

        <div class="col-lg-9">
            <div class="box">
                <div class="box-body">
                    <h2>Activity Logs</h2>

                    <table id="agents-list" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th width="10%">Username</th>
                            <th width="10%">Role</th>
                            <th>Description</th>
                            <th width="10%">Time</th>
                            <th width="10%">Date</th>
                            <th width="10%">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($activities as $activity)
                            <tr>
                                <td>{{($activity->user_id == 0) ? "System" : \App\User::find($activity->user_id)->username}}</td>
                                {{--                            <td>{{!! $roles->get_role_names_with_label($activity->user_id !!}}</td>--}}
                                <td>{!! nl2br($roles->get_role_names_with_label($activity->user_id)) !!}</td>
                                <td>{{$activity->description}}</td>
                                <td>{{$dateTime->dateDiff($activity->created_at)}}</td>
                                <td>{{$activity->created_at}}</td>
                                <td><button class="btn btn-success view-log-details" title="View" data-toggle="modal" data-target="#view-logs" value="{{$activity->id}}"><i class="fa fa-eye"></i></button></td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th width="10%">Username</th>
                            <th width="10%">Role</th>
                            <th>Description</th>
                            <th width="10%">Time</th>
                            <th width="10%">Date</th>
                            <th width="10%">Action</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="box profile">
                <div class="box-header"><h3>Generate Report</h3></div>
                <div class="box-body">
                    <input type="hidden" name="userId" id="userId" value="{{$user->id}}">
                    <div class="form-group start_date">
                        <label for="start_date">Start date</label>
                        <input type="date" name="start_date" class="form-control" id="start_date">
                    </div>
                    <div class="form-group end_date">
                        <label for="end_date">End date</label>
                        <input type="date" name="end_date" class="form-control" id="end_date">
                    </div>
                    <button type="submit" class="btn bg-red-active generate-report" value="pdf">Export to PDF</button>
                    <button type="submit" class="btn bg-green-active generate-report" value="excel">Export to Excel</button>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>


    {{--this will display the activity logs--}}
    <div class="modal fade" id="view-logs">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Log Details</h4>
                </div>

                <div class="modal-body">
                    <div class="logs-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{--Edit Staff--}}
    <div class="modal fade" id="edit_employee">
        <div class="modal-dialog modal-lg">
            <form method="post" id="update-staff">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Update Employee Profile</h4>
                    </div>

                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="user_value" id="user_value"/>
                        <span id="change_status"></span>
                        <div class="form-group">
                            <div class="row">
                                <span class="col-lg-4">
                                    <div class="form-group">
                                        <div class="edit_firstname">
                                            <label for="edit_firstname">First name</label> <span class="required">*</span>
                                            <input type="text" name="edit_firstname" id="edit_firstname" class="form-control" value="{{$userDetails->first()->firstname}}"/>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-4">
                                    <div class="form-group">
                                        <div class="edit_middlename">
                                            <label for="edit_middlename">Middle name</label>
                                            <input type="text" name="edit_middlename" id="edit_middlename" class="form-control" value="{{$userDetails->first()->middlename}}"/>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-4">
                                    <div class="form-group">
                                        <div class="edit_lastname">
                                            <label for="edit_lastname">Last name</label><span class="required">*</span>
                                            <input type="text" name="edit_lastname" id="edit_lastname" class="form-control" value="{{$userDetails->first()->lastname}}"/>
                                        </div>
                                    </div>
                                </span>
                            </div>
                            <div class="row">
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="edit_email">
                                            <label for="edit_email">Email</label><span class="required">*</span>
                                            <input type="text" name="edit_email" id="edit_email" class="form-control" value="{{$userDetails->first()->email}}"/>
                                        </div>
                                    </div>
                                </span>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <div class="edit_role">
                                                <label for="edit_role">Select Role</label>
                                                <select name="edit_role" class="form-control role" id="edit_role">
                                                    <option></option>
                                                    @foreach($roleList as $role)
                                                        <option value="{{$role->name}}">{{ucfirst($role->name)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="old_role" id="old_role"/>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <div class="edit_callcenter">
{{--                                                <label for="edit_callcenter">Assign To Call Center</label>--}}
                                                <input type="hidden" name="edit_callcenter" id="edit_callcenter">
{{--                                                <select name="edit_callcenter" class="form-control" id="edit_callcenter">--}}
{{--                                                    <option></option>--}}
{{--                                                    --}}{{--@foreach($callcenters as $callcenter)--}}
{{--                                                        <option value="{{$callcenter->id}}">{{ucfirst($callcenter->name)}}</option>--}}
{{--                                                    @endforeach--}}
{{--                                                </select>--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-purple"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                </div>
            </form>
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

    <!-- growl notification -->
    <script src="{{asset('bower_components/remarkable-bootstrap-notify/bootstrap-notify.min.js')}}"></script>

    <script src="{{asset('/js/activity.js')}}"></script>
    <script src="{{asset('/js/employee.js')}}"></script>
    <script src="{{asset('/js/reports.js')}}"></script>

    <script>
        $(function () {
            $('#agents-list').DataTable({
                'lengthChange': false
            })
        })
    </script>
@endsection