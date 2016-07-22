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

namespace Rogiel\MPQ;

use Rogiel\MPQ\Encryption\DefaultEncryption;
use Rogiel\MPQ\Hashing\FileKeyHashing;
use Rogiel\MPQ\Hashing\NameAHashing;
use Rogiel\MPQ\Hashing\NameBHashing;
use Rogiel\MPQ\Metadata\Block;
use Rogiel\MPQ\Metadata\BlockTable;
use Rogiel\MPQ\Metadata\Hash;
use Rogiel\MPQ\Metadata\HashTable;
use Rogiel\MPQ\Metadata\Header;
use Rogiel\MPQ\Metadata\UserData;
use Rogiel\MPQ\Stream\Block\BlockStream;
use Rogiel\MPQ\Stream\EncryptedStream;
use Rogiel\MPQ\Stream\FileStream;
use Rogiel\MPQ\Stream\MemoryStream;
use Rogiel\MPQ\Stream\Parser\BinaryStreamParser;
use Rogiel\MPQ\Stream\Stream;

class MPQFile {

	/**
	 * @var Stream
	 */
	private $stream;

	/**
	 * @var boolean
	 */
	private $parsed;

	/**
	 * @var UserData
	 */
	private $userData;

	/**
	 * @var Header
	 */
	private $header;

	/**
	 * @var HashTable
	 */
	private $hashTable;

	/**
	 * @var BlockTable
	 */
	private $blockTable;

	public function __construct(Stream $stream) {
		$this->stream = $stream;
	}

	// -----------------------------------------------------------------------------------------------------------------

	public function isParsed() {
		return $this->parsed;
	}

	public function parse() {
		if($this->isParsed()) {
			return;
		}

		$parser = new BinaryStreamParser($this->stream);

		$signature = $this->parseSignature($parser);
		if($signature == "MPQ27") {
			$this->userData = UserData::parse($parser);
			$this->stream->seek($this->getUserDataOffset());
		}

		$signature = $this->parseSignature($parser);
		if($signature == "MPQ26") {
			$this->header = Header::parse($parser);
		}

		$this->hashTable = $this->parseHashTable();
		$this->blockTable = $this->parseBlockTable();

		$this->parsed = true;
	}

	private function parseHashTable() {
		$hashing = new FileKeyHashing();
		$encryptedStream = new EncryptedStream($this->stream, new DefaultEncryption($hashing->hash('(hash table)')));
		$parser = new BinaryStreamParser($encryptedStream);
		$parser->seek($this->getUserDataOffset() + $this->getHeader()->getHashTablePos());
		$hashes = array();
		for($i = 0; $i<$this->getHeader()->getHashTableSize(); $i++) {
			$hashes[$i] = Hash::parse($parser);
		}
		return new HashTable($hashes);
	}

	private function parseBlockTable() {
		$hashing = new FileKeyHashing();
		$encryptedStream = new EncryptedStream($this->stream, new DefaultEncryption($hashing->hash('(block table)')));
		$parser = new BinaryStreamParser($encryptedStream);
		$parser->seek($this->getUserDataOffset() + $this->getHeader()->getBlockTablePos());
		$blocks = array();

//		$offsetFix = 0;
		for($i = 0; $i<$this->getHeader()->getBlockTableSize(); $i++) {
			$block = $blocks[$i] = Block::parse($parser);
//			if($block->getSize() == 0) {
//				$offsetFix++;
//			}
		}
		return new BlockTable($blocks);
	}

	private function parseSignature(BinaryStreamParser $parser) {
		$signature  = chr($parser->readByte());
		$signature .= chr($parser->readByte());
		$signature .= chr($parser->readByte());
		$signature .= $parser->readByte();

		return $signature;
	}

	// -----------------------------------------------------------------------------------------------------------------

	private function getUserDataOffset() {
		$userData = $this->getUserData();
		if($userData === null) {
			return 0;
		}
		return $userData->getHeaderOffset();
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * @param $fileName
	 * @return null|Hash
	 */
	public function getFileHash($fileName) {
		$hashingA = new NameAHashing();
		$hashingB = new NameBHashing();

		$hashA = $hashingA->hash($fileName);
		$hashB = $hashingB->hash($fileName);

		return $this->getHashTable()->findHashByHash($hashA, $hashB);
	}

	/**
	 * @param $fileName
	 * @return null|Block
	 */
	public function getFileBlock($fileName) {
		$hash = $this->getFileHash($fileName);
		if($hash == NULL) {
			return NULL;
		}

		return $this->getBlockTable()->getBlock($hash->getBlockIndex());
	}

	public function openStream($fileName) {
		$block = $this->getFileBlock($fileName);
		if($block == NULL) {
			return NULL;
		}

		$stream = clone $this->stream;
		$stream->seek($this->getUserDataOffset() + $block->getFilePos());
		$parser = new BinaryStreamParser($stream);

		$sectors = array();
		if($block->isChecksumed() || !$block->isSingleUnit()) {
			$blockSize = $block->getCompressedSize();
			$fileSize  = $block->getSize();

			for ($i = $fileSize; $i > 0; $i -= $blockSize) {
				$sectors[] = $parser->readUInt32();
				$blockSize -= 4;
			}
			$sectors[] = $parser->readUInt32();
		} else {
			$sectors = array(
				0,
				$block->getCompressedSize()
			);
		}

		return new BlockStream($this, $stream, $block, $sectors);
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * @return UserData
	 */
	public function getUserData() {
		return $this->userData;
	}

	/**
	 * @return Header
	 */
	public function getHeader() {
		return $this->header;
	}

	/**
	 * @return HashTable
	 */
	public function getHashTable() {
		return $this->hashTable;
	}

	/**
	 * @return BlockTable
	 */
	public function getBlockTable() {
		return $this->blockTable;
	}

	// -----------------------------------------------------------------------------------------------------------------

	public static function parseFile($file) {
		return new MPQFile(new FileStream($file));
	}

	public static function parseString($string) {
		return new MPQFile(new MemoryStream($string));
	}

}
