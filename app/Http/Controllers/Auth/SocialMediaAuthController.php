<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\SocialMediaAuthService;
use Illuminate\Http\Request;

class SocialMediaAuthController extends Controller
{

    protected $socialMediaAuthService;

    public function __construct(SocialMediaAuthService $socialMediaAuthService)
    {
        $this->socialMediaAuthService = $socialMediaAuthService;
    }

    public function redirect(string $provider)
    {
        return $this->socialMediaAuthService->redirect($provider);
    }

    public function callback(string $provider)
    {
        return $this->socialMediaAuthService->callback($provider);
    }

}