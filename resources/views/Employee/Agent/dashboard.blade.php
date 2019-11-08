@extends('layouts.agentDashboardTemplate')

@section('extra_meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Agent | Dashboard
@endsection
@section('extra_stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet">
@endsection
@section('page_header')
{{--    Ticket <button type="button" class="btn bg-purple" data-toggle="modal" data-target="#create-ticket"><i class="fa fa-plus"></i> Add New</button>--}}
@endsection
@section('main_content')
<br/><br/>
<div class="col-sm-6">


    <div class="box box-primary">
        <div class="box-header with-border">
            <h4 class="box-title">
                {{ $chart1->options['chart_title'] }}
            </h4>
            <div class="box-tools pull-right">
                <button type="button" id="collapse2" class="btn btn-box-tool" data-toggle="collapse"
                        data-target="#collapseTwo"><i id="toggler2" class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div id="collapseTwo" class="panel-collapse">
            <div class="box-body">
                <div>
                    {!! $chart1->renderHtml() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6">

    <div class="col-lg-12">
        <!-- Info Boxes Style 2 -->
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="ion ion-ios-book-outline"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"> {{ __('All Tasks') }} </span>
                <span class="info-box-number">0</span>

                <div class="progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    0% {{ __('Completed') }}
                  </span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>
    <div class="col-lg-12">
        <!-- /.info-box -->
        <div class="info-box bg-red">
            <span class="info-box-icon"><i class="ion ion-android-document"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">{{ __('All Reports') }}</span>
                <span class="info-box-number">0</span>

                <div class="progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    0% {{ __('Completed') }}
                  </span>
            </div>
            <!-- /.info-box-content -->
        </div>

    </div>

    <div class="col-lg-12">
        <!-- /.info-box -->
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="ion ion-android-call"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">{{ __('Calls') }}</span>
                <span class="info-box-number">0</span>

                <div class="progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    0% {{ __('Detected') }}
                  </span>
            </div>
            <!-- /.info-box-content -->
        </div>

    </div>

    <div class="col-lg-12">
        <!-- /.info-box -->
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="ion ion-star"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">{{ __('Rating') }}</span>
                <span class="info-box-number">0</span>

                <div class="progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    0% {{ __('Completed') }}
                  </span>
            </div>
            <!-- /.info-box-content -->
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

    <script src="{{asset('/js/employee.js')}}"></script>

    <script>
        $(function () {
            $('#ticket-list').DataTable()
        })
    </script>
    {!! $chart1->renderChartJsLibrary() !!}
    {!! $chart1->renderJs() !!}
@endsection