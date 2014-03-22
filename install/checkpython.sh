#!/bin/sh

PYTHON_URL='https://www.python.org/ftp/python/2.7/Python-2.7.tar.bz2'
PYTHON_VER='Python-2.7'
PYTHON_CFG='--enable-shared'

install_python () {
    (
        cd /tmp

        if [ ! -f "${PYTHON_VER}.tar.bz2" ];
        then
            wget "$PYTHON_URL" --no-check-certificate
        fi

        tar xf "${PYTHON_VER}.tar.bz2"
        cd "$PYTHON_VER"
        ./configure $PYTHON_CFG
        make && make altinstall

        cd ..
        rm "${PYTHON_VER}.tar.bz2" -f
    )
}

install_python_pip () {
    (
        cd /tmp

        if [ ! -f get-pip.py ];
        then
            wget 'https://raw.github.com/pypa/pip/master/contrib/get-pip.py' --no-check-certificate
        fi

        python2.7 get-pip.py

        rm get-pip.py -f
    )
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
