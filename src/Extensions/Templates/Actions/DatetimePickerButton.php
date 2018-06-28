<?php

namespace BotMan\Drivers\Line\Extensions\Templates\Actions;

class DatetimePickerButton extends AbstractButton
{
    /** @var string */
    protected $mode = '';

    /** @var string */
    protected $initial = '';

    /** @var string */
    protected $start = '';

    /** @var string */
    protected $end = '';

    /**
     * Set the datetime picker action mode.
     *
     * @param string $mode
     * @return $this
     */
    public function mode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Set the initial value of date or time.
     *
     * @param string $initial
     * @return $this
     */
    public function initial($initial)
    {
        $this->initial = $initial;

        return $this;
    }

    /**
     * Set a time interval with specific start and end.
     *
     * @param string $start
     * @return $this
     */
    public function range($start, $end)
    {
        $this->start = $start;
        $this->end = $end;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'datetimepicker',
            'label' => $this->label,
            'data' => $this->value,
            'mode' => $this->mode,
            'initial' => $this->initial,
            'min' => $this->start,
            'max' => $this->end,
        ];
    }
}
