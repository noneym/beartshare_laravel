<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@beartshare.com',
            'password' => Hash::make('password'),
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Yağlı Boya', 'slug' => 'yagli-boya'],
            ['name' => 'Akrilik', 'slug' => 'akrilik'],
            ['name' => 'Suluboya', 'slug' => 'suluboya'],
            ['name' => 'Heykel', 'slug' => 'heykel'],
            ['name' => 'Fotoğraf', 'slug' => 'fotograf'],
            ['name' => 'Dijital Sanat', 'slug' => 'dijital-sanat'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Artists (Based on beartshare.com)
        $artists = [
            [
                'name' => 'Mustafa Ata',
                'slug' => 'mustafa-ata',
                'birth_year' => 1945,
                'biography' => '1945\'te Trabzon\'da doğan Mustafa Ata, çağdaş Türk resminin kendine özgü üsluplarından birini inşa eden usta bir sanatçıdır. İlk ve orta öğrenimini Trabzon\'da tamamladıktan sonra 1967\'de Devlet Güzel Sanatlar Akademisi Resim Bölümü\'ne girdi. Erken dönemlerinde figürlü temel alan kompozisyonlar üreten sanatçı, zamanla yalnızlık, renk ve biçim ilişkisini merkeze alan; kaligrafik örgüsel yapılarıyla tanınan özgün bir plastik dile yöneldi.',
            ],
            [
                'name' => 'Turgut Atalay',
                'slug' => 'turgut-atalay',
                'birth_year' => 1918,
                'death_year' => 2004,
                'biography' => 'Türk resim sanatının önemli temsilcilerinden biri olan Turgut Atalay, soyut ekspresyonist tarzı ile tanınmaktadır.',
            ],
            [
                'name' => 'Ali Atmaca',
                'slug' => 'ali-atmaca',
                'birth_year' => 1947,
                'biography' => 'Türk çağdaş sanatının önemli temsilcilerinden Ali Atmaca, renkli ve dinamik kompozisyonlarıyla tanınır.',
            ],
            [
                'name' => 'Aydın Ayan',
                'slug' => 'aydin-ayan',
                'birth_year' => 1953,
                'biography' => 'Aydın Ayan, Türk resim sanatının önemli isimlerinden biridir. Eserlerinde doğa ve insan ilişkisini işlemektedir.',
            ],
            [
                'name' => 'Mustafa Ayaz',
                'slug' => 'mustafa-ayaz',
                'birth_year' => 1938,
                'death_year' => 2024,
                'biography' => 'Türk resminin önemli temsilcilerinden Mustafa Ayaz, geleneksel Türk motiflerini çağdaş yorumlarıyla birleştiren eserler üretmiştir.',
            ],
            [
                'name' => 'Resul Aytemür',
                'slug' => 'resul-aytemur',
                'birth_year' => 1951,
                'biography' => 'Resul Aytemür, toplumsal konuları ele alan figüratif çalışmalarıyla tanınan önemli bir Türk sanatçıdır.',
            ],
            [
                'name' => 'Hasan Vecih Bereketoğlu',
                'slug' => 'hasan-vecih-beretekoglu',
                'birth_year' => 1895,
                'death_year' => 1971,
                'biography' => 'Türk resminin öncü isimlerinden Hasan Vecih Bereketoğlu, empresyonist tarzı ile tanınmaktadır.',
            ],
            [
                'name' => 'Sabri Berkel',
                'slug' => 'sabri-berkel',
                'birth_year' => 1908,
                'death_year' => 1993,
                'biography' => 'Sabri Berkel, Türk resminin önemli isimlerinden biridir. Geometrik soyutlama ve renk ilişkisi üzerine çalışmalar yapmıştır.',
            ],
            [
                'name' => 'Orhan Peker',
                'slug' => 'orhan-peker',
                'birth_year' => 1927,
                'death_year' => 1978,
                'biography' => 'Orhan Peker, Türk resminin en özgün sanatçılarından biridir. Figüratif çalışmaları ve at desenleriyle tanınır.',
            ],
            [
                'name' => 'Şadan Bezeyiş',
                'slug' => 'sadan-bezeyis',
                'birth_year' => 1926,
                'biography' => 'Türk çağdaş sanatının önemli isimlerinden Şadan Bezeyiş, soyut ekspresyonist çalışmalarıyla bilinir.',
            ],
        ];

        foreach ($artists as $artistData) {
            Artist::create($artistData);
        }

        // Create Artworks
        $artworks = [
            [
                'artist_id' => 1, // Mustafa Ata
                'category_id' => 1, // Yağlı Boya
                'title' => 'İsimsiz',
                'slug' => 'mustafa-ata-isimsiz-1',
                'technique' => 'Tuval üzerine yağlıboya',
                'dimensions' => '97 x 130 cm',
                'year' => 1987,
                'price_tl' => 1150000,
                'price_usd' => 26441,
                'is_featured' => true,
                'description' => 'Sanatçının imzalı özgün eseridir.',
            ],
            [
                'artist_id' => 6, // Resul Aytemür
                'category_id' => 1,
                'title' => 'Cumhuriyet\'in 100. Yılı (Otoportreli)',
                'slug' => 'resul-aytemur-cumhuriyet-100',
                'technique' => 'Tuval üzerine yağlıboya',
                'dimensions' => '120 x 160 cm',
                'year' => 2023,
                'price_tl' => 575000,
                'price_usd' => 13221,
                'is_featured' => true,
                'description' => 'Cumhuriyetin 100. yılına özel hazırlanmış anıt eser.',
            ],
            [
                'artist_id' => 9, // Orhan Peker
                'category_id' => 1,
                'title' => 'Horoz',
                'slug' => 'orhan-peker-horoz',
                'technique' => 'Karton üzerine yağlıboya (mukavvaya marufle)',
                'dimensions' => '62 x 72 cm',
                'year' => 1956,
                'price_tl' => 850000,
                'price_usd' => 19540,
                'is_sold' => true,
                'description' => 'Eser Yüksek Ressam Sayın Bayram Karşıt tarafından onaylıdır.',
            ],
            [
                'artist_id' => 2, // Turgut Atalay
                'category_id' => 1,
                'title' => 'Soyut Kompozisyon',
                'slug' => 'turgut-atalay-soyut-kompozisyon',
                'technique' => 'Tuval üzerine yağlıboya',
                'dimensions' => '80 x 100 cm',
                'year' => 1975,
                'price_tl' => 920000,
                'price_usd' => 21149,
                'is_featured' => true,
            ],
            [
                'artist_id' => 3, // Ali Atmaca
                'category_id' => 1,
                'title' => 'Mavi Düşler',
                'slug' => 'ali-atmaca-mavi-dusler',
                'technique' => 'Tuval üzerine yağlıboya',
                'dimensions' => '100 x 120 cm',
                'year' => 1990,
                'price_tl' => 680000,
                'price_usd' => 15632,
                'is_featured' => true,
            ],
            [
                'artist_id' => 4, // Aydın Ayan
                'category_id' => 1,
                'title' => 'Doğanın Sesi',
                'slug' => 'aydin-ayan-doganin-sesi',
                'technique' => 'Tuval üzerine yağlıboya',
                'dimensions' => '90 x 110 cm',
                'year' => 2010,
                'price_tl' => 450000,
                'price_usd' => 10345,
            ],
            [
                'artist_id' => 5, // Mustafa Ayaz
                'category_id' => 1,
                'title' => 'Geleneksel Motifler',
                'slug' => 'mustafa-ayaz-geleneksel-motifler',
                'technique' => 'Tuval üzerine karışık teknik',
                'dimensions' => '70 x 90 cm',
                'year' => 2005,
                'price_tl' => 380000,
                'price_usd' => 8736,
            ],
            [
                'artist_id' => 7, // Hasan Vecih Bereketoğlu
                'category_id' => 1,
                'title' => 'İstanbul Manzarası',
                'slug' => 'hasan-vecih-beretekoglu-istanbul-manzarasi',
                'technique' => 'Tuval üzerine yağlıboya',
                'dimensions' => '50 x 70 cm',
                'year' => 1955,
                'price_tl' => 1200000,
                'price_usd' => 27586,
            ],
            [
                'artist_id' => 8, // Sabri Berkel
                'category_id' => 1,
                'title' => 'Geometrik Armoni',
                'slug' => 'sabri-berkel-geometrik-armoni',
                'technique' => 'Tuval üzerine yağlıboya',
                'dimensions' => '80 x 80 cm',
                'year' => 1970,
                'price_tl' => 750000,
                'price_usd' => 17241,
            ],
            [
                'artist_id' => 10, // Şadan Bezeyiş
                'category_id' => 1,
                'title' => 'Renkli Düşünceler',
                'slug' => 'sadan-bezeyis-renkli-dusunceler',
                'technique' => 'Tuval üzerine akrilik',
                'dimensions' => '100 x 100 cm',
                'year' => 1985,
                'price_tl' => 520000,
                'price_usd' => 11954,
            ],
        ];

        foreach ($artworks as $artworkData) {
            Artwork::create($artworkData);
        }
    }
}
