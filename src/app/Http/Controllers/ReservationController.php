<?php

namespace App\Http\Controllers;

use App\Jobs\SendReservationEmail;
use App\Notifications\ReservationNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Event;
use App\Mail\TestEmail;
use Mail;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        return Reservation::where('user_id', $userId)->get();
    }

    public function show($id)
    {
        return Reservation::find($id);
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();
            $validatedData = $request->validate([
                "event_id" => "required|exists:events,id",
                "seats_reserved" => "required|integer|gt:0",
            ]);

            $event = Event::find($validatedData["event_id"]);

            if ($validatedData["seats_reserved"] > $event->available_seats) {
                return response()->json(['errors' => 'Not enough seats'], 422);
            }

            $validatedData['user_id'] = $user->id;

            $reservation = Reservation::create($validatedData);

            $reservation->event->event_date = Carbon::parse($reservation->event->event_date)->format('d/m/Y H:i');

            // Mail::to($user->email)->send(new TestEmail($reservation));
            // $user->notify(new ReservationNotification($reservation));
            SendReservationEmail::dispatch($reservation, $user);
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
