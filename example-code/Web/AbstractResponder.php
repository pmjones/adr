<?php
namespace Web;

use Aura\View\View;
use Aura\Web\Response;
use Aura\Web\Request\Accept;
use Domain\Payload\PayloadInterface;

abstract class AbstractResponder
{
    protected $accept;

    protected $available = array();

    protected $response;

    protected $payload;

    protected $payload_method = array();

    protected $view;

    public function __construct(
        Response $response,
        View $view
    ) {
        $this->response = $response;
        $this->view = $view;
        $this->init();
    }

    protected function init()
    {
        if (! isset($this->payload_method['Domain\Payload\Error'])) {
            $this->payload_method['Domain\Payload\Error'] = 'error';
        }
    }

    public function __invoke()
    {
        $class = get_class($this->payload);
        $method = isset($this->payload_method[$class])
                ? $this->payload_method[$class]
                : 'notRecognized';
        $this->$method();
        return $this->response;
    }

    public function setAccept(Accept $accept)
    {
        $this->accept = $accept;
    }

    public function setPayload(PayloadInterface $payload)
    {
        $this->payload = $payload;
    }

    protected function notRecognized()
    {
        $domain_status = $this->payload->getStatus();
        $this->response->setStatus('500');
        $this->response->setBody("Unknown domain payload status: '$status'");
        return $this->response;
    }

    protected function negotiateMediaType()
    {
        if (! $this->available || ! $this->accept) {
            return true;
        }

        $available = array_keys($this->available);
        $media = $this->accept->media->negotiate($available);
        if (! $media) {
            $this->response->status->set(406);
            $this->response->content->setType('text/plain');
            $this->response->content->set(implode(',', $available));
            return false;
        }

        $this->response->content->setType($media->available->getValue());
        return true;
    }

    protected function renderView($view)
    {
        $content_type = $this->response->content->getType();
        if ($content_type) {
            $view .= $this->available[$content_type];
        }

        $this->view->setView($view);
        $this->view->addData($this->payload->get());
        $this->response->content->set($this->view->__invoke());
    }

    protected function notFound()
    {
        $this->response->status->set(404);
    }

    protected function error()
    {
        $e = $this->payload->getException();
        $this->response->setStatus('500');
        $this->response->setBody($e->getMessage());
    }
}
