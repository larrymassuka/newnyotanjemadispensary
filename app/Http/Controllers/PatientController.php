<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Clinic;
use App\Http\Controllers\Redirect;
use App\inpatient;
use App\Medicine;
use App\Patients;
use App\Prescription;
use App\Prescription_Medicine;
use App\Ward;
use Carbon\Carbon;
use DB;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use stdClass;

class PatientController extends Controller
{
    protected $wardArray;

    public function __construct()
    {
        $this->middleware('auth');
        $this->wardList = ['' => 'Select Ward No'] + Ward::pluck('id', 'ward_no')->all();
    }

    public function inPatientReport()
    {

        return view('patient.inpatient.inpatients', ["date"=>null,"title" => "Inpatient Details", "data_count" => 0]);

    }

    public function inPatientReportData(Request $request)
    {
        $data=DB::table('inpatients')->whereDate('created_at', '=', $request->date)->get();
        if($data->count()>0){
            return view('patient.inpatient.inpatients', ["title" => "Inpatient Details","date"=>$request->date,"data_count"=>$data->count(), "data" => $data]);

        }else{
            return redirect(route("inPatientReport"))->with('fail',"No Results Found");
        }
      
    }

    public function index()
    {
        $user = Auth::user();
        return view('patient.register_patient', ['title' => $user->name]);
    }

    public function patientHistory($id)
    {
        $prescs = Prescription::where('patient_id', $id)->orderBy('created_at', 'desc')->get();
        $title = "Patient History ($id)";

        $patient = Patients::withTrashed()->find($id);
        $hospital_visits = 1;
        $status = "Active";
        $last_seen = explode(" ", $patient->updated_at)[0];
        if ($patient->trashed()) {
            $status = "Inactive";
        }
        $hospital_visits += Prescription::where('patient_id', $patient->id)->count();

        return view('patient.history.index', compact('prescs', 'patient', 'title', 'hospital_visits', 'status', 'last_seen'));
    }

    public function patientProfileIntro(Request $request)
    {
        if ($request->has('pid')) {
            return redirect()->route('patientProfile', $request->pid);
        } else {
            return view('patient.profile.intro', ['title' => "Patient Profile"]);
        }
    }

    public function patientDelete($id, $action)
    {
        if ($action == "delete") {
            Patients::find($id)->delete();
        }if ($action == 'restore') {
            Patients::withTrashed()->find($id)->restore();
        }
        return redirect()->route('patientProfile', $id);
    }

    public function patientProfile($id)
    {
        $patient = Patients::withTrashed()->find($id);
        $hospital_visits = 1;
        $status = "Active";
        $last_seen = explode(" ", $patient->updated_at)[0];
        if ($patient->trashed()) {
            $status = "Inactive";
        }
        $hospital_visits += Prescription::where('patient_id', $patient->id)->count();

        return view('patient.profile.profile',
            [
                'title' => $patient->name,
                'patient' => $patient,
                'status' => $status,
                'last_seen' => $last_seen,
                'hospital_visits' => $hospital_visits,

            ]);
    }

    public function searchPatient(Request $request)
    {
        return view('patient.search_patient_view', ['title' => "Search Patient", "old_keyword" => null, "search_result" => ""]);
    }

    public function patientData(Request $request)
    {
        if ($request->cat == "name") {
            $result = Patients::withTrashed()->where('name', 'LIKE', '%' . $request->keyword . '%')->get();
        }
        if ($request->cat == "nic") {
            $result = Patients::withTrashed()->where('nic', 'LIKE', '%' . $request->keyword . '%')->get();

        }
        if ($request->cat == "telephone") {
            $result = Patients::withTrashed()->where('telephone', 'LIKE', '%' . $request->keyword . '%')->get();
        }
        return view('patient.search_patient_view', ["title" => "Search Results", "old_keyword" => $request->keyword, "search_result" => $result]);
    }

