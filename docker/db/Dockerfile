FROM --platform=$TARGETPLATFORM mariadb:10.6.4

# Update bash profile
COPY ./docker/.profile /root/.profile
COPY ./docker/.profile /root/.bashrc

COPY ./docker/db/custom.cnf /etc/mysql/mariadb.conf.d/z-custom.cnf
