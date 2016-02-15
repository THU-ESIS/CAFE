# CAFE_PORTAL
To see detailed information about CAFE, please check the [wiki page](https://github.com/THU-EarthInformationScienceLab/CAFE_NODE/wiki).
##Before your Installation
`Notice`: This package can be installed in any web server. As the function of this package is limited by some other external applications, a Linux environment is required. To ensure the node work correctly, following applications have to be installed before your installation:
######1.	MySQL Server and Client (http://www.mysql.com/downloads/ )     
```Bash 
sudo apt-get install mysql-server mysql-client  #For Ubuntu user
```     
######2.	Apache 2 HTTP Server (http://httpd.apache.org/ )   
```Bash 
sudo apt-get install apache2     #For Ubuntu user
``` 
######3.	PHp5 with extensions
```Bash 
sudo apt-get install php5,php5-mysql,php5-curl,php5-tidy     #For Ubuntu user
``` 
##Installation procedures
######1.	Database preparation.     
You have to create a user name of your database system, obtain the ip address,access port,username and password
######2.	Creating a database.      
Create a database named `userdb`. Grand privileges to the user created in step 1.     
######3.	Create database tables.      
The path of initiation script is: `/CAFE_PORTAL/init.sql`     
You have to enter mySQL, use the database in step2 and run this script.      
You may use code `source init.sql` in MySQL      
######4.	Grand write permission.      
Grand write permission to `ts_search/assets` and `ts_search/protected/runtime`       
######5. Database access configuration.     
Find the file `ts_search/protected/config/main.php` and replace the database information in line `57` of your own.     
######6. Server access configuration.       
find the file `/CAFE_web/ts_search/protected/models/` TSInterface.php and modify the server ip in line2, for example:     
```Bash 
define('TSInterfaceROOT','http://100.101.100.111:8088/datamanager-worker/');   
``` 
######7. Modify the virtual directory.     
You should log in the Server with a root account or use sudo mode.  For Ubuntu users:
```Bash 
sudo vi /etc/apache2/sites-enabled/000-default  #After DocumentRoot
sudo /etc/init.d/apache2 restart    #restart Apache
``` 
