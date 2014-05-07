<?php
namespace Blog\Action;

use Aura\Web\Request;
use Blog\Domain\BlogService;
use Blog\Responder\BlogAddResponder;

class BlogAddAction extends AbstractBlogAction
{
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
        return $this->response();
    }
}
