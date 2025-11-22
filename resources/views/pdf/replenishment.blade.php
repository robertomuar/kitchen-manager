<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de reposición</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 12px;
            color: #4b5563;
            margin-bottom: 12px;
        }
        .meta {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
        }
        th {
            background-color: #f3f4f6;
            font-size: 11px;
            text-align: left;
        }
        td {
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .small {
            font-size: 10px;
        }
        .badge-low {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 9999px;
            background-color: #fee2e2;
            color: #b91c1c;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <h1>Lista de reposición</h1>
    <p class="subtitle">Productos por debajo del mínimo configurado.</p>

    <p class="meta">
        Usuario: {{ $user->name }}<br>
        Generado: {{ $generatedAt->format('d/m/Y H:i') }}
    </p>

    @if ($items->isEmpty())
        <p>No hay ningún producto por debajo del mínimo en este momento. ✅</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Ubicación</th>
                    <th class="text-right">Cantidad actual</th>
                    <th>Unidad</th>
                    <th class="text-right">Mínimo</th>
                    <th class="text-right">Unidades a comprar</th>
                    <th class="text-center">Caducidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    @php
                        $quantity = (float) ($item->quantity ?? 0);
                        $min      = (float) ($item->min_quantity ?? 0);
                        $missing  = max(0, $min - $quantity);
                    @endphp
                    <tr>
                        <td>{{ optional($item->product)->name ?? '—' }}</td>
                        <td>{{ optional($item->location)->name ?? 'Sin ubicación' }}</td>
                        <td class="text-right">{{ number_format($quantity, 2, ',', '.') }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="text-right">{{ number_format($min, 2, ',', '.') }}</td>
                        <td class="text-right">
                            {{ number_format($missing, 2, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if ($item->expires_at)
                                {{ $item->expires_at->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="small" style="margin-top: 10px;">
        <!--     * “Unidades a comprar” = máximo(0, mínimo configurado − cantidad actual). -->
        </p>
    @endif
</body>
</html>