   public function registerPatient(Request $request)
{
    try {
        $patient = new Patients;
        $today_regs = (int) Patients::whereDate('created_at', date("Y-m-d"))->count();

        $number = $today_regs + 1;
        $year = date('Y') % 100;
        $month = date('m');
        $day = date('d');

        $reg_num = $year . $month . $day . $number;

        $date = date_create($request->reg_pbd);

        $patient->id = $reg_num;
        $patient->name = $request->reg_pname;
        $patient->address = $request->reg_paddress;
        $patient->occupation = $request->reg_poccupation;
        $patient->sex = $request->reg_psex;
        $patient->bod = date_format($date, "Y-m-d");
        $patient->telephone = $request->reg_ptel;
        $patient->nic = $request->reg_pnic;

        $patient->save();
        session()->flash('regpsuccess', 'Patient ' . $request->reg_pname . ' Registered Successfully !');
        session()->flash('pid', "$reg_num");

        // Log Activity
        activity()->performedOn($patient)->withProperties(['Patient ID' => $reg_num])->log('Patient Registration Success');

        return redirect()->back();
    } catch (\Exception $e) {
        // do task when error
        $error = $e->getCode();
        // log activity
        activity()->performedOn($patient)->withProperties(['Error Code' => $error, 'Error Message' => $e->getMessage()])->log('Patient Registration Failed');

        if ($error == '23000') {
            session()->flash('regpfail', 'Patient ' . $request->reg_pname . ' Is Already Registered..');
            return redirect()->back();
        }
    }
}
    public function validateAppNum(Request $request)
{
    $num = $request->number;
    
    // Try to find by appointment number first
    $rec = DB::table('appointments')
        ->join('patients', 'appointments.patient_id', '=', 'patients.id')
        ->select('patients.name as name', 'appointments.number as num', 'appointments.patient_id as pnum')
        ->where('appointments.number', $num)
        ->first();
    
    // If not found by appointment number, try by patient ID
    if (!$rec) {
        $rec = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->select('patients.name as name', 'appointments.number as num', 'appointments.patient_id as pnum')
            ->where('patients.id', $num)
            ->first();
    }
    
    // If still not found, just check if patient exists
    if (!$rec) {
        $patient = DB::table('patients')
            ->select('name', 'id as pnum')
            ->where('id', $num)
            ->first();
        
        if ($patient) {
            return response()->json([
                "exist" => true,
                "name" => $patient->name,
                "appNum" => "N/A",  // No appointment found
                "pNum" => $patient->pnum,
                "finger"=>Auth::user()->fingerprint,
            ]);
        }
    }
    
    if ($rec) {
        return response()->json([
            "exist" => true,
            "name" => $rec->name,
            "appNum" => $rec->num,
            "pNum" => $rec->pnum,
            "finger"=>Auth::user()->fingerprint,
        ]);
    } else {
        return response()->json([
            "exist" => false,
        ]);
    }
}
    public function checkPatientView()
    {
        $user = Auth::user();
        return view('patient.check_patient_intro', ['title' => "Check Patient"]);
    }

   public function checkPatient(Request $request)
{
    // If no appointment number provided but patient ID exists, create a temporary session
    if (empty($request->appNum) && !empty($request->pid)) {
        $patient = Patients::find($request->pid);
        
        if (!$patient) {
            return redirect()->route('check_patient_view')->with('fail', "Patient not found.");
        }
        
        // Get or create a temporary appointment
        $appointment = Appointment::where('patient_id', $request->pid)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$appointment) {
            // Create a temporary appointment if none exists
            $appointment = new Appointment;
            $appointment->number = 'TEMP-' . date('His');
            $appointment->patient_id = $request->pid;
            $appointment->completed = 'NO';
            $appointment->admit = 'NO';
            $appointment->save();
        }

        return $this->loadCheckPatientView($appointment, $patient);
    }
    
