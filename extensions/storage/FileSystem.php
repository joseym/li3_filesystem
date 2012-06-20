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

use lithium\core\ConfigException;


/**
 * The `FileSystem` static class provides a consistent interface to configure and utilize the different
 * file handling adatpers included, as well as your own adapters.
 *
 * In most cases, you will configure various named Filesystem configurations in your bootstrap process,
 * which will then be available to you in all other parts of your application.
 *
 * Strategies can be specified to target the data to the filename:
 * - write: writeFilename, write
 * - read: readFilename
 * - delete: deleteFilename
 * - exists: existsFilename
 *
 * For user uploadable files, keep an eye on security! See eg the information on OWASP.
 *
 * A simple example configuration:
 *
 * {{{
 * Filesystem::config(array(
 *     'db' => array('adapter' => 'FS.Grid'),
 *     'cdn' => array(
 *         'adapter' => 'CloudFiles',
 *         'api' =>'enter your api key here blah blah',
 *     ),
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
class FileSystem extends \lithium\core\Adaptable {

	/**
	 * Stores configurations for FileSystem adapters
	 *
	 * @var array
	 */
	protected static $_configurations = array();

	/**
	 * Libraries::locate() compatible path to adapters for this class.
	 *
	 * @var string Dot-delimited path.
	 */
	protected static $_adapters = 'adapter.storage.filesystem';

	/**
	 * Libraries::locate() compatible path to strategies for this class.
	 *
	 * @var string Dot-delimited path.
	 */
	protected static $_strategies = 'strategy.storage.filesystem';

	/**
	 * Optionally applies strategies. Strategies are applied, unless specificly disabled.
	 *
	 * @see \lithium\core\Adaptable::applyStrategies()
	 * @param string $method The strategy method to be applied.
	 * @param string $name The named configuration
	 * @param mixed $data The data to which the strategies will be applied.
	 * @param array $options additional options:
	 *             `strategies` if set to `false` will skip the strategies and return
	 *                          the data
	 * @return mixed Result of application of strategies to data. If no strategies
	 *         have been configured, this method will simply return the original data.
	 * @throws FileSystemException when a strategie blocks the data without throwing an exception
	 *         with the reason
	 */
	public static function applyStrategies($method, $name, $data, array $options = array()){
		$options += array('strategies' => true);
		if (!$options['strategies']) {
			return $data;
		}

		$data = parent::applyStrategies($method, $name, $data, $options);
		if (!$data) {
			$message = "Blocked from executing by a strategy targetting `$method`.";
			throw new FileSystemException($message);
		}
		return $data;
	}

	/**
	 * Writes data to a file, like but not exactly as `file_put_contents()`.
	 *
	 * Implementation details depend on the adapter used.
	 *
	 * @param string $name Configuration to be used.
	 * @param string $filename Path to the file where to write the data, normaly relative to the
	 *                         path configured.
	 * @param mixed $data The data to write. Can be either a string, an array or a stream resource.
	 * @param mixed $options Options for the method, filters and strategies. Supported at abstract level:
	 *              `strategies` if false, strategies will not be applied
	 * @return mixed The number of bytes that were written, or `false` on failure.
	 * @throws ConfigException On unknown configuration.
	 * @filter This method may be filtered.
	 */
	public static function write($name, $filename, $data, array $options = array()) {
		$settings = static::config();
		if (!isset($settings[$name])) {
			throw new ConfigException("Configuration `{$name}` has not been defined.");
		}

		$filename = static::applyStrategies(__FUNCTION__ . 'Filename', $name, $filename, $options);
		$data = static::applyStrategies(__FUNCTION__, $name, $data, compact('filename') + $options);

		$method = static::adapter($name)->write($filename, $data, $options);
		$params = compact('filename', 'data', 'options');
		return static::_filter(__FUNCTION__, $params, $method, $settings[$name]['filters']);
	}

	/**
	 * Reads data from a file, like but not exactly as `file_get_contents()`.
	 *
	 * Implementation details depend on the adapter used.
	 *
	 * @param string $name Configuration to be used.
	 * @param string $filename Path to the file to read the data, normaly relative to the
	 *                         path configured.
	 * @param mixed $options Options for the method and strategies.
	 * @return mixed The the read data or `false` on failure.
	 * @throws ConfigException On unknown configuration.
	 * @filter This method may be filtered.
	 */
	public static function read($name, $filename, array $options = array()) {
		$settings = static::config();
		if (!isset($settings[$name])) {
			throw new ConfigException("Configuration `{$name}` has not been defined.");
		}

		$filename = static::applyStrategies(__FUNCTION__ . 'Filename', $name, $filename, $options);
		$method = static::adapter($name)->read($filename, $options);
		$params = compact('filename', 'options');
		return static::_filter(__FUNCTION__, $params, $method, $settings[$name]['filters']);
	}

	/**
	 * Deletes a file.
	 *
	 * Implementation details depend on the adapter used.
	 *
	 * @param string $name Configuration to be used.
	 * @param string $filename Path to the file to delete, normaly relative to the
	 *                         path configured.
	 * @param mixed $options Options for the method and strategies.
	 * @return boolean 'true' if succeeded, `false` otherwise
	 * @throws ConfigException On unknown configuration.
	 * @filter This method may be filtered.
	 */
	public static function delete($name, $filename, array $options = array()) {
		$settings = static::config();
		if (!isset($settings[$name])) {
			throw new ConfigException("Configuration `{$name}` has not been defined.");
		}

		$filename = static::applyStrategies(__FUNCTION__ . 'Filename', $name, $filename, $options);
		$method   = static::adapter($name)->delete($filename);
		$params   = compact('filename', 'options');
		return static::_filter(__FUNCTION__, $params, $method, $settings[$name]['filters']);
	}

	/**
	 * Checks whether a file or directory exists
	 *
	 * Implementation details depend on the adapter used.
	 *
	 * @param string $name Configuration to be used.
	 * @param string $filename Path to the file to delete, normaly relative to the
	 *                         path configured.
	 * @param mixed $options Options for the method and strategies.
	 * @return boolean 'true' if succeeded, `false` otherwise
	 * @throws ConfigException On unknown configuration.
	 * @filter This method may be filtered.
	 */
	public static function exists($name, $filename, array $options = array()) {
		$settings = static::config();
		if (!isset($settings[$name])) {
			throw new ConfigException("Configuration `{$name}` has not been defined.");
		}

		$filename = static::applyStrategies(__FUNCTION__ . 'Filename', $name, $filename, $options);
		$method   = static::adapter($name)->exists($filename);
		$params   = compact('filename', 'options');
		return static::_filter(__FUNCTION__, $params, $method, $settings[$name]['filters']);
	}

}

?>