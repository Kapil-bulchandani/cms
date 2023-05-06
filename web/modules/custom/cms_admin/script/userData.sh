#!/bin/bash

su jenkins
cd /var/lib/jenkins/workspace/cms-deployment
sed -i 's/New site with Docker/Sitename/g' config/sync/system.site.yml
docker-compose -f docker-compose-server.yml up -d
