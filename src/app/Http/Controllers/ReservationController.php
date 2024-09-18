<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Event;

class ReservationController extends Controller
{
    public function index()
    {
        return Reservation::all();
    }

    public function show($id)
    {
        return Reservation::find($id);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                "user_id" => "required|exists:users,id",
                "event_id" => "required|exists:events,id",
                "seats_reserved" => "required|integer|gt:0",
            ]);

            $event = Event::find($validatedData["event_id"]);

            if ($validatedData["seats_reserved"] > $event->available_seats) {
                return response()->json(['errors' => 'Not enough seats'], 422);
            }

            $reservation = Reservation::create($validatedData);
            return $reservation;
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
                "user_id" => "exists:users,id",
                "event_id" => "exists:events,id",
                "seats_reserved" => "integer|gt:0",
            ]);

            $reservation = Reservation::find($id);
            if (!$reservation) {
                return response()->json(['errors' => 'Reservation not found'], 404);
            }
            $reservation->update($request->only($reservation->getFillable()));
            return $reservation;
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
            $reservation = Reservation::find($id);
            if (!$reservation) {
                return response()->json(['errors' => 'Reservation not found'], 404);
            }
            $reservation->delete();
            return $reservation;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
