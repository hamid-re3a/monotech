<?php

use Illuminate\Support\Facades\Route;
use Promotion\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use Promotion\Http\Controllers\Customer\PromotionController;

Route::middleware('user_activity')->group(function () {

   Route::middleware(['auth', 'block_user'])->group(function () {


       Route::middleware(['role:' . USER_ROLE_SUPER_ADMIN])->prefix('backoffice')->name('backoffice.')->group(function () {
            Route::post('promotion-codes',[AdminPromotionController::class, 'create'])->name('create');
            Route::get('promotion-codes/{promotionCode}',[AdminPromotionController::class, 'show'])->name('show');
            Route::get('promotion-codes',[AdminPromotionController::class, 'index'])->name('index');
       });

        Route::middleware(['email_verified'])->name('customer.')->group(function () {
            Route::post('assign-promotion',[PromotionController::class, 'assign'])->name('assign');
        });
    });
});
