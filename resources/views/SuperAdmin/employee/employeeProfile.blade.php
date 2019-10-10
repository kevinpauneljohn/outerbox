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
@endsection

@section('main_content')
    <div class="row">
        <div class="col-lg-3">
            <div class="box">
                <div class="box-body">

                    <div class="row">
                            <div align="center"><img src="http://outerbox.biz/tmc/vendor/adminLTE/img/default-avatar.png" class="img-circle" alt="User" style="max-height:70px;margin:15px;"></div>
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
                                    <td><b>Role</b> &nbsp;</td>
                                    <td>{{ucfirst($user->roles[0]->name)}}</td>
                                </tr>
                                <tr>
                                    <td><b>Last seen online </b>&nbsp;</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Department</b>&nbsp; </td>
                                    <td></td>
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

    <script src="{{asset('/js/employeeProfile.js')}}"></script>
    <script src="{{asset('/js/activity.js')}}"></script>

    <script>
        $(function () {
            $('#agents-list').DataTable({
                'lengthChange': false
            })
        })
    </script>
@endsection