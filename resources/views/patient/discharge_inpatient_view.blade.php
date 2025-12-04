@extends('template.main')

@section('title', $title)

@section('content_title',__('Discharge Patient'))
@section('content_description',__('Discharge In-Patients Here.'))
@section('breadcrumbs')

<ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="active">Discharge Inpatient</li>
</ol>
@endsection

@section('main_content')

<div @if (session()->has('regpsuccess') || session()->has('regpfail')) style="margin-bottom:0;margin-top:3vh" @else
    style="margin-bottom:0;margin-top:8vh" @endif class="row">
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

<div class="box box-info" id="disinpatient2" style="display:none">
    <div class="box-header with-border">
        <h3 class="box-title">{{__('Medical Officer - Abstract of Case')}}</h3>
    </div>
    <form method="post" action="{{ route('save_disinpatient') }}" class="form-horizontal" id="dischargeForm">
        {{csrf_field()}}
        <div class="box-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Complete the discharge details for the patient below.
            </div>

            <div class="form-group">
                <label for="patient_id" class="col-sm-2 control-label">{{__('Registration No')}}</label>
                <div class="col-sm-2">
                    <input type="text" required readonly class="form-control" name="reg_pid" id="patient_id">
                </div>
            </div>

            <div class="form-group">
                <label for="patient_name" class="col-sm-2 control-label">{{__('Name')}}</label>
                <div class="col-sm-10">
                    <input type="text" required readonly class="form-control" name="reg_pname" id="patient_name">
                </div>
            </div>

            <div class="form-group">
                <label for="patient_address" class="col-sm-2 control-label">{{__('Address')}}</label>
                <div class="col-sm-10">
                    <input type="text" required readonly class="form-control" name="reg_paddress" id="patient_address">
                </div>
            </div>

            <div class="form-group">
                <label for="patient_telephone" class="col-sm-2 control-label">{{__('Telephone')}}</label>
                <div class="col-sm-10">
                    <input type="tel" readonly class="form-control" name="reg_ptel" id="patient_telephone">
                </div>
            </div>

            <div class="form-group">
                <label for="ward_info" class="col-sm-2 control-label">{{__('Ward Information')}}</label>
                <div class="col-sm-10">
                    <input type="text" readonly class="form-control" id="ward_info">
                </div>
            </div>

            <div class="form-group">
                <label for="admission_date" class="col-sm-2 control-label">{{__('Admission Date')}}</label>
                <div class="col-sm-4">
                    <input type="text" readonly class="form-control" id="admission_date_display">
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail" class="col-sm-2 control-label">{{__('Discharge Summary:')}}<span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <textarea required class="form-control" name="reg_medicalofficer1" id="discharge_summary" rows="4"
                        placeholder="Enter discharge summary, condition at discharge, follow-up instructions..."></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="medofs2" class="col-sm-2 control-label">{{__('Discharged by')}}<span style="color:red">*</span></label>
                <div class="col-sm-10" id="al-box">
                    <input type="text" readonly value="{{Auth::user()->id}} ({{ucWords(Auth::user()->name)}})" 
                           required class="form-control" id="medofs2" name="reg_medicalofficer2">
                </div>
            </div>

            <div class="form-group">
                <label for="discharge_date" class="col-sm-2 control-label">{{__('Discharge Date')}}</label>
                <div class="col-sm-4">
                    <input type="text" readonly class="form-control" 
                           value="{{ date('Y-m-d H:i:s') }}" id="discharge_date">
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-success btn-lg pull-right">
                    <i class="fas fa-check"></i> Submit & Print Discharge
                </button>
                <button type="button" class="btn btn-default btn-lg pull-left" onclick="cancelDischarge()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </form>
</div>

<div class="row mt-5 pt-5">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="box box-info" id="disinpatient1">
            <div class="box-header with-border">
                <h3 class="box-title">{{__('Search Inpatient for Discharge')}}</h3>
            </div>
            <div class="box-body">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> 
                    Enter the inpatient's registration number to begin discharge process.
                </div>
                
                <div class="form-group">
                    <label for="pid" class="control-label" style="font-size:18px">
                        <i class="fas fa-id-card"></i> {{__('Patient Registration No:')}}
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon"><i class="fas fa-hashtag"></i></span>
                        <input type="number" class="form-control" id="pid"
                               placeholder="Enter Patient Registration Number" autofocus>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-info btn-flat" onclick="dischargeinpatientfunction()">
                                <i class="fa fa-search"></i> Search
                            </button>
                        </span>
                    </div>
                    <small class="text-muted">Enter the patient's registration number</small>
                </div>
                
                <div id="search_result" class="mt-3" style="display:none;">
                    <div class="alert alert-danger" id="error_msg" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>

