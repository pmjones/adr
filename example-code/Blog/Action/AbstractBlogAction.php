<?php
namespace Blog\Action;

class AbstractBlogAction
{
    protected $request;
    protected $domain;
    protected $responder;

    protected function response()
    {
        return $this->responder->__invoke();
    }
}
