# Imagen oficial de PHP con server embebido
FROM php:8.2-cli

# Copiar archivos del proyecto al container
WORKDIR /app
COPY . /app

# Exponer el puerto que usa Render
EXPOSE 10000

# Comando que ejecuta PHP
CMD ["php", "-S", "0.0.0.0:10000", "index.php"]