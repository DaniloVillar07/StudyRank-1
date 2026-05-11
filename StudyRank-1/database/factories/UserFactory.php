<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * UserFactory atualizada com todos os campos da migration:
 * nickname, xp_total, level, current_streak, last_activity_date.
 *
 * Uso nos testes:
 *   User::factory()->create()                      → usuário padrão
 *   User::factory()->create(['xp_total' => 150])   → usuário com XP específico
 *   User::factory()->streak(5)->create()           → com streak de 5 dias
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'               => fake()->name(),
            // Gera um nickname único sem espaços (ex: "user_a3f2b1")
            'nickname'           => 'user_' . Str::random(6),
            'email'              => fake()->unique()->safeEmail(),
            'email_verified_at'  => now(),
            'password'           => static::$password ??= Hash::make('password'),
            'remember_token'     => Str::random(10),
            // Campos de gamificação — valores padrão de um usuário novo
            'xp_total'           => 0,
            'level'              => 1,
            'current_streak'     => 0,
            'last_activity_date' => null,
            'avatar'             => null,
        ];
    }

    /**
     * State: usuário sem email verificado.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * State: usuário com streak ativo (estudou ontem).
     * Útil para testar incremento de streak nos testes.
     *
     * Uso: User::factory()->withStreak(3)->create()
     */
    public function withStreak(int $days = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'current_streak'     => $days,
            'last_activity_date' => now()->subDay()->toDateString(),
        ]);
    }

    /**
     * State: usuário com XP e nível específicos.
     * Útil para testar badges de XP.
     *
     * Uso: User::factory()->withXp(200)->create()
     */
    public function withXp(int $xp): static
    {
        return $this->state(fn (array $attributes) => [
            'xp_total' => $xp,
            'level'    => (int) floor($xp / 100) + 1,
        ]);
    }
}
