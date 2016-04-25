<?php
namespace App\Action;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \Respect\Validation\Validator as v;
use App\Libs\Uploads as Uploads;

final class UploadAction
{

	private $uploads;

	public function __construct(Uploads $uploads){
		$this->uploads = $uploads;
	}

    public function __invoke(Request $request, Response $response, $args)
    {

		$rand = new \Jamosaur\Randstring\Randstring('sentence', 30);
		$upload_url = $rand->generate();

		$uploads_data = $this->uploads->uploadFiles($upload_url, $_FILES['file']);
		$upload_url = $uploads_data['upload_url'];
		$files = $uploads_data['files'];

		$share_url = getenv('APP_BASE_URL') . '/files?url=' . $upload_url;
		$new_response = $response
			->withHeader('Content-type', 'application/json')
			->withJson([
				'upload_url' => $upload_url,
				'share_url' => $share_url,
				'files' => $files
			]);
		return $new_response;		

    }

}

