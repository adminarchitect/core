<?php

namespace Terranet\Administrator\Field;

use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Terranet\Administrator\Scaffolding;

class DateTime extends Generic
{
    /** @var string */
    protected $dateFormat = 'M j, Y';

    /** @var string */
    protected $timeFormat = 'g:i A';

    /** @var string */
    protected $dateTimeFormat = 'M j, Y g:i A';

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function onIndex()
    {
        $format = [
            self::class => $this->dateTimeFormat,
            Date::class => $this->dateFormat,
            Time::class => $this->timeFormat,
        ][get_class($this)];

        $formattedValue = Carbon::parse($this->value())->format($format);

        return [
            'formatted' => $formattedValue,
        ];
    }

    /**
     * @return mixed|string
     */
    public function onView()
    {
        return $this->onIndex();
    }

    /**
     * @param string $format
     *
     * @return self
     */
    public function setDateTimeFormat(string $format): self
    {
        $this->$dateTimeFormat = $format;

        return $this;
    }

    /**
     * @param string $dateFormat
     *
     * @return self
     */
    public function setDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * @param string $timeFormat
     *
     * @return self
     */
    public function setTimeFormat(string $timeFormat): self
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }
}
