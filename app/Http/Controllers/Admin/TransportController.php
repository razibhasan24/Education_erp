<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentTransport;
use App\Models\TransportRoute;
use App\Models\TransportStop;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::withCount('routes')->get();
        $routes = TransportRoute::with(['vehicle', 'stops'])->withCount('studentTransports')->get();
        $students = Student::where('status', 'active')->get();
        $assignments = StudentTransport::with(['student', 'route', 'stop'])->where('status', 'active')->latest()->get();
        return view('admin.transport.index', compact('vehicles', 'routes', 'students', 'assignments'));
    }

    public function storeVehicle(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'registration_no' => 'required|string|max:30|unique:vehicles,registration_no',
            'capacity' => 'required|integer|min:1',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'helper_name' => 'nullable|string|max:100',
            'helper_phone' => 'nullable|string|max:20',
        ]);
        Vehicle::create($data);
        return back()->with('success', 'যানবাহন যোগ হয়েছে।');
    }

    public function deleteVehicle(Vehicle $vehicle)
    {
        $vehicle->delete();
        return back()->with('success', 'যানবাহন মুছে ফেলা হয়েছে।');
    }

    public function storeRoute(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'start_point' => 'nullable|string|max:100',
            'end_point' => 'nullable|string|max:100',
            'monthly_fee' => 'required|numeric|min:0',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);
        TransportRoute::create($data);
        return back()->with('success', 'রুট যোগ হয়েছে।');
    }

    public function deleteRoute(TransportRoute $route)
    {
        $route->delete();
        return back()->with('success', 'রুট মুছে ফেলা হয়েছে।');
    }

    public function storeStop(Request $request, TransportRoute $route)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'pickup_time' => 'nullable',
            'drop_time' => 'nullable',
            'fee' => 'nullable|numeric|min:0',
            'sequence' => 'required|integer|min:1',
        ]);
        $data['transport_route_id'] = $route->id;
        TransportStop::create($data);
        return back()->with('success', 'স্টপ যোগ হয়েছে।');
    }

    public function assignStudent(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'transport_route_id' => 'required|exists:transport_routes,id',
            'transport_stop_id' => 'nullable|exists:transport_stops,id',
            'start_date' => 'required|date',
        ]);
        $data['status'] = 'active';
        StudentTransport::create($data);
        return back()->with('success', 'শিক্ষার্থীকে ট্রান্সপোর্টে যোগ করা হয়েছে।');
    }

    public function removeAssignment(StudentTransport $assignment)
    {
        $assignment->update(['status' => 'inactive', 'end_date' => now()]);
        return back()->with('success', 'সাময়িক বন্ধ করা হয়েছে।');
    }
}
