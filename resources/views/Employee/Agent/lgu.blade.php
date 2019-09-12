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
    <div class="box">
        <div class="box-body">
            <table id="lgu-list" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="8%">Date Registered</th>
                    <th width="8%">Station Name</th>
                    <th width="10%">Department</th>
                    <th width="20%">Address</th>
                    <th width="8%">Contact Person</th>
                    <th width="8%">Contact No.</th>
                    <th width="13%">Action</th>
                </tr>
                </thead>
                <tbody>
                @if($lgus->count() > 0)
                    @foreach($lgus->get() as $lgu)
                        <tr>
                            <td>{{$lgu->created_at}}</td>
                            <td>{{$lgu->station_name}}</td>
                            <td>{{$lgu->department}}</td>
                            <td>
                                {{ucfirst($lgu->address).', '}}
                                {{ucfirst(\App\Http\Controllers\address\AddressController::cityName($lgu->city).', ')}}
                                {{ucfirst(\App\Http\Controllers\address\AddressController::provinceName($lgu->province))}}
                            </td>
                            <td>{{ucfirst($lgu->contactname)}}</td>
                            <td>{{$lgu->contactno}}</td>
                            <td>
                                <a href="{{url('/agent/lgu/profile/'.$lgu->lgu_id)}}"> <button type="button" class="btn btn-default"><i class="fa fa-eye"></i></button></a>
                                <button type="button" class="btn btn-primary"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                <button type="button" class="btn btn-warning"><i class="fa fa-phone"></i></button>
                                <button type="button" class="btn btn-success"><i class="fa fa-comment"></i></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
                <tfoot>
                <tr>
                    <th>Date Registered</th>
                    <th>Station Name</th>
                    <th>Department</th>
                    <th>Address</th>
                    <th>Contact Person</th>
                    <th>Contact No.</th>
                    <th>Action</th>
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

    <script src="{{asset('/js/ticket.js')}}"></script>

    <script>
        $(function () {
            $('#lgu-list').DataTable()
        })
    </script>
@endsection