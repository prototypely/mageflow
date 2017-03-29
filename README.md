# README #

MageFlow Connect extension (MFX) uses modman. To install it for development or testing, run modman:
modman clone git@github.com:prototypely/mageflow.git

### What is this repository for? ###

* This repository contains MageFlow Connect extension for Magento 1.*
* Current major version is 1.2

## Releases ##

Use official releases only for use on production servers. Code in master branch should be considered as under constant development.
Stable releases are marked as GIT tags. It's recommended to use the released software packages and install these from Magento Connect.

### Installing extension ###

See [INSTALL.md](INSTALL.md)

#### Installing extension with modman ####

To install, run 

```
#!bash
modman init
modman clone git@github.com:prototypely/mageflow.git
```

To update, run 

```
#!bash
modman update mfx
```

### Symlinks and modman ###

In order for a modman module to work properly with Magento symlinks must be enabled. Symlinks in Magento can be enabled under System->Configuration->Advanced->Developer->Template settings 

## Uninstalling ##

MFX can be removed with modman
```
#!bash
modman remove mfx
```
Before uninstalling, please:
1. Make sure you do have a backup of your database
2. Delete all records from your Magento database table eav_attribute where attribute_code='mf_guid'

# MageFlow Developer Resources #

See [DeveloperGuide](/mageflow/mfx/wiki/DeveloperGuide)

## Who do I talk to? ##

* This repo was created by Sven Varkel - sven@prototypely.com
* Other contacts: info@prototypely.com
