<?php
namespace App\Libs;
use \Respect\Validation\Validator as v;

class Uploads {

	private $db;

	public function __construct(){

		$db_host = getenv('DB_HOST');
		$db_name = getenv('DB_NAME');
		$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8";
		$pdo = new \Slim\PDO\Database($dsn, getenv('DB_USER'), getenv('DB_PASS'));
		
		$this->db = $pdo;

	}

	public function getFiles($url){

		$select_files = $this->db->select(['files.url', 'files.original_name'])
                       ->from('files');
        $select_files->join('uploads', 'files.upload_id', '=', 'uploads.id');
        $select_files->where('uploads.url', '=', $url);

        $files_stmt = $select_files->execute();
        $files = $files_stmt->fetchAll();
        return $files;

	}

	public function saveUpload($upload_url){
		$uploads_insert = $this->db->insert(['url'])
		    ->into('uploads')
		    ->values([$upload_url]);
		$uploads_insert->execute(true);
		$upload_id = $this->db->lastInsertId();
		return $upload_id;
	}

	public function saveFile($file_url, $filename, $original_name, $mimetype, $upload_id){
		$files_insert = $this->db->insert([
			'url', 'path', 'original_name', 'mimetype', 'upload_id'
			])->into('files')
		    ->values([$file_url, $filename, $original_name, $mimetype, $upload_id]);
		$files_insert->execute(true);
		$file_id = $this->db->lastInsertId();
		return $file_id;
	}


	public function uploadFiles($upload_url, $files){
		
		$upload_id = $this->saveUpload($upload_url);

		$uploaded_files = [];

		foreach ($files['error'] as $key => $error) {
		    if ($error == UPLOAD_ERR_OK) {
		        $tmp_name = $files['tmp_name'][$key];
		        $filename = uniqid();
		        $original_name = filter_var($files['name'][$key], FILTER_SANITIZE_STRING);
		        $valid_size = v::size(null, '2MB')->validate($tmp_name);
		        
				$valid_mimetype = v::oneOf(
					v::mimetype('image/png'),
					v::mimetype('image/jpeg'),
					v::mimetype('image/gif'),
					v::mimetype('image/svg+xml')
				)->validate($tmp_name);

				$finfo = new \finfo(FILEINFO_MIME_TYPE);
				$mimetype = $finfo->file($tmp_name);

		        if($valid_size && $valid_mimetype){
		        	move_uploaded_file($tmp_name, "../uploads/{$filename}");
			        
			        $file_url = uniqid();
			        $uploaded_files[] = [
			        	'filename' => $filename,
			        	'original_name' => $original_name,
			        	'mimetype' => $mimetype,
			        	'upload_id' => $upload_id
			        ];
			        $this->saveFile($file_url, $filename, $original_name, $mimetype, $upload_id);
			        
		        }
		    }
		}

		$response_data = [
			'upload_url' => $upload_url,
			'files' => $uploaded_files
		];

		return $response_data;

	}
}