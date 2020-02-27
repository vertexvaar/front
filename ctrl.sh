#!/usr/bin/env bash

printf 'Front - Experimental parser written in PHP - Control Script\n'

help()
{
  printf "Help pages
ctrl.sh action [otions]

Available actions:
  help - Show this help text
  install - Run composer install (in docker)
  run - Run application in docker (install must be run once before!)
    -x, --xdebug - With xdebug enabled
"
}
install()
{
  docker run --rm -v $(pwd):/app -v $HOME/.composer/auth.json:/tmp/composer/auth.json -v $HOME/.composer/cache/:/tmp/composer/cache/ -w /app in2code/php-dev:7.4-fpm composer install
}
run()
{
  if [[ "$XDEBUG" -eq 1 ]]; then
    docker run --rm -it -v$(pwd):/app -w /app -e PHP_IDE_CONFIG="serverName=front.docker" in2code/php-dev:7.4-fpm php -dxdebug.remote_autostart=1 -dxdebug.remote_host=$(ifconfig $(/sbin/ip route | grep -v tun | awk '/default/ { print $5 }') | awk -F ' *|:' '/inet /{print $3}') ./front
  else
    docker run --rm -it -v$(pwd):/app -w /app in2code/php-dev:7.4-fpm ./front
  fi
}


POSITIONAL=()
while [[ $# -gt 0 ]]
do
key="$1"
case $key in
    -x|--xdebug)
    XDEBUG=1
    shift # past argument
    ;;
    *)    # unknown option
    POSITIONAL+=("$1") # save it in an array for later
    shift # past argument
    ;;
esac
done
set -- "${POSITIONAL[@]}" # restore positional parameters

if [[ "$1" != "" ]]; then
    action="$1"
else
    action='help'
fi

case "$action" in
  help)
    help;
    ;;
  install)
    install;
    ;;
  run)
    run;
     ;;
esac
