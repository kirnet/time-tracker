#!/bin/sh

BASEDIR=$(dirname $0)

${BASEDIR}/console cache:clear
${BASEDIR}/../node_modules/.bin/encore dev
${BASEDIR}/console assets:install