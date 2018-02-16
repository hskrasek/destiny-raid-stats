<?php

namespace App\Console\Commands;

use App\Character;
use App\Destiny1\Client as Destiny1Client;
use App\Destiny2\Client as Destiny2Client;
use App\Player;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PullCharacters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destiny:pull-characters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls down all characters for both Destiny 1 and Destiny 2';

    /**
     * @var Destiny1Client
     */
    private $destiny1Client;

    /**
     * @var Destiny2Client
     */
    private $destiny2Client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Destiny1Client $destiny1Client, Destiny2Client $destiny2Client)
    {
        parent::__construct();
        $this->destiny1Client = $destiny1Client;
        $this->destiny2Client = $destiny2Client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Grabbing everyone\'s Destiny 1 characters');
        $players      = Player::all();
        $d1Characters = collect();
        $d2Characters = collect();

        $players->each(function (Player $player) use ($d1Characters, $d2Characters) {
            $characters = $this->destiny1Client->getCharacters($player->membership_id);

            foreach ($characters as $character) {
                $d1Characters->push($player->characters()->firstOrCreate([
                    'character_id'    => array_get($character, 'characterBase.characterId'),
                    'game'            => 'destiny1',
                    'emblem_path'     => array_get($character, 'emblemPath'),
                    'background_path' => array_get($character, 'backgroundPath'),
                    'last_played_at'  => Carbon::parse(array_get($character, 'characterBase.dateLastPlayed'))
                        ->toDateTimeString(),
                ]));
            }

            $characters = $this->destiny2Client->getCharacters($player->membership_id);

            foreach ($characters as $character) {
                $d2Characters->push($player->characters()->firstOrCreate([
                    'character_id'    => array_get($character, 'characterId'),
                    'game'            => 'destiny2',
                    'emblem_path'     => array_get($character, 'emblemPath'),
                    'background_path' => array_get($character, 'emblemBackgroundPath'),
                    'last_played_at'  => Carbon::parse(array_get($character, 'dateLastPlayed'))
                        ->toDateTimeString(),
                ]));
            }
        });

        $this->info('Saved the following characters:');
        $this->table(
            ['id', 'player', 'character_id', 'game', 'last_played_at'],
            $d1Characters->merge($d2Characters)->map(function (Character $character) {
                return [
                    $character->id,
                    $character->player->display_name,
                    $character->character_id,
                    $character->game,
                    $character->last_played_at,
                ];
            })
        );
    }
}
