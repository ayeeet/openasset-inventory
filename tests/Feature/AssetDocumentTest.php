<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_upload_agreement_and_invoice_on_create()
    {
        Storage::fake('public');
        
        $user = User::factory()->create([
            'role' => 'manager',
            'accessible_modules' => ['assets'],
        ]);
        $this->actingAs($user);

        $agreement = UploadedFile::fake()->create('agreement.pdf', 100);
        $invoice = UploadedFile::fake()->create('invoice.jpg', 100);

        $response = $this->post(route('assets.store'), [
            'name' => 'Test Asset Docs',
            'serial_number' => 'DOC123',
            'category' => 'Electronics',
            'status' => 'active',
            'agreement' => $agreement,
            'invoice' => $invoice,
        ]);

        $response->assertRedirect(route('assets.index'));
        $this->assertDatabaseHas('assets', ['name' => 'Test Asset Docs']);

        $asset = Asset::where('name', 'Test Asset Docs')->first();
        
        $this->assertNotNull($asset->agreement);
        $this->assertNotNull($asset->invoice);
        
        Storage::disk('public')->assertExists($asset->agreement);
        Storage::disk('public')->assertExists($asset->invoice);
    }

    public function test_can_update_asset_documents()
    {
        Storage::fake('public');
        
        $user = User::factory()->create([
            'role' => 'manager',
            'accessible_modules' => ['assets'],
        ]);
        $this->actingAs($user);

        // Manually create asset
        $asset = Asset::create([
            'name' => 'Existing Asset',
            'category' => 'Furniture',
            'status' => 'active',
        ]);

        $newAgreement = UploadedFile::fake()->create('new_agreement.pdf', 100);

        $response = $this->put(route('assets.update', $asset->id), [
            'name' => 'Existing Asset Updated',
            'category' => 'Furniture',
            'status' => 'active',
            'agreement' => $newAgreement,
        ]);

        $response->assertRedirect(route('assets.index'));
        
        $asset->refresh();
        $this->assertNotNull($asset->agreement);
        Storage::disk('public')->assertExists($asset->agreement);
    }
    
    public function test_documents_are_deleted_when_asset_is_deleted()
    {
        Storage::fake('public');
        
        $user = User::factory()->create([
            'role' => 'admin',
            'accessible_modules' => ['assets'],
        ]);
        
        $this->actingAs($user);

        $agreement = UploadedFile::fake()->create('agreement_to_delete.pdf', 100);
        
        // Use store to ensure file is saved correctly with path
        $this->post(route('assets.store'), [
            'name' => 'Asset To Delete',
            'category' => 'Misc',
            'status' => 'active',
            'agreement' => $agreement,
        ]);
        
        $asset = Asset::where('name', 'Asset To Delete')->first();
        $filePath = $asset->agreement;
        
        Storage::disk('public')->assertExists($filePath);

        $response = $this->delete(route('assets.destroy', $asset->id));
        $response->assertRedirect(route('assets.index'));
        
        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
        Storage::disk('public')->assertMissing($filePath);
    }
}
