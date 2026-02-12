<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_upload_attachment_on_resource_create()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'manager',
            'accessible_modules' => ['resources'],
        ]);
        $this->actingAs($user);

        // Create a budget to allow resource creation
        Budget::create([
            'year' => 2024,
            'annual_budget' => 100000,
            'monthly_budget' => 10000,
            'remaining_budget' => 100000,
            'created_by' => $user->id,
        ]);

        $attachment = UploadedFile::fake()->create('resource_invoice.pdf', 100);

        $response = $this->post(route('resources.store'), [
            'title' => 'Test Resource',
            'amount' => 500,
            'type' => 'invoice',
            'month' => 5,
            'year' => 2024,
            'attachment' => $attachment,
        ]);

        $response->assertRedirect(route('resources.index', ['year' => 2024]));
        $this->assertDatabaseHas('resources', ['title' => 'Test Resource']);

        $resource = Resource::where('title', 'Test Resource')->first();
        $this->assertNotNull($resource->attachment);
        Storage::disk('public')->assertExists($resource->attachment);
    }

    public function test_can_update_resource_attachment()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'manager',
            'accessible_modules' => ['resources'],
        ]);
        $this->actingAs($user);

        Budget::create([
            'year' => 2024,
            'annual_budget' => 100000,
            'monthly_budget' => 10000,
            'remaining_budget' => 100000,
            'created_by' => $user->id,
        ]);

        $resource = Resource::create([
            'title' => 'Existing Resource',
            'amount' => 200,
            'type' => 'expense',
            'month' => 3,
            'year' => 2024,
            'created_by' => $user->id,
            'attachment' => 'resources/documents/old.pdf',
        ]);
        // Mock existing file
        Storage::disk('public')->put('resources/documents/old.pdf', 'content');

        $newAttachment = UploadedFile::fake()->create('new_invoice.pdf', 100);

        $response = $this->put(route('resources.update', $resource->id), [
            'title' => 'Updated Resource',
            'amount' => 200,
            'type' => 'expense',
            'month' => 3,
            'year' => 2024,
            'attachment' => $newAttachment,
        ]);

        $response->assertRedirect(route('resources.index', ['year' => 2024]));
        
        $resource->refresh();
        Storage::disk('public')->assertExists($resource->attachment);
        Storage::disk('public')->assertMissing('resources/documents/old.pdf');
    }

    public function test_attachment_is_deleted_when_resource_is_deleted()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'admin',
            'accessible_modules' => ['resources'],
        ]);
        $this->actingAs($user);

        Budget::create([
            'year' => 2024,
            'annual_budget' => 100000,
            'monthly_budget' => 10000,
            'remaining_budget' => 100000,
            'created_by' => $user->id,
        ]);
        
        $attachment = UploadedFile::fake()->create('delete_me.pdf', 100);
        
        $this->post(route('resources.store'), [
            'title' => 'To Delete',
            'amount' => 100,
            'type' => 'invoice',
            'month' => 10,
            'year' => 2024,
            'attachment' => $attachment,
        ]);

        $resource = Resource::where('title', 'To Delete')->first();
        Storage::disk('public')->assertExists($resource->attachment);

        $this->delete(route('resources.destroy', $resource->id));

        $this->assertDatabaseMissing('resources', ['id' => $resource->id]);
        Storage::disk('public')->assertMissing($resource->attachment);
    }
}
