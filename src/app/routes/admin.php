<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes untuk Super Admin panel.
| Semua route di-prefix dengan 'admin/' dan menggunakan middleware auth + role admin.
|
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', Admin\UserController::class);
    Route::patch('users/{user}/toggle-active', [Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');

    // Programs
    Route::resource('programs', Admin\ProgramController::class)->except('show');
    Route::get('programs/{program}/test-types', [Admin\ProgramTestTypeController::class, 'index'])->name('programs.test-types.index');
    Route::post('programs/{program}/test-types', [Admin\ProgramTestTypeController::class, 'store'])->name('programs.test-types.store');
    Route::delete('programs/{program}/test-types/{testType}', [Admin\ProgramTestTypeController::class, 'destroy'])->name('programs.test-types.destroy');

    // Test Types
    Route::resource('test-types', Admin\TestTypeController::class)->except('show');

    // Subscription Plans
    Route::resource('subscription-plans', Admin\SubscriptionPlanController::class);

    // Subscriptions Management
    Route::get('subscriptions', [Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/{subscription}', [Admin\SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::patch('subscriptions/{subscription}/status', [Admin\SubscriptionController::class, 'updateStatus'])->name('subscriptions.update-status');

    // Payments Management
    Route::get('payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::patch('payments/{payment}/status', [Admin\PaymentController::class, 'updateStatus'])->name('payments.update-status');

    // Test Packages
    Route::resource('test-packages', Admin\TestPackageController::class);

    // Tests (Generic Test Engine)
    Route::resource('tests', Admin\TestController::class);
    Route::patch('tests/{test}/status', [Admin\TestController::class, 'updateStatus'])->name('tests.update-status');

    // Test Sections
    Route::get('tests/{test}/sections', [Admin\TestSectionController::class, 'index'])->name('tests.sections.index');
    Route::post('tests/{test}/sections', [Admin\TestSectionController::class, 'store'])->name('tests.sections.store');
    Route::put('tests/{test}/sections/{section}', [Admin\TestSectionController::class, 'update'])->name('tests.sections.update');
    Route::delete('tests/{test}/sections/{section}', [Admin\TestSectionController::class, 'destroy'])->name('tests.sections.destroy');
    Route::post('tests/{test}/sections/reorder', [Admin\TestSectionController::class, 'reorder'])->name('tests.sections.reorder');

    // Questions (per Test)
    Route::get('tests/{test}/questions', [Admin\QuestionController::class, 'index'])->name('tests.questions.index');
    Route::get('tests/{test}/questions/create', [Admin\QuestionController::class, 'create'])->name('tests.questions.create');
    Route::post('tests/{test}/questions', [Admin\QuestionController::class, 'store'])->name('tests.questions.store');
    Route::get('tests/{test}/questions/{question}', [Admin\QuestionController::class, 'show'])->name('tests.questions.show');
    Route::get('tests/{test}/questions/{question}/edit', [Admin\QuestionController::class, 'edit'])->name('tests.questions.edit');
    Route::put('tests/{test}/questions/{question}', [Admin\QuestionController::class, 'update'])->name('tests.questions.update');
    Route::delete('tests/{test}/questions/{question}', [Admin\QuestionController::class, 'destroy'])->name('tests.questions.destroy');
    Route::post('tests/{test}/questions/reorder', [Admin\QuestionController::class, 'reorder'])->name('tests.questions.reorder');

    // CPNS Module
    Route::prefix('cpns')->name('cpns.')->group(function () {
        Route::get('blueprints', [Admin\Cpns\CpnsTestBlueprintController::class, 'index'])->name('blueprints.index');
        Route::post('blueprints', [Admin\Cpns\CpnsTestBlueprintController::class, 'store'])->name('blueprints.store');
        Route::put('blueprints/{blueprint}', [Admin\Cpns\CpnsTestBlueprintController::class, 'update'])->name('blueprints.update');
        Route::delete('blueprints/{blueprint}', [Admin\Cpns\CpnsTestBlueprintController::class, 'destroy'])->name('blueprints.destroy');

        Route::get('score-rules', [Admin\Cpns\CpnsScoreRuleController::class, 'index'])->name('score-rules.index');
        Route::post('score-rules', [Admin\Cpns\CpnsScoreRuleController::class, 'store'])->name('score-rules.store');
        Route::put('score-rules/{scoreRule}', [Admin\Cpns\CpnsScoreRuleController::class, 'update'])->name('score-rules.update');
        Route::delete('score-rules/{scoreRule}', [Admin\Cpns\CpnsScoreRuleController::class, 'destroy'])->name('score-rules.destroy');
    });

    // DISC Module
    Route::prefix('disc')->name('disc.')->group(function () {
        Route::resource('forms', Admin\Disc\DiscFormController::class);

        Route::get('forms/{form}/questions', [Admin\Disc\DiscQuestionController::class, 'index'])->name('questions.index');
        Route::get('forms/{form}/questions/create', [Admin\Disc\DiscQuestionController::class, 'create'])->name('questions.create');
        Route::post('forms/{form}/questions', [Admin\Disc\DiscQuestionController::class, 'store'])->name('questions.store');
        Route::get('forms/{form}/questions/{question}/edit', [Admin\Disc\DiscQuestionController::class, 'edit'])->name('questions.edit');
        Route::put('forms/{form}/questions/{question}', [Admin\Disc\DiscQuestionController::class, 'update'])->name('questions.update');
        Route::delete('forms/{form}/questions/{question}', [Admin\Disc\DiscQuestionController::class, 'destroy'])->name('questions.destroy');
    });

    // IST Module
    Route::prefix('ist')->name('ist.')->group(function () {
        Route::resource('forms', Admin\Ist\IstFormController::class);

        // Subtests per Form
        Route::get('forms/{form}/subtests', [Admin\Ist\IstSubtestController::class, 'index'])->name('subtests.index');
        Route::post('forms/{form}/subtests', [Admin\Ist\IstSubtestController::class, 'store'])->name('subtests.store');
        Route::put('forms/{form}/subtests/{subtest}', [Admin\Ist\IstSubtestController::class, 'update'])->name('subtests.update');
        Route::delete('forms/{form}/subtests/{subtest}', [Admin\Ist\IstSubtestController::class, 'destroy'])->name('subtests.destroy');

        // Form Items (pivot config)
        Route::get('forms/{form}/form-items', [Admin\Ist\IstFormItemController::class, 'index'])->name('form-items.index');
        Route::post('forms/{form}/form-items', [Admin\Ist\IstFormItemController::class, 'store'])->name('form-items.store');
        Route::put('forms/{form}/form-items/{formItem}', [Admin\Ist\IstFormItemController::class, 'update'])->name('form-items.update');
        Route::delete('forms/{form}/form-items/{formItem}', [Admin\Ist\IstFormItemController::class, 'destroy'])->name('form-items.destroy');

        // Instructions per Form
        Route::get('forms/{form}/instructions', [Admin\Ist\IstInstructionController::class, 'index'])->name('instructions.index');
        Route::post('forms/{form}/instructions', [Admin\Ist\IstInstructionController::class, 'store'])->name('instructions.store');
        Route::put('forms/{form}/instructions/{instruction}', [Admin\Ist\IstInstructionController::class, 'update'])->name('instructions.update');
        Route::delete('forms/{form}/instructions/{instruction}', [Admin\Ist\IstInstructionController::class, 'destroy'])->name('instructions.destroy');

        // Clues per Form
        Route::get('forms/{form}/clues', [Admin\Ist\IstClueController::class, 'index'])->name('clues.index');
        Route::post('forms/{form}/clues', [Admin\Ist\IstClueController::class, 'store'])->name('clues.store');
        Route::put('forms/{form}/clues/{clue}', [Admin\Ist\IstClueController::class, 'update'])->name('clues.update');
        Route::delete('forms/{form}/clues/{clue}', [Admin\Ist\IstClueController::class, 'destroy'])->name('clues.destroy');

        // Subtest Questions
        Route::get('subtests/{subtest}/questions', [Admin\Ist\IstSubtestQuestionController::class, 'index'])->name('subtest-questions.index');
        Route::post('subtests/{subtest}/questions', [Admin\Ist\IstSubtestQuestionController::class, 'store'])->name('subtest-questions.store');
        Route::put('subtests/{subtest}/questions/{subtestQuestion}', [Admin\Ist\IstSubtestQuestionController::class, 'update'])->name('subtest-questions.update');
        Route::delete('subtests/{subtest}/questions/{subtestQuestion}', [Admin\Ist\IstSubtestQuestionController::class, 'destroy'])->name('subtest-questions.destroy');
        Route::post('subtests/{subtest}/questions/reorder', [Admin\Ist\IstSubtestQuestionController::class, 'reorder'])->name('subtest-questions.reorder');
    });

    // Kraepelin Module
    Route::prefix('kraepelin')->name('kraepelin.')->group(function () {
        Route::resource('forms', Admin\Kraepelin\KraepelinFormController::class)->except('show');
    });

    // Psychotest Module
    Route::prefix('psychotest')->name('psychotest.')->group(function () {
        // Aspects
        Route::resource('aspects', Admin\Psychotest\PsychotestAspectController::class);

        // Characteristics per Aspect
        Route::get('aspects/{aspect}/characteristics', [Admin\Psychotest\PsychotestCharacteristicController::class, 'index'])->name('characteristics.index');
        Route::get('aspects/{aspect}/characteristics/create', [Admin\Psychotest\PsychotestCharacteristicController::class, 'create'])->name('characteristics.create');
        Route::post('aspects/{aspect}/characteristics', [Admin\Psychotest\PsychotestCharacteristicController::class, 'store'])->name('characteristics.store');
        Route::get('aspects/{aspect}/characteristics/{characteristic}', [Admin\Psychotest\PsychotestCharacteristicController::class, 'show'])->name('characteristics.show');
        Route::get('aspects/{aspect}/characteristics/{characteristic}/edit', [Admin\Psychotest\PsychotestCharacteristicController::class, 'edit'])->name('characteristics.edit');
        Route::put('aspects/{aspect}/characteristics/{characteristic}', [Admin\Psychotest\PsychotestCharacteristicController::class, 'update'])->name('characteristics.update');
        Route::delete('aspects/{aspect}/characteristics/{characteristic}', [Admin\Psychotest\PsychotestCharacteristicController::class, 'destroy'])->name('characteristics.destroy');

        // Forms
        Route::resource('forms', Admin\Psychotest\PsychotestFormController::class);

        // Questions per Form
        Route::get('forms/{form}/questions', [Admin\Psychotest\PsychotestQuestionController::class, 'index'])->name('questions.index');
        Route::get('forms/{form}/questions/create', [Admin\Psychotest\PsychotestQuestionController::class, 'create'])->name('questions.create');
        Route::post('forms/{form}/questions', [Admin\Psychotest\PsychotestQuestionController::class, 'store'])->name('questions.store');
        Route::get('forms/{form}/questions/{question}/edit', [Admin\Psychotest\PsychotestQuestionController::class, 'edit'])->name('questions.edit');
        Route::put('forms/{form}/questions/{question}', [Admin\Psychotest\PsychotestQuestionController::class, 'update'])->name('questions.update');
        Route::delete('forms/{form}/questions/{question}', [Admin\Psychotest\PsychotestQuestionController::class, 'destroy'])->name('questions.destroy');
    });

    // Test Attempts (Admin Viewing)
    Route::get('test-attempts', [Admin\TestAttemptController::class, 'index'])->name('test-attempts.index');
    Route::get('test-attempts/{attempt}', [Admin\TestAttemptController::class, 'show'])->name('test-attempts.show');

    // Activity Logs
    Route::get('activity-logs', [Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activityLog}', [Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
});
