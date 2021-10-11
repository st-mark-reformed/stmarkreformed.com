FROM mariadb:10.6.4

# Update bash profile
COPY ./docker/.profile /root/.profile
COPY ./docker/.profile /root/.bashrc

# Copy custom config into image
COPY ./docker/db/custom.cnf /etc/mysql/conf.d/custom.cnf
