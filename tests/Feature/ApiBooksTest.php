<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBooksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_get_all_books()
    {
        // Crear 4 libros.
        $books = Book::factory(4)->create();

        // Realizar petición a la ruta index
        $response = $this->getJson(route('books.index'));

        // Validamos la obtención de los libros
        $response->assertJsonFragment([
            'title' => $books[0]->title,
        ]);
        $response->assertJsonFragment([
            'title' => $books[1]->title,
        ]);
    }

    /** @test */
    function can_get_one_book()
    {
        // Crear un libro.
        $book = Book::factory()->create();

        // Realizar la petición Get a la ruta con el Id del libro.
        $response = $this->getJson(route('books.show', $book->id));

        // Test para validar que la respuesta coincide con el título del libro creado.
        $response->assertJsonFragment([
            'title' => $book->title,
        ]);
    }

    /** @test */
    function can_create_books()
    {
        // Test de validación para el campo title, required
        $this->postJson(route('books.store'), [])
            ->assertJsonValidationErrorFor('title');

        // Test para crear un libro en la DB.
        $this->postJson(route('books.store'), [
            'title' => 'mi nuevo libro',
        ])->assertJsonFragment([
            'title' => 'mi nuevo libro',
        ]);

        // Test para asegurar que se registró en la DB el título del libro.
        $this->assertDatabaseHas('books', [
            'title' => 'mi nuevo libro',
        ]);
    }

    /** @test */
    function can_update_book()
    {
        // Creamo un libro
        $book = Book::factory()->create();

        // Test de validación para el campo title, required
        $this->patchJson(route('books.update', $book->id), [])
            ->assertJsonValidationErrorFor('title');

        // Realizamos la petición Patch para actualizar el título.
        $this->patchJson(route('books.update', $book->id), [
            'title' => $book->title . ' editado',
        ])->assertJsonFragment([
            'title' => $book->title . ' editado',
        ]);

        // Test para validar que en la tabla Books existe un registro con el título editado.
        $this->assertDatabaseHas('books', [
            'title' => $book->title . ' editado',
        ]);
    }

    /** @test */
    function can_destroy_book()
    {
        // Creamos un libro para,
        $book = Book::factory()->create();

        // Realizamos la petición a la ruta para eliminar el libro creado.
        $this->deleteJson(route('books.destroy', $book->id))
            ->assertNoContent();

        // Test para validar que no existe el título del libro creado en la DB.
        $this->assertDatabaseMissing('books', [
            'title' => $book->title,
        ]);
    }
}