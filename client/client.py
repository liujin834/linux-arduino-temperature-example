import serial
import time

ser = serial.Serial('/dev/cu.usbmodem1431', 9600, timeout=1)
while True:
    # serial_string = ser.read(5)
    string = ser.readline()
    string = string.replace("\n", "")
    string = string.replace("\r", "")
    string.strip()

    if len(string) == 5:
        print str(string)
    else:
        print "abort"
    time.sleep(3)
