#include <OneWire.h>
#include <DallasTemperature.h>

//the pin you connect the ds18b20 to
#define DS18B20 2

OneWire ourWire(DS18B20);
DallasTemperature sensors(&ourWire);

void setup()
{
  Serial.begin(9600);
  delay(3000);
  sensors.begin();
}

void loop()
{
  //read temperature and output via serial
  sensors.requestTemperatures();
  Serial.println(sensors.getTempCByIndex(0));
}
