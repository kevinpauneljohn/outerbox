@extends('layouts.admin_template')

@section('title')
   Super Admin | Dashboard
    @endsection

@section('variable_menu')
    <li class="active"><a href="{{url('/super-admin/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
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
<br/><br/>
<div class="col-sm-6">

    <div class="box box-primary">
        <div class="box-header with-border">
            <h4 class="box-title"
            >
            {{ $chart2->options['chart_title'] }}
            </h4>
            <div class="box-tools pull-right">
                <button type="button" id="collapse1" class="btn btn-box-tool" data-toggle="collapse"
                        data-target="#collapseOne"><i id="toggler1" class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse">
            <div class="box-body">
                <div>
                    {!! $chart2->renderHtml() !!}
                </div>
            </div>
        </div>
    </div>
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
   {!! $chart1->renderChartJsLibrary() !!}
    {!! $chart1->renderJs() !!}
    {!! $chart2->renderChartJsLibrary() !!}
    {!! $chart2->renderJs() !!}
    @endsection
