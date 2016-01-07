<?php
/**
 * Copyright (c) 2016, Rogiel Sulzbach
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Rogiel\MPQ\Tests\Compression;


use Rogiel\MPQ\Compression\BZIPCompression;
use Rogiel\MPQ\Exception\Compression\InvalidInputDataException;
use Rogiel\MPQ\Tests\AbstractTestCase;

class BZIPCompressionTest extends AbstractTestCase {

	const TEST_COMPRESSED_DATA = "425a6834314159265359065c89da0000009780400000400080060490002000310c082031a916c41d41e2ee48a70a1200cb913b40";
	const TEST_UNCOMPRESSED_DATA = "Hello World";

	public function testCompress() {
		$compression = new BZIPCompression();
		$compressed = $compression->compress(
			self::TEST_UNCOMPRESSED_DATA, strlen(self::TEST_UNCOMPRESSED_DATA)
		);

		$this->assertEquals(hex2bin(self::TEST_COMPRESSED_DATA), $compressed);
	}

	public function testDecompress() {
		$compression = new BZIPCompression();
		$compressed = $compression->decompress(
			hex2bin(self::TEST_COMPRESSED_DATA), strlen(hex2bin(self::TEST_COMPRESSED_DATA))
		);

		$this->assertEquals(self::TEST_UNCOMPRESSED_DATA, $compressed);
	}

	const TEST_EMPTY_COMPRESSED_DATA = "425a683417724538509000000000";

	public function testEmptyCompress() {
		$compression = new BZIPCompression();
		$compressed = $compression->compress("", 0);
		$this->assertEquals(hex2bin(self::TEST_EMPTY_COMPRESSED_DATA), $compressed);
	}

	public function testEmptyDecompress() {
		$compression = new BZIPCompression();
		$compressed = $compression->decompress(
			hex2bin(self::TEST_EMPTY_COMPRESSED_DATA), strlen(hex2bin(self::TEST_EMPTY_COMPRESSED_DATA))
		);
		$this->assertEquals("", $compressed);
	}

	const TEST_INVALID_COMPRESSED_DATA = "425a6417724538509000000000";

	public function testInvalidDecompress() {
		$this->setExpectedException(InvalidInputDataException::class);
		$compression = new BZIPCompression();
		$compression->decompress(
			hex2bin(self::TEST_INVALID_COMPRESSED_DATA), strlen(hex2bin(self::TEST_INVALID_COMPRESSED_DATA))
		);
	}


}
