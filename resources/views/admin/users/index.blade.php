@extends('layouts.app')

@section('title', 'User Management')

@section('breadcrumb')
<li class="breadcrumb-item text-muted">Super Admin</li>
<li class="breadcrumb-item text-gray-900">Users</li>
@endsection

@section('toolbar_actions')
<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user">
    <i class="ki-duotone ki-plus fs-2"></i> Add User
</button>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-125px">User</th>
                        <th class="min-w-125px">Role</th>
                        <th class="min-w-125px">Joined Date</th>
                        <th class="text-end min-w-100px">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($users as $user)
                    <tr>
                        <td class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                <div class="symbol-label">
                                    <img src="{{ asset('assets/vendors/media/avatars/blank.png') }}" alt="{{ $user->name }}" class="w-100" />
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 text-hover-primary mb-1">{{ $user->name }}</span>
                                <span>{{ $user->email }}</span>
                            </div>
                        </td>
                        <td>
                            @foreach($user->roles as $role)
                                <div class="badge badge-light-{{ $role->name == 'admin' ? 'danger' : 'primary' }} fw-bold">{{ ucfirst($role->name) }}</div>
                            @endforeach
                            @if($user->roles->count() == 0)
                                <div class="badge badge-light-secondary fw-bold">No Role</div>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                        <td class="text-end">
                            <button class="btn btn-icon btn-light-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_user_{{ $user->id }}">
                                <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <form action="{{ url('admin/users/' . $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-light-danger btn-sm">
                                    <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="kt_modal_edit_user_{{ $user->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <div class="modal-content">
                                <form class="form" action="{{ url('admin/users/' . $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h2 class="fw-bold">Edit User</h2>
                                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                        </div>
                                    </div>
                                    <div class="modal-body py-10 px-lg-17">
                                        <div class="fv-row mb-7">
                                            <label class="required fs-6 fw-semibold mb-2">Name</label>
                                            <input type="text" class="form-control form-control-solid" name="name" value="{{ $user->name }}" required />
                                        </div>
                                        <div class="fv-row mb-7">
                                            <label class="required fs-6 fw-semibold mb-2">Email</label>
                                            <input type="email" class="form-control form-control-solid" name="email" value="{{ $user->email }}" required />
                                        </div>
                                        <div class="fv-row mb-7">
                                            <label class="required fs-6 fw-semibold mb-2">Role</label>
                                            <select class="form-select form-select-solid" name="role" required>
                                                <option value="">Select a Role...</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="fv-row mb-7">
                                            <label class="fs-6 fw-semibold mb-2">Password</label>
                                            <input type="password" class="form-control form-control-solid" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" />
                                        </div>
                                    </div>
                                    <div class="modal-footer flex-center">
                                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                                        <button type="submit" class="btn btn-primary">
                                            <span class="indicator-label">Update</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <form class="form" action="{{ url('admin/users') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">Add User</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Name</label>
                        <input type="text" class="form-control form-control-solid" name="name" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Email</label>
                        <input type="email" class="form-control form-control-solid" name="email" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Role</label>
                        <select class="form-select form-select-solid" name="role" required>
                            <option value="">Select a Role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Password</label>
                        <input type="password" class="form-control form-control-solid" name="password" required />
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
