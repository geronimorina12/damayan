<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CollectorController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionControllerForCollector;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberControllerForCollector;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\OfficialArchive;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportForCollector;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsNotificationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/registered-member', [DashboardController::class, 'registeredMember'])->name('members.registered');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// admin 
Route::get('/add-new-member', [AdminController::class, 'addNewMember'])->name('addNewMember');
Route::delete('/delete-member/{id}', [MembersController::class, 'destroy'])->name('deleteMember');
Route::post('/add-new-member-post', [AdminController::class, 'addMemberPost'])->name('addMemberPost');
Route::post('/add-beneficiary', [AdminController::class, 'addBeneficiary'])->name('addBeneficiary');
Route::delete('/delete-beneficiary/{id}', [AdminController::class, 'deleteBeneficiary'])->name('deleteBeneficiary');
Route::get('/view-member-info/{id}', [MembersController::class, 'viewMemberInfo'])->name('viewMemberInfo');
Route::get('/edit-member-route/{id}', [MembersController::class, 'editMember'])->name('editMember');
Route::put('/members/{id}/toggle-status', [MembersController::class, 'toggleStatus'])->name('toggleMemberStatus');
//contribution
Route::prefix('contribution')->name('contributions.')->middleware('auth')->group(function () {
    Route::get('/view-contributions', [ContributionController::class, 'index'])->name('index');
    Route::get('/add-contributions-route', [ContributionController::class, 'add'])->name('add');
    Route::post('/add-contributions-post', [ContributionController::class, 'store'])->name('store');
    Route::get('/toggle-purok/{purok}', [ContributionController::class, 'toggleContributionPurok'])->name('togglePurok');
});

//reports
Route::prefix('reports')->name('reports.')->middleware('auth')->group(function (){
    Route::get('/view-reports', [ReportController::class, 'index'])->name('index');
    Route::get('/add-collector-route', [ReportController::class, 'addCollector'])->name('addCollector');
     Route::post('/add-collector', [ReportController::class, 'storeCollector'])->name('storeCollector');
});

// Official
Route::prefix('officials')->name('officials.')->middleware('auth')->group(function (){
    Route::get('/view-officials', [OfficialController::class, 'index'])->name('index');
    Route::get('/edit-officials-route/{id}', [OfficialController::class, 'edit'])->name('edit');
    Route::put('/edit-officials-put/{id}', [OfficialController::class, 'editData'])->name('editData');
    Route::post('/add-official', [OfficialController::class, 'create'])->name('add');
    Route::get('/add-official-route', [OfficialController::class, 'addOfficialRoute'])->name('addOfficialRoute');
    Route::delete('/delete-official/{id}', [OfficialController::class, 'delete'])->name('delete');
});

// Member Archive
Route::prefix('archive')->name('archive.')->middleware('auth')->group(function () {
    Route::get('/view-archive', [ArchiveController::class, 'index'])->name('index');
    Route::get('/view-info/{id}', [ArchiveController::class, 'view'])->name('view');
    Route::delete('/delete-permanently/{id}', [ArchiveController::class, 'deletePermanently'])->name('deleteMember');
});
//!!! Hiwalay ang Member tas Official archive kay since magka-iba 
//ang data tas table san duwa
//Official archive
Route::prefix('officialArchive')->name('officialArchive.')->middleware('auth')->group(function () {
    Route::get('/view-official-archive', [OfficialArchive::class, 'index'])->name('viewOfficials');
    Route::delete('/delete-official-permanently/{id}', [OfficialArchive::class, 'deletePermanently'])->name('deleteOfficial');
});

//settings
Route::prefix('settings')->name('settings.')->middleware('auth')->group(function () {
    Route::get('/view-settings', [SettingsController::class, 'index'])->name('viewSettings');
});

//Role management
Route::prefix('role')->name('role.')->middleware('auth')->group(function () {
    Route::get('/view-roles', [RoleController::class, 'index'])->name('index');
    Route::get('/users/{user}/edit', [RoleController::class, 'edit'])->name('edit');
    Route::put('/users/{user}', [RoleController::class, 'update'])->name('update');
    Route::get('/add-user-route', [RoleController::class, 'add'])->name('add');
    Route::post('/add-user-post', [RoleController::class, 'addUser'])->name('addUser');
    Route::delete('/delete-user/{user}', [RoleController::class, 'destroy'])->name('deleteUser');
    Route::get('view-specific-role/{role}', [RoleController::class, 'viewSpecificRole'])->name('viewSpecificRole');
});

//Collector
Route::prefix('collector')->name('collector.')->middleware('auth')->group(function () {
    Route::get('/collector-dashboard', [CollectorController::class, 'index'])->name('collectorDashboard');
    Route::get('/view-members-as-collector', [MemberControllerForCollector::class, 'index'])->name('viewMembersAsCollector');
    Route::get('/view-report-as-collector', [ReportForCollector::class, 'index'])->name('viewReportAsCollector');
    Route::get('/toggle-status-as-collector/{status}/{purok}', [ReportForCollector::class, 'toggleStatus'])->name('toggleStatus');
    Route::get('/toggle-purok-as-collector/{status}/{purok}', [ReportForCollector::class, 'togglePurok'])->name('togglePurok');
});
Route::prefix('collector-contribution')->name('collectorContribution.')->middleware('auth')->group(function (){
    Route::get('/view-contribution-as-collector', [ContributionControllerForCollector::class, 'index'])->name('index');
    Route::get('/toggle-purok-as-collector/{purok}', [ContributionControllerForCollector::class, 'toggleContributionPurok'])->name('togglePurok');
    Route::delete('/delete-contribution/{id}', [ContributionControllerForCollector::class, 'deleteContribution'])->name('deleteContribution');
});
Route::prefix('collectorProfile')->name('collectorProfile.')->middleware('auth')->group(function (){
    Route::get('/view-profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/edit-profile-page', [ProfileController::class, 'editRoute'])->name('editRoute');
    Route::patch('/edit-profile', [ProfileController::class, 'update'])->name('update');
});

Route::prefix('activityLog')->name('activityLog.')->middleware('auth')->group(function () {
    Route::get('/view-activity-logs', [ActivityLogController::class, 'index'])->name('index');
    Route::get('/view-user/{id}', [ActivityLogController::class, 'viewUser'])->name('viewUser');
    Route::get('/activity-log/{id}/edit', [ActivityLogController::class, 'edit'])->name('edit');
    Route::put('/activity-log/{id}', [ActivityLogController::class, 'update'])->name('update');
});

Route::prefix('smsNotification')->name('smsNotification.')->middleware('auth')->group(function () {
    Route::get('/sms-notification', [SmsNotificationController::class, 'index'])->name('index'); // Para sa settings > sms notifications
    Route::get('/sms-page', [SmsNotificationController::class, 'smsPage'])->name('smsPage'); // Para sms na route (yung sa sidenav)
    Route::post('/add-death-report', [SmsNotificationController::class, 'addDeathReport'])->name('addDeathReport');
    Route::post('/send', [SmsNotificationController::class, 'send'])->name('send'); // For sending SMS
    Route::get('/death-report/select', [SmsNotificationController::class, 'selectDeceased'])->name('selectDeceased');
});
require __DIR__.'/auth.php';
