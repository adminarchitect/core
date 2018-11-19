<?php

namespace Terranet\Administrator\Field;

use Carbon\Carbon;

class DateTime extends Generic
{
    /** @var string */
    protected $dateFormat = 'M j, Y';

    /** @var string */
    protected $timeFormat = 'g:i A';

    /** @var string */
    protected $dateTimeFormat = 'M j, Y g:i A';

    /**
     * @return array
     */
    public function onIndex(): array
    {
        $format = [
            self::class => $this->dateTimeFormat,
            Date::class => $this->dateFormat,
            Time::class => $this->timeFormat,
        ][\get_class($this)];

        $formattedValue = Carbon::parse($this->value())->format($format);

        return [
            'formatted' => $formattedValue,
        ];
    }

    /**
     * @return array
     */
    public function onView(): array
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
