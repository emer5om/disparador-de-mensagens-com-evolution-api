<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Campanhas
    Route::prefix('campaigns')->name('campaigns.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CampaignController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\CampaignController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\CampaignController::class, 'store'])->name('store');
        Route::get('/{campaign}', [App\Http\Controllers\Admin\CampaignController::class, 'show'])->name('show');
        Route::get('/{campaign}/edit', [App\Http\Controllers\Admin\CampaignController::class, 'edit'])->name('edit');
        Route::put('/{campaign}', [App\Http\Controllers\Admin\CampaignController::class, 'update'])->name('update');
        Route::delete('/{campaign}', [App\Http\Controllers\Admin\CampaignController::class, 'destroy'])->name('destroy');
        
        // Campaign actions
        Route::post('/{campaign}/start', [App\Http\Controllers\Admin\CampaignController::class, 'start'])->name('start');
        Route::post('/{campaign}/pause', [App\Http\Controllers\Admin\CampaignController::class, 'pause'])->name('pause');
        Route::post('/{campaign}/resume', [App\Http\Controllers\Admin\CampaignController::class, 'resume'])->name('resume');
        Route::post('/{campaign}/stop', [App\Http\Controllers\Admin\CampaignController::class, 'stop'])->name('stop');
        Route::post('/{campaign}/duplicate', [App\Http\Controllers\Admin\CampaignController::class, 'duplicate'])->name('duplicate');
        Route::post('/{campaign}/add-numbers', [App\Http\Controllers\Admin\CampaignController::class, 'addNumbers'])->name('add-numbers');
    });

    // Instâncias
    Route::prefix('instances')->name('instances.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\InstanceController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\InstanceController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\InstanceController::class, 'store'])->name('store');
        Route::get('/{instance}', [App\Http\Controllers\Admin\InstanceController::class, 'show'])->name('show');
        Route::get('/{instance}/edit', [App\Http\Controllers\Admin\InstanceController::class, 'edit'])->name('edit');
        Route::put('/{instance}', [App\Http\Controllers\Admin\InstanceController::class, 'update'])->name('update');
        Route::delete('/{instance}', [App\Http\Controllers\Admin\InstanceController::class, 'destroy'])->name('destroy');
        
        // Additional instance actions
        Route::post('/{instance}/restart', [App\Http\Controllers\Admin\InstanceController::class, 'restart'])->name('restart');
        Route::get('/{instance}/refresh-qr', [App\Http\Controllers\Admin\InstanceController::class, 'refreshQR'])->name('refresh-qr');
        Route::get('/{instance}/status', [App\Http\Controllers\Admin\InstanceController::class, 'status'])->name('status');
        Route::post('/{instance}/test', [App\Http\Controllers\Admin\InstanceController::class, 'test'])->name('test');
    });

    // Leads
    Route::prefix('leads')->name('leads.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\LeadController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\LeadController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\LeadController::class, 'store'])->name('store');
        Route::get('/{leadList}', [App\Http\Controllers\Admin\LeadController::class, 'show'])->name('show');
        Route::delete('/{leadList}', [App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('destroy');
        Route::get('/{leadList}/export', [App\Http\Controllers\Admin\LeadController::class, 'export'])->name('export');
        Route::get('/{leadList}/mapping', [App\Http\Controllers\Admin\LeadController::class, 'mapping'])->name('mapping');
        Route::post('/{leadList}/mapping', [App\Http\Controllers\Admin\LeadController::class, 'saveMapping'])->name('save-mapping');
    });

    // Relatórios
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/campaign/{campaign}', [App\Http\Controllers\Admin\ReportController::class, 'campaign'])->name('campaign');
        Route::get('/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public QR Code routes (no authentication required)
Route::prefix('qr')->name('qrcode.')->group(function () {
    Route::get('/{instanceKey}', [App\Http\Controllers\QRCodeController::class, 'show'])->name('show');
    Route::get('/api/{instanceKey}', [App\Http\Controllers\QRCodeController::class, 'api'])->name('api');
});

require __DIR__.'/auth.php';
