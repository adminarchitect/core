<?php

namespace Terranet\Administrator;

use Illuminate\Support\Arr;

class Chain
{
    /**
     * Create chain of responsibility.
     *
     * @param  array  $queue
     * @return mixed
     */
    public static function make(array $queue = [])
    {
        foreach ($queue as $i => &$instance) {
            if ($next = Arr::get($queue, $i + 1)) {
                $instance->setNext($next);
            }
        }

        return Arr::first($queue);
    }
}
