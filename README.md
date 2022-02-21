# SIMPLE-API

A simple API with Core PHP to perform CRUD operations
## Some Securities Put In Place

`HTACCESS file`
The /.htaccess file has securities in place to
* Enforce HTTPS on all request (re writing all http to https)
* Redirect all requests coming into not-existing directories and non-existing files to root invalid.php
* Black list offending IPs. 
Note: I can make PHP to automatically update this htaccess' line number 16 to automatically blacklist any offeding User's IP if he fails certain criteria.
`db/dbconn.php file`
* Use of PDO prepared statement to avoid SQL injection (SQL attack is a technique to hack sites database from any input form of apis. To read more on SQL attacks, for a start, see: )

## ⭐ Installation

- Move all the files except the License and Readme files into your website document root directory
- Open /api/settings.php file and change the credentials and settings accordingly. Found in /api/settings.php
- If you wish to improve performance of database: Open the /api/settings.php file and change the PERSISTENT_DATABASE_CONNECTION to true. This uses persistence connection to database (which reduces the connection overhead)
- Initialize the installation. See below

## Initialization

Before using the api, you must manually create all the required database and tables by any other means - CLI, Bash, PhpMyadmin, etc.

OR

You must use allow our script to automagically create required database and Table for you by visiting  //youSiteAddress.com/api/init  from your browser.


## CRUD OPERATIONS: How To Use

```
### 1. Read/Get Single/Multiple Record
Request Method: GET
Accepted Payloads :- URLencoded form data, Multipart form data, JSON
Optional parameters :- userid, username, email, first_name, last_name, role, country, age.
Special parameters :-
	*sort - this can be used to sort the result, and may be any of the previous optional parameters above
	*limit - An integer eg 1,2,3,4,... used to limit the number results received
Examples:
	//youSiteAddress.com/api/search/?role=manager&limit=2&sort=created
	//youSiteAddress.com/api/search/?userid=1


### 2. Create/Add/Insert Single Record
Request Method :- POST
Accepted Payloads :- URLencoded form data, Multipart form data, JSON
Compulsory parameters :- username, email, first_name, last_name, role, age
Optional parameters :- country
Examples:
	//youSiteAddress.com/api/create/


### 3. Update a Single Record
Request Method :- PUT
Accepted Payloads :- URLencoded form data, Multipart form data, JSON
Compulsory parameters :- userid
Optional parameters :- username, email, first_name, last_name, role, age, country, created
Examples:
	//youSiteAddress.com/api/create/


### 4. Remove/Delete a Single Record
Request Method :- DELETE
Accepted Payloads :- URLencoded form data, Multipart form data, JSON
Compulsory parameters :- At least one (or more) of the following parameters - userid, username, email, first_name, last_name, role, age, country, created
Examples:
	//youSiteAddress.com/delete/create/


```

## HOW TO KNOW ERROR OCCURRED

Api returned for errors usually have a response status=error

Eg
```
{
  "message": "No employee record found with given parameter",
  "status": "error"
}
```
