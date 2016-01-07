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


use Rogiel\MPQ\Metadata\UserData;
use Rogiel\MPQ\Tests\AbstractTestCase;

class UserDataTest extends AbstractTestCase {

	const TEST_USER_RAW_DATA = "67685b0ba71a3e0c4783877483b6c41cddae8669eb33b9ada05452e362dadd16739e84f2683ff3ff550608dae02ac3d8cd4b0bbcdfa353146f3d9516f327f490d678fbe06b725ced05e405e6476e70ee3bdb217fd02ec1db0a2a374ef6d9d000a66ad6c53e69d1841a95837f5adcf33214b03c277f5c0e49ce2a0623232061573de9352a43eb1f9c9294940e4e98b272e66095a0ceb11c830779f8cc04a2224c1ec2bb510f74f96a2e212eda9156bd1edb7144d37480bd746098f4b25f117b41ffede36645c0413d6880eeb07b60b6915b3d1dfdcfc9cbc10d3ffcd9f0e8f19127210c20d38308e0ebcd672c6741679a772d17893975b4024b640e513aa5bca1ce3c38c180c9eb416e98d93d68fbda47e884ddc4af60c6194ddc3664b05f8d7bd5f4cb0a4ba4aaf213c4c4ddbe978c901bfdc6a8ec0533693b5961dc398a48629b299d9a6e5fc6e33e7d5e41e5b38e80f7c559496d70fb5759a77c0b8a2580976cb7a9578c232b891cc1e14623b37a5274cf3474f7350717522279487483956d3ec9dc73aea5656ba44ca243c913fab9feb5085f41a5ad08d31371d230eb6a6334490263c248c6207e4624dac5ff2777e25686446161024539826f54be01579dbea2877f25165f33b78803915f733bbcda0364f8b1a4ac04ba3556286ca22c946c2aacaf2810fd909b8d4be4fb9133c8b55fb742";

	const TEST_USER_DATA =
		"00020000".               /* size */
		"00040000".               /* header offset */
		"01000000".               /* user data header */
		self::TEST_USER_RAW_DATA; /* data */

	public function testParse() {
		$parser = $this->createMemoryParser(hex2bin(self::TEST_USER_DATA));
		$userData = UserData::parse($parser);

		$this->assertEquals(512, $userData->getSize());
		$this->assertEquals(1024, $userData->getHeaderOffset());
		$this->assertEquals(1, $userData->getHeader());
		$this->assertEquals(hex2bin(self::TEST_USER_RAW_DATA), $userData->getRawContent());
	}

}
