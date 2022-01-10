# dect-power-graphs

PHP graphs using jpgraph to visualize dect power data (Fritz!Dect devices connected to a Fritz!Box) and/or smart meter data.

There are 2 different index files:`index.php` will show graphs while `index_quick.php` will present compact data to be used on smartphones for example.

There is support for the following devices in `index_quick.php`:
* Fritz!Dect 200 (temperature, state, current energy consumption, accumulated energy consumption)
* Fritz!Dect 210 (temperature, state, current energy consumption, accumulated energy consumption)
* Fritz!Dect 301 (temperature, tsoll, battery, window open active, boost active, window open time, boost active time)
* Fritz!Dect 440 (temperature, battery)
* Han-Fun Blinds (e.g. RolloTron Dect 1213 --> level indicator)

The following devices information will be presentes in `index.php`:
* Fritz!Dect 200 (temperature graph, current energy consumption graph, accumulated energy consumption graph)
* Fritz!Dect 210 (temperature graph, current energy consumption graph, accumulated energy consumption graph)
* Fritz!Dect 301 (temperature graph)
* Fritz!Dect 440 (temperature graph)

If you have an outlet connected to a solar system `index.php` is able to present the following graphs (if configurated accordingly):
* energy output in W (24 hours)
* energy output in W (7days)
* accumulated energy generation in kWh per day (31days)
* accumulated energy generation in kWh per month (12 months)

If you have a smart meter and the data is reported properly (see https://github.com/micha2el/read-smartmeter) you can use `index.php` to present that data as well:
* smart meter energy consumption (24 hours)
* smart meter accumulated energy consumption per day (display of energy consumption, energy output and energy input)
* smart meter accumulated energy consumption per month (display of energy consumption, energy output and energy input)

## Getting started

Copy `config.php.example` to `config.php` and adjust configuration variables.

If necessary adjust picture titles and configuration in each `pic_.php`.

According to your config the graphs need input from DECT and/or smart devices. Have a look at my [scripts](https://github.com/micha2el/fritz-dect) to create data.

## Using smartmeter data

The graphs use data which is pushed from vzlogger ([Volkszaehler](https://volkszaehler.org)) using `push_smart.php` into local files. Additional information and how to create the necessary files can be found [here](https://github.com/micha2el/read-smartmeter). 

## Thanks
Thanks to [JPgraph](https://jpgraph.net) for being such a nice tool to display easy-to-use graphs.
