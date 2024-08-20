@extends('admin.layouts.admin')

@section('title', 'Administration Accounts')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <a href="{{ route('admin.accounts.managers.create') }}" class="btn btn-primary mb-2">Create <i class="fadeIn animated bx bx-chevrons-right"></i></a>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>sn</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($admins as $admin)
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>{{ $admin->user->fullName() }}</td>
                                    <td>{{ $admin->admin_user_role }}</td>
                                    <td>{{ $admin->user->email }}</td>
                                    <td>{{ $admin->user->phone }}</td>
                                    <th scope="row">
                                        <div class="col">
                                            <div class="dropdown">
                                                <span class=" dropdown-toggle text-primary" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <x-menu-icon />
                                                </span>
                                                <ul class="dropdown-menu custom-dropdown-menu" style="text-align: justify">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('admin.accounts.managers.edit', $admin) }}">
                                                            <i class="bx bx-edit me-0"></i> Edit

                                                        </a>
                                                    </li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('admin.accounts.managers.details', $admin) }}">
                                                            <i class="bx bx-coin-stack me-0"></i> View Details

                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.accounts.managers.delete', $admin) }}"
                                                            method="post" class="delete-student-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="dropdown-item bg-danger" type="submit">
                                                                <i class="bx bx-trash-alt me-0"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            @empty
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('javascript')

@endsection
