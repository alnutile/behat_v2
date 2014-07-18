## Behat V2

This will bring together different libraries to allow behat to run with events so that many of the other features can be tied into it

The test folder will walk you through how to pull these libraries into a working application and
in many ways document the code


## Libraries

### Behat Wrapper

  Library https://github.com/alnutile/behat-wrapper

  * Events for other classes to hook into like Prepare, Success, Error and Output
  * Using Symfony Process to do parallel tasks easily

### SauceLabs and other vendors

  Library https://github.com/alnutile/saucelabs_client

  * Includes events to trigger tests and update at end of behat tests
  * Events to pull in assets on a fail or as needed

### GitApiWrapper
  
   Library comming soon this just needs to be ported to this new build
   
   * Using the https://github.com/KnpLabs/php-github-api as the base allows BehatEditor to
     * Search files on github api 
     * CRUD files from github api
     * Run tests using files from githubAPI

### Reporting (coming soon)

  * To create events for reporting including pulling in the job id of the service we are using etc.
  * Events to send notifications as needed

### Data Interface (comming soon)

  * Working with LCD we are making a consistant Data interface for this library to then be used
  by others but still offer an interface to help with reporting and queries
  

## Setup

  * composer install
  * npm install
  * bower install

If you are using your local machine try ./start_server.sh to start the server

Also there needs to be a .evn file in the root of the app. Copy env_example to .env and set those settings as needed


## PHPUnit

  * Phantom needs to run for these so run
   ./node_modules/.bin/phantomjs --webdriver=8643

  to run the server for the tests or setup Saucelabs

  You will also have to plug in your saucelabs token into the behat.yml.example file included in private and copy that
  to behat.yml
