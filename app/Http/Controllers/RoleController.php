<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response(RoleResource::collection(Role::all()), 200);
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
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'guard_name' => ['string'],
        ]);

        // Create Role
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
        ]);

        // Build return array
        $response = [
            'role' => RoleResource::make($role),
        ];

        return response($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Role $role
     * @return Response
     */
    public function show(Role $role)
    {
        return response(RoleResource::make($role), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Role $role
     * @return Response
     */
    public function update(Request $request, Role $role)
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
                Rule::unique('roles')->ignore($role),
            ],
        ]);

        // Create array of values to update
        $updateArray = $validated;
        $updateArray['updated_at'] = date('Y-m-d H:i:s');

        // Persist
        $role->update($updateArray);

        // Build return array to show new resource and the fields that were changed in the update
        $returnArray = [
            'role' => RoleResource::make($role),
            'updated' => $role->getChanges(),
        ];

        return response($returnArray, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response([
            'message' => 'Role deleted successfully',
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
            'role' => ['integer', 'exists:roles,id'],
            'user' => ['integer', 'exists:users,id'],
        ]);

        // Find records
        $role = Role::find($request->role);
        $user = User::find($request->user);

        // Check if the user already has the role assigned to them
        if($user->hasRole($role->name)){
            return response([
                'message' => 'Role is already assigned to user',
                'role' => $role->id,
                'user' => $user->id,
            ], 200);
        }

        // Assign role to user
        $user->assignRole($role->name);

        return response([
            'message' => 'Role assigned to user successfully',
            'role' => $role->id,
            'user' => $user->id,
        ], 201);
    }
}
