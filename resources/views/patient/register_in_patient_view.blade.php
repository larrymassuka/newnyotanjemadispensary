@extends('template.main')

@section('title', $title)

@section('content_title',__('In Patient Registration'))
@section('content_description',__('Register New In Patients Here.'))
@section('breadcrumbs')

<ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="active">Inpatient Registration</li>
</ol>
@endsection

@section('main_content')
{{--  patient registration  --}}

<div @if (session()->has('regpsuccess') || session()->has('regpfail')) style="margin-bottom:0;margin-top:3vh" @else
    style="margin-bottom:0;margin-top:3vh" @endif class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        @if (session()->has('regpsuccess'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Success!</h4>
            {{session()->get('regpsuccess')}}
        </div>
        @endif
        @if (session()->has('regpfail'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            {{session()->get('regpfail')}}
        </div>
        @endif
    </div>
    <div class="col-md-1"></div>
</div>

<div class="box box-primary" id="reginpatient2" style="display:none">
    <div class="box-header with-border bg-primary">
        <h3 class="box-title text-white"><i class="fas fa-hospital-user"></i> {{__('Inpatient Registration Form')}}</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form method="post" action="{{ route('save_inpatient') }}" class="form-horizontal" id="inpatientForm">
        {{csrf_field()}}
        <div class="box-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Patient information loaded from outpatient record. Inpatient record will be created with a unique ID.
            </div>

            {{-- Patient Reference Section --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="outpatient_reg" class="col-sm-3 control-label">
                            <i class="fas fa-user-injured"></i> {{__('Outpatient Registration No')}}
                        </label>
                        <div class="col-sm-6">
                            <input type="hidden" name="patient_id" id="patient_id_hidden" required>
                            <input type="text" readonly class="form-control outpatient-field" 
                                   id="outpatient_reg_display" placeholder="Will auto-fill">
                            <small class="text-muted"><i class="fas fa-history"></i> Original outpatient registration number</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Patient Details Section --}}
            <div class="box-header with-border" style="margin-top:20px; margin-bottom:15px">
                <h4 class="box-title"><i class="fas fa-user-circle"></i> {{__('Patient Information')}}</h4>
            </div>

            <div class="form-group">
                <label for="patient_name" class="col-sm-2 control-label">{{__('Full Name')}}</label>
                <div class="col-sm-10">
                    <input type="text" readonly class="form-control outpatient-field" name="reg_pname" id="patient_name" placeholder="Patient Full Name">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="patient_nic" class="col-sm-4 control-label">{{__('NIC Number')}}</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control outpatient-field" name="reg_pnic" id="patient_nic" placeholder="National Identity Card Number">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="patient_telephone" class="col-sm-4 control-label">{{__('Telephone')}}</label>
                        <div class="col-sm-8">
                            <input type="tel" readonly class="form-control outpatient-field" name="reg_ptel" id="patient_telephone" placeholder="Patient Telephone Number">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="patient_address" class="col-sm-2 control-label">{{__('Address')}}</label>
                <div class="col-sm-10">
                    <input type="text" readonly class="form-control outpatient-field" name="reg_paddress" id="patient_address" placeholder="Patient Address">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="patient_occupation" class="col-sm-4 control-label">{{__('Occupation')}}</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control outpatient-field" name="reg_poccupation" id="patient_occupation" placeholder="Patient Occupation">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">{{__('Sex')}}</label>
                        <div class="col-sm-4">
                            <select disabled class="form-control outpatient-field" name="reg_psex" id="patient_sex">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <label for="patient_age" class="col-sm-2 control-label">{{__('Age')}}</label>
                        <div class="col-sm-2">
                            <input type="text" readonly class="form-control outpatient-field" name="reg_page" id="patient_age" placeholder="Age">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ward Selection Section --}}
            <div class="box-header with-border" style="margin-top:20px; margin-bottom:15px">
                <h4 class="box-title"><i class="fas fa-bed"></i> {{__('Ward & Admission Details')}}</h4>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">{{__('Select Ward')}}<span style="color:red">*</span></label>
                <div class="col-sm-6">
                    <select required class="form-control select2" name="ward_id" id="ward_select" style="width: 100%;">
                        <option value="">-- Select Ward --</option>
                        @if(isset($wards) && $wards->count() > 0)
                            @foreach ($wards as $ward)
                                <option value="{{ $ward->id }}" 
                                        data-available="{{ $ward->available_beds ?? $ward->free_beds ?? 0 }}"
                                        data-total="{{ $ward->total_beds ?? 0 }}"
                                        data-ward-no="{{ $ward->ward_no }}"
                                        data-doctor="{{ $ward->doctor_name ?? 'Not Assigned' }}">
                                    Ward {{ $ward->ward_no }} - {{ ucwords($ward->name) }}
                                    @if(($ward->available_beds ?? $ward->free_beds ?? 0) > 0)
                                        ({{ $ward->available_beds ?? $ward->free_beds ?? 0 }} beds available)
                                    @else
                                        <span class="text-red">(FULL)</span>
                                    @endif
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>No wards available in the system</option>
                        @endif
                    </select>
                    <div id="ward_details" class="mt-2" style="display:none;">
                        <div class="callout callout-info" style="margin-bottom: 0; padding: 10px;">
                            <h5><i class="icon fa fa-info"></i> Ward Details</h5>
                            <p id="ward_info_text"></p>
                            <p id="bed_info_text"></p>
                            <p id="doctor_info_text"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Medical Details Section --}}
            <div class="box-header with-border" style="margin-top:20px; margin-bottom:15px">
                <h4 class="box-title"><i class="fas fa-stethoscope"></i> {{__('Medical Information')}}</h4>
            </div>

            <div class="form-group">
                <label for="disease" class="col-sm-2 control-label">{{__('Diagnosis')}}<span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <input type="text" required class="form-control" id="disease" 
                           placeholder="Enter primary diagnosis / disease" name="disease" />
                    <small class="text-muted">Main reason for admission</small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">{{__('Illness Duration')}}<span style="color:red">*</span></label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                        <input type="number" required min="1" max="3650" step="1" class="form-control" 
                               name="duration" id="duration" placeholder="Number of days">
                        <span class="input-group-addon">days</span>
                    </div>
                    <small class="text-muted">How long has the patient been ill?</small>
                </div>
            </div>

            <div class="form-group">
                <label for="condition" class="col-sm-2 control-label">{{__('Current Condition')}}<span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <textarea class="form-control" required name="condition" id="condition" rows="4" 
                              placeholder="Describe patient's current condition, symptoms, and observations..."></textarea>
                    <small class="text-muted">Detailed description of patient's current health status</small>
                </div>
            </div>

            <div class="form-group">
                <label for="certified_officer" class="col-sm-2 control-label">{{__('Certified by')}}<span style="color:red">*</span></label>
                <div class="col-sm-4">
                    <input type="text" readonly value="{{ ucWords(Auth::user()->name) }}" required 
                           class="form-control bg-light" id="certified_officer" name="certified_officer" />
                    <small class="text-muted">Admitting doctor / medical officer</small>
                </div>
            </div>

            {{-- Admission Information --}}
            <div class="form-group">
                <label class="col-sm-2 control-label">{{__('Admission Date')}}</label>
                <div class="col-sm-4">
                    <input type="text" readonly class="form-control bg-light" 
                           value="{{ date('Y-m-d H:i:s') }}" id="admission_date">
                    <small class="text-muted">Date and time of admission</small>
                </div>
            </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-default btn-lg" onclick="cancelRegistration()" style="width: 100%;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn" style="width: 100%;">
                        <i class="fas fa-hospital"></i> Register Inpatient
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Search Section --}}
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="box box-primary" id="reginpatient1">
            <div class="box-header with-border bg-primary">
                <h3 class="box-title text-white"><i class="fas fa-search"></i> {{__('Search Patient for Admission')}}</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Important:</strong> Enter the outpatient registration number to admit patient. 
                    A new inpatient record will be created with a unique ID.
                </div>
                
                <div class="form-group">
                    <label for="pID" class="control-label" style="font-size:18px">
                        <i class="fas fa-id-card"></i> {{__('Outpatient Registration Number:')}}
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon"><i class="fas fa-hashtag"></i></span>
                        <input type="number" class="form-control" id="pID"
                            placeholder="Enter outpatient registration number" autofocus>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-info btn-flat" onclick="searchPatient()">
                                <i class="fa fa-search"></i> Search & Continue
                            </button>
                        </span>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Enter the patient's outpatient registration number (e.g., 230101001)
                    </small>
                </div>
                
                {{-- Search Results Area --}}
                <div id="search_result" class="mt-3" style="display:none;">
                    <div class="alert alert-danger" id="error_msg" style="display:none;"></div>
                    <div class="alert alert-success" id="success_msg" style="display:none;"></div>
                    <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
                </div>

                {{-- Quick Stats --}}
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $wards->count() ?? 0 }}</h3>
                                <p>Available Wards</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-hospital"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @php
                            $totalBeds = $wards->sum('total_beds') ?? 0;
                            $availableBeds = 0;
                            foreach ($wards as $ward) {
                                $availableBeds += $ward->available_beds ?? $ward->free_beds ?? 0;
                            }
                        @endphp
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>{{ $availableBeds }}</h3>
                                <p>Available Beds</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-bed"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>{{ $totalBeds - $availableBeds }}</h3>
                                <p>Occupied Beds</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-procedures"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
                <small class="text-muted">
                    <i class="fa fa-info-circle"></i> 
                    Note: Patient must be registered as an outpatient before admission. 
                    For new patients, register as outpatient first.
                </small>
            </div>
            <!-- /.box-footer -->
        </div>
    </div>
    <div class="col-md-1"></div>
