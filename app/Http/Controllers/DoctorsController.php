<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorsController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::query();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('specialty', 'like', '%' . $request->search . '%');
        }

        $doctors = $query->paginate(20);

        return view('Adminstration.doctors', compact('doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:doctors,email',
            'password' => 'required|string|min:6',
            'specialty' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        Doctor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'specialty' => $request->specialty,
            'phone' => $request->phone,
        ]);

        return back()->with('success', __('messages.doctor_added_success'));
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:doctors,email,' . $id,
            'specialty' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'specialty' => $request->specialty,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $doctor->update($data);

        return back()->with('success', __('messages.doctor_updated_success'));
    }

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        
        // Check if doctor has reservations
        if ($doctor->reservations()->count() > 0) {
            return back()->with('error', __('messages.doctor_delete_error'));
        }

        $doctor->delete();

        return back()->with('success', __('messages.doctor_deleted_success'));
    }
}
