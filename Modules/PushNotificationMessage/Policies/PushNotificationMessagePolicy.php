<?php

namespace Modules\PushNotificationMessage\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\PsPolicy;
use Modules\Core\Constants\Constants;
use Modules\PushNotificationMessage\Entities\PushNotificationMessage;

class PushNotificationMessagePolicy extends PsPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $module = Constants::pushNotificationModule;
        $model = PushNotificationMessage::class;
        parent::__construct($module, $model);
    }
}
