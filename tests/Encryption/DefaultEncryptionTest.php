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

namespace Rogiel\MPQ\Tests\Encryption;

use Rogiel\MPQ\Encryption\DefaultEncryption;
use Rogiel\MPQ\Exception\Encryption\InvalidBlockSizeException;
use Rogiel\MPQ\Exception\Encryption\InvalidKeyException;
use Rogiel\MPQ\Tests\AbstractTestCase;

class DefaultEncryptionTest extends AbstractTestCase {

	const TEST_KEY = "c89a7de0c5fea9caf3f0";

	const TEST_SINGLE_BLOCK_PLAIN_TEXT_STRING = "3ef8cc61";
	const TEST_SINGLE_BLOCK_CIPHER_TEXT_STRING = "b86de569";

	public function testSingleBlockEncrypt() {
		$encryption = new DefaultEncryption(hex2bin(self::TEST_KEY));
		$cipher = $encryption->encrypt(
			hex2bin(self::TEST_SINGLE_BLOCK_PLAIN_TEXT_STRING), strlen(hex2bin(self::TEST_SINGLE_BLOCK_PLAIN_TEXT_STRING))
		);
		$this->assertEquals(hex2bin(self::TEST_SINGLE_BLOCK_CIPHER_TEXT_STRING), $cipher);
	}

	public function testSingleBlockDencrypt() {
		$encryption = new DefaultEncryption(hex2bin(self::TEST_KEY));
		$plain = $encryption->decrypt(
			hex2bin(self::TEST_SINGLE_BLOCK_CIPHER_TEXT_STRING), strlen(hex2bin(self::TEST_SINGLE_BLOCK_CIPHER_TEXT_STRING))
		);
		$this->assertEquals(hex2bin(self::TEST_SINGLE_BLOCK_PLAIN_TEXT_STRING), $plain);
	}

	const TEST_MULTIPLE_BLOCKS_PLAIN_TEXT_STRING = "87d1d07454135b170e89045025ed1e8309ee2f14aaaab586d9e81505a8ba72c5";
	const TEST_MULTIPLE_BLOCKS_CIPHER_TEXT_STRING = "0144f97cc3492b88aed652bb914e8748c58871e1aea753ff04fe731f7cce9a38";

	public function testMultipleBlocksEncrypt() {
		$encryption = new DefaultEncryption(hex2bin(self::TEST_KEY));
		$cipher = $encryption->encrypt(
			hex2bin(self::TEST_MULTIPLE_BLOCKS_PLAIN_TEXT_STRING), strlen(hex2bin(self::TEST_MULTIPLE_BLOCKS_PLAIN_TEXT_STRING))
		);
		$this->assertEquals(hex2bin(self::TEST_MULTIPLE_BLOCKS_CIPHER_TEXT_STRING), $cipher);
	}

	public function testMultipleBlocksDencrypt() {
		$encryption = new DefaultEncryption(hex2bin(self::TEST_KEY));
		$plain = $encryption->decrypt(
			hex2bin(self::TEST_MULTIPLE_BLOCKS_CIPHER_TEXT_STRING), strlen(hex2bin(self::TEST_MULTIPLE_BLOCKS_CIPHER_TEXT_STRING))
		);
		$this->assertEquals(hex2bin(self::TEST_MULTIPLE_BLOCKS_PLAIN_TEXT_STRING), $plain);
	}

	// -----------------------------------------------------------------------------------------------------------------

	public function testBlocklSize() {
		$encryption = new DefaultEncryption(hex2bin(self::TEST_KEY));
		$this->assertEquals(4, $encryption->getBlockSize());
	}

	// -----------------------------------------------------------------------------------------------------------------

	const TEST_SMALL_KEY = "c89a7de0c5fea9ca";

	public function testSmallKey() {
		$this->setExpectedException(InvalidKeyException::class);
		new DefaultEncryption(hex2bin(self::TEST_SMALL_KEY));
	}

	const TEST_LARGE_KEY = "c89a7de0c5fea9cac89a7de0c5fea9ca";

	public function testLargeKey() {
		$this->setExpectedException(InvalidKeyException::class);
		new DefaultEncryption(hex2bin(self::TEST_LARGE_KEY));
	}

	const TEST_INVALID_BLOCK_SIZE_PLAIN_TEXT_STRING = "3ef8cc";
	const TEST_INVALID_BLOCK_SIZE_CIPHER_TEXT_STRING = "b86de5";

	public function testInvalidBlockSizeEncrypt() {
		$this->setExpectedException(InvalidBlockSizeException::class);

		$encryption = new DefaultEncryption(hex2bin(self::TEST_KEY));
		$plain = $encryption->encrypt(
			hex2bin(self::TEST_INVALID_BLOCK_SIZE_PLAIN_TEXT_STRING), strlen(hex2bin(self::TEST_INVALID_BLOCK_SIZE_PLAIN_TEXT_STRING))
		);
	}

	public function testInvalidBlockSizeDencrypt() {
		$this->setExpectedException(InvalidBlockSizeException::class);

		$encryption = new DefaultEncryption(hex2bin(self::TEST_KEY));
		$plain = $encryption->decrypt(
			hex2bin(self::TEST_INVALID_BLOCK_SIZE_CIPHER_TEXT_STRING), strlen(hex2bin(self::TEST_INVALID_BLOCK_SIZE_CIPHER_TEXT_STRING))
		);
	}

}
