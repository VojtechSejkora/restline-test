#!/usr/bin/env bash

set -e

STATE=0;

while [ "$#" -gt 0 ]
do
  case "$1" in
    --on | --enable ) STATE=1; shift ;;
    --off | --disable ) STATE=0; shift ;;
    -- ) shift; break ;;
    * ) break ;;
  esac
done

path_to_script_directory="$(dirname "$0")"
cd "$path_to_script_directory";


if [ $STATE -eq 1 ]; then
	cp /usr/local/etc/php/conf.d/xdebug.ini.off /usr/local/etc/php/conf.d/xdebug.ini
	rm -rf /usr/local/etc/php/conf.d/xdebug.ini.off
else
  cp /usr/local/etc/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini.off
	rm -rf /usr/local/etc/php/conf.d/xdebug.ini
fi
