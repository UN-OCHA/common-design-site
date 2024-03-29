FROM public.ecr.aws/unocha/php-k8s:8.2-stable as builder
COPY . /srv/www
WORKDIR /srv/www
# Clean up previous composer installation and run new one.
RUN rm -rf ./vendor && \
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_MEMORY_LIMIT=-1 \
  composer install --no-interaction --no-dev --prefer-dist

# Copy settings to default site location.
RUN cp -a docker/settings.php docker/services.yml html/sites/default

FROM public.ecr.aws/unocha/php-k8s:8.2-stable

ARG VCS_REF
ARG VCS_URL
ARG BUILD_DATE
ARG GITHUB_ACTOR
ARG GITHUB_REPOSITORY
ARG GITHUB_SHA
ARG GITHUB_REF

ENV  NGINX_SERVERNAME=commondesign.demo.ahconu.org
ENV  PHP_ENVIRONMENT=production
ENV  PHP_MEMORY_LIMIT=256M
ENV  PHP_MAX_CHILDREN=16
ENV  GIT_BLAME=$GITHUB_ACTOR
ENV  GIT_REPO=$GITHUB_REPOSITORY
ENV  GIT_SHA=$GITHUB_SHA
ENV  GIT_REF=$GITHUB_REF
LABEL info.humanitarianresponse.build.date=$BUILD_DATE \
      info.humanitarianresponse.build.vcs-url=$VCS_URL \
      info.humanitarianresponse.build.vcs-ref=$VCS_REF

COPY --from=builder /srv/www/assets /srv/www/assets
COPY --from=builder /srv/www/config /srv/www/config
COPY --from=builder /srv/www/config_dev /srv/www/config_dev
COPY --from=builder /srv/www/html /srv/www/html
COPY --from=builder /srv/www/vendor /srv/www/vendor
COPY --from=builder /srv/www/composer.json /srv/www/composer.json
COPY --from=builder /srv/www/composer.patches.json /srv/www/composer.patches.json
COPY --from=builder /srv/www/composer.lock /srv/www/composer.lock
COPY --from=builder /srv/www/PATCHES /srv/www/PATCHES
COPY --from=builder /srv/www/scripts /srv/www/scripts
