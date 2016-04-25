<?php
require 'app/src/Libs/Uploads.php';

class UploadsTest extends PHPUnit_Extensions_Database_TestCase
{
      
    static private $pdo = null;
   
    private $conn = null;

    protected function setUp()
    {
 
        $conn = $this->getConnection();
   		$conn->getConnection()->query("set foreign_key_checks=0");
   		$conn->getConnection()->query("TRUNCATE TABLE uploads");
   		$conn->getConnection()->query("TRUNCATE TABLE files");
  
    }

    protected function tearDown()
    {
        $conn = $this->getConnection();
   		$conn->getConnection()->query("set foreign_key_checks=0");
   		$conn->getConnection()->query("TRUNCATE TABLE uploads");
   		$conn->getConnection()->query("TRUNCATE TABLE files");
  		
		$files = glob(__DIR__ . '/../uploads/*'); 
		foreach($files as $file){ 
			if(is_file($file)){
		    	unlink($file); 
			}
		}

    }
    
    protected function getConnection()
    {
        if ($this->conn === null) {
        	$db_host = getenv('DB_HOST');
        	$db_name = getenv('DB_NAME');
        	$db_user = getenv('DB_USER');
        	$db_pass = getenv('DB_PASS');
            if (self::$pdo == null) {
                self::$pdo = new PDO("mysql:dbname={$db_name};host={$db_host}", $db_user, $db_pass);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $db_name);
        }
        return $this->conn;
    }

    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet(__DIR__ . '/test_data/db.xml');
    	
    }

    public function testUploadFiles(){

    	$client = new \GuzzleHttp\Client();
    	$upload_url = getenv('APP_URL') . '/upload';
    	$response = $client->request('POST',
    		$upload_url,
    		[
    			'multipart' => [
    				[
    					'name' => 'file[0]',
    					'contents' => fopen(__DIR__ . '/upload_files/electron-soundcloud.png', 'r')
    				]
    			]
    		]
    	);

    	$response_body = json_decode($response->getBody(), true);
    	$upload_url = $response_body['upload_url'];
    	$files = $response_body['files'];

    	$this->assertEquals(200, $response->getStatusCode());
    	$this->assertFileExists(__DIR__ . '/../uploads/' . $files[0]['filename']);

        $files_table_query = $this->getConnection()->createQueryTable(
            'files', 'SELECT original_name, mimetype, upload_id FROM files'
        );
        $files_table_expected = $this->getDataSet()->getTable('files');
        $this->assertTablesEqual($files_table_expected, $files_table_query);

    }


}