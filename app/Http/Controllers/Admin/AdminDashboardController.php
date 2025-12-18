<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $tablesWanted = [
            'users',
            'kitchens',
            'kitchen_user',
            'products',
            'locations',
            'stock_items',
        ];

        $counts = [];
        foreach ($tablesWanted as $t) {
            if (Schema::hasTable($t)) {
                $counts[$t] = DB::table($t)->count();
            }
        }

        return Inertia::render('Admin/Dashboard', [
            'counts' => $counts,
        ]);
    }
}
