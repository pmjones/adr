<?php
namespace Web\Blog\Responder;

class BlogCreateResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        'Domain\Result\Created' => 'created',
        'Domain\Result\NotCreated' => 'notCreated',
        'Domain\Result\NotValid' => 'notValid',
    );

    protected function created()
    {
        $blog = $this->result->get('blog');
        $this->response->redirect->created("/blog/read/{$blog->id}");
    }

    protected function notValid()
    {
        $this->response->setStatus('422');
        $this->renderView('add');
    }

    protected function notCreated()
    {
        $this->response->setStatus('500');
        $this->renderView('add');
    }
}
