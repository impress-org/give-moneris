# [Give - Moneris Gateway](https://givewp.com/addons/moneris-gateway/ "Give - Democratizing Generosity") #

Welcome to the GiveWP GitHub repository. This is the code source and the center of active development. Here you can browse the source, look at open issues, and contribute to the project. We recommend all developers follow the [GiveWP development blog](https://developers.givewp.com/) to stay up to date on the latest features and changes.

## Getting Started ##

If you're looking to contribute or actively develop on Give - Moneris Add-on then skip ahead to the Local Development section below. The following is if you're looking to actively use the plugin on your WordPress site.

## Description ##

This plugin requires the Give plugin activated to function properly. When activated, it adds a payment gateway for moneris.com.



### Minimum Requirements ###

* WordPress 4.8 or greater
* PHP version 7.0 or greater
* MySQL version 5.6 or greater
* Some payment gateways require fsockopen support (for IPN access)
* cURL version 5.40 or higher
* An SSL certificate is required to accept donations using Moneris payment gateway.

### Updating ###

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

### Support
This repository is not suitable for support. Please don't use GitHub issues for support requests. To get support please use the following channels:

* [GiveWP.com Priority Support](https://givewp.com/priority-support/) - exclusively for customers

## Local Development 

To get started contributing to Give - Moneris add-on then you will need to follow through the following steps:

1. Create a new WordPress site with `give.test` as the URL

2. Install & Activate Give Donations plugin from WordPress.org 

3. `cd` into your local plugins directory: `/path/to/wp-content/plugins/`

4. Clone this repository from GitHub into your plugins directory: `https://github.com/impress-org/give-moneris.git`

5. Run npm install to get the necessary npm packages: `npm install`

6. Activate the plugin in WordPress

That's it. You're now ready to start development.

### NPM Commands

Moneris add-on for Give relies on several npm commands to get you started:

* `npm run watch` - Live reloads JS and SASS files. Typically you'll run this command before you start development. It's necessary to build the JS/CSS however if you're working strictly within PHP it may not be necessary to run. 
* `npm run dev` - Runs a one time build for development. No production files are created.
* `npm run production` - Builds the minified production files for release.

### Development Notes

* Ensure that you have `SCRIPT_DEBUG` enabled within your wp-config.php file. Here's a good example of wp-config.php for debugging:
    ```
     // Enable WP_DEBUG mode
    define( 'WP_DEBUG', true );
    
    // Enable Debug logging to the /wp-content/debug.log file
    define( 'WP_DEBUG_LOG', true );
   
    // Loads unminified core files
    define( 'SCRIPT_DEBUG', true );
    ```
* Commit the `package-lock.json` file. Read more about why [here](https://docs.npmjs.com/files/package-lock.json). 
* Your editor should recognize the `.eslintrc` and `.editorconfig` files within the Repo's root directory. Please only submit PRs following those coding style rulesets. 
* Read [CONTRIBUTING.md](https://github.com/impress-org/give/blob/master/CONTRIBUTING.md) - it contains more about contributing to GiveWP.

## How to test this add-on? ##

[Click here](https://developer.moneris.com/More/Testing/Testing%20a%20Solution) to get the testing credentials of Moneris. There are 2 different types of testing credentials and merchant resource manager URL present in that URL.

1. For Canada account users.
2. For US account users.

Also, there is a list of test cards available which you can use to process a successful donation.

**Please note:** If you use the credentials of Canada account, then set base country as `Canada`. If you use the credentials fo US account, then set base country as `United States`. If any other country is set as base country then processing the donation will throw error.