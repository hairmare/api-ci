# API CI

Continuous API-doc building for your internal docs.

Install on cloudfoundry like so.

```bash
cf cs api-ci-mongodb
cf push api-ci
sh push-worker.sh
```

Then connect your dev instance to the cloudy mongodb
and add a user.

```bash
php app/console fos:user:create test test@example.com password
php app/console for:user:promote test ROLE_SUPER_ADMIN
```

