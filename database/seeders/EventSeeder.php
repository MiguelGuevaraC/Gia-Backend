<?php
namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $events = [
            [
                'name'           => 'Fiesta Electrónica Neon',
                'event_datetime' => '2025-02-10 22:00:00',
                'comment'        => 'Evento especial con DJ internacionales y luces neon.',
                
                'user_id'        => 1,
            ],
            [
                'name'           => 'Noche de Reggaetón',
                'event_datetime' => '2025-03-05 23:00:00',
                'comment'        => 'Reggaetón toda la noche con los mejores DJs locales.',
      
                'user_id'        => 1,
            ],
            [
                'name'           => 'Fiesta Retro 80s',
                'event_datetime' => '2025-03-20 21:00:00',
                'comment'        => 'Revive los 80s con música retro y vestimenta temática.',
    
                'user_id'        => 1,
            ],
            [
                'name'           => 'Salsa y Bachata Night',
                'event_datetime' => '2025-04-15 20:00:00',
                'comment'        => 'Aprende a bailar salsa y bachata con nuestros instructores.',
  
                'user_id'        => 1,
            ],
            [
                'name'           => 'Festival de Música Urbana',
                'event_datetime' => '2025-05-10 22:30:00',
                'comment'        => 'Lo mejor del trap y reggaetón en un solo lugar.',
    
                'user_id'        => 1,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }

}