</div>

<script>
    // Search patient function
    function searchPatient() {
        const patientId = document.getElementById("pID").value.trim();
        const searchResult = document.getElementById("search_result");
        const errorMsg = document.getElementById("error_msg");
        const successMsg = document.getElementById("success_msg");
        const warningMsg = document.getElementById("warning_msg");
        
        // Hide all messages
        searchResult.style.display = "none";
        errorMsg.style.display = "none";
        successMsg.style.display = "none";
        warningMsg.style.display = "none";
        
        // Validate input
        if (!patientId || patientId.length < 3) {
            errorMsg.innerHTML = "<i class='fa fa-exclamation-circle'></i> Please enter a valid outpatient registration number (minimum 3 digits)";
            searchResult.style.display = "block";
            errorMsg.style.display = "block";
            return;
        }
        
        // Prepare data
        const formData = new FormData();
        formData.append('pNum', patientId);
        formData.append('_token', '{{ csrf_token() }}');
        
        // Show loading
        const searchBtn = document.querySelector('.btn.btn-info');
        const originalText = searchBtn.innerHTML;
        searchBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Searching...';
        searchBtn.disabled = true;
        
        // Make AJAX request
        fetch("{{ route('regInPatient') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            // Reset button
            searchBtn.innerHTML = originalText;
            searchBtn.disabled = false;
            
            if (response.exist) {
                // Check if already admitted
                if (response.already_admitted) {
                    warningMsg.innerHTML = `
                        <i class='fa fa-exclamation-triangle'></i> 
                        <strong>Patient Already Has Inpatient Record!</strong><br>
                        Patient <strong>${response.name}</strong> already has an inpatient record.<br>
                        Inpatient Reference: <strong>${response.inpatient_no || 'Unknown'}</strong><br>
                        <small>Cannot create another inpatient record for the same patient.</small>
                    `;
                    searchResult.style.display = "block";
                    warningMsg.style.display = "block";
                    return;
                }
                
                // Fill patient details
                document.getElementById("patient_name").value = response.name || '';
                document.getElementById("patient_age").value = response.age || '';
                document.getElementById("patient_sex").value = response.sex || '';
                document.getElementById("patient_telephone").value = response.telephone || '';
                document.getElementById("patient_nic").value = response.nic || '';
                document.getElementById("patient_address").value = response.address || '';
                document.getElementById("patient_occupation").value = response.occupation || '';
                
                // Set outpatient registration
                document.getElementById("outpatient_reg_display").value = response.id || '';
                document.getElementById("patient_id_hidden").value = response.id || '';
                
                // Show success message
                successMsg.innerHTML = `
                    <i class='fa fa-check-circle'></i> 
                    <strong>Patient Found!</strong><br>
                    Patient: <strong>${response.name}</strong> (${response.age} years, ${response.sex})<br>
                    Outpatient Registration: <strong>${response.id}</strong><br>
                    <small>Please complete the admission details below. A new inpatient record ID will be generated automatically.</small>
                `;
                searchResult.style.display = "block";
                successMsg.style.display = "block";
                
                // Show registration form
                $("#reginpatient2").slideDown(1000);
                $("#reginpatient1").slideUp(1000);
                
                // Scroll to form
                $('html, body').animate({
                    scrollTop: $("#reginpatient2").offset().top - 20
                }, 1000);
                
                // Focus on first required field
                setTimeout(() => {
                    $("#disease").focus();
                }, 1100);
                
            } else {
                // Show error
                errorMsg.innerHTML = `
                    <i class='fa fa-exclamation-circle'></i> 
                    <strong>Patient Not Found!</strong><br>
                    ${response.message || 'No patient found with the provided registration number.'}<br>
                    <small>Please check the registration number and try again.</small>
                `;
                searchResult.style.display = "block";
                errorMsg.style.display = "block";
                
                // Focus back on input
                $("#pID").focus().select();
            }
        })
        .catch(error => {
            // Reset button
            searchBtn.innerHTML = originalText;
            searchBtn.disabled = false;
            
            // Show error
            errorMsg.innerHTML = `
                <i class='fa fa-exclamation-circle'></i> 
                <strong>Connection Error!</strong><br>
                Unable to connect to the server. Please check your internet connection and try again.<br>
                <small>Error: ${error.message}</small>
            `;
            searchResult.style.display = "block";
            errorMsg.style.display = "block";
            console.error('Error:', error);
        });
    }

    // Cancel registration
    function cancelRegistration() {
        if (confirm("Are you sure you want to cancel? All entered data will be lost.")) {
            // Reset form
            document.getElementById("inpatientForm").reset();
            
            // Clear auto-filled fields
            $("#outpatient_reg_display").val('');
            $("#patient_id_hidden").val('');
            $("#patient_name").val('');
            $("#patient_age").val('');
            $("#patient_sex").val('');
            $("#patient_telephone").val('');
            $("#patient_nic").val('');
            $("#patient_address").val('');
            $("#patient_occupation").val('');
            
            // Reset ward selection
            $("#ward_select").val('').trigger('change');
            
            // Hide messages
            $("#search_result").hide();
            $("#ward_details").hide();
            
            // Show search and hide form
            $("#reginpatient2").slideUp(1000);
            $("#reginpatient1").slideDown(1000);
            
            // Focus back on search
            setTimeout(() => {
                $("#pID").focus().select();
            }, 1100);
        }
    }

    // Ward selection handler
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap'
        });
        
        // Ward selection change
        $('#ward_select').on('change', function() {
            const selected = $(this).find('option:selected');
            const availableBeds = selected.data('available') || 0;
            const totalBeds = selected.data('total') || 0;
            const wardNo = selected.data('ward-no') || '';
            const doctor = selected.data('doctor') || 'Not Assigned';
            
            if (this.value) {
                // Show ward details
                $('#ward_details').show();
                
                // Update ward information
                $('#ward_info_text').html(`<strong>Ward ${wardNo}</strong> selected`);
                
                // Update bed information
                if (availableBeds > 0) {
                    $('#bed_info_text').html(
                        `<span class="text-green"><i class="fa fa-bed"></i> ${availableBeds} of ${totalBeds} beds available</span>`
                    );
                } else {
                    $('#bed_info_text').html(
                        `<span class="text-red"><i class="fa fa-bed"></i> No beds available! (${totalBeds} total beds)</span>`
                    );
                }
                
                // Update doctor information
                $('#doctor_info_text').html(`<i class="fa fa-user-md"></i> Ward Doctor: ${doctor}`);
                
            } else {
                $('#ward_details').hide();
            }
        });
        
        // Form validation
        $('#inpatientForm').on('submit', function(e) {
            // Check if ward is selected
            const wardId = $('#ward_select').val();
            if (!wardId) {
                e.preventDefault();
                alert('Please select a ward for admission.');
                $('#ward_select').focus();
                return false;
            }
            
            // Check bed availability
            const selectedWard = $('#ward_select').find('option:selected');
            const availableBeds = selectedWard.data('available') || 0;
            
            if (availableBeds <= 0) {
                e.preventDefault();
                alert('Cannot admit to this ward - no beds available! Please select another ward.');
                $('#ward_select').focus();
                return false;
            }
            
            // Validate diagnosis
            const diagnosis = $('#disease').val().trim();
            if (diagnosis.length < 3) {
                e.preventDefault();
                alert('Please enter a valid diagnosis (minimum 3 characters).');
                $('#disease').focus();
                return false;
            }
            
            // Validate condition
            const condition = $('#condition').val().trim();
            if (condition.length < 10) {
                e.preventDefault();
                alert('Please provide a more detailed description of the patient\'s condition (minimum 10 characters).');
                $('#condition').focus();
                return false;
            }
            
            // Validate duration
            const duration = $('#duration').val();
            if (!duration || duration < 1) {
                e.preventDefault();
                alert('Please enter a valid duration (minimum 1 day).');
                $('#duration').focus();
                return false;
            }
            
            // Show loading on submit button
            $('#submitBtn').html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            $('#submitBtn').prop('disabled', true);
            
            return true;
        });
        
        // Allow Enter key in search field
        $('#pID').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchPatient();
            }
        });
        
        // Auto-focus on search field when page loads
        setTimeout(() => {
            $('#pID').focus();
        }, 500);
        
        // Also rename the old function for backward compatibility
        window.registerinpatientfunction = searchPatient;
    });
