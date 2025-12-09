<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/departments', [DepartmentController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    /**
     * Admin-only routes
     */
    Route::middleware('check.admin')->group(function () {
        // Approvals & conflicts
        Route::get('/admin/pending-approvals', [AdminController::class, 'pendingApprovals']);
        Route::post('/admin/check-conflicts', [AdminController::class, 'checkConflicts']);
        Route::post('/admin/events/{event}/approve', [AdminController::class, 'approveEvent']);
        Route::post('/admin/events/{event}/reject', [AdminController::class, 'rejectEvent']);
        Route::post('/admin/announcements/{announcement}/approve', [AdminController::class, 'approveAnnouncement']);
        Route::post('/admin/announcements/{announcement}/reject', [AdminController::class, 'rejectAnnouncement']);

        // Admin analytics / dashboard (moved to DashboardController)
        Route::get('/admin/analytics/dashboard', [DashboardController::class, 'adminDashboard']);

        // Admin CRUD resources
        Route::apiResource('departments', DepartmentController::class)->except(['index']);
        Route::apiResource('organizations', OrganizationController::class);
        Route::apiResource('locations', LocationController::class);
        Route::apiResource('users', UserController::class);

        // Audit logs
        Route::get('/audit-logs', [AuditLogController::class, 'index']);
    });

    /**
     * Org admin routes
     */
    Route::middleware('check.org_admin')->group(function () {
        // Org dashboard
        Route::get('/org/dashboard', [DashboardController::class, 'orgDashboard']);

        // Events
        Route::post('/events', [EventController::class, 'store']);
        Route::get('/events/my-submissions', [EventController::class, 'mySubmissions']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
        Route::post('/events/{event}/submit', [EventController::class, 'submitForApproval']);

        // Announcements
        Route::post('/announcements', [AnnouncementController::class, 'store']);
        Route::get('/announcements/my-submissions', [AnnouncementController::class, 'mySubmissions']);
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update']);
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy']);
        Route::post('/announcements/{announcement}/submit', [AnnouncementController::class, 'submitForApproval']);
    });

    /**
     * Shared read-only routes
     */
    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);

    // Locations
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/locations/{location}', [LocationController::class, 'show']);

    // Organizations
    Route::get('/organizations', [OrganizationController::class, 'index']);
});
