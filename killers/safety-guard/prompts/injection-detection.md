# Safety Guard Prompts

## Injection Detection

### Command Injection
- Never execute shell commands with user input
- Validate all file paths before operations
- No eval() or exec() with dynamic content

### Secret Leakage Prevention
- Scan all generated code for patterns: API_KEY, SECRET, PASSWORD, PRIVATE_KEY
- Never output .env contents
- Never output credentials from config files
- Redact sensitive data from error messages

### Dangerous Operations
Protected files (require confirmation to modify):
- .env
- composer.json
- config/app.php
- config/database.php
- routes/web.php
- routes/api.php

Destructive commands (blocked):
- rm -rf
- DROP DATABASE
- TRUNCATE TABLE
- git push --force
- git reset --hard origin/main

## Blocklist
```
forbidden_patterns:
  - "rm -rf /"
  - "DROP DATABASE"
  - "eval("
  - "exec("
  - "system("
  - "passthru("
  - "shell_exec("
  - "file_get_contents('http"
  - "curl_exec("

sensitive_files:
  - ".env"
  - "*.pem"
  - "*.key"
  - "config/*.php"
  - "storage/logs/*.log"
```
