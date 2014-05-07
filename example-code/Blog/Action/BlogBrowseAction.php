<?php
namespace Blog\Action;

use Aura\Web\Request;
use Blog\Domain\BlogService;
use Blog\Responder\BlogBrowseResponder;

class BlogBrowseAction extends AbstractBlogAction
{
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
        $page = $this->request->query->get('page', 1);
        $this->responder->collection = $this->domain->fetchAllByPage($page);
        return $this->response();
    }
}
