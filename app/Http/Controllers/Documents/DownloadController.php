<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function __construct(protected DocumentService $documentService)
    {
    }

    public function __invoke(Document $document): StreamedResponse
    {
        // Verify file exists on the public disk
        abort_unless(Storage::disk('public')->exists($document->file_path), 404, 'File not found');
        
        // Validate access and log download (throws 403 if unauthorized)
        $this->documentService->downloadDocument($document, Auth::user());
        
        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name,
            ['Content-Type' => $document->mime_type]
        );
    }
}
