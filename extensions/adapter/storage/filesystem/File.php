<?php

namespace li3_filesystem\extensions\adapter\storage\filesystem;

/**
 * Lithium Filesystem: managing file uploads the easy way
 *
 * @copyright     Copyright 2012, Little Boy Genius (http://www.littleboygenius.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\core\Libraries;

/**
 * A File Filesystem adapter implementation. Requires
 * writable folder on filesystem for example webroot\uploads
 *
 * The `File` filesystem adapter is meant to be used through the `FileSystem` interface,
 * which abstracts away file writting, adapter instantiation and filter
 * implementation.
 *
 * A simple configuration of this adapter can be accomplished in `config/bootstrap/filesystem.php`
 * as follows:
 *
 * {{{
 * FileSystem::config(array(
 *     'filesystem-config-name' => array(
 *         'adapter' => 'File',
 *         'path' => '/webroot/img',
 *     )
 * ));
 * }}}
 */

class File extends \lithium\core\Object {

	/**
	 * Class constructor.
	 *
	 * @see app\extensions\storage\FileSystem::config()
	 * @param array $config Configuration parameters for this filesystem adapter. These settings are
	 *        indexed by name and queryable through `FileSystem::config('name')`.
	 *        The defaults are:
	 *        - 'path' : Path where uploaded files live `LITHIUM_APP_PATH . '/webroot/uploads'`.
	 */
	public function __construct(array $config = array()) {
		$defaults = array(
			'path' => Libraries::get(true, 'path') . '/webroot/uploads'
		);
		parent::__construct($config + $defaults);
	}

	/**
     * @param string $filename
     * @param string $data
     * @param array $options
     * @return mixed returns filename or false otherwise.
     */
	public function write($filename, $data, array $options = array()) {

		$path = $this->_config['path'];

		return function($self, $params) use (&$path) {

			$data = $params['data'];
			$path = "{$path}/{$params['filename']}";
			$filename = basename($path);

			$subpath = str_ireplace("/{$filename}", "", $path); // allow user to specify subpaths

			// Create new directory if required
			if (!file_exists($subpath)) {
				@mkdir($subpath, 0777, true);
				@chmod($subpath, 0777);
			}

			if(is_object($data) || is_array($data)){ // if a file upload object, array is passed we move a file
				if($this->_upload($data, $path)){
					return $params['filename'];
				}
			}

			if (file_put_contents($path, $data)) {
				return $params['filename'];
			}

			return false;
		};
	}

	private function _upload($data, $path){
		return move_uploaded_file($data->tmp_name, $path);
	}

	/**
     * @param string $filename
     * @return string|boolean
     */
	public function read($filename) {
		$path = $this->_config['path'];

		return function($self, $params) use (&$path) {
			$path = "{$path}/{$params['filename']}";

			if (file_exists($path)) {
				return file_get_contents($path);
			}

			return false;
		};
	}

	/**
     * @param string $filename
     * @return boolean
     */
	public function delete($filename) {
		$path = $this->_config['path'];
		return function($self, $params) use (&$path) {
			$path = "{$path}/{$params['filename']}";
			if(is_dir($path)){
				rmdir($path);
			}
			if (file_exists($path)) {
				return unlink($path);
			}

			return false;
		};
	}

	/**
	 * @param string $filename
	 * @return boolean
	 */
	public function exists($filename) {
		$path = $this->_config['path'];
		return function($self, $params) use (&$path) {
			$path = "{$path}/{$params['filename']}";

			clearstatcache();
			return file_exists($path);
		};
	}


}



?>