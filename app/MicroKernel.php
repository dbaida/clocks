<?php

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class MicroKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @return array
     */
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
        ];

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    /**
     * @param RouteCollectionBuilder $routes
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->add('/', 'kernel:indexAction', 'index');
        $routes->add('/actual_time', 'kernel:actualTimeAction', 'actual_time');
    }

    /**
     * @param ContainerBuilder $c
     * @param LoaderInterface $loader
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret'     => 'grossum',
            'templating' => [
                'engines' => [
                    'twig'
                ]
            ],
        ]);
    }

    /**
     * @return Response
     * @throws Throwable
     */
    public function indexAction()
    {
        /** @var ContainerBuilder $container */
        $container = $this->container;
        /** @var Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating */
        $templating = $container->get('templating');

        return $templating->renderResponse('index.html.twig', [
            'time' => $this->getActualTime()
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function actualTimeAction()
    {
        return new \Symfony\Component\HttpFoundation\JsonResponse([
            'data' => $this->getActualTime(),
        ]);
    }

    /**
     * @return array
     */
    private function getActualTime()
    {
        $berlinDateTime  = $this->buildDateTime('Europe/Berlin');
        $londonDateTime  = $this->buildDateTime('Europe/London');
        $newYorkDateTime = $this->buildDateTime('America/New_York');
        $kyivDateTime    = $this->buildDateTime('Europe/Kiev');

        return [
            'berlin'  => $this->buildTimeRow($berlinDateTime),
            'london'  => $this->buildTimeRow($londonDateTime),
            'newYork' => $this->buildTimeRow($newYorkDateTime),
            'kyiv'    => $this->buildTimeRow($kyivDateTime),
        ];
    }

    /**
     * @param DateTime $dateTime
     * @return array
     */
    private function buildTimeRow(\DateTime $dateTime)
    {
        return [
            'h' => $dateTime->format('H'),
            'i' => $dateTime->format('i'),
            's' => $dateTime->format('s'),
        ];
    }

    /**
     * @param string $timeZone
     * @return DateTime
     */
    private function buildDateTime($timeZone)
    {
        $dateTimeZone = new \DateTimeZone($timeZone);
        $dateTime = new \DateTime('now', $dateTimeZone);

        return $dateTime;
    }
}