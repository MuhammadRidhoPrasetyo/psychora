<?php

use App\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Routes untuk user (peserta tes).
| Semua route menggunakan middleware auth + verified.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // User Dashboard
    Route::get('/dashboard', [User\DashboardController::class, 'index'])->name('dashboard');

    // Program Catalog (Browsing)
    Route::prefix('programs')->name('programs.')->group(function () {
        Route::get('/', [User\ProgramCatalogController::class, 'index'])->name('index');
        Route::get('/{program}', [User\ProgramCatalogController::class, 'show'])->name('show');
        Route::get('/{program}/packages', [User\ProgramCatalogController::class, 'packages'])->name('packages');
        Route::get('/{program}/tests', [User\ProgramCatalogController::class, 'tests'])->name('tests');
    });

    // Subscription & Payment
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/plans', [User\SubscriptionController::class, 'plans'])->name('plans');
        Route::get('/checkout/{plan}', [User\SubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/subscribe', [User\SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/payment/{subscription}', [User\SubscriptionController::class, 'processPayment'])->name('payment');
        Route::get('/history', [User\SubscriptionController::class, 'history'])->name('history');
        Route::get('/invoices/{payment}', [User\SubscriptionController::class, 'invoice'])->name('invoices');
    });

    // Generic Test Engine
    Route::prefix('tests')->name('tests.')->group(function () {
        Route::post('/{test}/start', [User\GenericTestController::class, 'start'])->name('start');
        Route::get('/take/{attempt}', [User\GenericTestController::class, 'take'])->name('take');
        Route::post('/take/{attempt}/answer', [User\GenericTestController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/take/{attempt}/submit', [User\GenericTestController::class, 'submit'])->name('submit');
        Route::get('/result/{attempt}', [User\GenericTestController::class, 'result'])->name('result');
    });

    // DISC Test Engine
    Route::prefix('disc')->name('disc.')->group(function () {
        Route::post('/{form}/start', [User\DiscTestController::class, 'start'])->name('start');
        Route::get('/take/{attempt}', [User\DiscTestController::class, 'take'])->name('take');
        Route::post('/take/{attempt}/answer', [User\DiscTestController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/take/{attempt}/submit', [User\DiscTestController::class, 'submit'])->name('submit');
        Route::get('/result/{attempt}', [User\DiscTestController::class, 'result'])->name('result');
    });

    // IST Test Engine
    Route::prefix('ist')->name('ist.')->group(function () {
        Route::post('/{form}/start', [User\IstTestController::class, 'start'])->name('start');
        Route::get('/take/{attempt}', [User\IstTestController::class, 'take'])->name('take');
        Route::post('/take/{attempt}/answer', [User\IstTestController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/take/{attempt}/submit-subtest', [User\IstTestController::class, 'submitSubtest'])->name('submit-subtest');
        Route::post('/take/{attempt}/next-subtest', [User\IstTestController::class, 'nextSubtest'])->name('next-subtest');
        Route::get('/result/{attempt}', [User\IstTestController::class, 'result'])->name('result');
    });

    // Kraepelin Test Engine
    Route::prefix('kraepelin')->name('kraepelin.')->group(function () {
        Route::post('/{form}/start', [User\KraepelinTestController::class, 'start'])->name('start');
        Route::get('/take/{attempt}', [User\KraepelinTestController::class, 'take'])->name('take');
        Route::post('/take/{attempt}/answer', [User\KraepelinTestController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/take/{attempt}/batch-answer', [User\KraepelinTestController::class, 'saveBatchAnswers'])->name('batch-answer');
        Route::post('/take/{attempt}/submit', [User\KraepelinTestController::class, 'submit'])->name('submit');
        Route::get('/result/{attempt}', [User\KraepelinTestController::class, 'result'])->name('result');
    });

    // Psychotest Engine
    Route::prefix('psychotest')->name('psychotest.')->group(function () {
        Route::post('/{form}/start', [User\PsychotestTestController::class, 'start'])->name('start');
        Route::get('/take/{attempt}', [User\PsychotestTestController::class, 'take'])->name('take');
        Route::post('/take/{attempt}/answer', [User\PsychotestTestController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/take/{attempt}/submit', [User\PsychotestTestController::class, 'submit'])->name('submit');
        Route::get('/result/{attempt}', [User\PsychotestTestController::class, 'result'])->name('result');
    });

    // Bookmarks
    Route::prefix('bookmarks')->name('bookmarks.')->group(function () {
        Route::get('/', [User\BookmarkController::class, 'index'])->name('index');
        Route::post('/', [User\BookmarkController::class, 'store'])->name('store');
        Route::delete('/{bookmark}', [User\BookmarkController::class, 'destroy'])->name('destroy');
    });

    // History & Progress
    Route::prefix('history')->name('history.')->group(function () {
        Route::get('/', [User\HistoryController::class, 'index'])->name('index');
        Route::get('/{attempt}', [User\HistoryController::class, 'show'])->name('show');
        Route::get('/progress', [User\HistoryController::class, 'progress'])->name('progress');
    });

    // User Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [User\ProfileController::class, 'show'])->name('show');
        Route::put('/', [User\ProfileController::class, 'update'])->name('update');
    });
});
