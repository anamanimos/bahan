@extends('layouts.app')

@section('title', config('l5-swagger.documentations.'.$documentation.'.api.title'))

@section('toolbar_actions')
<a href="{{ url('admin/api/token') }}" class="btn btn-sm btn-primary">
    <i class="ki-duotone ki-key fs-2"><span class="path1"></span><span class="path2"></span></i>
    Manage API Tokens
</a>
@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}">
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}" sizes="16x16"/>
    <style>
    /* Hide Swagger topbar as we have our own layout header */
    .swagger-ui .topbar { display: none; }
    </style>
    
    <style>
        /* Adapt Swagger Dark Mode to Metronic Theme */
        [data-bs-theme="dark"] .scheme-container {
            background: #1e1e2d;
        }
        [data-bs-theme="dark"] .scheme-container,
        [data-bs-theme="dark"] .opblock .opblock-section-header{
            box-shadow: 0 1px 2px 0 rgba(255, 255, 255, 0.15);
        }
        [data-bs-theme="dark"] .operation-filter-input,
        [data-bs-theme="dark"] .dialog-ux .modal-ux,
        [data-bs-theme="dark"] input[type=email],
        [data-bs-theme="dark"] input[type=file],
        [data-bs-theme="dark"] input[type=password],
        [data-bs-theme="dark"] input[type=search],
        [data-bs-theme="dark"] input[type=text],
        [data-bs-theme="dark"] textarea{
            background: #1b1b29;
            color: #e7e7e7;
        }
        [data-bs-theme="dark"] .title,
        [data-bs-theme="dark"] li,
        [data-bs-theme="dark"] p,
        [data-bs-theme="dark"] table,
        [data-bs-theme="dark"] label,
        [data-bs-theme="dark"] .opblock-tag,
        [data-bs-theme="dark"] .opblock .opblock-summary-operation-id,
        [data-bs-theme="dark"] .opblock .opblock-summary-path,
        [data-bs-theme="dark"] .opblock .opblock-summary-path__deprecated,
        [data-bs-theme="dark"] h1,
        [data-bs-theme="dark"] h2,
        [data-bs-theme="dark"] h3,
        [data-bs-theme="dark"] h4,
        [data-bs-theme="dark"] h5,
        [data-bs-theme="dark"] .btn,
        [data-bs-theme="dark"] .tab li,
        [data-bs-theme="dark"] .parameter__name,
        [data-bs-theme="dark"] .parameter__type,
        [data-bs-theme="dark"] .prop-format,
        [data-bs-theme="dark"] .loading-container .loading:after{
            color: #e7e7e7;
        }
        [data-bs-theme="dark"] .opblock-description-wrapper p,
        [data-bs-theme="dark"] .opblock-external-docs-wrapper p,
        [data-bs-theme="dark"] .opblock-title_normal p,
        [data-bs-theme="dark"] .response-col_status,
        [data-bs-theme="dark"] table thead tr td,
        [data-bs-theme="dark"] table thead tr th,
        [data-bs-theme="dark"] .response-col_links,
        [data-bs-theme="dark"] .swagger-ui{
            color: #d1d5db;
        }
        [data-bs-theme="dark"] .parameter__extension,
        [data-bs-theme="dark"] .parameter__in,
        [data-bs-theme="dark"] .model-title{
            color: #949494;
        }
        [data-bs-theme="dark"] table thead tr td,
        [data-bs-theme="dark"] table thead tr th{
            border-color: rgba(120,120,120,.2);
        }
        [data-bs-theme="dark"] .opblock .opblock-section-header{
            background: transparent;
        }
        [data-bs-theme="dark"] .opblock.opblock-post{
            background: rgba(73,204,144,.1);
        }
        [data-bs-theme="dark"] .opblock.opblock-get{
            background: rgba(97,175,254,.1);
        }
        [data-bs-theme="dark"] .opblock.opblock-put{
            background: rgba(252,161,48,.1);
        }
        [data-bs-theme="dark"] .opblock.opblock-delete{
            background: rgba(249,62,62,.1);
        }
        [data-bs-theme="dark"] .loading-container .loading:before{
            border-color: rgba(255,255,255,10%);
            border-top-color: rgba(255,255,255,.6);
        }
        [data-bs-theme="dark"] svg:not(:root){
            fill: #e7e7e7;
        }
        [data-bs-theme="dark"] .opblock-summary-description {
            color: #fafafa;
        }
        
        /* Layout fixes to integrate with card nicely */
        .swagger-ui .wrapper { padding: 0; max-width: 100%; }
        .swagger-ui .info { display: none !important; }
    </style>
@endpush

@section('content')
<div id="swagger-ui"></div>
@endsection

@push('scripts')
<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script>
<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script>
<script>
    window.onload = function() {
        // Build a system
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',
            url: "{!! $urlToDocs !!}",
            operationsSorter: {!! isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null' !!},
            configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
            validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
            oauth2RedirectUrl: "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

            requestInterceptor: function(request) {
                request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                return request;
            },

            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],

            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],

            layout: "StandaloneLayout",
            docExpansion : "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
            deepLinking: true,
            filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
            persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",

        })

        window.ui = ui

        @if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
        ui.initOAuth({
            usePkceWithAuthorizationCodeGrant: "{!! (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
        })
        @endif
    }
</script>
@endpush
