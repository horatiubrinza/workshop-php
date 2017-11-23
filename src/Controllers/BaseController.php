<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

/**
 * The application base controller.
 */
abstract class BaseController
{
    /**
     * Builds a response with rendered content via Twig.
     *
     * @param Application $app
     * @param string      $template
     * @param array       $parameters
     *
     * @return Response
     */
    protected function render(Application $app, string $template, array $parameters = []): Response
    {
        return new Response($app['twig']->render($template, $parameters));
    }
}
