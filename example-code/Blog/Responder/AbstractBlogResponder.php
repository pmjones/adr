<?php
namespace Blog\Responder;

use AbstractResponder;

abstract class AbstractBlogResponder extends AbstractResponder
{
    protected function notFound($key)
    {
        if (! $this->data->$key) {
            $this->response->status->set(404);
            return $this->response;
        }
    }
}
