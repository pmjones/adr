<?php
namespace Blog\Responder;

class BlogCreateResponder extends AbstractBlogResponder
{
    protected $payload_method = array(
        'Domain\Payload\Created' => 'created',
        'Domain\Payload\NotCreated' => 'notCreated',
        'Domain\Payload\NotValid' => 'notValid',
    );

    protected function created()
    {
        $blog = $this->payload->get('blog');
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
