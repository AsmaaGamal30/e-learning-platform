<?php

namespace App\Http\Controllers\Settings;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Services\ProfileSettings\ProfileSettingsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SelectRoleController extends Controller
{

    protected $profileSettingsService;

    public function __construct(ProfileSettingsService $profileSettingsService)
    {
        $this->profileSettingsService = $profileSettingsService;
    }

    public function store(User $user, Request $request)
    {
        $request->validate([
            'role' => 'required|in:student,teacher',
        ]);

        return $this->profileSettingsService->selectRole($user, $request);
    }
}
