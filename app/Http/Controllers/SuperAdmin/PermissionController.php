<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Reports\Reports;
use App\Http\Controllers\UserAgentController;
use http\Env\Response;
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

            /**
             * @var $description
             * */
            $description = "Updated permission";
            $action = '<table class="table table-bordered">';
            $action .= '<tr><td><b>Ip Address: '.\request()->ip().'</b></td><td><b>Browser: </b>'.$agent->browser().' '.$agent->version($agent->browser()).'</td>
                        <td><b>Device Used:</b> '.$this->device->check_device().'</td><td><b>Operating System: </b>'.$agent->platform().' '.$agent->version($agent->platform()).'</td></tr>';
            $action .= '</table>';
            $action .= '<table class="table table-bordered">';
            $action .= '<tr><td colspan="3"><b>Action: </b>'.$description.'</td></tr>';
            $action .= '<tr><td colspan="3"><b>Role ID: </b>'.$request->permission_value.'</td></tr>';
            $action .= '<tr><td></td><td><b>Previous</b></td><td><b>Updated</b></td></tr>';
            $action .= '<tr><td><b>Permission Name</b></td><td>'.$permission->name.'</td><td>'.$request->edit_permission_name.'</td></tr>';
            $action .= '</table>';

            $this->activity->activity_log($action, $description);

            return ($permission->save()) ? response()->json(['success' => true]) : response()->json(['success' => false]);

        }

        return response()->json($validator->errors());
    }

    #delete the permission row
    /**
     * by: john kevin paunel
     * delete the permission row
     * @param Request $request
     * @return Response
     * */
    public function deletePermission(Request $request)
    {
        $permission = Permission::find($request->delete_permission_row);

        /**
         * @var $description
         * */
        $description = "Deleted a permission";
        /*activity logs*/
        /**
         * @var $action
         * */
        $action = '<table class="table table-bordered">';
        $action .= '<tr><td>Ip Address: '.\request()->ip().'</td><td>Browser: '.$this->device->agent->browser().' '.$this->device->agent->version($this->device->agent->browser()).'</td>
                        <td>Device Used: '.$this->device->check_device().'</td><td>Operating System: '.$this->device->agent->platform().' '.$this->device->agent->version($this->device->agent->platform()).'</td></tr>';
        $action .= '</table>';

        $action .= '<table>';
        $action .= '<tr><td colspan="2"><b>Action: </b>'.$description.'</td></tr>';
        $action .= '<tr><td><b>Permission ID</b></td><td>'.$request->delete_permission_row.'</td></tr>';
        $action .= '<tr><td><b>Permission Name</b></td><td>'.$permission->name.'</td></tr>';
        $action .= '</table>';

        $this->activity->activity_log($action, $description);

        return ($permission->delete()) ? response()->json(['success' => true]) : response()->json(['success' => false]);
    }

}
