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

            $action = '<table class="table table-bordered">';
            $action .= '<tr><td colspan="3">Role ID: '.$request->permission_value.'</td></tr>';
            $action .= '<tr><td></td><td>Previous</td><td>Updated</td></tr>';
            $action .= '<tr><td>Permission Name</td><td>'.$permission->name.'</td><td>'.$request->edit_permission_name.'</td></tr>';
            $action .= '</table>';

            $this->activity->activity_log($action, "updated permission");

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
        $this->activity->activity_log($action, "Deleted a permission");

        return ($permission->delete()) ? response()->json(['success' => true]) : response()->json(['success' => false]);
    }

}
