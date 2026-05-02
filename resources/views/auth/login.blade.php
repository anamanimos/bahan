@extends('layouts.auth')

@section('title', 'Login - Toko Kain Integrated System')

@section('content')
<!--begin::Authentication - Sign-in -->
<div class="d-flex flex-column flex-column-fluid flex-lg-row">
    <!--begin::Aside-->
    <div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
        <!--begin::Aside-->
        <div class="d-flex flex-center flex-lg-start flex-column">
            <!--begin::Logo-->
            <a href="{{ url('/') }}" class="mb-7">
                <img alt="Logo" src="{{ asset('assets/vendors/media/logos/custom-3.svg') }}" />
            </a>
            <!--end::Logo-->
            <!--begin::Title-->
            <h2 class="text-white fw-normal m-0">Integrated Inventory & POS System</h2>
            <!--end::Title-->
        </div>
        <!--begin::Aside-->
    </div>
    <!--end::Aside-->
    <!--begin::Body-->
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
        <!--begin::Card-->
        <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">
            <!--begin::Wrapper-->
            <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">
                <!--begin::Form-->
                <div class="form w-100 text-center">
                    <!--begin::Heading-->
                    <div class="text-center mb-11">
                        <!--begin::Title-->
                        <h1 class="text-gray-900 fw-bolder mb-3">Selamat Datang</h1>
                        <!--end::Title-->
                        <!--begin::Subtitle-->
                        <div class="text-gray-500 fw-semibold fs-6">Aplikasi Toko Kain & Bahan Pelengkap</div>
                        <!--end::Subtitle-->
                    </div>
                    <!--begin::Heading-->
                    
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <!--begin::Submit button-->
                    <div class="d-grid mb-10">
                        <a href="{{ url('auth/sso/redirect') }}" class="btn btn-primary">
                            <!--begin::Indicator label-->
                            <span class="indicator-label">Login via ERP SSO</span>
                            <!--end::Indicator label-->
                        </a>
                    </div>
                    <!--end::Submit button-->
                    <div class="text-gray-500 text-center fw-semibold fs-6">Autentikasi dikelola secara terpusat melalui sistem ERP.</div>
                </div>
                <!--end::Form-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Body-->
</div>
<!--end::Authentication - Sign-in-->
@endsection
