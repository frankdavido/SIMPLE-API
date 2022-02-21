<?php

$uri = getUriSegments();
$isInitCommand = isset($uri[2]) && $uri[1] === 'api' && $uri[2] === 'init';

try {
    // Set options
    $dboptions = array(
        PDO::ATTR_PERSISTENT => constants::PERSISTENT_DATABASE_CONNECTION === true,
        PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION //turn on errors in form of exception
    );
    // Create a new PDO instanace
    $dbconn = new PDO('mysql:host=' . constants::DATABASE_HOST . ';charset=utf8mb4', constants::DATABASE_USER, constants::DATABASE_PASSWORD, $dboptions);
} catch (PDOException $e) {
    $erroMessage = "Failed to connect to MySQL: " . $e->getMessage();
    /*
     * Inform site admin that an error occurred.
     * At thesame time, this ensures a normal site user or Api user won't see this echo. For security reason
     * To prevent leaking your password. Just log the error or send mail to admin
     */
    if ($isInitCommand) {
        echo $erroMessage; // or send a mail() to him/her.
        // TODO: Write logic to send mail
    }
    error_log($erroMessage);
    exit;
}



if ($isInitCommand && null !== TOPMOST_FILE && TOPMOST_FILE === 'init.php') {
    /*
     * Create database if not exist
     */
    try {
        $create__db = "CREATE DATABASE IF NOT EXISTS `" . constants::DATABASE_NAME . "` CHARACTER SET utf8 COLLATE utf8_unicode_ci";
        $stmt = $dbconn->exec($create__db);
        /*
         * Check if database existed already
         * That is if it had been initialized before or not
         */
        if (1 === (int) $stmt) {
            // NO! database did not exist already
            echo "Created database: `" . constants::DATABASE_NAME . "` successfully<br>";
        } else {
            echo "Database already initialized.<br>";
        }
        /*
         * Use the database
         */
        try {
            $dbconn->exec('USE ' . constants::DATABASE_NAME);
        } catch (PDOException $e) {
            echo "Error using Database - (" . constants::DATABASE_NAME . ") " . $e->getMessage();
            exit;
        }

        /*
         * ================= TABLES =========================
         *
         * Structure of the employees Table
         */
        $create_users_table = "CREATE TABLE IF NOT EXISTS `employees` (
            `userid` BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) not null,
            `first_name` VARCHAR(50) not null,
            `last_name` VARCHAR(50) not null,
            `email` VARCHAR(100) not null,
            `country` VARCHAR(32) not null DEFAULT 'Nigeria',
            `age` int(11) NOT NULL,
            `role` varchar(255) NOT NULL,
            `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `username` (`username`),
            UNIQUE KEY `email` (`email`),
            KEY `country` (`country`)
        ) CHARACTER SET utf8 ENGINE=InnoDB";
        /*
         * Now Create the Table
         */
        try {
            $dbconn->exec($create_users_table);
            echo "Created Table: `employees` successfully<br>";
        } catch (PDOException $e) {
            echo "Error creating Table - (employees): " . $e->getMessage();
            exit;
        }

        /*
         * Insert Test Datas into database
         */
        if (true === constants::CREATE_TEST_DATA) {
            try {
                echo "Attempts to creating Test Data... <br>";
                $dbconn->beginTransaction();
                $stmt = $dbconn->prepare("INSERT INTO `employees` (username, first_name, last_name, email, age, `role`) VALUES (?, ?, ?, ?, ?, ?)");
                try {
                    if (!$stmt->execute(['Micahel.Lee', 'Michael', 'Lee', 'michael.xxxx@example.com', 40, 'Human Resource Manager'])) {
                        throw new Exception('Error creating Test data for HR');
                    }
                    echo "Created Test data for HR successfully<br>";
                } catch (PDOException $p) {
                    if ($p->errorInfo[1] === 1062) {
                        // If its is a duplicate entry. $e->errorInfo[1] will be 1062
                        echo "Test data for HR already inserted<br>";
                    } else {
                        throw new Exception($p->getMessage());
                    }
                }
                try {
                    if (!$stmt->execute(['frank.david', 'Ukah', 'Franklin', 'ukah.frankxxx@example.com', 34, 'Senior Php Api Developer'])) {
                        throw new Exception('Error creating Test data for Developer');
                    }
                    echo "Created Test data for Developer successfully<br>";
                } catch (PDOException $p) {
                    if ($p->errorInfo[1] === 1062) {
                        // If its is a duplicate entry. $e->errorInfo[1] will be 1062
                        echo "Test data for Developer already inserted<br>";
                    } else {
                        throw new Exception($p->getMessage());
                    }
                }
                $stmt = null;
                $dbconn->commit();
                echo "Ok<b>";
            } catch (Exception $e) {
                $dbconn->rollback();
                throw $e;
            }
        }
    } catch (PDOException $e) {
        echo "Error using Database - (" . constants::DATABASE_NAME . ") " . $e->getMessage();
        exit;
    }
} else {
    /*
     * Use already created database
     */
    try {
        $dbconn->exec('USE ' . constants::DATABASE_NAME);
    } catch (PDOException $e) {
        // Fail silently. And send mail to the admin. Do not echo.
        // To prevent leaking your password. Just log the error or send mail to admin
        //// echo "Error using Database - (" . constants::DATABASE_NAME . ") " . $e->getMessage();
        error_log($e->getMessage());
        // TODO: Write logic to send mail
        exit;
    }
}
