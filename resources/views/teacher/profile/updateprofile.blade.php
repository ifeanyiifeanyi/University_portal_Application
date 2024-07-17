<form action="{{route('teacher.update.profile')}}" method="POST" id="requestForm">
    @csrf
<input type="hidden" value="{{$profile->user_id}}" name="user_id">

  <div class="row mb-3">
    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
    <div class="col-md-8 col-lg-9">
      <input name="full_name" type="text" class="form-control" id="fullName" value="{{$getuser->name}}" readonly>
    </div>
  </div>
  <div class="row mb-3">
    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email</label>
    <div class="col-md-8 col-lg-9">
      <input name="full_name" type="text" class="form-control" id="fullName" value="{{$getuser->email}}" readonly>
    </div>
  </div>
  <div class="row mb-3">
    <label for="Country" class="col-md-4 col-lg-3 col-form-label">Date of birth</label>
    <div class="col-md-8 col-lg-9">
      <input name="date_of_birth" type="date" class="form-control" value="{{$profile->date_of_birth}}">
      @if ($errors->has('date_of_birth'))
          <span class="text-danger">{{$errors->first('date_of_birth')}}</span>
      @endif
    </div>
  </div>
  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Gender</label>
    <div class="col-md-8 col-lg-9">
        <select name="gender" class="form-control">
            <option value="" selected disabled>Select gender</option>
            <option value="male" @if($profile->gender == 'male') selected @endif>Male</option>
            <option value="female" @if($profile->gender == 'female') selected @endif>Female</option>
        </select>
        @if ($errors->has('gender'))
        <span class="text-danger">{{$errors->first('gender')}}</span>
    @endif
    </div>
  </div>
  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Teaching experience in years</label>
    <div class="col-md-8 col-lg-9">
      <input name="teaching_experience" type="number" class="form-control" value="{{$profile->teaching_experience}}">
      @if ($errors->has('teaching_experience'))
      <span class="text-danger">{{$errors->first('teaching_experience')}}</span>
  @endif
    </div>
  </div>
  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Teacher type</label>
    <div class="col-md-8 col-lg-9">
      <input name="teacher_type" type="text" class="form-control" value="{{$profile->teacher_type}}">
      @if ($errors->has('teacher_type'))
      <span class="text-danger">{{$errors->first('teacher_type')}}</span>
  @endif
    </div>
  </div>
  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Teacher Qualification</label>
    <div class="col-md-8 col-lg-9">
        <select name="teacher_qualification" class="form-control">
            <option value="" selected disabled>Select qualification</option>
            <option value="Bsc" @if($profile->teacher_qualification == 'Bsc') selected @endif>Bsc</option>
            <option value="Msc" @if($profile->teacher_qualification == 'Msc') selected @endif>Msc</option>
            <option value="Hnd" @if($profile->teacher_qualification == 'Hnd') selected @endif>Hnd</option>
            <option value="Phd" @if($profile->teacher_qualification == 'Phd') selected @endif>Phd</option>
        </select>
        @if ($errors->has('teacher_qualification'))
        <span class="text-danger">{{$errors->first('teacher_qualification')}}</span>
        @endif
    </div>
  </div>
  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Title</label>
    <div class="col-md-8 col-lg-9">
        <select name="teacher_title" class="form-control">
            <option value="" selected disabled>Select title</option>
            <option value="Mr" @if($profile->teacher_title == 'Mr') selected @endif>Mr</option>
            <option value="Mrs" @if($profile->teacher_title == 'Mrs') selected @endif>Mrs</option>
            <option value="Doctor" @if($profile->teacher_title == 'Doctor') selected @endif>Doctor</option>
            <option value="Professor" @if($profile->teacher_title == 'Professor') selected @endif>Professor</option>
        </select>
        @if ($errors->has('teacher_title'))
        <span class="text-danger">{{$errors->first('teacher_title')}}</span>
        @endif
    </div>
  </div>
  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Date of employment</label>
    <div class="col-md-8 col-lg-9">
      <input name="date_of_employment" type="date" class="form-control" value="{{$profile->date_of_employment}}">
      @if ($errors->has('date_of_employment'))
      <span class="text-danger">{{$errors->first('date_of_employment')}}</span>
      @endif
    </div>
  </div>
  <div class="row mb-3">
    <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
    <div class="col-md-8 col-lg-9">
      <input name="address" type="text" class="form-control" id="Address" value="{{$profile->address}}">
      @if ($errors->has('address'))
      <span class="text-danger">{{$errors->first('address')}}</span>
      @endif
    </div>
  </div>
  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Nationality</label>
    <div class="col-md-8 col-lg-9">
        <select name="nationality" class="form-control">
            <option value="" selected disabled>Select nationality</option>
            <option value="Nigeria" @if($profile->nationality == 'Nigeria') selected @endif>Nigeria</option>
            <option value="Ghana" @if($profile->nationality == 'Ghana') selected @endif>Ghana</option>
        </select>
        @if ($errors->has('nationality'))
      <span class="text-danger">{{$errors->first('nationality')}}</span>
      @endif
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Level</label>
    <div class="col-md-8 col-lg-9">
        <select name="teacher_level" class="form-control">
            <option value="" selected disabled>Select level</option>
            <option value="Faculty_officer" @if($profile->level == 'Faculty_officer') selected @endif>Faculty officer</option>
            <option value="Hod" @if($profile->level == 'Hod') selected @endif>Hod</option>
           
        </select>
        @if ($errors->has('teacher_level'))
        <span class="text-danger">{{$errors->first('teacher_level')}}</span>
        @endif
    </div>
  </div>
  <div class="text-center">
    <button type="submit" class="btn btn-warning w-100">Update profile</button>
  </div>
</form>