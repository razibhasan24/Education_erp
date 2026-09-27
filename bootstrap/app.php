<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',  // যদি api.php ফাইল থাকে
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        /*
        |--------------------------------------------------------------------------
        | Global Middleware (সব রিকোয়েস্টে চলে)
        |--------------------------------------------------------------------------
        */
        $middleware->use([
            // \App\Http\Middleware\TrustHosts::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Web Middleware Group
        |--------------------------------------------------------------------------
        */
        $middleware->web(append: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | API Middleware Group
        |--------------------------------------------------------------------------
        */
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware Alias (Route এ ব্যবহারের জন্য)
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            // Spatie Permission
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            // Laravel Built-in
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            // কাস্টম (নিচে বানাতে হবে)
            // 'active.user' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Trusted Proxies (লোড ব্যালেন্সার / ক্লাউডফ্লেয়ার / হোস্টিং)
        |--------------------------------------------------------------------------
        */
        $middleware->trustProxies(at: '*');

        /*
        |--------------------------------------------------------------------------
        | Trusted Hosts
        |--------------------------------------------------------------------------
        */
        $middleware->trustHosts(at: [
            'localhost',
            '127.0.0.1',
            // 'your-domain.com',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        /*
        |--------------------------------------------------------------------------
        | Custom Exception Rendering
        |--------------------------------------------------------------------------
        */

        // 403 — Unauthorized
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'আপনার এই কাজটি করার অনুমতি নেই।'], 403);
            }
            return response()->view('errors.403', ['message' => 'আপনার এই কাজটি করার অনুমতি নেই।'], 403);
        });

        // 404 — Not Found
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'রিসোর্স খুঁজে পাওয়া যায়নি।'], 404);
            }
        });

        // Custom Business Logic Exception (ঐচ্ছিক)
        $exceptions->render(function (\App\Exceptions\BusinessException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        });

        /*
        |--------------------------------------------------------------------------
        | Exception Logging (ঐচ্ছিক)
        |--------------------------------------------------------------------------
        */
        $exceptions->report(function (\Throwable $e) {
            // চাইলে Slack / Sentry / ইমেইলে পাঠাতে পারেন
        });

    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {

        /*
        |--------------------------------------------------------------------------
        | Scheduled Tasks (Cron)
        |--------------------------------------------------------------------------
        | সার্ভারে crontab এ যোগ করুন:
        | * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
        */

        // 1. পুরোনো read notification মুছুন — প্রতি সপ্তাহে
        $schedule->command('notifications:purge --days=90')
            ->weekly()
            ->sundays()
            ->at('02:00')
            ->name('purge-old-notifications')
            ->withoutOverlapping();

        // 2. পুরোনো SMS লগ মুছুন — প্রতি মাসে
        $schedule->call(function () {
            \App\Models\SmsLog::where('created_at', '<', now()->subMonths(6))->delete();
        })
            ->monthlyOn(1, '03:00')
            ->name('purge-old-sms-logs')
            ->withoutOverlapping();

        // 3. ডেটাবেস ব্যাকআপ — প্রতিদিন রাত 3টায়
        // (BackupController এর লজিক কমান্ডে নিয়ে আসতে হবে)
        // $schedule->command('backup:run')
        //     ->dailyAt('03:00')
        //     ->name('daily-db-backup')
        //     ->withoutOverlapping();

        // 4. বকেয়া ফি রিমাইন্ডার SMS — প্রতি সোমবার সকাল 10টায়
        $schedule->call(function () {
            $students = \App\Models\Student::where('status', 'active')->get();
            $smsService = app(\App\Services\SmsService::class);

            foreach ($students as $student) {
                $due = \App\Models\FeeInvoice::where('student_id', $student->id)->sum('due_amount');
                if ($due > 0 && $student->father_phone) {
                    $smsService->sendDueSms($student, $due);
                }
            }
        })
            ->weeklyOn(1, '10:00')
            ->name('weekly-due-reminders')
            ->withoutOverlapping();

        // 5. Queue failed jobs পরিষ্কার — প্রতিদিন
        $schedule->command('queue:prune-failed --hours=48')
            ->daily()
            ->name('prune-failed-jobs');

        // 6. Cache পরিষ্কার — প্রতিদিন ভোর 4টায়
        $schedule->command('cache:prune-stale-tags')
            ->dailyAt('04:00')
            ->name('prune-stale-cache');

        // 7. Session cleanup — প্রতিদিন
        $schedule->command('session:prune')
            ->daily()
            ->name('prune-sessions');
    })
    ->create();