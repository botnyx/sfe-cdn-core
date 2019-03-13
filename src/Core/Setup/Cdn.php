<?php

namespace Botnyx\Sfe\Cdn\Core\Setup;



class Cdn{

  function __construct( $vendorDir){
		$this->vendorDir = $vendorDir;
		$this->projectDir = realpath($vendorDir . '/..');

		$this->installedComponents = json_decode(\Botnyx\Sfe\Backend\Core\Setup\Backend::readfile($vendorDir."/composer/installed.json"));

	}

  static function postInstall(Event $event){

		$vendorDir = realpath($event->getComposer()->getConfig()->get('vendor-dir'));


		$installer = new \Botnyx\Sfe\Cdn\Core\Setup\Cdn($vendorDir);
		$installer->setup();


	}

  static function readfile ($filename){
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		return $contents;
	}

  /*
		This is the main database setup.
	*/
	public function setup (){
		echo "\n";
		echo "Setting up: `sfe-cdn-base`\n";
		//echo "version: `sfe-cdn-base`\n";
		//$installed[0]->version_normalized;




		#var_dump("xyz");
		echo "-----------------------------------------\n";

		$searchDir = realpath($this->vendorDir . '/../..');
		if( file_exists($searchDir."/configuration.ini") ){
			echo "found a previous `configuration.ini` ( ".$searchDir."/configuration.ini"." ) \n";

			$app = $this->readConfiguration($searchDir."/configuration.ini" );
			$coreComponent = $this->getComponentVersion($this->installedComponents);

			#echo "\nsettings\n";
			#print_r($app->settings['conn']);


			/* create the $this->pdo instance */
			$this->createPDO($app->settings['conn']);


			#echo "\npaths\n";
			#print_r($app->paths);
			#echo "\ncoreComponent\n";
			#print_r($coreComponent);

			#echo "need to add config-parse code here..\n";
			////////////////////////////////////////////////////
			$this->createdb($this->pdo);

			//echo "\n\nFINISHED!!\n\n";

		}else{
			echo "\n No configuration found, starting setup.\n";
			$public_html = $this->public_html();
			$tempFolder = $this->tempFolder();

			/* create the $this->pdo instance from requested credentials */
			$dbCredentials = $this->dbCredentials();

			////////////////////////////////////////////////////
			$this->pdo;
			echo "\n\nUNFINISHED!!\n\n";



		}
		echo "\n";
	}




      private function getComponentVersion($installedComponents){
  		foreach($installedComponents as $c){
  			if($c->name=="botnyx/sfe-backend-core"){
  				return (object)array("name"=>$c->name,
  							 "version"=>$c->version,
  							 "version_normalized"=>$c->version_normalized
  							);
  			}
  		}

  	}

  	private function readConfiguration($configfile){
  		$app = new \Botnyx\Sfe\Shared\Application(parse_ini_file($configfile, true));
  		return $app;
  	}



    	private function tempFolder(){
    		echo "---------------------\n";
    		echo "Configuration step 2.\n";
    		echo "Please enter the location of your /tmp directory \n";
    		$input = rtrim(fgets(STDIN));

    		if( !file_exists( $input )){
    			echo "Error: this location doesnt exist. try again.\n\n\n";
    			$this->tempFolder();
    		}
    		return $input;
    	}


}
