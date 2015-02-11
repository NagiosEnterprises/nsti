. install/libinstall.sh

cp ${BASEPATH}/nsti/dist/apache.conf /etc/httpd/conf.d/nsti.conf

# Applying patch for Apache 2.4 configs in CentOS 7
# without this patch, access is denied to all pages in NSTI
if [ $dist -eq 'el7' ]; then
    echo "Applying patches to Apache configs for Apache 2.4.x syntax..."
    sed -i '/<\/Directory>/c\   Require all granted\n</Directory>' /etc/httpd/conf.d/$f.conf
fi

service httpd restart