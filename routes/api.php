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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/departments', [DepartmentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::middleware('check.admin')->group(function () {
        Route::get('/admin/pending-approvals', [AdminController::class, 'pendingApprovals']);
        Route::post('/admin/check-conflicts', [AdminController::class, 'checkConflicts']);
        Route::post('/admin/events/{event}/approve', [AdminController::class, 'approveEvent']);
        Route::post('/admin/events/{event}/reject', [AdminController::class, 'rejectEvent']);
        Route::post('/admin/announcements/{announcement}/approve', [AdminController::class, 'approveAnnouncement']);
        Route::post('/admin/announcements/{announcement}/reject', [AdminController::class, 'rejectAnnouncement']);

        Route::get('/admin/analytics/dashboard', [DashboardController::class, 'adminDashboard']);

        Route::apiResource('departments', DepartmentController::class)->except(['index']);
        Route::apiResource('organizations', OrganizationController::class);
        Route::apiResource('locations', LocationController::class);
        Route::apiResource('users', UserController::class);

        Route::get('/audit-logs', [AuditLogController::class, 'index']);
    });

    Route::middleware('check.org_admin')->group(function () {
        Route::get('/org/dashboard', [DashboardController::class, 'orgDashboard']);

        Route::post('/events', [EventController::class, 'store']);
        Route::get('/events/my-submissions', [EventController::class, 'mySubmissions']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
        Route::post('/events/{event}/submit', [EventController::class, 'submitForApproval']);

        Route::post('/announcements', [AnnouncementController::class, 'store']);
        Route::get('/announcements/my-submissions', [AnnouncementController::class, 'mySubmissions']);
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update']);
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy']);
        Route::post('/announcements/{announcement}/submit', [AnnouncementController::class, 'submitForApproval']);
    });


    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);

    Route::get('/user/registrations', [EventController::class, 'myRegistrations']);
    Route::post('/events/{event}/register', [EventController::class, 'register']);
    Route::delete('/events/{event}/register', [EventController::class, 'cancelRegistration']);
    Route::get('/events/{event}/registrations', [EventController::class, 'getRegistrations']);

    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);

    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/locations/{location}', [LocationController::class, 'show']);

    Route::get('/organizations', [OrganizationController::class, 'index']);
});
