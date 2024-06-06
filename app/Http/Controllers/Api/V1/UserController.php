<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filter)
    {
        return UserResource::collection(
            User::filter($filter)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            Gate::authorize('store', User::class);
            return new UserResource(User::create($request->mappedAttributes()));

        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to create a ticket', 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $user_id)
    {
        //PATCH
        try {
            $user = User::findOrFail($user_id);

            //policy
            Gate::authorize('update', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);

        } catch (ModelNotFoundException $e) {
            return $this->error('user not found', 404);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this user', 403);
        }
    }

    public function replace(ReplaceUserRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            Gate::authorize('replace', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);

        } catch (ModelNotFoundException $e) {
            return $this->error('User not found', 404);
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            Gate::authorize('delete', $user);
            $user->delete();
            return $this->ok('User deleted', []);
        } catch (ModelNotFoundException $e) {
            return $this->error('User not found', 404);
        }
    }
}
