# Test KCO using Klarna PHP SDK at https://github.com/klarna/kco_rest_php

*NOTE: This is a demo only. Not to be used for a real site. Please don't even use it as a starting point.*

## Installation
* Clone repo
* composer install
* Edit src/settings.php and set your MID and sharedSecret for playground access
* Make sure to deploy somewhere publicly accessible with SSL

## Running
There are 2 entry points:

* [your url]/start -> Starts a checkout process with no customer info sent to Klarna
* [your url]/prefill -> Starts a checkout process with customer info sent to Klarna via prefill

