<x-tenant-layout title="Listagem Relatórios Tarefa" :themeAction="$themeAction" :status="$status" :message="$message">
{{-- :status="$status" :message="$message"> --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Tasks Reports') }}</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('List') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @livewire('tenant.tasks-reports.show-tasks-reports')
                </div>
            </div>
        </div>
    </div>
</x-tenant-layout>
