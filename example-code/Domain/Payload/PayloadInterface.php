<?php
namespace Domain\Payload;

interface PayloadInterface
{
    public function get($key = null);
}
