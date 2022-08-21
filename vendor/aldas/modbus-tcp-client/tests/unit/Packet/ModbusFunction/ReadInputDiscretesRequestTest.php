<?php

namespace Tests\Packet\ModbusFunction;

use ModbusTcpClient\Packet\ErrorResponse;
use ModbusTcpClient\Packet\ModbusFunction\ReadInputDiscretesRequest;
use ModbusTcpClient\Packet\ModbusPacket;
use PHPUnit\Framework\TestCase;

class ReadInputDiscretesRequestTest extends TestCase
{
    public function testPacketToString()
    {
        $this->assertEquals(
            "\x00\x01\x00\x00\x00\x06\x10\x02\x00\x6B\x00\x03",
            (new ReadInputDiscretesRequest(107, 3, 16, 1))->__toString()
        );
    }

    public function testPacketProperties()
    {
        $packet = new ReadInputDiscretesRequest(107, 3, 17, 1);
        $this->assertEquals(ModbusPacket::READ_INPUT_DISCRETES, $packet->getFunctionCode());
        $this->assertEquals(107, $packet->getStartAddress());
        $this->assertEquals(3, $packet->getQuantity());

        $header = $packet->getHeader();
        $this->assertEquals(1, $header->getTransactionId());
        $this->assertEquals(0, $header->getProtocolId());
        $this->assertEquals(6, $header->getLength());
        $this->assertEquals(17, $header->getUnitId());
    }

    public function testParse()
    {
        $packet = ReadInputDiscretesRequest::parse("\x00\x01\x00\x00\x00\x06\x11\x02\x00\x6B\x00\x03");
        $this->assertEquals($packet, (new ReadInputDiscretesRequest(107, 3, 17, 1))->__toString());
        $this->assertEquals(107, $packet->getStartAddress());
        $this->assertEquals(3, $packet->getQuantity());

        $header = $packet->getHeader();
        $this->assertEquals(1, $header->getTransactionId());
        $this->assertEquals(0, $header->getProtocolId());
        $this->assertEquals(6, $header->getLength());
        $this->assertEquals(17, $header->getUnitId());
    }

    public function testParseShouldReturnErrorResponseForTooShortPacket()
    {
        $packet = ReadInputDiscretesRequest::parse("\x00\x01\x00\x00\x00\x06\x11\x02\x00\x6B\x00");
        self::assertInstanceOf(ErrorResponse::class, $packet);
        $toString = $packet->__toString();
        // transaction id is random
        $toString[0] = "\x00";
        $toString[1] = "\x00";
        self::assertEquals("\x00\x00\x00\x00\x00\x03\x00\x82\x04", $toString);
    }

    public function testParseShouldReturnErrorResponseForInvalidFunction()
    {
        $packet = ReadInputDiscretesRequest::parse("\x00\x01\x00\x00\x00\x06\x11\x04\x00\x6B\x00\x01");
        self::assertInstanceOf(ErrorResponse::class, $packet);
        $toString = $packet->__toString();
        self::assertEquals("\x00\x01\x00\x00\x00\x03\x11\x82\x01", $toString);
    }

    public function testParseShouldReturnErrorResponseForInvalidQuantity()
    {
        $packet = ReadInputDiscretesRequest::parse("\x00\x01\x00\x00\x00\x06\x11\x02\x00\x6B\x00\x00");
        self::assertInstanceOf(ErrorResponse::class, $packet);
        $toString = $packet->__toString();
        self::assertEquals("\x00\x01\x00\x00\x00\x03\x11\x82\x03", $toString);
    }

}
