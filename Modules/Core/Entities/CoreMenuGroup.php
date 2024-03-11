<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreSubMenuGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CoreMenuGroup extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'group_name', 'group_icon', 'group_lang_key', 'is_show_on_menu', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag'];

    protected $table = "psx_core_menu_groups";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const groupName = "group_name";
    const tableName = "psx_core_menu_groups";
    const id = 'id';
    const isShowOnMenu = "is_show_on_menu";

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\CoreMenuGroupsFactory::new();
    }

    public function sub_menu_group(){
        $loginUserRoles = UserPermission::where('user_id', Auth::id())->first();

        if (!empty($loginUserRoles)) {
            $roleIds = explode(',', $loginUserRoles->role_id);
            $moduleIds = RolePermission::whereIn('role_id', $roleIds)->get()->pluck("module_id");
            $dropDownSubMenuIds = CoreSubMenuGroup::where("is_dropdown", 1)->get()->pluck("id");
            $linkSubMenuIds = Module::whereIn('id', $moduleIds)->where('sub_menu_id', '!=', 0)->get()->pluck("sub_menu_id");
            $subMenuIds = $dropDownSubMenuIds->merge($linkSubMenuIds);
            return $this->hasMany(CoreSubMenuGroup::class)->where('is_show_on_menu', 1)->with(['module', 'icon', 'routeName'])->whereIn('id', $subMenuIds)->orderBy('ordering', 'asc');
        } else {
            return $this->hasMany(CoreSubMenuGroup::class)->where('is_show_on_menu', 1)->with(['module', 'icon', 'routeName'])->orderBy('ordering', 'asc');
        }
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function editor(){
        return $this->belongsTo(User::class, 'updated_user_id');
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
