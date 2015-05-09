import serial
import time
import pycurl
import cStringIO

ser = serial.Serial('/dev/cu.usbmodem1431', 9600, timeout=1)
while True:
    # serial_string = ser.read(5)
    string = ser.readline()
    string = string.replace("\n", "")
    string = string.replace("\r", "")
    string.strip()

    if len(string) == 5:
        print str(string)
        c = pycurl.Curl()
        host = 'http://localhost:8009/?'
        url = [
            'client=db18fda-cdf-401f-a560-0cfdfaead4e',
            'ts_observe='+time.strftime("%Y-%m-%d")+'%20'+time.strftime("%H:%M:%S"),
            'val='+str(string)
        ]

        uri = host + '&'.join(url)

        c.setopt(c.URL, uri)
        c.perform()

        buf = cStringIO.StringIO()
        print buf.getvalue()
        buf.close()

        time.sleep(600)
    else:
        print "abort"
        time.sleep(1)
