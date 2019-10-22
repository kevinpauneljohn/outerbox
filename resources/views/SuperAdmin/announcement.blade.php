@extends('layouts.admin_template')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Super Admin | Announcement
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Announcement
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
    <div class="box">
        <div class="box-body">
            <table id="announcement" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="10%">Date Created</th>
                    <th width="10%">Title</th>
                    <th width="10%">Status</th>
                    <th width="10%">LGU</th>
                    <th width="10%">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($announcements as $announcement)
                    <tr>
                        <td>{{$announcement->created_at}}</td>
                        <td>{{ucfirst($announcement->title)}}</td>
                        <td>{!! $status->announcementStatus($announcement->status) !!}</td>
                        <td>
                            @foreach(\App\User::find($announcement->user_id)->lgus as $lgu)
                                {{$lgu->station_name}}
                                @endforeach
                        </td>
                        <td>
                            <button class="btn btn-success view-announcement-detail" title="View" data-toggle="modal" data-target="#view-announcement" value="{{$announcement->id}}"><i class="fa fa-eye"></i></button>

                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th width="10%">Date Created</th>
                    <th width="10%">Title</th>
                    <th width="10%">Status</th>
                    <th width="10%">LGU</th>
                    <th width="10%">Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    {{--View Announcement--}}
    <div class="modal fade" id="view-announcement">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h3 class="announcement-title"></h3>
                </div>

                <div class="modal-body announcement-description">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{--End of View Announcement--}}

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

    <script src="{{asset('/js/announcement.js')}}"></script>

    <script>
        $(function () {
            $('#announcement').DataTable({
            });

            $('.textarea').wysihtml5();
        })
    </script>
@endsection