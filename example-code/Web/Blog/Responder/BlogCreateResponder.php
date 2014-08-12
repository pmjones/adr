<?php
namespace Blog\Responder;

use Domain\Result;

class BlogCreateResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        Result::STATUS_CREATED => 'created',
        Result::STATUS_NOT_CREATED => 'notCreated',
        Result::STATUS_NOT_VALID => 'notValid',
        Result::STATUS_ERROR => 'error',
    );

    protected function created($result)
    {
        $subject = $this->result->getSubject();
        $this->response->redirect->created("/blog/read/{$subject->id}");
    }

    protected function notValid($result)
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
