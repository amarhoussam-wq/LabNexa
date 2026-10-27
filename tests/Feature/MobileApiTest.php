<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Request_reservation;
use App\Models\Analyse;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_register_and_receive_token()
    {
        $response = $this->postJson('/api/v1/patient/register', [
            'name' => 'Mobile User',
            'email' => 'mobile@test.com',
            'phone' => '0555555555',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['token', 'patient' => ['id', 'name', 'email', 'phone']]);
        
        $this->assertDatabaseHas('patients', ['email' => 'mobile@test.com']);
    }

    public function test_patient_can_login_and_receive_token()
    {
        $patient = Patient::factory()->create([
            'email' => 'login@test.com',
            'password' => bcrypt('secret123')
        ]);

        $response = $this->postJson('/api/v1/patient/login', [
            'email' => 'login@test.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'patient']);
    }

    public function test_patient_can_download_reservation_pdf_stream()
    {
        $patient = Patient::factory()->create();
        Sanctum::actingAs($patient, ['*']);

        $analyse = Analyse::factory()->create([
            'name' => 'Test Analysis'
        ]);

        $request = Request_reservation::factory()->create([
            'patient_id' => $patient->id
        ]);
        $request->analyses()->attach($analyse->id);

        $response = $this->get('/api/v1/patient/reservations/' . $request->id . '/pdf');
        
        $response->assertStatus(200);
        
        // Assert the response is a stream/inline PDF rather than a download attachment
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition') ?? 'inline');
    }
}
