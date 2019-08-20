@extends('layouts.adminDashboardTemplate')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Admin | Agent | Profile
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Agent Profile
@endsection

@section('main_content')
    <div class="row">
        <div class="col-lg-3">
            <div class="box">
                <div class="box-body">

                    <div class="row">
                        <div align="center"><img src="http://outerbox.biz/tmc/vendor/adminLTE/img/default-avatar.png" class="img-circle" alt="User" style="max-height:70px;margin:15px;"></div>
                        <p>
                        <table style="margin-left:30px !important;">
                            <tr>
                                <td><b>Date Registered </b>&nbsp;</td>
                                <td>: {{$user->created_at}}</td>
                            </tr>
                            <tr>
                                <td><b>Full Name </b>&nbsp;</td>
                                <td>: {{ucfirst($user->firstname)}} {{ ($user->middlename) ? ucfirst($user->middlename): ''}} {{ucfirst($user->lastname)}}</td>
                            </tr>
                            <tr>
                                <td><b>Username</b> &nbsp;</td>
                                <td>: {{$user->username}}</td>
                            </tr>
                            <tr>
                                <td><b>Role</b> &nbsp;</td>
                                <td>: {{ucfirst($user->roles[0]->name)}}</td>
                            </tr>
                            <tr>
                                <td><b>Last seen online </b>&nbsp;</td>
                                <td>: </td>
                            </tr>
                            <tr>
                                <td><b>Department</b>&nbsp; </td>
                                <td>: </td>
                            </tr>
                        </table>
                        </p>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <div class="box">
                <div class="box-body">
                    <h3 align="center">USER ACTIVITY</h3>
                </div>
                <!-- /.box-body -->
            </div>
        </div>

        <div class="col-lg-9">
            <div class="box">
                <div class="box-body">
                    <table id="agents-list" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th width="10%">Ticker #</th>
                            <th width="20%">Location Of Incident</th>
                            <th>Station Name</th>
                            <th>Date Reported</th>
                            <th>Time handled</th>
                            <th>Time Reached</th>
                            <th>Status</th>
                            <th width="15%">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                        <tr>
                            <th width="10%">Ticker #</th>
                            <th width="20%">Location Of Incident</th>
                            <th>Station Name</th>
                            <th>Date Reported</th>
                            <th>Time handled</th>
                            <th>Time Reached</th>
                            <th>Status</th>
                            <th width="15%">Action</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.box-body -->
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

    <script>
        $(function () {
            $('#agents-list').DataTable({
                'lengthChange': false
            })
        })
    </script>
@endsection