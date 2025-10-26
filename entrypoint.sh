#!/bin/bash
set -e

# Muda para o diretório correto
cd /var/www/html

# Verifica se o diretório vendor existe
if [ ! -d "vendor" ]; then
    echo "🔧 Instalando dependências do Composer..."
    composer install --no-interaction --no-dev --optimize-autoloader
    
    # Garante que as permissões estão corretas
    chown -R www-data:www-data /var/www/html
fi

# Inicia o Apache
exec apache2-foreground

