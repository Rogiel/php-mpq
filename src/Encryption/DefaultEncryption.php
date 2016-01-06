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

namespace Rogiel\MPQ\Encryption;


use Rogiel\MPQ\Util\CryptoUtils;

class DefaultEncryption implements Encryption {

	private $key;
	private $seed;

	public function __construct($key) {
		$this->key = $key;
		$this->seed = ((0xEEEE << 16) | 0xEEEE);
		CryptoUtils::initTable();
	}

	public function reset($key) {
		$this->key = $key;
		$this->seed = ((0xEEEE << 16) | 0xEEEE);
	}

	public function decrypt($string, $length) {
		$data = $this->createBlockArray($string, $length);

		$datalen = $length / 4;
		for($i = 0;$i < $datalen;$i++) {
			$this->seed = CryptoUtils::uPlus($this->seed,CryptoUtils::$cryptTable[0x400 + ($this->key & 0xFF)]);
			$ch = $data[$i] ^ (CryptoUtils::uPlus($this->key,$this->seed));

			$this->key = (CryptoUtils::uPlus(((~$this->key) << 0x15), 0x11111111)) | (CryptoUtils::rShift($this->key,0x0B));
			$this->seed = CryptoUtils::uPlus(CryptoUtils::uPlus(CryptoUtils::uPlus($ch,$this->seed),($this->seed << 5)),3);
			$data[$i] = $ch & ((0xFFFF << 16) | 0xFFFF);
		}
		
		return $this->createDataStream($data, $length / 4);
	}

	public function encrypt($data, $length) {
		$key = clone $this->key;

		$seed = ((0xEEEE << 16) | 0xEEEE);
		$datalen = $length;
		for($i = 0;$i < $datalen;$i++) {
			$seed = CryptoUtils::uPlus($seed,CryptoUtils::$cryptTable[0x400 + ($key & 0xFF)]);
			$ch = $data[$i] ^ (CryptoUtils::uPlus($key,$seed));

			$key = (CryptoUtils::uPlus(((~$key) << 0x15), 0x11111111)) | (CryptoUtils::rShift($key,0x0B));
			$seed = CryptoUtils::uPlus(CryptoUtils::uPlus(CryptoUtils::uPlus($data[$i],$seed),($seed << 5)),3);
			$data[$i] = $ch & ((0xFFFF << 16) | 0xFFFF);
		}
		return $data;
	}

	public function getBlockSize() {
		return 4;
	}

	private function createBlockArray($string, $length) {
		$data = array();
		for($i = 0; $i<$length / 4; $i++) {
			$t = unpack("V", substr($string, 4*$i, 4));
			$data[$i] = $t[1];
		}
		return $data;
	}

	private function createDataStream($data, $length) {
		$dataOutput = '';
		for($i = 0; $i<$length / 4; $i++) {
			$dataOutput .= pack("V", $data[$i]);
		}
		return $dataOutput;
	}

}