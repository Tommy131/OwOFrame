#!/bin/bash
VERSION="v1.0.0"
OWO_VERSION="1.0.5-dev"
PREFIX="[OwOTools]"

echo "-------------------------------------------------------------"
echo "         OwOFrame Git Toolbox is Running ${VERSION}"
echo "-------------------------------------------------------------"
echo "Git Enviroment needed!"
echo -e
echo -e
echo -e


echo "${PREFIX} Please choose one selection:"
echo "[0] git pull                 Get current branch latest changes from GitHub upstream"
echo "[1] git status               Get the change status of the current local branch"
echo "[2] git checkout master      To change current branch to "master""
echo "[3] git checkout ${OWO_VERSION}   To change current branch to "${OWO_VERSION}""
echo "[4] composer install         To install OwOFrame in current path"
echo "[x] exit                     Exit the toolbox"
read -p "${PREFIX} Your choose: " INPUT
echo -e
echo -e

if [ $INPUT ]
then
    if [ $INPUT == 0 ]
    then
        echo "Checking upstrem..."
        git pull
        echo -e
        echo -e
        echo -e
    elif [ $INPUT == 1 ]
    then
        echo "Checking locate status..."
        git status
        echo -e
        echo -e
        echo -e
    elif [ $INPUT == 2 ]
    then
        git checkout master
    elif [ $INPUT == 3 ]
    then
        git checkout $OWO_VERSION
    elif [ $INPUT == 4 ]
    then
        composer install
    elif [ $INPUT == "x" ]
    then
        echo -e
        echo -e
        echo "---------------------"
        echo "${PREFIX} Byebye~"
        echo "---------------------"
        echo -e
        exit
    else
        echo "Unknown usage! Script will terminate here."
    fi
else
    echo "${PREFIX} Error Input, stop to run this script."
fi

echo -e "Press any keys to continue..."
read -n 1
exit