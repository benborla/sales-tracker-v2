<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserInformation;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use App\Models\UserStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\GroupTeamMember;

class UserObserver
{
    public function creating(User $user)
    {
        Nova::whenServing(function (NovaRequest $request) use ($user) {

            if (!$this->isChangeFromStaffOrCustomerResource($request)) {
                return;
            }

            $middleInitial = strtoupper(substr($request->middle_name, 0, 1));
            $attributes = $user->getAttributes();
            $attributes['name'] = "$request->first_name $middleInitial. $request->last_name";
            $attributes['password'] = Hash::make('password');
            $attributes['email_verified_at'] = now();
            $attributes['remember_token'] = \Illuminate\Support\Str::random(10);

            foreach ((new UserInformation)->getFillable() as $field) {
                unset($attributes[$field]);
            }

            unset(
                $attributes['store'],
                $attributes['role'],
                $attributes['staff'],
                $attributes['team'],
            );

            $user->setRawAttributes($attributes);
        });
    }
    public function created(User $user)
    {
        $user->save();

        Nova::whenServing(function (NovaRequest $request) use ($user) {
            $userInformation = $request->only((new UserInformation)->getFillable());
            $userInformation['user_id'] = $user->id;

            // @INFO: Add user information
            UserInformation::create($userInformation);

            // @INFO: Add user to store
            $this->addToStore($user, (int) $request->store);

            // @INFO: Add user to the team
            if ($request->team) {
                GroupTeamMember::create([
                    'group_teams_id' => (int) $request->team,
                    'user_id' => $user->id
                ]);
            }

            // @INFO: Add user role
            DB::table('role_user')->insertOrIgnore([
                'user_id' => $user->id,
                'role_id' => (int) $request->role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function updating(User $user)
    {
        Nova::whenServing(function (NovaRequest $request) use ($user) {

            if (!$this->isChangeFromStaffOrCustomerResource($request)) {
                return;
            }

            $attributes = $user->getAttributes();

            foreach ((new UserInformation)->getFillable() as $field) {
                unset($attributes[$field]);
            }
            $user->setRawAttributes($attributes);
        });
    }

    public function updated(User $user)
    {
        Nova::whenServing(function (NovaRequest $request) use ($user) {
            $userInformation = $request->only((new UserInformation)->getFillable());

            if ($user->information instanceof UserInformation) {
                $user->information->update($userInformation);
            }
        });
    }

    /**
     * Adds the user to the specified store
     *
     * @return void
     */
    private function addToStore(User $user, $storeId): void
    {
        UserStore::firstOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'store_id' => $storeId
            ]
        );
    }

    /**
     * Only execute the observers if the changes are coming from Customer
     * or Staff resource
     *
     * @return bool
     */
    private function isChangeFromStaffOrCustomerResource(NovaRequest $request): bool
    {
        return in_array($request->type, [
            UserInformation::USER_TYPE_STAFF,
            UserInformation::USER_TYPE_CUSTOMER,
        ]);
    }
}
