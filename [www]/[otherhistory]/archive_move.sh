#!/bin/bash
cd /home/sites/police/www/otherhistory
find . -maxdepth 1 -mtime +14 -name "*.txt" -exec mv {} '/home/sites/police/www/otherhistory/archive' \;