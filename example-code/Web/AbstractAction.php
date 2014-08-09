<?php
namespace Web;

abstract class AbstractAction
{
    protected $request;
    protected $domain;
    protected $responder;

    protected function response()
    {
        return $this->responder->__invoke();
    }
}
