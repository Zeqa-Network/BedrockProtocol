<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\ShowStoreOfferRedirectType;

class ShowStoreOfferPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::SHOW_STORE_OFFER_PACKET;

	public string $offerId;
	public bool $showAll;
	public ShowStoreOfferRedirectType $redirectType;

	/**
	 * @generate-create-func
	 */
	public static function create(string $offerId, bool $showAll, ShowStoreOfferRedirectType $redirectType) : self{
		$result = new self;
		$result->offerId = $offerId;
		$result->showAll = $showAll;
		$result->redirectType = $redirectType;
		return $result;
	}

	protected function decodePayload(ByteBufferReader $in, int $protocolId) : void{
		$this->offerId = CommonTypes::getString($in);
		if($protocolId >= ProtocolInfo::PROTOCOL_1_20_50){
			$this->redirectType = ShowStoreOfferRedirectType::fromPacket(Byte::readUnsigned($in));
		}else{
			$this->showAll = CommonTypes::getBool($in);
		}
	}

	protected function encodePayload(ByteBufferWriter $out, int $protocolId) : void{
		CommonTypes::putString($out, $this->offerId);
		if($protocolId >= ProtocolInfo::PROTOCOL_1_20_50){
			Byte::writeUnsigned($out, $this->redirectType->value);
		}else{
			CommonTypes::putBool($out, $this->showAll);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleShowStoreOffer($this);
	}
}
