<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_transaction_store()
    {
        $this->seed();

        $response = $this->postJson('/api/transaction', [
            "card_number" => "6011328012741707",
            "type" => "deposito",
            "amount" => 100000
        ]);

        $response->assertStatus(200);
    }

    public function test_transaction_store_badrequest()
    {
        $this->seed();

        $response = $this->postJson('/api/transaction', [
            "card_number" => "6011328012741707",
            "amount" => 100000
        ]);

        $response->assertStatus(400);
    }

    public function test_transaction_store_notfound()
    {
        $this->seed();

        $response = $this->postJson('/api/transaction', [
            "card_number" => "555",
            "type" => "deposito",
            "amount" => 100000
        ]);

        $response->assertStatus(404)->assertJson([
            'msg' => 'Cuenta No Encontrada',
        ]);;
    }

    public function test_transaction_store_moreamount()
    {
        $this->seed();

        $response = $this->postJson('/api/transaction', [
            "card_number" => "6011328012741707",
            "type" => "retiro",
            "amount" => 200000000
        ]);

        $response->assertStatus(200)->assertJson([
            'msg' => 'Saldo insuficiente',
        ]);
    }
    
    public function test_transaction_notification_failretire()
    {
        $this->seed();

        Notification::fake();

        $response = $this->postJson('/api/transaction', [
            "card_number" => "6011328012741707",
            "type" => "retiro",
            "amount" => 200000000
        ]);

        Notification::assertNotSentTo(
            [\App\User::where('name', 'Aldo Verdugo')->first()],
            FailedRetire::class
        );
    }

    
}
