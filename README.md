# Docker Deployment Step
## 1. Change Host File
add below code to /etc/hosts file
```bash
127.0.0.1  local.mtrc.com
```
## 2. Install Docker Environment
## 3. Run Docker
run below code under folder
```bash
docker-compose up -d
```
## 4. Setup IP
check mysql container IP and put into sites/local/settings.php
```bash
docker inspect mysql-80
```
## 4. Project Setup
default port is 8081
[local.mtrc.com:8081](http://local.mtrc.com:8081/)