<?php
namespace Domain\Result;

abstract class AbstractResult implements ResultInterface
{
    protected $result = array();

    public function __construct(array $result)
    {
        $this->result = $result;
    }

    public function get($key = null)
    {
        if ($key === null) {
            return $this->result;
        }

        if (isset($this->result[$key])) {
            return $this->result[$key];
        }

        return null;
    }
}
