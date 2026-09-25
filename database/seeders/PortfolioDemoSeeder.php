<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Faq;
use App\Models\Package;
use App\Models\Photo;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class PortfolioDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Initial Settings
        Setting::set('demo_mode', '1');
        Setting::set('telegram', '@romerayun');
        Setting::set('phone', '+7 914 808-95-55');
        Setting::set('city_ru', 'Иркутск');
        Setting::set('city_en', 'Irkutsk');
        Setting::set('hero_phrase_ru', 'Фотограф в Иркутске – Роман Юн');
        Setting::set('hero_phrase_en', 'Photographer in Irkutsk – Roman Yun');
        Setting::set('hero_sub_ru', 'Портреты, съёмки для пар и семей, события и контент для бизнеса. Иркутск.');
        Setting::set('hero_sub_en', 'Portraits, sessions for couples and families, events, and business content. Irkutsk.');

        // 2. Categories
        $categoriesData = [
            [
                'slug' => 'portraits',
                'name_ru' => 'Портреты',
                'name_en' => 'Portraits',
                'sort_order' => 1,
            ],
            [
                'slug' => 'couples',
                'name_ru' => 'Пары',
                'name_en' => 'Couples',
                'sort_order' => 2,
            ],
            [
                'slug' => 'families',
                'name_ru' => 'Семьи',
                'name_en' => 'Families',
                'sort_order' => 3,
            ],
            [
                'slug' => 'events',
                'name_ru' => 'События',
                'name_en' => 'Events',
                'sort_order' => 4,
            ],
            [
                'slug' => 'business',
                'name_ru' => 'Бизнес и контент',
                'name_en' => 'Business & Content',
                'sort_order' => 5,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['slug']] = Category::updateOrCreate(['slug' => $c['slug']], $c);
        }

        // 3. Demo Photos Directory
        $demoDir = storage_path('app/public/demo');
        if (!File::exists($demoDir)) {
            File::makeDirectory($demoDir, 0755, true);
        }

        // Curated photography dataset with direct Unsplash CC-compatible IDs
        // and exact photographer attribution metadata
        $curatedPhotos = [
            'portrait-1' => [
                'url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Aiony Haust',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 1500,
            ],
            'portrait-2' => [
                'url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Joseph Gonzalez',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 1600,
            ],
            'portrait-3' => [
                'url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Valerie Elash',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 1500,
            ],
            'couple-1' => [
                'url' => 'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Everton Vila',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 800,
            ],
            'couple-2' => [
                'url' => 'https://images.unsplash.com/photo-1522529599102-193c0d76b5b6?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Candice Picard',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 1500,
            ],
            'couple-3' => [
                'url' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Carly Rae Hobbins',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 800,
            ],
            'family-1' => [
                'url' => 'https://images.unsplash.com/photo-1542037104857-ffbb0b9155fb?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Jessica Rockowitz',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 800,
            ],
            'family-2' => [
                'url' => 'https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Daiga Ellaby',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 1500,
            ],
            'event-1' => [
                'url' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Evangeline Shaw',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 800,
            ],
            'event-2' => [
                'url' => 'https://images.unsplash.com/photo-1528605248644-14dd04022da1?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Priscilla Du Preez',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 800,
            ],
            'business-1' => [
                'url' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Austin Distel',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 800,
            ],
            'business-2' => [
                'url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
                'author' => 'Nastuh Abootalebi',
                'license' => 'Unsplash Free License',
                'width' => 1200,
                'height' => 800,
            ],
            'hero-main' => [
                'url' => 'https://images.unsplash.com/photo-1500485035595-cbe6f645feb1?auto=format&fit=crop&w=1400&q=85',
                'author' => 'Bailey Zindel',
                'license' => 'Unsplash Free License',
                'width' => 1400,
                'height' => 1750,
            ],
            'hero-secondary' => [
                'url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1000&q=80',
                'author' => 'David Marcu',
                'license' => 'Unsplash Free License',
                'width' => 1000,
                'height' => 1250,
            ],
        ];

        // Download or create local cache
        $credits = [];
        foreach ($curatedPhotos as $name => $data) {
            $localFilename = "demo/{$name}.jpg";
            $localFullPath = storage_path("app/public/{$localFilename}");

            if (!File::exists($localFullPath)) {
                try {
                    $response = Http::timeout(15)->get($data['url']);
                    if ($response->successful()) {
                        File::put($localFullPath, $response->body());
                    } else {
                        $this->createFallbackSvgPlaceholder($localFullPath, $name);
                    }
                } catch (\Throwable $e) {
                    $this->createFallbackSvgPlaceholder($localFullPath, $name);
                }
            }

            $credits[$name] = [
                'file' => $localFilename,
                'author' => $data['author'],
                'license' => $data['license'],
            ];
        }

        File::put(storage_path('app/public/demo/credits.json'), json_encode($credits, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // 4. Demonstration Series
        $seriesData = [
            [
                'slug' => 'northern-light-portraits',
                'category_id' => $categories['portraits']->id,
                'title_ru' => 'Северный свет',
                'title_en' => 'Northern Light',
                'description_ru' => 'Серия монохромных и приглушённых портретов, снятых в естественном рассеянном свете сибирской осени. Внимание к линиям взгляда и силуэту.',
                'description_en' => 'A series of muted and quiet portraits captured in the soft diffuse light of a Siberian autumn. Focusing on genuine gaze and understated silhouette.',
                'location_ru' => 'Иркутск, исторический центр',
                'location_en' => 'Irkutsk, historic quarter',
                'shooting_date' => 'Сентябрь 2026',
                'cover_image' => 'demo/portrait-1.jpg',
                'is_featured' => true,
                'is_published' => true,
                'is_demo' => true,
                'sort_order' => 1,
                'photos' => [
                    ['image_path' => 'demo/portrait-1.jpg', 'alt_ru' => 'Портрет девушки в мягком осеннем свете', 'alt_en' => 'Portrait of woman in soft autumn daylight', 'width' => 1200, 'height' => 1500],
                    ['image_path' => 'demo/portrait-2.jpg', 'alt_ru' => 'Мужской портрет с естественной текстурой света', 'alt_en' => 'Male portrait with natural light texture', 'width' => 1200, 'height' => 1600],
                    ['image_path' => 'demo/portrait-3.jpg', 'alt_ru' => 'Портретный кадр крупным планом', 'alt_en' => 'Close-up editorial portrait', 'width' => 1200, 'height' => 1500],
                ],
            ],
            [
                'slug' => 'angara-sunset-walk',
                'category_id' => $categories['couples']->id,
                'title_ru' => 'Вечер у Ангары',
                'title_en' => 'Angara River Walk',
                'description_ru' => 'Неспешная прогулка двоих вдоль набережной в предзакатные часы. Чистые текстуры воды, теплый ветер и искренние жесты без постановки.',
                'description_en' => 'A calm late-afternoon walk along the embankment. Clean water reflections, warm air, and unscripted intimacy.',
                'location_ru' => 'Набережная реки Ангары',
                'location_en' => 'Angara river embankment, Irkutsk',
                'shooting_date' => 'Август 2026',
                'cover_image' => 'demo/couple-1.jpg',
                'is_featured' => true,
                'is_published' => true,
                'is_demo' => true,
                'sort_order' => 2,
                'photos' => [
                    ['image_path' => 'demo/couple-1.jpg', 'alt_ru' => 'Пара на фоне заката', 'alt_en' => 'Couple against the sunset light', 'width' => 1200, 'height' => 800],
                    ['image_path' => 'demo/couple-2.jpg', 'alt_ru' => 'Спокойный момент вдвоем', 'alt_en' => 'Quiet moment together', 'width' => 1200, 'height' => 1500],
                    ['image_path' => 'demo/couple-3.jpg', 'alt_ru' => 'Прогулочный кадр', 'alt_en' => 'Embankment stroll frame', 'width' => 1200, 'height' => 800],
                ],
            ],
            [
                'slug' => 'family-quiet-morning',
                'category_id' => $categories['families']->id,
                'title_ru' => 'Утренний чай',
                'title_en' => 'Quiet Family Morning',
                'description_ru' => 'Домашняя серия выходного дня: неспешный завтрак, солнечные блики на столе и живые эмоции детей.',
                'description_en' => 'A cozy weekend story at home: slow breakfast, sun rays dancing across the table, and spontaneous smiles.',
                'location_ru' => 'Иркутск',
                'location_en' => 'Irkutsk',
                'shooting_date' => 'Июль 2026',
                'cover_image' => 'demo/family-1.jpg',
                'is_featured' => true,
                'is_published' => true,
                'is_demo' => true,
                'sort_order' => 3,
                'photos' => [
                    ['image_path' => 'demo/family-1.jpg', 'alt_ru' => 'Семейное теплое утро', 'alt_en' => 'Warm family morning frame', 'width' => 1200, 'height' => 800],
                    ['image_path' => 'demo/family-2.jpg', 'alt_ru' => 'Детали домашнего уюта', 'alt_en' => 'Quiet home moments', 'width' => 1200, 'height' => 1500],
                ],
            ],
            [
                'slug' => 'gallery-chamber-opening',
                'category_id' => $categories['events']->id,
                'title_ru' => 'Открытие в галерее',
                'title_en' => 'Chamber Gallery Night',
                'description_ru' => 'Камерная хроника открытия выставки современного искусства. Живые диалоги, детали экспозиции и погружение в атмосферу события.',
                'description_en' => 'Documentary coverage of a local contemporary art opening. Meaningful conversations, artwork details, and atmosphere.',
                'location_ru' => 'Арт-пространство, Иркутск',
                'location_en' => 'Art Space, Irkutsk',
                'shooting_date' => 'Сентябрь 2026',
                'cover_image' => 'demo/event-1.jpg',
                'is_featured' => false,
                'is_published' => true,
                'is_demo' => true,
                'sort_order' => 4,
                'photos' => [
                    ['image_path' => 'demo/event-1.jpg', 'alt_ru' => 'Гости вечера в галерее', 'alt_en' => 'Guests engaging at art space', 'width' => 1200, 'height' => 800],
                    ['image_path' => 'demo/event-2.jpg', 'alt_ru' => 'Общение и атмосфера события', 'alt_en' => 'Conversations and atmosphere', 'width' => 1200, 'height' => 800],
                ],
            ],
            [
                'slug' => 'pottery-artisan-workshop',
                'category_id' => $categories['business']->id,
                'title_ru' => 'Керамическая мастерская',
                'title_en' => 'Craft Pottery Studio',
                'description_ru' => 'Контентная съемка рабочего процесса локального мастера: глина, гончарный круг, руки мастера и текстуры готовых изделий.',
                'description_en' => 'Visual branding and editorial storytelling for a local ceramics studio: wheel spinning, textured clay, and craft.',
                'location_ru' => 'Мастерская в предместье',
                'location_en' => 'Studio, Irkutsk outskirts',
                'shooting_date' => 'Июнь 2026',
                'cover_image' => 'demo/business-1.jpg',
                'is_featured' => false,
                'is_published' => true,
                'is_demo' => true,
                'sort_order' => 5,
                'photos' => [
                    ['image_path' => 'demo/business-1.jpg', 'alt_ru' => 'Работа за гончарным кругом', 'alt_en' => 'Artisan at work in studio', 'width' => 1200, 'height' => 800],
                    ['image_path' => 'demo/business-2.jpg', 'alt_ru' => 'Детали интерьера мастерской', 'alt_en' => 'Studio workspace textures', 'width' => 1200, 'height' => 800],
                ],
            ],
        ];

        foreach ($seriesData as $s) {
            $photos = $s['photos'];
            unset($s['photos']);

            $series = Series::updateOrCreate(['slug' => $s['slug']], $s);

            $series->photos()->delete();
            $photoOrder = 1;
            foreach ($photos as $p) {
                Photo::create([
                    'series_id' => $series->id,
                    'image_path' => $p['image_path'],
                    'alt_ru' => $p['alt_ru'],
                    'alt_en' => $p['alt_en'],
                    'width' => $p['width'],
                    'height' => $p['height'],
                    'sort_order' => $photoOrder++,
                ]);
            }
        }

        // 5. The 3 Required Packages (with uninvented prices: null = 'Стоимость уточняется')
        $packagesData = [
            [
                'slug' => 'short-session',
                'title_ru' => 'Короткая съёмка',
                'title_en' => 'Short Session',
                'subtitle_ru' => 'Камерная прогулка или экспресс-портрет для одного человека или пары',
                'subtitle_en' => 'A focused walk or express portrait for an individual or couple',
                'duration_ru' => '1 час',
                'duration_en' => '1 hour',
                'photo_count_ru' => 'от 25 готовых кадров в авторской цветокоррекции',
                'photo_count_en' => 'from 25 finished photos with editorial color grading',
                'includes_ru' => "Предварительная консультация и подбор локации\n1 час неспешной съёмки\nАвторская цветокоррекция всех отобранных кадров\nЗакрытая онлайн-галерея для просмотра и скачивания",
                'includes_en' => "Preliminary consultation and location scouting\n1 hour of relaxed shooting\nAuthorial color grading on all selected frames\nPrivate online gallery for viewing and download",
                'delivery_time_ru' => 'Срок готовности уточняется',
                'delivery_time_en' => 'Delivery timeframe upon request',
                'price' => null, // Requirement: real prices are unknown, show 'Стоимость уточняется'
                'is_price_from' => false,
                'extra_conditions_ru' => null,
                'extra_conditions_en' => null,
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'slug' => 'personal-story',
                'title_ru' => 'Индивидуальная история',
                'title_en' => 'Personal Story',
                'subtitle_ru' => 'Полноценная серия для портфолио, пары или семьи с возможностью смены образов и локаций',
                'subtitle_en' => 'Complete visual narrative for portraits, couples, or families with multiple looks and spots',
                'duration_ru' => '2–2.5 часа',
                'duration_en' => '2–2.5 hours',
                'photo_count_ru' => 'от 50 готовых кадров',
                'photo_count_en' => 'from 50 finished photos',
                'includes_ru' => "Разработка концепции, подбор референсов и локаций\nПомощь с образами и комфортной динамикой в кадре\n2–2.5 часа съёмки в городе или на природе\nПолная авторская цветокоррекция серии\nПерсональная галерея с бессрочным доступом",
                'includes_en' => "Concept preparation, visual moodboard, and location plan\nWardrobe guidance and natural posing support\n2–2.5 hours of shooting in town or nature\nConsistent authorial color grading\nPersonal online gallery with permanent access",
                'delivery_time_ru' => 'Срок готовности уточняется',
                'delivery_time_en' => 'Delivery timeframe upon request',
                'price' => null,
                'is_price_from' => false,
                'extra_conditions_ru' => null,
                'extra_conditions_en' => null,
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'slug' => 'event-project',
                'title_ru' => 'Событие / проект',
                'title_en' => 'Event / Project',
                'subtitle_ru' => 'Репортажная съёмка камерных мероприятий, выставок, мастер-классов или визуальный контент для бренда',
                'subtitle_en' => 'Documentary coverage for intimate events, exhibitions, workshops, or brand storytelling',
                'duration_ru' => 'По согласованию (от 3 часов)',
                'duration_en' => 'By arrangement (from 3 hours)',
                'photo_count_ru' => 'Определяется масштабом проекта',
                'photo_count_en' => 'Depends on project scope',
                'includes_ru' => "Обсуждение тайминга и ключевых акцентов репортажа\nВнимательная репортажная хроника людей и деталей\nБазовая коррекция всех удачных кадров серии\nПередача материала через закрытую ссылку для команды и гостей",
                'includes_en' => "Schedule alignment and priority highlights discussion\nUnobtrusive documentary coverage of guests and details\nComplete color correction of all keepers\nDelivery via secure private web link for team and guests",
                'delivery_time_ru' => 'Срок готовности уточняется',
                'delivery_time_en' => 'Delivery timeframe upon request',
                'price' => null,
                'is_price_from' => false,
                'extra_conditions_ru' => null,
                'extra_conditions_en' => null,
                'sort_order' => 3,
                'is_published' => true,
            ],
        ];

        foreach ($packagesData as $pkg) {
            Package::updateOrCreate(['slug' => $pkg['slug']], $pkg);
        }

        // 6. Draft FAQs (remain draft per specification)
        $faqsData = [
            [
                'question_ru' => 'Как забронировать дату съёмки?',
                'question_en' => 'How can I reserve a photoshoot date?',
                'answer_ru' => 'Условия бронирования и предоплаты находятся на этапе утверждения автором и будут опубликованы здесь.',
                'answer_en' => 'Booking and deposit terms are currently being formalized by the photographer and will appear here shortly.',
                'is_draft' => true,
                'sort_order' => 1,
            ],
            [
                'question_ru' => 'Помогаете ли вы с выбором места и подготовкой?',
                'question_en' => 'Do you assist with location selection and styling?',
                'answer_ru' => 'Да, мы заранее обсуждаем общее настроение, подбираем маршрут или студию в Иркутске и согласовываем цветовую гамму одежды.',
                'answer_en' => 'Yes, we discuss mood, scout suitable locations in Irkutsk, and align on comfortable wardrobe styling.',
                'is_draft' => true,
                'sort_order' => 2,
            ],
            [
                'question_ru' => 'В каком виде передаются фотографии?',
                'question_en' => 'In what format are the photographs delivered?',
                'answer_ru' => 'Все готовые кадры загружаются в удобную персональную онлайн-галерею, где их можно просмотреть в высоком разрешении и скачать на телефон или компьютер.',
                'answer_en' => 'All finished photographs are uploaded to a private web gallery with high-resolution viewing and single-click downloading.',
                'is_draft' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($faqsData as $faq) {
            Faq::updateOrCreate(['question_ru' => $faq['question_ru']], $faq);
        }
    }

    private function createFallbackSvgPlaceholder(string $path, string $label): void
    {
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900" viewBox="0 0 1200 900">
  <rect width="1200" height="900" fill="#EAE5DC"/>
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="'Cormorant Garamond', Georgia, serif" font-size="36" fill="#6E6963">
    Roman Yun • Demo Series: {$label}
  </text>
</svg>
SVG;
        File::put($path, $svg);
    }
}