</script>

<style>
    .outpatient-field {
        background-color: #f8f9fa !important;
        cursor: not-allowed;
        border-left: 4px solid #007bff !important;
    }
    
    .bg-light {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }
    
    .select2-container--bootstrap .select2-selection {
        border-radius: 4px !important;
        border: 1px solid #d2d6de !important;
    }
    
    .small-box {
        border-radius: 5px;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    }
    
    .small-box > .inner {
        padding: 10px;
    }
    
    .small-box h3 {
        font-size: 38px;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    
    .small-box p {
        font-size: 15px;
    }
    
    .small-box .icon {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 0;
        font-size: 70px;
        color: rgba(0,0,0,0.15);
    }
    
    .callout {
        border-radius: 3px;
        margin: 0 0 20px 0;
        padding: 15px 30px 15px 15px;
        border-left: 5px solid #eee;
    }
    
    .callout-info {
        background-color: #f0f7fd;
        border-color: #007bff;
    }
    
    .text-red {
        color: #dd4b39 !important;
    }
    
    .text-green {
        color: #00a65a !important;
    }
    
    .text-yellow {
        color: #f39c12 !important;
    }
    
    .bg-aqua {
        background-color: #00c0ef !important;
        color: white !important;
    }
    
    .bg-green {
        background-color: #00a65a !important;
        color: white !important;
    }
    
    .bg-yellow {
        background-color: #f39c12 !important;
        color: white !important;
    }
</style>
@endsection