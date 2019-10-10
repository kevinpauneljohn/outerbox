@extends('layouts.admin_template')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Super Admin | Activity Logs
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('/bower_components/select2/dist/css/select2.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Activity Logs Push
@endsection
@section('variable_menu')
    <li><a href="{{url('/super-admin/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
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
    <li><a href="{{url('/super-admin/callCenter')}}"><i class="fa fa-phone-square"></i> <span>Call Center</span></a></li>
    <li><a href="{{url('/super-admin/lgu')}}"><i class="fa fa-bank"></i> <span>LGUs</span></a></li>
    <li class="active"><a href="{{url('/super-admin/activity')}}"><i class="fa fa-list"></i> <span>Activity</span></a></li>
@endsection
@section('main_content')
    <div class="box">
        <div class="box-body">
            <table id="activities" class="table table-bordered table-hover">
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
    <!-- /.box -->

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

    <script src="{{asset('/js/activity.js')}}"></script>

    <script>
        $(function () {
            $('#activities').DataTable({
            })
        })
    </script>
@endsection
