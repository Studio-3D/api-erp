#!/bin/bash

# Exit on error
set -e

echo "Starting Laravel API in DEVELOPMENT mode..."

# Check if Docker is installed
if ! [ -x "$(command -v docker)" ]; then
  echo 'Error: Docker is not installed.' >&2
  exit 1
fi

# Check if Docker Compose is installed
if ! [ -x "$(command -v docker-compose)" ]; then
  echo 'Error: Docker Compose is not installed.' >&2
  exit 1
fi

# Make dev.sh executable
chmod +x dev.sh

# Start the services
echo "Starting Docker services for development..."
docker-compose -f docker-compose.dev.yml down
docker-compose -f docker-compose.dev.yml up -d

echo "Services started successfully!"
echo ""
echo "Your Laravel API is now available at:"
echo "  - http://localhost:8000"
echo ""
echo "The local MySQL database is available at:"
echo "  - Host: localhost"
echo "  - Port: 3306"
echo "  - Username: root"
echo "  - Password: Kilo15.35"
echo "  - Database: erp_studio5d_1"
echo ""
echo "To stop the services, run:"
echo "  docker-compose -f docker-compose.dev.yml down"
