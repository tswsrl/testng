<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

define('TOTAL_PAGES', 10);
define('RESULTS_PER_PAGE', 5);

$app = new Application();

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views'
]);

$lexer = new Twig_Lexer($app['twig'], [
    'tag_variable' => ['{@', '@}']
]);

$app['twig']->setLexer($lexer);

$app->get('/', function (Silex\Application $app) {
    return $app['twig']->render('index.twig');
});

$app->get('/products/{name}/{page}', function (Silex\Application $app, $name, $page) {
    
    $page = max([1, intval($page)]) - 1;
    $products = [];

    if($page < TOTAL_PAGES){
    
    for($i = 1; $i <= RESULTS_PER_PAGE; $i++){
        $index = $i + $page * RESULTS_PER_PAGE;
        $products[] = [
            'name' => sprintf('Product %s %d', $name, $index),
            'description' => sprintf('Product %d description', $index)
        ];
    }

        return $app->json([
            'products' => $products,
            'pages' => TOTAL_PAGES,
            'total' => TOTAL_PAGES * RESULTS_PER_PAGE
        ]);

    } else {
        return $app->json([
            'message' => 'Page not found'
        ], 404);
    }

});

$app->run();
