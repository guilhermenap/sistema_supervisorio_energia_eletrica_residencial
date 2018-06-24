#include "EmonLib.h" //Inclui biblioteca Emonlib
#define ADC_BITS    12 //Define a leitura como 12 Bits
EnergyMonitor emon[13]; //Cria 14 instâncias dentro do vetor emon

int pin[]={23,22,1,3,21,19,18,5}; //Pinos digitais utilizados para multiplexação

//----------------- Função de multiplexação------------------------------------
   int multiplex(int ativar) 
   {
        for(int i=0;i<8;i++)
        {
            if(ativar == i)
            {
            digitalWrite(pin[i], HIGH); 
            }
            else
            {
              digitalWrite(pin[i], LOW); 
            }
        }
         
          emon[ativar+6].calcVI(200,200);// calcVI(x,y) onde x é a quantidade de meia onda a ser mensurada e y é o atraso para a proxima medição
          
          Serial.print("V: ");
          Serial.print(emon[ativar+6].Vrms);
          Serial.print(" V");
          Serial.print("\n"); 
    }
//----------------- Função de medição de corrente-------------------------------
    int corrente(int ativar)
    {
      Serial.print("Correte "+String(ativar)+" :");
      Serial.print(emon[ativar].calcIrms(1480)); //Numero de amostras que contém em 1 período de 60Hz 
      Serial.print(" A");
      Serial.print("\n");
      
    }
//------------------------------------------------------------------------------

void setup()
{  
  Serial.begin(115200);//Inicializa a janela serial em 115200 baud
  
//---------------Configuração de corrente--------------------------------------- 
  emon[1].current(36, 7);//current(x,y) onde x é o pino ADC a ser mensurado e y é a constante de calibração
  emon[2].current(39, 7); 
  emon[3].current(34, 7);
  emon[4].current(35, 7);
  emon[5].current(32, 7);

//---------------Configuração de tensão-----------------------------------------
  for(int i=6;i<14;i++)
  {
     emon[i].voltage(33, 70, 1.7);//voltage(x,y,z) onde x é o pino ADC a ser mensurado, y é a constante de calibração e z é a constante de avanço de fase.
  }
//------------------------------------------------------------------------------ 
  for(int i=0;i<8;i++)
  {
  pinMode(pin[i], OUTPUT);//deifine os pinos do vetor pin como saída.
  }    
}

void loop()
{  
  for(int i=1;i<6;i++)
  { 
          corrente(i);//lopping de medição de corrente;
  }
  

  for(int i=0;i<8;i++)
  { 
          multiplex(i);//lopping de medição de tensão
  }
  
 delay(2000);
}
