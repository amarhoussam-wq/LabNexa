<?php

use App\Http\Controllers\AnalysesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Labo_dzController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\QualityDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

Route::get('/ping', function() { return 'pong'; });
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ==========================================================================
// 1. PUBLIC ROUTES
// ==========================================================================
Route::get('/', function() {
    if (str_contains(request()->header('User-Agent'), 'curl') || str_contains(request()->header('User-Agent'), 'Wget')) {
        return 'pong';
    }
    return app()->make(App\Http\Controllers\Labo_dzController::class)->index();
})->name('home');
Route::post('/booking', [Labo_dzController::class, 'booking'])->name('booking');
Route::get('/booking/success/{id}', function ($id) {
    $reservation = \App\Models\Request_reservation::findOrFail($id);
    $analyses = $reservation->analyses()->get();
    $preparations = $analyses->filter(function ($a) {
        return !empty($a->preparation_instructions);
    });
    return view('booking-success', compact('reservation', 'analyses', 'preparations'));
})->name('booking.success');
Route::post('/message', [Labo_dzController::class, 'message'])->name('message');
Route::get('/analysis-info', [Labo_dzController::class, 'analysisInfo'])->name('analysis.info');

Route::get('/debug-admins', function() {
    return \App\Models\Administrator::all(['id', 'name', 'email']);
});

// Redirect if user appends /access/ by mistake
Route::get('/access/unify-access', function() {
    return redirect('/unify-access');
});

// Temporary: Unify Credentials (admin / admin123) for Ministerial Committee Review
Route::get('/unify-access', function() {
    $results = [];

    // 1. Admin - Update ALL existing admins to admin/admin123
    $admins = \App\Models\Administrator::all();
    if ($admins->count() > 0) {
        foreach ($admins as $admin) {
            $admin->update([
                'name' => 'admin',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            ]);
        }
        $results[] = "✅ Updated " . $admins->count() . " Administrators";
    } else {
        \App\Models\Administrator::create([
            'name' => 'admin',
            'email' => 'admin@labnexa.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
        ]);
        $results[] = "✅ Created new Master Administrator";
    }

    // 2. Doctor
    \App\Models\Doctor::updateOrCreate(
        ['email' => 'admin'],
        [
            'name' => 'Dr. LabNexa Admin',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'specialty' => 'System Administration',
            'phone' => '0500000000',
            'address' => 'LabNexa HQ',
        ]
    );
    $results[] = "✅ Updated Master Doctor Account";

    // 3. Patient
    \App\Models\Patient::firstOrCreate(
        ['email' => 'admin'],
        [
            'name' => 'Patient Admin Account',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'phone' => '0600000000',
            'gender' => 'male',
            'birth_date' => '1990-01-01',
        ]
    );
    $results[] = "✅ Updated Master Patient Account";

    return "<div style='font-family: Arial; padding: 40px; text-align: center;'>
                <h1 style='color: #005EB8;'>LabNexa Unified Access System</h1>
                <div style='background: #f0f7ff; padding: 20px; border-radius: 10px; display: inline-block; text-align: left;'>
                    " . implode("<br>", $results) . "
                    <hr style='border: 0; border-top: 1px solid #cce3f6; margin: 20px 0;'>
                    <p><strong>Username/Email:</strong> admin</p>
                    <p><strong>Password:</strong> admin123</p>
                </div>
                <p style='margin-top: 20px; color: #666;'>You can now use these credentials across all portals.</p>
                <a href='/access' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background: #005EB8; color: white; text-decoration: none; border-radius: 5px;'>Go to Login Portal</a>
            </div>";
});

// ==========================================================================
// 2. UNIFIED ACCESS PORTAL (Doctor & Patient)
// ==========================================================================
Route::get('/access', [\App\Http\Controllers\UnifiedAccessController::class, 'show'])->name('access');
Route::post('/access', [\App\Http\Controllers\UnifiedAccessController::class, 'login'])->name('access.submit');

Route::group(['prefix' => 'portal', 'middleware' => 'auth:patient'], function () {
    Route::get('/dashboard', [\App\Http\Controllers\PatientPortalController::class, 'dashboard'])->name('patient.dashboard');
    Route::get('/download/{id}', [\App\Http\Controllers\PatientPortalController::class, 'downloadResult'])->name('patient.download');
    Route::post('/logout', [\App\Http\Controllers\PatientPortalController::class, 'logout'])->name('patient.logout');
});

