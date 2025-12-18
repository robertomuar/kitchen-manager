<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminDatabaseController extends Controller
{
    public function index(Request $request)
    {
        $tables = $this->mysqlTables();
        $table  = $request->query('table');

        // Si no hay tabla seleccionada, solo renderiza sidebar
        if (!$table) {
            return Inertia::render('Admin/DbBrowser', [
                'tables'      => $tables,
                'activeTable' => null,
                'columns'     => [],
                'rows'        => null,
            ]);
        }

        // Seguridad: solo permitir tablas existentes
        if (!in_array($table, $tables, true)) {
            abort(404);
        }

        $columns = Schema::getColumnListing($table);

        $query = DB::table($table);

        // Orden estable si existe id o created_at
        if (in_array('id', $columns, true)) {
            $query->orderByDesc('id');
        } elseif (in_array('created_at', $columns, true)) {
            $query->orderByDesc('created_at');
        }

        $rows = $query->paginate(50);

        // ✅ Importante: NO propagar _token ni _method en URLs de paginación
        $rows->appends($request->except(['page', '_token', '_method']));

        // Recorta strings muy largos para no reventar la tabla
        $rows->setCollection(
            $rows->getCollection()->map(function ($row) {
                $arr = (array) $row;

                foreach ($arr as $k => $v) {
                    if (is_string($v) && mb_strlen($v) > 200) {
                        $arr[$k] = Str::limit($v, 200);
                    }
                }

                return $arr;
            })
        );

        return Inertia::render('Admin/DbBrowser', [
            'tables'      => $tables,
            'activeTable' => $table,
            'columns'     => $columns,
            'rows'        => $rows,
        ]);
    }

    public function showRow(Request $request)
    {
        $tables = $this->mysqlTables();
        $table  = $request->query('table');
        $id     = $request->query('id');

        if (!$table || !in_array($table, $tables, true)) {
            abort(404);
        }

        $columns = Schema::getColumnListing($table);

        if (!in_array('id', $columns, true)) {
            abort(400, 'Esta tabla no tiene columna id.');
        }

        $row = DB::table($table)->where('id', $id)->first();
        if (!$row) abort(404);

        return Inertia::render('Admin/DbRow', [
            'table' => $table,
            'row'   => (array) $row,
        ]);
    }

    private function mysqlTables(): array
    {
        $raw = DB::select('SHOW TABLES');
        $tables = [];

        foreach ($raw as $obj) {
            $arr = (array) $obj;
            $firstKey = array_key_first($arr);
            if ($firstKey) {
                $tables[] = (string) $arr[$firstKey];
            }
        }

        sort($tables);
        return $tables;
    }
}
