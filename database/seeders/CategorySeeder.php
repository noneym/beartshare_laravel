<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Yağlı Boya', 'description' => 'Yağlı boya tablo eserleri'],
            ['name' => 'Akrilik', 'description' => 'Akrilik boya eserleri'],
            ['name' => 'Akrilik Panel', 'description' => 'Akrilik üzerine baskı / panel eserler (örn. Julian Opie tarzı)'],
            ['name' => 'Karışık Teknik', 'description' => 'Birden çok teknik kullanılarak yapılmış eserler'],
            ['name' => 'Suluboya', 'description' => 'Suluboya eserleri'],
            ['name' => 'Guaj', 'description' => 'Guaj boya eserleri'],
            ['name' => 'Pastel', 'description' => 'Pastel boya eserleri'],
            ['name' => 'Karakalem / Çizim', 'description' => 'Kâğıt üzerine karakalem ve grafit çizimler'],
            ['name' => 'Mürekkep', 'description' => 'Mürekkep çalışmaları'],
            ['name' => 'Kolaj', 'description' => 'Kolaj eserleri'],
            ['name' => 'Heykel', 'description' => 'Üç boyutlu heykel eserleri'],
            ['name' => 'Bronz Heykel', 'description' => 'Bronz döküm heykel eserleri'],
            ['name' => 'Seramik', 'description' => 'Seramik eserler'],
            ['name' => 'Fotoğraf', 'description' => 'Fotoğraf eserleri / baskıları'],
            ['name' => 'Dijital Sanat', 'description' => 'Dijital olarak üretilmiş eserler'],
            ['name' => 'Baskı / Print', 'description' => 'Sınırlı sayıda baskı eserler'],
            ['name' => 'Litografi', 'description' => 'Litografi baskı eserler'],
            ['name' => 'Serigrafi', 'description' => 'Serigrafi (silk-screen) baskı eserler'],
            ['name' => 'Gravür', 'description' => 'Gravür / etching eserler'],
            ['name' => 'Mixed Media', 'description' => 'Karma medya eserleri'],
        ];

        foreach ($categories as $idx => $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
