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

namespace Rogiel\MPQ\Tests\Stream\Parser;


use Rogiel\MPQ\Tests\AbstractTestCase;

class BinaryStreamParserTest extends AbstractTestCase {

	public function testReadByte() {
		$parser = $this->createMemoryParser(
			hex2bin("00FF")
		);
		$this->assertEquals(0x00, $parser->readByte());
		$this->assertEquals(0xFF, $parser->readByte());
	}

	public function testReadBytes() {
		$parser = $this->createMemoryParser(
			hex2bin("00FF")
		);
		$this->assertEquals(hex2bin("00FF"), $parser->readBytes(2));
	}

	public function testReadUInt32() {
		$parser = $this->createMemoryParser(
			hex2bin("0000BEEF")
		);
		$this->assertEquals(4022206464, $parser->readUInt32());
	}

	public function testReadUInt16() {
		$parser = $this->createMemoryParser(
			hex2bin("0E0A")
		);
		$this->assertEquals(2574, $parser->readUInt16());
	}

	public function testSkip() {
		$parser = $this->createMemoryParser(
			hex2bin("DEADBEEF")
		);
		$parser->skip(2);
		$this->assertEquals(0xBE, $parser->readByte());
	}

	public function testSeek() {
		$parser = $this->createMemoryParser(
			hex2bin("DEADBEEF")
		);
		$parser->seek(3);
		$this->assertEquals(0xEF, $parser->readByte());
	}


}
