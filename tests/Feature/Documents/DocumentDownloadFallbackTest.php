<?php

declare(strict_types=1);

namespace Tests\Feature\Documents;

use App\Models\Branch;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentDownloadFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_private_file_returns_not_found_instead_of_public_fallback(): void
    {
        config(['filesystems.document_disk' => 'local']);
        Storage::fake('local');
        Storage::fake('public');

        $branch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $branch->id]);

        Permission::findOrCreate('documents.download', 'web');
        $user->givePermissionTo('documents.download');

        $document = Document::create([
            'title' => 'Secure Document',
            'code' => 'DOC-SEC',
            'file_name' => 'secure.pdf',
            'file_path' => 'documents/secure.pdf',
            'file_size' => 100,
            'file_type' => 'pdf',
            'mime_type' => 'application/pdf',
            'status' => 'published',
            'branch_id' => $branch->id,
            'uploaded_by' => $user->id,
            'is_public' => false,
            'access_level' => 'private',
        ]);

        Storage::disk('public')->put($document->file_path, 'public copy');

        $this->actingAs($user);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('File not found');

        app(DocumentService::class)->downloadDocument($document, $user);
    }
}
