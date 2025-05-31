<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SuperiorityController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\TechnologyCategoryController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// User Routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Guest Routes
Route::middleware('guest')->group(function () {
    // Admin Authentication Routes
    Route::post('/admin/register', [AdminController::class, 'register']);
    Route::post('/admin/login', [AdminController::class, 'login']);
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/get-old-password', [ResetPasswordController::class, 'getOldPassword']);
    Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
    Route::get('/check-reset-token', [ResetPasswordController::class, 'checkResetToken']);

    // Public Resource Routes (index, show, edit)
    Route::resource('settings', SettingsController::class)->only(['index', 'show', 'edit']);
    Route::resource('project-categories', ProjectCategoryController::class)->only(['index', 'show', 'edit']);
    Route::resource('perusahaans', PerusahaanController::class)->only(['index', 'show', 'edit']);
    Route::resource('projects', ProjectController::class)->only(['index', 'show', 'edit']);
    Route::resource('services', ServiceController::class)->only(['index', 'show', 'edit']);
    Route::resource('team-members', TeamMemberController::class)->only(['index', 'show', 'edit']);
    Route::resource('galleries', GalleryController::class);
    Route::resource('superiorities', SuperiorityController::class)->only(['index', 'show', 'edit']);
    Route::resource('blogs', BlogController::class)->only(['index', 'show', 'edit']);
    Route::resource('careers', CareerController::class);
    Route::resource('vacancies', VacancyController::class)->only(['index', 'show', 'edit', 'store']);
    Route::resource('technology-categories', TechnologyCategoryController::class)->only(['index', 'show', 'edit']);
    Route::resource('technologies', TechnologyController::class)->only(['index', 'show', 'edit']);
    Route::resource('keywords', KeywordController::class)->only(['index', 'show', 'edit']);
    Route::resource('tags', TagController::class)->only(['index', 'show', 'edit']);
    Route::get('/vacancies/{id}/download/{type}', [VacancyController::class, 'downloadFile'])
    ->where('type', 'cv|portfolio'); // Membatasi type hanya untuk cv atau portfolio
    Route::patch('/vacancies/{id}/verify', [VacancyController::class, 'updateStatus']);


});

// Authenticated Routes for Admin (store, update, delete)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin', [AdminController::class, 'profile']);
    Route::get('/admin/all', [AdminController::class, 'all']);
    Route::match(['put', 'post'], '/admin/profile/store', [AdminController::class, 'store'])->name('admin.profile.store');
    Route::post('/admin/logout', [AdminController::class, 'logout']);
    Route::put('/admin/change-password', [AdminController::class, 'changePassword'])->name('admin.change-password');
    
    // Resource Routes for Admin Access Only (store, update, destroy)
    Route::resource('settings', SettingsController::class)->only(['store', 'update', 'destroy']);
    Route::resource('project-categories', ProjectCategoryController::class)->only(['store', 'update', 'destroy']);
    Route::resource('perusahaans', PerusahaanController::class)->only(['store', 'update', 'destroy']);
    Route::resource('projects', ProjectController::class)->only(['store', 'update', 'destroy']);
    Route::resource('services', ServiceController::class)->only(['store', 'update', 'destroy']);
    Route::resource('team-members', TeamMemberController::class)->only(['store', 'update', 'destroy']);
    // Route::resource('galleries', GalleryController::class)->only(['store', 'update', 'destroy']);
    Route::resource('superiorities', SuperiorityController::class)->only(['store', 'update', 'destroy']);
    Route::resource('blogs', BlogController::class)->only(['store', 'update', 'destroy']);
    Route::resource('tags', TagController::class)->only(['store', 'update', 'destroy']);
    // Route::resource('careers', CareerController::class)->only(['store', 'update', 'destroy']);
    // Route::resource('vacancies', VacancyController::class)->only(['destroy']);
 
    Route::resource('technology-categories', TechnologyCategoryController::class)->only(['store', 'update', 'destroy']);
    Route::resource('technologies', TechnologyController::class)->only(['store', 'update', 'destroy']);
    Route::resource('keywords', KeywordController::class)->only(['store', 'update', 'destroy']);

    // Log Activity Route for Admin Only
    Route::resource('log_activity', LogActivityController::class);
});
