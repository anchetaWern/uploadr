<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class HomeAction
{
    private $view;
    private $uploads;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, $args)
    {

        $page_data = [
            'app_name' => getenv('APP_NAME')
        ];
        
        $this->view->render($response, 'home.twig', $page_data);
        return $response;
    }


}
