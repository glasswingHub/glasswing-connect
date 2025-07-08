<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Importer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ImportRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_importer_access_cannot_access_records()
    {
        $user = User::factory()->create();
        $importer = Importer::factory()->create();

        $response = $this->actingAs($user)
            ->get("/imports/{$importer->id}/records");

        $response->assertStatus(403);
        $response->assertSee('No tienes permisos para acceder a este importador');
    }

    public function test_user_with_importer_access_can_access_records()
    {
        $user = User::factory()->create();
        $importer = Importer::factory()->create();

        // Asociar el usuario al importador
        DB::table('importer_user')->insert([
            'user_id' => $user->id,
            'importer_id' => $importer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get("/imports/{$importer->id}/records");

        $response->assertStatus(200);
    }

    public function test_user_without_importer_access_cannot_access_show_record()
    {
        $user = User::factory()->create();
        $importer = Importer::factory()->create();

        $response = $this->actingAs($user)
            ->get("/imports/{$importer->id}/records/1");

        $response->assertStatus(403);
        $response->assertSee('No tienes permisos para acceder a este importador');
    }

    public function test_user_with_importer_access_can_access_show_record()
    {
        $user = User::factory()->create();
        $importer = Importer::factory()->create();

        // Asociar el usuario al importador
        DB::table('importer_user')->insert([
            'user_id' => $user->id,
            'importer_id' => $importer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get("/imports/{$importer->id}/records/1");

        $response->assertStatus(200);
    }

    public function test_user_without_importer_access_cannot_import_record()
    {
        $user = User::factory()->create();
        $importer = Importer::factory()->create();

        $response = $this->actingAs($user)
            ->post("/imports/{$importer->id}/records/1/import");

        $response->assertStatus(403);
        $response->assertSee('No tienes permisos para acceder a este importador');
    }

    public function test_user_with_importer_access_can_import_record()
    {
        $user = User::factory()->create();
        $importer = Importer::factory()->create();

        // Asociar el usuario al importador
        DB::table('importer_user')->insert([
            'user_id' => $user->id,
            'importer_id' => $importer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post("/imports/{$importer->id}/records/1/import");

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_any_record_endpoint()
    {
        $importer = Importer::factory()->create();

        $response = $this->get("/imports/{$importer->id}/records");
        $response->assertRedirect('/login');

        $response = $this->get("/imports/{$importer->id}/records/1");
        $response->assertRedirect('/login');

        $response = $this->post("/imports/{$importer->id}/records/1/import");
        $response->assertRedirect('/login');
    }
} 