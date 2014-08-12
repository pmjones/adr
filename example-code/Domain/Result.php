<?php
namespace Domain\Result;

abstract class Result
{
    const STATUS_FOUND          = 'STATUS_FOUND';
    const STATUS_NOT_FOUND      = 'STATUS_NOT FOUND';
    const STATUS_CREATED        = 'STATUS_CREATED';
    const STATUS_NOT_CREATED    = 'STATUS_NOT_CREATED';
    const STATUS_UPDATED        = 'STATUS_UPDATED';
    const STATUS_NOT_UPDATED    = 'STATUS_NOT_UPDATED';
    const STATUS_DELETED        = 'STATUS_DELETED';
    const STATUS_NOT_DELETED    = 'STATUS_NOT_DELETED';
    const STATUS_NOT_VALID      = 'STATUS_NOT_VALID';
    const STATUS_ERROR          = 'STATUS_ERROR';
    const STATUS_NEW_ENTITY     = 'STATUS_NEW_ENTITY';

    protected $status;

    protected $subject;

    protected $info;

    public function __construct($status, $subject = null, $info = null)
    {
        $this->status = $status;
        $this->subject = $subject;
        $this->info = $info;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getInfo()
    {
        return $this->info;
    }
}