// ==========================================================================
// 3. PHYSICIAN PORTAL (Guard: doctor)
// ==========================================================================
Route::group(['prefix' => 'physician'], function () {
    Route::group(['middleware' => 'auth:doctor'], function () {
        Route::get('/dashboard', [\App\Http\Controllers\DoctorDashboardController::class, 'dashboard'])->name('doctor.dashboard');
        Route::get('/patient-result/{id}', [\App\Http\Controllers\DoctorDashboardController::class, 'showPatientResult'])->name('doctor.patient.result');
        Route::get('/eligibility-report/{id}', [\App\Http\Controllers\DoctorDashboardController::class, 'showEligibilityReport'])->name('doctor.eligibility.report');
        Route::get('/download/{id}', [\App\Http\Controllers\DoctorDashboardController::class, 'downloadResult'])->name('doctor.download');
        Route::post('/{id}/ai-insight', [ReservationsController::class, 'generateAiInsight'])->name('doctor.bookings.ai-insight');
        Route::post('/logout', [\App\Http\Controllers\DoctorAuthController::class, 'logout'])->name('doctor.logout');
    });
});

// ==========================================================================
// 4. ADMINISTRATIVE PANEL (Guard: administrator)
// ==========================================================================
// Admin Auth
Route::middleware('guest:administrator')->group(function () {
    Route::get('/auth', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/auth/login', [AuthController::class, 'administrator_login'])->name('auth.administrator');
});

Route::post('/auth/logout', [AuthController::class, 'logout'])->name('administrator.logout')->middleware('auth:administrator');

// Admin Protected Area
Route::middleware('auth:administrator')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Patient Mobile App Access Management (Admin)
    Route::prefix('dashboard/patients')->group(function () {
        Route::get('/', [\App\Http\Controllers\PatientsController::class, 'index'])->name('admin.patients.index');
        Route::patch('/{id}/set-password', [\App\Http\Controllers\PatientsController::class, 'setPassword'])->name('admin.patients.set-password');
        Route::put('/{id}/update-profile', [\App\Http\Controllers\PatientsController::class, 'updateProfile'])->name('admin.patients.update-profile');
        Route::delete('/{id}/revoke-password', [\App\Http\Controllers\PatientsController::class, 'revokePassword'])->name('admin.patients.revoke-password');
        Route::post('/merge', [\App\Http\Controllers\PatientsController::class, 'mergePatients'])->name('admin.patients.merge');
    });

    // Reservations & Eligibility
    Route::group(['prefix' => 'dashboard/reservations'], function () {
        Route::get('/', [ReservationsController::class, 'reservations'])->name('reservations');
        Route::get('/statistics', [\App\Http\Controllers\StatisticsController::class, 'index'])->name('admin.bookings.statistics');
        Route::get('/filter', [ReservationsController::class, 'filterReservations'])->name('filter.reservations');
        Route::put('/{id}', [ReservationsController::class, 'updateBookingStatus'])->name('admin.bookings.update');

        Route::get('/requests', [ReservationsController::class, 'reservationRequests'])->name('reservation.requests');
        Route::post('/requests/{id}/confirm', [ReservationsController::class, 'confirmRequest'])->name('reservation.requests.confirm');
        Route::post('/requests/{id}/reject', [ReservationsController::class, 'rejectRequest'])->name('reservation.requests.reject');

        Route::post('/{id}/check-eligibility', [ReservationsController::class, 'checkExecutionEligibility'])->name('admin.bookings.check-eligibility');
        Route::get('/{id}/eligibility-check', [ReservationsController::class, 'showEligibilityCheck'])->name('admin.bookings.eligibility.form');
        Route::post('/{id}/eligibility-check', [ReservationsController::class, 'submitEligibilityCheck'])->name('admin.bookings.eligibility.submit');
        Route::get('/{id}/full-eligibility', [ReservationsController::class, 'showFullEligibilityCheck'])->name('admin.bookings.full-eligibility.form');
        Route::post('/{id}/full-eligibility', [ReservationsController::class, 'submitFullEligibilityCheck'])->name('admin.bookings.full-eligibility.submit');
        Route::get('/{id}/eligibility-results', [ReservationsController::class, 'showEligibilityResults'])->name('admin.bookings.eligibility.results');
        Route::get('/{id}/print-report', [ReservationsController::class, 'printEligibilityReport'])->name('admin.bookings.eligibility.print');
        Route::put('/analysis/{id}/status', [ReservationsController::class, 'updateAnalysisStatus'])->name('admin.bookings.analysis.status.update');
        Route::put('/{id}/referral', [ReservationsController::class, 'updateReferral'])->name('admin.bookings.referral.update');
        Route::post('/{id}/notify', [ReservationsController::class, 'notifyParticipants'])->name('admin.bookings.notify');
        Route::get('/{id}/results', [ReservationsController::class, 'showResultsForm'])->name('admin.bookings.results.form');
        Route::post('/{id}/results', [ReservationsController::class, 'updateResults'])->name('admin.bookings.results.update');
        Route::post('/{id}/ai-insight', [ReservationsController::class, 'generateAiInsight'])->name('admin.bookings.ai-insight');
        Route::get('/{id}', [ReservationsController::class, 'showReservation'])->name('admin.bookings.show');
    });

    // Analyses & Protocol
    Route::group(['prefix' => 'dashboard/analyses'], function () {
        Route::get('/', [AnalysesController::class, 'analyses'])->name('analyses');
        Route::get('/create', [AnalysesController::class, 'createAnalysis'])->name('analyses.create');
        Route::post('/', [AnalysesController::class, 'storeAnalysis'])->name('analyses.store');
        Route::get('/{id}/edit', [AnalysesController::class, 'editAnalysis'])->name('analyses.edit');
        Route::put('/{id}', [AnalysesController::class, 'updateAnalysis'])->name('analyses.update');
        Route::delete('/{id}', [AnalysesController::class, 'destroyAnalysis'])->name('analyses.destroy');
        Route::put('/{id}/toggle-availability', [AnalysesController::class, 'toggleAvailability'])->name('analyses.toggle-availability');
    });

    Route::group(['prefix' => 'dashboard/eligibility'], function () {
        Route::get('/{analysis_id}', [\App\Http\Controllers\EligibilityController::class, 'manage'])->name('eligibility.manage');
        Route::post('/{analysis_id}/questions', [\App\Http\Controllers\EligibilityController::class, 'storeQuestion'])->name('eligibility.questions.store');
        Route::put('/questions/{id}', [\App\Http\Controllers\EligibilityController::class, 'updateQuestion'])->name('eligibility.questions.update');
        Route::delete('/questions/{id}', [\App\Http\Controllers\EligibilityController::class, 'destroyQuestion'])->name('eligibility.questions.destroy');
        Route::post('/questions/{question_id}/options', [\App\Http\Controllers\EligibilityController::class, 'storeOption'])->name('eligibility.options.store');
        Route::put('/options/{id}', [\App\Http\Controllers\EligibilityController::class, 'updateOption'])->name('eligibility.options.update');
        Route::delete('/options/{id}', [\App\Http\Controllers\EligibilityController::class, 'destroyOption'])->name('eligibility.options.destroy');
    });

    // CRM & Communications
    Route::group(['prefix' => 'dashboard/messages'], function () {
        Route::get('/', [MessagesController::class, 'messages'])->name('messages');
        Route::post('/send', [MessagesController::class, 'sendMessage'])->name('messages.send');
        Route::post('/send-result', [MessagesController::class, 'sendResult'])->name('messages.send-result');
        Route::delete('/{id}', [MessagesController::class, 'deleteMessage'])->name('messages.delete');
        Route::patch('/{id}/mark-as-read', [MessagesController::class, 'markAsRead'])->name('messages.markAsRead');
    });

    // System Settings
    Route::group(['prefix' => 'dashboard/settings'], function () {
        Route::get('/', [\App\Http\Controllers\SettingsController::class, 'index'])->name('admin.settings');
        Route::post('/update', [\App\Http\Controllers\SettingsController::class, 'update'])->name('admin.settings.update');
    });


    // Doctor Management (Admin)
    Route::group(['prefix' => 'dashboard/doctors'], function () {
        Route::get('/', [\App\Http\Controllers\DoctorsController::class, 'index'])->name('admin.doctors.index');
        Route::post('/', [\App\Http\Controllers\DoctorsController::class, 'store'])->name('admin.doctors.store');
        Route::put('/{id}', [\App\Http\Controllers\DoctorsController::class, 'update'])->name('admin.doctors.update');
        Route::delete('/{id}', [\App\Http\Controllers\DoctorsController::class, 'destroy'])->name('admin.doctors.destroy');
    });

    // Quality Dashboard
    Route::group(['prefix' => 'dashboard/quality'], function () {
        Route::get('/', [QualityDashboardController::class, 'index'])->name('admin.quality.dashboard');
        Route::post('/nc', [QualityDashboardController::class, 'storeNc'])->name('admin.quality.nc.store');
    });

});

