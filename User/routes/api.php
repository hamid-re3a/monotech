<?php

use Illuminate\Support\Facades\Route;
use User\Http\Controllers\Admin\EmailContentController;
use User\Http\Controllers\Admin\LoginAttemptSettingController;
use User\Http\Controllers\Admin\RoleController;
use User\Http\Controllers\Admin\TranslateController;
use User\Http\Controllers\Admin\UserActivityController as AdminUserActivityController;
use User\Http\Controllers\Admin\UserController as AdminUserController;
use User\Http\Controllers\Front\ActivityController;
use User\Http\Controllers\Front\AuthController;
use User\Http\Controllers\GeneralController;
use User\Http\Controllers\Front\LoginSecurityController;
use User\Http\Controllers\Front\SettingController;
use User\Http\Controllers\Admin\SettingController as AdminSettingController;
use User\Http\Controllers\Front\UserController;
use User\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::middleware('user_activity')->group(function () {

    Route::get('all_settings', [SettingController::class, 'index'])->name('all-settings');

    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::middleware(['auth', 'block_user'])->group(function () {
        Route::middleware(['email_verified'])->group(function () {
            Route::post('/add2fa_on_token', [LoginSecurityController::class, 'add2faOnToken'])->name('add-2fa-on-token')->middleware(['2fa']);
            Route::middleware(['token_passed_2fa'])->group(function () {
                Route::middleware(['role:' . USER_ROLE_SUPER_ADMIN])->prefix('admin')->name('admin.')->group(function () {

                    Route::name('user.')->prefix('users')->group(function () {
                        Route::post('/counts', [AdminDashboardController::class, 'counts'])->name('user-data-counts');
                        Route::post('/create_user', [AdminUserController::class, 'createUserByAdmin'])->name('create-user');
                        Route::post('', [AdminUserController::class, 'index'])->name('users-list');
                        Route::post('/user', [AdminUserController::class, 'getUser'])->name('user-data');
                        Route::patch('/', [AdminUserController::class, 'update'])->name('update');
                        Route::patch('/reset_password', [AdminUserController::class, 'resetPassword'])->name('reset-password');
                        Route::post('/block_or_unblock_user', [AdminUserController::class, 'blockOrUnblockUser'])->name('block-or-unblock-user-account');
                        Route::post('/activate_or_deactivate_user', [AdminUserController::class, 'activateOrDeactivateUserAccount'])->name('activate-or-deactivate-user-account');
                        Route::post('/freeze_or_unfreeze_user', [AdminUserController::class, 'freezeOrUnfreezeUserAccount'])->name('freeze-or-unfreeze-user-account');

                        Route::post('/verify_email_user', [AdminUserController::class, 'verifyUserEmailAccount'])->name('verify-email-user-account');
                        Route::get('/user_email_verification_history', [AdminUserController::class, 'emailVerificationHistory'])->name('user-email-verification-history');
                        Route::get('/user_login_history', [AdminUserController::class, 'loginHistory'])->name('user-login-history');
                        Route::get('/user_block_history', [AdminUserController::class, 'blockHistory'])->name('user-block-history');
                        Route::get('/user_password_history', [AdminUserController::class, 'passwordHistory'])->name('password-history');

                        Route::name('activities.')->prefix('activities')->group(function () {
                            Route::post('list', [AdminUserActivityController::class, 'index'])->name('index');
                            Route::post('user', [AdminUserActivityController::class, 'userActivity'])->name('user-list');
                        });

                    });

                    Route::prefix('login-attempts-settings')->name('login-attempts-settings')->group(function () {
                        Route::get('', [LoginAttemptSettingController::class, 'index'])->name('index');
                        Route::post('', [LoginAttemptSettingController::class, 'store'])->name('store');
                        Route::patch('', [LoginAttemptSettingController::class, 'update'])->name('update');
                        Route::delete('', [LoginAttemptSettingController::class, 'delete'])->name('delete');
                    });

                    Route::prefix('settings')->group(function () {
                        Route::get('', [AdminSettingController::class, 'index'])->name('index');
                        Route::patch('', [AdminSettingController::class, 'update'])->name('update');
                    });

                    Route::prefix('email-content')->name('email-content')->group(function () {
                        Route::get('', [EmailContentController::class, 'index'])->name('index');
                        Route::patch('', [EmailContentController::class, 'update'])->name('update');
                    });

                    Route::prefix('translates')->name('translates.')->group(function () {
                        Route::get('/', [TranslateController::class, 'index'])->name('list');
                        Route::get('/unfinished', [TranslateController::class, 'unfinished'])->name('unfinished');
                        Route::post('/show', [TranslateController::class, 'show'])->name('show');
                        Route::post('/store', [TranslateController::class, 'store'])->name('store');
                        Route::patch('/update', [TranslateController::class, 'update'])->name('update');
                        Route::delete('/delete', [TranslateController::class, 'destroy'])->name('destroy');
                    });

                    Route::prefix('role')->name("role")->group(function () {
                        Route::get('/get_roles', [RoleController::class, 'getAllRoles'])->name('get-roles');
                        Route::post('/create', [RoleController::class, 'createRole'])->name('create-roles');
                    });


                });

                Route::middleware(['role:client'])->name('customer.')->group(function () {
                    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
                    Route::get('/user', [AuthController::class, 'getAuthUser'])->name('current-user');

                    Route::post('/generate2fa_secret', [LoginSecurityController::class, 'generate2faSecret'])->name('2fa-secret');
                    Route::post('/generate2fa_enable', [LoginSecurityController::class, 'enable2fa'])->name('2fa-enable');
                    Route::post('/generate2fa_disable', [LoginSecurityController::class, 'disable2fa'])->name('2fa-disable')->middleware(['2fa']);

                    Route::middleware('has_valid_package')->group(function () {
                        Route::post('sponsor_new_user', [UserController::class, 'sponsor'])->name('sponsor-new-user');

                        Route::prefix('profile_management')->group(function () {
                            Route::get('', [UserController::class, 'getDetails'])->name('user-profile-detail');
                            Route::post('change_password', [UserController::class, 'changePassword'])->name('change-password');
                            Route::post('update_personal_details', [UserController::class, 'updatePersonalDetails'])->name('update-personal-details');
                        });


                        Route::prefix('activities')->name('activities.')->group(function () {
                            Route::get('/', [ActivityController::class, 'index'])->name('full-list');
                        });

                        Route::prefix('general')->name('general.')->group(function () {
                            Route::get('user/details/{member_id}', [GeneralController::class, 'getUserDetails'])->name('user-details');
                            Route::get('user/avatar/{member_id}', [GeneralController::class, 'getAvatarDetails'])->name('avatar-details');
                        });

                    });

                });
            });
        });
    });


    Route::middleware(['block_user'])->group(function () {
        Route::name('auth.')->group(function () {
            Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware(['login_attempt']);
            Route::post('/get_email_verify_token', [AuthController::class, 'askForEmailVerificationOtp'])->name('ask-for-email-otp');
            Route::post('/verify_email_token', [AuthController::class, 'verifyEmailOtp'])->name('verify-email-otp');
            Route::post('/forgot_password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
            Route::post('/reset_forgot_password', [AuthController::class, 'resetForgetPassword'])->name('reset-forgot-password');
        });
        Route::get('/ping', [AuthController::class, 'ping'])->name('ping');
    });
});
