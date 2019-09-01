@extends('layouts.adminDashboardTemplate')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Admin | LGU
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
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#local" data-toggle="tab" aria-expanded="true">Local</a></li>
                    <li class=""><a href="#national" data-toggle="tab" aria-expanded="false">National</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="local">
                        <button type="button" class="btn bg-purple" data-toggle="modal" data-target="#create-lgu" style="margin: 0px 0px 20px 0px;"><i class="fa fa-plus"></i> Add New</button>
                        <table id="local-lgu" class="table table-bordered table-hover">
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
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="national">
                        <table id="national-lgu" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th width="10%">Date Registered</th>
                                <th width="10%">Station Name</th>
                                <th>Department</th>
                                <th>Region</th>
                                <th>Province</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>Contact Person</th>
                                <th>Contact No.</th>
                                <th width="20%">Action</th>
                            </tr>
                            </thead>
                            <tbody>


                            </tbody>
                            <tfoot>
                            <tr>
                                <th width="10%">Date Registered</th>
                                <th width="10%">Station Name</th>
                                <th>Department</th>
                                <th>Region</th>
                                <th>Province</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>Contact Person</th>
                                <th>Contact No.</th>
                                <th width="20%">Action</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
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
            $('#local-lgu').DataTable({'lengthChange': false})
            $('#national-lgu').DataTable({'lengthChange': false})
        })
    </script>
@endsection