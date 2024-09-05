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

# Copy custom Nginx configuration template and replace $PORT
COPY nginx.conf /tmp/nginx.conf
RUN sed -i "s/\$PORT/${PORT}/g" /tmp/nginx.conf && \
  mv /tmp/nginx.conf /etc/nginx/nginx.conf

COPY --chown=nobody --from=composer /app /var/www/html

# Set proper permissions for nginx.conf
RUN chown nobody:nobody /etc/nginx/nginx.conf && \
  chmod 644 /etc/nginx/nginx.conf

# Expose the port
EXPOSE ${PORT}