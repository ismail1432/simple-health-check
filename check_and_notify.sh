#!/bin/bash

source config.sh

notifySlack() {
	curl --request POST \
	  --url $1 \
	  --header "Authorization: Bearer $2" \
	  --header 'Content-type: application/json' \
	  --data '{
			"channel": "'$3'",
			"text": "'"$4"'",
		}'
}

notifyDiscord() {
	curl --request POST \
	  --url $1 \
	  --header 'Content-type: application/json' \
	  --data '{
	  	"username": "Simple Health Check BOT",
	  	"content": "'"$2"'"
	}'
}

for path in ${enpointToCheck[@]}; do
  status_code=$(curl --write-out %{http_code} --silent --output /dev/null ${path})
  if ([ "$status_code" -eq 000 ]) || ([ "$status_code" -ge 500 ] && [ "$status_code" -lt 600 ]) ; then
	if $slackActive ; then
		notifySlack ${slackUrl} ${slackToken} ${slackChannel} ":x: ${path} seems down with code status ${status_code}"
	fi
	if $discordActive ; then
		notifyDiscord ${discordUrl} ":x: ${path} seems down with code status ${status_code}"
	fi
  fi
done
