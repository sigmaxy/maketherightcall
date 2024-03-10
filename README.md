add following line to /etc/hosts
127.0.0.1  local.mtrc.com
Docker installation
setup docker
run
docker-compose up -d

check mysql container IP and put into sites/local/settings.php
docker inspect mysql-80

default port 8081
local.mtrc.com:8081