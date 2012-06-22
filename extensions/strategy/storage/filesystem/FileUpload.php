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


namespace li3_filesystem\extensions\strategy\storage\filesystem;

/**
 * A simple, and by no means secure, strategy to prevent uploaded files from being written.
 *
 * For user uploadable files, keep an eye on security! See eg the information on OWASP.
 *
 * A simple example configuration:
 *
 * {{{
 * Filesystem::config(array(
 *     'default' => array(
 *         'adapter' => 'File',
 *         'path' => 'uploads\',
 *         'strategies' => array(
 *             'FileUpload' => array('allowed' => array('png', 'jpg'))
 *         )
 *     )
 * ));
 * }}}
 *
 * @link https://www.owasp.org/index.php/Unrestricted_File_Upload
 * @see lithium\core\Adaptable
 * @see FileSystemAdapter;
 */
use li3_filesystem\extensions\storage\FileSystemException;

class FileUpload extends \lithium\core\Object {

	/**
	 * When passed a the uploaded file variable from a form submission with
	 * file upload, it will only write files names having an allowed extension.
	 *
	 * Config used:
	 * 'allowed' array of allowed file types (currently only used for extensions)
	 *
	 * TODO: make more secure
	 *
	 * @param array $data The data from a formbased file upload
	 * @param mixed $options Options for the method, filters and strategies.
	 * @return mixed $data
	 * @throws FileSystemException
	 */
	public function write($data, array $options = array()) {
		$allowed  = $this->_config['allowed'];

		$origFileName = pathinfo($data['name']);
		$filename = pathinfo($options['filename']);

		if (! ($filename['extension'] == $origFileName['extension'] &&
				in_array(strtolower($origFileName['extension']), $allowed))) {
			$message = "File `$filename` is not allowed to be uploaded.";
			throw new FileSystemException($message);
		}

		$data = file_get_contents($data['tmp_name']);

		return $data;
	}


}

?>