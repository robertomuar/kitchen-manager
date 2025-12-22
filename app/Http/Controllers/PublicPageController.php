<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function home(Request $request): Response
    {
        return Inertia::render('Public/Home', $this->pageData($request, 'Inicio'));
    }

    public function features(Request $request): Response
    {
        return Inertia::render('Public/Features', $this->pageData($request, 'Funcionalidades'));
    }

    public function faq(Request $request): Response
    {
        return Inertia::render('Public/Faq', $this->pageData($request, 'Preguntas frecuentes'));
    }

    public function pricing(Request $request): Response
    {
        return Inertia::render('Public/Pricing', $this->pageData($request, 'Planes y precios'));
    }

    public function contact(Request $request): Response
    {
        return Inertia::render('Public/Contact', $this->pageData($request, 'Contacto'));
    }

    public function privacy(Request $request): Response
    {
        return Inertia::render('Public/PrivacyPolicy', $this->pageData($request, 'Política de privacidad'));
    }

    public function terms(Request $request): Response
    {
        return Inertia::render('Public/Terms', $this->pageData($request, 'Términos y condiciones'));
    }

    private function pageData(Request $request, string $title): array
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $path = '/' . ltrim($request->path(), '/');
        $canonical = $baseUrl . ($path === '/' ? '' : $path);

        return [
            'baseUrl' => $baseUrl,
            'canonical' => $canonical,
            'title' => $title,
        ];
    }
}
