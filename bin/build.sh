#!/bin/sh

BASEDIR=$(dirname $0)

${BASEDIR}/console cache:clear
${BASEDIR}/../node_modules/.bin/encore dev
${BASEDIR}/console assets:install
# Need installed global
#browserify node_modules/moment-timer > public/build/js/moment-timer.js
cd ${BASEDIR}/../
npm install
cd ${BASEDIR}/../express/
npm install

pm2 start ${BASEDIR}/../express/bin/www --name tracker-express --log-date-format 'DD-MM-YY HH:mm:ss.SSS'
