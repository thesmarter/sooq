<?php

namespace Modules\DataDeletionPolicy\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Constants\Constants;
use App\Policies\PsPolicy;
use Modules\Core\Entities\CorePrivacyPolicy;

class CoreDataDeletionPolicy extends PsPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $module = Constants::dataDeletionPolicyModule;
        $model = CorePrivacyPolicy::class;
        parent::__construct($module, $model);
    }
}
