<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    /**
     * Display a listing of the notes.
     * GET /api/notes
     */
    public function index(): JsonResponse
    {
        $notes = Note::orderBy('updated_at', 'desc')->get();
        return response()->json($notes);
    }

    /**
     * Store a newly created note.
     * POST /api/notes
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $note = Note::create($validated);
        return response()->json($note, 201);
    }

    /**
     * Display the specified note.
     * GET /api/notes/{id}
     */
    public function show(Note $note): JsonResponse
    {
        return response()->json($note);
    }

    /**
     * Update the specified note.
     * PUT /api/notes/{id}
     */
    public function update(Request $request, Note $note): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $note->update($validated);
        return response()->json($note);
    }

    /**
     * Remove the specified note.
     * DELETE /api/notes/{id}
     */
    public function destroy(Note $note): JsonResponse
    {
        $note->delete();
        return response()->json(null, 204);
    }
}
