<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Reports\Reports;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;


class PermissionController extends Controller
{
    /**
     * @var $activity
     * */
    private $activity;

    public function __construct()
    {
        $this->activity = new Reports;
    }

    #retrieve the permission details like id or name
    public function getPermissionDetails(Request $request)
    {
        $permission = Permission::find($request->id);
        return $permission;
    }

    #update the permission details/name
    public function updatePermission(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'edit_permission_name'       => 'required|min:3|max:30|unique:permissions,name'
        ]);

        if($validator->passes())
        {
            $permission = Permission::find($request->permission_value);
            /*activity log*/
            $action = "updated the permission from ".$permission->name." to ".$request->edit_permission_name." with permission id: ".$request->permission_value;

            $permission->name = $request->edit_permission_name;

            $this->activity->activity_log($action);

            return ($permission->save()) ? response()->json(['success' => true]) : response()->json(['success' => false]);

        }

        return response()->json($validator->errors());
    }

    #delete the permission row
    public function deletePermission(Request $request)
    {
        $permission = Permission::find($request->delete_permission_row);

        /*activity logs*/
        $action = "deleted a permission name: ".$permission->name." with permission id: ".$request->delete_permission_row;
        $this->activity->activity_log($action);

        return ($permission->delete()) ? response()->json(['success' => true]) : response()->json(['success' => false]);
    }

}
