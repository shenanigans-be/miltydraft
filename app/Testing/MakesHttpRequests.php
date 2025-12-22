<?php

namespace App\Testing;

trait MakesHttpRequests
{
    protected function call(string $method, string $url, array $data = []): TestResponse
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request($method, $url);

        return new TestResponse($response);
    }

    public function get($url): TestResponse
    {
        return $this->call('GET', $url);
    }

    public function post($url, $data): TestResponse
    {
        return $this->call('GET', $url, $data);
    }
}