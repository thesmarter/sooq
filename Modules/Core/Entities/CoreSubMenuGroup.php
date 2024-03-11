<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreMenu;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\CoreMenuGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Casts\Attribute;


class CoreSubMenuGroup extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'sub_menu_name', 'sub_menu_desc', 'sub_menu_icon', 'sub_menu_lang_key', 'ordering', 'is_show_on_menu', 'core_menu_group_id', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag'];

    protected $table = "psx_core_sub_menu_groups";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const id = 'id';
    const tableName = "psx_core_sub_menu_groups";
    const coreMenuGroupId = "core_menu_group_id";
    const isShowOnMenu = "is_show_on_menu";
    const subMenuName = "sub_menu_name";

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\CoreMenuGroupsFactory::new();
    }

    public function module(){
        $loginUserRoles = UserPermission::where('user_id', Auth::id())->first();
//        return $this->hasMany(CoreModule::class)->where('is_show_on_menu', 1)->orderBy('ordering', 'asc');

        if (!empty($loginUserRoles)) {
            $roleIds = explode(',', $loginUserRoles->role_id);
            $coreModuleIds = RolePermission::whereIn('role_id', $roleIds)->get()->pluck("module_id");
            $moduleIds = Module::whereIn('id', $coreModuleIds)->where('menu_id', '!=', 0)->get()->pluck("menu_id");

            return $this->hasMany(CoreMenu::class)->where('is_show_on_menu', 1)->whereIn('id',$moduleIds)->orderBy('ordering', 'asc')->with(['routeName']);
        } else {
            return $this->hasMany(CoreMenu::class)->where('is_show_on_menu', 1)->orderBy('ordering', 'asc')->with(['routeName']);
        }

    }

    public function core_menu_group(){
        return $this->belongsTo(CoreMenuGroup::class);
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function editor(){
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function icon(){
        return $this->belongsTo(Icon::class,'icon_id','id');
    }

    public function routeName(){
        return $this->belongsTo(Module::class, "module_id", "id");
    }


    public function authorizations($abilities = []){
        return collect(array_flip($abilities))->map(function ($index, $ability){
            return Gate::allows($ability, $this);
        });
    }

//    public function toArray()
//    {
//        return parent::toArray() + [
//            'authorizations' => $this->authorizations(['update','delete','create'])
//        ];
//    }

    protected function authorization(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->authorizations(['update','delete','create']),
        );
    }
}
