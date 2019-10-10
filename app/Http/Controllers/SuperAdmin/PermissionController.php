<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Reports\Reports;
use App\Http\Controllers\UserAgentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;


class PermissionController extends Controller
{
    /**
     * @var $activity
     * */
    private $activity;
    /**
     * @var $device
     * */
    private $device;

    public function __construct()
    {
        $this->activity = new Reports;
        $this->device = new UserAgentController;
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

        $agent = new Agent;
        $validator = Validator::make($request->all(),[
            'edit_permission_name'       => 'required|min:3|max:30|unique:permissions,name'
        ]);

        if($validator->passes())
        {
            $permission = Permission::find($request->permission_value);
            $permission->name = $request->edit_permission_name;
            /*activity log*/

            $action = '<table class="table table-bordered">';
            $action .= '<tr><td>Ip Address: '.\request()->ip().'</td><td>Browser: '.$agent->browser().' '.$agent->version($agent->browser()).'</td>
                        <td>Device Used: '.$this->device->check_device().'</td><td>Operating System: '.$agent->platform().' '.$agent->version($agent->platform()).'</td></tr>';
            $action .= '</table>';
            $action .= '<table class="table table-bordered">';
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

        $description = "Deleted a permission";
        /*activity logs*/
        $action = '<table class="table table-bordered">';
        $action .= '<tr><td>Ip Address: '.\request()->ip().'</td><td>Browser: '.$this->device->agent->browser().' '.$this->device->agent->version($this->device->agent->browser()).'</td>
                        <td>Device Used: '.$this->device->check_device().'</td><td>Operating System: '.$this->device->agent->platform().' '.$this->device->agent->version($this->device->agent->platform()).'</td></tr>';
        $action .= '</table>';

        $action .= '<table>';
        $action .= '<tr><td colspan="2">Action: '.$description.'</td></tr>';
        $action .= '<tr><td>Permission ID</td><td>'.$request->delete_permission_row.'</td></tr>';
        $action .= '<tr><td>Permission Name</td><td>'.$permission->name.'</td></tr>';
        $action .= '</table>';

        $this->activity->activity_log($action, $description);

        return ($permission->delete()) ? response()->json(['success' => true]) : response()->json(['success' => false]);
    }

}
