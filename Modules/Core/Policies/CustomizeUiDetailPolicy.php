<?php

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\PsPolicy;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CustomizeUiDetail;

class CustomizeUiDetailPolicy extends PsPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $module = Constants::customizeUiDetailModule;
        $model = CustomizeUiDetail::class;
        parent::__construct($module, $model);
    }
}
