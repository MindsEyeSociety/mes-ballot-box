<?php

namespace MESBallotBox\Propel;

use MESBallotBox\Propel\Base\Ballot as BaseBallot;

/**
 * Skeleton subclass for representing a row from the 'Ballot' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Ballot extends BaseBallot
{
    private $timezonesNice = Array(
        1 => 'Eastern',
        2 => 'Central',
        3 => 'Mountain',
        4 => 'Western',
        5 => 'Alaska',
        6 => 'Hawaii'
    );
    private $timezonesPHP = Array(
        1 => 'America/New_York',
        2 => 'America/Chicago',
        3 => 'America/Denver',
        4 => 'America/Los_Angeles',
        5 => 'America/Anchorage',
        6 => 'Pacific/Honolulu'
    );
    
    public function setStartDate($start){
        $timezone = new \DateTimeZone($this->getTimezonePHP());
        $startTime = \DateTime::createFromFormat('Y-m-d\TH:i',$start,$timezone);
        //error_log(var_export(Array('start' => $start, 'timezone' => $timezone, 'startTime' => $startTime),true),3,'/home/ubuntu/mes-ballot-box/date.log');
        //exit;
        $this->setStartTime($startTime->format('U'));
        
    }
    
    public function getStartDate($format='l, F jS Y h:i A'){
        $timezone = new \DateTimeZone($this->getTimezonePHP());
        $startTime = new \DateTime(false,$timezone);
        $startTime->setTimestamp($this->getStarttime());
        return $startTime->format($format);
    }
    
    /*public function getStartArray(){
        $parts = explode('-',$this->getStartDate('Y-n-d-H-i-s'));
        foreach($parts as &$part){
            $part = (int)$part;
        }
        unset($part);
        $parts[1] -= 1;
        return $parts;
    }
    
    public function getEndArray(){
        $parts = explode('-',$this->getEndDate('Y-n-d-H-i-s'));
        foreach($parts as &$part){
            $part = (int)$part;
        }
        unset($part);
        $parts[1] -= 1;
        return $parts;
    }*/
    
    public function setEndDate($end){
        $timezone = new \DateTimeZone($this->getTimezonePHP());
        $endTime = \DateTime::createFromFormat('Y-m-d\TH:i',$end,$timezone);
        $this->setEndTime($endTime->format('U'));
    }
    
    public function getEndDate($format='l, F jS Y h:i A'){
        $timezone = new \DateTimeZone($this->getTimezonePHP());
        $endTime = new \DateTime(false,$timezone);
        $endTime->setTimestamp($this->getEndtime());
        return $endTime->format($format);
    }

    public function getTimezoneNice(){
        return $this->timezonesNice[(int)$this->getTimezone()];
    }
    
    function getTimezonePHP(){
        return $this->timezonesPHP[$this->getTimezone()];
    }
}
