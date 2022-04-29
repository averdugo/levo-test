<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\{Transaction, Account, User};

class DatabaseTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    
    public function test_database_useradmin()
    {
        $this->seed();

        $this->assertDatabaseHas('users',[
            'email' => 'admin@test.com',
        ]);
    }

    public function test_database_account()
    {
        $this->assertDatabaseHas('accounts',[
            'card_number' => '6011328012741707',
        ]);
    }


    public function test_database_account_has_transactions()
    {
        $account = new Account;
        $this->assertInstanceOf(Collection::class, $account->transactions );
    }


}
