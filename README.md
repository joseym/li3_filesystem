# A File System plugin for the Lithium Framework

This is 'li3 filesystem: managing files the easy way', by Hans Donner.

It builds on ' Lithium Filesystem: managing file uploads the easy way'
by Little Boy Genius (https://github.com/mariuskubilius/li3_filesystem).


## Installation

Checkout the code to your library directory:

    cd libraries
    git clone git@github.com:/hans-d/li3_filesystem.git

Include the library in in your `/app/config/bootstrap/libraries.php`

    Libraries::add('li3_filesystem');

## Configuration

Local filesystem configurations would look something like:

    use li3_filesystem\extensions\storage\FileSystem;

    FileSystem::config(array(
        'default' => array('adapter' => 'File')
    ));

Stream based configurations would look something like:

    use li3_filesystem\storage\FileSystem;

    FileSystem::config(array(
        'ftp' => array(
            'adapter' => 'Stream',
            'wrapper' => 'ftp',
            'path' => 'user:password@example.com/pub/'
        }
    ));
