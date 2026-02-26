protected $routeMiddleware = [
    // ...
    'creator.approved' => \App\Http\Middleware\CreatorApproved::class,
];
