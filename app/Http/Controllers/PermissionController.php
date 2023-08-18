<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response(PermissionResource::collection(Permission::all()), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate posted fields
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'guard_name' => ['string'],
        ]);

        // Create Permission
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
        ]);

        // Build return array
        $response = [
            'permission' => PermissionResource::make($permission),
        ];

        return response($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Permission $permission
     * @return Response
     */
    public function show(Permission $permission)
    {
        return response(PermissionResource::make($permission), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Permission $permission
     * @return Response
     */
    public function update(Request $request, Permission $permission)
    {
        /*
         * Name should be unique
         * If a user PUTs name through request with the existing name
         * So we tell the validation to ignore the unique rule when this happens
         * */
        $validated = $request->validate([
            'name' => [
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($permission),
            ],
        ]);

        // Create array of values to update
        $updateArray = $validated;
        $updateArray['updated_at'] = date('Y-m-d H:i:s');

        // Persist
        $permission->update($updateArray);

        // Build return array to show new resource and the fields that were changed in the update
        $returnArray = [
            'permission' => PermissionResource::make($permission),
            'updated' => $permission->getChanges(),
        ];

        return response($returnArray, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Permission $permission
     * @return Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response([
            'message' => 'Permission deleted successfully',
        ], 200);
    }

    /**
     * Assign a permission to a model
     *
     * @param Request $request
     * @return Response
     */
    public function assign(Request $request)
    {
        // Validate posted fields
        $request->validate([
            'permission' => ['integer', 'exists:permissions,id'],
            'role' => ['integer', 'exists:roles,id'],
        ]);

        // Find records
        $permission = Permission::find($request->permission);
        $role = Role::find($request->role);

        // Check if the role already has the permission assigned
        if($role->hasPermissionTo($permission->name)){
            return response([
                'message' => 'Permission is already assigned to role',
                'permission' => $permission->id,
                'role' => $role->id,
            ], 200);
        }

        // Assign permission to role
        $role->givePermissionTo($permission);

        return response([
            'message' => 'Permission assigned to role successfully',
            'permission' => $permission->id,
            'role' => $role->id,
        ], 201);
    }
}
