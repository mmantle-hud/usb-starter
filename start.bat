@echo off
set "ROOT=%~dp0"
set "PATH=%ROOT%php;%ROOT%tools;%ROOT%node;%ROOT%npm-global;%ROOT%sqlite;%ROOT%git\cmd;%PATH%"
set "HOME=%ROOT%home"
if not exist "%ROOT%home" mkdir "%ROOT%home"
set "PHPRC=%ROOT%php"
set "COMPOSER_HOME=%ROOT%composer-home"
set "COMPOSER_CACHE_DIR=%ROOT%composer-home\cache"
set "npm_config_cache=%ROOT%npm-cache"
set "npm_config_prefix=%ROOT%npm-global"

if not exist "%ROOT%www" mkdir "%ROOT%www"
pushd "%ROOT%www"

echo Working in: %CD%
php -v
node -v
sqlite3 --version
cmd /k