@extends('layouts.admin_template')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('title')
    Super Admin | Roles | Permissions
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    Permissions <button type="button" class="btn bg-purple" data-toggle="modal" data-target="#permissions"><i class="fa fa-plus"></i> Add New</button>
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
                <li><a href="{{url('/super-admin/roles')}}">View Roles</a></li>
                <li class="active"><a href="/super-admin/permissions">View Permissions</a></li>
            </ul>
        </a>
    </li>
    <li><a href="{{url('/super-admin/callCenter')}}"><i class="fa fa-phone-square"></i> <span>Call Center</span></a></li>
    <li><a href="{{url('/super-admin/lgu')}}"><i class="fa fa-bank"></i> <span>LGUs</span></a></li>
    <li><a href="{{url('/super-admin/activity')}}"><i class="fa fa-list"></i> <span>Activity</span></a></li>
@endsection

@section('main_content')
    <div class="box">
        <div class="box-body">
            <table id="position_list" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="20%">Permission</th>
                    <th width="15%">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>{{ $permission->name }}</td>
                            <td>
                                <button type="button" class="btn btn-primary edit_permission_btn" title="Edit" data-toggle="modal" data-target="#edit_permission" value="{{ $permission->id }}"><i class="fa fa-edit"></i></button>
                                <button type="button" class="btn btn-danger delete_permission_btn" title="Delete" data-toggle="modal" data-target="#delete_permission" value="{{ $permission->id }}"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>Permission</th>
                    <th>Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

    <!-- Modal -->
    {{--Create New Permission Form--}}
    <form method="post" action="{{route('permission')}}" id="permission_form">
        <div class="modal fade" id="permissions">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add New Permission</h4>
                    </div>
                    <div class="modal-body">

                        @csrf
                        <div class="form-group">
                            <label for="permission_name">Permission</label>
                            <div class="permission_name">
                                <input type="text" name="permission_name" class="form-control" id="permission_name"/>
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
    <div class="modal fade" id="edit_permission">
        <div class="modal-dialog">
            <form method="post" id="update_permission">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Update Permission</h4>
                    </div>

                    <div class="modal-body">

                        @csrf
                        <input type="hidden" name="permission_value" id="permission_value"/>
                        <div class="form-group">
                            <label for="edit_permission_name">Permission Name</label>
                            <div class="edit_permission_name">
                                <input type="text" name="edit_permission_name" class="form-control" id="edit_permission_name"/>
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

    <div class="modal modal-danger fade" id="delete_permission">
        <div class="modal-dialog">
            <form method="post" id="delete_form">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Delete Permission</h4>
                    </div>
                    <div class="modal-body">
                        <span>Are you sure you want to delete permission: <b class="delete_permission_name_confirm"></b>?</span>
                    </div>
                    <input type="hidden" name="delete_permission_row" class="delete_permission_row"/>
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

    <script src="{{asset('/js/permission.js')}}"></script>

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
            })
        })
    </script>
@endsection