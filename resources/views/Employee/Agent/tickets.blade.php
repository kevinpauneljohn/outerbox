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
            <table id="ticket-list" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="8%">Ticket #</th>
                    <th>Location of Incident</th>
                    <th>Station name</th>
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
                </thead>
                <tbody>
                @foreach($tickets as $ticket)
                    <tr>
                        <td>{{\App\Http\Controllers\Ticket\CreateTicketController::getSequence($ticket->id)}}</td>
                        <td>{{$ticket->app_response}}</td>
                        <td>{{$ticket->station_name}}</td>
                        <td>{{$ticket->date_reported}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><span class="label bg-{{\App\Http\Controllers\Ticket\CreateTicketController::get_status_label($ticket->status)}}">{{$ticket->status}}</span></td>
                        <td>
                            <a href="{{url('/call')}}"><button type="button" class="btn btn-primary"><i class="fa fa-phone"></i></button></a>
                            <button type="button" class="btn btn-success"><i class="fa fa-arrows-h"></i></button>
                            <button type="button" class="btn btn-danger"><i class="fa fa-warning"></i></button>
                            <button type="button" class="btn btn-warning"><i class="fa fa-user-times"></i></button>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
                <tfoot>
                <tr>
                    <th width="8%">Ticket #</th>
                    <th>Location of Incident</th>
                    <th>Station name</th>
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

    <script src="{{asset('/js/employee.js')}}"></script>

    <script>
        $(function () {
            $('#ticket-list').DataTable()
        })
    </script>
@endsection