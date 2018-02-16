<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Completion;
use App\Destiny1\Activity;

Route::get('/', function () {
    /** @var \Illuminate\Support\Collection $completions */
    $completions = Completion::with('character.player')->whereCompleted(true)->get();

    $completions->groupBy(function (Completion $completion) {
        $destiny1Activity = Activity::byBungieId($completion->activity_hash)->first();

        if (null !== $destiny1Activity) {
            if (!empty(data_get($destiny1Activity, 'json.skulls'))) {
                return data_get($destiny1Activity, 'json.activityName') . ' - ' . data_get(
                    $destiny1Activity,
                    'json.skulls.0.displayName'
                );
            }

            return data_get($destiny1Activity, 'json.activityName');
        }

        $destiny2Activity = \App\Destiny2\Activity::byBungieId($completion->activity_hash)->first();

        return $destiny2Activity->id . ' ' . data_get(
            $destiny2Activity,
            'json.displayProperties.name'
        );
    })->dd();
});
