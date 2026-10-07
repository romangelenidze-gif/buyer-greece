<?php

namespace Tests\Feature\Filament;

use App\Enums\PackageSourceType;
use App\Enums\PackageStatus;
use App\Filament\Resources\IncomingPackageResource\Pages\CreateIncomingPackage;
use App\Filament\Resources\IncomingPackageResource\Pages\ListIncomingPackages;
use App\Models\Customer;
use App\Models\IncomingPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IncomingPackageResourceTest extends TestCase
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

    public function test_can_render_incoming_packages_list(): void
    {
        $packages = IncomingPackage::factory()->count(2)->create([
            'customer_id' => $this->customer->id,
        ]);

        Livewire::test(ListIncomingPackages::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($packages);
    }

    public function test_can_receive_new_package_in_warehouse(): void
    {
        Livewire::test(CreateIncomingPackage::class)
            ->fillForm([
                'customer_id' => $this->customer->id,
                'public_package_number' => 'PKG-10001',
                'store_tracking_number' => 'TRK-123456789',
                'source_type' => PackageSourceType::BUYER_ORDER->value,
                'weight_kg' => 2.50,
                'status' => PackageStatus::RECEIVED_IN_GREECE->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('incoming_packages', [
            'public_package_number' => 'PKG-10001',
            'source_type' => PackageSourceType::BUYER_ORDER->value,
            'status' => PackageStatus::RECEIVED_IN_GREECE->value,
        ]);
    }
}