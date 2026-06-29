<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EngagementLetterController;
use App\Http\Controllers\EngagementLetterTemplateController;
use App\Http\Controllers\SigningController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientServiceController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\CompaniesHouseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\ReportBuilderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorChallengeController;
use App\Http\Controllers\TwoFactorSetupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTwoFactorController;
use App\Http\Controllers\WebAuthn\WebAuthnRegisterController;
use Illuminate\Support\Facades\Route;

// Public signing routes (no auth required)
Route::get('/sign/{token}', [SigningController::class, 'show'])->name('sign.show');
Route::post('/sign/{token}', [SigningController::class, 'sign'])->name('sign.sign');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// 2FA challenge — accessible without full auth (user is pending)
Route::middleware('guest')->group(function () {
    Route::get('two-factor/challenge', [TwoFactorChallengeController::class, 'show'])->name('two-factor.challenge');
    Route::post('two-factor/challenge/totp', [TwoFactorChallengeController::class, 'verifyTotp'])->name('two-factor.totp');
    Route::post('two-factor/challenge/passkey/options', [TwoFactorChallengeController::class, 'passkeyOptions'])->name('two-factor.passkey.options');
    Route::post('two-factor/challenge/passkey', [TwoFactorChallengeController::class, 'verifyPasskey'])->name('two-factor.passkey.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('profile/preferences', [ProfileController::class, 'savePreference'])->name('profile.preferences.save');

    // 2FA setup
    Route::get('two-factor', [TwoFactorSetupController::class, 'show'])->name('two-factor.index');
    Route::post('two-factor/totp/enable', [TwoFactorSetupController::class, 'enableTotp'])->name('two-factor.totp.enable');
    Route::get('two-factor/totp/setup', [TwoFactorSetupController::class, 'showTotpSetup'])->name('two-factor.totp.setup');
    Route::post('two-factor/totp/confirm', [TwoFactorSetupController::class, 'confirmTotp'])->name('two-factor.totp.confirm');
    Route::post('two-factor/totp/disable', [TwoFactorSetupController::class, 'disableTotp'])->name('two-factor.totp.disable');
    Route::delete('two-factor/passkeys/{id}', [TwoFactorSetupController::class, 'deletePasskey'])->name('two-factor.passkey.delete');

    // Passkey registration
    Route::post('webauthn/register/options', [WebAuthnRegisterController::class, 'options'])->name('webauthn.register.options');
    Route::post('webauthn/register', [WebAuthnRegisterController::class, 'register'])->name('webauthn.register');

    Route::resource('clients', ClientController::class);
    Route::post('clients/{client}/services', [ClientServiceController::class, 'store'])->name('clients.services.store');
    Route::delete('clients/{client}/services/{service}', [ClientServiceController::class, 'destroy'])->name('clients.services.destroy');
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/urgent', [TaskController::class, 'toggleUrgent'])->name('tasks.urgent');
    Route::resource('services', ServiceController::class);
    Route::resource('renewals', RenewalController::class);
    Route::resource('engagement-letters', EngagementLetterController::class);
    Route::post('engagement-letters/{engagementLetter}/send', [EngagementLetterController::class, 'send'])->name('engagement-letters.send');
    Route::get('engagement-letters/{engagementLetter}/pdf', [EngagementLetterController::class, 'pdf'])->name('engagement-letters.pdf');

    Route::resource('jobs', JobController::class);
    Route::post('jobs/{job}/complete', [JobController::class, 'complete'])->name('jobs.complete');
    Route::patch('jobs/{job}/status', [JobController::class, 'updateStatus'])->name('jobs.status');

    Route::get('api/companies-house/search', [CompaniesHouseController::class, 'search'])->name('companies-house.search');
    Route::get('api/companies-house/{number}/officers', [CompaniesHouseController::class, 'officers'])->name('companies-house.officers');
    Route::get('api/companies-house/{number}', [CompaniesHouseController::class, 'profile'])->name('companies-house.profile');

    Route::post('impersonate/stop', [ImpersonateController::class, 'stop'])->name('impersonate.stop');
    Route::post('impersonate/{user}', [ImpersonateController::class, 'start'])->name('impersonate.start');

    Route::middleware('manager')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::post('users/{user}/2fa/totp/generate', [UserTwoFactorController::class, 'generateTotp'])->name('users.2fa.totp.generate');
        Route::post('users/{user}/2fa/totp/confirm', [UserTwoFactorController::class, 'confirmTotp'])->name('users.2fa.totp.confirm');
        Route::post('users/{user}/2fa/totp/disable', [UserTwoFactorController::class, 'disableTotp'])->name('users.2fa.totp.disable');
        Route::delete('users/{user}/2fa/passkeys/{id}', [UserTwoFactorController::class, 'deletePasskey'])->name('users.2fa.passkey.delete');
        Route::post('users/{user}/2fa/reset', [UserTwoFactorController::class, 'reset'])->name('users.2fa.reset');
        Route::resource('client-types', ClientTypeController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::get('activity', [ActivityController::class, 'index'])->name('activity.index');
        Route::get('changelog', [ChangelogController::class, 'index'])->name('changelog.index');
        Route::post('admin/engagement-letter-templates/reorder', [EngagementLetterTemplateController::class, 'reorder'])->name('admin.engagement-letter-templates.reorder');
        Route::resource('admin/engagement-letter-templates', EngagementLetterTemplateController::class)
            ->names([
                'index'   => 'admin.engagement-letter-templates.index',
                'create'  => 'admin.engagement-letter-templates.create',
                'store'   => 'admin.engagement-letter-templates.store',
                'edit'    => 'admin.engagement-letter-templates.edit',
                'update'  => 'admin.engagement-letter-templates.update',
                'destroy' => 'admin.engagement-letter-templates.destroy',
            ])
            ->parameters(['engagement-letter-templates' => 'engagementLetterTemplate']);
        Route::get('admin/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::get('admin/backup/export/{type}', [BackupController::class, 'export'])->name('backup.export');
        Route::get('admin/backup/template/{type}', [BackupController::class, 'template'])->name('backup.template');
        Route::post('admin/backup/import', [BackupController::class, 'import'])->name('backup.import');
        Route::get('changelog/pdf', [ChangelogController::class, 'pdf'])->name('changelog.pdf');
        Route::get('changelog/download', [ChangelogController::class, 'download'])->name('changelog.download');
        Route::post('reports/email', [ReportController::class, 'email'])->name('reports.email');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/fixed-prices', [ReportController::class, 'fixedPrices'])->name('reports.fixed-prices');
        Route::get('reports/fixed-prices/csv', [ReportController::class, 'fixedPricesCsv'])->name('reports.fixed-prices.csv');
        Route::get('reports/fixed-prices/pdf/{orientation}', [ReportController::class, 'fixedPricesPdf'])
            ->name('reports.fixed-prices.pdf')
            ->where('orientation', 'portrait|landscape');

        Route::get('reports/upcoming-jobs', [ReportController::class, 'upcomingJobs'])->name('reports.upcoming-jobs');
        Route::get('reports/upcoming-jobs/csv', [ReportController::class, 'upcomingJobsCsv'])->name('reports.upcoming-jobs.csv');
        Route::get('reports/upcoming-jobs/pdf/{orientation}', [ReportController::class, 'upcomingJobsPdf'])
            ->name('reports.upcoming-jobs.pdf')
            ->where('orientation', 'portrait|landscape');

        // Custom report builder
        Route::post('reports/custom/preview', [ReportBuilderController::class, 'preview'])->name('reports.custom.preview');
        Route::get('reports/custom/{savedReport}/run', [ReportBuilderController::class, 'run'])->name('reports.custom.run');
        Route::get('reports/custom/{savedReport}/csv', [ReportBuilderController::class, 'csv'])->name('reports.custom.csv');
        Route::get('reports/custom/{savedReport}/pdf/{orientation}', [ReportBuilderController::class, 'pdf'])->name('reports.custom.pdf')->where('orientation', 'portrait|landscape');
        Route::post('reports/custom/{savedReport}/email', [ReportBuilderController::class, 'email'])->name('reports.custom.email');
        Route::resource('reports/custom', ReportBuilderController::class)
            ->parameters(['custom' => 'savedReport'])
            ->names([
                'index'   => 'reports.custom.index',
                'create'  => 'reports.custom.create',
                'store'   => 'reports.custom.store',
                'show'    => 'reports.custom.show',
                'edit'    => 'reports.custom.edit',
                'update'  => 'reports.custom.update',
                'destroy' => 'reports.custom.destroy',
            ]);
    });
});
