<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    /**
     * Display a listing of the todos.
     * GET /api/todos
     */
    public function index(): JsonResponse
    {
        $todos = Todo::orderBy('created_at', 'desc')->get();
        return response()->json($todos);
    }

    /**
     * Store a newly created todo.
     * POST /api/todos
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
        ]);

        $todo = Todo::create($validated);
        return response()->json($todo, 201);
    }

    /**
     * Display the specified todo.
     * GET /api/todos/{id}
     */
    public function show(Todo $todo): JsonResponse
    {
        return response()->json($todo);
    }

    /**
     * Update the specified todo.
     * PUT /api/todos/{id}
     */
    public function update(Request $request, Todo $todo): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
        ]);

        $todo->update($validated);
        return response()->json($todo);
    }

    /**
     * Toggle the completion status of a todo.
     * PATCH /api/todos/{id}/toggle
     */
    public function toggle(Todo $todo): JsonResponse
    {
        $todo->update(['is_completed' => !$todo->is_completed]);
        return response()->json($todo);
    }

    /**
     * Remove the specified todo.
     * DELETE /api/todos/{id}
     */
    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();
        return response()->json(null, 204);
    }
}
