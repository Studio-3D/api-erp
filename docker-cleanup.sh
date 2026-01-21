#!/bin/bash

# Docker Cleanup Script - Remove unnecessary images

echo "=========================================="
echo "Docker Images Cleanup"
echo "=========================================="
echo ""

# Remove all ECR images (already in AWS)
echo "Removing ECR images (already in AWS)..."
docker images | grep "amazonaws.com" | awk '{print $3}' | xargs -r docker rmi -f 2>/dev/null || true

# Remove dangling images (tagged as <none>)
echo "Removing dangling images..."
docker image prune -f

# Remove MySQL if not used (you use XAMPP)
echo "Removing MySQL image (using XAMPP)..."
docker rmi mysql:8 2>/dev/null || true

echo ""
echo "=========================================="
echo "Cleanup Complete!"
echo "=========================================="
echo ""
echo "Remaining images:"
docker images

echo ""
echo "Disk space saved:"
docker system df
