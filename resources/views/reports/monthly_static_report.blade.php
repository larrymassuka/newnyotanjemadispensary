@extends('template.main')

@section('title', $title)

@section('content_title',"Monthly Statistics Report")
@section('content_description',"Monthly Hospital Statistics Summary")
@section('breadcrumbs')

<ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="active">Monthly Report</li>
</ol>
@endsection

@section('main_content')

<style>
    @media print {
        .no-print, .no-print * {
            display: none !important;
        }
        body {
            font-size: 11pt;
            padding: 0;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 100%;
        }
        .print-table th, .print-table td {
            padding: 4px !important;
        }
    }
    
    @media screen {
        body {
            background: #f5f5f5;
        }
        .report-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 900px;
        }
    }
    
    .hospital-header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 15px;
    }
    
    .section-title {
        background: #f8f9fa;
        padding: 10px;
        border-left: 4px solid #007bff;
        margin: 25px 0 15px 0;
        font-weight: bold;
    }
    
    .stat-box {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background: #f8f9fa;
    }
    
    .stat-label {
        font-weight: bold;
        color: #495057;
        margin-bottom: 5px;
    }
    
    .stat-value {
        font-size: 18px;
        font-weight: bold;
        color: #007bff;
    }
    
    .print-table {
        width: 100%;
        margin-bottom: 20px;
    }
    
    .print-table th {
        background: #343a40;
        color: white;
        padding: 8px;
        text-align: left;
    }
    
    .print-table td {
        padding: 8px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .print-table tr:nth-child(even) {
        background: #f8f9fa;
    }
    
    .footer-note {
        margin-top: 30px;
        padding-top: 15px;
        border-top: 1px dashed #dee2e6;
        color: #6c757d;
        font-style: italic;
    }
</style>

<div class="report-container">
    <div class="hospital-header">
        <h2 class="mb-2">NEW NYOTA NJEMA DISPENSARY</h2>
        <h4 class="text-muted mb-3">Monthly Statistics Report</h4>
        <p class="mb-1">
            <i class="fas fa-calendar-alt"></i> Report Period: {{ date('F Y') }}
        </p>
        <p class="mb-0">
            <i class="fas fa-clock"></i> Generated: {{ date('Y-m-d H:i:s') }}
        </p>
    </div>

    <div class="row">
        <!-- Outpatient Statistics -->
        <div class="col-md-6">
            <div class="section-title">
                <i class="fas fa-user-md"></i> Outpatient Department
            </div>
            
            <div class="stat-box">
                <div class="stat-label">Total Appointments</div>
                <div class="stat-value">{{ $total ?? 0 }}</div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="stat-box">
                        <div class="stat-label">First Arrivals</div>
                        <div class="stat-value">{{ $fa ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-box">
                        <div class="stat-label">Second Arrivals</div>
                        <div class="stat-value">{{ $sa ?? 0 }}</div>
                    </div>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="stat-label">Average Daily Patients</div>
                <div class="stat-value">{{ $avgpatient ?? 0 }}</div>
            </div>
        </div>
        
        <!-- Inpatient Statistics -->
        <div class="col-md-6">
            <div class="section-title">
                <i class="fas fa-procedures"></i> Inpatient Department
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="stat-box">
                        <div class="stat-label">Total Wards</div>
                        <div class="stat-value">{{ $wardcnt ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-box">
                        <div class="stat-label">Total Beds</div>
                        <div class="stat-value">{{ $bedcnt ?? 0 }}</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="stat-box">
                        <div class="stat-label">Admissions This Month</div>
                        <div class="stat-value">{{ $inpcnt ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-box">
                        <div class="stat-label">Discharged This Month</div>
                        <div class="stat-value">{{ $dispcnt ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Staff Attendance -->
    <div class="section-title">
        <i class="fas fa-user-clock"></i> Staff Attendance Summary
    </div>
    
    <table class="print-table">
        <thead>
            <tr>
                <th>Staff Type</th>
                <th>Duty Days This Month</th>
                <th>Total Employees</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Administrative Staff</td>
                <td>{{ $admindaycnt ?? 0 }} days</td>
                <td>{{ $noemp ?? 0 }}</td>
            </tr>
            <tr>
                <td>Medical Officers</td>
                <td>{{ $doctordaycnt ?? 0 }} days</td>
                <td>{{ $noemp ?? 0 }}</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Appointments Summary -->
    <div class="section-title">
        <i class="fas fa-calendar-check"></i> Monthly Appointments Summary
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="stat-box text-center">
                <div class="stat-label">Total Appointments</div>
                <div class="stat-value" style="font-size: 24px;">{{ $total ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box text-center">
                <div class="stat-label">Unique Patients</div>
                <div class="stat-value" style="font-size: 24px;">{{ $fa ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box text-center">
                <div class="stat-label">Follow-up Visits</div>
                <div class="stat-value" style="font-size: 24px;">{{ $sa ?? 0 }}</div>
            </div>
        </div>
    </div>
    
    <!-- Medicine Summary (Placeholder) -->
    <div class="section-title">
        <i class="fas fa-pills"></i> Medicine Summary
    </div>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Medicine inventory and dispensing reports are available in separate pharmacy reports.
    </div>
    
    <!-- Certification Footer -->
    <div class="footer-note">
        <div class="row">
            <div class="col-md-8">
                <p><i class="fas fa-check-circle"></i> I certify that the above statistical reports and information are true and accurate to the best of my knowledge.</p>
            </div>
            <div class="col-md-4 text-right">
                <p><strong>Certified By:</strong></p>
                <p>.......................................</p>
                <p>Position: _________________________</p>
                <p>Date: {{ date('Y-m-d') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Print Buttons -->
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg mr-3">
            <i class="fas fa-print"></i> Print Report
        </button>
        <button onclick="window.history.back()" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>
</div>

<script>
    // Auto-format numbers
    document.addEventListener('DOMContentLoaded', function() {
        // Format all stat values with commas
        document.querySelectorAll('.stat-value').forEach(function(element) {
            let value = element.textContent;
            if (!isNaN(value) && value !== '') {
                element.textContent = parseInt(value).toLocaleString();
            }
        });
    });
    
    // Auto-print option (uncomment if needed)
    /*
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 1000);
    };
    */
</script>

@endsection