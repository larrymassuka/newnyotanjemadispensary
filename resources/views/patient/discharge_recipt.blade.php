@php
use App\Inpatient;
use App\Patients;
use App\Ward;

// Check if data is available
$hasData = isset($INPtableUpdate) && $INPtableUpdate;
$patient = $hasData ? Patients::find($INPtableUpdate->patient_id) : null;
$ward = $hasData && isset($INPtableUpdate->ward_id) ? Ward::find($INPtableUpdate->ward_id) : null;

@endphp
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
<style>
    @media print
    {    
        .no-print, .no-print *
        {
            display: none !important;
        }
        
        body {
            font-size: 12pt;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
        }
    }
    
    @media screen
    {
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .receipt-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
    }
    
    .hospital-header {
        border-bottom: 3px solid #007bff;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    
    .receipt-title {
        color: #007bff;
        font-weight: bold;
        margin-bottom: 30px;
    }
    
    .patient-info {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 30px;
    }
    
    .discharge-details {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .footer-note {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px dashed #dee2e6;
        font-style: italic;
        color: #6c757d;
    }
    
    .print-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
    
    .back-btn {
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 1000;
    }
</style>
<title>Discharge Receipt - {{ $hasData && $patient ? $patient->name : 'Patient' }}</title>
</head>
<body>

<div class="container">
    <div class="receipt-container">
        @if($hasData)
        <div class="hospital-header text-center">
            <h1 class="text-uppercase font-weight-bold">NEW NYOTA NJEMA DISPENSARY</h1>
            <h3 class="text-muted">Discharge Receipt</h3>
            <p class="mb-0">
                <i class="fas fa-map-marker-alt"></i> Hospital Address • 
                <i class="fas fa-phone"></i> Contact: XXX-XXXXXXX • 
                <i class="fas fa-envelope"></i> Email: info@hospital.com
            </p>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="patient-info">
                    <h4 class="receipt-title"><i class="fas fa-user-injured"></i> Patient Information</h4>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Patient Name:</strong></td>
                            <td>{{ $patient->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Registration No:</strong></td>
                            <td>{{ $patient->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Age/Sex:</strong></td>
                            <td>{{ $patient->getAge() }} years / {{ $patient->sex }}</td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td>{{ $patient->address }}</td>
                        </tr>
                        <tr>
                            <td><strong>Contact:</strong></td>
                            <td>{{ $patient->telephone }}</td>
                        </tr>
                        @if($ward)
                        <tr>
                            <td><strong>Ward:</strong></td>
                            <td>Ward {{ $ward->ward_no }} - {{ $ward->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="patient-info">
                    <h4 class="receipt-title"><i class="fas fa-calendar-alt"></i> Admission Details</h4>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Admission Date:</strong></td>
                            <td>{{ $INPtableUpdate->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Discharge Date:</strong></td>
                            <td>
                                @if(isset($INPtableUpdate->discharged_date))
                                    {{ \Carbon\Carbon::parse($INPtableUpdate->discharged_date)->format('Y-m-d H:i') }}
                                @else
                                    {{ date('Y-m-d H:i') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Length of Stay:</strong></td>
                            <td>
                                @php
                                    $admissionDate = \Carbon\Carbon::parse($INPtableUpdate->created_at);
                                    $dischargeDate = isset($INPtableUpdate->discharged_date) ? 
                                        \Carbon\Carbon::parse($INPtableUpdate->discharged_date) : 
                                        \Carbon\Carbon::now();
                                    $days = $admissionDate->diffInDays($dischargeDate);
                                @endphp
                                {{ $days }} day(s)
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Diagnosis:</strong></td>
                            <td>{{ $INPtableUpdate->disease }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="discharge-details">
            <h4 class="receipt-title"><i class="fas fa-file-medical-alt"></i> Discharge Summary</h4>
            
            <div class="mb-4">
                <h5>Condition at Discharge:</h5>
                <div class="p-3 bg-light rounded">
                    @if(isset($INPtableUpdate->description) && !empty($INPtableUpdate->description))
                        {{ $INPtableUpdate->description }}
                    @elseif(isset($INPtableUpdate->condition))
                        {{ $INPtableUpdate->condition }}
                    @else
                        Patient discharged in stable condition. Follow-up appointment recommended.
                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>Medications Prescribed:</h5>
                    <div class="p-3 bg-light rounded">
                        @if(isset($INPtableUpdate->medications) && !empty($INPtableUpdate->medications))
                            {{ $INPtableUpdate->medications }}
                        @else
                            As per discharge prescription
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5>Follow-up Instructions:</h5>
                    <div class="p-3 bg-light rounded">
                        @if(isset($INPtableUpdate->follow_up) && !empty($INPtableUpdate->follow_up))
                            {{ $INPtableUpdate->follow_up }}
                        @else
                            Follow up with primary physician in 1 week.
                            Return to emergency if symptoms worsen.
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <h5><i class="fas fa-user-md"></i> Admitting Physician</h5>
                    <p class="mb-1"><strong>Name:</strong> 
                        @if(isset($INPtableUpdate->certified_officer))
                            {{ $INPtableUpdate->certified_officer }}
                        @else
                            Dr. Not Specified
                        @endif
                    </p>
                    <p class="mb-0"><strong>Date:</strong> {{ $INPtableUpdate->created_at->format('Y-m-d') }}</p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <h5><i class="fas fa-user-md"></i> Discharging Physician</h5>
                    <p class="mb-1"><strong>Name:</strong> 
                        @if(isset($INPtableUpdate->discharged_officer))
                            {{ $INPtableUpdate->discharged_officer }}
                        @else
                            {{ ucwords(Auth::user()->name) }}
                        @endif
                    </p>
                    <p class="mb-0"><strong>Date:</strong> {{ date('Y-m-d') }}</p>
                </div>
            </div>
        </div>
        
        <div class="footer-note text-center">
            <p class="mb-2">
                <i class="fas fa-exclamation-circle"></i> 
                This discharge summary is for medical records purposes only.
            </p>
            <p class="mb-0">
                Receipt Generated: {{ date('Y-m-d H:i:s') }} | 
                Document ID: DCH-{{ strtoupper(uniqid()) }}
            </p>
        </div>
        
        @else
        <div class="alert alert-danger text-center">
            <h2><i class="fas fa-exclamation-triangle"></i> No Data Available</h2>
            <p>Discharge information could not be loaded. Please try again.</p>
            <a href="{{ route('discharge_inpatient') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Discharge Page
            </a>
        </div>
        @endif
    </div>
</div>

@if($hasData)
<div class="no-print">
    <button onclick="window.print()" class="btn btn-info btn-lg print-btn">
        <i class="fas fa-print"></i> Print Receipt
    </button>
    
    <a href="{{ route('discharge_inpatient') }}" class="btn btn-dark btn-lg back-btn">
        <i class="fas fa-arrow-left"></i> Back to Discharge
    </a>
</div>

<script>
    // Auto-print option (uncomment if you want auto-print)
    // window.onload = function() {
    //     setTimeout(function() {
    //         window.print();
    //     }, 1000);
    // };
    
    // After printing, redirect back
    window.onafterprint = function() {
        setTimeout(function() {
            window.location.href = "{{ route('discharge_inpatient') }}";
        }, 1000);
    };
</script>
@endif

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>