# Redirect

This tool redirects to a destination via regular expressions in TXT records.

## Build

```
docker build -t redirect .
```

## Docker Compose

```yml
version: '3'

services:

    redirect:
        image: redirect
        restart: unless-stopped
        environment:
            REDIRECT_HOME: redirect.spnr.de
        ports:
            - 80:80
```