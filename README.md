#README
##What is MMi?

* MMi is a PHP7 full-stack web framework. It is written with speed and flexibility in mind. It allows developers to build better and easy to maintain websites with PHP.

* MMi can be used to develop all kind of websites, from your personal blog to high traffic ones.

##Requirements

* MMi is only supported on PHP 7.3.x and up.

##Installation

###The best way to install MMi is to use composer:

1. composer require mmi/mmi
2. configure Your environment in .env (.env.sample can be found in this repository)
3. you will want to map RouterConfig::class to implementation
4. if you are using database probably you will need to run ./bin/mmi Mmi:DbDeploy