#!/bin/sh

CF_MONGODB_TYPE="default"

if [[ -f `pwd`/deploy.local.sh ]]; then
    . `pwd`/deploy.local.sh
fi

if [[ ! $APP_NAME ]]; then
    read -p "Enter app name: " APP_NAME
fi
if [[ ! $CF_API ]]; then
    read -p "Enter cloudfoundry API endpoint: " CF_API
fi
if [[ ! $CF_DOMAIN ]]; then
    read -p "Enter cloudfoundry DOMAIN: " CF_DOMAIN
fi
if [[ ! $CF_ORG ]]; then
    read -p "Enter cloudfoundry ORG: " CF_ORG
fi
if [[ ! $CF_USER ]]; then
    read -p "Enter cloudfoundry username: " CF_USER
fi
if [[ ! $CF_PASS ]]; then
    read -s -p "Enter cloudfoundry password: " CF_PASS
fi

ECHO_CMD=`which echo`

cf api $CF_API
# call behind pipe to ensure that cf login is not interactive
echo '' | cf login -u $CF_USER -p $( $ECHO_CMD -n $CF_PASS) -o $CF_ORG
if [[ $? -ne 0 ]]; then
    echo "Auth failed"
    echo "Exited with non-zero exit code"
    exit;
fi

# create mongodb if it did not exist
cf cs mongodb $CF_MONGODB_TYPE "${APP_NAME}-mongodb"

# push webinterface (does a green/blue deploy w/o rollback support)
cf app "${APP_NAME}-blue"
if [[ $? -eq 0 ]]; then
    DEPLOY_TARGET="${APP_NAME}-green"
    OLD_TARGET="${APP_NAME}-blue"
fi
cf app "${APP_NAME}-green"
if [[ $? -eq 0 ]]; then
    DEPLOY_TARGET="${APP_NAME}-blue"
    OLD_TARGET="${APP_NAME}-green"
fi

if [[ ! $DEPLOY_TARGET ]]; then
    echo "Initial Deploy, remember to set up the DB"
    DEPLOY_TARGET="${APP_NAME}-blue"
    OLD_TARGET="${APP_NAME}-green"
fi

echo "Deploying API to ${DEPLOY_TARGET}"
cf push $DEPLOY_TARGET
if [[ $? -ne 0 ]]; then
    echo "Push to ${DEPLOY_TARGET}failed"
    echo "Exited with non-zero exit code"
    exit;
fi
cf map-route $DEPLOY_TARGET $CF_DOMAIN -n $APP_NAME
cf unmap-route $OLD_TARGET $CF_DOMAIN -n $APP_NAME
cf stop $OLD_TARGET
cf delete $OLD_TARGET -f

# push worker (we just push these without green/blue since a small backlog won't matter)
echo "Deploying Worker"
sed -i '' -e 's/"WEB_SERVER": "nginx",/"WEB_SERVER": "none",/' .bp-config/options.json
cf push -f manifest.worker.yml
if [[ $? -ne 0 ]]; then
    echo "Pushing Worker failed"
fi
sed -i '' -e 's/"WEB_SERVER": "none",/"WEB_SERVER": "nginx",/' .bp-config/options.json

