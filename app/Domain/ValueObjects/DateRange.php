<?php

namespace App\Domain\ValueObjects;

class DateRange
{
    private \DateTime $startDate;
    private \DateTime $endDate;

    public function __construct(\DateTime $startDate, \DateTime $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    public function contains(\DateTime $date): bool
    {
        return $date >= $this->startDate && $date <= $this->endDate;
    }
}
