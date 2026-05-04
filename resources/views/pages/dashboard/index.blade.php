@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item text-gray-900">Dashboard</li>
@endsection

@section('content')
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!--begin::Col-->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
        <!--begin::Card widget 20-->
        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-100 mb-5 mb-xl-10" style="background-color: #F1416C;background-image:url('{{ asset('assets/vendors/media/patterns/vector-1.png') }}')">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <!--begin::Title-->
                <div class="card-title d-flex flex-column">
                    <!--begin::Amount-->
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $stats['total_products'] }}</span>
                    <!--end::Amount-->
                    <!--begin::Subtitle-->
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Produk</span>
                    <!--end::Subtitle-->
                </div>
                <!--end::Title-->
            </div>
            <!--end::Header-->
            <!--begin::Card body-->
            <div class="card-body d-flex align-items-end pt-0">
                <!--begin::Progress-->
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                        <span>Lihat Detail</span>
                        <span>100%</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                        <div class="bg-white rounded h-8px" role="progressbar" style="width: 100%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <!--end::Progress-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card widget 20-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
        <!--begin::Card widget 7-->
        <div class="card card-flush h-md-100 mb-5 mb-xl-10">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <!--begin::Title-->
                <div class="card-title d-flex flex-column">
                    <!--begin::Amount-->
                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_suppliers'] }}</span>
                    <!--end::Amount-->
                    <!--begin::Subtitle-->
                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Supplier</span>
                    <!--end::Subtitle-->
                </div>
                <!--end::Title-->
            </div>
            <!--end::Header-->
            <!--begin::Card body-->
            <div class="card-body d-flex flex-column justify-content-end pe-0">
                <!--begin::Title-->
                <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Supplier Terdaftar</span>
                <!--end::Title-->
                <!--begin::Users group-->
                <div class="symbol-group symbol-hover flex-nowrap">
                    @foreach($recent_receipts->pluck('supplier')->unique()->take(5) as $supplier)
                    <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="{{ $supplier->name }}">
                        <span class="symbol-label bg-primary text-inverse-primary fw-bold">{{ substr($supplier->name, 0, 1) }}</span>
                    </div>
                    @endforeach
                </div>
                <!--end::Users group-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card widget 7-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
        <!--begin::Card widget 17-->
        <div class="card card-flush h-md-100 mb-5 mb-xl-10">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <!--begin::Title-->
                <div class="card-title d-flex flex-column">
                    <!--begin::Amount-->
                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_receipts_month'] }}</span>
                    <!--end::Amount-->
                    <!--begin::Subtitle-->
                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Nota Masuk Bulan Ini</span>
                    <!--end::Subtitle-->
                </div>
                <!--end::Title-->
            </div>
            <!--end::Header-->
            <!--begin::Card body-->
            <div class="card-body pt-2 pb-4 d-flex align-items-center">
                <!--begin::Chart-->
                <div class="d-flex flex-center me-5 pt-2">
                    <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px" data-kt-size="70" data-kt-line="11"></div>
                </div>
                <!--end::Chart-->
                <!--begin::Labels-->
                <div class="d-flex flex-column content-justify-center w-100">
                    <!--begin::Label-->
                    <div class="d-flex fs-6 fw-semibold align-items-center">
                        <!--begin::Bullet-->
                        <div class="bullet w-8px h-6px rounded-2 bg-danger me-3"></div>
                        <!--end::Bullet-->
                        <!--begin::Label-->
                        <div class="text-gray-500 flex-grow-1 me-4">Baru</div>
                        <!--end::Label-->
                        <!--begin::Stats-->
                        <div class="fw-bolder text-gray-700 text-xxl-end">{{ $stats['total_receipts_month'] }}</div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Label-->
                </div>
                <!--end::Labels-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card widget 17-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
        <!--begin::List widget 26-->
        <div class="card card-flush h-lg-100">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Pengajuan Beli</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Menunggu Persetujuan</span>
                </h3>
                <!--end::Title-->
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-5">
                <!--begin::Item-->
                <div class="d-flex flex-stack">
                    <!--begin::Section-->
                    <div class="text-gray-700 fw-semibold fs-6 me-2">Pending</div>
                    <!--end::Section-->
                    <!--begin::Statistics-->
                    <div class="d-flex align-items-senter">
                        <span class="text-gray-900 fw-bolder fs-6">{{ $stats['pending_pr'] }}</span>
                        <span class="badge badge-light-warning fs-base ms-2">
                        <i class="ki-duotone ki-information fs-5 text-warning ms-n1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></span>
                    </div>
                    <!--end::Statistics-->
                </div>
                <!--end::Item-->
                <div class="separator separator-dashed my-3"></div>
                <div class="text-center">
                    <a href="{{ route('inventory.purchase-requisition.index') }}" class="btn btn-sm btn-light-primary w-100 mt-2">Lihat Semua PR</a>
                </div>
            </div>
            <!--end::Body-->
        </div>
        <!--end::List widget 26-->
    </div>
    <!--end::Col-->
