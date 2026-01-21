#!/bin/bash

# AWS Laravel API Deployment Script
# This script deploys your Laravel API to AWS using ECR + App Runner

set -e

# Configuration
AWS_REGION="eu-west-1"  # App Runner available region (Ireland, close to your RDS in Stockholm)
ECR_REPO_NAME="immogestion-api"
APP_RUNNER_SERVICE_NAME="immogestion-api"
APP_RUNNER_CPU="1024"     # 1 vCPU (cost-effective, can upgrade later)
APP_RUNNER_MEMORY="2048"  # 2 GB (sufficient for Laravel)

echo "=========================================="
echo "AWS Laravel API Deployment"
echo "=========================================="
echo ""

# Force Docker AWS CLI for Windows compatibility
echo "Using Docker to run AWS CLI..."
# Function to run AWS CLI via Docker
aws_cli() {
    docker run --rm \
        -e AWS_ACCESS_KEY_ID="$AWS_ACCESS_KEY_ID" \
        -e AWS_SECRET_ACCESS_KEY="$AWS_SECRET_ACCESS_KEY" \
        -e AWS_DEFAULT_REGION="$AWS_DEFAULT_REGION" \
        amazon/aws-cli "$@"
}

# Get AWS Account ID
echo "Getting AWS account information..."
AWS_ACCOUNT_ID=$(aws_cli sts get-caller-identity --query Account --output text 2>&1)

if [ $? -ne 0 ] || [ -z "$AWS_ACCOUNT_ID" ]; then
    echo "Failed to get AWS account ID. Please configure AWS credentials."
    echo "Error: $AWS_ACCOUNT_ID"
    echo ""
    echo "Set these environment variables:"
    echo "  export AWS_ACCESS_KEY_ID=your_access_key"
    echo "  export AWS_SECRET_ACCESS_KEY=your_secret_key"
    echo "  export AWS_DEFAULT_REGION=us-east-1"
    exit 1
fi

echo "AWS Account ID: $AWS_ACCOUNT_ID"
echo "AWS Region: $AWS_REGION"
echo ""

# Step 1: Create ECR Repository
echo "Step 1: Creating ECR repository..."
aws_cli ecr describe-repositories --repository-names $ECR_REPO_NAME --region $AWS_REGION > /dev/null 2>&1 || \
aws_cli ecr create-repository \
    --repository-name $ECR_REPO_NAME \
    --region $AWS_REGION \
    --image-scanning-configuration scanOnPush=true

ECR_URI="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPO_NAME"
echo "   ECR URI: $ECR_URI"
echo ""

# Step 2: Build Docker Image
echo "Step 2: Building Docker image..."
docker build -f Dockerfile.aws -t $ECR_REPO_NAME:latest .
echo "   Image built"
echo ""

# Step 3: Login to ECR and Push
echo "Step 3: Pushing to ECR..."
aws_cli ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $ECR_URI

docker tag $ECR_REPO_NAME:latest $ECR_URI:latest
docker tag $ECR_REPO_NAME:latest $ECR_URI:$(git rev-parse --short HEAD 2>/dev/null || echo "manual")

docker push $ECR_URI:latest
docker push $ECR_URI:$(git rev-parse --short HEAD 2>/dev/null || echo "manual") || true
echo "   Image pushed to ECR"
echo ""

# Step 4: Create IAM Role (if needed)
echo "Step 4: Setting up IAM role..."
ROLE_NAME="AppRunnerECRAccessRole"

if ! aws_cli iam get-role --role-name $ROLE_NAME > /dev/null 2>&1; then
    # Create role with inline policy document
    TRUST_POLICY='{"Version":"2012-10-17","Statement":[{"Effect":"Allow","Principal":{"Service":"build.apprunner.amazonaws.com"},"Action":"sts:AssumeRole"}]}'
    
    aws_cli iam create-role \
        --role-name $ROLE_NAME \
        --assume-role-policy-document "$TRUST_POLICY"

    aws_cli iam attach-role-policy \
        --role-name $ROLE_NAME \
        --policy-arn arn:aws:iam::aws:policy/service-role/AWSAppRunnerServicePolicyForECRAccess
    
    echo "   IAM role created"
else
    echo "   IAM role already exists"
fi

ROLE_ARN=$(aws_cli iam get-role --role-name $ROLE_NAME --query 'Role.Arn' --output text)
echo "   Role ARN: $ROLE_ARN"
echo ""

# Step 5: Create/Update App Runner Service
echo "Step 5: Deploying to App Runner..."

SERVICE_ARN=$(aws_cli apprunner list-services --region $AWS_REGION \
    --query "ServiceSummaryList[?ServiceName=='$APP_RUNNER_SERVICE_NAME'].ServiceArn" \
    --output text 2>/dev/null || echo "")

if [ -z "$SERVICE_ARN" ]; then
    echo "   Creating new App Runner service..."
    
    # Read environment variables from .env.aws if exists
    ENV_VARS='{"NODE_ENV":"production","APP_ENV":"production"}'
    if [ -f ".env.aws" ]; then
        echo "   Loading environment variables from .env.aws"
    fi
    
    SERVICE_ARN=$(aws_cli apprunner create-service \
        --service-name $APP_RUNNER_SERVICE_NAME \
        --source-configuration "{
            \"ImageRepository\": {
                \"ImageIdentifier\": \"$ECR_URI:latest\",
                \"ImageRepositoryType\": \"ECR\",
                \"ImageConfiguration\": {
                    \"Port\": \"80\",
                    \"RuntimeEnvironmentVariables\": $ENV_VARS
                }
            },
            \"AuthenticationConfiguration\": {
                \"AccessRoleArn\": \"$ROLE_ARN\"
            },
            \"AutoDeploymentsEnabled\": true
        }" \
        --instance-configuration "{
            \"Cpu\": \"$APP_RUNNER_CPU\",
            \"Memory\": \"$APP_RUNNER_MEMORY\"
        }" \
        --health-check-configuration '{
            "Protocol": "HTTP",
            "Path": "/api/health",
            "Interval": 10,
            "Timeout": 5,
            "HealthyThreshold": 1,
            "UnhealthyThreshold": 5
        }' \
        --region $AWS_REGION \
        --query 'Service.ServiceArn' \
        --output text)
    
    echo "   Service created: $SERVICE_ARN"
else
    echo "   Updating existing App Runner service..."
    aws_cli apprunner update-service \
        --service-arn $SERVICE_ARN \
        --source-configuration "{
            \"ImageRepository\": {
                \"ImageIdentifier\": \"$ECR_URI:latest\",
                \"ImageRepositoryType\": \"ECR\"
            }
        }" \
        --region $AWS_REGION > /dev/null
    
    echo "   Service updated: $SERVICE_ARN"
fi

echo ""
echo "=========================================="
echo "Deployment Complete!"
echo "=========================================="
echo ""
echo "Service Details:"
echo "   Service ARN: $SERVICE_ARN"
echo ""
echo "Get your service URL:"
echo "   aws apprunner describe-service --service-arn $SERVICE_ARN --region $AWS_REGION --query 'Service.ServiceUrl' --output text"
echo ""
echo "Check deployment status:"
echo "   aws apprunner describe-service --service-arn $SERVICE_ARN --region $AWS_REGION"
echo ""
echo "Next Steps:"
echo "   1. Configure environment variables in AWS App Runner console"
echo "   2. Set up RDS MySQL database"
echo "   3. Configure ElastiCache Redis (optional)"
echo "   4. Set up custom domain"
echo ""
