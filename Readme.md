# Install
```bash
composer require revenuewire/email-client
```

# Send Direct
```php
\RW\Email\Client::init(["region" => "[YOUR_REGION]", "url" => "[YOUR_QUEUE]"]);
$templateId = "59d2be5f18c13";
$from = ["name" => "Scott", "emailAddress" => "noreply@safecart.com"];
$to = [["name" => "Scott", "emailAddress" => "swang@revenuewire.com"]];
$data = ["name" => "Scott", "message" => "可以吧 8"];
$result = \RW\Email\Client::directSend($templateId, $from, $to, $data);
```