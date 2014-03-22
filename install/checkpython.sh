#!/bin/sh

PIP_TAR='https://pypi.python.org/packages/source/p/pip/pip-1.1.tar.gz'

install_python_pip () {
    (
        cd /tmp

        if [ ! -f pip-1.1.tar.gz ];
        then
            wget "${PIP_TAR}" --no-check-certificate
        fi
        
        tar xf pip-1.1.tar.gz
        cd pip-1.1
        python setup.py install

        cd ..
        rm -rf pip-1.1.tar.gz
        rm -rf pip-1.1
    )
}

echo "Checking if pip is installed..."
if ! is_installed pip;
then
    install_python_pip
fi
