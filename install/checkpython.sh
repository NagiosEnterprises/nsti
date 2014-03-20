#!/bin/sh

. ./libinstall.sh

PYTHON_URL='https://www.python.org/ftp/python/2.7/Python-2.7.tar.bz2'
PYTHON_VER='Python-2.7'
PYTHON_CFG='--enable-shared'

install_python () {
    cd /tmp
    wget "$PYTHON_URL"
    tar xf "${PYTHON-VER}.tar.bz2"
    cd "$PYTHON-VER"
    ./configure $PYTHON_CFG
    make && make altinstall
}

install_python_pip () {
    
}

echo "Checking Python version..."
if ! is_installed python2.7;
then
    install_python
fi

echo "Checking if pip is installed..."
if ! is_installed pip2.7;
then
    install_python_pip
fi