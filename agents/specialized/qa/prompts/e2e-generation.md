# E2E Test Generation Guide - QA

## End-to-End Testing for Condominios-Vzla

### 1. Critical User Flows to Test

#### Flow: Complete Payment Lifecycle
```php
public function test_complete_payment_lifecycle(): void
{
    // 1. Admin creates a common expense
    $admin = User::factory()->create();
    $expense = CommonExpense::factory()->create(['amount' => 1000]);

    // 2. System distributes expense to units
    $service = new CommonExpenseDistributionService();
    $result = $service->distribute($expense);

    // 3. Resident receives receipt
    $unit = Unit::first();
    $receipt = Receipt::where('unit_id', $unit->id)->first();
    $this->assertNotNull($receipt);
    $this->assertEquals('pending', $receipt->status);

    // 4. Resident makes payment
    $payment = Payment::create([
        'unit_id' => $unit->id,
        'receipt_id' => $receipt->id,
        'amount' => $receipt->amount,
        'payment_date' => now(),
        'ways_to_pay_id' => WaysToPays::first()->id,
    ]);

    // 5. Receipt status updates
    $receipt->refresh();
    $this->assertEquals('paid', $receipt->status);

    // 6. Payment appears in history
    $this->assertDatabaseHas('payments', [
        'unit_id' => $unit->id,
        'amount' => $receipt->amount,
    ]);
}
```

#### Flow: Common Area Booking
```php
public function test_complete_booking_lifecycle(): void
{
    $dweller = Dweller::factory()->create();
    $area = CommonArea::factory()->create();

    // 1. Resident books area
    $response = $this->actingAs($dweller->user)
        ->post(route('resident.common-areas.book'), [
            'common_area_id' => $area->id,
            'date' => now()->addDays(7)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'purpose' => 'Family gathering',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('common_area_bookings', [
        'common_area_id' => $area->id,
        'dweller_id' => $dweller->id,
        'status' => 'pending',
    ]);

    // 2. Admin approves booking
    $booking = CommonAreaBooking::first();
    $admin = User::factory()->create();
    $response = $this->actingAs($admin)
        ->put(route('bookings.update', $booking), [
            'status' => 'approved',
        ]);

    $booking->refresh();
    $this->assertEquals('approved', $booking->status);
}
```

#### Flow: Assembly Session with Voting
```php
public function test_assembly_session_with_voting(): void
{
    $condominium = Condominium::factory()->create();
    $session = AssemblySession::factory()->create([
        'condominium_id' => $condominium->id,
        'status' => 'scheduled',
    ]);

    // 1. Create motion
    $motion = Motion::factory()->create([
        'assembly_session_id' => $session->id,
        'description' => 'Increase common fee by 10%',
    ]);

    // 2. Dwellers vote
    $dwellers = Dweller::factory()->count(5)->create();
    foreach ($dwellers as $i => $dweller) {
        Vote::create([
            'motion_id' => $motion->id,
            'dweller_id' => $dweller->id,
            'vote' => $i < 3 ? 'yes' : 'no',
        ]);
    }

    // 3. Verify results
    $this->assertEquals(3, $motion->votes()->where('vote', 'yes')->count());
    $this->assertEquals(2, $motion->votes()->where('vote', 'no')->count());
    $this->assertTrue($motion->isApproved());
}
```

### 2. E2E Test Structure
```php
class PaymentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function end_to_end_payment_process()
    {
        // Given - Setup
        // When - Action
        // Then - Assertions
    }
}
```

### 3. Browser Testing (Optional - Laravel Dusk)
```php
// If Dusk is installed
public function test_login_flow_in_browser(): void
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
            ->type('email', 'admin@example.com')
            ->type('password', 'password')
            ->press('Sign In')
            ->assertPathIs('/admin/dashboard')
            ->assertSee('Dashboard');
    });
}
```

### 4. API Integration Tests
```php
public function test_full_api_workflow(): void
{
    // 1. Register
    $response = $this->postJson(route('api.auth.register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertCreated();

    // 2. Login
    $response = $this->postJson(route('api.auth.login'), [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);
    $token = $response->json('token');

    // 3. Access protected resource
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson(route('api.condominiums.index'));
    $response->assertOk();

    // 4. Logout
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson(route('api.auth.logout'));
    $response->assertOk();

    // 5. Verify token is revoked
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson(route('api.condominiums.index'));
    $response->assertUnauthorized();
}
```

### 5. Performance E2E Tests
```php
public function test_dashboard_loads_within_time_limit(): void
{
    $user = User::factory()->create();

    $start = microtime(true);
    $response = $this->actingAs($user)->get(route('dashboard.index'));
    $duration = microtime(true) - $start;

    $response->assertOk();
    $this->assertLessThan(2.0, $duration, 'Dashboard should load in under 2 seconds');
}
```
