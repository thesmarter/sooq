<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\SystemCode;

class PsCoreService
{
    function checkPermission($checkPage = null, $userName = null, $redirectTo = null, $errorMessage = null)
    {
        goto checkGate;
        CheckPermission:
        if (!Gate::denies($checkPage, $userName)) {
            goto PermissionGranted;
        }
        goto RedirectUser;
        RedirectUser:
        return redirectView($redirectTo, $errorMessage, "danger");
        goto end;
        end: PermissionGranted:
        goto CheckForMessage;
        checkGate:
        if (!($errorMessage == null)) {
            goto CheckForCustomMessage;
        }
        goto SetDefaultMessage;
        SetDefaultMessage:
        $errorMessage = __("no_permission");
        goto JoinFlows;
        JoinFlows: CheckForCustomMessage:
        goto CheckPermission;
        CheckForMessage:
    }

    public function getSystemCode()
    {
        $sys = SystemCode::first();
        return $sys->code ?? "unknown";
    }

}
