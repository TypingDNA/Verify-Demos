# TypingDNA Verify integration with Okta

Follow the steps presented in this [tutorial](https://www.typingdna.com/docs/set-up-okta-verify-mfa-for-php-apps.html) to get started with the demo.


#### Prerequisites

Set up an Okta authentication to your PHP app using the following [tutorial](https://developer.okta.com/blog/2018/07/09/five-minute-php-app-auth).

Create an account on [TypingDNA](https://www.typingdna.com/).

Run an [ngrok](https://ngrok.com/) instance that will generate a custom domain ```your_subdomain.ngrok.io```.

Create a new integration on the [TypingDNA Dashboard](https://www.typingdna.com/clients)  with the name of your choice and the domain ```your_subdomain.ngrok.io```.

Set the redirect_uri variable in the ```index.php``` file with the ngrok link

```
$redirect_uri = 'https://your_subdomain.ngrok.io';
```

Set the TypingDNA Verify credentials with the information from the TypingDNA dashboard on the ```verify.php``` file

```
$client_id='your_verify_client_id';
$secret='your_verify_client_secret';
$application_id='your_verify_application_id';
```

Run your application using the following link ```https://your_subdomain.ngrok.io```. 
