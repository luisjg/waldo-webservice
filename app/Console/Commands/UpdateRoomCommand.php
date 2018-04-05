<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateRoomCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'update:room {room_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the room cache';


    public function handle()
    {
        $room = $this->argument('room_id');
        $formattedRoom = formatRoomCollection($room);
        if ($formattedRoom) {
            $this->info('The following '.$room.' data has been added to the cache.');
        }
    }
}