<?php
/**
 * This file is part of the 2015_02_InShower
 * Copyright (c) 2015
 *
 * @file    ApplicationManager.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Managers;

use Nette\Object;
use Nette\Utils\DateTime;
use Nette\Utils\Validators;

class ApplicationManager extends Object
{
    const PERIOD_FROM = 'from';
    const PERIOD_TO   = 'to';
    const PERIODS     = 4;

    /** @var mixed DI settings */
    private $contain;


    function __construct($contain)
    {
        $this->contain = $contain;
    }

    /**
     * @param        $num
     * @param string $period from|to
     *
     * @return null
     */
    public function getSettingDateForWeek($num, $period)
    {
        return isset($this->contain["week$num"][$period])
            ? $this->contain["week$num"][$period]
            : null;
    }


    public function isAllowedWeek($period)
    {
        if (Validators::isInRange($period, array(1, self::PERIODS))) {
            $from = DateTime::from($this->getSettingDateForWeek($period, self::PERIOD_FROM));
            $to   = DateTime::from($this->getSettingDateForWeek(self::PERIODS, self::PERIOD_TO));
            $now  = DateTime::from('now');
            if ($now > $from && $now < $to) {
                return true;
            }
        }
        return false;
    }


    public function getAllowedWeeks()
    {
        $result = 0;
        foreach (range(1, self::PERIODS) as $period) {
            if ($this->isAllowedWeek($period)) {
                $result++;
            }
        }
        return $result;
    }

}