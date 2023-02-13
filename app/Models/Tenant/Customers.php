<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mpyw\ComposhipsEagerLimit\ComposhipsEagerLimit;

class Customers extends Model
{
    use HasFactory;
    use ComposhipsEagerLimit;

    protected $fillable = ['name', 'short_name', 'vat', 'contact', 'address', 'email', 'district', 'county', 'zipcode', 'zone','account_manager'];

    protected static function booted()
    {
        self::addGlobalScope('ordered', function (Builder $queryBuilder) {
            $queryBuilder->orderBy('name');
        });
    }

    public function customerCounty()
    {
        return $this->hasOne(Counties::class, ['id', 'district_id'], ['county', 'district']);
    }

    public function customerDistrict()
    {
        return $this->hasOne(Districts::class, 'id', 'district');
    }

}
