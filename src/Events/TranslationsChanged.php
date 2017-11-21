<?php

namespace Terranet\Administrator\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranslationsChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var array
     */
    public $locales;

    public function __construct(array $locales = [])
    {
        $this->locales = $locales;
    }
}