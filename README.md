# A PHP library for MPQ reading

This library allows you to read MPQ files from PHP.

## Installation

The recommended way of installing this library is using Composer.

    composer require "rogiel/mpq"
    
## Example

    use Rogiel\MPQ\MPQFile;
    
    $file = MPQFile::parseFile(__DIR__.'/test.SC2Replay');
    $stream = $file->openStream('replay.details');
    while($data = $stream->readBytes(100)) {
    	echo $data;
    }

## TODO

* Encrypted files (parcial support)
* File writing
