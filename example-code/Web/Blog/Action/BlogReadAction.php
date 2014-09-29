<?php
namespace Web\Blog\Action;

use Aura\Web\Request;
use Domain\Blog\BlogService;
use Web\Blog\Responder\BlogReadResponder;

class BlogReadAction
{
    protected $request;
    protected $domain;
    protected $responder;

    public function __construct(
        Request $request,
        BlogService $domain,
        BlogReadResponder $responder
    ) {
        $this->request = $request;
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function __invoke($id)
    {
        $payload = $this->domain->fetchPost($id);
        $this->responder->setPayload($payload);
        return $this->responder->__invoke();
    }
}
