# Image PHP + Apache, avec le pilote PDO pour PostgreSQL (pdo_pgsql).
# L'image officielle "php" ne l'installe pas par défaut : on l'ajoute ici.
FROM php:8.3-apache

# libpq-dev contient la bibliothèque cliente PostgreSQL dont pdo_pgsql a besoin.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Réglages "développement" : les erreurs PHP s'affichent dans la page,
# ce qui est bien plus pratique pour corriger son code pendant le TP.
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Apache ne sert jamais les fichiers cachés (.git, .env...) ni les fichiers
# de configuration Docker (docker-compose.yml) : ils répondent "404 Not Found".
RUN printf '%s\n' \
        'RedirectMatch 404 "/\."' \
        'RedirectMatch 404 "\.ya?ml$"' \
        > /etc/apache2/conf-enabled/hide-private-files.conf
