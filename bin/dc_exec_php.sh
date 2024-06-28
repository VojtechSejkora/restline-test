#!/usr/bin/env bash

if [[ -t 0 ]]; then
	docker_term_opts="-it";
else
	docker_term_opts="";
fi

docker exec $docker_term_opts rentline-php $@
exit $?
