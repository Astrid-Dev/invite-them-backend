<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Helpers\FunctionsHelper;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->create([
            'pseudo' => 'admin27',
            'email' => 'letopo.dev@gmail.com',
            'password' => Hash::make('SP@2713')
        ]);

        $event = Event::query()
            ->create([
                'name' => 'Mariage de Simon & Prisca',
                'code' => FunctionsHelper::generateCode(table: 'events', field: 'code'),
                'date' => '2024-07-27',
                'user_id' => $user->id
            ]);

        $event->scanners()->create([
            'user_id' => $user->id
        ]);

        $this->call([
            ILovePdfApiKeySeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
