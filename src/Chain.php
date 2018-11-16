<?php

namespace Terranet\Administrator;

class Chain
{
    /**
     * Create chain of responsibility.
     *
     * @param array $queue
     */
    public static function make(array $queue = [])
    {
        foreach ($queue as $i => &$instance) {
            if ($next = array_get($queue, $i + 1)) {
                $instance->setNext($next);
            }
        }

        return array_first($queue);
    }
}