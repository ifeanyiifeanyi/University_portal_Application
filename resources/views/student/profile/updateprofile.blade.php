<form action="{{route('student.update.profile')}}" method="POST" id="requestForm">
  @csrf
<div class="row mb-3">
  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Matric number</label>
  <div class="col-md-8 col-lg-9">
    <input type="text" class="form-control" value="{{$student->matric_number}}" readonly>
  </div>
</div>
<div class="row mb-3">
  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Jamb registration number</label>
  <div class="col-md-8 col-lg-9">
    <input type="text" class="form-control" name="jamb_registration_number" value="{{$student->jamb_registration_number}}" readonly>
  </div>
</div>
@if ($errors->has('jamb_registration_number'))
<span class="text-danger">{{$errors->first('jamb_registration_number')}}</span>
@endif
<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">First name</label>
    <input type="text" class="form-control" name="firstname" id="" value="{{$student->user->first_name}}" readonly>
    @if ($errors->has('firstname'))
    <span class="text-danger">{{$errors->first('firstname')}}</span>
    @endif
  </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Last Name</label>
    <input type="text" class="form-control" name="lastname" value="{{$student->user->last_name}}" readonly>
    @if ($errors->has('lastname'))
    <span class="text-danger">{{$errors->first('lastname')}}</span>
    @endif
  </div>
</div>
<div class="row mb-3">
  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Other names</label>
  <div class="col-md-8 col-lg-9">
    <input type="text" class="form-control" name="othernames" value="{{$student->user->other_name}}">
    @if ($errors->has('othernames'))
<span class="text-danger">{{$errors->first('othernames')}}</span>
@endif
  </div>
</div>
<div class="row mb-3">
  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Date of birth</label>
  <div class="col-md-8 col-lg-9">
    <input type="date" class="form-control" name="date_of_birth" value="{{$student->date_of_birth}}">
    @if ($errors->has('date_of_birth'))
    <span class="text-danger">{{$errors->first('date_of_birth')}}</span>
    @endif
  </div>
</div>
<div class="row mb-3">
  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Gender</label>
  <div class="col-md-8 col-lg-9">

      <select name="gender" id="" class="form-control">
          <option value="" disabled selected>Select gender</option>
          <option value="Male" @if($student->gender == 'Male') selected @endif>Male</option>
          <option value="Female" @if($student->gender == 'Female') selected @endif>Female</option>
          <option value="Other" @if($student->gender == 'Other') selected @endif>Other</option>
      </select>
      @if ($errors->has('gender'))
<span class="text-danger">{{$errors->first('gender')}}</span>
@endif
  </div>
</div>

<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Phone number</label>
      <input type="text" class="form-control" name="phonenumber" value="{{$student->user->phone}}">
      @if ($errors->has('phonenumber'))
      <span class="text-danger">{{$errors->first('phonenumber')}}</span>
      @endif
    </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Email</label>
    <input type="email" class="form-control" value="{{$student->user->email}}" readonly>
    @if ($errors->has('email'))
    <span class="text-danger">{{$errors->first('email')}}</span>
    @endif
  </div>
</div>

<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Nationality</label>
      <input type="text" class="form-control" name="nationality" value="{{$student->nationality}}">
      @if ($errors->has(''))
      <span class="text-danger">{{$errors->first('')}}</span>
      @endif
    </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">State of origin</label>
    <input type="text" class="form-control" name="state_of_origin" value="{{$student->state_of_origin}}">
    @if ($errors->has('state_of_origin'))
<span class="text-danger">{{$errors->first('state_of_origin')}}</span>
@endif
  </div>
</div>

<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Local government/province</label>
      <input type="text" class="form-control" name="local_govt_of_origin" value="{{$student->lga_of_origin}}">
      @if ($errors->has('local_govt_of_origin'))
<span class="text-danger">{{$errors->first('local_govt_of_origin')}}</span>
@endif

    </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Home town</label>
    <input type="text" class="form-control" name="hometown" value="{{$student->hometown}}">
    @if ($errors->has(''))
<span class="text-danger">{{$errors->first('')}}</span>
@endif
  </div>
</div>


<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Residential address</label>
      <input type="text" class="form-control" name="residential_address" value="{{$student->residential_address}}">
      @if ($errors->has('residential_address'))
      <span class="text-danger">{{$errors->first('residential_address')}}</span>
      @endif
    </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Permanent address</label>
    <input type="text" class="form-control" name="permanent_address" value="{{$student->permanent_address}}">
    @if ($errors->has('permanent_address'))
