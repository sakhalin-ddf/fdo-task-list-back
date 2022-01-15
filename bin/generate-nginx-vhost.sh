#! /bin/bash

__FILE__="$(readlink -e "${BASH_SOURCE[0]}")"
__DIR__="$(dirname "${__FILE__}")"

HOST="${1:-fdo-task-list.local}"
WEB_ROOT="$(readlink -e ${__DIR__}/../public)"

cat << EOT
server {
    listen      443;
    server_name ${HOST};
    return      301 http://${HOST}\$request_uri;
}

server {
    listen      80;
    server_name ${HOST};
    index       index.html;
    root        ${WEB_ROOT};

    client_max_body_size 128M;

    location / {
        try_files \$uri \$uri/ /index.html?\$query_string;
    }

    location ~* /api {
        try_files   \$uri /index.php\$is_args\$args;
    }

    location ~\.php\$ {
        root        ${WEB_ROOT};

        include     fastcgi_params;

        set_real_ip_from        10.0.0.0/8;
        set_real_ip_from        127.0.0.0/8;
        set_real_ip_from        172.16.0.0/12;
        set_real_ip_from        192.168.0.0/16;
        real_ip_header          X-Forwarded-For;
        real_ip_recursive       on;

        fastcgi_index           index.php;
        fastcgi_param           DOCUMENT_ROOT   \$document_root;
        fastcgi_param           SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_pass            php8.0;
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
    }

    location ~ ^.+\.(jpg|jpeg|gif|png|ico|css|css\.map|pdf|ppt|txt|bmp|rtf|js|js\.map|ttf|otf|woff|woff2|svg)\$ {
        expires    3d;
        try_files  \$uri =404;
        access_log off;
    }
}
EOT