@extends('layouts.admin_template')

@section('title')
    Super Admin | LGU
@endsection

@section('variable_menu')
    <li><a href="{{url('/super-admin/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
    <li><a href="{{url('employee')}}"><i class="fa fa-user-plus"></i> <span>Employee</span></a></li>
    <li><a href="{{url('/super-admin/roles')}}"><i class="fa fa-users"></i> <span>Roles</span></a></li>
    <li><a href="{{url('/super-admin/callCenter')}}"><i class="fa fa-phone-square"></i> <span>Call Center</span></a></li>
    <li class="active"><a href="{{url('/super-admin/lgu')}}"><i class="fa fa-bank"></i> <span>LGUs</span></a></li>
@endsection

@section('main_content')

@endsection

@section('extra_script')
@endsection