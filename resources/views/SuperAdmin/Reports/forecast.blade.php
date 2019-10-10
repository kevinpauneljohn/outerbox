@extends('layouts.admin_template')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Super Admin | Forecast Report
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('/bower_components/select2/dist/css/select2.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Forecast Report
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
    <li class="active treeview">
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

<div class="col-sm-6">
        <div class="box box-primary">
                <div class="box-header with-border">

                    <div class="box-tools pull-right">
                        <button type="button" id="collapse2" class="btn btn-box-tool" data-toggle="collapse"
                                data-target="#collapseTwo"><i id="toggler2" class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div id="collapseTwo" class="panel-collapse">
                    <div class="box-body">
                            <table id="activities" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="10%">Users</th>
                                        <th width="10%">Role</th>
                                        <th>Emergency Details</th>
                                        <th width="10%">Date</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>

                                </table>
                    </div>
             </div>
        </div>

</div>
<div class="col-sm-6">

    <div class="col-lg-12">
        <!-- Info Boxes Style 2 -->
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="ion ion-ios-book-outline"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"> {{ __('Report Generated') }} </span>
                <span class="info-box-number">0</span>

                <div class="progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                  <span class="progress-description">
                    0% {{ __('Completed') }}
                  </span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>

    <div class="col-lg-12">
        <div class="box"> <!-- Info Boxes Style 2 -->
            <div class="box-body">
                <div style="width: 50%; display: inline !important;">
                    {!!Form::submit("Export to PDF")->danger()!!}
                    {!!Form::submit("Export to Excel")->success()!!}
                    {!!Form::date('start_date', 'Start Date')!!}{!!Form::date('end_date', 'End Date')!!}
                    {!!Form::submit("Filter Date")!!}

                </div>
            </div> <!-- /.info-box-content -->
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

    </script>
@endsection
