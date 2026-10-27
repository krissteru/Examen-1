it('crea un equipo y responde 201 con el encabezado Location', function () {
    $categoria = Categoria::factory()->create();
    $randomCode = 'EQ-' . rand(1000, 9999);

    $response = $this->postJson('/api/equipos', [
        'nombre' => 'Laptop Lenovo',
        'codigo' => $randomCode,
        'categoria_id' => $categoria->id,
        'estado' => 'disponible',
    ]);

    // Imprime el error exacto en la terminal si no da 201
    if ($response->status() !== 201) {
        dump($response->json());
    }

    $response->assertStatus(201)
             ->assertHeader('Location');
});