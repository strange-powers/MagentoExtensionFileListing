# Magento 1 Extension File Listing
This little tool is for everybody who has been struggling with removing a Magento 1 Extension without the original extension Files.

It's supposed to be used from the command line interface.

## Usage
Make sure that you moved the folder in the root directory of your Magento installation.

First of all navigate into the magento-extension-destruction directory and use the following commands to start the script:

```php run.php <name-of-the-extension>```

Where the name of the extension has to be the Magento extension identifier.

To get the Magento extension identifier navigate to /path/to/magento/app/etc/modules.
The XML files name of the extension you want to list is the identifier.

###Example
Here is an example for the PayPal Plus Payment Extension which usually comes with a fresh Magento installation.

```php run.php Iways_PayPalPlus```

##Bugs

Be very careful with this tool. It won't show you all of the files the extension contains.

It just shows which template files the extension uses. These can be base extension files as well.