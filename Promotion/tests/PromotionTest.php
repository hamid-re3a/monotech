<?php


namespace Promotion\tests;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;
use User\UserConfigure;

class PromotionTest extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $this->app->setLocale('en');

        UserConfigure::seed();

    }



}
