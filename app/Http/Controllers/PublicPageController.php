<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Public/Home');
    }

    public function features(): Response
    {
        return Inertia::render('Public/Features');
    }

    public function faq(): Response
    {
        return Inertia::render('Public/Faq');
    }

    public function pricing(): Response
    {
        return Inertia::render('Public/Pricing');
    }

    public function contact(): Response
    {
        return Inertia::render('Public/Contact');
    }

    public function privacy(): Response
    {
        return Inertia::render('Public/PrivacyPolicy');
    }

    public function terms(): Response
    {
        return Inertia::render('Public/Terms');
    }

    public function cookiesPolicy(): Response
    {
        return Inertia::render('Public/CookiesPolicy');
    }

    public function legalNotice(): Response
    {
        return Inertia::render('Legal/AvisoLegal');
    }

    public function legalTerms(): Response
    {
        return Inertia::render('Legal/Terminos');
    }

    public function legalPrivacy(): Response
    {
        return Inertia::render('Legal/Privacidad');
    }

    public function legalCookies(): Response
    {
        return Inertia::render('Legal/Cookies');
    }
}
