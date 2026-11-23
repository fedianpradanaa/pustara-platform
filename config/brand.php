<?php

$brands = [
    'pustara' => [
        'name' => 'Pustaka Nusantara',
        'page_title' => 'Pustaka Nusantara',
        'description' => 'Pustaka Nusantara adalah perpustakaan digital untuk menemukan dan menikmati koleksi bacaan dengan mudah.',
        'social_description' => 'Perpustakaan digital, lebih dekat dan mudah diakses. Temukan dan nikmati koleksi bacaan melalui Pustaka Nusantara.',
        'social_image_path' => 'images/logo/social-preview.png',
        'logo_path' => 'images/logo/logo.png',
        'favicon_path' => 'images/logo/favicon.png',
        'apple_touch_icon_path' => 'images/logo/apple-touch-icon.png',
        'contact_email' => 'info@pustakanusantara.id',
        'website_suffix' => '.pustakanusantara.id',
        'landing' => [
            'hero_title' => 'Perpustakaan digital, lebih dekat dan mudah diakses.',
            'hero_description' => 'Pustaka Nusantara adalah ruang digital untuk menemukan dan menikmati koleksi bacaan. Akses layanan perpustakaan dengan praktis melalui satu platform.',
            'about_title' => 'Membuka akses ke pengalaman membaca digital.',
            'about_description' => 'Kami percaya akses terhadap bacaan dan pengetahuan sebaiknya terasa sederhana. Pustaka Nusantara menghadirkan pengalaman perpustakaan digital yang bisa dijangkau dari perangkat Anda.',
            'services_title' => 'Fokus pada hal yang penting: membaca.',
            'call_to_action' => 'Siap menjelajahi Pustaka Nusantara?',
        ],
        'theme' => [
            '50' => '239 246 255',
            '100' => '219 234 254',
            '200' => '191 219 254',
            '300' => '147 197 253',
            '400' => '96 165 250',
            '500' => '59 130 246',
            '600' => '37 99 235',
            '700' => '29 78 216',
            '800' => '30 64 175',
            '900' => '30 58 138',
            '950' => '23 37 84',
        ],
    ],
    'gia' => [
        'name' => 'GIA e-Library',
        'page_title' => 'GIA e-Library',
        'description' => 'GIA e-Library adalah perpustakaan digital untuk menjelajahi dan menikmati koleksi bacaan.',
        'social_description' => 'Jelajahi koleksi bacaan digital dengan mudah melalui GIA e-Library.',
        'social_image_path' => 'images/gia/social-preview.png',
        'logo_path' => 'images/gia/logo.png',
        'favicon_path' => 'images/gia/favicon.png',
        'apple_touch_icon_path' => 'images/gia/apple-touch-icon.png',
        'contact_email' => null,
        'website_suffix' => null,
        'landing' => [
            'hero_title' => 'Temukan bacaan yang membuka wawasan.',
            'hero_description' => 'GIA e-Library menghadirkan pengalaman membaca digital yang praktis untuk menjelajahi beragam koleksi buku.',
            'about_title' => 'Perpustakaan digital untuk pengalaman membaca yang lebih mudah.',
            'about_description' => 'Jelajahi koleksi bacaan melalui GIA e-Library dan nikmati akses perpustakaan digital yang nyaman dari berbagai perangkat.',
            'services_title' => 'Jelajahi koleksi, nikmati pengalaman membaca.',
            'call_to_action' => 'Mulai menjelajahi GIA e-Library.',
        ],
        'theme' => [
            '50' => '240 253 250',
            '100' => '204 251 241',
            '200' => '153 246 228',
            '300' => '94 234 212',
            '400' => '45 212 191',
            '500' => '20 184 166',
            '600' => '13 148 136',
            '700' => '15 118 110',
            '800' => '17 94 89',
            '900' => '19 78 74',
            '950' => '4 47 46',
        ],
    ],
];

$activeBrand = env('APP_BRAND', 'pustara');

if (!array_key_exists($activeBrand, $brands)) {
    throw new InvalidArgumentException("Unsupported APP_BRAND value: {$activeBrand}");
}

return [
    'active' => $activeBrand,
    'current' => $brands[$activeBrand],
];
