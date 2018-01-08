<?php


namespace rollun\barcode\Middleware;


use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\permission\Auth\Middleware\UserResolver;
use Zend\Diactoros\Response;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class MenuInjector
 * @package rollun\barcode\Middleware
 * TODO: remove logic in service
 */
class MenuInjector implements MiddlewareInterface
{
    /** @var UrlHelper */
    protected $urlHelper;

    /** @var array */
    protected $mainMenuRules = [
        "select-parcel" => [
            "title" => "Select parcel",
            "accessForRole" => "guest",
        ],
        "admin-index" => [
            "title" => "Admin home",
            "accessForRole" => "users",
        ],
        "scans-info" => [
            "title" => "Scans Info",
            "accessForRole" => "users",
        ],
        "view-parcels" => [
            "title" => "View Parcels",
            "accessForRole" => "users",
        ],
    ];

    /**
     * MenuInjector constructor.
     * @param UrlHelper $urlHelper
     * @param array $mainMenuRules
     */
    public function __construct(UrlHelper $urlHelper, array $mainMenuRules = [])
    {
        $this->urlHelper = $urlHelper;
        $this->mainMenuRules = array_merge_recursive($this->mainMenuRules, $mainMenuRules);
    }

    /**
     * Build main menu by access options
     * !IMPORTANT Not support inherit role
     * @param $currentRoles
     * @return array[]
     */
    protected function buildMainMenu($currentRoles)
    {
        $mainMenu = [];
        foreach ($this->mainMenuRules as $routeName => $option) {
            if(in_array($option["accessForRole"], $currentRoles)) {
                $mainMenu[] = [
                    'title' => $option['title'],
                    'link' => $this->urlHelper->generate($routeName),
                ];
            }
        }

        return $mainMenu;
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
        $user = $request->getAttribute(UserResolver::KEY_ATTRIBUTE_USER);
        if(!isset($user)) {
            $user = ["roles" => ["guest"]];
        }

        $responseData = [
            "main_menu" => $this->buildMainMenu($user['roles'])
        ];

        //We have priority by merged data
        $responseData = array_merge_recursive($request->getAttribute("responseData", []), $responseData);

        $request = $request->withAttribute('responseData', $responseData);

        $response = $delegate->process($request);
        return $response;
    }
}