<?php
/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Action\HomePageAction::class, 'home');
 * $app->post('/album', App\Action\AlbumCreateAction::class, 'album.create');
 * $app->put('/album/:id', App\Action\AlbumUpdateAction::class, 'album.put');
 * $app->patch('/album/:id', App\Action\AlbumUpdateAction::class, 'album.patch');
 * $app->delete('/album/:id', App\Action\AlbumDeleteAction::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Action\ContactAction::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Action\ContactAction::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Action\ContactAction::class,
 *     Zend\Expressive\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

/** @var \Zend\Expressive\Application $app */

$app->get('/', App\Action\HomePageAction::class, 'home');
$app->get('/api/ping', App\Action\PingAction::class, 'api.ping');

if ($container->has('api-datastore')) {
    $app->route(
        '/api/datastore[/{resourceName}[/{id}]]',
        'api-datastore',
        ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
        'api-datastore');
}
$app->route(
    '/barcode',
    "select-parcel-service",
    ["GET"],
    'select-parcel'
);
$app->route(
    '/barcode/{parcel_number}',
    "search-barcode-service",
    ["GET"],
    'search-barcode'
);

//admin
// scans-info-service
// delete-parcel-service
// edit-parcel-service
// add-parcel-service
// view-parcels-service

$app->route(
    '/admin/scans_info',
    "scans-info-service",
    ["GET"],
    'scans-info'
);

$app->route(
    '/admin/parcels',
    "view-parcels-service",
    ["GET"],
    'view-parcels'
);
$app->route(
    '/admin/parcels/add',
    "add-parcel-service",
    ["GET"],
    'add-parcel'
);
$app->route(
    '/admin/parcels/{parcel_number}/delete',
    "delete-parcel-service",
    ["GET"],
    'delete-parcel'
);
$app->route(
    '/admin/parcels/{parcel_number}/edit',
    "edit-parcel-service",
    ["GET"],
    'edit-parcel'
);