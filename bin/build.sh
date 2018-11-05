#!/bin/sh

BASEDIR=$(dirname $0)

${BASEDIR}/console cache:clear
${BASEDIR}/../node_modules/.bin/encore dev
${BASEDIR}/console assets:install
# Need installed global
#browserify node_modules/moment-timer > public/build/js/moment-timer.js