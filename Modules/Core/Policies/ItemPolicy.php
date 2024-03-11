<?php

namespace Modules\Core\Policies;

use Modules\Core\Entities\Item;
use App\Policies\PsPolicy;
use Modules\Core\Constants\Constants;

class ItemPolicy extends PsPolicy
{
    public function __construct()
    {
        $module = Constants::itemModule;
        $model = Item::class;
        parent::__construct($module, $model);
    }
}
