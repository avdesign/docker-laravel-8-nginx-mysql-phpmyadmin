<p align="center"><a href="#" target="_blank"><img src="https://painel.avdesign.com.br/img/logo/login-title.png"></a></p>

<p align="center">
<a href="#"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="#"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="#"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="#"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

# Demo - Docker/Laravel/Nginx/Mysql/Phpmyadmin

Este é um app de demonstração do Laravel, criado para servir como base de projetos.

Depois de instalar o **docker** e o **docker-compose**, você pode colocar este ambiente em funcionamento com:

Clone o Repositório e renomeie o diretório `docker-laravel-8-nginx-mysql-phpmyadmin` para `laravel_app` ou um nome de sua escolha.
````
$ git clone https://github.com/avdesign/docker-laravel-8-nginx-mysql-phpmyadmin.git

$ mv docker-laravel-8-nginx-mysql-phpmyadmin laravel_app
````
Copie o arquivo `.env.example` para o arquivo `.env` para personalizar a configuração do ambiente.
````
$ cp .env.example .env
````
Usaremos os comandos do docker-compose para compilar a imagem do app e executar os serviços que especificamos em nossa configuração do `docker-compose.yml`
````
$ docker-compose build app
````
Quando a compilação terminar, execute o ambiente em modo de segundo plano com:
````
$ docker-compose up -d
````
Agora, seu ambiente está funcionando! Porém, ainda precisaremos executar alguns comandos para concluir a configuração do app.
````
$ docker-compose exec app composer install
````
Antes de testar, gerar uma chave única para o app com:
````
$ docker-compose exec app php artisan key:generate
````
Teste a conexão com o MySQL executando o comando:
````
$ docker-compose exec app php artisan migrate --seed
````


### Nota 
O volume nomeado `dbdata` mantém o conteúdo da pasta `/var/lib/mysql` dentro do contêiner. Isso permite que você pare e reinicie os serviços sem perder os dados do banco de dados.


# Tutorial desta demo:

### Instalação do Laravel 
Escolhendo uma versão:
````
$ cd ~
$ composer create-project --prefer-dist laravel/laravel:^7.0 laravel_app
````
Versão mais recente:
````
$ composer create-project --prefer-dist laravel/laravel laravel_app
````
Navegue até o diretório `laravel_app`
````
$ cd laravel_app
````
 vamos criar um novo arquivo `.env` para personalizar as opções de configuração do ambiente de desenvolvimento.
 ````
 $ cp .env.example .env
 ````
 Abra este arquivo usando o `nano` ou um editor de texto de sua escolha:
 ````
 $ nano .env
 ````
 vamos chamar nosso serviço de banco de dados de `db`. Substitua o valor do **DB_HOST** pelo nome do serviço de banco de dados:
 ````
|---------------------------
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=laravel_app
    DB_USERNAME=laravel_user
    DB_PASSWORD=secret
----------------------------|
 ````
 ### Configurando o Dockerfile
 Crie um novo arquivo `Dockerfile` usando o `nano` ou um editor de texto de sua escolha: 
 ````
 $ nano Dockerfile
 ````
 Copie os seguintes conteúdo para seu Dockerfile:
 ````
 FROM php:7.4-fpm

# Argumentos definidos em docker-compose.yml
ARG user
ARG uid

# Instale as dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instale as extensões necessárias do PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Receber as últimas as configurações do Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crie um usuário do sistema para executar os comandos Composer e Artisan
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Definir diretório de trabalho
WORKDIR /var/www

USER $user
````
### Configurando os arquivos do Nginx
Para configurar o Nginx, compartilharemos um arquivo `app.conf` que configurará o app no servidor. Crie a pasta **.docker/nginx** com:
````
$ mkdir -p .docker/nginx
````
Crie um novo arquivo chamado app.conf, dentro desse diretório:
````
$ nano .docker/nginx/app.conf
````
Copie a seguinte configuração do Nginx para aquele arquivo:
````
server {
    listen 80;
    index index.php index.html;
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
    root /var/www/public;
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
    location / {
        try_files $uri $uri/ /index.php?$query_string;
        gzip_static on;
    }
}
````
### Configurar o banco de dados do MySQL
Crie uma nova pasta para seus arquivos de log do MySQL dentro da pasta `.docker`:
````
$ mkdir .docker/mysql
````
Crie um novo arquivo app.cnf:
````
$ nano .docker/mysql/app.cnf
````
### Configurando o serviço do PHP
Crie o diretório `php`
````
$ mkdir php
````
Crie o arquivo `local.ini`, para configurar o serviço PHP e atuar como um processador PHP para solicitações de entrada do Nginx.

A criação deste arquivo permitirá que você substitua o arquivo padrão `php.ini` que o PHP lê ao iniciar.
````
$ nano .docker/php/local.ini
````
Copie os seguintes conteúdo para seu arquivo `local.ini`:
````
upload_max_filesize=100M
post_max_size=100M

````
### Criando um ambiente multi-contêiner com o Docker Compose
Crie um arquivo `docker-compose.yml` na raiz da pasta do app:
````
$ nano docker-compose.yml
````
Copie os seguintes conteúdo para seu arquivo `docker-compose.yml`:
````
version: "3.7"
services:
  #PHP Service
  app:
    build:
      args:
        user: sammy
        uid: 1000
      context: ./
      dockerfile: Dockerfile
    image: laravel_app
    container_name: laravel_app
    restart: unless-stopped
    working_dir: /var/www/
    volumes:
      - ./:/var/www
      - .docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - network  

  #MySQL Service
  db:
    image: mysql:5.7
    container_name: larevel_db
    restart: unless-stopped
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_USER: ${DB_USERNAME}
      SERVICE_TAGS: dev
      SERVICE_NAME: mysql
    volumes:
      - .docker/dbdata:/var/lib/mysql/
      - .docker/mysql/app.cnf:/etc/mysql/my.cnf
    networks:
      - network   


  #Nginx Service
  nginx:
    image: nginx:alpine
    container_name: larevel_nginx
    restart: unless-stopped
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www
      - .docker/nginx:/etc/nginx/conf.d
    networks:
      - network

  #Phpmyadmin
  pma:
    depends_on:
      - db  
    image: phpmyadmin/phpmyadmin
    container_name: larevel_pma
    environment:
      PMA_ARBITRARY: 1
      PMA_HOST: db
      PMA_USER: ${DB_USERNAME}
      PMA_PASSWORD: ${DB_PASSWORD}
    restart: unless-stopped
    ports:
      - "8080:80"
    networks:
      - network
      
#Docker Networks
networks:
  network:
    driver: bridge

#Volumes
volumes:
  dbdata:
    driver: local
````
### Comandos Básicos
Permissão para o diretório `dbdata`
````
$ sudo chmod -R 775 .docker/dbdata/*
````
Para exibir informações detalhadas sobre os arquivos no diretório do app:
````
$ docker-compose exec app ls -l
````
Comando logs para verificar os registros gerados por seus serviços:
````
$ docker-compose logs nginx
````
Pausar seu ambiente, mantendo o estado de todos seus serviços:
````
$ docker-compose pause
````
Reiniciar seus serviços com:
````
$ docker-compose unpause
````
Remover todos os seus contêineres, redes e volumes, execute:
````
$ docker-compose down
````




