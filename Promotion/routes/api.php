<?php

use Illuminate\Support\Facades\Route;
use Promotion\Http\Controllers\Admin\PromotionController as AdminPromotionController;

Route::middleware('user_activity')->group(function () {

   Route::middleware(['auth', 'block_user'])->group(function () {


       Route::middleware(['role:' . USER_ROLE_SUPER_ADMIN])->prefix('backoffice')->name('backoffice.')->group(function () {
            Route::post('/promotion',[AdminPromotionController::class, 'create'])->name('create');
       });

        Route::middleware(['email_verified'])->group(function () {
            Route::post('/promotion',[PromotionController::class, 'create'])->name('create');

        });
    });
});
