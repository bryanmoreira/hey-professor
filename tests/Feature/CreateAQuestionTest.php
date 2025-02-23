<?php

use App\Models\User;

use function Pest\Laravel\{actingAs, assertDatabaseCount, assertDatabaseHas, post};

it('should be able to create a new question bigger than 255 characters', function () {
    // Arrange :: preparar
    $user = User::factory()->create();
    actingAs($user);

    //Act
    $request = post(route('question.store'), [
        'question' => str_repeat('a', 256) . '?',
    ]);

    //Assert
    $request->assertRedirect(route('dashboard'));
    assertDatabaseCount('questions', 1);
    assertDatabaseHas('questions', [
        'question' => str_repeat('a', 256) . '?',
    ]);
});

it('should check if ends with question mark ?', function () {
    // Arrange :: preparar
    $user = User::factory()->create();
    actingAs($user);

    //Act
    $request = post(route('question.store'), [
        'question' => str_repeat('a', 10),
    ]);

    //Assert
    $request->assertSessionHasErrors([
        'question' => 'Are you sure that is a question? It is missing the question mark.',
    ]);
    assertDatabaseCount('questions', 0);
});

it('should have at least 10 characters', function () {
    // Arrange :: preparar
    $user = User::factory()->create();
    actingAs($user);

    //Act
    $request = post(route('question.store'), [
        'question' => str_repeat('a', 8) . '?',
    ]);

    //Assert
    $request->assertSessionHasErrors(['question' => __('validation.min.string', ['min' => 10, 'attribute' => 'question'])]);
    assertDatabaseCount('questions', 0);
});
