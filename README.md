# Test KCO using Klarna PHP SDK at https://github.com/klarna/kco_rest_php

*NOTE: This is a demo only. Not to be used for a real site. Please don't even use it as a starting point.*

## Installation
* Clone repo
* composer install
* Edit src/settings.php and set your MID and sharedSecret for playground access
* Make sure to deploy somewhere publicly accessible with SSL

## Running
* Run https://[your-publicly-available-hostname]/

Additionally you can run the following 
* https://[your-publicly-available-hostname]/capture/{orderid} -> Captures the funds for the order (where {orderid} is the Klarna order ID from the success URL)
