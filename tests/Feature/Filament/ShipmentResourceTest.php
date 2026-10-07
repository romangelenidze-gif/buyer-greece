<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ShipmentResource\Pages\CreateShipment;
use App\Filament\Resources\ShipmentResource\Pages\ListShipments;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShipmentResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'super_admin',
        ]);
        $this->customer = Customer::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_can_render_shipments_list(): void
    {
        $shipments = Shipment::factory()->count(2)->create([
            'customer_id' => $this->customer->id,
        ]);

        Livewire::test(ListShipments::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($shipments);
    }

    public function test_can_create_shipment(): void
    {
        Livewire::test(CreateShipment::class)
            ->fillForm([
                'customer_id' => $this->customer->id,
                'public_shipment_number' => 'SHP-999888',
                'status' => 'preparing',
                'carrier' => 'CAMEX',
                'destination_country' => 'Грузия',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('shipments', [
            'public_shipment_number' => 'SHP-999888',
            'carrier' => 'CAMEX',
        ]);
    }
}