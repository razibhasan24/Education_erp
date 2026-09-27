<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $settings->institute_name ?? 'Welcome')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'SolaimanLipi','Kalpurush', Arial, sans-serif; }
        .navbar-brand { font-weight: 700; }
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                        url('https://picsum.photos/1600/600') center/cover;
            color: #fff;
            padding: 90px 0;
            text-align: center;
        }
        .hero h1 { font-size: 2.8rem; font-weight: 700; }
        .stat-box {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: 0.3s;
        }
        .stat-box:hover { transform: translateY(-5px); }
        .stat-box i { font-size: 2.5rem; color: #0d6efd; margin-bottom: 10px; }
        .stat-box h3 { font-size: 2rem; color: #333; margin: 0; }
        .notice-card {
            border-left: 4px solid #0d6efd;
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 6px;
        }
        footer {
            background: #212529;
            color: #adb5bd;
            padding: 40px 0 20px;
        }
        footer a { color: #adb5bd; text-decoration: none; }
        footer a:hover { color: #fff; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('frontend.home') }}">
            @if($settings && $settings->logo)
                <img src="{{ asset('storage/'.$settings->logo) }}" alt="Logo" height="40" class="me-2">
            @endif
            {{ $settings->institute_name ?? 'Universal School' }}
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.home')?'active':'' }}" href="{{ route('frontend.home') }}">হোম</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.about')?'active':'' }}" href="{{ route('frontend.about') }}">আমাদের সম্পর্কে</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.notices')?'active':'' }}" href="{{ route('frontend.notices') }}">নোটিশ</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.admission')?'active':'' }}" href="{{ route('frontend.admission') }}">অনলাইন ভর্তি</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.contact')?'active':'' }}" href="{{ route('frontend.contact') }}">যোগাযোগ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> লগইন</a></li>
            </ul>
        </div>
    </div>
</nav>

@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    </div>
@endif
@if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    </div>
@endif

@yield('content')

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5 class="text-white">{{ $settings->institute_name ?? 'Universal School' }}</h5>
                <p>{{ $settings->address ?? '' }}</p>
                @if($settings && $settings->eiin)
                    <p><b>EIIN:</b> {{ $settings->eiin }}</p>
                @endif
            </div>
            <div class="col-md-4">
                <h5 class="text-white">দ্রুত লিংক</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('frontend.about') }}"><i class="fas fa-angle-right"></i> আমাদের সম্পর্কে</a></li>
                    <li><a href="{{ route('frontend.notices') }}"><i class="fas fa-angle-right"></i> নোটিশ</a></li>
                    <li><a href="{{ route('frontend.admission') }}"><i class="fas fa-angle-right"></i> অনলাইন ভর্তি</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="text-white">যোগাযোগ</h5>
                @if($settings && $settings->phone)<p><i class="fas fa-phone"></i> {{ $settings->phone }}</p>@endif
                @if($settings && $settings->email)<p><i class="fas fa-envelope"></i> {{ $settings->email }}</p>@endif
            </div>
        </div>
        <hr class="border-secondary">
        <div class="text-center">
            &copy; {{ date('Y') }} {{ $settings->institute_name ?? 'USMS' }}. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
