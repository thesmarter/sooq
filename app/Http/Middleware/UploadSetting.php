<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Entities\BackendSetting;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\UserInfo;

class UploadSetting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $upload_setting = BackendSetting::first()->upload_setting;


        if (Auth::check()){
            // dd(Auth::user());
            $bluemark_conds['core_keys_id'] = Constants::usrIsVerifyBlueMark;
            $bluemark_conds['user_id'] = Auth::user()->id;
            // dd($bluemark_conds);
            $is_verify_blue_mark= '';
            $blueMarkUser = UserInfo::where($bluemark_conds)->first();
            // dd($blueMarkUser);
            if($blueMarkUser){
                $is_verify_blue_mark = $blueMarkUser->value;
                // dd($is_verify_blue_mark);

            }
            if($upload_setting == 'admin-bluemark'){
                if(Auth::user()->role_id == 1  || $is_verify_blue_mark == 1){

                    return $next($request);
                }
                else{
                    return redirect()->route("dashboard");
                }
            }
            if($upload_setting == 'admin'){
                if(Auth::user()->role_id == 1){
                    return $next($request);
                }
                else{
                    return redirect()->route("dashboard");
                }
            }
            else{
                return $next($request);
            }


        }
        else{
            return $next($request);
        }


    }
}
