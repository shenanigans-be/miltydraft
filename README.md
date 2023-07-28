# Miltydraft generator

[Visit the app here](https://milty.shenanigans.be/).

An expanded version of miltydraft.com, with saving/sharing drafts across sessions. 

## Getting started

To install a local copy of this app you can clone it from the Git Repo: 

`git@github.com:shenanigans-be/miltydraft.git`

You will need to install the PHP dependencies with [Composer](https://getcomposer.org/) by running  `composer install` in the root directory

Lastly you'll need to create a .env file (also in the root directory). 
See `.env.example` for details. 


### Libraries and Dependencies

No huge dependencies on either the back-end or the front-end. Frontend runs on vanilla JS and Jquery (cause I'm lazy) and the Back-end is vanilla PHP (because, again, I'm lazy and laravel or whatever seemed like huge overkill).
As such there's no build-system, or compiling required except for the steps described above. 
You _will_ need to run it through Apache in order to make the URL rewriting work (for the draft page).


### Understanding the App flow

1. Players come in on index.php and choose their options. 
2. A JSON config file is created (either locally or remotely, depending on .env settings) with a unique ID
3. That Draft ID is also the Draft URL: APP_URL/d/{draft-id} (URL rewriting is done via .htaccess)
4. Players (or the Admin) make draft choices, which updates the draft json file (with very loose security, since we're assuming a very low amount of bad actors)

