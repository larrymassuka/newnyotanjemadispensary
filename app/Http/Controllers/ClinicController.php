<?php

namespace App\Http\Controllers;

use App\Clinic;
use App\Patients;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clinics = Clinic::with('doctor')->get();
        return view('clinics.index', compact('clinics'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $doctors = User::where('user_type', 'doctor')->get();
        return view('clinics.create', compact('doctors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'doctor_id' => 'required|exists:users,id',
            'location' => 'nullable|string',
            'schedule' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        Clinic::create($request->all());

        return redirect()->route('clinics.index')
            ->with('success', 'Clinic created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Clinic $clinic)
    {
        $clinic->load(['doctor', 'patients']);
        return view('clinics.show', compact('clinic'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Clinic $clinic)
    {
        $doctors = User::where('user_type', 'doctor')->get();
        return view('clinics.edit', compact('clinic', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Clinic $clinic)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'doctor_id' => 'required|exists:users,id',
            'location' => 'nullable|string',
            'schedule' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $clinic->update($request->all());

        return redirect()->route('clinics.index')
            ->with('success', 'Clinic updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clinic $clinic)
    {
        $clinic->delete();

        return redirect()->route('clinics.index')
            ->with('success', 'Clinic deleted successfully.');
    }

    /**
     * Add patient to clinic
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Clinic  $clinic
     * @return \Illuminate\Http\Response
     */
    public function addPatient(Request $request, Clinic $clinic)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id'
        ]);

        $clinic->addPatientToClinic($request->patient_id);

        return back()->with('success', 'Patient added to clinic successfully.');
    }

    /**
     * Remove patient from clinic
     *
     * @param  \App\Clinic  $clinic
     * @param  \App\Patients  $patient
     * @return \Illuminate\Http\Response
     */
    public function removePatient(Clinic $clinic, Patients $patient)
    {
        DB::table('clinic_patient')
            ->where('clinic_id', $clinic->id)
            ->where('patients_id', $patient->id)
            ->delete();

        return back()->with('success', 'Patient removed from clinic successfully.');
    }

    /**
     * Get clinic patients
     *
     * @param  \App\Clinic  $clinic
     * @return \Illuminate\Http\Response
     */
    public function getPatients(Clinic $clinic)
    {
        $patients = $clinic->patients;
        return view('clinics.patients', compact('clinic', 'patients'));
    }
}