// System Utilities (restricted to local environment)
if (app()->environment('local')) {
    Route::get('/system/fresh-start', function (\Illuminate\Http\Request $request) {
        if ($request->get('code') !== 'clear123') {
            return "❌ يرجى استخدام هذا الرابط فقط في بيئة التطوير.";
        }

        try {
            Log::info("DATABASE FRESH START INITIATED (LOCAL ONLY)");
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $counts = [];
            $tables = [
                'patients', 'histories', 'messages', 'request_reservations', 
                'reminders', 'analyse_request_reservation', 'request_reservation_analyses', 
                'patient_answers', 'reservations', 'reservation_analyses', 'audit_logs',
                'personal_access_tokens'
            ];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $counts[$table] = DB::table($table)->count();
                    DB::table($table)->truncate();
                    Log::info("Truncated table: $table (was $counts[$table] rows)");
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $msg = "✅ تم مسح جميع البيانات بنجاح (بيئة التطوير فقط)!<br><br><b>الإحصائيات:</b><ul style='text-align:left; direction:ltr;'>";
            foreach ($counts as $table => $count) {
                $msg .= "<li>$table: $count records deleted</li>";
            }
            $msg .= "</ul>";
            
            return $msg;
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Log::error("Fresh start error: " . $e->getMessage());
            return "❌ خطأ أثناء مسح البيانات: " . $e->getMessage();
        }
    })->name('admin.system.fresh-start');
}

