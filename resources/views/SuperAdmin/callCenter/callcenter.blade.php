@extends('layouts.admin_template')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Super Admin | Call Center
@endsection

@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('/bower_components/select2/dist/css/select2.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Call Center <button type="button" class="btn bg-purple" data-toggle="modal" data-target="#callCenterModal"><i class="fa fa-plus"></i> Add New</button>
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
    <li class="active"><a href="{{url('/super-admin/callCenter')}}"><i class="fa fa-phone-square"></i> <span>Call Center</span></a></li>
    <li><a href="{{url('/super-admin/lgu')}}"><i class="fa fa-bank"></i> <span>LGUs</span></a></li>
@endsection

@section('main_content')
    <div class="box">
        <div class="box-body">
            <table id="position_list" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="10%">Call Center</th>
                    <th width="30%">Address</th>
                    <th width="10%">Total No. of Agents</th>
                    <th>Assigned LGUs</th>
                    <th width="15%">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($callcenters as $callcenter)
                        <tr>
                            <td>{{ucfirst($callcenter->name)}}</td>
                            <td>{{ucfirst($callcenter->street).', '.ucfirst(\App\Http\Controllers\address\AddressController::cityName($callcenter->city).', '.ucfirst(\App\Http\Controllers\address\AddressController::provinceName($callcenter->state).' '.$callcenter->postalcode))}}</td>
                            <td></td>
                            <td></td>
                            <td>
                                <a href="{{route('callcenter.profile',['id' => $callcenter->id])}}"><button type="button" class="btn btn-success edit-btn" title="View"><i class="fa fa-eye"></i></button></a>
                                <button type="button" class="btn btn-primary edit-callcenter" title="Edit" data-toggle="modal" data-target="#edit_callCenterModal" value="{{ $callcenter->id }}"><i class="fa fa-edit"></i></button>
                                <button type="button" class="btn btn-danger delete-callcenter" title="Delete" data-toggle="modal" data-target="#delete_call_center" value="{{ $callcenter->id }}"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>Call Center</th>
                    <th>Address</th>
                    <th>Total No. of Agents</th>
                    <th>Assigned LGUs</th>
                    <th>Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->


    {{--Create New Call Center--}}
    <div class="modal fade" id="callCenterModal">
        <div class="modal-dialog">
            <form method="post" id="add-call-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add Call Center</h4>
                    </div>

                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="callcenter">Call Center Name</label>
                            <div class="callcenter">
                                <input type="text" name="callcenter" class="form-control" id="callcenter"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <div class="row">
                                <span class="col-lg-12">
                                    <div class="form-group">
                                        <div class="street_address">
                                            <input type="text" name="street_address" id="street_address" class="form-control"/>
                                            <label for="street_address">Street Address</label>
                                        </div>
                                    </div>
                                </span>
                            </div>
                            <div class="row">
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="region">
                                            <select class="form-control regions" style="width: 100%;" id="region" name="region">
                                                <option></option>
                                                @foreach($regions as $region)
                                                    <option value="{{$region->regCode}}">{{$region->regDesc}}</option>
                                                @endforeach
                                            </select>
                                            <label for="region">Region</label>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="state">
                                            <select class="form-control provinces" style="width: 100%;" id="state" name="state">

                                            </select>
{{--                                            <input type="text" name="state" id="state" class="form-control"/>--}}
                                            <label for="state">State</label>
                                        </div>
                                    </div>
                                </span>
                            </div>
                            <div class="row">
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="city">
                                            <select class="form-control cities" style="width: 100%;" id="city" name="city">

                                            </select>
{{--                                            <input type="text" name="city" id="city" class="form-control"/>--}}
                                            <label for="city">City</label>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="postal_code">
                                            <input type="text" name="postal_code" id="postal_code" class="form-control"/>
                                            <label for="postal_code">Postal Code</label>
                                        </div>
                                    </div>
                                </span>
                            </div>
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


    {{--Update Call Center Details--}}
    <div class="modal fade" id="edit_callCenterModal">
        <div class="modal-dialog">
            <form method="post" id="edit-call-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add Call Center</h4>
                    </div>

                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="callcenter_value" id="callcenter_value" />
                        <div class="form-group">
                            <label for="update_callcenter">Call Center Name</label>
                            <div class="update_callcenter">
                                <input type="text" name="update_callcenter" class="form-control" id="update_callcenter"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <div class="row">
                                <span class="col-lg-12">
                                    <div class="form-group">
                                        <div class="update_street_address">
                                            <input type="text" name="update_street_address" id="update_street_address" class="form-control"/>
                                            <label for="update_street_address">Street Address</label>
                                        </div>
                                    </div>
                                </span>
                            </div>
                            <div class="row">
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="update_region">
                                            <select class="form-control update_regions" style="width: 100%;" id="update_region" name="update_region">
                                                <option></option>
                                                @foreach($regions as $region)
                                                    <option value="{{$region->regCode}}">{{$region->regDesc}}</option>
                                                @endforeach
                                            </select>
                                            <label for="update_region">Region</label>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="update_state">
                                                <select class="form-control update_provinces" style="width: 100%;" id="update_state" name="update_state">

                                                </select>
                                                <label for="update_state">State</label>
                                            <label for="update_state">State</label>
                                        </div>
                                    </div>
                                </span>

                            </div>
                            <div class="row">
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="update_city">
                                            <select class="form-control update_cities" style="width: 100%;" id="update_city" name="update_city">

                                            </select>
                                            <label for="update_city">City</label>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="update_postal_code">
                                            <input type="text" name="update_postal_code" id="update_postal_code" class="form-control"/>
                                            <label for="update_postal_code">Postal Code</label>
                                        </div>
                                    </div>
                                </span>
                            </div>
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

    {{--Delete Call Center--}}
    <div class="modal modal-danger fade" id="delete_call_center">
        <div class="modal-dialog">
            <form method="post" id="delete_form_call_center">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete Call Center</h4>
                    </div>
                    <div class="modal-body">
                        <span>Are you sure you want to delete call center: <b class="callcenter_name"></b>?</span>
                    </div>
                    <input type="hidden" name="call_center_delete_id" id="call_center_delete_id"/>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline" name="submit" value=""><i class="fa fa-trash"></i> Delete</button>
                    </div>
                </div>
            </form>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('extra_script')
    <!-- DataTables -->
    <script src="{{asset('/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
    <!-- SlimScroll -->
    <script src="{{asset('/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>

    <!-- Select2 -->
    <script src="{{asset('/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <!-- FastClick -->
    <script src="{{asset('/bower_components/fastclick/lib/fastclick.js')}}"></script>

    <script src="{{asset('/js/callcenter.js')}}"></script>

    <!-- growl notification -->
    <script src="{{asset('bower_components/remarkable-bootstrap-notify/bootstrap-notify.min.js')}}"></script>

    <script>
        $(function () {
            $('#position_list').DataTable()
            $('#example2').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : false,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false
            });

            // $('.regions').select2();
            // $('.provinces').select2();
            // $('.cities').select2();
            //
            // $('.update_regions').select2();
            // $('.update_provinces').select2();
            // $('.update_cities').select2();
        })
    </script>
@endsection