#!/bin/bash

# You need to install 'jq' to format the json output (sudo apt install jq)

WSTOKEN="251ba299239d724d0f7e8d4702bb7083"
ENDPOINT="http://ubuntu2.local:8081/webservice/rest/server.php"

# Get top level
COURSEID="4"
WSFUNCTION="local_gugrades_get_levelonecategories"
curl "${ENDPOINT}?wstoken=${WSTOKEN}&wsfunction=${WSFUNCTION}&moodlewsrestformat=json&courseid=${COURSEID}" | jq

# Get activities
CATEGORYID="3"
WSFUNCTION="local_gugrades_get_activities"
curl "${ENDPOINT}?wstoken=${WSTOKEN}&wsfunction=${WSFUNCTION}&moodlewsrestformat=json&courseid=${COURSEID}&categoryid=${CATEGORYID}" | jq

# Get capture page
GRADEITEMID=4
PAGENO=0
PAGELENGTH=0
FIRSTNAME=""
LASTNAME=""
WSFUNCTION="local_gugrades_get_capture_page"
curl "${ENDPOINT}?wstoken=${WSTOKEN}&wsfunction=${WSFUNCTION}&moodlewsrestformat=json&courseid=${COURSEID}&gradeitemid=${GRADEITEMID}&pageno=${PAGENO}&pagelength=${PAGELENGTH}&firstname=${FIRSTNAME}&lastname=${LASTNAME}" | jq
