<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkController extends Controller
{
    public function index()
    {
        return \Inertia\Inertia::render('Works', [
            'works' => \App\Models\Work::orderBy('created_at', 'desc')->get()
        ]);
    }

    public function list()
    {
        return response()->json(\App\Models\Work::orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client' => 'nullable|string|max:255',
            'technical_manager' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'status' => 'required|string',
            'type' => 'nullable|string',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'total_area' => 'nullable|numeric',
        ]);

        $work = \App\Models\Work::create($validated);

        return response()->json($work, 201);
    }

    public function update(Request $request, $id)
    {
        $work = \App\Models\Work::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client' => 'nullable|string|max:255',
            'technical_manager' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'status' => 'required|string',
            'type' => 'nullable|string',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'total_area' => 'nullable|numeric',
        ]);

        $work->update($validated);

        return response()->json($work);
    }

    public function destroy($id)
    {
        $work = \App\Models\Work::findOrFail($id);
        $work->delete();

        return response()->json(['message' => 'Obra excluída com sucesso']);
    }
}
