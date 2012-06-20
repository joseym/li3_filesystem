<?php

namespace li3_filesystem\tests\cases\storage;

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


use li3_filesystem\extensions\storage\FileSystem;

class FileSystemTest extends \lithium\test\Integration {

	public function setUp() {
		FileSystem::reset();
	}

	public function tearDown() {
		FileSystem::reset();
	}

	public function testBasicFileSystemConfig() {
		$result = FileSystem::config();
		$this->assertFalse($result);

		$config = array('default' => array('adapter' => '\some\adapter', 'filters' => array()));
		$result = FileSystem::config($config);
		$this->assertNull($result);

		$expected = $config;
		$result   = FileSystem::config();
		$this->assertEqual($expected, $result);

		$result = FileSystem::reset();
		$this->assertNull($result);

		$config = array('default' => array('adapter' => '\some\adapter', 'filters' => array()));
		FileSystem::config($config);

		$result   = FileSystem::config();
		$expected = $config;
		$this->assertEqual($expected, $result);

		$result = FileSystem::reset();
		$this->assertNull($result);

		$config = array('default' => array(
				'adapter' => '\some\adapter',
				'filters' => array('Filter1', 'Filter2')
		));
		FileSystem::config($config);

		$result   = FileSystem::config();
		$expected = $config;
		$this->assertEqual($expected, $result);
	}

	public function testFileSystemWrite() {
		$config = array('default' => array(
				'adapter' => '\li3_filesystem\tests\mocks\adapter\storage\filesystem\Mock',
				'filters' => array(),
				'path'    => '/whatever/we/need'
		));
		FileSystem::config($config);

		$result = FileSystem::config();
		$this->assertEqual($config, $result);

		$filename = 'test_file';
		$data     = 'Some test content';

		$this->assertTrue(FileSystem::write('default', $filename, $data));
		$this->assertException(
				'Configuration `non_existing` has not been defined.',
				function () use ($filename, $data) {
					FileSystem::write('non_existing', $filename, $data);
				}
		);
	}

	public function testFileSystemRead() {
		$config = array('default' => array(
				'adapter' => '\li3_filesystem\tests\mocks\adapter\storage\filesystem\Mock',
				'filters' => array(),
				'path'    => '/whatever/we/need'
		));

		FileSystem::config($config);
		$result = FileSystem::config();
		$this->assertEqual($config, $result);

		$filename = 'test_file';
		$data     = 'Some Test content';

		$result = FileSystem::write('default', $filename, $data);
		$this->assertTrue($result);

		$result = FileSystem::read('default', $filename);
		$this->assertEqual($data, $result);
	}
}

?>