<?php
namespace Blog\Responder;

class BlogReadResponder extends AbstractBlogResponder
{
    public function __invoke()
    {
        if (! $this->created()) {
            $this->renderView('add');
        }

        return $this->response;
    }

    protected function created()
    {
        if (isset($this->data->blog->id)) {
            $id = $this->data->blog->id;
            $this->response->redirect->created("/blog/read/{$id}");
            return true;
        }

        return false;
    }
}
