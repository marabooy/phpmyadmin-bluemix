## Bluemix PHPMyAdmin
Manage your Bluemix Mysql services


###Configuration 
1. Mysql Config

	see the htdocs/config.inc.php you can add customizations for cleardb easily too

2. Mainfest.yaml

3. Composer.json
4. Procfile 

####Important parts
1. The composer.json file 

	This specifies php and other core libraries that are required by the application  e.g phpmyadmin requires the mbstring library
	The defaults and other posibilities can be found [here] (https://devcenter.heroku.com/articles/php-support#extensions )

	```
	
	{
		"require": {
			"ext-mbstring":"*", (specifies that this requires the mbstring extension)
			"ext-gd":"*" (requires the gd library from php)
		}
	}

	```

	Also has the script part which is run on deploy. if you need to run extra scripts to configure your enviroment here is the place to put it
	More details on composer scripts [here] (https://getcomposer.org/doc/articles/scripts.md)

2. The Procfile (Inherited from the heroku buildpack)

	This file defines the processes required by the application
	e.g the web process and the commands required
	Worker threads and etc are also defined in this file more on the procfile [here] (https://devcenter.heroku.com/articles/procfile)
	e.g.
	
	```
	
	web: vendor/bin/heroku-php-apache2 htdocs/

	```

	Specifies that the web process [vendor/bin/heroku-php-apache2] and the path is [htdocs/] path is relative to the project root

3. Your Manifest file

4. Options.json

    With this you can specify the url to download the phpmyadmin zip from this reduces the amount of bandwidth used during cf push
    The hash value contains the md5 hash of the zip file (Just to make sure we are downloading/have downloaded the correct thing) :)


###Uses
1. [PHP Buildpack] (https://github.com/cloudfoundry/php-buildpack)
2. Extensions and libraries uses the composer tool to require platform libraries/extensions. Reference found on this [page] (https://getcomposer.org/doc/02-libraries.md#platform-packages)
3. Available extensions are currently documented [here] (https://devcenter.heroku.com/articles/php-support#extensions) 


###Notes
This buildpack utilizes the heroku buildpack and hence the heroku php docs will also work for this 




