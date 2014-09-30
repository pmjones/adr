<?php
namespace Web\Blog\Action;

use Aura\Web\Request;
use Domain\Blog\BlogService;
use Web\Blog\Responder\BlogUpdateResponder;

class BlogUpdateAction
{
    protected $request;
    protected $domain;
    protected $responder;

    public function __construct(
        Request $request,
        BlogService $domain,
        BlogUpdateResponder $responder
    ) {
        $this->request = $request;
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function __invoke($id)
    {
        $data = $this->request->post->get('blog');
        $payload = $this->domain->update($id, $data);
        $this->responder->setPayload($payload);
        return $this->responder->__invoke();
    }
}
