@extends('adminlte::page')

@section('title', $title ?? 'USMS - Universal School Management System')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
        @yield('page-actions')
    </div>
@stop
@section('content_top_nav_right')
    @auth
        @php
            $unreadNotifications = auth()->user()->unreadNotifications->take(5);
            $unreadCount = auth()->user()->unreadNotifications->count();
        @endphp

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown">
                <i class="far fa-bell"></i>
                @if($unreadCount > 0)
                    <span class="badge badge-warning navbar-badge">{{ $unreadCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" aria-labelledby="notifDropdown">
                <span class="dropdown-item dropdown-header">{{ $unreadCount }}টি নতুন নোটিফিকেশন</span>

                @forelse($unreadNotifications as $notif)
                    <a href="{{ $notif->url ?? '#' }}" class="dropdown-item">
                        <div class="media">
                            <i class="{{ $notif->icon }} mr-2 text-{{ $notif->color }}"></i>
                            <div class="media-body">
                                <h3 class="dropdown-item-title">{{ $notif->title }}</h3>
                                <p class="text-sm mb-0">{{ Str::limit($notif->message, 40) }}</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> {{ $notif->time_ago }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <span class="dropdown-item text-center text-muted">কোনো নতুন নোটিফিকেশন নেই</span>
                @endforelse

                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.notifications.index') }}" class="dropdown-item dropdown-footer">
                    সব নোটিফিকেশন দেখুন
                </a>
            </div>
        </li>
    @endauth
@stop
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @yield('content')
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap4-theme/1.0.0/select2-bootstrap4.min.css">
    @stack('styles')
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script>
        $(function() {
            $('.datatable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "খুঁজুন...",
                    lengthMenu: "_MENU_ প্রতি পেজে",
                    info: "_START_ থেকে _END_ দেখানো হচ্ছে, মোট _TOTAL_",
                    paginate: {
                        previous: "পূর্ববর্তী",
                        next: "পরবর্তী"
                    }
                }
            });
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });
    </script>
    @stack('scripts')
@stop
