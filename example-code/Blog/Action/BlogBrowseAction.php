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
        BlogBrowseResponder $responder
    ) {
        $this->request = $request;
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function __invoke()
    {
        $page = $this->request->query->get('page', 1);
        $this->responder->collection = $this->domain->fetchAllByPage($page);
        $this->responser->acceptable = $this->request->accept->media->get();
        return $this->response();
    }
}
