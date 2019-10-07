@extends('layouts.admin_template')

@section('title')
    Super Admin | LGU
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('/bower_components/select2/dist/css/select2.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    LGU <button type="button" class="btn bg-purple" data-toggle="modal" data-target="#create-lgu"><i class="fa fa-plus"></i> Add New</button>
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
    <li class="active"><a href="{{url('/super-admin/lgu')}}"><i class="fa fa-bank"></i> <span>LGUs</span></a></li>
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
                                <button type="button" class="btn btn-default"><i class="fa fa-eye"></i></button>
                                <button type="button" class="btn btn-primary edit-lgu-btn" data-toggle="modal" data-target="#edit-lgu" value="{{$lgu->lgu_id}}"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-danger delete-lgu-btn" data-toggle="modal" data-target="#delete-lgu" value="{{$lgu->lgu_id}}"><i class="fa fa-trash"></i></button>
                                <button type="button" class="btn btn-warning"><i class="fa fa-phone"></i></button>
                                <button type="button" class="btn btn-success"><i class="fa fa-comment"></i></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
                <tfoot>
                <tr>
                    <th width="8%">Date Registered</th>
                    <th width="8%">Station Name</th>
                    <th width="10%">Department</th>
                    <th width="20%">Address</th>
                    <th width="8%">Contact Person</th>
                    <th width="8%">Contact No.</th>
                    <th width="13%">Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

    {{--Create New LGU--}}
    <div class="modal fade" id="create-lgu">
        <div class="modal-dialog">
            <form method="post" id="add-lgu">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add LGU</h4>
                    </div>

                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="station_name">Station Name</label>
                                    <div class="station_name">
                                        <input type="text" name="station_name" class="form-control" id="station_name"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <div class="department">
                                        <input type="text" name="department" class="form-control" id="department"/>
                                    </div>
                                </div>
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

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="contactperson_name">
                                        <label for="contactperson_name">Contact Person</label>
                                        <input type="text" name="contactperson_name" id="contactperson_name" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="contactperson_no">
                                        <label for="contactperson_no">Contact Number</label>
                                        <input type="text" name="contactperson_no" id="contactperson_no" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="call_center">
                                        <label for="call_center">Assign To Call Center</label>
                                        <select class="call-center-list form-control" name="call_center" id="call_center">
                                            <option></option>
                                            @foreach($callCenters as $callCenter)
                                                <option value="{{$callCenter->id}}">{{$callCenter->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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

    <script src="{{asset('/js/lgu.js')}}"></script>

    <script>
        $(function () {
            $('#lgu-list').DataTable({
            })
        })
    </script>
@endsection