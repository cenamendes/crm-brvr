<x-tenant-layout title="{{ __('List Zones') }}" :themeAction="$themeAction" :status="$status" :message="$message">
    {{-- Content --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-9">
                <div class="page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Zones') }}</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('List') }}</a></li>
                    </ol>
                </div>
            </div>
            <div class="col-3 text-right">
                <a href="{{ route('tenant.setup.zones.create') }}" class="btn btn-primary">{{ __('Create Zone') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Zones') }}</h4>
                    </div>
                    <div class="card-body">
                        @livewire('tenant.setup.zones.show-zones')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tenant-layout>
