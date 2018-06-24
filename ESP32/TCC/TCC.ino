#include <Arduino.h>
#define ADC_BITS    12

#include <WiFi.h>
#include <WiFiMulti.h>

#include <HTTPClient.h>

#define USE_SERIAL Serial

WiFiMulti wifiMulti;

IPAddress local_IP(192, 168, 0,81);
IPAddress gateway(192, 168, 0, 1);
IPAddress subnet(255, 255, 0, 0);
IPAddress primaryDNS(8, 8, 8, 8); 
IPAddress secondaryDNS(8, 8, 4, 4);


#include "EmonLib.h"                   
EnergyMonitor emon1; 
EnergyMonitor emon2;
EnergyMonitor emon3;
EnergyMonitor emon4;
EnergyMonitor emon5;

EnergyMonitor emon6;   
EnergyMonitor emon7; 
EnergyMonitor emon8; 

EnergyMonitor emon9;
EnergyMonitor emon10;
EnergyMonitor emon11;
EnergyMonitor emon12;
EnergyMonitor emon13;


float cont;
float acumulado1,acumulado2,acumulado3,acumulado4,acumulado5;
float v1,v2, v3;
float f1,f2,f3,f4,f5;
String conteudoweb;

int pino[]={23,22,1,3,21,19,18,5};
   int multiplex(int ativar) 
   {
        for(int a=0;a<8;a++)
        {
            if(ativar == a)
            {
            digitalWrite(pino[a], HIGH); 
            }
            else
            {
              digitalWrite(pino[a], LOW); 
            }
        }
    }
    
void setup()
{ 
    USE_SERIAL.begin(115200);

    USE_SERIAL.println();
    USE_SERIAL.println();
    USE_SERIAL.println();

    for(uint8_t t = 4; t > 0; t--) {
        USE_SERIAL.printf("[SETUP] WAIT %d...\n", t);
        USE_SERIAL.flush();
        delay(1000);
    }
    //wifiMulti.addAP("Dido", "Maestrelli@10");

    wifiMulti.addAP("Maestrelli-2g", "Maestrelli@10");

   Serial.begin(115200);

     if (!WiFi.config(local_IP, gateway, subnet, primaryDNS, secondaryDNS)) {
    Serial.println("STA Failed to configure");
  }
  
    emon1.current(36, 7);  
    emon2.current(39, 7); 
    emon3.current(34, 7);
    emon4.current(35, 7);
    emon5.current(32, 7);
    
    emon6.voltage(33, 70, 1.7);  // Voltage: input pin, calibration, phase_shift
    emon7.voltage(33, 70, 1.7);  // Voltage: input pin, calibration, phase_shift
    emon8.voltage(33, 70, 1.7);  // Voltage: input pin, calibration, phase_shift

    emon9.voltage(33, 70, 1.7);
    emon10.voltage(33, 70, 1.7);
    emon11.voltage(33, 70, 1.7);
    emon12.voltage(33, 70, 1.7);
    emon13.voltage(33, 70, 1.7);

    
  for(int a=0;a<8;a++)
  {
  pinMode(pino[a], OUTPUT);
  }  
  
}
 
void loop()
{
   multiplex(0);
   emon6.calcVI(200,200);//Va
   v1   = emon6.Vrms;
   
   multiplex(1);
   emon7.calcVI(200,2000);//Vb
   v2   = emon7.Vrms;
   
   multiplex(2);
   emon8.calcVI(200,200);//Vc
   v3   = emon8.Vrms;
  
   multiplex(3);
   emon9.calcVI(200,200);//falha1
   f1   = emon9.Vrms;
   
   multiplex(4);
   emon10.calcVI(200,200);//falha2
   f2   = emon10.Vrms;
   
   multiplex(5);
   emon11.calcVI(200,200);//falha3
   f3   = emon11.Vrms;

   multiplex(6);
   emon12.calcVI(200,200);//falha4
   f4   = emon12.Vrms;
   
   multiplex(7);
   emon13.calcVI(200,200);//falha5
   f5   = emon13.Vrms;

    Serial.print(v1);Serial.print("\n");
    Serial.print(v2);Serial.print("\n");
    Serial.print(v3);Serial.print("\n");

    Serial.print(f1);Serial.print("\n");
    Serial.print(f2);Serial.print("\n");
    Serial.print(f3);Serial.print("\n");
    Serial.print(f4);Serial.print("\n");
    Serial.print(f5);Serial.print("\n");Serial.print("\n");
  
  if(cont<3)
  {
    emon1.calcIrms(1480);  // Calculate Irms only
    emon2.calcIrms(1480);  // Calculate Irms only
    emon3.calcIrms(1480);  // Calculate Irms only
    emon4.calcIrms(1480);  // Calculate Irms only
    emon5.calcIrms(1480);  // Calculate Irms only
    


    float i1=emon1.Irms;
    float i2=emon2.Irms;
    float i3=emon3.Irms;
    float i4=emon4.Irms;
    float i5=emon5.Irms;

    
      if (i1>0.3)
      {
        acumulado1=acumulado1+i1;
      }
      if(i2>0.3)
      {
        acumulado2=acumulado2+i2;
      }
      if(i3>0.3)
      {
        acumulado3=acumulado3+i3;
      }
      if(i4>0.3)
      {
        acumulado4=acumulado4+i4;
      }
      if(i5>0.3)
      {
        acumulado5=acumulado5+i5;
      }
      //delay(100);
  }
  else
  {
    acumulado1=acumulado1/cont;
    acumulado2=acumulado2/cont;
    acumulado3=acumulado3/cont;
    acumulado4=acumulado4/cont;
    acumulado5=acumulado5/cont;
    
    cont=0;
      // wait for WiFi connection
      Serial.print("...............");
    if((wifiMulti.run() == WL_CONNECTED)) {

        HTTPClient http;

        USE_SERIAL.print("[HTTP] begin...\n");

       conteudoweb= String(acumulado1)+","+String(acumulado2)+","+String(acumulado3)+","+String(acumulado4)+","+String(acumulado5)+"/"+String(v1)+","+String(v2)+","+String(v3)+"/"+String(f1)+","+String(f2)+","+String(f3)+","+String(f4)+","+String(f5);
        
       
       http.begin("https://SEU_SITE/bd.php?consumo="+conteudoweb+"&hash=3452345246456"); //  
        
        USE_SERIAL.print("[HTTP] GET...\n");
        // start connection and send HTTP header
        int httpCode = http.GET();

        // httpCode will be negative on error
        if(httpCode > 0) {
            // HTTP header has been send and Server response header has been handled
            USE_SERIAL.printf("[HTTP] GET... code: %d\n", httpCode);

            // file found at server
            if(httpCode == HTTP_CODE_OK) {
                String payload = http.getString();
                USE_SERIAL.println(payload);
            }
        } else {
            USE_SERIAL.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
        }

        http.end();
    }
  }
  cont++;
}
