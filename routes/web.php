<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveContribution;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CollectorController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionControllerForCollector;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeathReport;
use App\Http\Controllers\FilterAnalytics;
use App\Http\Controllers\MemberControllerForCollector;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\OfficialArchive;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportForCollector;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SelectMemberController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsNotificationController;
use App\Http\Controllers\SmsNotificationSavedController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/logtest', function() {
    Log::error('Test log: 500 error debugging');
    return 'Check storage/logs/laravel.log';
});
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
Route::get('/members/search', [DashboardController::class, 'search'])->name('members.search');
Route::get('/members/search-page', [DashboardController::class, 'searchPage'])
    ->name('members.searchPage');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/president/profile', [ProfileController::class, 'presidentProfile'])
    ->name('presidentProfile.show');
});

// admin 
Route::get('/add-new-member', [AdminController::class, 'addNewMember'])->name('addNewMember');
Route::get('/member/new', [AdminController::class, 'newlyRegistered'])->name('newlyRegistered');
Route::delete('/delete-member/{id}', [MembersController::class, 'destroy'])->name('deleteMember');
Route::post('/add-new-member-post', [AdminController::class, 'addMemberPost'])->name('addMemberPost');
Route::post('/add-beneficiary', [AdminController::class, 'addBeneficiary'])->name('addBeneficiary');
Route::delete('/delete-beneficiary/{id}', [AdminController::class, 'deleteBeneficiary'])->name('deleteBeneficiary');
Route::get('/beneficiaries/{member}', [MembersController::class, 'fetchBeneficiary'])->name('beneficiaries.fetch');
Route::get('/view-member-info/{id}', [MembersController::class, 'viewMemberInfo'])->name('viewMemberInfo');
Route::get('/edit-member-route/{id}', [MembersController::class, 'editMember'])->name('editMember');
Route::put('/members/{id}', [MembersController::class, 'update'])->name('members.update');
Route::put('/members/{id}/toggle-status', [MembersController::class, 'toggleStatus'])->name('toggleMemberStatus');
//contribution
Route::prefix('contribution')->name('contributions.')->middleware('auth')->group(function () {
    Route::get('/view-contributions', [ContributionController::class, 'index'])->name('index');
    Route::get('/add-contributions-route', [ContributionController::class, 'add'])->name('add');
    Route::post('/add-contributions-post', [ContributionController::class, 'store'])->name('store');
    Route::get('/toggle-purok/{purok}/{deceasedId}', [ContributionController::class, 'toggleContributionPurok'])->name('togglePurok');
    Route::get('/members-data', [ContributionController::class, 'getMembersData'])
    ->name('members.data');
    Route::get('/list', [ArchiveContribution::class, 'getContributions'])
    ->name('list');
});

//reports
Route::prefix('reports')->name('reports.')->middleware('auth')->group(function (){
    Route::get('/view-reports', [ReportController::class, 'index'])->name('index');
    Route::get('/add-collector-route', [ReportController::class, 'addCollector'])->name('addCollector');
     Route::post('/add-collector', [ReportController::class, 'storeCollector'])->name('storeCollector');
    Route::get('/deceased', [ReportController::class, 'getDeceased'])->name('deceased');
    Route::get('/contributions/{id}', [ReportController::class, 'getContributions'])->name('contributions');
});
    
// Official
Route::prefix('officials')->name('officials.')->middleware('auth')->group(function (){
    Route::get('/view-officials', [OfficialController::class, 'index'])->name('index');
    Route::get('/edit-officials-route/{id}', [OfficialController::class, 'edit'])->name('edit');
    Route::get('/president/has', [OfficialController::class, 'hasPresident'])->name('hasPresident');
    Route::put('/edit-officials-put/{id}', [OfficialController::class, 'editData'])->name('editData');
    Route::post('/add-official', [OfficialController::class, 'create'])->name('add');
    Route::get('/add-official-route', [OfficialController::class, 'addOfficialRoute'])->name('addOfficialRoute');
    Route::delete('/delete-official/{id}', [OfficialController::class, 'delete'])->name('delete');
    Route::patch('/status/toggle/{id}', [OfficialController::class, 'toggleStatus'])->name('status.toggle');
});