// Utilities
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'fr'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// Diagnostics & maintenance helpers (restricted to local environment)
if (app()->environment('local')) {
    // Diagnostics for Hosting
    Route::get('/debug-db', function () {
        try {
            DB::connection()->getPdo();
            $tables = DB::connection()->getSchemaBuilder()->getAllTables();
            $tableName = 'request_reservations';
            $tableExists = Schema::hasTable($tableName);
            $count = $tableExists ? DB::table($tableName)->count() : 'Table not found';
            
            return [
                'database' => DB::getDatabaseName(),
                'connection' => 'Connected',
                'tables_count' => count($tables),
                'target_table_exists' => $tableExists,
                'bookings_recorded' => $count,
                'columns' => Schema::getColumnListing($tableName),
                'env_app_url' => config('app.url'),
                'env_db_port' => config('database.connections.mysql.port'),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
        }
    });
}

// Unified Secure PDF Download (Supports both requests and confirmed)
Route::get('/reservation/{id}/pdf/{type?}', [\App\Http\Controllers\PdfController::class, 'generateReservationPdf'])->name('reservation.pdf');
Route::get('/reservation/{id}/label', [\App\Http\Controllers\PdfController::class, 'generateSampleLabel'])->name('reservation.label');

// Debug: comprehensive login diagnostics (local only)
if (app()->environment('local')) {
    Route::get('/debug-login', function (\Illuminate\Http\Request $request) {
        $info = [];
        $info['app_url'] = config('app.url');
        $info['session_driver'] = config('session.driver');
        $info['session_domain'] = config('session.domain') ?: '(null - use request domain)';
        $info['session_secure'] = config('session.secure') ? 'true' : 'false';
        $info['session_same_site'] = config('session.same_site');
        $info['session_cookie_name'] = config('session.cookie');
        $info['request_url'] = $request->url();
        $info['request_scheme'] = $request->getScheme();
        $info['request_host'] = $request->getHost();
        $info['is_secure'] = $request->isSecure() ? 'true' : 'false';
        $info['session_id'] = session()->getId();
        $info['route_auth_url'] = route('auth.administrator');
        $info['route_dashboard_url'] = route('dashboard');
        
        // Test session persistence
        $visits = session('debug_visits', 0) + 1;
        session(['debug_visits' => $visits]);
        $info['session_visits'] = $visits . ' (should increment on refresh)';
        
        // Check admin
        $admin = \App\Models\Administrator::where('name', 'doctor1')->first();
        $info['admin_exists'] = $admin ? "YES (ID: {$admin->id})" : 'NO';
        
        // Test password
        if ($admin) {
            $info['password_check'] = \Illuminate\Support\Facades\Hash::check('fbifbifbi', $admin->password) ? '✅ CORRECT' : '❌ WRONG';
        }
        
        // Check auth state
        $info['is_authenticated'] = \Illuminate\Support\Facades\Auth::guard('administrator')->check() ? 'YES' : 'NO';
        
        $html = "<h2>🔍 Login Debug Info</h2><table border='1' cellpadding='8'>";
        foreach ($info as $key => $val) {
            $html .= "<tr><td><strong>{$key}</strong></td><td>{$val}</td></tr>";
        }
        $html .= "</table>";
        $html .= "<br><p><strong>Refresh this page</strong> to see if session_visits increments. If it stays at 1, sessions are NOT persisting.</p>";
        
        return $html;
    });
}

// ─── Production maintenance routes (public – run once on server) ─────────────

Route::get('/run-migrate', function () {
    try {
        // 0. Clear Caches
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        $cacheOutput = \Illuminate\Support\Facades\Artisan::output();

        // 1. Run Migrations
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrationOutput = \Illuminate\Support\Facades\Artisan::output();

        // 2. Populate Data (Sample types and Tube colors)
        $mappings = [
            'FNS' => ['type' => 'EDTA', 'color' => '#A020F0'],
            'تحليل الدم الشامل' => ['type' => 'EDTA', 'color' => '#A020F0'],
            'NFS' => ['type' => 'EDTA', 'color' => '#A020F0'],
            'Groupage' => ['type' => 'EDTA', 'color' => '#A020F0'],
            'فصيلة الدم' => ['type' => 'EDTA', 'color' => '#A020F0'],
            'ECBU' => ['type' => 'Urine', 'color' => '#ffc107'],
            'تحليل البول الكامل' => ['type' => 'Urine', 'color' => '#ffc107'],
            'Glycémie' => ['type' => 'Dry Tube', 'color' => '#dc3545'],
            'تحليل السكر في الدم' => ['type' => 'Dry Tube', 'color' => '#dc3545'],
            'Bilan Lipidique' => ['type' => 'Dry Tube', 'color' => '#dc3545'],
            'تحليل الكوليسترول والدهون' => ['type' => 'Dry Tube', 'color' => '#dc3545'],
            'PT' => ['type' => 'Citrate', 'color' => '#007bff'],
            'INR' => ['type' => 'Citrate', 'color' => '#007bff'],
            'سرعة الترسيب' => ['type' => 'Citrate', 'color' => '#007bff'],
        ];

        $updateCount = 0;
        foreach ($mappings as $name => $data) {
            $updated = \App\Models\Analyse::where('name', 'like', "%$name%")
                   ->orWhere('name_fr', 'like', "%$name%")
                   ->update([
                       'sample_type' => $data['type'],
                       'tube_color' => $data['color']
                   ]);
            $updateCount += $updated;
        }

        return response("<pre style='font-family:monospace;background:#111;color:#0f0;padding:20px'>✅ Migrations executed successfully!\n\n{$migrationOutput}\n\n✅ Data sync complete: {$updateCount} analyses updated with sample types and colors.</pre>");
    } catch (\Exception $e) {
        return response("<pre style='color:red'>❌ Error: {$e->getMessage()}</pre>", 500);
    }
});

Route::get('/run-seed-research', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'ResearchDataSeeder',
            '--force' => true,
        ]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("<pre style='font-family:monospace;background:#111;color:#0f0;padding:20px'>✅ 80 patients seeded (Groupe A + B)!\n\n{$output}\n\nOpen the Quality Dashboard to see results.</pre>");
    } catch (\Exception $e) {
        return response("<pre style='color:red'>❌ Error: {$e->getMessage()}\n\nRun /run-migrate first!</pre>", 500);
    }
});