</div>

<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!--begin::Col-->
    <div class="col-xl-8">
        <!--begin::Chart widget 36-->
        <div class="card card-flush h-lg-100">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Tren Penerimaan Barang</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Jumlah nota masuk dalam 7 hari terakhir</span>
                </h3>
                <!--end::Title-->
            </div>
            <!--end::Header-->
            <!--begin::Card body-->
            <div class="card-body pt-0 ps-4 pe-4">
                <div id="kt_charts_widget_36" style="height: 350px;"></div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Chart widget 36-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xl-4">
        <!--begin::List widget 24-->
        <div class="card card-flush h-lg-100">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Penerimaan Terbaru</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">5 Transaksi Terakhir</span>
                </h3>
                <!--end::Title-->
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-6">
                @foreach($recent_receipts as $receipt)
                <!--begin::Item-->
                <div class="d-flex flex-stack">
                    <!--begin::Symbol-->
                    <div class="symbol symbol-40px me-4">
                        <div class="symbol-label fs-2 fw-semibold bg-light-primary text-primary">{{ substr($receipt->supplier->name ?? '?', 0, 1) }}</div>
                    </div>
                    <!--end::Symbol-->
                    <!--begin::Section-->
                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                        <!--begin::Content-->
                        <div class="flex-grow-1 me-2">
                            <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">{{ $receipt->supplier->name ?? 'Unknown' }}</a>
                            <span class="text-muted fw-semibold d-block fs-7">No: {{ $receipt->identifier }}</span>
                        </div>
                        <!--end::Content-->
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-center">
                            <span class="text-gray-800 fw-bold fs-6">{{ $receipt->created_at->format('d M Y') }}</span>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Section-->
                </div>
                <!--end::Item-->
                @if(!$loop->last)
                <div class="separator separator-dashed my-4"></div>
                @endif
                @endforeach
                
                @if($recent_receipts->isEmpty())
                <div class="text-center py-10">
                    <span class="text-muted">Belum ada data penerimaan barang.</span>
                </div>
                @endif
            </div>
            <!--end::Body-->
        </div>
        <!--end::List widget 24-->
    </div>
    <!--end::Col-->
</div>
@endsection

@push('scripts')
<script>
    var kt_charts_widget_36_data = @json($trend_data);
    
    var initChart = function() {
        var element = document.getElementById("kt_charts_widget_36");

        if (!element) {
            return;
        }

        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--bs-gray-200');
        var baseColor = KTUtil.getCssVariableValue('--bs-primary');
        var lightColor = KTUtil.getCssVariableValue('--bs-primary-light');

        var options = {
            series: [{
                name: 'Nota Masuk',
                data: kt_charts_widget_36_data.values
            }],
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {},
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.7,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [baseColor]
            },
            xaxis: {
                categories: kt_charts_widget_36_data.labels,
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: baseColor,
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                style: {
                    fontSize: '12px'
                },
                y: {
                    formatter: function(val) {
                        return val + ' Nota'
                    }
                }
            },
            colors: [baseColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            markers: {
                strokeColor: baseColor,
                strokeWidth: 3
            }
        };

        var chart = new ApexCharts(element, options);
        chart.render();
    }

    KTUtil.onDOMContentLoaded(function() {
        initChart();
    });
</script>
@endpush
