# dect-power-graphs

PHP graphs using jpgraph to visualize dect power data (Fritz!Dect devices connected to a Fritz!Box) and/or smart meter data.

## Getting started

Copy `config.php.example` to `config.php` and adjust configuration variables.

If necessary adjust picture titles and configuration in each `pic_.php`.

According to your config the graphs need input from DECT and/or smart devices. Have a look at my [scripts](https://github.com/micha2el/fritz-dect) to create data.

## Using smartmeter data

The graphs use data which is pushed from vzlogger ([Volkszaehler](https://volkszaehler.org)) using `push_smart.php` into local files. Additional information and how to create the necessary files can be found [here](https://github.com/micha2el/read-smartmeter). 

## Thanks
Thanks to [JPgraph](https://jpgraph.net) for being such a nice tool to display easy-to-use graphs.
