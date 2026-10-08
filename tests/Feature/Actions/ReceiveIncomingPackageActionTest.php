<?php

namespace Tests\Feature\Actions;

use App\Actions\Packages\ReceiveIncomingPackageAction;
use App\Enums\PackageStatus;
use App\Models\IncomingPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiveIncomingPackageActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_receives_incoming_package_with_custom_attributes(): void
    {
        $package = IncomingPackage::factory()->create([
            'dimensions' => '10x10x10',
            'internal_note' => 'Initial note',
        ]);

        $action = app(ReceiveIncomingPackageAction::class);
        $result = $action->execute(
            $package,
            2.5,
            '20x20x20',
            'Updated note',
            PackageStatus::cases()[0]
        );

        $this->assertEquals(2.5, $result->weight_kg);
        $this->assertEquals('20x20x20', $result->dimensions);
        $this->assertEquals('Updated note', $result->internal_note);
        $this->assertEquals(PackageStatus::cases()[0], $result->status);
        $this->assertNotNull($result->received_at);
    }
}