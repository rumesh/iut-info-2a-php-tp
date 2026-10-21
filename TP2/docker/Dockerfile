# Image PHP + Apache, avec le pilote PDO pour PostgreSQL (pdo_pgsql).
# L'image officielle "php" ne l'installe pas par défaut : on l'ajoute ici.
FROM php:8.3-apache

# libpq-dev contient la bibliothèque cliente PostgreSQL dont pdo_pgsql a besoin.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Réglages "développement" (par défaut) : les erreurs PHP s'affichent dans la
# page, ce qui est bien plus pratique pour corriger son code pendant le TP.
# Sur un serveur public, construire avec PHP_ENV=production : les erreurs sont
# alors masquées (elles révéleraient chemins, requêtes et accès à la base).
ARG PHP_ENV=development
RUN cp "$PHP_INI_DIR/php.ini-$PHP_ENV" "$PHP_INI_DIR/php.ini" \
    && if [ "$PHP_ENV" = "production" ]; then \
        printf '%s\n' \
            'expose_php = Off' \
            'session.use_strict_mode = 1' \
            'session.cookie_httponly = 1' \
            'session.cookie_secure = 1' \
            'session.cookie_samesite = Lax' \
            'session.upload_progress.enabled = Off' \
            > "$PHP_INI_DIR/conf.d/zz-production.ini"; \
        # Aucun formulaire du site n'envoie de fichier : on refuse toute requête
        # multipart/form-data avant qu'elle n'atteigne PHP (protection contre
        # PHP2Shell, CVE-2026-109618 / CVE-2026-109619).
        printf '%s\n' \
            "<If \"req('Content-Type') =~ m#multipart/#i\">" \
            '    Require all denied' \
            '</If>' \
            > /etc/apache2/conf-enabled/zz-deny-multipart.conf; \
    fi

# Ne pas annoncer les versions exactes d'Apache et de PHP aux visiteurs.
RUN printf '%s\n' 'ServerTokens Prod' 'ServerSignature Off' \
        > /etc/apache2/conf-enabled/zz-hide-version.conf

# Apache ne sert jamais les fichiers cachés (.git, .env...) ni les fichiers
# de configuration Docker (docker-compose.yml) : ils répondent "404 Not Found".
RUN printf '%s\n' \
        'RedirectMatch 404 "/\."' \
        'RedirectMatch 404 "\.ya?ml$"' \
        > /etc/apache2/conf-enabled/hide-private-files.conf
