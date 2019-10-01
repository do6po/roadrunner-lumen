<?php

namespace Eplightning\RoadRunnerLumen\Extensions;

use Eplightning\RoadRunnerLumen\Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\RoadRunner\PSR7Client;
use Throwable;

class ExtensionStack implements ExtensionInterface
{
    /**
     * @var ExtensionInterface[]
     */
    protected $extensions;

    /**
     * ExtensionStack constructor.
     *
     * @param ExtensionInterface[] $extensions
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * @param Application $application
     * @param Config $config
     */
    public function init(Application $application, Config $config): void
    {
        foreach ($this->extensions as $extension) {
            $extension->init($application, $config);
        }
    }

    /**
     * @param Application $application
     * @param PSR7Client $client
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function handleRequest(Application $application, PSR7Client $client, ServerRequestInterface $request): bool
    {
        foreach ($this->extensions as $extension) {
            if ($extension->handleRequest($application, $client, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Application $application
     * @param ServerRequestInterface $request
     */
    public function beforeRequest(Application $application, ServerRequestInterface $request): void
    {
        foreach ($this->extensions as $extension) {
            $extension->beforeRequest($application, $request);
        }
    }

    /**
     * @param Application $application
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function afterRequest(
        Application $application,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): void {
        foreach ($this->extensions as $extension) {
            $extension->afterRequest($application, $request, $response);
        }
    }

    /**
     * @param Application $application
     * @param Request $request
     */
    public function beforeHandle(Application $application, Request $request): void
    {
        foreach ($this->extensions as $extension) {
            $extension->beforeHandle($application, $request);
        }
    }

    /**
     * @param Application $application
     * @param Request $request
     * @param Response $response
     */
    public function afterHandle(Application $application, Request $request, Response $response): void
    {
        foreach ($this->extensions as $extension) {
            $extension->afterHandle($application, $request, $response);
        }
    }

    /**
     * @param Application $application
     * @return mixed
     */
    public function beforeLoop(Application $application): void
    {
        foreach ($this->extensions as $extension) {
            $extension->beforeLoop($application);
        }
    }

    /**
     * @param Application $application
     * @return mixed
     */
    public function afterLoop(Application $application): void
    {
        foreach ($this->extensions as $extension) {
            $extension->afterLoop($application);
        }
    }

    /**
     * @param Application $application
     * @param ServerRequestInterface $request
     * @param Throwable $e
     * @return null|ResponseInterface|Throwable
     */
    public function error(Application $application, ServerRequestInterface $request, Throwable $e)
    {
        $result = null;

        foreach ($this->extensions as $extension) {
            $newResult = $extension->error($application, $request, $e);

            if (!is_null($newResult)) {
                $result = $newResult;
            }
        }

        return $result;
    }
}
