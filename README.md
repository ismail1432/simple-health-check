# Simple-health-check-monitoring :bulb: 

## Simple script that ping a web application and send a slack notification if there is a server error.

![img.png](img.png)

---

## You can use the PHP script or the Bash script.

### PHP

Edit the `config.php.dist` with your parameters see [slack documentation](https://api.slack.com/messaging/sending)

```cp config.php.dist config.php```

Run script (can be run with an interval via a CRON)

```php path/to/directory/check_and_notif.php > /dev/null```

---
### Bash

Edit the `config.sh.dist` with your parameters see [slack documentation](https://api.slack.com/messaging/sending)

Run script (can be run with an interval via a CRON)

```bash path/to/directory/check_and_notif.sh > /dev/null```