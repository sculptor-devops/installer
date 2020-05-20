#### Sculptor Installer
Linux base installation utility.

#### Build

``` bash
php installer app:build
```

#### Usage

Command | Parameter | Description
------------ | ------------- | -------------
list-stages | None |List all stages 
run | None | Start a new installation
 run-stage | --step="STEP NAME" | Run a single step, see list for names
 
 
#### Installation
``` bash
sh bin/setup.sh
./installer run
```

#### Notes
Machine must be empty before running, see installation log for detailed error information, the log file is placed in the user home.
