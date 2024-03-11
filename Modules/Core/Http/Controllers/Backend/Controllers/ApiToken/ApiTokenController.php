<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\ApiToken;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Laravel\Jetstream\Jetstream;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Project;
use Illuminate\Support\Str;
use Modules\Core\Http\Services\ApiTokenService;
class ApiTokenController extends Controller
{
    const parentPath = "api_token/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "api_token.index";
    const createRoute = "api_token.create";
    const editRoute = "api_token.edit";

    protected $apiTokenService, $dangerFlag;

    public function __construct(ApiTokenService $apiTokenService)
    {
        $this->apiTokenService = $apiTokenService;

        $this->dangerFlag = Constants::danger;
    }

    public function index(Request $request)
    {
        $dataArr = $this->apiTokenService->index($request);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::indexPath, $dataArr);
    }

    /**
     * Create a new API token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $token = $request->user()->createToken(
            $request->name,
            Jetstream::validPermissions($request->input('permissions', []))
        );

        return back()->with('flash', [
            'token' => explode('|', $token->plainTextToken, 2)[1],
        ]);
    }

    public function defaultTokenCreating(Request $request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try{

            if(Schema::hasColumn("psx_projects", "token")){
                $project = Project::first();
                // prevent creating duplicate token
                $checkTokenExist = PersonalAccessToken::where('name',$request->name)->first();

                if(!isset($checkTokenExist) || empty($checkTokenExist) || empty($project->token)){
                    $token = $request->user()->createToken(
                        $request->name,
                        Jetstream::validPermissions($request->input('permissions', []))
                    );

                    $project->token = explode('|', $token->plainTextToken, 2)[1];
                    $project->update();
                }
                $token = $project->token;
            } else {
                $token = null;
            }


            return redirect()->back()->with([
                'defaultBuilderToken' => $token,
            ]);


            // dd($checkTokenExist);
            // if(!empty($checkTokenExist)){
            //     $checkTokenExist->token = hash('sha256',$plainTextToken = Str::random(40));
            //     $checkTokenExist->update();

            //     $token = new NewAccessToken($checkTokenExist,$checkTokenExist->getKey()."|".$plainTextToken);
            // }
            // else{
            //     $token = $request->user()->createToken(
            //         $request->name,
            //         Jetstream::validPermissions($request->input('permissions', []))
            //     );
            // }

            // $project->token = explode('|', $token->plainTextToken, 2)[1];
            // $project->update();

            // return redirect()->back()->with([
            //     'defaultBuilderToken' => explode('|', $token->plainTextToken, 2)[1],
            // ]);
        }
        catch(\Throwable $e){
            // dd($e);
            return $e->getMessage();
        }
    }

    /**
     * Update the given API token's permissions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $tokenId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $tokenId)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        $token = $request->user()->tokens()->where('id', $tokenId)->firstOrFail();

        $token->forceFill([
            'abilities' => Jetstream::validPermissions($request->input('permissions', [])),
        ])->save();

        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $dataArr = $this->apiTokenService->create();
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::createPath, $dataArr);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->apiTokenService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $dataArr = $this->apiTokenService->destroy($id);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
