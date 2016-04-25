<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Libs\Uploads as Uploads;

final class FileListAction
{
    private $view;
    private $uploads;

    public function __construct(Twig $view, Uploads $uploads)
    {
        $this->view = $view;
        $this->uploads = $uploads;
    }

    public function __invoke(Request $request, Response $response, $args)
    {   

        $url = $request->getQueryParams()['url'];
        $files = $this->uploads->getFiles($url);

        $page_data = [
            'app_name' => getenv('APP_NAME'),
            'url' => $url,
            'files' => $files
        ];
        
        $this->view->render($response, 'files.twig', $page_data);
        return $response;
    }


}
