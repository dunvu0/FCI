#!/bin/bash

# Wait for SQL Server to start
sleep 30s

# Run the setup script
/opt/mssql-tools/bin/sqlcmd -S localhost -U sa -P SqliPass123! -d master -i /docker-entrypoint-initdb.d/init.sql

echo "MSSQL initialization completed"
