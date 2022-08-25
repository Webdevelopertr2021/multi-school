<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route("user-login");
});


// User login
Route::get("/user-login","Auth\LoginController@loginForm")->name("user-login");
Route::post("/attemp-login","Auth\LoginController@attemptLogin")->name('user.login');

Route::post("/user-logout","Auth\LoginController@logout")->name("user-logout");
// End

// Storage resources
Route::group(["prefix" => "storage"],function(){
    Route::get("/user/photos/{filename}","Storage\StorageController@getUserPhoto")->name("user-photo");
    Route::get('/student/photo/{filename}',"Storage\StorageController@getStudentPhoto")->name("student-photo");
    Route::get("/bank/photo/{filename}","Storage\StorageController@getBankPhoto")->name("bank-attachment");
});
// end

// Admin actions
Route::group(["prefix"=>"admin","middleware"=>"auth"],function(){

    Route::get("/{any}","Admin\AdminPagesController@index")->where("any", "^(?!api/.*$).*");

    Route::group(["prefix" => "api"], function(){

        // Schools
        Route::post("/store-school","Admin\SchoolController@store");

        Route::get("/get-school-list","Admin\SchoolController@getList");

        Route::post("/update-school","Admin\SchoolController@update");

        Route::post("/delete-school","Admin\SchoolController@delete");

        Route::get("/get-school-data","Admin\SchoolController@getData");

        Route::get("/get-all-schools","Admin\SchoolController@getAllSchool");

        Route::post("/add-supervisor","Admin\SuperVisorController@addSupervisor");

        Route::post("/update-supervisor-profile","Admin\SuperVisorController@updateSupervisor");

        Route::post("/delete-supervisor","Admin\SuperVisorController@deleteSupervisor");

        Route::get("/get-supervisors","Admin\SuperVisorController@get");

        Route::post("/add-teacher","Admin\SuperVisorController@addTeacher");

        Route::get("/get-all-supervisors","Admin\SuperVisorController@getList");

        Route::get("/get-user-data","Admin\SuperVisorController@getUser");

        Route::get("/get-teacher-list-by-supervisor","Admin\TeacherController@getTeacherBySuperv");
        // 

        // class
        Route::post('/add-new-class',"Admin\ClassController@add");

        Route::get("/get-class-list","Admin\ClassController@getList");

        Route::post("/delete-class","Admin\ClassController@delete");

        Route::post('/update-class',"Admin\ClassController@update");
        // 

        // Section
        Route::post("/add-new-section","Admin\SectionController@store");

        Route::get("/get-section-list","Admin\SectionController@getList");

        Route::post("/delete-section","Admin\SectionController@delete");

        Route::get("/get-section-data","Admin\SectionController@getData");

        Route::post("/update-section","Admin\SectionController@update");
        // End

        // Students

        Route::post("/create-new-student","Admin\StudentController@store");

        Route::get("/get-student-list","Admin\StudentController@getStudentList");

        Route::post("/delete-student","Admin\StudentController@delete");

        Route::get("/get-student-data","Admin\StudentController@getData");

        Route::post("/update-student","Admin\StudentController@update");

        Route::post("/import-student","Admin\StudentController@importStudent");

        Route::get("/get-student-ratings","Admin\StudentController@getRatings");
        // End

        // Teacher
        Route::get("/get-teacher-list","Admin\TeacherController@list");

        Route::post("/delete-teacher","Admin\TeacherController@delete");

        Route::get('/get-edit-teacher-data',"Admin\TeacherController@getTeacherInfo");

        Route::post("/update-teacher","Admin\SuperVisorController@updateTeacher");

        Route::get("/get-teacher-ratings","Admin\TeacherController@getTeacherRating");

        Route::get("/get-teacher-data","Admin\SuperVisorController@getTeacherData");

        Route::get("/get-teachers-students","Admin\SuperVisorController@getStudentByTeacher");

        // End

        // Dashboard
        
        Route::get("/get-dashboard-data","Admin\AdminPagesController@getDashboardData");

        Route::get("/get-admin-list","Admin\AdminPagesController@adminList");

        Route::get("/get-manager-list","Admin\AdminPagesController@managerList");

        Route::post('/create-admin',"Admin\AdminPagesController@createAdmin");

        Route::post("/delete-admin","Admin\AdminPagesController@deleteAdmin");

        Route::get("/get-admin-data","Admin\AdminPagesController@getAdminData");

        Route::post("/update-admin","Admin\AdminPagesController@updateAdmin");

        Route::get("/get-my-profile-data","Admin\AdminPagesController@myProfile");

        Route::post('/update-my-profile',"Admin\AdminPagesController@updateMyProfile");

        Route::post('/create-manager',"Admin\AdminPagesController@createManager");

        Route::post("/update-manager","Admin\AdminPagesController@updateManager");

        // End

        // Leave request

        Route::get("/get-leave-request","Admin\LeaveRequestController@getList");

        // end

        // Salary

        Route::get("/get-salary-info","Admin\SalaryController@getSalaryData");
        
        Route::post("/submit-payment","Admin\SalaryController@submitPayment");

        Route::get("/payment-list","Admin\SalaryController@getPaymentList");

        Route::get("/filter-salary-data","Admin\SalaryController@filterSalaryData");
        // End


    });
});
// End

