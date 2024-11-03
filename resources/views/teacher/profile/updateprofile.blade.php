@include('messages')
<form action="{{route('teacher.update.profile')}}" method="POST" id="requestForm">
    @csrf
<input type="hidden" value="{{$profile->user_id}}" name="user_id">

  <div class="row mb-3">
    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">First name</label>
    <div class="col-md-8 col-lg-9">
      <input name="" type="text" class="form-control" value="{{$getuser->first_name}}" readonly>
    </div>
  </div>
  <div class="row mb-3">
    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Last name</label>
    <div class="col-md-8 col-lg-9">
      <input name="" type="text" class="form-control" value="{{$getuser->last_name}}" readonly>
    </div>
  </div>
  <div class="row mb-3">
    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Other name</label>
    <div class="col-md-8 col-lg-9">
      <input name="" type="text" class="form-control" value="{{$getuser->other_name}}" readonly>
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


{{-- not done --}}

  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Office hours</label>
    <div class="col-md-8 col-lg-9">
      
        <input name="office_hours" type="text" class="form-control" value="{{$profile->office_hours}}">
        @if ($errors->has('office_hours'))
        <span class="text-danger">{{$errors->first('office_hours')}}</span>
        @endif
    </div>
  </div>

<div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Office address</label>
    <div class="col-md-8 col-lg-9">
      
        <input name="office_address" type="text" class="form-control" value="{{$profile->office_address}}">
        @if ($errors->has('office_address'))
        <span class="text-danger">{{$errors->first('office_address')}}</span>
        @endif
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Biography</label>
    <div class="col-md-8 col-lg-9">
      <textarea name="biography" cols="20" class="form-control" rows="10">{{$profile->biography}}</textarea>
        @if ($errors->has('biography'))
        <span class="text-danger">{{$errors->first('biography')}}</span>
        @endif
    </div>
  </div>
  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Certificates</label>
    <div class="col-md-8 col-lg-9">
      
         <table class="table" id="displaycertificates">
                <thead>
                  <tr>
                    
                    <th>Certificate name</th>
                    
                    <th>Actions</th>                   
                  </tr>
                </thead>
                @if (is_array(json_decode($profile->certifications)) && count(json_decode($profile->certifications)) > 0)
                @foreach (json_decode($profile->certifications) as $index => $certification)
                   <tr>
                
                    <td>
                    
                               <div class="form-group">
                                   
                                   <input type="text" class="form-control" name="certifications[]" value="{{$certification}}">
                                 
                               </div>
                             </td>
                           
                               <td>
                                   <button type="button" id="cancelcert" class="btn btn-danger">X</button>
                               </td>
                           </tr>
                @endforeach
            @else
            <tr>
                
              <td>
              
                         <div class="form-group">
                             
                             <input type="text" class="form-control" name="certifications[]">
                           
                         </div>
                       </td>
                     
                         <td>
                             <button type="button" id="cancelcert" class="btn btn-danger">X</button>
                         </td>
                     </tr>
            @endif
               
                    
           
     
              </table>
     <button class="btn btn-primary mt-3 w-50" type="button" id="addcert">Add certificate</button>
    </div>
  </div>


  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Publications</label>
    <div class="col-md-8 col-lg-9">
      
         <table class="table" id="displaypublications">
                <thead>
                  <tr>
                    
                    <th>Publication name</th>
                                    
                  </tr>
                </thead>
              
                @forelse(json_decode($profile->publications ?? '[]') as $publication)
                <tr>
               
                  <td>
                             <div class="form-group">
                                 
                                 <input type="text" class="form-control" name="publications[]" value="{{ $publication }}">
                               
                             </div>
                             
                           </td>
                           <td>
                            <button type="button" id="cancelpub" class="btn btn-danger">X</button>
                        </td>
                </tr>
                              
                            @empty
                            <tr>
               
                              <td>
                                         <div class="form-group">
                                             
                                             <input type="text" class="form-control" name="publications[]">
                                           
                                         </div>
                                         
                                       </td>
                                       <td>
                                        <button type="button" id="cancelpub" class="btn btn-danger">X</button>
                                    </td>
                            </tr>
                            @endforelse

                {{-- @if (is_array(json_decode($profile->publications)) && count(json_decode($profile->publications)) > 0)
                @foreach (json_decode($profile->publications) as $index => $publication)
                    @php
                        $parts = explode(' - ', $publication);
                        $title = $parts[0] ?? '';
                        $year = $parts[1] ?? '';
                       
                        
                    @endphp
                   

                    <tr>
               
                      <td>
                                 <div class="form-group">
                                     
                                     <input type="text" class="form-control" name="publication_name[]" value="{{ $title }}">
                                   
                                 </div>
                                 
                               </td>
                               <td>
                                <div class="form-group">
                                     
                                  <input type="text" class="form-control" name="publication_year[]" value="{{ $year }}">
                                
                              </div>
                               </td>
                             
                                 <td>
                                     <button type="button" id="cancelpub" class="btn btn-danger">X</button>
                                 </td>
                             </tr>
                @endforeach
            @else
            <tr>
               
              <td>
                         <div class="form-group">
                             
                             <input type="text" class="form-control" name="publications[]">
                           
                         </div>
                         
                       </td>
                       <td>
                        <div class="form-group">
                             
                          <input type="text" class="form-control" name="publications[]">
                        
                      </div>
                       </td>
                     
                         <td>
                             <button type="button" id="cancelpub" class="btn btn-danger">X</button>
                         </td>
                     </tr>
            @endif --}}
                    
          
     
              </table>
     <button class="btn btn-primary mt-3 w-50" type="button" id="addpub">Add publication</button>
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-md-4 col-lg-3 col-form-label">Number of awards</label>
    <div class="col-md-8 col-lg-9">
      
        <input name="number_of_awards" type="number" class="form-control" value="{{$profile->number_of_awards}}">
        @if ($errors->has('number_of_awards'))
        <span class="text-danger">{{$errors->first('number_of_awards')}}</span>
        @endif
    </div>
  </div>
  {{-- not done --}}



  <div class="text-center">
    <button type="submit" class="btn btn-warning w-100">Update profile</button>
  </div>
</form>