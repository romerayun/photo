<?php

namespace Tests\Feature;

use App\Mail\ContactFormSubmitted;
use App\Models\Package;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormAndDirectCommunicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        Setting::set('telegram', '@romerayun');
        Setting::set('phone', '+7 (914) 808-95-55');
        Setting::set('contact_email', 'romerayun@gmail.com');
    }

    public function test_navbar_and_footer_contain_direct_contact_buttons(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Direct Telegram button in navbar
        $response->assertSee('https://t.me/romerayun', false);
        $response->assertSee('Telegram', false);

        // Real response time indicated in footer
        $response->assertSee('1–2 часа (09:00–21:00)', false);
    }

    public function test_contacts_page_does_not_contain_admin_instructions(): void
    {
        // Even when contacts are empty, public visitors shouldn't see instructions to log into admin
        Setting::set('telegram', '');
        Setting::set('phone', '');

        $response = $this->get('/contacts');
        $response->assertStatus(200);

        $response->assertDontSee('панели управления');
        $response->assertDontSee('авторизуйтесь в административной панели');
        $response->assertDontSee('admin.login');
    }

    public function test_package_is_passed_to_contacts_page_and_telegram(): void
    {
        $pkg = Package::where('is_published', true)->first();
        $this->assertNotNull($pkg);

        $pkgTitle = $pkg->title_ru;

        $response = $this->get('/contacts?package=' . urlencode($pkgTitle));
        $response->assertStatus(200);

        // Banner and preselection in form
        $response->assertSee('Выбранный формат съёмки');
        $response->assertSee($pkgTitle);
        $response->assertSee('selected', false);

        // Telegram link with prefilled text
        $encodedTitle = urlencode($pkgTitle);
        $response->assertSee($encodedTitle, false);
    }

    public function test_pricing_page_cta_links_pass_package_name(): void
    {
        $response = $this->get('/pricing');
        $response->assertStatus(200);

        $pkg = Package::where('is_published', true)->first();
        $this->assertNotNull($pkg);

        $response->assertSee('contacts?package=' . rawurlencode($pkg->title_ru), false);
    }

    public function test_contacts_page_states_real_response_time(): void
    {
        $response = $this->get('/contacts');
        $response->assertStatus(200);

        $response->assertSee('Срок ответа: в течение 1–2 часов');
        $response->assertSee('09:00 до 21:00');
    }

    public function test_contact_form_sends_email_successfully(): void
    {
        Mail::fake();

        $payload = [
            'name' => 'Александр Тестовый',
            'email' => 'alex@example.com',
            'phone' => '+7 (999) 111-22-33',
            'telegram' => '@alex_test',
            'package' => 'Индивидуальный портрет',
            'message' => 'Хочу провести портретную съёмку в студии в субботу.',
        ];

        $response = $this->post('/contacts', $payload);

        $response->assertRedirect('/contacts?package=' . rawurlencode('Индивидуальный портрет'));
        $response->assertSessionHas('contact_success');

        Mail::assertSent(ContactFormSubmitted::class, function ($mail) use ($payload) {
            return $mail->hasTo('romerayun@gmail.com') &&
                   $mail->data['name'] === $payload['name'] &&
                   $mail->data['email'] === $payload['email'] &&
                   $mail->data['package'] === $payload['package'];
        });
    }

    public function test_contact_form_honeypot_drops_spam_without_sending_email(): void
    {
        Mail::fake();

        $payload = [
            'name' => 'Spam Bot',
            'email' => 'bot@spam.com',
            'message' => 'Buy cheap viagra now',
            '_hp' => 'I am a robot filling hidden fields',
        ];

        $response = $this->post('/contacts', $payload);

        $response->assertRedirect('/contacts');
        Mail::assertNothingSent();
    }

    public function test_contact_form_validates_required_fields(): void
    {
        Mail::fake();

        $response = $this->post('/contacts', [
            'name' => '',
            'email' => 'invalid-email',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
        $response->assertSessionDoesntHaveErrors(['message']);
        Mail::assertNothingSent();
    }

    public function test_contact_form_supports_custom_contact_method_and_mask(): void
    {
        Mail::fake();

        $payload = [
            'name' => 'Елена Фото',
            'contact_method' => 'telegram',
            'contact_value' => '@elena_photo',
            'package' => 'Индивидуальный портрет',
            'message' => 'Добрый день, хочу забронировать фотосессию.',
        ];

        $response = $this->post('/contacts', $payload);

        $response->assertRedirect('/contacts?package=' . rawurlencode('Индивидуальный портрет'));
        $response->assertSessionHas('contact_success');

        Mail::assertSent(ContactFormSubmitted::class, function ($mail) use ($payload) {
            return $mail->hasTo('romerayun@gmail.com') &&
                   $mail->data['name'] === $payload['name'] &&
                   $mail->data['contact_method'] === 'telegram' &&
                   $mail->data['contact_value'] === '@elena_photo';
        });
    }
}
