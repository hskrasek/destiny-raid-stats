<?php

namespace App\Console\Commands;

use App\Destiny1\Client;
use App\Player;
use Illuminate\Console\Command;

class PullPlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destiny:pull-players';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull all destiny players';

    /**
     * @var Client
     */
    private $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        collect([
            'HEATSEEKERBUNGE',
            'Oml3t',
            'Tunavi',
            'ToviTTCSNoname',
            'Rev3rb',
            'Tawpgun',
            'Evasive Ebu',
            'VAMMMPY',
            'shnateman',
            'Awful Waffle96',
            'bigdsdeath79',
            'Mr BlueberryJam',
        ])->each(function ($gamertag) {
            $player = $this->client->getMembership($gamertag);
            Player::firstOrCreate([
                'membership_id' => $player['membershipId'],
                'display_name'  => $player['displayName'],
            ]);
        });
    }
}
