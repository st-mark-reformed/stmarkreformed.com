FROM nginx:1.25.5

# Update bash profile
COPY ./docker/.profile /root/.profile
COPY ./docker/.profile /root/.bashrc

COPY ./docker/proxy/default.conf /etc/nginx/conf.d/default.conf

RUN echo 'server_tokens off;' > /etc/nginx/conf.d/server_tokens.conf
