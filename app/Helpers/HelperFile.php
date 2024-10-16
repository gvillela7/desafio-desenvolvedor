<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class HelperFile
{
    public function __construct()
    {
    }

    /**
     * @throws GuzzleException
     */
    public static function getFile($upload): ResponseInterface
    {
        $client = new Client();
        $uploadArray = json_decode($upload, true);
        $path = $uploadArray['path'];
        return $client->get($path, ['sink' => public_path('storage/test.csv')]);
    }
}
