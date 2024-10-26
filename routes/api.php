<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\LeavePolicyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CompanyPolicyController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesignationPositionController;
use App\Http\Controllers\TaskHistoryController;
use App\Http\Controllers\BacklinkController;

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
// Employee Routes
Route::post('admin/employees/create', [EmployeeController::class, 'createEmployee']);
Route::post('admin/employees/fetch', [EmployeeController::class, 'fetchEmployees']);
Route::post('admin/employees/remove', [EmployeeController::class, 'updateTrashStatus']);

// Attendance Routes
Route::post('/check-in', [CheckinController::class, 'checkIn']);
Route::post('/check-out', [CheckinController::class, 'checkOut']);

// New route to fetch check-in records
Route::post('/checkrecord', [CheckinController::class, 'getCheckIns']);

Route::post('admin/attendance/fetch', [AttendanceController::class, 'fetchAttendance']);
Route::post('admin/attendance/action', [AttendanceController::class, 'actionAttendance']);


Route::get('/designations-and-positions', [DesignationPositionController::class, 'fetchDesignationsAndPositions']);


// Leave Routes
Route::post('/apply-leave', [LeaveController::class, 'applyLeave']);
Route::post('/all-holiday', [LeaveController::class, 'getAllHolidays']);

Route::post('/leaves/fetch', [LeaveController::class, 'getLeavesByUserId']);
Route::post('/leaves/fetchAdmin', [LeaveController::class, 'getLeaves']);
Route::post('leaves/updateStatus', [LeaveController::class, 'updateLeaveStatus']);

Route::get('/leave-policies', [LeavePolicyController::class, 'index']);
Route::put('/leave-policies/{id}', [LeavePolicyController::class, 'update']);
// Fetch all company policies
Route::get('company-policies', [CompanyPolicyController::class, 'index']);
Route::post('company-policies', [CompanyPolicyController::class, 'store']);
Route::put('company-policies/{id}', [CompanyPolicyController::class, 'update']);

// Notification Routes
Route::get('tasks/fetch', [TaskController::class, 'fetchTasks']);
Route::post('tasks/create', [TaskController::class, 'createTask']);
Route::post('tasks/update', [TaskController::class, 'updateTask']);

Route::post('/task-history', [TaskHistoryController::class, 'getTaskHistory']);
Route::post('/comments-fetch', [TaskCommentController::class, 'fetchComments']); // Fetch comments
Route::post('/comments-create', [TaskCommentController::class, 'createComment']); // Create comment
Route::post('/comments-update', [TaskCommentController::class, 'updateComment']); // Update comment
Route::post('/comments-delete', [TaskCommentController::class, 'deleteComment']); // Delete comment

// Task Comment Routes
Route::post('admin/task-comments/fetch', [TaskCommentController::class, 'fetchTaskComments']);
Route::get('admin/task-comments/{id}', [TaskCommentController::class, 'show']);
Route::delete('admin/task-comments/{id}', [TaskCommentController::class, 'destroy']);

Route::post('/appointments', [AppointmentController::class, 'fetchlist']);
Route::post('/appointments/store', [AppointmentController::class, 'store']);
Route::post('/appointments/update', [AppointmentController::class, 'update']);
Route::post('/appointments/remove', [AppointmentController::class, 'Remove']);
Route::post('/appointments/status', [AppointmentController::class, 'statusUpdate']);



Route::post('/backlinks', [BacklinkController::class, 'fetchList']);         // Fetch a list of backlinks
Route::post('/backlinks/store', [BacklinkController::class, 'store']);       // Store a new backlink
Route::post('/backlinks/update', [BacklinkController::class, 'update']);     // Update an existing backlink
Route::post('/backlinks/remove', [BacklinkController::class, 'remove']);     // Remove (soft delete) an existing backlink
Route::post('/backlinks/status', [BacklinkController::class, 'statusUpdate']); // Update the status of an existing backlink
Route::post('/backlinks/update-checked', [BacklinkController::class, 'updateChecked']); // Update checked status for multiple backlinks

// Setting Routes
Route::post('admin/settings/fetch', [SettingController::class, 'fetchSettings']);
Route::get('admin/settings/{id}', [SettingController::class, 'show']);
Route::put('admin/settings/{id}', [SettingController::class, 'update']);
Route::delete('admin/settings/{id}', [SettingController::class, 'destroy']);