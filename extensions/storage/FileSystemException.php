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
 * A FileSystemException is thrown when actions on the filesystem could not be completed,
 * due to various reasons. Eg when an applied strategie decides that the file may not be
 * written.
 */
class FileSystemException extends \RuntimeException {}