    // Original logic for when appointment number is provided
    $appointment = Appointment::where('number', $request->appNum)
        ->where('patient_id', $request->pid)
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$appointment) {
        // Try to find any appointment for this patient
        $appointment = Appointment::where('patient_id', $request->pid)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$appointment) {
            return redirect()->route('check_patient_view')->with('fail', "No appointment found for this patient.");
        }
    }

    if ($appointment->completed == "YES") {
        return redirect()->route('check_patient_view')->with('fail', "This Appointment Has Already Been Channeled.");
    }

    $patient = Patients::find($appointment->patient_id);

    if (!$patient) {
        return redirect()->route('check_patient_view')->with('fail', "Patient not found.");
    }

    return $this->loadCheckPatientView($appointment, $patient);
}

/**
 * Helper method to load the check patient view with all required data
 */
private function loadCheckPatientView($appointment, $patient)
{
    $user = Auth::user();

    // Get patient's prescription history
    $prescriptions = Prescription::where('patient_id', $patient->id)
        ->orderBy('created_at', 'DESC')
        ->get();

    // Initialize medical data objects
    $pBloodPressure = new stdClass;
    $pBloodPressure->flag = false;

    $pBloodSugar = new stdClass;
    $pBloodSugar->flag = false;

    $pCholestrol = new stdClass;
    $pCholestrol->flag = false;

    // Extract latest medical readings from prescriptions
    foreach ($prescriptions as $prescription) {
        if (!$pBloodPressure->flag && $prescription->bp) {
            $bpData = json_decode($prescription->bp);
            if ($bpData && !empty($bpData->value)) {
                $bpParts = explode("/", $bpData->value);
                if (count($bpParts) == 2) {
                    $pBloodPressure->sys = $bpParts[0];
                    $pBloodPressure->dia = $bpParts[1];
                    $pBloodPressure->date = $bpData->updated ?? $prescription->created_at;
                    $pBloodPressure->flag = true;
                }
            }
        }

        if (!$pCholestrol->flag && $prescription->cholestrol) {
            $cholestrolData = json_decode($prescription->cholestrol);
            if ($cholestrolData && !empty($cholestrolData->value)) {
                $pCholestrol->value = $cholestrolData->value;
                $pCholestrol->date = $cholestrolData->updated ?? $prescription->created_at;
                $pCholestrol->flag = true;
            }
        }

        if (!$pBloodSugar->flag && $prescription->blood_sugar) {
            $sugarData = json_decode($prescription->blood_sugar);
            if ($sugarData && !empty($sugarData->value)) {
                $pBloodSugar->value = $sugarData->value;
                $pBloodSugar->date = $sugarData->updated ?? $prescription->created_at;
                $pBloodSugar->flag = true;
            }
        }

        // Break early if all data found
        if ($pBloodPressure->flag && $pCholestrol->flag && $pBloodSugar->flag) {
            break;
        }
    }

    // Determine last visit date
    $updated = "No Previous Visits";
    if ($prescriptions->count() > 0) {
        $updated = explode(" ", $prescriptions[0]->created_at)[0];
    }

    // Get clinic assignments
    $assinged_clinics = $patient->clinics;
    $clinics = Clinic::all();

    return view('patient.check_patient_view', [
        'title' => "Check Patient",
        'appNum' => $appointment->number,
        'appID' => $appointment->id,
        'pName' => $patient->name,
        'pSex' => $patient->sex,
        'pAge' => $patient->getAge(),
        'pCholestrol' => $pCholestrol,
        'pBloodSugar' => $pBloodSugar,
        'pBloodPressure' => $pBloodPressure,
        'inpatient' => $appointment->admit,
        'pid' => $patient->id,
        'medicines' => Medicine::all(),
        'updated' => $updated,
        'assinged_clinics' => $assinged_clinics,
        'clinics' => $clinics,
    ]);
}
    public function addToClinic(Request $request)
    {
        foreach ($request->clinic as $clinic) {
            $c = Clinic::find($clinic);
            $c->addPatientToClinic($request->pid);
        }
        $assinged_clinics = Patients::find($request->pid)->clinics;
        $clinics = Clinic::all();
        $pid = $request->pid;
        $html_list = view('patient.patinet_clinic', compact('pid', 'assinged_clinics', 'clinics'))->render();
        $html_already = view('patient.patient_clinic_registered', compact('assinged_clinics', 'clinics'))->render();
        return response()->json([
            'code' => 200,
            'html_already' => $html_already,
            'html_list' => $html_list,
        ]);

    }

    public function markInPatient(Request $request)
    {
        $pid = $request->pid;
        $app_num = $request->app_num;
        $user = Auth::user();
        $appointment = Appointment::where('number', $app_num)->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->where('patient_id', $pid)->first();
        if ($appointment->admit == "NO") {
            $appointment->admit = "YES";
            $appointment->doctor_id = $user->id;
            $appointment->save();
            return response()->json([
                'success' => true,
                'appid' => $appointment->id,
                'pid' => $pid,
                'app_num' => $app_num,
            ]);
        }
    }

    public function checkPatientSave(Request $request)
    {

        $user = Auth::user();
        $presc = new Prescription;
        $presc->doctor_id = $user->id;
        $presc->patient_id = $request->patient_id;
        $presc->diagnosis = $request->diagnosis;
        $presc->appointment_id = $request->appointment_id;

        $presc->medicines = json_encode($request->medicines);

        $bp = new stdClass;
        $bp->value = $request->pressure;
        $bp->updated = Carbon::now()->toDateTimeString();
        $presc->bp = json_encode($bp);

        $gloucose = new stdClass;
        $gloucose->value = $request->glucose;
        $gloucose->updated = Carbon::now()->toDateTimeString();
        $presc->blood_sugar = json_encode($gloucose);

        $cholestrol = new stdClass;
        $cholestrol->value = $request->cholestrol;
        $cholestrol->updated = Carbon::now()->toDateTimeString();
        $presc->cholestrol = json_encode($cholestrol);

        $presc->save();

        $appointment = Appointment::find($request->appointment_id);
        $appointment->completed = "YES";
        $appointment->doctor_id = $user->id;
        $appointment->save();

        foreach ($request->medicines as $medicine) {
            $med = Medicine::where('name_english', strtolower($medicine['name']))->first();
            $pres_med = new Prescription_Medicine;
            $pres_med->medicine_id = $med->id;
            $pres_med->prescription_id = $presc->id;
            $pres_med->note = $medicine['note'];
            $pres_med->save();
        }

        // Log Activity
        activity()->performedOn($presc)->withProperties(['Patient ID' => $request->patient_id, 'Doctor ID' => $user->id, 'Prescription ID' => $presc->id, 'Appointment ID' => $request->appointment_id, 'Medicines' => json_encode($request->medicines)])->log('Check Patient Success');

        return http_response_code(200);
    }

    public function create_channel_view()
    {
        $user = Auth::user();
        $appointments = DB::table('appointments')->join('patients', 'appointments.patient_id', '=', 'patients.id')->select('patients.name', 'appointments.number', 'appointments.patient_id')->whereRaw(DB::Raw('Date(appointments.created_at)=CURDATE()'))->orderBy('appointments.created_at', 'desc')->get();

        return view('patient.create_channel_view', ['title' => "Channel Appointments", 'appointments' => $appointments]);
    }

    public function regcard($id)
    {
        $patient = Patients::find($id);
        $data = [
            'name' => $patient->name,
            'sex' => $patient->sex,
            'id' => $patient->id,
            'reg' => explode(" ", $patient->created_at)[0],
            'dob' => $patient->bod,
        ];
        return view('patient.patient_reg_card', $data);
    }

    public function register_in_patient_view()
    {
        $user = Auth::user();
        $data = DB::table('wards')
                    ->select('*')
                    ->join('users', 'wards.doctor_id', '=', 'users.id')
                    ->get();
        // dd($data);
        return view('patient.register_in_patient_view', ['title' => "Register Inpatient",'data'=>$data]);
    }

