<?php

namespace App\Interfaces\Tenant\Tasks;

use Livewire\Request;
use App\Models\Tenant\Tasks;
use App\Models\Tenant\SerieNumbers;
use App\Models\Tenant\TasksReports;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\Tenant\Tasks\TasksFormRequest;

interface TasksInterface
{
    public function add(TasksFormRequest $request): Tasks;

    public function dispatchTask(Tasks $task): TasksReports;

    public function getTasks($perPage);

    public function getTaskSearch($searchString,$perPage): LengthAwarePaginator;

    public function getTask($task): Tasks;

    public function getTaskById($taskId): Tasks;

    public function updateTask(Tasks $task, object $values): bool;

    public function createTask(object $values): Tasks;

    public function taskCalendar(): Collection;

    public function taskCalendarMonthChange($month,$year): Collection;

    /**FILTRO */

    public function getTasksFilter(string $searchString,int $tech,int $client,int $typeReport,int $work,string $ordenation,string $dateBegin,string $dateEnd,$perPage): LengthAwarePaginator;

    /**FIM FILTRO */

    /** Search Serie Number */

    public function searchSerialNumber($serialNumber): LengthAwarePaginator;

    /********* */

}
