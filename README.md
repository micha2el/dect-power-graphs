# dect-power-graphs

PHP graphs using jpgraph to visualize dect power data (Fritz!Dect devices connected to a Fritz!Box) and/or smart meter data.

There are 2 different index files:`index.php` will show graphs while `index_quick.php` will present compact data to be used on smartphones for example.

There is support for the following devices in `index_quick.php`:
* Fritz!Dect 200 (temperature, state, current energy consumption, accumulated energy consumption)
* Fritz!Dect 210 (temperature, state, current energy consumption, accumulated energy consumption)
* Fritz!Dect 301 (temperature, tsoll, battery, window open active, boost active, window open time, boost active time)
* Fritz!Dect 440 (temperature, battery)
* Han-Fun Blinds (e.g. RolloTron Dect 1213 --> level indicator)

The following graphs are avaiable in `index.php`:
* current energy consumption for last 24 hours (Fritz!Dect 200, Fritz!Dect 210)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_energy_current.png?raw=true)
* accumulated energy consumption for last 14 days (Fritz!Dect 200, Fritz!Dect 210)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_verbrauch.png?raw=true)
* accumulated energy consumption for last 12 months (Fritz!Dect 200, Fritz!Dect 210)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_verbrauch_monthly.png?raw=true)
* temperature graph for last 24 hours (Fritz!Dect 200, Fritz!Dect 210, Fritz!Dect 440 and in seprated chart Fritz!Dect 330)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_temp.png?raw=true)

If you have an outlet connected to a solar system the following graphs will be possible (if configurated accordingly):
* energy output in W (24 hours)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_solar_power_daily.png?raw=true)
* energy output in W (7days)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_solar_power.png?raw=true)
* accumulated energy generation in kWh per day (31days)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_solar_production.png?raw=true)
* accumulated energy generation in kWh per month (12 months)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_solar_production_monthly.png?raw=true)

If you have a smart meter and the data is reported properly (see [read-smartmeter](https://github.com/micha2el/read-smartmeter)) you can see the following graphs - again if configured properly (and please keep in mind that this will change the display of the solar input outlet):
* smart meter energy consumption (24 hours)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_smart.png?raw=true)
* smart meter accumulated energy consumption per day (display of energy consumption, energy output and energy input)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_energy_consumption_daily.png?raw=true)
* smart meter accumulated energy consumption per month (display of energy consumption, energy output and energy input)
![Graph Example](https://github.com/micha2el/power-graphs/blob/main/documentation/readme_pic_energy_consumption_monthly.png?raw=true)

## Getting started

Copy `config.php.example` to `config.php` and adjust configuration variables.

If necessary adjust picture titles and configuration in each `pic_.php`.

According to your config the graphs need input from DECT and/or smart devices. Have a look at my [scripts](https://github.com/micha2el/fritz-dect) to create data.

## Using smartmeter data

The graphs use data which is pushed from vzlogger ([Volkszaehler](https://volkszaehler.org)) using `push_smart.php` into local files. Additional information and how to create the necessary files can be found [here](https://github.com/micha2el/read-smartmeter). 

## Thanks
Thanks to [JPgraph](https://jpgraph.net) for being such a nice tool to display easy-to-use graphs.