// ─── Create storage symlink (fixes 404 on prescription images) ─────────────
Route::get('/run-storage-link', function () {
    try {
        // Remove broken symlink if exists
        $link = public_path('storage');
        if (is_link($link)) {
            unlink($link);
        }
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $output = \Illuminate\Support\Facades\Artisan::output();

        // Verify it worked
        $exists = file_exists(public_path('storage'));
        $status = $exists ? '✅ Symlink created successfully!' : '⚠️ Symlink may not have been created.';

        return response("<pre style='font-family:monospace;background:#111;color:#0f0;padding:20px'>{$status}\n\n{$output}\n\nPrescription files should now be accessible.</pre>");
    } catch (\Exception $e) {
        return response("<pre style='color:red'>❌ Error: {$e->getMessage()}</pre>", 500);
    }
});

// ─── Fix Storage Permissions (Ensures directories exist and are writable) ────
Route::get('/fix-storage', function () {
    try {
        $paths = [
            storage_path('app/public/prescriptions'),
            storage_path('app/public/results'),
            storage_path('framework/views'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('logs'),
        ];

        $results = [];
        foreach ($paths as $path) {
            if (!file_exists($path)) {
                mkdir($path, 0775, true);
                $results[] = "📁 Created: $path";
            }
            
            // Try to set permissions to 775
            chmod($path, 0775);
            $results[] = "✅ Fixed Perms: $path (775)";
        }

        $summary = implode("\n", $results);
        return response("<pre style='font-family:monospace;background:#111;color:#0f0;padding:20px'>🚀 STORAGE MAINTENANCE COMPLETE\n\n{$summary}</pre>");
    } catch (\Exception $e) {
        return response("<pre style='color:red'>❌ Error: {$e->getMessage()}\n\nPlease check if the /app/storage folder is owned by the web user (www-data).</pre>", 500);
    }
});

// ─── Secure Database Backup (Public with secret key) ─────────────────────────
Route::get('/secure-backup', function (\Illuminate\Http\Request $request) {
    $secret = $request->get('key');
    $expectedSecret = 'labo_dz_2026_safe'; // You can change this secret key

    if ($secret !== $expectedSecret) {
        return response("❌ Access Denied. Invalid Secret Key.", 403);
    }

    try {
        $filename = "backup_labo_dz_" . date('Y-m-d_H-i-s') . ".sql";
        $path = storage_path('app/' . $filename);

        // Database credentials from ENV
        $host = config('database.connections.mysql.host');
        
        // CRITICAL: Inside Docker, we MUST use the service name 'db' to reach the database.
        // Even if DB_HOST is set to a public IP in .env/Coolify, it often fails from inside.
        $host = 'db'; 
        
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        // Execute mysqldump with stderr capture
        $command = sprintf(
            'mysqldump --no-tablespaces -h %s -u %s -p%s %s 2>&1 > %s',
            $host,
            $username,
            $password,
            $database,
            escapeshellarg($path)
        );

        $output = [];
        $returnVar = -1;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $errorDetail = implode("\n", $output);
            // If mysqldump is missing, the output might be empty or "command not found"
            if (empty($errorDetail)) {
                $errorDetail = "Is 'mysqldump' installed? (You must Redeploy in Coolify after my last change).";
            }
            return response("❌ Backup failed. Error code: $returnVar\n\nDetails:\n$errorDetail", 500);
        }

        if (!file_exists($path)) {
            return response("❌ Backup file was not created.", 500);
        }

        return response()->download($path)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        return response("❌ Error: " . $e->getMessage(), 500);
    }
})->name('system.backup');


