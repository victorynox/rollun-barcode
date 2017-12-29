<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.05.17
 * Time: 17:44
 */

namespace rollun\barcode\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use Xiag\Rql\Parser\Query;

class StatsBarcodeAction implements MiddlewareInterface
{
    /**
     * @var DataStoresInterface
     */
    protected $scanBarcode;

    /**
     * StatsBarcodeAction constructor.
     * @param DataStoresInterface $scanBarcode
     */
    public function __construct(DataStoresInterface $scanBarcode)
    {
        $this->scanBarcode = $scanBarcode;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $result = $this->scanBarcode->query(new Query());

        $request = $request->withAttribute('responseData', ['data' => $result]);

        $response = $delegate->process($request);
        return $response;
    }
}
