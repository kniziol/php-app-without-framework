<?php
declare(strict_types=1);

namespace Kni;

use Psr\Http\Message\ResponseInterface;

/**
 * HelloWorld
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class HelloWorld
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Class constructor
     *
     * @param string            $name
     * @param ResponseInterface $response
     */
    public function __construct(string $name, ResponseInterface $response)
    {
        $this->name = $name;
        $this->response = $response;
    }

    public function __invoke(): ResponseInterface
    {
        $template = '<html><head></head><body>Hello %s!</body></html>';

        $response = $this
            ->response
            ->withHeader('Content-Type', 'text/html');

        $response
            ->getBody()
            ->write(sprintf($template, $this->name));

        return $response;
    }
}