// ─── Serve any file from storage (no symlink needed) ─────────────────────────
// Handles paths like: prescriptions/abc.jpg OR abc.jpg
Route::get('/serve-file/{path}', function ($path) {
    // Decode and sanitize
    $path = urldecode($path);
    $path = ltrim($path, '/');

    // Security: block path traversal
    if (str_contains($path, '..')) {
        abort(403);
    }

    // Try multiple storage locations
    $candidates = [
        public_path($path),
        public_path('labnexa_lite.apk'),
        public_path('labnexa_v1.2.0.apk'),
        public_path('labnexa_v1.1.4.apk'),
        storage_path('app/public/' . $path),
        storage_path('app/' . $path),
        base_path($path),
        base_path('public/' . $path),
    ];

    $filePath = null;
    foreach ($candidates as $candidate) {
        if (file_exists($candidate)) {
            $filePath = $candidate;
            break;
        }
    }

    if (!$filePath) {
        return response()->json([
            'error'   => 'File not found',
            'tried'   => $candidates,
            'storage' => storage_path('app/public'),
        ], 404);
    }

    $mime = mime_content_type($filePath) ?: 'application/octet-stream';
    return response()->file($filePath, [
        'Content-Type'        => $mime,
        'Content-Disposition' => 'inline',
    ]);
})->where('path', '.*')->name('serve.file');

