<?php
namespace Blog\Responder;

class BlogReadResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        return $this->created()
            || $this->responseView('add');
    }

    protected function created()
    {
        if (isset($this->data->blog->id)) {
            $id = $this->data->blog->id;
            $this->response->redirect->created("/blog/read/{$id}");
            return $this->response;
        }
    }
}
