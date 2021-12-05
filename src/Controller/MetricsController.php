<?php

namespace App\Controller;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\APC;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController
{
    /**
     * @Route(path="/metrics")
     */
    public function metricsAction(): Response
    {
        $registry = new CollectorRegistry(new APC());
        $renderer = new RenderTextFormat();
        $renderer->render($registry->getMetricFamilySamples());

        return new Response(
            $renderer->render($registry->getMetricFamilySamples()),
            Response::HTTP_OK,
            [
                'Content-Type' => $renderer::MIME_TYPE,
            ]
        );
    }
}