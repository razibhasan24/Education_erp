<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AdmissionTestResult;
use Illuminate\Http\Request;

class AdmissionResultController extends Controller
{
    public function index()
    {
        $settings = \App\Models\InstituteSetting::first();
        return view('frontend.admission-result', compact('settings'));
    }

    public function check(Request $request)
    {
        $data = $request->validate([
            'roll_no' => 'required|string',
            'phone' => 'required|string',
        ]);

        $result = AdmissionTestResult::where('roll_no', $data['roll_no'])
            ->where('phone', $data['phone'])
            ->where('is_published', true)
            ->with('schoolClass')
            ->first();

        if (!$result) {
            return back()->with('error', 'এই রোল নম্বর এবং মোবাইল নম্বরের সাথে কোনো ফলাফল পাওয়া যায়নি।')->withInput();
        }

        $settings = \App\Models\InstituteSetting::first();
        return view('frontend.admission-result-show', compact('result', 'settings'));
    }
}