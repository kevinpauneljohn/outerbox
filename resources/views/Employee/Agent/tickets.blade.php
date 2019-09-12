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
                    <th width="8%">Parent Ticket</th>
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
                        <td><a href="{{url('/ticket/'.$ticket->id)}}"> {{\App\Http\Controllers\Ticket\CreateTicketController::getSequence($ticket->id)}}</a></td>
                        <td><a href="{{url('/ticket/'.$ticket->id)}}"> {{\App\Http\Controllers\Ticket\TicketController::get_parent_ticket($ticket->id)}}</a></td>
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
{{--                            <a href="{{url('/v1/call-user')}}"><button type="button" class="btn btn-primary call_user" value="{{$ticket->id}}"><i class="fa fa-phone"></i></button></a>--}}
                            <input type="hidden" name="user_mobile_no{{$ticket->id}}" value="{{\App\Http\Controllers\AgentPageController::get_mobile_no($ticket->app_response)}}">
                            <button type="button" class="btn btn-primary call_user" value="{{$ticket->id}}" data-toggle="modal" data-target="#lead-details" title="Call User"><i class="fa fa-phone"></i></button>
                            <button type="button" class="btn btn-success connect_to_lgu" value="{{$ticket->lgu_id}}" title="Connect To LGU"><i class="fa fa-arrows-h"></i></button>
                            <button type="button" class="btn bg-aqua-active relate-ticket-btn" title="Create Child Ticket" data-toggle="modal" data-target="#create-child-ticket" value="{{$ticket->id}}"><i class="fa fa-ticket"></i></button>
                            <button type="button" class="btn bg-yellow-active twilio_recordings" title="Display Call Recordings" value="{{$ticket->id}}" data-toggle="modal" data-target="#display-call-recordings"><i class="fa fa-play"></i></button>
                            <button type="button" class="btn bg-aqua-active twilio_call_back" title="Create Child Ticket" value="{{$ticket->id}}"><i class="fa fa-ticket"></i></button>
{{--                            <button type="button" class="btn btn-warning"><i class="fa fa-user-times"></i></button>--}}
                        </td>
                    </tr>
                    @endforeach

                </tbody>
                <tfoot>
                <tr>
                    <th width="8%">Ticket #</th>
                    <th width="8%">Parent Ticket</th>
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


    {{--display lead details--}}
    <div class="modal fade" id="lead-details">
        <div class="modal-dialog">

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Lead Details</h4>
                    </div>

                    <div class="modal-body">
                        <table class="table table-bordered table-hover" id="lead-info-table">
                            <tr>
                                <td width="25%">Full Name</td>
                                <td id="fullname"></td>
                            </tr>
                            <tr>
                                <td>Contact No.</td>
                                <td id="mobile_no"></td>
                            </tr>
                            <tr>
                                <td>Incident Location</td>
                                <td id="address"></td>
                            </tr>
                            <tr>
                                <td>Date Reported</td>
                                <td id="request_date"></td>
                            </tr>
                            <tr>
                                <td>Type of accident</td>
                                <td id="type_of_accident"></td>
                            </tr>
                            <tr>
                                <td>Latitude</td>
                                <td id="lat"></td>
                            </tr>
                            <tr>
                                <td>Longitude</td>
                                <td id="lang"></td>
                            </tr>
                            <tr>
                                <td>Contact Person</td>
                                <td id="emergency_contact"></td>
                            </tr>
                            <tr>
                                <td>Contact Person Number</td>
                                <td id="contact_no"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>

        </div>
    </div>


    {{--create child ticket modal--}}
    <div class="modal fade" id="create-child-ticket">
        <div class="modal-dialog">
            <form id="child-ticket-form">
                @csrf
                <input type="hidden" name="ticketId"/>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Relate This Ticket To:</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="parent-ticket">Parent Ticket</label>
                            <select name="ticketList" class="form-control">
                                <option></option>
                                @foreach($callCenterTickets as $ticket)
                                    <option value="{{$ticket}}">{{sprintf("%'.09d\n", $ticket)}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-purple"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{--display call recordings--}}
    <div class="modal fade" id="display-call-recordings">
        <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Call Recordings</h4>
                    </div>

                    <div class="modal-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <button type="button" class="btn btn-success" title="Play Recording"><i class="fa fa-play"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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

    <script src="{{asset('/js/ticket.js')}}"></script>

    <script>
        $(function () {
            $('#ticket-list').DataTable()
        })
    </script>
@endsection