@extends('template.main')

@section('title', $title)

@section('content_title',"Pharmacy")
@section('content_description',"Issue Medicines here.")
@section('breadcrumbs')

<ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="active">Issue Medicines</li>
</ol>
@endsection

@section('main_content')

<div class="row mt-5 pt-5">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="box box-info" id="issuemedicine1">
            <div class="box-header with-border">
                <h3 class="box-title">{{__('Enter Patient Registration Number Or The Appointment Number')}}</h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="p_id" class="control-label" style="font-size:18px">
                        {{__('Registration or Appointment No:')}}
                    </label>
                    <input type="number" class="form-control mt-2" name="patientid" 
                           id="p_id" placeholder="Enter Registration or Appointment Number" 
                           autofocus>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-info" onclick="checkPatient()">
                        <i class="fas fa-search"></i> Check
                    </button>
                </div>
                
                <div id="searchResult" style="display:none; margin-top: 20px;">
                    <div class="alert alert-danger" id="errorMessage" style="display:none;"></div>
                </div>
            </div>
        </div>

        <div class="box box-info" id="issuemedicine2" style="display:none">
            <div class="box-header with-border">
                <h3 class="box-title">Approved to Issue Medicine</h3>
            </div>
            <div class="box-body mt-0">
                <h4>Registration No : <span id="patient_id"></span></h4>
                <h4>Patient Name : <span id="p_name"></span></h4>
                <h4>Appointment No &nbsp;: <span id="p_appnum"></span></h4>
                
                <div class="mt-4">
                    <button id="btn-issue" type="button" class="btn btn-primary btn-lg" onclick="go()">
                        <i class="fas fa-pills"></i> Issue Medicine Now
                    </button>
                    <button type="button" class="btn btn-warning btn-lg" onclick="cancel()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>

<script>
    var presid = null;
    
    function checkPatient() {
        var patientId = document.getElementById("p_id").value;
        var searchResult = document.getElementById("searchResult");
        var errorMessage = document.getElementById("errorMessage");
        
        // Hide previous messages
        searchResult.style.display = "none";
        errorMessage.style.display = "none";
        
        if (!patientId || patientId < 1) {
            errorMessage.textContent = "Please enter a valid registration or appointment number!";
            searchResult.style.display = "block";
            errorMessage.style.display = "block";
            return;
        }
        
        // Show loading
        var checkBtn = document.querySelector('.btn.btn-info');
        var originalText = checkBtn.innerHTML;
        checkBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
        checkBtn.disabled = true;
        
        var data = new FormData;
        data.append('pNum', patientId);
        data.append('_token', '{{csrf_token()}}');
        
        $.ajax({
            type: "POST",
            url: "{{route('issueMedicine2')}}",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            error: function(xhr, status, error) {
                console.error('Error:', error);
                errorMessage.textContent = "Connection error. Please try again.";
                searchResult.style.display = "block";
                errorMessage.style.display = "block";
                checkBtn.innerHTML = originalText;
                checkBtn.disabled = false;
            },
            success: function(response) {
                console.log('Response:', response);
                checkBtn.innerHTML = originalText;
                checkBtn.disabled = false;
                
                if(response.exist) {
                    // Update display
                    $("#p_name").text(response.name || 'N/A');
                    $("#patient_id").text(response.pNUM || 'N/A');
                    $("#p_appnum").text(response.appNum || 'N/A');
                    presid = response.pres_id;
                    
                    // Show the issue section
                    $("#issuemedicine2").slideDown(1000);
                    $("#issuemedicine1").slideUp(1000);
                    
                    // Focus on the issue button
                    setTimeout(function() {
                        $("#btn-issue").focus();
                    }, 1100);
                    
                } else {
                    errorMessage.textContent = response.message || "No prescription found for today!";
                    searchResult.style.display = "block";
                    errorMessage.style.display = "block";
                }
            }
        });
    }
    
    function go() {
        if (presid) {
            window.location.href = "/issue/" + presid;
        } else {
            alert("Prescription ID not found. Please search again.");
        }
    }
    
    function cancel() {
        if (confirm("Are you sure you want to cancel?")) {
            // Reset the form
            $("#p_id").val('');
            
            // Show search and hide issue section
            $("#issuemedicine2").slideUp(1000);
            $("#issuemedicine1").slideDown(1000);
            
            // Hide any error messages
            $("#searchResult").hide();
            
            // Reset presid
            presid = null;
            
            // Focus back on search
            setTimeout(function() {
                $("#p_id").focus();
            }, 1100);
        }
    }
    
    // Allow pressing Enter key to submit
    $(document).ready(function() {
        $("#p_id").keypress(function(e) {
            if (e.which == 13) { // Enter key
                e.preventDefault();
                checkPatient();
            }
        });
        
        // Auto-focus on input
        $("#p_id").focus();
    });
</script>

@endsection