@extends('parent.layouts.parent')

@section('title', 'Childrens')
@section('parent')

<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Childrens</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Childrens</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All childrens</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th scope="col">Child name</th>
                                    <th scope="col">Matric number</th>
                                    <th scope="col">Parent type</th>
                                    <th scope="col"></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($childrens as $children)
                                <tr>
                                    <td>{{$children->student->user->first_name}} {{$children->student->user->last_name}} {{$children->student->user->other_name}}</td>
                                    <td>{{$children->student->matric_number}}</td>
                                    <td>{{$children->parent_type}}</td>
                                    
                                    <td>
                                        <a href="{{route('parent.view.child',['id'=>$children->student->id])}}" class="btn w-50 text-white" style="background: #AE152D;">View</a>
                                    </td>
                                    </tr>
                                @empty
                                    
                                @endforelse
                               
                              
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection