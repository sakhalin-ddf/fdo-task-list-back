<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class JsonRequestTransformer implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    /**
     * @param RequestEvent $event
     *
     * @throws \JsonException
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $request = $event->getRequest();

        // Decode body payload
        $this->decodeBody($request);
    }

    /**
     * @param Request $request
     *
     * @throws \JsonException
     */
    private function decodeBody(Request $request): void
    {
        if ($request->getContentType() === 'json') {
            $json = $request->getContent() ?: '[]';

            $request->request->replace(\json_decode($json, true, 512, JSON_THROW_ON_ERROR));
        }
    }
}
