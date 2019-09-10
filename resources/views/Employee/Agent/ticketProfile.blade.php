@extends('layouts.agentDashboardTemplate')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Agent | Leads
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Ticket <button type="button" class="btn bg-purple" data-toggle="modal" data-target="#create-ticket"><i class="fa fa-plus"></i> Add New</button>
@endsection
@section('main_content')
    <div class="box">
        <div class="box-body">
            <table cellspacing="100">
                <tr>
                    <td>Assigned Agent</td>
                    <td>: {{ucfirst($agent->firstname)}} {{ucfirst($agent->lastname)}}</td>
                </tr>
                <tr>
                    <td>Username</td>
                    <td>: {{$agent->username}}</td>
                </tr>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    <div class="row">

        <div>

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

    <script src="{{asset('/js/ticket.js')}}"></script>

    <script>
        $(function () {
            $('#ticket-list').DataTable()
        })
    </script>
@endsection