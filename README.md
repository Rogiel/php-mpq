# A PHP library for MPQ reading
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FRogiel%2Fphp-mpq.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2FRogiel%2Fphp-mpq?ref=badge_shield)


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


## License
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FRogiel%2Fphp-mpq.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2FRogiel%2Fphp-mpq?ref=badge_large)