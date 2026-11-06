@echo off
set "ROOT=%~dp0"
set "PATH=%ROOT%php;%ROOT%tools;%ROOT%git\cmd;%PATH%"
set "HOME=%ROOT%home"
if not exist "%ROOT%home" mkdir "%ROOT%home"
set "PHPRC=%ROOT%php"
set "COMPOSER_HOME=%ROOT%composer-home"
set "COMPOSER_CACHE_DIR=%ROOT%composer-home\cache"
set "npm_config_cache=%ROOT%npm-cache"
set "npm_config_prefix=%ROOT%npm-global"

echo Starting Adminer...
start "Adminer" php -S localhost:8081 -t "%ROOT%adminer"

echo Adminer available at http://localhost:8081/adminer.php

if not exist "%ROOT%www" mkdir "%ROOT%www"
pushd "%ROOT%www"

echo Working in: %CD%
php -v
node -v
cmd /k