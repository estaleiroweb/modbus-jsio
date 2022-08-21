<?php
/**
 * Phpmodbus Copyright (c) 2004, 2013 Jan Krakora
 *  
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt.
 *   
 *
 * @copyright  Copyright (c) 2004, 2013 Jan Krakora
 * @license PhpModbus license 
 * @category Phpmodbus
 * @tutorial Phpmodbus.pkg 
 * @package Phpmodbus 
 * @version $id$
 *  
 */

/**
 * ModbusMaster
 *
 * This class deals with the MODBUS master
 *  
 * Implemented MODBUS master functions:
 *   - FC  1: read coils
 *   - FC  2: read input discretes
 *   - FC  3: read multiple registers
 *   - FC  4: read multiple input registers 
 *   - FC  5: write single coil  
 *   - FC  6: write single register
 *   - FC 15: write multiple coils
 *   - FC 16: write multiple registers
 *   - FC 23: read write registers
 *   
 * @author Jan Krakora
 * @copyright  Copyright (c) 2004, 2013 Jan Krakora
 * @package Phpmodbus  
 *
 */
class ModbusMaster {
  private $sock;
  public $host = "192.168.1.1";
  public $port = "502";  
  public $client = "";
  public $client_port = "502";
  public $status;
  public $timeout_sec = 5; // Timeout 5 sec
  public $endianness = 0; // Endianness codding (little endian == 0, big endian == 1) 
  public $socket_protocol = "UDP"; // Socket protocol (TCP, UDP)
  
  /**
   * ModbusMaster
   *
   * This is the constructor that defines {@link $host} IP address of the object. 
   *     
   * @param String $host An IP address of a Modbus TCP device. E.g. "192.168.1.1"
   * @param String $protocol Socket protocol (TCP, UDP)   
   */         
  function ModbusMaster($host, $protocol){
    $this->socket_protocol = $protocol;
    $this->host = $host;
  }

  /**
   * __toString
   *
   * Magic method
   */
  function  __toString() {
      return "{$this->status}";
  }

