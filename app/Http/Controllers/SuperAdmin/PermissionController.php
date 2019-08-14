<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;


class PermissionController extends Controller
{
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
            'edit_permission_name'       => 'required|min:3|max:30'
        ]);

        if($validator->passes())
        {
            $permission = Permission::find($request->permission_value);
            $permission->name = $request->edit_permission_name;

            return ($permission->save()) ? response()->json(['success' => true]) : response()->json(['success' => false]);
        }

        return response()->json($validator->errors());
    }

    #delete the permission row
    public function deletePermission(Request $request)
    {
        $permission = Permission::find($request->delete_permission_row);
        return ($permission->delete()) ? response()->json(['success' => true]) : response()->json(['success' => false]);
    }

}
