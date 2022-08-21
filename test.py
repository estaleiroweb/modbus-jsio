#!/usr/bin/python3

"""
pip install testresources
pip install pyModbusTCP
pip install pymodbus


pip3 install -U urllib3
pip3 install -U pymodbus repl serial documentation development
"""

from pymodbus.client import ModbusTcpClient

client = ModbusTcpClient('192.168.1.115')
#client.write_coil(1, True)
result = client.read_coils(1,1)
print(result.bits[0])
client.close()