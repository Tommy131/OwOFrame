@echo off
set TITLE=OwOFrame Git ToolScript
title %TITLE%
set VERSION=v1.0.1
set OWO_VERSION=1.0.5-dev
set PREFIX=[OwOTools]
set GIT="C:\Program Files\Git\bin\git.exe"

if not exist %GIT% (
    set GIT=git
)

echo -------------------------------------------------------------
echo          %TITLE% is Running @%VERSION%
echo -------------------------------------------------------------
echo Git Enviroment needed!
echo.
echo.
echo.


:begin
set input=4
echo Please choose one selection (Default=[4]):
echo [0] git pull                 Get current branch latest changes from GitHub upstream
echo [1] git status               Get the change status of the current local branch
echo [2] git checkout master      To change current branch to "master"
echo [3] git checkout %OWO_VERSION%   To change current branch to "%OWO_VERSION%"
echo [4] cmd                      Run normal CMD in current path
echo [5] composer install         To install OwOFrame in current path
echo [x] exit                     Exit the toolbox
set /p input=%PREFIX% Your choose: 
echo.
echo.


if %input% == 0 goto 0
if %input% == 1 goto 1
if %input% == 2 goto 2
if %input% == 3 goto 3
if %input% == 4 goto 4
if %input% == 5 goto 5
if %input% == 6 goto 6
if %input% == x goto x
goto begin


:0
cls
echo Checking upstrem...
%GIT% pull
echo.
echo.
echo.
goto begin


:1
cls
echo Checking locate status...
%GIT% status
echo.
echo.
echo.
goto begin


:2
%GIT% checkout master
goto x


:3
%GIT% checkout %OWO_VERSION%
goto x


:4
cmd


:5
start cmd.exe /k "composer install"
goto x


:x
echo.
echo.
echo ---------------------
echo %PREFIX% Byebye~
echo ---------------------
echo.
pause
exit