// Supervisors
Route::group(["prefix" => "supervisor", "middleware" => "auth:supervisor"],function(){

    Route::get("/{any}","Supervisor\SupervisorController@index")->where("any", "^(?!api/.*$).*$");

    Route::group(["prefix" => "api"],function() {

        Route::get("/get-teacher-list","Supervisor\SuperVisorController@getTeacher");

        Route::get("/check-user-review","Supervisor\SuperVisorController@checkUser");

        Route::post("/submit-review","Supervisor\RatingController@submit");

        Route::get("/get-leave-request","Supervisor\LeaveRequestController@getList");

        Route::post("/update-application-status","Supervisor\LeaveRequestController@updateStatus");

        Route::get("/get-teacher-ratings","Supervisor\RatingController@getTeacherRating");

        Route::get("/get-dashboard-data","Supervisor\DashboardController@getDashboardData");

        Route::get('/get-my-profile-data',"Supervisor\DashboardController@myProfile");

        Route::post('/update-my-profile',"Supervisor\DashboardController@updateMyProfile");

        Route::get("/get-teacher-data","Supervisor\SuperVisorController@getTeacherData");

        Route::get("/get-teachers-students","Supervisor\SuperVisorController@getStudentByTeacher");

        Route::get("/get-student-ratings","Supervisor\StudentController@getRatings");

    });

});
// End

// Teachers
Route::group(["prefix" => "teacher", "middleware" => "auth:teacher"], function(){

    Route::get("/{any}","Teacher\TeacherController@index")->where("any", "^(?!api/.*$).*$");
    
    Route::group(["prefix" => "api"],function(){

        Route::post("/submit-leave-request","Teacher\LeaveRequestController@submit");

        Route::get("/get-leave-data","Teacher\LeaveRequestController@getData");

        Route::get('/get-my-students',"Teacher\StudentController@getStudents");

        Route::get("/check-student-review","Teacher\StudentController@checkStudent");

        Route::post("/submit-review","Teacher\StudentController@submitReview");

        Route::get("/get-student-ratings","Teacher\StudentController@getRatings");

        Route::get("/get-dashboard-data","Teacher\DashboardController@getDashboardData");

        Route::get('/get-my-profile-data',"Teacher\DashboardController@myProfile");

        Route::post('/update-my-profile',"Teacher\DashboardController@updateMyProfile");

        Route::get("/get-payment-history","Teacher\SalaryController@getPaymentHistory");

        Route::post("/accept-payment","Teacher\SalaryController@acceptPayment");
    });
});
// End

// Manager
Route::group(["prefix" => "manager","middleware" => "auth:manager"],function(){

    Route::get("/{any}","Manager\ManagerController@index")->where("any", "^(?!api/.*$).*$");

    Route::group(["prefix" => "api"],function(){

        Route::get("/get-all-supervisors","Manager\SupervisorController@getList");

        Route::get("/get-my-students","Manager\StudentController@getStudents");

        Route::get("/get-teacher-list","Manager\TeacherController@getTeacher");

        Route::get("/get-teacher-data","Manager\TeacherController@getTeacherData");

        Route::get("/get-teachers-students","Manager\TeacherController@getStudentByTeacher");

        Route::get("/get-teacher-list-by-supervisor","Manager\TeacherController@getTeacherBySuperv");

        Route::get("/get-leave-request","Manager\LeaveRequestController@getList");

        Route::post("/update-application-status","Manager\LeaveRequestController@updateStatus");

        Route::get("/get-teacher-ratings","Manager\TeacherController@getTeacherRating");

        Route::get("/get-student-ratings","Manager\StudentController@getRatings");

        Route::get('/get-my-profile-data',"Manager\DashboardController@myProfile");

        Route::post('/update-my-profile',"Manager\DashboardController@updateMyProfile");

    });
    
});
// End

// User notification
Route::get("/user-notifications","NotificationController@getList");
// End