#!/bin/sh

docker exec -i starterkit-db psql -U postgres -tc "SELECT 1 FROM pg_database WHERE datname = 'starterkit_test';" | grep -q 1 && {
    echo "Banco starterkit_test já existe."
    exit 0
}

docker exec -i starterkit-db psql -U postgres -c "CREATE DATABASE starterkit_test;"
echo "Banco starterkit_test criado."
