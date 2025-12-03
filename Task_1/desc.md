# Task 1: Build web có lỗ hổng SQL Injection (1-2 tuần)

Dev một website có lỗ hổng SQL Injection từ cơ bản đển RCE full dạng, thỏa mãn điều kiện sau:

+ Có thể dùng được tất cả payload (dùng nhiều DB) trên PayloadAllTheThings
+ Có thể dùng bất kỳ ngôn ngữ gì
+ Không yêu cầu màu mè, chỉ cần có lỗ hổng
+ Hạn chế dùng AI dev, chỉ dùng tham khảo, cần tự dev để hiểu bản chất lỗ hổng

-> Review cuối task: demo và viết writeup lỗ hổng (có thể)

----

## Lên idea các ý

### 1 => error based, union based, blind(time, boolean),out of band, stacked query, stored procedure, sqli to rce

error based: search, union: search, blind: login, blind: 

### 2 => dung PHP 7.4

### stacked query supported

php driver ho tro stacked query:

+ mysql
    1. mysqli::multi_query()
    2. PDO_MYSQL (can enable them vai option)

+ sqlite
    1. sqlite3::exec()
    2. PDO::exec()

+ postgresql
    1. `pg_send_query`
    2. PDO...

> `PDO_PGSQL` driver khong ho tro multi query trong mot lan call prepare(), query() tren cac phien ban moi => mot so version cu co' support

+ mssql
    1. sqlsrv::sqlsrv_query()
    2. pdo::query(), pdo::execute()

> may cai PDO... driver can enable them vai option

### sqli to rce

+ mysql
    1. file-write with `into outfile`, `into dumpfile`
    2. if `UDF Library` existed -> call `sys_exec`, `sys_eval`
+ sqlite
    1. attach database
    2. load_extension
    3. write file
+ postgresql
    1. copy to write files - write webshell, copy to program...
    2. using libc.so.6
+ mssql
    1. xp_cmdshell()
    2. aaa

### cấu trúc thư mục

đơn giản, dễ hiểu

### docker

single container, phần mssql server sẽ được dựng bên ngoài máy ảo độc lập, nên không cần dựng nó trong docker

## writeup

với mỗi case viết:

+ nguyên nhân
+ điều kiện khai thác
+ exploit 
