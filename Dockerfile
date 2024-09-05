FROM composer AS composer

# copying the source directory and install the dependencies with composer
COPY ./app /app

# run composer install to install the dependencies
RUN composer install \
  --optimize-autoloader \
  --no-interaction \
  --no-progress

# continue stage build with the desired image and copy the source including the
# dependencies downloaded by composer
FROM trafex/php-nginx

# Add environment variable for port
ENV PORT=8080

# Copy custom Nginx configuration template
COPY nginx.conf.template /etc/nginx/nginx.conf.template

COPY --chown=nobody --from=composer /app /var/www/html

# Use envsubst to replace PORT in nginx.conf
RUN envsubst '$PORT' < /etc/nginx/nginx.conf.template > /tmp/nginx.conf && \
  mv /tmp/nginx.conf /etc/nginx/nginx.conf && \
  rm /etc/nginx/nginx.conf.template && \
  chown nobody:nobody /etc/nginx/nginx.conf && \
  chmod 644 /etc/nginx/nginx.conf

# Expose the port
EXPOSE ${PORT}