public function regInPatientValid(Request $request)
{
    $pNum = $request->pNum;
    
    // Try to find patient by ID first
    $patient = Patients::find($pNum);
    
    if ($patient) {
        return response()->json([
            'exist' => true,
            'name' => $patient->name,
            'sex' => $patient->sex,
            'address' => $patient->address,
            'occupation' => $patient->occupation,
            'telephone' => $patient->telephone,
            'nic' => $patient->nic,
            'age' => $patient->getAge(), // Using model method
            'id' => $patient->id,
        ]);
    }
    
    // If not found, try appointment number (for short numbers)
    if (strlen((string) $pNum) < 5) {
        $appointment = Appointment::where('number', $pNum)->first();
        if ($appointment) {
            $patient = $appointment->patient;
            if ($patient) {
                return response()->json([
                    'exist' => true,
                    'name' => $patient->name,
                    'sex' => $patient->sex,
                    'address' => $patient->address,
                    'occupation' => $patient->occupation,
                    'telephone' => $patient->telephone,
                    'nic' => $patient->nic,
                    'age' => $patient->getAge(),
                    'id' => $patient->id,
                ]);
            }
        }
    }
    
    return response()->json([
        'exist' => false,
    ]);
}

    public function store_inpatient(Request $request)
    {
        $pid = $request->reg_pid;
        $Ptable = Patients::find($pid);
        $INPtable = new inpatient;

        $Ptable->civil_status = $request->reg_ipcondition;
        $Ptable->birth_place = $request->reg_ipbirthplace;
        $Ptable->nationality = $request->reg_ipnation;
        $Ptable->religion = $request->reg_ipreligion;
        $Ptable->income = $request->reg_inpincome;
        $Ptable->guardian = $request->reg_ipguardname;
        $Ptable->guardian_address = $request->reg_ipguardaddress;

        $INPtable->patient_id = $request->reg_pid;
        $INPtable->ward_id = $request->reg_ipwardno;
        $INPtable->patient_inventory = $request->reg_ipinventory;
    
        $INPtable->house_doctor = $request->reg_iphousedoc;
        $INPtable->approved_doctor = $request->reg_ipapprovedoc;
        $INPtable->disease = $request->reg_admitofficer1;
        $INPtable->duration = $request->reg_admitofficer2;
        $INPtable->condition = $request->reg_admitofficer3;
        $INPtable->certified_officer = $request->reg_admitofficer4;

        $Ptable->save();
        $INPtable->save();

        // decrement bed count by 1
        $getFB = Ward::where('ward_no', $request->reg_ipwardno)->first();
        $newFB = $getFB->free_beds-=1;
        Ward::where('ward_no', $request->reg_ipwardno)->update(['free_beds' => $newFB]);

      
        return redirect()->back()->with('regpsuccess', "Inpatient Successfully Registered");
    }

    public function get_ward_list()
    {
        $wardList = $this->wardList;
        $data=DB::table('wards')->join('users','wards.doctor_id','=','users.id')->select('*')->get();
         return view('register_in_patient_view', ['data'=>$data]);
        // $wards = Ward::all();
        // dd($wardss);
        // return view('register_in_patient_view', compact(['wards']));
    }

    public function discharge_inpatient()
    {
        $user = Auth::user();
        return view('patient.discharge_inpatient_view', ['title' => "Discharge Inpatient"]);
    }

  public function disInPatientValid(Request $request)
{
    $pNum = $request->pNum;
    
    // Find inpatient record - removed discharged='NO' check
    $inpatient = DB::table('patients')
        ->join('inpatients', 'patients.id', '=', 'inpatients.patient_id')
        ->select(
            'inpatients.patient_id as id', 
            'patients.name as name', 
            'patients.address as address', 
            'patients.telephone as tel', 
            'inpatients.discharged as dis',
            'inpatients.ward_id',
            'inpatients.created_at as admission_date'
        )
        ->where('inpatients.patient_id', $pNum)  // Only check if patient exists as inpatient
        ->first();

    if ($inpatient) {
        return response()->json([
            'exist' => true,
            'name' => $inpatient->name,
            'address' => $inpatient->address,
            'telephone' => $inpatient->tel,
            'id' => $inpatient->id,
            'ward_id' => $inpatient->ward_id,
            'admission_date' => $inpatient->admission_date,
            'discharge_status' => $inpatient->dis,  // Include status for info
            'message' => $inpatient->dis === 'YES' ? 
                'Patient found (already discharged)' : 
                'Patient found and ready for discharge'
        ]);
    } else {
        // Check if patient exists at all
        $patient = DB::table('patients')
            ->where('id', $pNum)
            ->first();
            
        if ($patient) {
            return response()->json([
                'exist' => false,
                'message' => 'Patient exists but is not registered as an inpatient.'
            ]);
        } else {
            return response()->json([
                'exist' => false,
                'message' => 'Patient not found in the system.'
            ]);
        }
    }
}
    public function store_disinpatient(Request $request)
    {
        // try{
        $pid = $request->reg_pid;
        $INPtableUpdate = Inpatient::where('patient_id', $pid)->first();

        $timestamp = now();
        $INPtableUpdate->discharged = 'YES';
        $INPtableUpdate->discharged_date = $timestamp;
        $INPtableUpdate->description = $request->reg_medicalofficer1;
        $INPtableUpdate->discharged_officer = $request->reg_medicalofficer2;

        $INPtableUpdate->save();

        // increment bed count by 1
        $wardNo = $INPtableUpdate->ward_id;
        $getFB = Ward::where('ward_no', $wardNo)->first();
        $newFB = $getFB->free_beds+=1;
        Ward::where('ward_no', $wardNo)->update(['free_beds' => $newFB]);

        return view('patient.discharge_recipt',compact('INPtableUpdate'))->with('regpsuccess', "Inpatient Successfully Discharged");;
        // }
        // catch(\Throwable $th){
        //     return redirect()->back()->with('error',"Unkown Error Occured");
        // }
    }

    public function getPatientData(Request $request)
    {
        $regNum = $request->regNum;
        $patient = Patients::find($regNum);
        if ($patient) {

            $num = DB::table('appointments')->select('id')->whereRaw(DB::raw("date(created_at)=CURDATE()"))->count() + 1;

            return response()->json([
                'exist' => true,
                'name' => $patient->name,
                'sex' => $patient->sex,
                'address' => $patient->address,
                'occupation' => $patient->occupation,
                'telephone' => $patient->telephone,
                'nic' => $patient->nic,
                'age' => $patient->getAge(),
                'id' => $patient->id,
                'appNum' => $num,
            ]);
        } else {
            return response()->json([
                'exist' => false,
            ]);
        }
    }

