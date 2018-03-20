# CrossbarHttpBridge
Crossbar HTTP Bridge service for Laravel

## Installation
1. Run these commands
```bash
composer require idutch/crossbarhttpbridge
php artisan vendor:publish
```
2. Add the following to your .env file
```
CROSSBAR_HTTP_BRIDGE_URI=https://your.crossbar.service:443
CROSSBAR_HTTP_BRIDGE_VERIFY_SSL=true or false
CROSSBAR_HTTP_BRIDGE_PUBLISH_PATH=/publish
CROSSBAR_HTTP_BRIDGE_CALL_PATH=/call
```
3. If you've configured your crossbar server only to accept signed requests then also add the following to your .env file
```
CROSSBAR_HTTP_BRIDGE_PUBLISHER_KEY=your_publisher_key
CROSSBAR_HTTP_BRIDGE_PUBLISHER_SECRET=your_publisher_secret
CROSSBAR_HTTP_BRIDGE_CALLER_KEY=your_caller_key
CROSSBAR_HTTP_BRIDGE_CALLER_SECRET=your_caller_secret
```
## Example

```php
<?php
namespace App\Http\Controllers;

use iDutch\CrossbarHttpBridge\CrossbarHttpBridgeInterface;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     * @param CrossbarHttpBridgeInterface $crossbarHttpBridge
     * @return \Illuminate\Http\Response
     */
    public function index(CrossbarHttpBridgeInterface $crossbarHttpBridge)
    {
        //Publish 'Hello world!' to topic.name
        $crossbarHttpBridge->publish('topic.name', [['message' => 'Hello world!']]);
        
        //Get all active subscriptions on topic.name from the server
        $subscription = $crossbarHttpBridge->call('wamp.subscription.lookup', ['topic.name']);
        $clients = [];

        if (!is_null($subscription['args'][0])) {
            $subscribers = $crossbarHttpBridge->call('wamp.subscription.list_subscribers', [$subscription['args'][0]]);
            foreach ($subscribers['args'][0] as $key => $subscriber) {
                $clients[$key] = $crossbarHttpBridge->call('wamp.session.get', [$subscriber])['args'][0];
            }
        }
        return view('home', ['clients' => $clients]);
    }
}
``` 
