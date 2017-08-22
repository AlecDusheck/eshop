This is eShop, its a rudimentary shop using Paypal and Slim.

This is in-progress, and is a proof and concept.

How to Install:

- You will need to create the database, and tables
Here is the example database structure (working on an installer)

![mysql](http://i.imgur.com/b0d3azh.png)

- If using Apache, you will need to configure it
You will need to configure your Apache vhost (can be in apache/conf/extra) configuration.
_________________________________________________________________________________________
Add/Change these lines:
ServerName <server name>
DocumentRoot <path to public folder>
_________________________________________________________________________________________
Example of this:
<VirtualHost *:80>
ServerName localhost
DocumentRoot    D:\xampp\htdocs\public
</VirtualHost>
_________________________________________________________________________________________

You will also need mod_rewrite for Apache.
Install this with: a2enmod rewrite
_________________________________________________________________________________________
