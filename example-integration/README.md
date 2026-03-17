# Rabo Smart Pay PHP SDK Example integration

This example integration demonstrates the usage of the underlying Smartpay SDK. 
It uses a (standard) Symfony Framework setup with docker.

## Setup

1. Run all commands from repository root.
2. Build and start services (`php`, `nginx`, `valkey`):
```bash
docker compose -f example-integration/docker-compose.yml build
docker compose -f example-integration/docker-compose.yml up -d
```
3. Install dependencies in the `php` container:
```bash
docker compose -f example-integration/docker-compose.yml exec php composer install -n
```
4. Add your credentials to `example-integration/.env.local`. This file is excluded from Git.
5. Open: `http://localhost:1234`

## Troubleshooting: Windows line endings (CRLF)

This repository enforces LF line endings through root `.gitattributes`.
For fresh clones, no manual line-ending setup is needed.

CRLF in shell entrypoints causes Linux containers to fail with "not found" errors.
Typical symptoms:
```text
sh: 1: /usr/local/bin/container-entrypoint: not found
service "php" is not running
```

If this is an existing Windows clone, run this once from repository root:

1. Set local Git line ending behavior:
```bash
git config --local core.autocrlf false
git config --local core.eol lf
```
2. Re-checkout tracked files (discards local changes in tracked files):
```bash
git reset --hard HEAD
```
3. Verify critical files are LF:
```bash
git ls-files --eol example-integration/docker/php/container-entrypoint.sh
```
Expected: `w/lf`.

Then rebuild and restart:
```bash
docker compose -f example-integration/docker-compose.yml build --no-cache php
docker compose -f example-integration/docker-compose.yml up -d
```

## Troubleshooting: "Cannot find omnikassa sdk/package"

`composer.json` in this folder loads `rabobank/omnikassa-sdk` from local path `../`.
So this example must run inside the full repository, not as a copied standalone `example-integration` folder.
