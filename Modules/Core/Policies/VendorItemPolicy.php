<?php

namespace Modules\Core\Policies;

use App\Policies\PsVendorPolicy;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Category;
use Modules\Core\Entities\Item;

class VendorModulePolicy extends PsVendorPolicy
{
    public function __construct()
    {
        $module = constants::vendorItemModule;
        $model = Item::class;
        parent::__construct($module, $model);
    }
}
