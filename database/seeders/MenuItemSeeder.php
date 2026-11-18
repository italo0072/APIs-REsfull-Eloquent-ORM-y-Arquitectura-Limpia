<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'entradas' => [
                ['Tiradito de atún con maracuyá', 'Atún fresco, leche de tigre de maracuyá y crocante de plátano.', 11.50, 10],
                ['Gyozas de cerdo y jengibre', 'Seis piezas con salsa ponzu y chile ahumado.', 9.00, 12],
            ],
            'sopas' => [
                ['Crema de camote y coco', 'Con aceite de cilantro y semillas tostadas.', 7.50, 15],
            ],
            'platos-fuertes' => [
                ['Costilla glaseada en tamarindo', 'Cocción lenta 12 horas, puré de yuca y vegetales asados.', 21.00, 25],
                ['Salmón miso con arroz de coco', 'Salmón glaseado en miso blanco, arroz de coco y pak choi.', 23.50, 22],
                ['Risotto de hongos y trufa', 'Arroz arborio, mezcla de hongos y aceite de trufa.', 17.00, 25],
            ],
            'postres' => [
                ['Tres leches de matcha', 'Bizcocho húmedo de matcha con crema de coco.', 6.50, 8],
                ['Volcán de chocolate y chile', 'Chocolate 70% con helado de vainilla.', 7.00, 15],
            ],
            'bebidas' => [
                ['Limonada de jengibre y albahaca', 'Refrescante, servida con hielo frappé.', 3.50, 5],
                ['Mojito de maracuyá', 'Ron blanco, hierbabuena y maracuyá natural.', 6.00, 6],
            ],
        ];

        foreach ($items as $slug => $rows) {
            $category = Category::where('slug', $slug)->firstOrFail();

            foreach ($rows as [$name, $description, $price, $time]) {
                MenuItem::updateOrCreate(
                    ['category_id' => $category->id, 'name' => $name],
                    [
                        'description' => $description,
                        'price' => $price,
                        'preparation_time' => $time,
                        'is_available' => true,
                    ]
                );
            }
        }
    }
}
