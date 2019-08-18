@extends('layouts.admin_template')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Super Admin | Staffs
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Employee <button type="button" class="btn bg-purple" data-toggle="modal" data-target="#create-staff"><i class="fa fa-plus"></i> Add New</button>
@endsection
@section('variable_menu')
    <li><a href="{{url('/super-admin/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
    <li class="active"><a href="{{url('employee')}}"><i class="fa fa-user-plus"></i> <span>Employee</span></a></li>
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
    <li><a href="{{url('/super-admin/lgu')}}"><i class="fa fa-bank"></i> <span>LGUs</span></a></li>
@endsection

@section('main_content')
    <div class="box">
        <div class="box-body">
            <table id="agents-list" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="10%">Date Registered</th>
                    <th width="20%">Full Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Call Center</th>
                    <th width="15%">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->created_at}}</td>
                            <td>
                                {{ucfirst($user->firstname).' '.ucfirst($user->lastname)}}
                            </td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->username}}</td>
                            <td>{{(sizeof($user->roles) > 0) ? ucfirst($user->roles[0]->name) : ''}}</td>
                            <td></td>
                            <td>
                                <a href="#"><button type="button" class="btn btn-success edit-btn" title="View"><i class="fa fa-eye"></i></button></a>
                                <button type="button" class="btn btn-primary edit-callcenter" title="Edit" data-toggle="modal" data-target="#edit_callCenterModal" value=""><i class="fa fa-edit"></i></button>
                                <button type="button" class="btn btn-danger delete-callcenter" title="Delete" data-toggle="modal" data-target="#delete_call_center" value=""><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th width="10%">Date Registered</th>
                    <th width="20%">Full Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Call Center</th>
                    <th width="15%">Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

    {{--Create New Staff--}}
    <div class="modal fade" id="create-staff">
        <div class="modal-dialog modal-lg">
            <form method="post" id="add-staff">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add New Employee</h4>
                    </div>

                    <div class="modal-body">
                        @csrf

                        <div class="form-group">
                            <div class="row">
                                <span class="col-lg-4">
                                    <div class="form-group">
                                        <div class="firstname">
                                            <label for="firstname">First name</label> <span class="required">*</span>
                                            <input type="text" name="firstname" id="firstname" class="form-control"/>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-4">
                                    <div class="form-group">
                                        <div class="middlename">
                                            <label for="middlename">Middle name</label>
                                            <input type="text" name="middlename" id="middlename" class="form-control"/>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-4">
                                    <div class="form-group">
                                        <div class="lastname">
                                            <label for="lastname">Last name</label><span class="required">*</span>
                                            <input type="text" name="lastname" id="lastname" class="form-control"/>
                                        </div>
                                    </div>
                                </span>
                            </div>
                            <div class="row">
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="email">
                                            <label for="email">Email</label><span class="required">*</span>
                                            <input type="text" name="email" id="email" class="form-control"/>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="username">
                                            <label for="username">Username</label><span class="required">*</span>
                                            <input type="text" name="username" id="username" class="form-control"/>
                                        </div>
                                    </div>
                                </span>
                            </div>

                            <div class="row">
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="password">
                                            <label for="password">Password</label><span class="required">*</span>
                                            <input type="password" name="password" id="password" class="form-control"/>
                                        </div>
                                    </div>
                                </span>
                                <span class="col-lg-6">
                                    <div class="form-group">
                                        <div class="password_confirmation">
                                            <label for="password_confirmation">Confirm Password</label><span class="required">*</span>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"/>
                                        </div>
                                    </div>
                                </span>
                            </div>
{{--                        </div>--}}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <div class="role">
                                            <label for="role">Select Role</label>
                                            <select name="role" class="form-control role" id="role">
                                                <option></option>
                                                @foreach($roles as $role)
                                                    <option value="{{$role->name}}">{{ucfirst($role->name)}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <div class="callcenter">
                                            <label for="callcenter">Assign To Call Center</label>
                                            <select name="callcenter" class="form-control role" id="callcenter">
                                                <option></option>
                                                @foreach($callcenters as $callcenter)
                                                    <option value="{{$callcenter->id}}">{{ucfirst($callcenter->name)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
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

    <script src="{{asset('/js/employee.js')}}"></script>

    <script>
        $(function () {
            $('#agents-list').DataTable()
        })
    </script>
@endsection