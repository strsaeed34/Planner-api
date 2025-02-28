<?php

namespace App\Http\Controllers;

use App\Models\Note;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $notes = $request->user()->notes;
        return response()->json($notes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|string|max:255',
                'content' => 'required|string',

            ]
        );

        $note = $request->user()->notes()->create($request->only('title', 'content'));
        return response()->json($note, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $note = Note::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        if ($note) {
            return response()->json($note);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        if ($note->user_id != Auth::id()) {
            return response()->json(["error"=> "You do not have permission to modify this note."],401);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
        ]);

        $note->update($request->only('title', 'content'));

        return response()->json($note);
    }

    /**
     * Remove the specified resource from storage.
     */ 
    public function destroy(Note $note)
    {
        if ($note->user_id != Auth::id()) {
            return response()->json(["error"=> "You do not have permission to delete this note."],401);
        }

        $note->delete();
        return response()->json(["message"=>"Note Deleted Successfully"]);
    }
}
