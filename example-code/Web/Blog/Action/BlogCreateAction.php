<?php
namespace Web\Blog\Action;

use Aura\Web\Request;
use Domain\Blog\BlogService;
use Web\Blog\Responder\BlogCreateResponder;

class BlogCreateAction
{
    protected $request;
    protected $domain;
    protected $responder;

    public function __construct(
        Request $request,
        BlogService $domain,
        BlogCreateResponder $responder
    ) {
        $this->request = $request;
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function __invoke()
    {
        $data = $this->request->post->get('blog');
        $payload = $this->domain->create($data);
        $this->responder->setPayload($payload);
        return $this->responder->__invoke();
    }
}
