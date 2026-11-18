<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_raiz_de_la_api_devuelve_un_indice_json(): void
    {
        $this->getJson('/api')
            ->assertOk()
            ->assertJsonPath('message', 'La Buena Mesa API')
            ->assertJsonPath('endpoints.menu_items', '/api/menu-items');
    }

    public function test_lista_los_elementos_del_menu(): void
    {
        MenuItem::factory()->count(3)->create();

        $this->getJson('/api/menu-items')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_muestra_un_elemento(): void
    {
        $item = MenuItem::factory()->create();

        $this->getJson("/api/menu-items/{$item->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $item->id);
    }

    public function test_devuelve_404_json_si_no_existe(): void
    {
        $this->getJson('/api/menu-items/999')
            ->assertNotFound()
            ->assertJson(['message' => 'Recurso no encontrado.']);
    }

    public function test_crea_un_elemento(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/api/menu-items', [
            'category_id' => $category->id,
            'name' => 'Ceviche nikkei',
            'price' => 12.5,
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Ceviche nikkei')
            ->assertJsonPath('data.price', 12.5);

        $this->assertDatabaseHas('menu_items', ['name' => 'Ceviche nikkei']);
    }

    public function test_valida_datos_al_crear(): void
    {
        $this->postJson('/api/menu-items', ['price' => -5])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category_id', 'name', 'price']);
    }

    public function test_patch_actualiza_parcialmente(): void
    {
        $item = MenuItem::factory()->create(['price' => 10]);

        $this->patchJson("/api/menu-items/{$item->id}", ['is_available' => false])
            ->assertOk()
            ->assertJsonPath('data.is_available', false)
            ->assertJsonPath('data.price', 10);
    }

    public function test_put_exige_todos_los_campos_obligatorios(): void
    {
        $item = MenuItem::factory()->create();

        $this->putJson("/api/menu-items/{$item->id}", ['price' => 9])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category_id', 'name']);
    }

    public function test_elimina_un_elemento(): void
    {
        $item = MenuItem::factory()->create();

        $this->deleteJson("/api/menu-items/{$item->id}")->assertNoContent();
        $this->assertDatabaseMissing('menu_items', ['id' => $item->id]);
    }

    public function test_filtra_por_categoria(): void
    {
        $postres = Category::factory()->create(['name' => 'Postres', 'slug' => 'postres']);
        MenuItem::factory()->count(2)->create(['category_id' => $postres->id]);
        MenuItem::factory()->create();

        $this->getJson('/api/menu-items/category/postres')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/menu-items/category/inexistente')->assertNotFound();
    }

    public function test_filtra_por_categoria_usando_id_o_nombre_visible(): void
    {
        $postres = Category::factory()->create(['name' => 'Postres', 'slug' => 'postres']);
        MenuItem::factory()->count(2)->create(['category_id' => $postres->id]);

        $this->getJson("/api/menu-items/category/{$postres->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/menu-items/category/Postres')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_filtra_por_disponibilidad(): void
    {
        MenuItem::factory()->create();
        MenuItem::factory()->unavailable()->create();

        $this->getJson('/api/menu-items?available=1')->assertJsonCount(1, 'data');
    }
}
