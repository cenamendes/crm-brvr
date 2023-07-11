<x-tenant-layout title="Editar Tarefa" :themeAction="$themeAction">
    <div class="container-fluid">
        <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Report Task') }}</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Update') }}</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $task->reference }}</a></li>
            </ol>
        </div>
        <div class="default-tab">
            @livewire('tenant.tasks-reports.edit-tasks-reports', ['taskReportToUpdate' => $taskReportToUpdate, 'task' => $task])
        </div>
    </div>
</x-tenant-layout>
