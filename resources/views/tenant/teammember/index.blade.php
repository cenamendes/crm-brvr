<x-tenant-layout title="{{ __('List Team Members') }}" :themeAction="$themeAction" :status="$status" :message="$message">
    {{-- Content --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-9">
                <div class="page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Team Members') }}</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('List') }}</a></li>
                    </ol>
                </div>
            </div>
            <div class="col-3 text-right">
                <a href="{{ route('tenant.team-member.create') }}" class="btn btn-primary">{{ __('Create Team Member') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Team Members') }}</h4>
                    </div>
                    <div class="card-body">
                        @livewire('tenant.teammembers.show-teammembers')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tenant-layout>
