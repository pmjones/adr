<?php
namespace Blog\Responder;

use Domain\Result;

class BlogCreateResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        'Domain\Result\Created' => 'created',
        'Domain\Result\NotCreated' => 'notCreated',
        'Domain\Result\NotValid' => 'notValid',
    );

    protected function created()
    {
        $subject = $this->result->getSubject();
        $this->response->redirect->created("/blog/read/{$subject->id}");
    }

    protected function notValid()
    {
        $this->response->setStatus('422');
        $this->renderView('add', array(
            'blog' => $this->result->getSubject(),
            'messages' => $this->result->getInfo(),
        ));
    }

    protected function notCreated()
    {
        $this->response->setStatus('500');
        $this->renderView('add', array(
            'blog' => $this->result->getSubject(),
            'messages' => $this->result->getInfo(),
        ));
    }
}
