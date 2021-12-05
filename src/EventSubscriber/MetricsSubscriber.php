<?php

namespace App\EventSubscriber;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MetricsSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::TERMINATE => 'onKernelTerminate',
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $request->attributes->set('metrics_request_start_time', microtime(true));
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $duration = microtime(true) - $request->attributes->get('metrics_request_start_time');
        $httpStatus = $response->getStatusCode();
        $httpMethod = $request->getMethod();
        $route = $request->attributes->get('_route');

        $collectorRegistry = new CollectorRegistry(new APC());

        $requestCount = $collectorRegistry->getOrRegisterCounter(
            'metrics',
            'request_count',
            'Count of requests handled by the app',
            ['method', 'route', 'http_status']
        );
        $requestCount->inc([$httpMethod, $route, $httpStatus]);

        $latencyHistogram = $collectorRegistry->getOrRegisterHistogram(
            'metrics',
            'latency',
            'Latency of request in nanoseconds',
            ['method', 'route']
        );
        $latencyHistogram->observe($duration, [$httpMethod, $route]);
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (random_int(0, 100) === 1 && $event->getRequest()->attributes->get('_route') !== 'app_metrics_metrics') {
            $event->setResponse(new JsonResponse(['error'=> 'Random error'], 500));
        }
    }
}