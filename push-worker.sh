#!/bin/sh

sed -i '' -e 's/"WEB_SERVER": "nginx",/"WEB_SERVER": "none",/' .bp-config/options.json
cf push api-ci-worker
sed -i '' -e 's/"WEB_SERVER": "none",/"WEB_SERVER": "nginx",/' .bp-config/options.json
