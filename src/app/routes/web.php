<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TestTakingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Subscription
    Route::get('subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::get('subscription/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('subscription/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::get('subscription/status', [SubscriptionController::class, 'status'])->name('subscription.status');

    // Catalog
    Route::get('catalog/programs', [CatalogController::class, 'programs'])->name('catalog.programs');
    Route::get('catalog/programs/{program}', [CatalogController::class, 'programDetail'])->name('catalog.programs.show');
    Route::get('catalog/packages', [CatalogController::class, 'packages'])->name('catalog.packages');

    // Test Taking
    Route::get('tests', [TestTakingController::class, 'index'])->name('tests.index');
    Route::get('tests/{slug}', [TestTakingController::class, 'show'])->name('tests.show');
    Route::post('tests/{slug}/start', [TestTakingController::class, 'start'])->name('tests.start');
    Route::get('tests/attempts/{attempt}', [TestTakingController::class, 'take'])->name('tests.take');
    Route::post('tests/attempts/{attempt}/answer', [TestTakingController::class, 'saveAnswer'])->name('tests.answer');
    Route::post('tests/attempts/{attempt}/submit', [TestTakingController::class, 'submit'])->name('tests.submit');
    Route::get('tests/attempts/{attempt}/result', [TestTakingController::class, 'result'])->name('tests.result');
});

require __DIR__.'/settings.php';
