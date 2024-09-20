<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Validation\Rule;

class EventController extends Controller
{

    protected $imageService;
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
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
                "thumbnail" => "required|image|max:2048|mimes:jpeg,png,jpg,gif,svg",
            ]);
            $thumbnail_url = $this->imageService->upload($request->file('thumbnail'), 'events');

            $validatedData["thumbnail_url"] = $thumbnail_url;

            $event = Event::create($validatedData);
            return $event;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request content type: ' . $request->header('Content-Type'));
        \Log::info('All request data:', $request->all());
        if (!$id) {
            return response()->json(['errors' => 'Id not found'], 404);
        }
        try {
            $event = Event::find($id);
            if (!$event) {
                return response()->json(['errors' => 'Event not found'], 404);
            }

            $request->validate([
                "name" => [
                    "sometimes",
                    "required",
                    "max:32",
                    "string",
                    "min:5",
                    Rule::unique('events')->ignore($id),
                ],
                "description" => "sometimes|required|max:255",
                "event_date" => "sometimes|required|date|after:now",
                "available_seats" => "sometimes|required|integer|gt:0",
                "thumbnail" => "sometimes|required|image|max:2048|mimes:jpeg,png,jpg,gif,svg",
            ]);

            $data = $request->only($event->getFillable());

            if ($request->hasFile('thumbnail')) {
                $thumbnail_url = $this->imageService->upload($request->file('thumbnail'), 'events');
                $data['thumbnail_url'] = $thumbnail_url;
            }


            $event->update($data);
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
