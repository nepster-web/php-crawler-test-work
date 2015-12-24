@echo off
@setlocal

set CWR_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%CWR_PATH%cwr" %*

@endlocal
