<?php

namespace App\Services\ProfileSettings;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProfileSettingsService
{
    public function selectRole(User $user, Request $request)
    {

        $role = $request->role;
        if ($role === Role::TEACHER) {

            $team = Team::create([
                'name' => $user->name . "'s Team",
                'user_id' => $user->id,
                'personal_team' => true,
            ]);

            $user->current_team_id = $team->id;
            $user->save();
            $team->users()->attach($user->id, ['role' => $role->value]);

        } else {
            $role = Role::STUDENT;

            $team = Team::create([
                'name' => $user->name . "'s Study Group",
                'user_id' => $user->id,
                'personal_team' => false,
            ]);

            $user->current_team_id = $team->id;
            $user->save();
            $team->users()->attach($user->id, ['role' => $role->value]);
        }

        return response()->json(['message' => 'Role and team assigned successfully.'], Response::HTTP_OK);
    }
}
