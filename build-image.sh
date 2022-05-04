#!/bin/sh

type docker >/dev/null 2>&1 || {
  echo >&2 "not found docker(https://docs.docker.com/get-docker/) Aborting."
  sleep 1
  exit 1
}

default_tag=sv-erp-api:hyperf
echo -e "image tag is(${default_tag}): "
read tag

if [[ -z "${tag}" ]]; then
  tag=${default_tag}
fi

echo "exec docker build -t ${tag} ."
sudo docker build -t ${tag} .
