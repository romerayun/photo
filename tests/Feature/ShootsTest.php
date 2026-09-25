<?php

namespace Tests\Feature;

use App\Models\Shoot;
use App\Models\ShootFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShootsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Роман Юн',
            'email' => 'admin@romanyun.ru',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
    }

    public function test_admin_can_view_shoots_calendar(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.shoots.index'));
        $response->assertStatus(200);
        $response->assertSee('Календарь съёмок');
    }

    public function test_admin_can_create_shoot_with_image_attachments(): void
    {
        Storage::fake('public');

        $imageFile = UploadedFile::fake()->image('moodboard_ref.jpg', 800, 600);

        $payload = [
            'client_name' => 'Анна Смирнова',
            'social_link' => 'https://instagram.com/anna',
            'phone' => '+7 999 111-22-33',
            'description' => 'Нежная портретная съёмка в студии',
            'shoot_date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '14:00',
            'duration_minutes' => 90,
            'status' => 'planned',
            'location' => 'Студия Wood, зал White',
            'price' => 15000,
            'files' => [$imageFile],
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.shoots.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $shoot = Shoot::where('client_name', 'Анна Смирнова')->first();
        $this->assertNotNull($shoot);
        $this->assertNotNull($shoot->share_token);
        $this->assertCount(1, $shoot->files);

        $file = $shoot->files->first();
        $this->assertEquals('moodboard_ref.jpg', $file->original_name);
        $this->assertTrue($file->is_image);
        $this->assertStringStartsWith('/storage/shoots/', $file->url);
        Storage::disk('public')->assertExists($file->file_path);
    }

    public function test_admin_can_upload_additional_file_to_shoot(): void
    {
        Storage::fake('public');

        $shoot = Shoot::create([
            'client_name' => 'Михаил',
            'shoot_date' => now()->toDateString(),
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'status' => 'planned',
        ]);

        $image = UploadedFile::fake()->image('extra_reference.png', 400, 400);

        $response = $this->actingAs($this->admin)->postJson("/admin/shoots/{$shoot->id}/files", [
            'files' => [$image],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertCount(1, $shoot->fresh()->files);
    }

    public function test_client_public_share_page_displays_shoot_and_images(): void
    {
        Storage::fake('public');

        $shoot = Shoot::create([
            'client_name' => 'Екатерина Романова',
            'shoot_date' => now()->addDays(3)->toDateString(),
            'start_time' => '16:00',
            'duration_minutes' => 120,
            'status' => 'planned',
            'location' => 'Парк Горького',
            'description' => 'Уличная съёмка на закате',
        ]);

        $shootFile = ShootFile::create([
            'shoot_id' => $shoot->id,
            'file_path' => 'shoots/' . $shoot->id . '/sample_ref.jpg',
            'original_name' => 'sample_ref.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 204800,
        ]);

        $response = $this->get(route('shoots.share', $shoot->share_token));

        $response->assertStatus(200);
        $response->assertSee('Екатерина Романова');
        $response->assertSee('Парк Горького');
        $response->assertSee('Здесь будет результат съёмки');
        $response->assertSee('sample_ref.jpg');
    }

    public function test_admin_can_update_gallery_link(): void
    {
        $shoot = Shoot::create([
            'client_name' => 'Ольга',
            'shoot_date' => now()->toDateString(),
            'start_time' => '12:00',
            'duration_minutes' => 60,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->patchJson("/admin/shoots/{$shoot->id}/gallery-link", [
            'gallery_link' => 'https://disk.yandex.ru/d/example-gallery-id',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'gallery_link' => 'https://disk.yandex.ru/d/example-gallery-id',
        ]);

        $this->assertEquals('https://disk.yandex.ru/d/example-gallery-id', $shoot->fresh()->gallery_link);
    }

    public function test_client_public_share_page_displays_ready_photos_when_gallery_link_is_set(): void
    {
        $shoot = Shoot::create([
            'client_name' => 'Алина',
            'shoot_date' => now()->toDateString(),
            'start_time' => '14:00',
            'duration_minutes' => 60,
            'status' => 'planned',
            'gallery_link' => 'https://disk.yandex.ru/d/my-ready-photos',
        ]);

        $response = $this->get(route('shoots.share', $shoot->share_token));

        $response->assertStatus(200);
        $response->assertSee('Результат вашей съёмки');
        $response->assertSee('Смотреть и скачать фото');
        $response->assertSee('https://disk.yandex.ru/d/my-ready-photos');
    }

    public function test_client_public_share_page_displays_contract_and_prepayment_block(): void
    {
        $shoot = Shoot::create([
            'client_name' => 'Мария Петрова',
            'shoot_date' => now()->addDays(14)->toDateString(),
            'start_time' => '15:00',
            'duration_minutes' => 90,
            'status' => 'planned',
            'location' => 'Студия Wood',
            'price' => 3500,
            'prepayment' => 1000,
        ]);

        $response = $this->get(route('shoots.share', $shoot->share_token));

        $response->assertStatus(200);
        $response->assertSee('ДОГОВОР И ПРЕДОПЛАТА');
        $response->assertSee('Стоимость съёмки');
        $response->assertSee('3 500 ₽');
        $response->assertSee('Предоплата (бронь)');
        $response->assertSee('1 000 ₽');
        $response->assertSee('Остаток');
        $response->assertSee('2 500 ₽');
        $response->assertSee('в день съёмки');
        $response->assertSee('Для подтверждения бронирования ознакомьтесь с договором и внесите предоплату до');
        $response->assertSee('Открыть и скачать договор');
        $response->assertSee('договором-офертой № ' . $shoot->contract_number);
        $response->assertSee('Внести предоплату 1 000 ₽');
        $response->assertSee('Реквизиты для внесения предоплаты');
        $response->assertSee('Внесение предоплаты означает принятие договора-оферты');
    }

    public function test_client_can_upload_receipt_on_public_page(): void
    {
        Storage::fake('public');

        $shoot = Shoot::create([
            'client_name' => 'Виктория',
            'shoot_date' => now()->addDays(7)->toDateString(),
            'start_time' => '11:00',
            'duration_minutes' => 60,
            'status' => 'planned',
            'price' => 5000,
        ]);

        $receiptImage = UploadedFile::fake()->image('payment_receipt.jpg', 600, 800);

        $response = $this->postJson(route('shoots.share.receipt', $shoot->share_token), [
            'receipt' => $receiptImage,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['receipt_url', 'original_name', 'uploaded_at']);

        $shoot->refresh();
        $this->assertNotNull($shoot->receipt_path);
        $this->assertEquals('payment_receipt.jpg', $shoot->receipt_original_name);
        $this->assertNotNull($shoot->receipt_uploaded_at);
        Storage::disk('public')->assertExists($shoot->receipt_path);
    }

    public function test_client_public_page_shows_receipt_upload_zone(): void
    {
        $shoot = Shoot::create([
            'client_name' => 'Дмитрий',
            'shoot_date' => now()->addDays(10)->toDateString(),
            'start_time' => '13:00',
            'duration_minutes' => 60,
            'status' => 'planned',
        ]);

        $response = $this->get(route('shoots.share', $shoot->share_token));

        $response->assertStatus(200);
        $response->assertSee('Загрузка чека об оплате');
        $response->assertSee('Загрузите скриншот или фото чека');
    }

    public function test_admin_can_confirm_booking(): void
    {
        $admin = User::factory()->create();

        $shoot = Shoot::create([
            'client_name' => 'Алиса',
            'shoot_date' => now()->addDays(5)->toDateString(),
            'start_time' => '14:00',
            'duration_minutes' => 60,
            'status' => 'planned',
            'price' => 4500,
            'receipt_path' => 'shoots/receipts/test.jpg',
            'receipt_original_name' => 'test.jpg',
            'receipt_uploaded_at' => now(),
        ]);

        $this->assertNull($shoot->booking_confirmed_at);

        $response = $this->actingAs($admin)->patchJson(route('admin.shoots.confirm_booking', $shoot));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['booking_confirmed_at', 'shoot']);

        $shoot->refresh();
        $this->assertNotNull($shoot->booking_confirmed_at);
    }

    public function test_client_public_page_displays_confirmed_booking_banner(): void
    {
        $shoot = Shoot::create([
            'client_name' => 'Екатерина',
            'shoot_date' => now()->addDays(6)->toDateString(),
            'start_time' => '15:00',
            'duration_minutes' => 60,
            'status' => 'planned',
            'price' => 6000,
            'receipt_path' => 'shoots/receipts/receipt.jpg',
            'receipt_original_name' => 'receipt.jpg',
            'receipt_uploaded_at' => now()->subDay(),
            'booking_confirmed_at' => now(),
        ]);

        $response = $this->get(route('shoots.share', $shoot->share_token));

        $response->assertStatus(200);
        $response->assertSee('Бронирование подтверждено!');
        $response->assertSee('Предоплата успешно получена');
        $response->assertSee('bookingConfirmed: true', false);
    }
}
