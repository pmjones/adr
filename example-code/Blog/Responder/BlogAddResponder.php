<?php
namespace Blog\Responder;

class BlogReadResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        if (isset($this->data->blog->id)) {
            $id = $this->data->blog->id;
            $this->response->redirect->created("/blog/read/{$id}");
            return $this->response;
        }

        return $this->responseView('add');
    }
}
