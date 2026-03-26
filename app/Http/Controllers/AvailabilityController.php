<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\BlockedDate;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    // GET /api/availability — return weekly schedule
    public function index()
    {
        return response()->json(Availability::orderBy('day_of_week')->get());
    }

    // POST /api/availability — save full weekly schedule
    public function save(Request $request)
    {
        $request->validate([
            'schedule'                  => 'required|array',
            'schedule.*.day_of_week'    => 'required|integer|min:0|max:6',
            'schedule.*.start_time'     => 'required|date_format:H:i',
            'schedule.*.end_time'       => 'required|date_format:H:i',
            'schedule.*.slot_duration'  => 'required|integer|min:15',
            'schedule.*.max_per_slot'   => 'required|integer|min:1',
            'schedule.*.is_active'      => 'required|boolean',
        ]);

        // Replace all records
        Availability::truncate();
        foreach ($request->schedule as $row) {
            Availability::create($row);
        }

        return response()->json(['message' => 'Schedule saved.']);
    }

    // GET /api/availability/slots?date=2026-03-10 — return slots for a given date
    public function slots(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $date    = Carbon::parse($request->date);
        $dow     = $date->dayOfWeek; // 0=Sun ... 6=Sat
        $dateStr = $date->toDateString();

        // Check if date is blocked
        $blocked = BlockedDate::where('date', $dateStr)->first();
        if ($blocked) {
            return response()->json(['blocked' => true, 'reason' => $blocked->reason, 'slots' => []]);
        }

        // Check if past
        if ($date->isPast() && !$date->isToday()) {
            return response()->json(['slots' => []]);
        }

        // Get availability for this day
        $avail = Availability::where('day_of_week', $dow)->where('is_active', true)->first();
        if (!$avail) {
            return response()->json(['slots' => []]);
        }

        // Generate time slots
        $slots    = [];
        $current  = Carbon::parse($dateStr . ' ' . $avail->start_time);
        $end      = Carbon::parse($dateStr . ' ' . $avail->end_time);
        $duration = $avail->slot_duration;
        $maxPerSlot = $avail->max_per_slot;

        while ($current->copy()->addMinutes($duration)->lte($end)) {
            $slotTime = $current->format('H:i');
            $slotLabel = $current->format('g:i A');

            // Count bookings already in this slot
            $booked = Booking::where('time', $slotLabel)
                ->whereDate('created_at', $dateStr)
                ->whereNotIn('status', ['cancelled'])
                ->count();

            $remaining = max(0, $maxPerSlot - $booked);

            $slots[] = [
                'time'      => $slotLabel,
                'value'     => $slotTime,
                'booked'    => $booked,
                'remaining' => $remaining,
                'max'       => $maxPerSlot,
                'available' => $remaining > 0,
            ];

            $current->addMinutes($duration);
        }

        return response()->json(['slots' => $slots, 'blocked' => false]);
    }

    // GET /api/availability/calendar?month=2026-03 — return available days for a month
    public function calendar(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);

        $start  = Carbon::parse($request->month . '-01')->startOfMonth();
        $end    = $start->copy()->endOfMonth();
        $today  = Carbon::today();

        $activeDays    = Availability::where('is_active', true)->pluck('day_of_week')->toArray();
        $blockedDates  = BlockedDate::whereBetween('date', [$start, $end])->pluck('date')->map(fn($d) => Carbon::parse($d)->toDateString())->toArray();

        $days = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $dateStr  = $cursor->toDateString();
            $dow      = $cursor->dayOfWeek;
            $isPast   = $cursor->lt($today);
            $isBlocked = in_array($dateStr, $blockedDates);
            $hasAvail  = in_array($dow, $activeDays);

            $days[] = [
                'date'      => $dateStr,
                'dow'       => $dow,
                'available' => $hasAvail && !$isPast && !$isBlocked,
                'blocked'   => $isBlocked,
                'past'      => $isPast,
            ];

            $cursor->addDay();
        }

        return response()->json($days);
    }

    // GET /api/availability/blocked — list blocked dates
    public function blockedDates()
    {
        return response()->json(BlockedDate::orderBy('date')->get());
    }

    // POST /api/availability/blocked — add a blocked date
    public function blockDate(Request $request)
    {
        $request->validate([
            'date'   => 'required|date',
            'reason' => 'nullable|string|max:255',
        ]);

        $blocked = BlockedDate::updateOrCreate(
            ['date' => $request->date],
            ['reason' => $request->reason]
        );

        return response()->json($blocked, 201);
    }

    // DELETE /api/availability/blocked/{id} — remove a blocked date
    public function unblockDate($id)
    {
        BlockedDate::findOrFail($id)->delete();
        return response()->json(['message' => 'Date unblocked.']);
    }
}