<span class="text-danger">{{$errors->first('permanent_address')}}</span>
@endif
  </div>
</div>

<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Marital status</label>
      <input type="text" class="form-control" name="marital_status" value="{{$student->marital_status}}">
      @if ($errors->has('marital_status'))
      <span class="text-danger">{{$errors->first('marital_status')}}</span>
      @endif
    </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Religion</label>
    <input type="text" class="form-control" name="religion" value="{{$student->religion}}">
    @if ($errors->has('religion'))
<span class="text-danger">{{$errors->first('religion')}}</span>
@endif
  </div>
</div>
<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Blood group</label>
      <input type="text" class="form-control" name="bloodgroup" value="{{$student->blood_group}}">
      @if ($errors->has('bloodgroup'))
      <span class="text-danger">{{$errors->first('bloodgroup')}}</span>
      @endif
    </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Genotype</label>
    <input type="text" class="form-control" name="genotype" value="{{$student->genotype}}">
    @if ($errors->has('genotype'))
<span class="text-danger">{{$errors->first('genotype')}}</span>
@endif
  </div>
</div>
<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Next kins name</label>
      <input type="text" class="form-control" name="next_of_kin_name" value="{{$student->next_of_kin_name}}">
      @if ($errors->has('next_of_kin_name'))
      <span class="text-danger">{{$errors->first('next_of_kin_name')}}</span>
      @endif
    </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Next kins relationship</label>
    <input type="text" class="form-control" name="next_of_kin_relationship" value="{{$student->next_of_kin_relationship}}">
    @if ($errors->has('next_of_kin_relationship'))
<span class="text-danger">{{$errors->first('next_of_kin_relationship')}}</span>
@endif
  </div>
</div>
<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Kins phone</label>
      <input type="text" class="form-control" name="next_of_kin_phone" value="{{$student->next_of_kin_phone}}">
      @if ($errors->has('next_of_kin_phone'))
      <span class="text-danger">{{$errors->first('next_of_kin_phone')}}</span>
      @endif
    </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Kins address</label>
    <input type="text" class="form-control" name="next_of_kin_address" value="{{$student->next_of_kin_address}}">
    @if ($errors->has('next_of_kin_address'))
<span class="text-danger">{{$errors->first('next_of_kin_address')}}</span>
@endif
  </div>
</div>
<div class="row mb-3">

  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Year of admission</label>
      <input type="text" class="form-control" name="year_of_admission" value="{{$student->year_of_admission}}">
      @if ($errors->has('year_of_admission'))
      <span class="text-danger">{{$errors->first('year_of_admission')}}</span>
      @endif
    </div>
  <div class="col-md-6">
      <label for="fullName" class="col-form-label">Mode of entry</label>

    <input type="text" class="form-control" name="mode_of_entry" value="{{$student->mode_of_entry}}">
    @if ($errors->has('mode_of_entry'))
<span class="text-danger">{{$errors->first('mode_of_entry')}}</span>
@endif
  </div>
</div>

<div class="row mb-3">
  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Current level</label>
  <div class="col-md-8 col-lg-9">
    <select id="level" name="current_level" class="form-control">
      <option value="" disabled selected>Select level</option>


  </select>
    {{-- <input type="text" class="form-control" name="current_level" value="{{$student->current_level}}"> --}}
  </div>
</div>

<div class="text-center">
  <button type="submit" class="btn w-100" style="background: #0d382e;color:white">Update changes</button>
</div>
</form>
<input type="hidden" name="" id="department_id" value="{{ $student->department_id }}">
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const departmentSelect = document.getElementById('department_id');
      const levelSelect = document.getElementById('level');

      function updateLevels() {
          const departmentId = departmentSelect.value;
          fetch(`/student/fees/departments/${departmentId}/levels`)
              .then(response => response.json())
              .then(levels => {
                console.log(levels);
                  levelSelect.innerHTML = '';
                  levels.forEach(level => {
                      const option = document.createElement('option');
                      option.value = level;
                      option.textContent = level;
                      levelSelect.appendChild(option);
                  });
              });
      }

      departmentSelect.addEventListener('change', updateLevels);
      updateLevels(); // Initial population
  });
</script>
