FROM php:7-alpine
MAINTAINER Travis Glenn Hansen <travisghansen@yahoo.com>

COPY html /app/html
COPY config /app/config

CMD ["/usr/local/bin/php","-S","0.0.0.0:8080","-t","/app/html"]

EXPOSE 8080
