<?php

namespace App\Console\Commands;

use App\Character;
use App\Completion;
use App\Destiny1\Client as Destiny1Client;
use App\Destiny2\Client as Destiny2Client;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PullCompletions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destiny:pull-completions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull destiny raid completions';

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
        $characters = Character::with('player')->whereGame('destiny1')->get();

        $characters->each(function (Character $character) {
            collect($this->destiny1Client->getRaidCompletions(
                $character->player->membership_id,
                $character->character_id
            ))->each(function ($activity) use ($character) {
                $character->completions()->save(new Completion([
                    'activity_hash'             => array_get($activity, 'activityDetails.referenceId'),
                    'completed'                 => (bool)array_get($activity, 'values.completed.basic.value'),
                    'assists'                   => array_get($activity, 'values.assists.basic.value'),
                    'deaths'                    => array_get($activity, 'values.deaths.basic.value'),
                    'kills'                     => array_get($activity, 'values.kills.basic.value'),
                    'kills_deaths_ratio'        => array_get($activity, 'values.killsDeathsRatio.basic.value'),
                    'kills_deaths_assists'      => array_get($activity, 'values.killsDeathsAssists.basic.value'),
                    'activity_duration_seconds' => array_get($activity, 'values.activityDurationSeconds.basic.value'),
                    'player_count'              => array_get($activity, 'values.playerCount.basic.value'),
                    'period'                    => Carbon::parse(array_get($activity, 'period'))->toDateTimeString(),
                ]));
            });

            $this->info('Saved ' . $character->completions->count() . ' raid attempts for ' . $character->player->display_name . '\'s character ' . $character->character_id);
        });

        $destiny2Characters = Character::with('player')->whereGame('destiny2')->get();

        $destiny2Characters->each(function (Character $character) {
            collect($this->destiny2Client->getRaidCompletions(
                $character->player->membership_id,
                $character->character_id
            ))->each(function ($activity) use ($character) {
                $character->completions()->save(new Completion([
                    'activity_hash'             => array_get($activity, 'activityDetails.referenceId'),
                    'completed'                 => (bool)array_get($activity, 'values.completed.basic.value'),
                    'assists'                   => array_get($activity, 'values.assists.basic.value'),
                    'deaths'                    => array_get($activity, 'values.deaths.basic.value'),
                    'kills'                     => array_get($activity, 'values.kills.basic.value'),
                    'kills_deaths_ratio'        => array_get($activity, 'values.killsDeathsRatio.basic.value'),
                    'kills_deaths_assists'      => array_get($activity, 'values.killsDeathsAssists.basic.value'),
                    'activity_duration_seconds' => array_get($activity, 'values.activityDurationSeconds.basic.value'),
                    'player_count'              => array_get($activity, 'values.playerCount.basic.value'),
                    'period'                    => Carbon::parse(array_get($activity, 'period'))->toDateTimeString(),
                ]));
            });

            $this->info('Saved ' . $character->completions->count() . ' raid attempts for ' . $character->player->display_name . '\'s character ' . $character->character_id);
        });
    }
}
