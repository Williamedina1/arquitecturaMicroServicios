<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait ConsumesExternalService
{
    public function performRequest($method, $requestUrl, $formParams = [], $headers = [])
    {
        if (empty($this->baseUri)) {
             // For safety, though the constructor usually checks this.
             return;
        }

        $client = new Client([
            'base_uri' => $this->baseUri,
            'timeout' => 10.0,
        ]);

        if (isset($this->secret)) {
            $headers['Authorization'] = $this->secret;
        }

        $options = ['headers' => $headers];

        if (!empty($formParams)) {
            if (in_array(strtoupper($method), ['GET', 'DELETE'])) {
                $options['query'] = $formParams;
            } else {
                $options['json'] = $formParams;
            }
        }

        try {
            $response = $client->request($method, $requestUrl, $options);

            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                 if (isset($decoded['data']) && is_array($decoded) && count($decoded) === 1) {
                    return $decoded['data'];
                }
                return $decoded;
            }
            return $body;
        } catch (ClientException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
