@extends('layouts.lguTemplate')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    LGU | Announcement
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('/bower_components/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('/bower_components/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
    <button class="btn bg-purple" data-toggle="modal" data-target="#add-announcement"><i class="fa fa-plus"></i> &nbsp; Add New</button>
@endsection
@section('main_content')
    <div class="box">
        <div class="box-body">
            <h2>Announcements</h2>

            <table id="announcement" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="10%">Date Created</th>
                    <th width="10%">Title</th>
                    <th width="10%">Status</th>
                    <th width="10%">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $announcement)
                            <tr>
                                <td>{{$announcement->created_at}}</td>
                                <td>{{ucfirst($announcement->title)}}</td>
                                <td><small class="label bg-yellow">{{$announcement->status}}</small></td>
                                <td>
                                    <button class="btn btn-success" title="View" data-toggle="modal" data-target="#view-announcement"><i class="fa fa-eye"></i></button>
                                </td>
                            </tr>
                        @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th width="10%">Date Created</th>
                    <th width="10%">Title</th>
                    <th width="10%">Status</th>
                    <th width="10%">Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>

    {{--add announcement--}}
    <div class="modal fade" id="add-announcement">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="add-announcement-form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Announcement</h4>
                </div>

                <div class="modal-body">

                        @csrf
                        <div class="form-group title">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" id="title"/>
                        </div>
                        <div class="description">
                            <label for="description">Description</label>
                            <div class="box-body pad">

                                <textarea name="description" id="description" class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>

                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-purple"><i class="fa fa-check"></i> Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{--end add announcement--}}
    <div class="modal fade" id="view-announcement">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">


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
    <script src="{{asset('bower_components/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>

    <script src="{{asset('/js/announcement.js')}}"></script>

    <script>
        $(function () {
            $('#announcement').DataTable({
            })

            $('.textarea').wysihtml5()
        })
    </script>
@endsection