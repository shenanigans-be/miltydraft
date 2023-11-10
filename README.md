# Miltydraft generator

[Visit the app here](https://milty.shenanigans.be/).

An expanded version of miltydraft.com, with saving/sharing drafts across sessions. 

## Getting started

Requirements: 
* make sure you have [PHP](https://www.php.net/manual/en/install.php) installed
* make sure you have [composer](https://getcomposer.org/download/) installed

```
# 1. clone the repo
git clone git@github.com:shenanigans-be/miltydraft.git
cd miltydraft

# 2. install dependencies
composer install

# 3. create a .env file, also edit the URL to match your XAMPP server url
cp .env.example .env

# 4. make directory for draft JSONs
mkdir drafts

# 5. run server with XAMPP
```

### Libraries and Dependencies

Frontend runs on vanilla JS and Jquery (cause I'm lazy) and the Back-end is vanilla PHP (because, again, I'm lazy and laravel or whatever seemed like huge overkill).
As such there's no build-system, or compiling required except for the steps described above.
You _will_ need to run it through Apache in order to make the URL rewriting work (for the draft page). We recommend using [XAMPP](https://www.apachefriends.org/) for this.

### Understanding the App flow

1. Players come in on index.php and choose their options. 
2. A JSON config file is created (either locally or remotely, depending on .env settings) with a unique ID
3. That Draft ID is also the Draft URL: APP_URL/d/{draft-id} (URL rewriting is done via .htaccess)
4. Players (or the Admin) make draft choices, which updates the draft json file (with very loose security, since we're assuming a very low amount of bad actors)

