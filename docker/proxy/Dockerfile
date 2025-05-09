FROM nginx:1.25.5

# Update bash profile
COPY ./docker/.profile /root/.profile
COPY ./docker/.profile /root/.bashrc

COPY ./docker/proxy/05-docker-entrypoint.sh /docker-entrypoint.d/05-docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.d/05-docker-entrypoint.sh

COPY ./docker/proxy/proxyheaders.conf /etc/nginx/conf.d/proxyheaders.conf
COPY ./docker/proxy/default.conf.template /etc/nginx/conf.d/default.conf.template

RUN echo 'server_tokens off;' > /etc/nginx/conf.d/server_tokens.conf
