<?php

namespace iDutch\CrossbarHttpBridge;

use GuzzleHttp\Client as GuzzleClient;
use iDutch\CrossbarHttpBridge\Exception\RequestException;

/**
 * Class Factory
 * @package iDutch\CrossbarHttpBridge\HttpBridge
 */
class CrossbarHttpBridgeClient implements CrossbarHttpBridgeInterface
{
    const CALL = 'call';
    const PUBLISH = 'publish';

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * @var array
     */
    private $options;

    /**
     * CrossbarHttpBridgeClient constructor.
     * @param GuzzleClient $client
     * @param array $options
     */
    public function __construct(GuzzleClient $client, array $options)
    {
        $this->client = $client;
        $this->options = $options;
    }

    /**
     * @param string $topic
     * @param array|null $args
     * @param array|null $kwargs
     * @return array JSON decoded response
     * @throws \Exception
     */
    public function publish(string $topic, $args = null, $kwargs = null): array
    {
        return $this->request($topic, self::PUBLISH, $args, $kwargs);
    }

    /**
     * @param string $procedure
     * @param array|null $args
     * @param array|null $kwargs
     * @return array JSON decoded response
     * @throws \Exception
     */
    public function call(string $procedure, $args = null, $kwargs = null): array
    {
        return $this->request($procedure, self::CALL, $args, $kwargs);
    }

    /**
     * @param string $topic_procedure
     * @param $type
     * @param $args
     * @param $kwargs
     * @return mixed
     * @throws RequestException
     */
    private function request(string $topic_procedure, string $type, $args, $kwargs): array
    {
        $jsonBody = $this->prepareBody($topic_procedure, $type, $args, $kwargs);
        try {
            $uri = '';
            if (self::PUBLISH === $type) {
                $uri = $this->options['publish_path'];
            } else if (self::CALL === $type) {
                $uri = $this->options['call_path'];
            } else {
                throw new RequestException('Unknows request type: `{$type}`');
            }
            $response = $this->client->post(
                $uri,
                [
                    'json' => $jsonBody,
                    'query' => $this->prepareSignature($jsonBody, $type)
                ]
            );
        } catch (\Exception $e) {
            throw new RequestException($e->getMessage(), 500, $e);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $topic_procedure
     * @param string $type
     * @param array $args
     * @param array $kwargs
     * @return array
     * @throws RequestException
     */
    private function prepareBody(string $topic_procedure, string $type, $args, $kwargs): array
    {
        $body = [];
        if (self::PUBLISH === $type) {
            $body['topic'] = $topic_procedure;
        } else if (self::CALL === $type) {
            $body['procedure'] = $topic_procedure;
        } else {
            throw new RequestException('Unknows request type: `{$type}`');
        }

        if (null !== $args) {
            $body['args'] = $args;
        }
        if (null !== $kwargs) {
            $body['kwargs'] = $kwargs;
        }
        return $body;
    }

    /**
     * @param array $body
     * @param string $type
     * @throws RequestException
     *
     * @return array
     */
    private function prepareSignature(array $body, string $type): array
    {
        $query = [];
        $seq = mt_rand(0, pow(2, 12));
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $timestamp = $now->format("Y-m-d\TH:i:s.u\Z");
        $query['seq'] = $seq;
        $query['timestamp'] = $timestamp;

        $key = null;
        $secret = null;
        if (self::PUBLISH === $type) {
            $key = $this->options['publisher_key'];
            $secret = $this->options['publisher_secret'];
        } else if (self::CALL === $type) {
            $key = $this->options['caller_key'];
            $secret = $this->options['caller_secret'];
        } else {
            throw new RequestException('Unknows request type: `{$type}`');
        }

        if (null !== $key && null !== $secret) {
            $nonce = mt_rand(0, -((int) pow(2, (8 * PHP_INT_SIZE) - 1) + 1));
            $signature = hash_hmac(
                'sha256',
                $key . $timestamp . $seq . $nonce . json_encode($body),
                $secret,
                true
            );
            $query['key'] = $key;
            $query['nonce'] = $nonce;
            $query['signature'] = strtr(base64_encode($signature), '+/', '-_');
        }
        return $query;
    }
}
