<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Store;
use App\Models\BusinessDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessDetailsApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user + store for testing
        $this->user = User::factory()->create();
        $this->store = Store::factory()->create();
        $this->user->store_id = $this->store->id;
        $this->user->save();
    }

    /** @test */
    public function user_can_create_business_details()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/store-setup/business-details', [
            'business_name' => 'Test Business',
            'owner_name'    => 'John Doe',
            'email'         => 'test@business.com',
            'phone'         => '1234567890',
            'gst_number'    => 'GST12345',
            'address'       => '123 Main Street',
            'logo'          => UploadedFile::fake()->image('logo.png'),
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Business details saved successfully',
                 ]);

        $this->assertDatabaseHas('business_details', [
            'store_id'      => $this->store->id,
            'business_name' => 'Test Business',
        ]);

        // Assert logo file was stored
        Storage::disk('public')->assertExists(BusinessDetail::first()->logo);
    }

    /** @test */
    public function user_can_update_business_details()
    {
        // First, create
        BusinessDetail::create([
            'store_id'      => $this->store->id,
            'business_name' => 'Old Business',
            'owner_name'    => 'John Doe',
            'email'         => 'old@business.com',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/store-setup/business-details', [
            'business_name' => 'Updated Business',
            'owner_name'    => 'John Doe',
            'email'         => 'updated@business.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Business details saved successfully',
                 ]);

        $this->assertDatabaseHas('business_details', [
            'store_id'      => $this->store->id,
            'business_name' => 'Updated Business',
        ]);
    }

    /** @test */
    public function user_can_fetch_business_details()
    {
        $business = BusinessDetail::create([
            'store_id'      => $this->store->id,
            'business_name' => 'Test Business',
            'owner_name'    => 'John Doe',
            'email'         => 'test@business.com',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/store-setup/business-details');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'data' => [
                         'store_id' => $this->store->id,
                         'business_name' => 'Test Business',
                     ]
                 ]);
    }
}
