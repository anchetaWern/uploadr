<?php
namespace App\Action;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class FileDownloadAction
{

    public function __invoke(Request $request, Response $response, $args)
    {

        $url = $request->getQueryParams()['url'];

        $db_host = getenv('DB_HOST');
        $db_name = getenv('DB_NAME');
        $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8";
        $pdo = new \Slim\PDO\Database($dsn, getenv('DB_USER'), getenv('DB_PASS'));

        $select_files = $pdo->select(['path', 'original_name'])
                       ->from('files')
                       ->where('url', '=', $url);

        $files_stmt = $select_files->execute();
        $file = $files_stmt->fetch();               

        $file_path = '../uploads/' . $file['path'];
        $new_response = $response
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $file['original_name'] . '"')
            ->withHeader('Expires', 0)
            ->withHeader('Cache-Control', 'must-revalidate')
            ->withHeader('Pragma', 'public')
            ->withHeader('Content-Length', filesize($file_path));
        
        readfile($file_path);
        return $new_response;
    }


}
