<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
            KernelEvents::EXCEPTION => 'onKernelException',
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

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];

        $response = new JsonResponse(['message' => $exception->getMessage()], $status, $headers);
        $this->addDebugHeaders($exception, $response);

        $event->setResponse($response);
    }

    /**
     * @param \Throwable   $exception
     * @param JsonResponse $response
     */
    private function addDebugHeaders(\Throwable $exception, JsonResponse $response): void
    {
        while ($exception->getPrevious() !== null) {
            $exception = $exception->getPrevious();
        }

        $file = $exception->getFile();
        $line = $exception->getLine();

        foreach ($exception->getTrace() as $item) {
            /*
             * Skip trace without file and line
             */
            if (isset($item['file'], $item['line']) === false) {
                continue;
            }

            /*
             * Skip trace with file not from "src" directory
             */
            if (\mb_strpos($item['file'], SRC_DIR) !== 0) {
                continue;
            }

            $file = $item['file'];
            $line = $item['line'];

            break;
        }

        $response->headers->set('X-Error-File', \str_replace(APP_DIR, '', $file));
        $response->headers->set('X-Error-Line', (string) $line);
    }
}
