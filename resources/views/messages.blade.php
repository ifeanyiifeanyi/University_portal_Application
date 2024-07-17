@if(count($errors) > 0)
@foreach($errors->all() as $error)
{{-- <div class="alert alert-danger text-white">{{$error}}</div> --}}

<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{$error}}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endforeach
@endif
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show alert-success-bg" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
{{-- <div class="alert alert-success text-white" style="background: green;">

{{ session('success') }}

</div> --}}


@endif
@if (session('error'))
{{-- <div class="alert alert-danger text-white" style="background: red;">
{{ session('error') }}
</div> --}}
<div class="alert alert-danger alert-dismissible fade show alert-fail-bg" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif