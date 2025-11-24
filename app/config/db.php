<?php

/**
 * SQLite PDO Database Connection
 * This file:
 *  - Connects to SQLite
 *  - Ensures database folder exists
 *  - Creates "users" and "tasks" tables if missing
 *  - Enables foreign keys
 */

function getDB(): PDO
/*The function returns a PDO object, which is ang ating database connection
      This ensures the entire project uses ONE shared connection*/
{
    static $pdo = null;
    /* static means the variable remembers its value between function calls,
        It prevents the database from reconnecting multiple times*/

    // Reuse existing connection
    if ($pdo !== null) {
        return $pdo;
    }
    /*If $pdo already has a connection → return it immediately
        This avoids creating multiple connections
        This is called a singleton pattern*/


    // Path to database file
    //$dbPath = __DIR__ . '/../../database/database.db';

    $dbPath = dirname(__DIR__, 2) . '/database/database.db';

    // Create directory if missing
    if (!file_exists(dirname($dbPath))) {
        mkdir(dirname($dbPath), 0777, true);
    }

    /* Checks if the /database/ folder exists
        If not, it auto-creates it
        0777 = full access
        true = create parent folders if necessary
        This prevents errors on fresh installationdds
    */



    // Create PDO connection
    $pdo = new PDO("sqlite:" . $dbPath);

    /*  Connects to SQLite using PDO
        "sqlite:" . $dbPath tells PDO to use a SQLite database file
        If the file doesn’t exist, SQLite creates it automatically
        This is the main connection to database
    */

    // Throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //  Forces PDO to throw exceptions when errors happen
    //Helps with debugging
    //Prevents silent failures



    // Enable foreign key constraints
    $pdo->exec("PRAGMA foreign_keys = ON");
    /*  SQLite does NOT enable foreign keys by default
        We manually turn it on
        This ensures tasks are linked to users
        Also allows cascading delete/update 
    */





    /**
     * USERS TABLE
     * Holds authentication data
     */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at TEXT NOT NULL
        )
    ");

        /*  Creates users table if not already created
            This table is used for registration & login
          
        */







    /**
     * TASKS TABLE
     * Stores user's personal to-do items
     */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            description TEXT,
            date TEXT,
            created_at TEXT NOT NULL,

            FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        )
    ");
    /*  Creates tasks table if not already created
            user_id links tasks to the user
            Cascades:
                When user is deleted → tasks are deleted
                When user id changes → tasks update automatically
        */





    return $pdo;
    //Sends the PDO connection back to the caller
}
