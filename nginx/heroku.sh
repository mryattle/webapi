
#!/bin/bash


echo "Replace PORT with: $1"
cp /prod.conf /etc/nginx/conf.d/default.conf
sed -i 's|80|'"$1"'|g' /etc/nginx/conf.d/default.conf
/usr/sbin/nginx -c /etc/nginx/nginx.conf -g 'daemon off;'