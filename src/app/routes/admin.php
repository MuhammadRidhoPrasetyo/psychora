<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\TestTypeController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', UserController::class)->except(['create', 'store']);

    // Subscription Plans
    Route::resource('plans', SubscriptionPlanController::class)->except(['show']);

    // Programs
    Route::resource('programs', ProgramController::class)->except(['show']);

    // Test Types
    Route::resource('test-types', TestTypeController::class)->except(['show']);

    // Tests
    Route::resource('tests', TestController::class);

    // Test Sections (nested under tests)
    Route::post('tests/{test}/sections', [SectionController::class, 'store'])->name('tests.sections.store');
    Route::put('tests/{test}/sections/{section}', [SectionController::class, 'update'])->name('tests.sections.update');
    Route::delete('tests/{test}/sections/{section}', [SectionController::class, 'destroy'])->name('tests.sections.destroy');

    // Questions (nested under tests)
    Route::get('tests/{test}/questions', [QuestionController::class, 'index'])->name('tests.questions.index');
    Route::get('tests/{test}/questions/create', [QuestionController::class, 'create'])->name('tests.questions.create');
    Route::post('tests/{test}/questions', [QuestionController::class, 'store'])->name('tests.questions.store');
    Route::get('tests/{test}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('tests.questions.edit');
    Route::put('tests/{test}/questions/{question}', [QuestionController::class, 'update'])->name('tests.questions.update');
    Route::delete('tests/{test}/questions/{question}', [QuestionController::class, 'destroy'])->name('tests.questions.destroy');

    // Payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
});
