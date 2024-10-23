<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'title' => 'required|string|max:255',
            'contents' => 'required',
            'category_id' => 'required|exists:categories,id',
            'file' => 'nullable|file|mimes:jpg,png,pdf,docx',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            // Store file in storage/app/documents
            $filePath = $request->file('file')->store('documents');
        }

        // Create the document
        $document = Document::create([
            'title' => $request->title,
            'content' => $request->contents,
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
            'file' => $filePath,
        ]);

        return response()->json($document, 201);
    }

    public function deleteDocument($id)
    {
        $document = Document::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
    
        // Delete the file if it exists
        if ($document->file) {
            Storage::delete($document->file);
        }
    
        // Delete the document record from the database
        $document->delete();
    
        return response()->json(['message' => 'Document deleted successfully'], 200);
    }
    public function getUserDocuments()
    {
        $documents = Document::where('user_id', auth()->id())->get();

        return response()->json($documents);
    }
}
