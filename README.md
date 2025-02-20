# Miltydraft generator

[Visit the app here](https://milty.shenanigans.be/).

An expanded version of miltydraft.com, with saving/sharing drafts across sessions.

## Requirements: 
* make sure you have [docker](https://docs.docker.com/get-started/) installed

## Getting started

To install a local copy of this app you can clone it from the Git Repo: 

`git@github.com:shenanigans-be/miltydraft.git`

Then follow these steps:

1. Add `127.0.0.1 miltydraft.test` to your `/etc/hosts` file. This first step is optional though. You can use
   127.0.0.1 directly as well.
2. Run `docker-compose up -d --build`. This will first build the image, then start all services.
3. Run `docker-compose exec app composer install`. This will install all php dependencies.
4. Create a `.env` file. See `.env.example` for details.
5. Go to [http://miltydraft.test](http://miltydraft.test) or [127.0.0.1](127.0.0.1) in your browser.

### Libraries and Dependencies

Frontend runs on vanilla JS/jQuery (I'm aware jQuery is a bit of a blast from the past at this point; sue me and/or change it and PR me if you want) and the Back-end is vanilla PHP.
As such there's no build-system, or compiling required except for the steps described above.

To make this app as lean and mean (and easy to understand for anyone) as possible, external dependencies, both in the front- and backend should be kept to an absolute minimum. 

### Understanding the App flow

1. Players come in on index.php and choose their options. 
2. A JSON config file is created (either locally or remotely, depending on .env settings) with a unique ID
3. That Draft ID is also the Draft URL: APP_URL/d/{draft-id} (URL rewriting is done via Caddy)
4. Players (or the Admin) make draft choices, which updates the draft json file (with very loose security, since we're assuming a very low amount of bad actors)