// Member Archive
Route::prefix('archive')->name('archive.')->middleware('auth')->group(function () {
    Route::get('/view-archive', [ArchiveController::class, 'index'])->name('index');
    Route::get('/view-deceased', [ArchiveController::class, 'viewDeceasedMember'])->name('viewDeceasedMember');
    Route::get('/view-info/{id}', [ArchiveController::class, 'view'])->name('view');
    Route::delete('/delete-permanently/{id}', [ArchiveController::class, 'deletePermanently'])->name('deleteMember');
    Route::post('/undo/{id}', [ArchiveController::class, 'undo'])->name('undo');
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
    Route::get('/officials/{official}/edit', [RoleController::class, 'editOfficial'])->name('official.edit');
    Route::put('/users/{user}', [RoleController::class, 'update'])->name('update');
    Route::put('/officials/{official}', [RoleController::class, 'updateOfficial'])->name('official.update');
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
    Route::get('/contributions/{id}/{purok}', [ReportForCollector::class, 'getContributions'])->name('contributions');
    Route::get('/notification/has', [CollectorController::class, 'hasNotification'])->name('hasNotification');
    Route::post('/notification/mark-as-read/{id}', [CollectorController::class, 'markAsRead'])->name('markAsRead');
});

Route::prefix('collector-contribution')->name('collectorContribution.')->middleware('auth')->group(function (){
    Route::get('/view-contribution-as-collector', [ContributionControllerForCollector::class, 'index'])->name('index');
    Route::get('/toggle-purok-as-collector/{purok}/{deceasedId}', [ContributionControllerForCollector::class, 'toggleContributionPurok'])->name('togglePurok');
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
    Route::post('/add-death-report', [SmsNotificationController::class, 'addDeathReport'])->name('addDeathReport');
    Route::post('/send-schedule-contribution', [SmsNotificationController::class, 'sendScheduleContribution'])->name('sendScheduleContribution');

    Route::post('/send-reminders', [SmsNotificationController::class, 'sendReminders'])->name('sendReminders');
    Route::post('/send-fund-updates', [SmsNotificationController::class, 'sendFundUpdates'])->name('sendFundUpdates');

    Route::get('/sms-notification', [SmsNotificationController::class, 'index'])->name('index');
    Route::get('/sms-page', [SmsNotificationController::class, 'smsPage'])->name('smsPage');
    Route::get('/death-report/select', [SmsNotificationController::class, 'selectDeceased'])->name('selectDeceased');
    Route::get('/send-to-all-selected/{type}/{message}/', [SmsNotificationController::class, 'sendToAllSelected'])->name('sendToAllSelected');
    Route::get('/send-to-all-selected/v2/{type}/{message}/{deceased}/{last}', [SmsNotificationController::class, 'sendToAllSelected'])->name('sendToAllSelectedV2');
});

Route::prefix('smsSelectMember')->name('smsSelectMember.')->middleware('auth')->group(function () {
    Route::post('/add-death-report', [SelectMemberController::class, 'addDeathReport'])->name('deathReport');
    Route::post('/send-schedule-contribution', [SelectMemberController::class, 'sendScheduleContribution'])->name('scheduleContribution');
    Route::post('/send-reminders', [SelectMemberController::class, 'sendReminders'])->name('reminders');
    Route::post('/send-fund-updates', [SelectMemberController::class, 'sendFundUpdates'])->name('fundUpdates');
});

// save only the message to the database not send in sms
Route::post('smsNotificationSaved/add-death-report', [SmsNotificationSavedController::class, 'send'])->name('smsNotificationSaved.send');

Route::prefix('deathReport')->name('deathReport.')->middleware('auth')->group(function (){
    Route::post('/undo/{id}', [DeathReport::class, 'undo'])->name('undo');
    Route::delete('/delete-permanently/{id}', [DeathReport::class, 'deletePermanently'])->name('deletePermanently');
});
Route::prefix('officialArchived')->name('officialArchived.')->middleware('auth')->group(function (){
    Route::post('/restore/{id}', [OfficialArchive::class, 'restoreOfficial'])->name('restoreOfficial');
});
Route::prefix('filterAnalytics')->name('filterAnalytics.')->middleware('auth')->group(function (){
    Route::get('/deceased/next/{id}', [FilterAnalytics::class, 'next'])->name('next');
    Route::get('/deceased/previous/{id}', [FilterAnalytics::class, 'previous'])->name('previous');
});
Route::prefix('filterContribution')->name('filterContribution.')->middleware('auth')->group(function (){
    Route::get('/contribution/toggle/{id}/{purok}', [ContributionController::class, 'toggle'])->name('toggle');
});
require __DIR__.'/auth.php';
