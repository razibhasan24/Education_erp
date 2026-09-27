@extends('adminlte::page')

@section('title', $title ?? 'শিক্ষক পোর্টাল')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">@yield('page-title', 'ড্যাশবোর্ড')</h1>
        @yield('page-actions')
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @yield('content')
@stop

@section('css')
    @stack('styles')
@stop

@section('js')
    @stack('scripts')
@stop
