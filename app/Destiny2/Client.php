<?php namespace App\Destiny2;

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
            'query' => $query,
        ]);

        $response = json_decode((string)$response->getBody(), true);

        if (array_get($response, 'ErrorCode') !== 1) {
            $this->log->error('destiny.api.error', [
                'message' => array_get($response, 'Message'),
                'status'  => array_get($response, 'ErrorStatus'),
            ]);

            throw new RuntimeException('There was an error calling the Destiny 2 API.');
        }

        return array_get($response, 'Response', []);
    }

    public function getCharacters(string $membershipId): array
    {
        return array_get(
            $this->get('TigerXbox/Profile/' . $membershipId . '/', ['components' => 'Characters']),
            'characters.data',
            []
        );
    }

    public function getRaidCompletions(string $membershipId, string $characterId): array
    {
        return array_get(
            $this->get(
                'TigerXbox/Account/' . $membershipId . '/Character/' . $characterId . '/Stats/Activities/',
                ['mode' => 4]
            ),
            'activities',
            []
        );
    }
}
