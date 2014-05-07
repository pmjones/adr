<?php
namespace Aura\Blog\Responder;

abstract class AbstractBlogResponder
{
    public function notFound($key)
    {
        if (! $this->data->$key) {
            $this->response->set(404);
            return $this->response;
        }
    }
}
