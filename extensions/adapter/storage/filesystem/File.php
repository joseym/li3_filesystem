<?php

/**
 * li3 filesystem: managing files the easy way
 * @copyright     Modifications by Hans Donner
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 *
 * Original:
 * Lithium Filesystem: managing file uploads the easy way
 * @copyright     Copyright 2012, Little Boy Genius (http://www.littleboygenius.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */



namespace li3_filesystem\extensions\adapter\storage\filesystem;

use li3_filesystem\extensions\storage\FileSystemException;
use li3_filesystem\extensions\storage\FileSystemAdapater;
use lithium\core\Libraries;

/**
 * A regulare File Filesystem adapter implementation. Requires a writable path on filesystem,
 * for example storage\uploads.
 *
 * A simple configuration of this adapter can be accomplished like:
 *
 * {{{
 * FileSystem::config(array(
 *     'filesystem-config-name' => array(
 *         'adapter' => 'File',
 *         'path' => 'some/path/to/the/files/to/store',
 *     )
 * ));
 * }}}
 *
 * @see \li3_filesystem\extensions\storage\FileSystem
 * @see \li3_filesystem\extensions\storage\FileSystemAdapter
 */

class File extends \lithium\core\Object implements FileSystemAdapater {

	/**
	 * Class constructor.
	 *
	 * @see app\extensions\storage\FileSystem::config()
	 * @param array $config Configuration parameters.
	 *        - `path`: parent directory where to store the file
	 *          Defaults to `LITHIUM_APP_PATH . '/resources/tmp/files'`.
	 *        - `mkdir`: `false` if path may not be created if needed, else
	 *                   an array with mkdir options (default):
	 *                   - `mode`: `550`
	 *                   - `recursive`: `true`
	 *                   - `writeReturns`: what `write()` should return on succes.
	 *                      Allowed: 'name' (default), 'size'.
	 */
	public function __construct(array $config = array()) {
		$config += array(
			'path' => Libraries::get(true, 'resources') . '/tmp/files',
			'mkdir' => array('mode' => 550, 'recursive' => true),
			'writeReturns' => 'name',
		);
		parent::__construct($config);

		if ($config['mkdir']) {
			$mkdir =$config['mkdir'];
			mkdir($config['path'], $mkdir['mode'], $mkdir['recursive']);
		}
	}

	/**
	 * Writes data to a file, like but not exactly as `file_put_contents()`.
	 *
	 * @param string $filename Path to the file where to write the data, normaly relative to the
	 *                         path configured.
	 * @param mixed $data The data to write. Can be either a string, an array or a stream resource.
	 * @param mixed $options Options for the method.
	 * @return callable A callable implementing the action.
	 *                  The callable returns the number of bytes that were written, or `false`
	 *                  on failure.
	 */
	public function write($filename, $data, array $options = array()) {
		$config = $this->_config;
		$path = $config['path'];

		return function($self, $params) use (&$path) {
			$data = $params['data'];
			$path = "{$path}/{$params['filename']}";

			$size = file_put_contents($path, $data);
			switch ($params['options']['writeReturns']) {
				case 'size':
					return $size;
				default:
					return $params['filename'];
			}
		};
	}

	/**
	 * Reads data from a file, like but not exactly as `file_get_contents()`.
	 *
	 * @param string $filename Path to the file to read the data, normaly relative to the
	 *                         path configured.
	 * @param mixed $options Options for the method and strategies.
	 * @return callable A callable implementing the action.
	 *                  The callable returns the read data or `false` on failure.
	 */
	public function read($filename, array $options = array()) {
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
	 * Deletes a file.
	 *
	 * @param string $name Configuration to be used.
	 * @param string $filename Path to the file to delete, normaly relative to the
	 *                         path configured.
	 * @param mixed $options Options for the method and strategies.
	 * @return callable A callable implementing the action.
	 *                  The callable returns 'true' if succeeded, `false` otherwise
	 */
	public function delete($filename, array $options = array()) {
		$path = $this->_config['path'];

		return function($self, $params) use (&$path) {
			$path = "{$path}/{$params['filename']}";

			if (file_exists($path)) {
				return unlink($path);
			}

			return false;
		};
	}

	/**
	 * Checks whether a file or directory exists
	 *
	 * @param string $filename Path to the file to delete, normaly relative to the
	 *                         path configured.
	 * @param mixed $options Options for the method and strategies.
	 * @return callable A callable implementing the action.
	 *                  The callable returns 'true' if succeeded, `false` otherwise
	 */
	public function exists($filename, array $options = array()) {
		$path = $this->_config['path'];

		return function($self, $params) use (&$path) {
			$path = "{$path}/{$params['filename']}";

			clearstatcache();
			return file_exists($path);
		};
	}

}

?>