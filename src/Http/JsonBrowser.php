<?php

namespace GoPay\Http;

use GoPay\Http\Log\Logger;
use GuzzleHttp\Message\Request as GuzzleReq;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Stream\Stream;

class JsonBrowser
{
    private $logger;
    private $timeout;

    public function __construct(Logger $l, $timeoutInSeconds)
    {
        $this->logger = $l;
        $this->timeout = $timeoutInSeconds;
    }

    public function send(Request $r)
    {
        try {
            $client = new GuzzleClient();
			$guzzRequest = $client->request($r->method, $r->url, [
				'body' => $r->body,
				'headers' => $r->headers
			]);
            $response = new Response((string) $guzzRequest->getBody());
            $response->statusCode = (string) $guzzRequest->getStatusCode();
            $response->json = json_decode((string) $response, true);
        } catch (\Exception $e) {
            $response = new Response($e->getMessage());
            $response->statusCode = 500;
        }
        $this->logger->logHttpCommunication($r, $response);
        return $response;
    }
}
