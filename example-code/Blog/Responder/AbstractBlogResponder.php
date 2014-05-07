<?php
namespace Aura\Blog\Responder;

abstract class AbstractBlogResponder
{
    protected function notFound($key)
    {
        if (! $this->data->$key) {
            $this->response->status->set(404);
            return $this->response;
        }
    }
}
