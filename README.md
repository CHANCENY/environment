# Environment Library

## Overview
The Environment Library is designed to manage configuration settings and environment variables for your PHP applications.
It provides an easy-to-use interface for loading, retrieving, and setting environment variables to streamline application development.

---

## Features
- Load environment variables from files.
- Retrieve environment variables with default fallbacks.
- Dynamically set environment variables.
- Exception handling for invalid configurations.

---

## Installation
To use the Environment Library, include it in your project directory or install it via Composer:

```bash
composer require simp/environment
```

---

## Usage

### Loading Environment Variables
You can load environment variables:

```php
// Save configuration
\Simp\Environment\Environment::create("server_host", [
    "host" => "localhost",
    "port" => 3306,
    "database" => "database",
    "username" => "chance_",
    "password" => "12345",
    "prefix" => "chance_"
]);

// Get configuration data.
\Simp\Environment\Environment::load('server_host')
```

## License
This project is licensed under the MIT License. See the LICENSE file for details.

---

## Contribution
Contributions are welcome! Please fork the repository and submit a pull request with your changes.