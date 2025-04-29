<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    private KernelInterface $kernel;

    public function __construct(Environment $twig, KernelInterface $kernel)
    {
        $this->twig = $twig;
        $this->kernel = $kernel;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;

        $showMessage = $this->kernel->isDebug(); // true en dev/test, false en prod

        $html = $this->twig->render('error/error.html.twig', [
            'code' => $statusCode,
            'message' => $showMessage ? $exception->getMessage() : "Une erreur est apparue"
        ]);

        $event->setResponse(new Response($html, $statusCode));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
