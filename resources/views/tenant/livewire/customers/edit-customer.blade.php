
<x-tenant.customers.form
    :action="route('tenant.customers.update',$customer->id)"
    :districts="$districts"
    :counties="$counties"
    :idCustomer="$customer->id"
    :name="$customer->name"
    :shortName="$customer->short_name"
    :vat="$customer->vat"
    :email="$customer->email"
    :contact="$customer->contact"
    :address="$customer->address"
    :zipcode="$customer->zipcode"
    :district="$customer->district"
    :county="$customer->county"
    :accountmanager="$account_manager"
    :allAccountManagers="$allAccountManagers"
    :update="true" buttonAction="{{ __('Update Customer') }}"
    formTitle="{{ __('Update Customer') }}"/>
