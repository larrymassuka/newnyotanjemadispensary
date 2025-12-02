@extends('template.main')

@section('title', $title)

@section('content_title',__('In Patient Registration'))
@section('content_description',__('Register New In Patients Here.'))
@section('breadcrumbs')

<!-- <ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="active">Here</li>
</ol> -->
@endsection

@section('main_content')
{{--  patient registration  --}}

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



<div class="box box-info" id="reginpatient2" style="display:none">
    <div class="box-header with-border">
        <h3 class="box-title">{{__('Pre Registration Form')}}</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form method="post" action="{{ route('save_inpatient') }}" class="form-horizontal">
        {{csrf_field()}}
        <div class="box-body">

            <div class="form-group">
                <label for="patient_id" class="col-sm-2 control-label">{{__('Registration No')}}<span style="color:red">*</span></label></label></label>
                <div class="col-sm-2">
                    <input type="hidden" name="patient_id" id="patient_id_hidden">
                    <input type="text" required readonly class="form-control" name="reg_pid" id="patient_id_display">
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">{{__('Full Name')}}<span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <input type="text" required readonly class="form-control" name="reg_pname" id="patient_name"
                        placeholder="Enter Patient Full Name">
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">{{__('NIC Number')}}<span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <input type="text" required readonly class="form-control" name="reg_pnic" id="patient_nic"
                        placeholder="National Identity Card Number">
                </div>
            </div>

            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">{{__('Address')}}<span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <input type="text" required readonly class="form-control" name="reg_paddress" id="patient_address"
                        placeholder="Enter Patient Address ">
                </div>
            </div>

            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">{{__('Telephone')}}</label>
                <div class="col-sm-10">
                    <input type="tel" readonly class="form-control" name="reg_ptel" id="patient_telephone"
                        placeholder="Patient Telephone Number">
                </div>
            </div>

            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">{{__('Occupation')}}<span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <input type="text" required readonly class="form-control" name="reg_poccupation"
                        id="patient_occupation" placeholder="Enter Patient Occupation ">
                </div>
            </div>

            <!-- select -->
            <div class="form-group">
                <label class="col-sm-2 control-label">{{__('Sex')}}<span style="color:red">*</span></label>
                <div class="col-sm-2">
                    <select required disabled class="form-control" name="reg_psex" id="patient_sex">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <label for="patient_age" class="col-sm-2 control-label">{{__('Age')}}</label>
                <div class="col-sm-2">
                    <input type="text" readonly class="form-control" name="reg_page" id="patient_age"
                        placeholder="Enter Age">
                </div>
            </div>
        </div>


        <div class="box-header with-border">
            <h3 class="box-title">{{__('Ward & Medical Information')}}</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-2 control-label">{{__('Ward')}}<span style="color:red">*</span></label>
                <div class="col-sm-4">
                    <select required class="form-control" name="ward_id">
                        <option value="">Select Ward</option>
                        @if($wards)
                        @foreach ($wards as $ward)
                                <option value="{{$ward->id}}">{{$ward->ward_no}} - {{ucwords($ward->name)}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="box-header with-border">
            <h3 class="box-title">{{__('Medical Details')}}</h3>
        </div>

        <div class="box-body">
            <div class="form-group">
                <label for="disease" class="col-sm-2 control-label">{{__('Disease/Diagnosis')}}<span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <input type="text" required class="form-control" id="disease" placeholder="Enter diagnosis of patient"
                    name="disease" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">{{__('Duration of illness')}}<span style="color:red">*</span></label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">{{__('Days:')}}</span>
                        <input type="number" required min="1" step="1" data-number-to-fixed="2" data-number-stepfactor="100"
                            class="form-control" name="duration" id="duration">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="condition" class="col-sm-2 control-label">{{__('Current Condition')}}<span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <textarea class="form-control" required name="condition" id="condition" rows="3" cols="100"
                        placeholder="Enter current condition of patient here"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="certified_officer" class="col-sm-2 control-label">{{__('Certified by')}}<span style="color:red">*</span></label>
                <div class="col-sm-4">
                    <input type="text" readonly value="{{ucWords(Auth::user()->name)}}" required class="form-control" id="certified_officer" 
                        name="certified_officer" />
                </div>
            </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
            <input type="submit" class="btn btn-info pull-right" value="Register Inpatient">
            <input type="reset" class="btn btn-default" value="Cancel">
        </div>
    </form>
</div>



<div class="row mt-5 pt-5">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="box box-info" id="reginpatient1">
            <div class="box-header with-border">
                <h3 class="box-title">{{__('Enter Registration No. Or Scan the bar code')}}</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="form-group">
                    <label for="pID" class="control-label" style="font-size:18px">{{__('Registration No or Appointment No:')}}</label>
                    <input type="number" required class="form-control" onchange="registerinpatientfunction()" id="pID"
                        placeholder="Enter Registration No" />
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-info" onclick="registerinpatientfunction()">{{__('Enter')}}</button>
                </div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
            </div>
            <!-- /.box-footer -->
        </div>
    </div>
</div>


<script>
    function registerinpatientfunction() {
        var x = document.getElementById("pID").value;
        if (x > 0) {
            var data = new FormData;
            data.append('pNum', x);
            data.append('_token', '{{csrf_token()}}');

            $.ajax({
                type: "post",
                url: "{{route('regInPatient')}}",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                error: function(data){
                    console.log(data);
                },
                success: function (response) {
                    if(response.exist){
                        console.log(response.name);
                        $("#patient_name").val(response.name);
                        $("#patient_age").val(response.age);
                        $("#patient_sex").val(response.sex);
                        $("#patient_telephone").val(response.telephone);
                        $("#patient_nic").val(response.nic);
                        $("#patient_address").val(response.address);
                        $("#patient_occupation").val(response.occupation);
                        $("#patient_id_display").val(response.reg_number); // Display registration number
                        $("#patient_id_hidden").val(response.id); // Hidden field for patient_id
                        
                        // Auto-fill certified officer if not already set
                        if(!$("#certified_officer").val()) {
                            $("#certified_officer").val("{{ucWords(Auth::user()->name)}}");
                        }

                        $("#reginpatient2").slideDown(1000);
                        $("#reginpatient1").slideUp(1000);

                    } else {
                        alert("Please Enter a Valid Admitted Patient Registration Number or Appointment Number!");
                    }
                }
            });
        } else {
            alert("Please Enter a Valid Registration Number!");
        }
    }
</script>
@endsection