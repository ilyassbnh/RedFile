<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDoc extends Controller
{
    // List all documents
    public function index()
    {
        $documents = Document::with('category')->get();
        return response()->json($documents);
    }

    // Store a new document
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'file' => 'nullable|file|mimes:jpg,png,pdf,docx|max:2048',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('documents');
        }

        $document = Document::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'user_id' => auth()->id(), // assuming authenticated user
            'file' => $filePath,
        ]);

        return response()->json($document, 201);
    }

    // Show a single document
    public function show($id)
    {
        $document = Document::with('category')->findOrFail($id);
        return response()->json($document);
    }

    // Update an existing document
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // Validate the incoming data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'file' => 'nullable|file|mimes:jpg,png,pdf,docx|max:2048',
        ]);

        // Prepare the data for update
        $updateData = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            // Store the new file
            $filePath = $request->file('file')->store('documents', 'public');

            // Add the file path to the update data
            $updateData['file'] = $filePath;

            // Delete old file if exists
            if ($document->file) {
                Storage::disk('public')->delete($document->file);
            }
        }

        // Update the document with the validated data
        $document->update($updateData);

        return response()->json($document);
    }


    // Delete a document
    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        if ($document->file) {
            Storage::delete($document->file);
        }

        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }
}

