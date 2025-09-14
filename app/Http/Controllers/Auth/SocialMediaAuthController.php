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

    public function facebook(Request $request)
    {
    }

    public function google(Request $request)
    {
    }
}
