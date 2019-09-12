@extends('layouts.agentDashboardTemplate')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Agent | LGU
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    LGU
@endsection
@section('main_content')

{{--    Lgu Profile Details--}}
    <div class="box">
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="8%">Date Registered</th>
                        <th width="8%">Station Name</th>
                        <th width="10%">Department</th>
                        <th width="20%">Address</th>
                        <th width="8%">Contact Person</th>
                        <th width="8%">Contact No.</th>
                    </tr>
                <tbody>
                    <tr>
                        <td>{{$lguDetails->created_at}}</td>
                        <td>{{$lguDetails->station_name}}</td>
                        <td>{{$lguDetails->department}}</td>
                        <td>
                            {{ucfirst($lguDetails->address).', '}}
                            {{ucfirst(\App\Http\Controllers\address\AddressController::cityName($lguDetails->city).', ')}}
                            {{ucfirst(\App\Http\Controllers\address\AddressController::provinceName($lguDetails->province))}}
                        </td>
                        <td>{{$lguDetails->fullname}}</td>
                        <td>{{$lguDetails->contactno}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    {{--lgu profile details end--}}

    {{--start lgu ticket handled--}}
<div class="box">
    <div class="box-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th width="8%">Ticket #</th>
                <th width="8%">Parent Ticket</th>
                <th width="10%">Location of Incident</th>
                <th>Date Reported</th>
                <th>Time Handled</th>
                <th>Time Reached</th>
                <th>Duration Before Agent Handled the case</th>
                <th>Call Duration</th>
                <th>Duration until the Agent transfer the request to LGU</th>
                <th>Duration Accepted by the LGU</th>
                <th>Duration Of Response To The Site</th>
                <th>Status</th>
                <th width="15%">Action</th>
            </tr>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
    {{--end lgu ticket handled--}}
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

    <script src="{{asset('/js/ticket.js')}}"></script>

    <script>
        $(function () {
            $('#lgu-list').DataTable()
        })
    </script>
@endsection