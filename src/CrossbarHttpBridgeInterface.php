<?php

namespace iDutch\CrossbarHttpBridge;

interface CrossbarHttpBridgeInterface
{

    /**
     * @param string $topic
     * @param null $args
     * @param null $kwargs
     * @return array
     */
    public function publish(string $topic, $args = null, $kwargs = null): array;

    /**
     * @param string $procedure
     * @param null $args
     * @param null $kwargs
     * @return array
     */
    public function call(string $procedure, $args = null, $kwargs = null): array;

}