<script>
    function dischargeinpatientfunction() {
        var patientId = document.getElementById("pid").value.trim();
        var searchResult = document.getElementById("search_result");
        var errorMsg = document.getElementById("error_msg");
        
        // Hide previous messages
        searchResult.style.display = "none";
        errorMsg.style.display = "none";
        
        if (!patientId || patientId.length < 3) {
            errorMsg.innerHTML = "<i class='fa fa-exclamation-circle'></i> Please enter a valid registration number (minimum 3 digits)";
            searchResult.style.display = "block";
            errorMsg.style.display = "block";
            return;
        }
        
        var data = new FormData;
        data.append('pNum', patientId);
        data.append('_token', '{{csrf_token()}}');

        // Show loading
        $(".btn.btn-info").html('<i class="fa fa-spinner fa-spin"></i> Searching...').prop('disabled', true);
        
        $.ajax({
            type: "post",
            url: "{{route('disInPatient')}}",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                errorMsg.innerHTML = "<i class='fa fa-exclamation-circle'></i> Connection error. Please try again.";
                searchResult.style.display = "block";
                errorMsg.style.display = "block";
                $(".btn.btn-info").html('<i class="fa fa-search"></i> Search').prop('disabled', false);
            },
            success: function (response) {
                console.log(response); // Debug
                $(".btn.btn-info").html('<i class="fa fa-search"></i> Search').prop('disabled', false);
                
                if(response.exist) {
                    // Fill patient details
                    $("#patient_name").val(response.name);
                    $("#patient_telephone").val(response.telephone);
                    $("#patient_address").val(response.address);
                    $("#patient_id").val(response.id);
                    
                    // Fill additional information if available
                    if (response.ward_id) {
                        $("#ward_info").val("Ward ID: " + response.ward_id);
                    }
                    
                    if (response.admission_date) {
                        var admissionDate = new Date(response.admission_date);
                        $("#admission_date_display").val(admissionDate.toLocaleDateString());
                    }
                    
                    // Show discharge form
                    $("#disinpatient2").slideDown(1000);
                    $("#disinpatient1").slideUp(1000);
                    
                    // Focus on discharge summary
                    setTimeout(function() {
                        $("#discharge_summary").focus();
                    }, 1100);
                    
                } else {
                    errorMsg.innerHTML = "<i class='fa fa-exclamation-circle'></i> " + 
                        (response.message || "Patient not found or not registered as an inpatient.");
                    searchResult.style.display = "block";
                    errorMsg.style.display = "block";
                }
            }
        });
    }
    
    function cancelDischarge() {
        if (confirm("Are you sure you want to cancel? All entered data will be lost.")) {
            // Reset form
            document.getElementById("dischargeForm").reset();
            
            // Hide discharge form and show search
            $("#disinpatient2").slideUp(1000);
            $("#disinpatient1").slideDown(1000);
            
            // Clear search result
            $("#search_result").hide();
            
            // Focus back on search input
            setTimeout(function() {
                $("#pid").focus().select();
            }, 1100);
        }
    }
    
    // Allow Enter key in search field
    $(document).ready(function() {
        $("#pid").keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                dischargeinpatientfunction();
            }
        });
        
        // Auto-focus on search
        setTimeout(function() {
            $("#pid").focus();
        }, 500);
    });
</script>

<style>
    .alert-warning {
        background-color: #fcf8e3;
        border-color: #faebcc;
        color: #8a6d3b;
    }
    
    .input-group-lg > .form-control,
    .input-group-lg > .input-group-addon,
    .input-group-lg > .input-group-btn > .btn {
        height: 46px;
        padding: 10px 16px;
        font-size: 18px;
        line-height: 1.3333333;
        border-radius: 6px;
    }
</style>

@endsection