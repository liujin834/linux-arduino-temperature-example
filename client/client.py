import serial

ser = serial.Serial('/dev/ttyACM0', 9600, timeout=1)
while True:
    data = ser.read(100)
    print str(data)