@extends('adminlte::page')

@section('title', $title ?? 'স্টুডেন্ট পোর্টাল')

@section('content_header')
    <h1 class="m-0">@yield('page-title', 'ড্যাশবোর্ড')</h1>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    @stack('styles')
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function(){ $('.datatable').DataTable({ responsive: true, pageLength: 25 }); });
    </script>
    @stack('scripts')
@stop
