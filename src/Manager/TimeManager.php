<?php

namespace Fchris82\TimeTravellerBundle\Manager;

class TimeManager
{
    private bool $timePassing;

    /**
     * We need it because of the timezone AND `clone` is faster than always creating a new DateTime object.
     */
    private ?\DateTime $now = null;

    private ?int $offset = null;

    /**
     * @param bool $timePassing You can control the "passing behaviour". If it is true, time will be passing during the
     *                          process. If you set `2010-01-01T00:00:00` and the running process will take 3 seconds,
     *                          `getNow()` will get back: `2010-01-01T00:00:03`. If it is false it will always get back
     *                          the set value: `2010-01-01T00:00:00`. It depends on what behaviour you need.
     */
    public function __construct(bool $timePassing)
    {
        $this->timePassing = $timePassing;
    }

    public function setNow(\DateTime $now): void
    {
        $this->append($now);
    }

    public function getNow(): \DateTime
    {
        if ($this->isShifted()) {
            return $this->timePassing ?
                (clone $this->now)->setTimestamp(time() + $this->offset) :
                (clone $this->now)
            ;
        }

        return new \DateTime();
    }

    public function getSqlNow(): string
    {
        return $this->getNow()->format('Y-m-d H:i:s');
    }

    public function shiftForward(\DateInterval $offset): void
    {
        $this->append($this->getNow()->add($offset));
    }

    public function shiftBackward(\DateInterval $offset): void
    {
        $this->append($this->getNow()->sub($offset));
    }

    public function modify(string $modifier): void
    {
        $this->append($this->getNow()->modify($modifier));
    }

    public function isShifted(): bool
    {
        return !is_null($this->now);
    }

    private function append(\DateTime $value): void
    {
        $this->offset = $value->getTimestamp() - time();
        $this->now = $value;
    }

    public function isTimePassing(): bool
    {
        return $this->timePassing;
    }

    public function setTimePassing(bool $timePassing): self
    {
        $this->timePassing = $timePassing;

        return $this;
    }
}
