# kitos_archimate_webui #

 Kitos_archimate_webui er en nem web brugerflade, til brugen af archimate importerne i [kitos_tools](https://github.com/os2kitos/kitos_tools).

 * kitos_archimate_webui'en - frontend applikation lavet i PHP.

# Forudsætninger #

Applikation forudsætter følgende:

* At man har fået oprettet en KITOS bruger, med adgang til WebAPI'et.
* At man har adgang til en docker server, samt at docker-compose er installeret og kan bruges til at bygge et image.
* At man har installeret git klient, til installation af selve projektet.

# Installation #

1. Installer kitos_archimate_webui på din docker server via git:
```shell
git clone https://github.com/ThomasSKokholm/kitos_archimate_webui.git
```
2. gå til kitos_archimate_webui mappen.
```shell
cd kitos_archimate_webui
```
3. Start kitos_archimate_webui'en, som docker container. Der er nu bygget de nødvendige images og applikation kan startes.

```shell
sudo docker-compose up -d
```

4. Nu kan kitos_archimate_webui'ens frontend tilgåes på http://<docker-server>:8063/webui/ eller http://<docker-server>:8063/webui/index.php

5. Hvis der ønskes at kitos_archimate_webui'en, skal kunne tilgåes på anden port end 8063,
skal docker-compose.yml, tilpasses, på følgende linjer.

```
ports:
      - "8063:80"
```
Angiv det portnummer der ønskes i stedet for "8063".

6. kitos_archimate_webui'en kan nu genstartes med docker-compose.
```
sudo docker-compose down 
sudo docker-compose up -d
```

# Brugen af WebUI'en #

* Gå ind på docker url'en.
* Indtast bruger navn til KITOS
* Indtast kodeord
* Valgmulighed, du kan vælge om du vil have en eksisterende model opdateret, eller spring over.
* Tryk på 'Vælg filer'/'Gennemse...' knappen.
* Find din tome archimate model fil.
* Tryk på Upload knappen.
* hvis filen er blevet uploadet, fåes beskeden "Filen er blevet uploaded".
* Tryk på 'Kør archimate Script' knappen.
* så vil der gå ca. 10-15 sekunder, hvorefter vil der dukke en fil download dialog op eller der vil blive automatisk hente din nye archimate fil til din download/overførsler mappe.
* Hvis scriptet ikke er færdig med at køre, efter 15 sekunder, kan du vente 10-15 sekunder, og så opdater/genopfriske websiden, hvor så archimate filen skulle dukke frem.
