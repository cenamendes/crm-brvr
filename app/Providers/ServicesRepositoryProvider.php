<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tenant\CustomerNotifications;
use App\Interfaces\Tenant\Tasks\TasksInterface;
use App\Repositories\Tenant\Tasks\TasksRepository;
use App\Interfaces\Tenant\Profile\ProfileInterface;
use App\Interfaces\Tenant\Analysis\AnalysisInterface;

use App\Interfaces\Tenant\Setup\Zones\ZonesInterface;
use App\Repositories\Tenant\Profile\ProfileRepository;
use App\Interfaces\Tenant\Customers\CustomersInterface;
use App\Repositories\Tenant\Analysis\AnalysisRepository;
use App\Repositories\Tenant\Setup\Zones\ZonesRepository;
use App\Interfaces\Tenant\TasksTimes\TasksTimesInterface;
use App\Repositories\Tenant\Customers\CustomersRepository;
use App\Interfaces\Tenant\Setup\Services\ServicesInterface;
use App\Repositories\Tenant\TasksTimes\TasksTimesRepository;
use App\Interfaces\Tenant\TasksReports\TasksReportsInterface;
use App\Repositories\Tenant\Setup\Services\ServicesRepository;
use App\Repositories\Tenant\TasksReports\TasksReportsRepository;
use App\Interfaces\Tenant\CustomerContacts\CustomerContactsInterface;
use App\Interfaces\Tenant\CustomerServices\CustomerServicesInterface;
use App\Interfaces\Tenant\CustomerLocation\CustomerLocationsInterface;
use App\Repositories\Tenant\Setup\Services\EloquentServicesRepository;
use App\Repositories\Tenant\CustomerServices\CustomerServicesRepository;
use App\Repositories\Tenant\CustomerContacts\CustomersContactsRepository;
use App\Repositories\Tenant\CustomerLocations\CustomerLocationsRepository;
use App\Interfaces\Tenant\CustomerNotification\CustomerNotificationInterface;
use App\Repositories\Tenant\CustomerNotification\CustomerNotificationRepository;

class ServicesRepositoryProvider extends ServiceProvider
{
    public array $bindings = [
        ServicesInterface::class => ServicesRepository::class,
        TasksInterface::class => TasksRepository::class,
        CustomerServicesInterface::class => CustomerServicesRepository::class,
        CustomersInterface::class => CustomersRepository::class,
        CustomerLocationsInterface::class => CustomerLocationsRepository::class,
        CustomerContactsInterface::class => CustomersContactsRepository::class,
        TasksReportsInterface::class => TasksReportsRepository::class,
        TasksTimesInterface::class => TasksTimesRepository::class,
        AnalysisInterface::class => AnalysisRepository::class,
        ProfileInterface::class => ProfileRepository::class,    
        CustomerNotificationInterface::class => CustomerNotificationRepository::class,    
    ];
}
