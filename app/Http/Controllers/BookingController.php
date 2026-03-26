<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentConfirmed;
use App\Mail\AppointmentCompleted;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'service'    => 'required|string',
            'duration'   => 'required|string',
            'time'       => 'required|string',
        ]);

        $booking = Booking::create($validated + [
            'phone'  => $request->phone,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'data'    => $booking,
        ], 201);
    }

    public function index()
    {
        $bookings = Booking::orderBy('created_at', 'desc')->get()->map(function ($booking) {
            return [
                'id'               => $booking->id,
                'patient_name'     => $booking->first_name . ' ' . $booking->last_name,
                'email'            => $booking->email,
                'phone'            => $booking->phone,
                'service'          => $booking->service,
                'appointment_date' => $booking->created_at->toDateString(),
                'appointment_time' => $booking->time,
                'notes'            => $booking->reason,
                'status'           => $booking->status ?? 'pending',
                'duration'         => $booking->duration,
            ];
        });

        return response()->json($bookings);
    }

    // Look up active bookings by patient email
    public function byEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $bookings = Booking::where('email', $request->email)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id'               => $booking->id,
                    'patient_name'     => $booking->first_name . ' ' . $booking->last_name,
                    'email'            => $booking->email,
                    'service'          => $booking->service,
                    'appointment_date' => $booking->created_at->toDateString(),
                    'appointment_time' => $booking->time,
                    'duration'         => $booking->duration,
                    'status'           => $booking->status ?? 'pending',
                ];
            });

        return response()->json($bookings);
    }

    // Look up a single booking by cancellation token (from email link)
    public function byToken(Request $request)
    {
        $booking = Booking::where('id', $request->id)
            ->where('cancel_token', $request->token)
            ->firstOrFail();

        return response()->json([
            'id'               => $booking->id,
            'patient_name'     => $booking->first_name . ' ' . $booking->last_name,
            'email'            => $booking->email,
            'service'          => $booking->service,
            'appointment_date' => $booking->created_at->toDateString(),
            'appointment_time' => $booking->time,
            'duration'         => $booking->duration,
            'status'           => $booking->status,
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->update(['status' => $request->status]);

        if ($request->status === 'confirmed') {
            Mail::to($booking->email)->send(new AppointmentConfirmed($booking));
        }

        if ($request->status === 'completed') {
            Mail::to($booking->email)->send(new AppointmentCompleted($booking));
        }

        if ($request->status === 'cancelled') {
            Mail::to($booking->email)->send(new AppointmentCancelled($booking));
        }

        return response()->json($booking);
    }
}