public function addChannel(Request $request)
    {
        $app = new Appointment;
        $num = DB::table('appointments')->select('id')->whereRaw(DB::raw("date(created_at)=CURDATE()"))->count() + 1;
        $pid = $request->id;
        $patient = Patients::find($pid);

        $app->number = $num;
        $app->patient_id = $pid;
        $app->save();
        try {
            $app->save();
            return response()->json([
                'exist' => true,
                'name' => $patient->name,
                'id' => $patient->id,
                'appID' => $app->id,
                'appNum' => $num,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'exist' => false,
            ]);
        }
    }

    public function editPatientview(Request $request)
    {
        // dd($request->reg_pid);
        $user = Auth::user();
        // $data = DB::table('patients')->select('*')->where('id',$request->reg_pid)->first();
        $data = Patients::find($request->reg_pid);
        return view('patient.edit_patient_view', ['title' => "Edit Patient", 'patient' => $data]);
    }

    public function updatePatient(Request $result)
    {
        // dd($result->reg_pbd);
        $user = Auth::user();
        
        $query = DB::table('patients')
            ->where('id', $result->reg_pid)
            ->update(array(
                'name' => $result->reg_pname,
                'address' => $result->reg_paddress,
                'sex' => $result->reg_psex,
                'bod' => $result->reg_pbd,
                'occupation' => $result->reg_poccupation,
                'nic' => $result->reg_pnic,
                'telephone' => $result->reg_ptel,
            ));

        if ($query) {
            //activity log
            activity()->performedOn($user)->log('Patient details updated!');
            return redirect()
                ->route('searchPatient')
                ->with('success', 'You have successfully updated patient details.');
        } else {
            return redirect()
                ->route('searchPatient')
                ->with('unsuccess', 'Error in Updating details !!!');
        }

    }
}
