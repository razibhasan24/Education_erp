<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelAllocation;
use App\Models\HostelRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelController extends Controller
{
    public function index()
    {
        $hostels = Hostel::with(['rooms'])->withCount('rooms')->get();
        $students = Student::where('status', 'active')->get();
        $allocations = HostelAllocation::with(['student', 'hostel', 'room'])->where('status', 'active')->latest()->get();
        return view('admin.hostel.index', compact('hostels', 'students', 'allocations'));
    }

    public function storeHostel(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:boys,girls',
            'warden_name' => 'nullable|string|max:100',
            'warden_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
            'monthly_fee' => 'required|numeric|min:0',
        ]);
        Hostel::create($data);
        return back()->with('success', 'হোস্টেল যোগ হয়েছে।');
    }

    public function deleteHostel(Hostel $hostel)
    {
        $hostel->delete();
        return back()->with('success', 'হোস্টেল মুছে ফেলা হয়েছে।');
    }

    public function storeRoom(Request $request, Hostel $hostel)
    {
        $data = $request->validate([
            'room_no' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'monthly_fee' => 'nullable|numeric|min:0',
        ]);
        $data['hostel_id'] = $hostel->id;
        HostelRoom::create($data);
        return back()->with('success', 'রুম যোগ হয়েছে।');
    }

    public function deleteRoom(HostelRoom $room)
    {
        $room->delete();
        return back()->with('success', 'রুম মুছে ফেলা হয়েছে।');
    }

    public function allocate(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'hostel_id' => 'required|exists:hostels,id',
            'hostel_room_id' => 'required|exists:hostel_rooms,id',
            'start_date' => 'required|date',
        ]);

        $room = HostelRoom::findOrFail($data['hostel_room_id']);
        if ($room->occupied >= $room->capacity) {
            return back()->with('error', 'রুম পূর্ণ।');
        }

        DB::beginTransaction();
        try {
            $data['status'] = 'active';
            HostelAllocation::create($data);
            $room->increment('occupied');
            DB::commit();
            return back()->with('success', 'রুম বরাদ্দ হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function release(HostelAllocation $allocation)
    {
        DB::beginTransaction();
        try {
            $allocation->update(['status' => 'left', 'end_date' => now()]);
            $allocation->room->decrement('occupied');
            DB::commit();
            return back()->with('success', 'রুম খালি করা হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
