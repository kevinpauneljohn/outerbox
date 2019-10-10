@extends('layouts.admin_template')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @endsection
@section('title')
    Super Admin | Roles
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Roles <button type="button" class="btn bg-purple" data-toggle="modal" data-target="#roles"><i class="fa fa-plus"></i> Add New</button>
    @endsection

@section('variable_menu')
    <li><a href="{{url('/super-admin/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
    <li><a href="{{url('employee')}}"><i class="fa fa-user-plus"></i> <span>Employee</span></a></li>
    <li class="active treeview">
        <a href="#">
            <i class="fa fa-bank"></i><span>Roles</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            <ul class="treeview-menu">
                <li class="active"><a href="{{url('/super-admin/roles')}}">View Roles</a></li>
                <li><a href="/super-admin/permissions">View Permissions</a></li>
            </ul>
        </a>
    </li>
    <li><a href="{{url('/super-admin/callCenter')}}"><i class="fa fa-phone-square"></i> <span>Call Center</span></a></li>
    <li><a href="{{url('/super-admin/lgu')}}"><i class="fa fa-bank"></i> <span>LGUs</span></a></li>
    <li><a href="{{url('/super-admin/activity')}}"><i class="fa fa-list"></i> <span>Activity</span></a></li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-pie-chart"></i><span>Reports</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            <ul class="treeview-menu">
                <li><a href="{{url('/super-admin/performance-eval')}}">Performance Evaluation</a></li>
                <li><a href="/super-admin/user-management">User Management</a></li>
                <li><a href="/super-admin/forecast">Forecast</a></li>
            </ul>
        </a>
    </li>
@endsection

@section('main_content')
    <div class="box">
        <div class="box-body">
            <table id="position_list" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="20%">Role</th>
                    <th>Description</th>
                    <th width="15%">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ ucfirst($role->name) }}</td>
                            <td>{{ ucfirst($role->description) }}</td>
                            <td>
                                <button type="button" class="btn btn-primary edit-btn" title="Edit" data-toggle="modal" data-target="#edit_role" value="{{ $role->id }}"><i class="fa fa-edit"></i></button>
                                <button type="button" class="btn btn-danger delete-btn" title="Delete" data-toggle="modal" data-target="#delete" value="{{ $role->id }}"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>Role</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

    <!-- Modal -->
    {{--Create New Role Form--}}
    <form method="post" action="{{route('role')}}" id="role_form">
    <div class="modal fade" id="roles">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Role</h4>
                </div>
                <div class="modal-body">

                        @csrf
                        <div class="form-group">
                            <label for="name">Role Name</label>
                            <div class="name">
                                <input type="text" name="name" class="form-control" id="name"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <div class="description">
                                <textarea class="form-control" name="description" id="description"></textarea>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-purple"><i class="fa fa-plus"></i> Add</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    </form>
    <!-- /.modal -->

    {{--Update Role Form--}}
    <div class="modal fade" id="edit_role">
        <div class="modal-dialog">
            <form method="post" id="update_role">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update Role</h4>
                </div>

                <div class="modal-body">

                        @csrf
                    <input type="hidden" name="role_value" id="role_value"/>
                    <span id="change_status"></span>
                        <div class="form-group">
                            <label for="edit_name">Role Name</label>
                            <div class="edit_name">
                                <input type="text" name="edit_name" class="form-control" id="edit_name"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <div class="edit_description">
                                <textarea class="form-control" name="edit_description" id="edit_description"></textarea>
                            </div>
                        </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-purple"><i class="fa fa-edit"></i> Update</button>
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="modal modal-danger fade" id="delete">
        <div class="modal-dialog">
            <form method="post" id="delete_form">
                @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete Role</h4>
                </div>
                <div class="modal-body">
                    <span>Are you sure you want to delete role: <b class="role_name"></b>?</span>
                </div>
                <input type="hidden" name="role" class="role_delete" value="4"/>
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

    <!-- FastClick -->
    <script src="{{asset('/bower_components/fastclick/lib/fastclick.js')}}"></script>

    <!-- growl notification -->
    <script src="{{asset('bower_components/remarkable-bootstrap-notify/bootstrap-notify.min.js')}}"></script>

    <script src="{{asset('/js/role.js')}}"></script>

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
            })
        })
    </script>
@endsection