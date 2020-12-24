# Simple-web-application-monitoring :bulb: 

## Simple vanilla PHP script that ping a web application and send a slack notification if there is a server error.


![img.png](img.png)

### Configuration

Edit the `config.php.dist` with your parameters see [slack documentation](https://api.slack.com/messaging/sending)

```cp config.php.dist config.php```

### Run (can be run with an interval via a CRON)
```php path/to/directory/check_and_notif.php > /dev/null```
