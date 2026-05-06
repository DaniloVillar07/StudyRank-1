<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Criando o seu usuário principal (O Ninja)
        User::create([
            'name'     => 'Ninja do Laravel',
            'email'    => 'ninja@studyrank.com',
            'password' => Hash::make('password'), // Sempre use Hash para senhas!
            'nickname' => 'laraninja',
            'xp_total' => 150,
            'level'    => 1,
        ]);

        // Criando usuários competidores para o Ranking
        $competidores = [
            ['name' => 'Ana Dev', 'email' => 'ana@teste.com', 'nickname' => 'anadev'],
            ['name' => 'Bruno Code', 'email' => 'bruno@teste.com', 'nickname' => 'brunocode'],
            ['name' => 'Carla PHP', 'email' => 'carla@teste.com', 'nickname' => 'carlaphp'],
        ];

        foreach ($competidores as $c) {
            User::create([
                'name'     => $c['name'],
                'email'    => $c['email'],
                'password' => Hash::make('password'),
                'nickname' => $c['nickname'],
                'xp_total' => rand(50, 500), // XP aleatório para testar o ranking
                'level'    => 1,
            ]);
        }
    }

    
}