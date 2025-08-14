#!/usr/bin/env bash

GROUP="www-data"

DIRECTORIES=(
    "bootstrap"
    "storage"
    "storage/logs"
    "storage/framework/cache/data"
)

install_acl_if_missing() {
    if ! command -v setfacl >/dev/null 2>&1; then
        echo "Instalando suporte a ACL..."
        if command -v apk >/dev/null 2>&1; then
            apk add --no-cache acl
        elif command -v apt-get >/dev/null 2>&1; then
            apt-get update && apt-get install -y acl && rm -rf /var/lib/apt/lists/*
        else
            echo "Gerenciador de pacotes não suportado. Instale 'acl' manualmente."
            exit 1
        fi
    fi
}

change_group_and_permissions() {
    local dir="$1"

    if [[ ! -d "$dir" ]]; then
        echo "Diretório $dir não encontrado. Pulando."
        return
    fi

    echo "Aplicando permissões em $dir"

    chgrp -R "$GROUP" "$dir"
    chmod -R u+rwX,g+rwX "$dir"

    find "$dir" -type d -print0 | xargs -0 chmod g+s

    setfacl -R -m u::rwX,g::rwX,o::rX "$dir"
    setfacl -dR -m u::rwX,g::rwX,o::rX "$dir"
}

install_acl_if_missing

for dir in "${DIRECTORIES[@]}"; do
    change_group_and_permissions "$dir"
done

echo "Permissões e ACLs aplicadas com sucesso."
