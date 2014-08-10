<?php
namespace Web\Blog\Action;

use Aura\Web\Request;
use Domain\Blog\BlogService;
use Web\Blog\Responder\BlogAddResponder;

class BlogAddAction
{
    protected $request;
    protected $domain;
    protected $responder;

    public function __construct(
        Request $request,
        BlogService $domain,
        BlogAddResponder $responder
    ) {
        $this->request = $request;
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function __invoke($id)
    {
        $data = $this->request->post->get('blog');
        $this->responder->blog = $this->domain->create($data);
        return $this->responder->__invoke();
    }
}
