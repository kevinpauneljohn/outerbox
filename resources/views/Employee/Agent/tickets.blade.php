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
                    <th width="10%">Location of Incident</th>
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
                        <td>{{\App\Http\Controllers\AgentPageController::get_app_response($ticket->app_response)}}</td>
                        <td><button type="button" name="select_lgu" class="btn bg-aqua" data-toggle="modal" data-target="#select-lgu" value="{{$ticket->id}}">{{(!empty($ticket->station_name)) ? $ticket->station_name : 'Select LGU'}}</button></td>
{{--                        <td>{{$ticket->date_reported}}</td>--}}
                        <td>{{\App\Http\Controllers\AgentPageController::get_requested_date($ticket->app_response)}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <select name="status" id="{{$ticket->id}}">
                                <option value="{{$ticket->status}}">{{$ticket->status}}</option>
                                @if($ticket->status != 'Pending')
                                    <option value="Pending">Pending</option>
                                @endif
                                @if($ticket->status != 'On-going')
                                    <option value="On-going">On-going</option>
                                    @endif
                                @if($ticket->status != 'Prank')
                                    <option value="Prank">Prank</option>
                                    @endif
                                @if($ticket->status != 'Completed')
                                    <option value="Completed">Completed</option>
                                    @endif

                            </select>
                        </td>
                        <td>
                            <a href="{{url('/call')}}"><button type="button" class="btn btn-primary" value="{{$ticket->id}}"><i class="fa fa-phone"></i></button></a>
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

    <div class="modal fade" id="select-lgu">
        <div class="modal-dialog">
            <form method="post" id="lgu-form">
                @csrf
                <input type="hidden" name="ticket_id" id="ticket_id"/>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Assign LGU</h4>
                    </div>

                    <div class="modal-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>LGUs</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($lgus as $lgu)
                                <tr>
                                    <td>{{$lgu->station_name}}</td>
                                    <td>
                                        {{ucfirst($lgu->address).', '}}
                                        {{ucfirst(\App\Http\Controllers\address\AddressController::cityName($lgu->city).', ')}}
                                        {{ucfirst(\App\Http\Controllers\address\AddressController::provinceName($lgu->province))}}
                                    </td>
                                    <td>
                                        <button name="chosen_lgu" type="submit" class="btn btn-info" value="{{$lgu->id}}">Select</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-purple"><i class="fa fa-check"></i> Save</button>
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

    <script src="{{asset('/js/ticket.js')}}"></script>

    <script>
        $(function () {
            $('#ticket-list').DataTable()
        })
    </script>
@endsection