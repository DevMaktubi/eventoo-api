<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Reservation;

class EventController extends Controller
{
    public function index()
    {
        return Event::all();
    }

    public function show($id)
    {
        if (!$id) {
            return response()->json(['errors' => 'Id not found'], 404);
        }
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['errors' => 'Event not found'], 404);
        }
        $taken_seats = Reservation::where("event_id", $event->id)->sum("seats_reserved");
        $event->available_seats -= $taken_seats;
        return $event;
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                "name" => "required|max:32|unique:events|string|min:5",
                "description" => "required|max:255",
                "event_date" => "required|date|after:now",
                "available_seats" => "required|integer|gt:0",
            ]);

            $event = Event::create($validatedData);
            return $event;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            return response()->json(['errors' => 'Id not found'], 404);
        }
        try {
            $request->validate([
                "name" => "max:32|unique:events|string|min:5",
                "description" => "max:255",
                "event_date" => "date|after:now",
                "available_seats" => "integer|gt:0",
            ]);

            $event = Event::find($id);
            if (!$event) {
                return response()->json(['errors' => 'Event not found'], 404);
            }
            $event->update($request->only($event->getFillable()));
            return $event;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        if (!$id) {
            return response()->json(['errors' => 'Id not found'], 404);
        }
        try {
            $event = Event::find($id);
            if (!$event) {
                return response()->json(['errors' => 'Event not found'], 404);
            }
            $event->delete();
            return $event;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
