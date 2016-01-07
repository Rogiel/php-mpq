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

namespace Rogiel\MPQ\Tests\Metadata;


use Rogiel\MPQ\Metadata\Block;
use Rogiel\MPQ\Tests\AbstractTestCase;

class BlockTest extends AbstractTestCase {

	const TEST_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00000000"; /* flags */

	public function testParse() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(8208, $block->getFilePos());
		$this->assertEquals(1048576, $block->getCompressedSize());
		$this->assertEquals(1048576, $block->getSize());
		$this->assertEquals(0, $block->getFlags());
	}

	const TEST_IMPLODED_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00010000"; /* flags */

	public function testImplodedFlag() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_IMPLODED_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(true, $block->isImploded());
	}

	const TEST_COMPRESSED_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00020000"; /* flags */

	public function testCompressedFlag() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_COMPRESSED_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(true, $block->isCompressed());
	}

	const TEST_ENCRYPTED_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00000100"; /* flags */

	public function testEncryptedFlag() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_ENCRYPTED_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(true, $block->isEncrypted());
	}

	const TEST_FIX_KEY_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00000200"; /* flags */

	public function testKeyBasedOnPositionFlag() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_FIX_KEY_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(true, $block->isKeyBasedOnPosition());
	}

	const TEST_PATCHED_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00001000"; /* flags */

	public function testPatchedFlag() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_PATCHED_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(true, $block->isPatched());
	}

	const TEST_SINGLE_UNIT_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00000001"; /* flags */

	public function testSingleUnitFlag() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_SINGLE_UNIT_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(true, $block->isSingleUnit());
	}

	const TEST_DELETE_MARKER_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00000002"; /* flags */

	public function testDeleteMarkerFlag() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_DELETE_MARKER_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(true, $block->isDeleted());
	}

	const TEST_CHECKSUMED_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00000004"; /* flags */

	public function testChecksumedFlag() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_CHECKSUMED_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(true, $block->isChecksumed());
	}

	const TEST_EXISTS_BLOCK_DATA =
		"10200000". /* file position */
		"00001000". /* compressed size */
		"00001000". /* size */
		"00000080"; /* flags */

	public function testExistsFlag() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_EXISTS_BLOCK_DATA));
		$block = Block::parse($parser);

		$this->assertEquals(true, $block->isExisting());
	}

}