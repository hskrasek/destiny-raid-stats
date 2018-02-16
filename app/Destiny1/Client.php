<?php namespace App\Destiny1;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $log;

    public function __construct(GuzzleClient $client, LoggerInterface $log)
    {
        $this->client = $client;
        $this->log    = $log;
    }

    /**
     * @param string $endpoint
     *
     * @return array
     * @throws \Exception
     */
    public function get(string $endpoint, $query = []): array
    {
        $response = $this->client->get(Str::endsWith($endpoint, '/') ? $endpoint : $endpoint . '/', [
            'query' => $query
        ]);

        $response = json_decode((string)$response->getBody(), true);

        if (array_get($response, 'ErrorCode') !== 1) {
            $this->log->error('destiny.api.error', [
                'message' => array_get($response, 'Message'),
                'status'  => array_get($response, 'ErrorStatus'),
            ]);

            throw new RuntimeException('There was an error calling the Destiny 1 API.');
        }

        return array_get($response, 'Response', []);
    }

    /**
     * @param string $gamertag
     *
     * @return array
     */
    public function getMembership(string $gamertag): array
    {
        return array_get($this->get('SearchDestinyPlayer/TigerXbox/' . $gamertag), '0', []);
    }

    public function getCharacters(string $membershipId): array
    {
        return array_get($this->get('TigerXbox/Account/' . $membershipId . '/Summary/'), 'data.characters', []);
    }

    public function getRaidCompletions(string $membershipId, string $characterId): array
    {
        return array_get(
            $this->get(
                'Stats/ActivityHistory/TigerXbox/' . $membershipId . '/' . $characterId . '/',
                ['mode' => 4, 'count' => 250]
            ),
            'data.activities',
            []
        );
    }
}
