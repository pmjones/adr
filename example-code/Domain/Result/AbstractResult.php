<?php
namespace Domain\Result;

abstract class AbstractResult
{
    protected $subject;

    public function __construct($subject = null)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}
