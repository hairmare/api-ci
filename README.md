# API CI

Continuous API-doc building for your internal docs.

Installs on cloudfoundry like so.

## Step 1: Create ``deploy.local.sh``

```bash
export APP_NAME=api-ci
export CF_API=https://cloudfoundry.example.com
export CF_ORG=default
export CF_USER=
export CF_PASS=
export CF_DOMAIN=example.com
```

## Step 2: Run ``deploy.sh``

```bash
sh deploy.sh
```

## Step 3: Create initial user

Connect your dev instance to the cloudy mongodb and add a user.

```bash
php app/console fos:user:create test test@example.com password
php app/console for:user:promote test ROLE_SUPER_ADMIN
```
