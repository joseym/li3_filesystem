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


namespace li3_filesystem\extensions\storage;

/**
 * The filesystem adapter interface.
 *
 * A filesystem adapter is meant to be used through the `FileSystem` interface,
 * which abstracts away file writting, adapter instantiation and filter implementation.
 *
 * An example configuration for a adapter looks like:
 *
 * {{{
 * FileSystem::config(array(
 *     'filesystem-config-name' => array(
 *         'adapter' => 'fileSystemAdapter', // Use an existing file adapater
 *         'path' => 'some/path/to/use',
 *         'settingA' => 'whatever'
 *     )
 * ));
 * }}}
 *
 * @see \li3_filesystem\extensions\storage\FileSystem
 * @see \li3_filesystem\extensions\adapter\storage\filesystem\File
 */

interface FileSystemAdapater {

	/**
	 * Writes data to a file, like but not exactly as `file_put_contents()`.
	 *
	 * @param string $filename Path to the file where to write the data, normaly relative to the
	 *                         path configured.
	 * @param mixed $data The data to write. Can be either a string, an array or a stream resource.
	 * @param mixed $options Options for the method.
	 * @return callable A callable implementing the action.
	 *                  The callable should return the number of bytes that were written,
	 *                  or `false` on failure.
	 */

	public function write($filename, $data, array $options = array());

	/**
	 * Reads data from a file, like but not exactly as `file_get_contents()`.
	 *
	 * @param string $filename Path to the file to read the data, normaly relative to the
	 *                         path configured.
	 * @param mixed $options Options for the method and strategies.
	 * @return callable A callable implementing the action.
	 *                  The callable should return the read data or `false` on failure.
	 */
	public function read($filename, array $options = array());

	/**
	 * Deletes a file.
	 *
	 * @param string $name Configuration to be used.
	 * @param string $filename Path to the file to delete, normaly relative to the
	 *                         path configured.
	 * @param mixed $options Options for the method and strategies.
	 * @return callable A callable implementing the action.
	 *                  The callable should return 'true' if succeeded, `false` otherwise
	 */
	public function delete($filename, array $options = array());

	/**
	 * Checks whether a file or directory exists
	 *
	 * @param string $filename Path to the file to delete, normaly relative to the
	 *                         path configured.
	 * @param mixed $options Options for the method and strategies.
	 * @return callable A callable implementing the action.
	 *                  The callable should return 'true' if succeeded, `false` otherwise
	 */
	public function exists($filename, array $options = array());

}

?>