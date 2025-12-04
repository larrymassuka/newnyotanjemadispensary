@extends('template.main')

@section('title', $title)

@section('content_title',__('Check Patient'))
@section('content_description',__('Check Patient here and update history from here !'))
@section('breadcrumbs')

<ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="active">Check Patient</li>
</ol>
@endsection

@section('main_content')

<script>
    $(document).ready(function () {
        // Auto-focus on the input field
        $("#appNum").focus();
        
        // Submit form on Enter key
        $("#appNum").keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $("#checkPatientForm").submit();
            }
        });
    });
</script>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="box box-success mt-5">
            <div class="box-header with-border">
                <h3 class="box-title">{{__('Check Patient - Direct Access')}}</h3>
            </div>
            <div class="box-body">
                <form class="pl-5 pr-5 pb-5" method="post" action="{{route('checkPatient')}}" id="checkPatientForm">
                    @csrf
                    <h3>{{__('Enter Appointment Number Or Patient Number')}}</h3>
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon"><i class="fas fa-search"></i></span>
                        <input id="appNum" name="appNum" class="form-control input-lg" type="number" 
                               placeholder="Enter Appointment No or Patient ID" autofocus required>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-arrow-right"></i> Go Directly
                            </button>
                        </span>
                    </div>
                    <input name="pid" type="hidden" id="pnum">
                    <p id="validation" class="mt-2 text-danger"></p>
                    
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="icon fa fa-info-circle"></i>
                            <strong>Quick Access:</strong> Enter the appointment number or patient registration number 
                            and press Enter or click the arrow button to go directly to the patient checking page.
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        @if (session()->has('fail'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            {{session()->get('fail')}}
        </div>
        @endif
    </div>
    <div class="col-md-1"></div>
</div>

@endsection