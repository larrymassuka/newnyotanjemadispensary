@extends('template.main')

@section('title', $title)

@section('content_title', "Attendance Report")
@section('content_description', "Generate attendance reports for staff members")
@section('breadcrumbs')

<ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="active">Attendance Report</li>
</ol>
@endsection

@section('main_content')

<style>
    @media screen {
        body {
            background: #f5f5f5;
        }
        .report-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 800px;
        }
    }
    
    .form-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #007bff;
        margin-bottom: 25px;
    }
    
    .section-title {
        color: #007bff;
        font-weight: bold;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }
    
    .staff-type-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .staff-type-card:hover {
        border-color: #007bff;
        background: #f8f9fa;
    }
    
    .staff-type-card.selected {
        border-color: #007bff;
        background: #e7f3ff;
    }
    
    .staff-icon {
        font-size: 24px;
        color: #007bff;
        margin-right: 10px;
    }
    
    .date-input-group {
        position: relative;
    }
    
    .date-input-group .input-group-addon {
        background: #007bff;
        color: white;
        border: 1px solid #007bff;
    }
    
    .btn-generate {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s;
    }
    
    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,123,255,0.3);
    }
    
    .quick-date-btn {
        margin: 5px;
        font-size: 12px;
    }
</style>

<div class="report-container">
    <div class="text-center mb-4">
        <h2 class="mb-2"><i class="fas fa-calendar-check"></i> Attendance Report Generator</h2>
        <p class="text-muted">Generate detailed attendance reports for staff members</p>
    </div>

    <form method="post" action="{{ route('gen_att_reports') }}" id="attendanceForm">
        @csrf
        
        <div class="form-section">
            <h4 class="section-title"><i class="fas fa-users"></i> Select Staff Category</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="staff-type-card" onclick="selectStaffType('My Attendance')">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user staff-icon"></i>
                            <div>
                                <h5 class="mb-1">My Attendance</h5>
                                <p class="text-muted mb-0">View your own attendance record</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="staff-type-card" onclick="selectStaffType('All')">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users staff-icon"></i>
                            <div>
                                <h5 class="mb-1">All Staff</h5>
                                <p class="text-muted mb-0">All hospital staff members</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="staff-type-card" onclick="selectStaffType('Doctors')">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-md staff-icon"></i>
                            <div>
                                <h5 class="mb-1">Doctors</h5>
                                <p class="text-muted mb-0">Medical officers only</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="staff-type-card" onclick="selectStaffType('General Staff')">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-tie staff-icon"></i>
                            <div>
                                <h5 class="mb-1">General Staff</h5>
                                <p class="text-muted mb-0">Pharmacists & other staff</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="type" id="staffType" value="My Attendance" required>
        </div>
        
        <div class="form-section">
            <h4 class="section-title"><i class="fas fa-calendar-alt"></i> Select Date Range</h4>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <p class="text-muted mb-2">Quick Select:</p>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm quick-date-btn" onclick="setDateRange('today')">
                            Today
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm quick-date-btn" onclick="setDateRange('week')">
                            This Week
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm quick-date-btn" onclick="setDateRange('month')">
                            This Month
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm quick-date-btn" onclick="setDateRange('year')">
                            This Year
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="startDate" class="font-weight-bold">
                            <i class="fas fa-calendar-plus"></i> Start Date
                        </label>
                        <div class="date-input-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control datepicker" 
                                       id="startDate" name="start" 
                                       placeholder="Select start date" required
                                       value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="endDate" class="font-weight-bold">
                            <i class="fas fa-calendar-minus"></i> End Date
                        </label>
                        <div class="date-input-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control datepicker" 
                                       id="endDate" name="end" 
                                       placeholder="Select end date" required
                                       value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i> 
                Report will include: Total days worked, attendance percentage, and leave details.
            </div>
        </div>
        
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-generate btn-lg">
                <i class="fas fa-file-alt"></i> Generate Attendance Report
            </button>
        </div>
    </form>
    
    <!-- Report Preview (will be shown after generation) -->
    @if(isset($details) && count($details) > 0)
    <div class="mt-5">
        <div class="alert alert-success">
            <h4><i class="fas fa-check-circle"></i> Report Generated Successfully</h4>
            <p>Period: {{ $start ?? '' }} to {{ $end ?? '' }} | Type: {{ $type ?? '' }}</p>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Staff Type</th>
                        <th>Days Worked</th>
                        <th>Short Leaves</th>
                        <th>Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $staff)
                    <tr>
                        <td>{{ $staff->id }}</td>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->type }}</td>
                        <td>{{ $staff->attended ?? 0 }}</td>
                        <td>{{ $staff->shortleave ?? 0 }}</td>
                        <td>
                            @php
                                $totalDays = $staff->attended + $staff->shortleave;
                                $percentage = $totalDays > 0 ? round(($staff->attended / $totalDays) * 100, 1) : 0;
                            @endphp
                            <span class="badge badge-{{ $percentage >= 90 ? 'success' : ($percentage >= 75 ? 'warning' : 'danger') }}">
                                {{ $percentage }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="text-center no-print">
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Print Report
            </button>
            <a href="{{ route('all_print_preview', ['start' => $start, 'end' => $end, 'type' => $type]) }}" 
               class="btn btn-info" target="_blank">
                <i class="fas fa-external-link-alt"></i> Full Print Preview
            </a>
        </div>
    </div>
    @endif
</div>

<script>
    // Initialize staff type selection
    function selectStaffType(type) {
        // Update hidden input
        document.getElementById('staffType').value = type;
        
        // Update visual selection
        document.querySelectorAll('.staff-type-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Find and select the clicked card
        event.currentTarget.classList.add('selected');
    }
    
    // Initialize date pickers
    $(function() {
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    });
    
    // Quick date range functions
    function setDateRange(range) {
        const today = new Date();
        let startDate, endDate;
        
        switch(range) {
            case 'today':
                startDate = today;
                endDate = today;
                break;
            case 'week':
                startDate = new Date(today.setDate(today.getDate() - today.getDay()));
                endDate = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'year':
                startDate = new Date(today.getFullYear(), 0, 1);
                endDate = new Date(today.getFullYear(), 11, 31);
                break;
        }
        
        // Format dates as YYYY-MM-DD
        const formatDate = (date) => {
            return date.toISOString().split('T')[0];
        };
        
        $('#startDate').val(formatDate(startDate));
        $('#endDate').val(formatDate(endDate));
    }
    
    // Form validation
    document.getElementById('attendanceForm').addEventListener('submit', function(e) {
        const startDate = new Date($('#startDate').val());
        const endDate = new Date($('#endDate').val());
        
        if (startDate > endDate) {
            e.preventDefault();
            alert('Error: Start date cannot be after end date!');
            $('#startDate').focus();
            return false;
        }
        
        // Add loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating Report...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
    
    // Initialize with "My Attendance" selected
    document.addEventListener('DOMContentLoaded', function() {
        selectStaffType('My Attendance');
    });
</script>

@endsection