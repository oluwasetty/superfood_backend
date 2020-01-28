<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Resource as RoleResource;
use App\Http\Requests\RoleRequest;
use App\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // returns all roles
        $roles = Role::paginate(10);
        return RoleResource::collection($roles)->additional(['status' => true]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        // creates roles
        $role = Role::create([
            'name' => $request->name,
            'slug' => strtolower($request->name),
            'permissions' => $request->permissions,
        ]);
        return (new RoleResource($role))->additional(['status' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        // update all roles
        $role = Role::find($id);
        if ($role->update([
            'name' => $request->name,
            'slug' => strtolower($request->name),
            'permissions' => $request->permissions,
        ])) {
            return (new RoleResource($role))->additional(['status' => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete roles
        $role = Role::find($id);
        if ($role->delete()) {
            return (new RoleResource($role))->additional(['status' => true]);
        }
    }
}