  /**
   * connect
   *
   * Connect the socket
   *
   * @return bool
   */
  private function connect(){
    // Create a protocol specific socket 
    if ($this->socket_protocol == "TCP"){ 
        // TCP socket
        $this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);      
    } elseif ($this->socket_protocol == "UDP"){
        // UDP socket
        $this->sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    } else {
        throw new Exception("Unknown socket protocol, should be 'TCP' or 'UDP'");
    }
    // Bind the client socket to a specific local port
    if (strlen($this->client)>0){
        $result = socket_bind($this->sock, $this->client, $this->client_port);
        if ($result === false) {
            throw new Exception("socket_bind() failed.</br>Reason: ($result)".
                socket_strerror(socket_last_error($this->sock)));
        } else {
            print "Bound\n";
        }
    }
    // Socket settings
    socket_set_option($this->sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 1, 'usec' => 0));
    // Connect the socket
    $result = @socket_connect($this->sock, $this->host, $this->port);
    if ($result === false) {
        throw new Exception("socket_connect() failed.</br>Reason: ($result)".
            socket_strerror(socket_last_error($this->sock)));
    } else {
        print "Connected\n";
        return true;        
    }    
  }

  /**
   * disconnect
   *
   * Disconnect the socket
   */
  private function disconnect(){    
    socket_close($this->sock);
    print "Disconnected\n";
  }

  /**
   * send
   *
   * Send the packet via Modbus
   *
   * @param string $packet
   */
  private function send($packet){
    socket_write($this->sock, $packet, strlen($packet));  
    print "Send\n";
  }

  /**
   * rec
   *
   * Receive data from the socket
   *
   * @return bool
   */
  private function rec(){
    socket_set_nonblock($this->sock);
    $readsocks[] = $this->sock;     
    $writesocks = NULL;
    $exceptsocks = NULL;
    $rec = "";
    $lastAccess = time();
    while (socket_select($readsocks, 
            $writesocks, 
            $exceptsocks,
            0, 
            300000) !== FALSE) {
            print "Wait data ... \n";
        if (in_array($this->sock, $readsocks)) {
            while (@socket_recv($this->sock, $rec, 2000, 0)) {
                print "Data received\n";
                return $rec;
            }
            $lastAccess = time();
        } else {             
            if (time()-$lastAccess >= $this->timeout_sec) {
                throw new Exception( "Watchdog time expired [ " .
                  $this->timeout_sec . " sec]!!! Connection to " . 
                  $this->host . " is not established.");
            }
        }
        $readsocks[] = $this->sock;
    }
  } 
  
  /**
   * responseCode
   *
   * Check the Modbus response code
   *
   * @param string $packet
   * @return bool
   */
  private function responseCode($packet){    
    if((ord($packet[7]) & 0x80) > 0) {
      // failure code
      $failure_code = ord($packet[8]);
      // failure code strings
      $failures = array(
        0x01 => "ILLEGAL FUNCTION",
        0x02 => "ILLEGAL DATA ADDRESS",
        0x03 => "ILLEGAL DATA VALUE",
        0x04 => "SLAVE DEVICE FAILURE",
        0x05 => "ACKNOWLEDGE",
        0x06 => "SLAVE DEVICE BUSY",
        0x08 => "MEMORY PARITY ERROR",
        0x0A => "GATEWAY PATH UNAVAILABLE",
        0x0B => "GATEWAY TARGET DEVICE FAILED TO RESPOND");
      // get failure string
      if(key_exists($failure_code, $failures)) {
        $failure_str = $failures[$failure_code];
      } else {
        $failure_str = "UNDEFINED FAILURE CODE";
      }
      // exception response
      throw new Exception("Modbus response error code: $failure_code ($failure_str)");
    } else {
      print "Modbus response error code: NOERROR\n";
      return true;
    }    
  }
  
  /**
   * readCoils
   * 
   * Modbus function FC 1(0x01) - Read Coils
   * 
   * Reads {@link $quantity} of Coils (boolean) from reference 
   * {@link $reference} of a memory of a Modbus device given by 
   * {@link $unitId}.
   * 
   * @param type $unitId
   * @param type $reference
   * @param type $quantity 
   */
  function readCoils($unitId, $reference, $quantity){
    print "readCoils: START\n";
    // connect
    $this->connect();
    // send FC 1
    $packet = $this->readCoilsPacketBuilder($unitId, $reference, $quantity);
    print $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    print $this->printPacket($rpacket);    
    // parse packet    
    $receivedData = $this->readCoilsParser($rpacket, $quantity);
    // disconnect
    $this->disconnect();
    print "readCoils: DONE\n";
    // return
    return $receivedData;
  }
  
  /**
   * fc1
   * 
   * Alias to {@link readCoils} method
   * 
   * @param type $unitId
   * @param type $reference
   * @param type $quantity
   * @return type 
   */
  function fc1($unitId, $reference, $quantity){
    return $this->readCoils($unitId, $reference, $quantity);
  }
  
  /**
   * readCoilsPacketBuilder
   * 
   * FC1 packet builder - read coils
   * 
   * @param type $unitId
   * @param type $reference
   * @param type $quantity
   * @return type 
   */
  private function readCoilsPacketBuilder($unitId, $reference, $quantity){
    $dataLen = 0;
    // build data section
    $buffer1 = "";
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(1);              // FC 1 = 1(0x01)
    // build body - read section    
    $buffer2 .= iecType::iecINT($reference);      // refnumber = 12288      
    $buffer2 .= iecType::iecINT($quantity);       // quantity
    $dataLen += 5;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID
    $buffer3 .= iecType::iecINT(0);               // protocol ID
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * readCoilsParser
   * 
   * FC 1 response parser
   * 
   * @param type $packet
   * @param type $quantity
   * @return type 
   */
  private function readCoilsParser($packet, $quantity){    
    $data = array();
    // check Response code
    $this->responseCode($packet);
    // get data from stream
    for($i=0;$i<ord($packet[8]);$i++){
      $data[$i] = ord($packet[9+$i]);
    }    
    // get bool values to array
    $data_bolean_array = array();
    $di = 0;
    foreach($data as $value){
      for($i=0;$i<8;$i++){
        if($di == $quantity) continue;
        // get boolean value 
        $v = ($value >> $i) & 0x01;
        // build boolean array
        if($v == 0){
          $data_bolean_array[] = FALSE;
        } else {
          $data_bolean_array[] = TRUE;
        }
        $di++;
      }
    }
    return $data_bolean_array;
  }
  
  /**
   * readInputDiscretes
   * 
   * Modbus function FC 2(0x02) - Read Input Discretes
   * 
   * Reads {@link $quantity} of Inputs (boolean) from reference 
   * {@link $reference} of a memory of a Modbus device given by 
   * {@link $unitId}.
   * 
   * @param type $unitId
   * @param type $reference
   * @param type $quantity 
   */
  function readInputDiscretes($unitId, $reference, $quantity){
    print "readInputDiscretes: START\n";
    // connect
    $this->connect();
    // send FC 2
    $packet = $this->readInputDiscretesPacketBuilder($unitId, $reference, $quantity);
    print $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    print $this->printPacket($rpacket);    
    // parse packet    
    $receivedData = $this->readInputDiscretesParser($rpacket, $quantity);
    // disconnect
    $this->disconnect();
    print "readInputDiscretes: DONE\n";
    // return
    return $receivedData;
  }
  
  /**
   * fc2
   * 
   * Alias to {@link readInputDiscretes} method
   * 
   * @param type $unitId
   * @param type $reference
   * @param type $quantity
   * @return type 
   */
  function fc2($unitId, $reference, $quantity){
    return $this->readInputDiscretes($unitId, $reference, $quantity);
  }
  
  /**
   * readInputDiscretesPacketBuilder
   * 
   * FC2 packet builder - read coils
   * 
   * @param type $unitId
   * @param type $reference
   * @param type $quantity
   * @return type 
   */
  private function readInputDiscretesPacketBuilder($unitId, $reference, $quantity){
    $dataLen = 0;
    // build data section
    $buffer1 = "";
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(2);              // FC 2 = 2(0x02)
    // build body - read section    
    $buffer2 .= iecType::iecINT($reference);      // refnumber = 12288      
    $buffer2 .= iecType::iecINT($quantity);       // quantity
    $dataLen += 5;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID
    $buffer3 .= iecType::iecINT(0);               // protocol ID
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * readInputDiscretesParser
   * 
   * FC 2 response parser, alias to FC 1 parser i.e. readCoilsParser.
   * 
   * @param type $packet
   * @param type $quantity
   * @return type 
   */
  private function readInputDiscretesParser($packet, $quantity){
    return $this->readCoilsParser($packet, $quantity);
  }
  
  /**
   * readMultipleRegisters
   *
   * Modbus function FC 3(0x03) - Read Multiple Registers.
   * 
   * This function reads {@link $quantity} of Words (2 bytes) from reference 
   * {@link $referenceRead} of a memory of a Modbus device given by 
   * {@link $unitId}.
   *    
   *
   * @param int $unitId usually ID of Modbus device 
   * @param int $reference Reference in the device memory to read data (e.g. in device WAGO 750-841, memory MW0 starts at address 12288).
   * @param int $quantity Amounth of the data to be read from device.
   * @return false|Array Success flag or array of received data.
   */
  function readMultipleRegisters($unitId, $reference, $quantity){
    print "readMultipleRegisters: START\n";
    // connect
    $this->connect();
    // send FC 3    
    $packet = $this->readMultipleRegistersPacketBuilder($unitId, $reference, $quantity);
    print $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    print $this->printPacket($rpacket);    
    // parse packet    
    $receivedData = $this->readMultipleRegistersParser($rpacket);
    // disconnect
    $this->disconnect();
    print "readMultipleRegisters: DONE\n";
    // return
    return $receivedData;
  }
  
  /**
   * fc3
   *
   * Alias to {@link readMultipleRegisters} method.
   *
   * @param int $unitId
   * @param int $reference
   * @param int $quantity
   * @return false|Array
   */
  function fc3($unitId, $reference, $quantity){
    return $this->readMultipleRegisters($unitId, $reference, $quantity);
  }  
  
  /**
   * readMultipleRegistersPacketBuilder
   *
   * Packet FC 3 builder - read multiple registers
   *
   * @param int $unitId
   * @param int $reference
   * @param int $quantity
   * @return string
   */
  private function readMultipleRegistersPacketBuilder($unitId, $reference, $quantity){
    $dataLen = 0;
    // build data section
    $buffer1 = "";
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(3);             // FC 3 = 3(0x03)
    // build body - read section    
    $buffer2 .= iecType::iecINT($reference);  // refnumber = 12288      
    $buffer2 .= iecType::iecINT($quantity);       // quantity
    $dataLen += 5;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID
    $buffer3 .= iecType::iecINT(0);               // protocol ID
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * readMultipleRegistersParser
   *
   * FC 3 response parser
   *
   * @param string $packet
   * @return array
   */
  private function readMultipleRegistersParser($packet){    
    $data = array();
    // check Response code
    $this->responseCode($packet);
    // get data
    for($i=0;$i<ord($packet[8]);$i++){
      $data[$i] = ord($packet[9+$i]);
    }    
    return $data;
  }
  
  /**
   * readMultipleInputRegisters
   *
   * Modbus function FC 4(0x04) - Read Multiple Input Registers.
   * 
   * This function reads {@link $quantity} of Words (2 bytes) from reference 
   * {@link $referenceRead} of a memory of a Modbus device given by 
   * {@link $unitId}.
   *    
   *
   * @param int $unitId usually ID of Modbus device 
   * @param int $reference Reference in the device memory to read data.
   * @param int $quantity Amounth of the data to be read from device.
   * @return false|Array Success flag or array of received data.
   */
  function readMultipleInputRegisters($unitId, $reference, $quantity){
    print "readMultipleInputRegisters: START\n";
    // connect
    $this->connect();
    // send FC 4    
    $packet = $this->readMultipleInputRegistersPacketBuilder($unitId, $reference, $quantity);
    print $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    print $this->printPacket($rpacket);    
    // parse packet    
    $receivedData = $this->readMultipleInputRegistersParser($rpacket);
    // disconnect
    $this->disconnect();
    print "readMultipleInputRegisters: DONE\n";
    // return
    return $receivedData;
  }
  
  /**
   * fc4
   *
   * Alias to {@link readMultipleInputRegisters} method.
   *
   * @param int $unitId
   * @param int $reference
   * @param int $quantity
   * @return false|Array
   */
  function fc4($unitId, $reference, $quantity){
    return $this->readMultipleInputRegisters($unitId, $reference, $quantity);
  }  
  
  /**
   * readMultipleInputRegistersPacketBuilder
   *
   * Packet FC 4 builder - read multiple input registers
   *
   * @param int $unitId
   * @param int $reference
   * @param int $quantity
   * @return string
   */
  private function readMultipleInputRegistersPacketBuilder($unitId, $reference, $quantity){
    $dataLen = 0;
    // build data section
    $buffer1 = "";
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(4);                                                // FC 4 = 4(0x04)
    // build body - read section    
    $buffer2 .= iecType::iecINT($reference);                                        // refnumber = 12288      
    $buffer2 .= iecType::iecINT($quantity);                                         // quantity
    $dataLen += 5;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));                                     // transaction ID
    $buffer3 .= iecType::iecINT(0);                                                 // protocol ID
    $buffer3 .= iecType::iecINT($dataLen + 1);                                      // lenght
    $buffer3 .= iecType::iecBYTE($unitId);                                          // unit ID
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * readMultipleInputRegistersParser
   *
   * FC 4 response parser
   *
   * @param string $packet
   * @return array
   */
  private function readMultipleInputRegistersParser($packet){    
    $data = array();
    // check Response code
    $this->responseCode($packet);
    // get data
    for($i=0;$i<ord($packet[8]);$i++){
      $data[$i] = ord($packet[9+$i]);
    }    
    return $data;
  }
  
  /**
   * writeSingleCoil
   *
   * Modbus function FC5(0x05) - Write Single Register.
   *
   * This function writes {@link $data} single coil at {@link $reference} position of 
   * memory of a Modbus device given by {@link $unitId}.
   *
   *
   * @param int $unitId usually ID of Modbus device 
   * @param int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at address 12288)
   * @param array $data value to be written (TRUE|FALSE).
   * @return bool Success flag
   */       
  function writeSingleCoil($unitId, $reference, $data){
    print "writeSingleCoil: START\n";
    // connect
    $this->connect();
    // send FC5    
    $packet = $this->writeSingleCoilPacketBuilder($unitId, $reference, $data);
    print $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    print $this->printPacket($rpacket);    
    // parse packet
    $this->writeSingleCoilParser($rpacket);
    // disconnect
    $this->disconnect();
    print "writeSingleCoil: DONE\n";
    return true;
  }


  /**
   * fc5
   *
   * Alias to {@link writeSingleCoil} method
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @param array $dataTypes
   * @return bool
   */
  function fc5($unitId, $reference, $data, $dataTypes){    
    return $this->writeSingleCoil($unitId, $reference, $data, $dataTypes);
  }


  /**
   * writeSingleCoilPacketBuilder
   *
   * Packet builder FC5 - WRITE single register
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @param array $dataTypes
   * @return string
   */
  private function writeSingleCoilPacketBuilder($unitId, $reference, $data){
    $dataLen = 0;
    // build data section
    $buffer1 = "";
    foreach($data as $key=>$dataitem) {
      if($dataitem == TRUE){
       $buffer1 = iecType::iecINT(0xFF00);
      } else {
       $buffer1 = iecType::iecINT(0x0000);
      };
    };
    $dataLen += 2;
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(5);             // FC5 = 5(0x05)
    $buffer2 .= iecType::iecINT($reference);      // refnumber = 12288
    $dataLen += 3;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID    
    $buffer3 .= iecType::iecINT(0);               // protocol ID    
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght    
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID    
    
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * writeSingleCoilParser
   *
   * FC5 response parser
   *
   * @param string $packet
   * @return bool
   */
  private function writeSingleCoilParser($packet){
    $this->responseCode($packet);
    return true;
  }
  
  /**
   * writeSingleRegister
   *
   * Modbus function FC6(0x06) - Write Single Register.
   *
   * This function writes {@link $data} single word value at {@link $reference} position of 
   * memory of a Modbus device given by {@link $unitId}.
   *
   *
   * @param int $unitId usually ID of Modbus device 
   * @param int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at address 12288)
   * @param array $data Array of values to be written.
   * @param array $dataTypes Array of types of values to be written. The array should consists of string "INT", "DINT" and "REAL".    
   * @return bool Success flag
   */       
  function writeSingleRegister($unitId, $reference, $data, $dataTypes){
    print "writeSingleRegister: START\n";
    // connect
    $this->connect();
    // send FC6    
    $packet = $this->writeSingleRegisterPacketBuilder($unitId, $reference, $data, $dataTypes);
    print $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    print $this->printPacket($rpacket);    
    // parse packet
    $this->writeSingleRegisterParser($rpacket);
    // disconnect
    $this->disconnect();
    print "writeSingleRegister: DONE\n";
    return true;
  }


  /**
   * fc6
   *
   * Alias to {@link writeSingleRegister} method
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @param array $dataTypes
   * @return bool
   */
  function fc6($unitId, $reference, $data, $dataTypes){    
    return $this->writeSingleRegister($unitId, $reference, $data, $dataTypes);
  }


  /**
   * writeSingleRegisterPacketBuilder
   *
   * Packet builder FC6 - WRITE single register
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @param array $dataTypes
   * @return string
   */
  private function writeSingleRegisterPacketBuilder($unitId, $reference, $data, $dataTypes){
    $dataLen = 0;
    // build data section
    $buffer1 = "";
    foreach($data as $key=>$dataitem) {
      $buffer1 .= iecType::iecINT($dataitem);   // register values x
      $dataLen += 2;
      break;
    }
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(6);             // FC6 = 6(0x06)
    $buffer2 .= iecType::iecINT($reference);      // refnumber = 12288
    $dataLen += 3;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID    
    $buffer3 .= iecType::iecINT(0);               // protocol ID    
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght    
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID    
    
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * writeSingleRegisterParser
   *
   * FC6 response parser
   *
   * @param string $packet
   * @return bool
   */
  private function writeSingleRegisterParser($packet){
    $this->responseCode($packet);
    return true;
  }

  
  /**
   * writeMultipleCoils
   * 
   * Modbus function FC15(0x0F) - Write Multiple Coils
   *
   * This function writes {@link $data} array at {@link $reference} position of 
   * memory of a Modbus device given by {@link $unitId}. 
   * 
   * @param type $unitId
   * @param type $reference
   * @param type $data
   * @return type 
   */
  function writeMultipleCoils($unitId, $reference, $data){
    print "writeMultipleCoils: START\n";
    // connect
    $this->connect();
    // send FC15    
    $packet = $this->writeMultipleCoilsPacketBuilder($unitId, $reference, $data);
    print $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    print $this->printPacket($rpacket);    
    // parse packet
    $this->writeMultipleCoilsParser($rpacket);
    // disconnect
    $this->disconnect();
    print "writeMultipleCoils: DONE\n";
    return true;
  }
  
  /**
   * fc15
   *
   * Alias to {@link writeMultipleCoils} method
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @return bool
   */
  function fc15($unitId, $reference, $data){    
    return $this->writeMultipleCoils($unitId, $reference, $data);
  }
  
  /**
   * writeMultipleCoilsPacketBuilder
   *
   * Packet builder FC15 - Write multiple coils
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @return string
   */
  private function writeMultipleCoilsPacketBuilder($unitId, $reference, $data){
    $dataLen = 0;    
    // build bool stream to the WORD array
    $data_word_stream = array();
    $data_word = 0;
    $shift = 0;    
    for($i=0;$i<count($data);$i++) {
      if((($i % 8) == 0) && ($i > 0)) {
        $data_word_stream[] = $data_word;
        $shift = 0;
        $data_word = 0;
        $data_word |= (0x01 && $data[$i]) << $shift;
        $shift++;
      }
      else {
        $data_word |= (0x01 && $data[$i]) << $shift;
        $shift++;
      }
    }
    $data_word_stream[] = $data_word;
    // show binary stream to status string
    foreach($data_word_stream as $d){
        print sprintf("byte=b%08b\n", $d);
    }    
    // build data section
    $buffer1 = "";
    foreach($data_word_stream as $key=>$dataitem) {
        $buffer1 .= iecType::iecBYTE($dataitem);   // register values x
        $dataLen += 1;
    }
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(15);             // FC 15 = 15(0x0f)
    $buffer2 .= iecType::iecINT($reference);      // refnumber = 12288      
    $buffer2 .= iecType::iecINT(count($data));      // bit count      
    $buffer2 .= iecType::iecBYTE((count($data)+7)/8);       // byte count
    $dataLen += 6;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID    
    $buffer3 .= iecType::iecINT(0);               // protocol ID    
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght    
    $buffer3 .= iecType::iecBYTE($unitId);        // unit ID    
     
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * writeMultipleCoilsParser
   *
   * FC15 response parser
   *
   * @param string $packet
   * @return bool
   */
  private function writeMultipleCoilsParser($packet){
    $this->responseCode($packet);
    return true;
  }
  
  /**
   * writeMultipleRegister
   *
   * Modbus function FC16(0x10) - Write Multiple Register.
   *
   * This function writes {@link $data} array at {@link $reference} position of 
   * memory of a Modbus device given by {@link $unitId}.
   *
   *
   * @param int $unitId usually ID of Modbus device 
   * @param int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at address 12288)
   * @param array $data Array of values to be written.
   * @param array $dataTypes Array of types of values to be written. The array should consists of string "INT", "DINT" and "REAL".    
   * @return bool Success flag
   */       
  function writeMultipleRegister($unitId, $reference, $data, $dataTypes){
    print "writeMultipleRegister: START\n";
    // connect
    $this->connect();
    // send FC16    
    $packet = $this->writeMultipleRegisterPacketBuilder($unitId, $reference, $data, $dataTypes);
    print $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    print $this->printPacket($rpacket);    
    // parse packet
    $this->writeMultipleRegisterParser($rpacket);
    // disconnect
    $this->disconnect();
    print "writeMultipleRegister: DONE\n";
    return true;
  }


  /**
   * fc16
   *
   * Alias to {@link writeMultipleRegister} method
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @param array $dataTypes
   * @return bool
   */
  function fc16($unitId, $reference, $data, $dataTypes){    
    return $this->writeMultipleRegister($unitId, $reference, $data, $dataTypes);
  }


  /**
   * writeMultipleRegisterPacketBuilder
   *
   * Packet builder FC16 - WRITE multiple register
   *     e.g.: 4dd90000000d0010300000030603e807d00bb8
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @param array $dataTypes
   * @return string
   */
  private function writeMultipleRegisterPacketBuilder($unitId, $reference, $data, $dataTypes){
    $dataLen = 0;
    // build data section
    $buffer1 = "";
    foreach($data as $key=>$dataitem) {
      if($dataTypes[$key]=="INT"){
        $buffer1 .= iecType::iecINT($dataitem);   // register values x
        $dataLen += 2;
      }
      elseif($dataTypes[$key]=="DINT"){
        $buffer1 .= iecType::iecDINT($dataitem, $this->endianness);   // register values x
        $dataLen += 4;
      }
      elseif($dataTypes[$key]=="REAL") {
        $buffer1 .= iecType::iecREAL($dataitem, $this->endianness);   // register values x
        $dataLen += 4;
      }       
      else{
        $buffer1 .= iecType::iecINT($dataitem);   // register values x
        $dataLen += 2;
      }
    }
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(16);             // FC 16 = 16(0x10)
    $buffer2 .= iecType::iecINT($reference);      // refnumber = 12288      
    $buffer2 .= iecType::iecINT($dataLen/2);        // word count      
    $buffer2 .= iecType::iecBYTE($dataLen);     // byte count
    $dataLen += 6;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID    
    $buffer3 .= iecType::iecINT(0);               // protocol ID    
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght    
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID    
    
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * writeMultipleRegisterParser
   *
   * FC16 response parser
   *
   * @param string $packet
   * @return bool
   */
  private function writeMultipleRegisterParser($packet){
    $this->responseCode($packet);
    return true;
  }

  /**
   * readWriteRegisters
   *
   * Modbus function FC23(0x17) - Read Write Registers.
   * 
   * This function writes {@link $data} array at reference {@link $referenceWrite} 
   * position of memory of a Modbus device given by {@link $unitId}. Simultanously, 
   * it returns {@link $quantity} of Words (2 bytes) from reference {@link $referenceRead}.
   *
   *
   * @param int $unitId usually ID of Modbus device 
   * @param int $referenceRead Reference in the device memory to read data (e.g. in device WAGO 750-841, memory MW0 starts at address 12288).
   * @param int $quantity Amounth of the data to be read from device.   
   * @param int $referenceWrite Reference in the device memory to write data.
   * @param array $data Array of values to be written.
   * @param array $dataTypes Array of types of values to be written. The array should consists of string "INT", "DINT" and "REAL".   
   * @return false|Array Success flag or array of data.
   */
  function readWriteRegisters($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes){
    print "readWriteRegisters: START\n";
    // connect
    $this->connect();
    // send FC23    
    $packet = $this->readWriteRegistersPacketBuilder($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes);
    print $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    print $this->printPacket($rpacket);
    // parse packet
    $receivedData = $this->readWriteRegistersParser($rpacket);
    // disconnect
    $this->disconnect();
    print "writeMultipleRegister: DONE\n";
    // return
    return $receivedData;
  }
  
  /**
   * fc23
   *
   * Alias to {@link readWriteRegisters} method.
   *
   * @param int $unitId
   * @param int $referenceRead
   * @param int $quantity
   * @param int $referenceWrite
   * @param array $data
   * @param array $dataTypes
   * @return false|Array
   */
  function fc23($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes){
    return $this->readWriteRegisters($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes);
  }
  
  /**
   * readWriteRegistersPacketBuilder
   *
   * Packet FC23 builder - READ WRITE registers
   *
   *
   * @param int $unitId
   * @param int $referenceRead
   * @param int $quantity
   * @param int $referenceWrite
   * @param array $data
   * @param array $dataTypes
   * @return string
   */
  private function readWriteRegistersPacketBuilder($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes){
    $dataLen = 0;
    // build data section
    $buffer1 = "";
    foreach($data as $key => $dataitem) {
      if($dataTypes[$key]=="INT"){
        $buffer1 .= iecType::iecINT($dataitem);   // register values x
        $dataLen += 2;
      }
      elseif($dataTypes[$key]=="DINT"){
        $buffer1 .= iecType::iecDINT($dataitem, $this->endianness);   // register values x
        $dataLen += 4;
      }
      elseif($dataTypes[$key]=="REAL") {
        $buffer1 .= iecType::iecREAL($dataitem, $this->endianness);   // register values x
        $dataLen += 4;
      }       
      else{
        $buffer1 .= iecType::iecINT($dataitem);   // register values x
        $dataLen += 2;
      }
    }
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(23);             // FC 23 = 23(0x17)
    // build body - read section    
    $buffer2 .= iecType::iecINT($referenceRead);  // refnumber = 12288      
    $buffer2 .= iecType::iecINT($quantity);       // quantity
    // build body - write section    
    $buffer2 .= iecType::iecINT($referenceWrite); // refnumber = 12288      
    $buffer2 .= iecType::iecINT($dataLen/2);      // word count      
    $buffer2 .= iecType::iecBYTE($dataLen);       // byte count
    $dataLen += 10;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID    
    $buffer3 .= iecType::iecINT(0);               // protocol ID    
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght    
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID    
    
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  

  /**
   * readWriteRegistersParser
   *
   * FC23 response parser
   *
   * @param string $packet
   * @return array
   */
  private function readWriteRegistersParser($packet){
    $data = array();
    // if not exception
    if(!$this->responseCode($packet))
      return false;
    // get data
    for($i=0;$i<ord($packet[8]);$i++){
      $data[$i] = ord($packet[9+$i]);
    }    
    return $data;
  }

  /**
   * byte2hex
   *
   * Parse data and get it to the Hex form
   *
   * @param char $value
   * @return string
   */
  private function byte2hex($value){
    $h = dechex(((int)$value >> 4) & 0x0F);
    $l = dechex((int)$value & 0x0F);
    return "$h$l";
  }

  /**
   * printPacket
   *
   * Print a packet in the hex form
   *
   * @param string $packet
   * @return string
   */
  private function printPacket($packet){
    $str = "";   
    $str .= "Packet: "; 
    for($i=0;$i<strlen($packet);$i++){
      $str .= $this->byte2hex(ord($packet[$i]));
    }
    $str .= "\n";
    return $str;
  }
}


#########################################

/**
 * Phpmodbus Copyright (c) 2004, 2013 Jan Krakora
 *
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt.
 *
 * @author Jan Krakora
 * @copyright Copyright (c) 2004, 2013 Jan Krakora
 * @license PhpModbus license
 * @category Phpmodbus
 * @package Phpmodbus
 * @version $id$
 */

/**
 * IecType
 *
 * The class includes set of IEC-1131 data type functions that converts a PHP
 * data types to a IEC data type.
 *
 * @author Jan Krakora
 * @copyright  Copyright (c) 2004, 2010 Jan Krakora
 * @package Phpmodbus
 */
class IecType {

    /**
     * iecBYTE
     *
     * Converts a value to IEC-1131 BYTE data type
     *
     * @param value value from 0 to 255
     * @return value IEC BYTE data type
     *
     */
    public static function iecBYTE($value) {
        return chr($value & 0xFF);
    }

    /**
     * iecINT
     *
     * Converts a value to IEC-1131 INT data type
     *
     * @param value value to be converted
     * @return value IEC-1131 INT data type
     *
     */
    public static function iecINT($value) {
        return self::iecBYTE(($value >> 8) & 0x00FF) .
                self::iecBYTE(($value & 0x00FF));
    }

    /**
     * iecDINT
     *
     * Converts a value to IEC-1131 DINT data type
     *
     * @param value value to be converted
     * @param value endianness defines endian codding (little endian == 0, big endian == 1)
     * @return value IEC-1131 INT data type
     *
     */
    public static function iecDINT($value, $endianness = 0) {
        // result with right endianness
        return self::endianness($value, $endianness);
    }

    /**
     * iecREAL
     *
     * Converts a value to IEC-1131 REAL data type. The function uses function  @use float2iecReal.
     *
     * @param value value to be converted
     * @param value endianness defines endian codding (little endian == 0, big endian == 1)
     * @return value IEC-1131 REAL data type
     */
    public static function iecREAL($value, $endianness = 0) {
        // iecREAL representation
        $real = self::float2iecReal($value);
        // result with right endianness
        return self::endianness($real, $endianness);
    }

    /**
     * float2iecReal
     *
     * This function converts float value to IEC-1131 REAL single precision form.
     *
     * For more see [{@link http://en.wikipedia.org/wiki/Single_precision Single precision on Wiki}] or
     * [{@link http://de.php.net/manual/en/function.base-convert.php PHP base_convert function commentary}, Todd Stokes @ Georgia Tech 21-Nov-2007] or
     * [{@link http://www.php.net/manual/en/function.pack.php PHP pack/unpack functionality}]
     *
     * @param float value to be converted
     * @return value IEC REAL data type
     */
    private static function float2iecReal($value) {
        // get float binary string
        $float = pack("f", $value);
        // set 32-bit unsigned integer of the float
        $w = unpack("L", $float);
        return $w[1];
    }

    /**
     * endianness
     *
     * Make endianess as required.
     * For more see http://en.wikipedia.org/wiki/Endianness
     *
     * @param int $value
     * @param bool $endianness
     * @return int
     */
    private static function endianness($value, $endianness = 0) {
        if ($endianness == 0)
            return
                    self::iecBYTE(($value >> 8) & 0x000000FF) .
                    self::iecBYTE(($value & 0x000000FF)) .
                    self::iecBYTE(($value >> 24) & 0x000000FF) .
                    self::iecBYTE(($value >> 16) & 0x000000FF);
        else
            return
                    self::iecBYTE(($value >> 24) & 0x000000FF) .
                    self::iecBYTE(($value >> 16) & 0x000000FF) .
                    self::iecBYTE(($value >> 8) & 0x000000FF) .
                    self::iecBYTE(($value & 0x000000FF));
    }

}



/**
 * Phpmodbus Copyright (c) 2004, 2012 Jan Krakora
 *
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt.
 *
 * @author Jan Krakora
 * @copyright Copyright (c) 2004, 2012 Jan Krakora
 * @license PhpModbus license
 * @category Phpmodbus
 * @package Phpmodbus
 * @version $id$
 *
 */

/**
 * PhpType
 *
 * The class includes set of methods that convert the received Modbus data
 * (array of bytes) to the PHP data type, i.e. signed int, unsigned int and float.
 *
 * @author Jan Krakora
 * @copyright  Copyright (c) 2004, 2012 Jan Krakora
 * @package Phpmodbus
 *
 */
class PhpType {

    /**
     * bytes2float
     *
     * The function converts array of 4 bytes to float. The return value
     * depends on order of the input bytes (endianning).
     *
     * @param array $values
     * @param bool $endianness
     * @return float
     */
    public static function bytes2float($values, $endianness = 0) {
        $data = array();
        $real = 0;

        // Set the array to correct form
        $data = self::checkData($values);
        // Combine bytes
        $real = self::combineBytes($data, $endianness);
        // Convert the real value to float
        return (float) self::real2float($real);
    }

    /**
     * bytes2signedInt
     *
     * The function converts array of 2 or 4 bytes to signed integer.
     * The return value depends on order of the input bytes (endianning).
     *
     * @param array $values
     * @param bool $endianness
     * @return int
     */
    public static function bytes2signedInt($values, $endianness = 0) {
        $data = array();
        $int = 0;
        // Set the array to correct form
        $data = self::checkData($values);
        // Combine bytes
        $int = self::combineBytes($data, $endianness);
        // In the case of signed 2 byte value convert it to 4 byte one
        if ((count($values) == 2) && ((0x8000 & $int) > 0)) {
            $int = 0xFFFF8000 | $int;
        }
        // Convert the value
        return (int) self::dword2signedInt($int);
    }

    /**
     * bytes2unsignedInt
     *
     * The function converts array of 2 or 4 bytes to unsigned integer.
     * The return value depends on order of the input bytes (endianning).
     *
     * @param array $values
     * @param bool $endianness
     * @return int|float
     */
    public static function bytes2unsignedInt($values, $endianness = 0) {
        $data = array();
        $int = 0;
        // Set the array to correct form
        $data = self::checkData($values);
        // Combine bytes
        $int = self::combineBytes($data, $endianness);
        // Convert the value
        return self::dword2unsignedInt($int);
    }

    /**
     * bytes2string
     *
     * The function converts an values array to the string. The function detects
     * the end of the string by 0x00 character as defined by string standards.
     *
     * @param array $values
     * @param bool $endianness
     * @return string
     */
    public static function bytes2string($values, $endianness = 0) {
        // Prepare string variable
        $str = "";
        // Parse the received data word array
        for($i=0;$i<count($values);$i+=2) {
            if ($endianness) {
                if($values[$i] != 0)
                    $str .= chr($values[$i]);
                else
                    break;
                if($values[$i+1] != 0)
                    $str .= chr($values[$i+1]);
                else
                    break;
            }
            else {
                if($values[$i+1] != 0)
                    $str .= chr($values[$i+1]);
                else
                    break;
                if($values[$i] != 0)
                    $str .= chr($values[$i]);
                else
                    break;
            }
        }
        // return string
        return $str;
    }

    /**
     * real2float
     *
     * This function converts a value in IEC-1131 REAL single precision form to float.
     *
     * For more see [{@link http://en.wikipedia.org/wiki/Single_precision Single precision on Wiki}] or
     * [{@link http://de.php.net/manual/en/function.base-convert.php PHP base_convert function commentary}, Todd Stokes @ Georgia Tech 21-Nov-2007] or
     * [{@link http://www.php.net/manual/en/function.pack.php PHP pack/unpack functionality}]
     *
     * @param value value in IEC REAL data type to be converted
     * @return float float value
     */
    private static function real2float($value) {
        // get unsigned long
        $ulong = pack("L", $value);
        // set float
        $float = unpack("f", $ulong);
        
        return $float[1];
    }

    /**
     * dword2signedInt
     *
     * Switch double word to signed integer
     *
     * @param int $value
     * @return int
     */
    private static function dword2signedInt($value) {
        if ((0x80000000 & $value) != 0) {
            return -(0x7FFFFFFF & ~$value)-1;
        } else {
            return (0x7FFFFFFF & $value);
        }
    }

    /**
     * dword2signedInt
     *
     * Switch double word to unsigned integer
     *
     * @param int $value
     * @return int|float
     */
    private static function dword2unsignedInt($value) {
        if ((0x80000000 & $value) != 0) {
            return ((float) (0x7FFFFFFF & $value)) + 2147483648;
        } else {
            return (int) (0x7FFFFFFF & $value);
        }
    }

    /**
     * checkData
     *
     * Check if the data variable is array, and check if the values are numeric
     *
     * @param int $data
     * @return int
     */
    private static function checkData($data) {
        // Check the data
        if (!is_array($data) ||
                count($data)<2 ||
                count($data)>4 ||
                count($data)==3) {
            throw new Exception('The input data should be an array of 2 or 4 bytes.');
        }
        // Fill the rest of array by zeroes
        if (count($data) == 2) {
            $data[2] = 0;
            $data[3] = 0;
        }
        // Check the values to be number
        if (!is_numeric($data[0]) ||
                !is_numeric($data[1]) ||
                !is_numeric($data[2]) ||
                !is_numeric($data[3])) {
            throw new Exception('Data are not numeric or the array keys are not indexed by 0,1,2 and 3');
        }

        return $data;
    }

    /**
     * combineBytes
     *
     * Combine bytes together
     *
     * @param int $data
     * @param bool $endianness
     * @return int
     */
    private static function combineBytes($data, $endianness) {
        $value = 0;
        // Combine bytes
        if ($endianness == 0)
            $value = (($data[3] & 0xFF)<<16) |
                    (($data[2] & 0xFF)<<24) |
                    (($data[1] & 0xFF)) |
                    (($data[0] & 0xFF)<<8);
        else
            $value = (($data[3] & 0xFF)<<24) |
                    (($data[2] & 0xFF)<<16) |
                    (($data[1] & 0xFF)<<8) |
                    (($data[0] & 0xFF));

        return $value;
    }
}
