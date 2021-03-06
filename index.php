<?php
    use SBLib\Handlers\Pages\ErrorsPage;
    use SBLib\Handlers\AbstractPage;

    use SBLib\Utilities\MISC;

    require('./inc/globals.php');

    /** @var object $rootDirectory */
    if((!file_exists($rootDirectory->server . 'install.lock')
        || !file_exists($rootDirectory->server . 'config/.htaccess'))
        && filter_input(INPUT_GET,'page', FILTER_SANITIZE_STRING) != 'installer') {
        header("Location: " . $rootDirectory->client . 'installer'); exit;
    }

    $page   = ucwords(filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING)) . 'Page';
    $params = filter_input(INPUT_GET, 'params', FILTER_SANITIZE_STRING);

    if($page === 'Page') {
        header("Location: " . $rootDirectory->clientFull . 'portal');
        exit;
    }

    if(class_exists($page)) {
        $pageHandler = new $page($params);
    } else if(class_exists('SBLib\Handlers\Pages\\' . $page)) {
        $className = 'SBLib\Handlers\Pages\\' . $page;
        $pageHandler = new $className($params);
    } else {
        http_response_code(404);
        $pageHandler = new ErrorsPage(404, $page);
    }

    if(!$pageHandler instanceof AbstractPage) {
        echo $page . ' does not extend SBLib\Handlers\AbstractPage'; exit;
    }

    // TODO: Implement a decent plugin functionality.
    // Let's handle the plugins directory, and register them to the $pageHandler object.
    $pluginsDirectory = MISC::findDirectory('plugins');
    foreach(glob($pluginsDirectory . '/*.json') as $jsonConfig) {
        $config = json_decode(file_get_contents($jsonConfig));
        foreach($config->classes as $class) {
            require_once($pluginsDirectory . $config->name . '/' . $class);
        }

        $pageHandler->registerPlugin($config);
    }