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

namespace Rogiel\MPQ\Tests\Hashing;


use Rogiel\MPQ\Hashing\NameAHashing;
use Rogiel\MPQ\Hashing\Hashing;
use Rogiel\MPQ\Tests\AbstractTestCase;

class NameAHashingTest extends AbstractTestCase {

	/**
	 * @var Hashing
	 */
	private $hashing;

	public function setUp() {
		$this->hashing = new NameAHashing();
	}

	const TEST_PLAIN_TEXT = "b99cb06efcddccdf861494b2b431a592";
	const TEST_HASHED_TEXT = 1258300789;

	public function testHash() {
		$hash = $this->hashing->hash(hex2bin(self::TEST_PLAIN_TEXT));
		$this->assertEquals(self::TEST_HASHED_TEXT, $hash);
	}

	const TEST_EMPTY_TEXT = "";
	const TEST_HASHED_EMPTY = 2146271213;

	public function testEmptyHash() {
		$hash = $this->hashing->hash(hex2bin(self::TEST_EMPTY_TEXT));
		$this->assertEquals(self::TEST_HASHED_EMPTY, $hash);
	}

}
