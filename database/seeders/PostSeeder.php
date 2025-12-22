<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        Post::updateOrCreate(
            ['slug' => 'organizar-despensa-inventario-digital'],
            [
                'title' => 'Cómo organizar tu despensa con un inventario digital',
                'excerpt' => 'Una guía práctica para transformar tu despensa en un sistema ordenado y fácil de mantener, sin depender de listas en papel.',
                'content' => '<p>Organizar la despensa no tiene por qué ser complicado. El primer paso es crear un inventario básico con los productos que compras con más frecuencia. Registra nombre, cantidad y ubicación; con eso tendrás un mapa real de lo que ya tienes.</p><p>El segundo paso es incluir fechas de caducidad. Esto te permite priorizar lo que debes consumir antes y reducir desperdicio. Un inventario digital te recuerda qué productos están cerca de caducar para que puedas planificar recetas.</p><p>Por último, define mínimos para los productos esenciales. Así sabrás cuándo reponer sin revisar cada estante. Con un par de minutos a la semana, tu despensa se mantiene ordenada y tus compras son más eficientes.</p>',
                'meta_description' => 'Guía práctica para organizar la despensa con un inventario digital y reducir el desperdicio de comida.',
                'published_at' => now()->subDays(7),
            ]
        );

        Post::updateOrCreate(
            ['slug' => 'alertas-caducidad-alimentos'],
            [
                'title' => 'Alertas de caducidad: evita tirar comida cada semana',
                'excerpt' => 'Aprende a configurar alertas de caducidad para consumir a tiempo y ahorrar dinero en tus compras semanales.',
                'content' => '<p>Las fechas de caducidad son una de las principales causas de desperdicio doméstico. Al registrar cada producto con su fecha de vencimiento, puedes ver rápidamente qué debes consumir primero.</p><p>Las alertas de caducidad ayudan a planificar menús. Cuando un producto está por vencer, puedes incorporarlo a tu receta semanal y aprovecharlo en lugar de tirarlo.</p><p>Con un sistema de inventario, estas alertas se vuelven automáticas. Solo necesitas mantener tu stock actualizado y la app te mostrará qué productos requieren atención.</p>',
                'meta_description' => 'Configura alertas de caducidad para reducir desperdicio y planificar compras inteligentes en casa.',
                'published_at' => now()->subDays(3),
            ]
        );
    }
}