// Intercept direct download requests for missing APK files and serve them from root/storage
Route::get('/{filename}.apk', function ($filename) {
    $path = $filename . '.apk';
    
    // Security: block path traversal
    if (str_contains($path, '..')) {
        abort(403, 'Path traversal detected');
    }

    $candidates = [
        public_path($path),
        public_path('labnexa_lite.apk'),
        public_path('labnexa_v1.2.0.apk'),
        public_path('labnexa_v1.1.4.apk'),
        storage_path('app/public/' . $path),
        storage_path('app/' . $path),
        base_path($path),
        base_path('public/' . $path),
    ];

    $filePath = null;
    foreach ($candidates as $candidate) {
        if (file_exists($candidate)) {
            $filePath = $candidate;
            break;
        }
    }

    if (!$filePath) {
        abort(404, 'File not found');
    }

    return response()->file($filePath, [
        'Content-Type'        => 'application/vnd.android.package-archive',
        'Content-Disposition' => 'attachment; filename="' . basename($filePath) . '"',
    ]);
});

// Patient Secure Reports (Moved outside for guest access during registration)
Route::get('/report/view/{id}', [\App\Http\Controllers\PdfController::class, 'viewReservationReport'])
    ->name('report.view');
Route::get('/report/download/{id}', [\App\Http\Controllers\PdfController::class, 'generateReservationPdf'])
    ->name('patient.report.download');

Route::middleware('auth:patient,administrator,doctor')->group(function () {
    Route::get('/patient/eligibility-report/{id}', function(\Illuminate\Http\Request $request, $id) {
        $request->merge(['report_type' => 'eligibility']);
        return app(\App\Http\Controllers\PdfController::class)->viewReservationReport($request, $id, 'confirmed');
    })->name('patient.report.eligibility');
});

Route::get('/test-email-live', function () {
    try {
        $targetEmail = 'amar.houssam@univ-ouargla.dz';
        
        // 1. Plain text email
        \Illuminate\Support\Facades\Mail::raw('This is a PLAIN TEXT test email. If you receive this but not the PDF one, the issue is the attachment or HTML.', function ($message) use ($targetEmail) {
            $message->to($targetEmail)
                    ->subject('Test 1: Plain Text');
        });

        // 2. HTML template email without attachment
        \Illuminate\Support\Facades\Mail::send('emails.booking-confirmed', [
            'patientName' => 'Test User',
            'reservationId' => 999,
            'analysisDate' => '2026-06-05',
            'analysisTime' => '10:00',
            'analyses' => [],
            'smartMessage' => 'This is a test message without attachment.',
        ], function ($message) use ($targetEmail) {
            $message->to($targetEmail)
                    ->subject('Test 2: HTML Without Attachment');
        });

        return response()->json([
            'success' => true,
            'message' => 'Sent Test 1 and Test 2 to ' . $targetEmail
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Web Cron Endpoint (Bypasses Coolify container issues)
Route::get('/system/cron/run/{secret}', function ($secret) {
    if ($secret !== 'labnexa-secret-cron-2026') {
        abort(403);
    }
    
    set_time_limit(0);
    \Illuminate\Support\Facades\Artisan::call('schedule:run');
    
    return response()->json([
        'status' => 'success',
        'message' => 'Cron job executed successfully',
        'output' => \Illuminate\Support\Facades\Artisan::output()
    ]);
});
