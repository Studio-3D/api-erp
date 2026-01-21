# Laravel API AWS Deployment Guide

Complete guide for deploying your Laravel API to AWS using containerized approach.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Prerequisites](#prerequisites)
3. [Quick Start](#quick-start)
4. [AWS Services Setup](#aws-services-setup)
5. [Environment Configuration](#environment-configuration)
6. [Deployment](#deployment)
7. [CI/CD Setup](#cicd-setup)
8. [Monitoring](#monitoring)
9. [Troubleshooting](#troubleshooting)

---

## Architecture Overview

```
┌─────────────────┐
│  Vercel (Front) │
└────────┬────────┘
         │ HTTPS
         ▼
┌─────────────────┐         ┌──────────────┐
│  App Runner     │────────▶│  RDS MySQL   │
│  (Laravel API)  │         │  (Database)  │
└────────┬────────┘         └──────────────┘
         │                   
         │                  ┌──────────────┐
         ├─────────────────▶│ ElastiCache  │
         │                  │   (Redis)    │
         │                  └──────────────┘
         │                   
         │                  ┌──────────────┐
         └─────────────────▶│  S3 Bucket   │
                            │  (Storage)   │
                            └──────────────┘
```

**Components:**
- **App Runner**: Fully managed container service for Laravel
- **RDS MySQL**: Managed database (supports 2 databases: erp_db & espace_client)
- **ElastiCache Redis**: Caching, sessions, queues
- **S3**: File storage
- **ECR**: Private Docker registry
- **CloudWatch**: Logs and monitoring

---

## Prerequisites

### Local Requirements
- Docker Desktop installed and running
- Git installed
- AWS Account with appropriate permissions

### AWS Requirements
- IAM user with permissions for:
  - ECR (push/pull images)
  - App Runner (create/update services)
  - RDS (create/manage databases)
  - ElastiCache (create/manage Redis)
  - S3 (create/manage buckets)
  - IAM (create roles)

---

## Quick Start

### Option 1: Automated Deployment Script

```bash
cd api_0.1

# Set AWS credentials (Windows PowerShell)
$env:AWS_ACCESS_KEY_ID="your_access_key"
$env:AWS_SECRET_ACCESS_KEY="your_secret_key"
$env:AWS_DEFAULT_REGION="us-east-1"

# Run deployment script
bash aws-deploy.sh
```

### Option 2: Manual Step-by-Step

Continue reading for detailed manual setup instructions.

---

## AWS Services Setup

### 1. Create RDS MySQL Database

**Via AWS Console:**

1. Go to **RDS Console** → **Create database**
2. Choose **MySQL** (version 8.0+)
3. Select **Production** template
4. **DB instance identifier**: `laravel-api-db`
5. **Master username**: `admin`
6. **Master password**: (create strong password)
7. **DB instance class**: `db.t3.micro` (free tier) or `db.t3.small`
8. **Storage**: 20 GB GP3, enable auto-scaling
9. **Connectivity**:
   - VPC: Default VPC
   - Public access: **Yes** (for initial setup)
   - Security group: Create new, allow MySQL (3306) from anywhere (or your IP)
10. **Database authentication**: Password authentication
11. Create database

**Create databases:**
```sql
CREATE DATABASE erp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE espace_client CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Save these details:**
- Endpoint: `laravel-api-db.xxxxx.us-east-1.rds.amazonaws.com`
- Port: `3306`
- Username: `admin`
- Password: (your password)

### 2. Create ElastiCache Redis (Optional but Recommended)

**Via AWS Console:**

1. Go to **ElastiCache Console** → **Create**
2. Choose **Redis**
3. **Cluster mode**: Disabled
4. **Name**: `laravel-api-cache`
5. **Node type**: `cache.t3.micro`
6. **Number of replicas**: 0 (for dev) or 1+ (for prod)
7. **Subnet group**: Default
8. **Security group**: Allow Redis (6379) from App Runner
9. Create cluster

**Save endpoint:** `laravel-api-cache.xxxxx.cache.amazonaws.com:6379`

### 3. Create S3 Bucket for File Storage

**Via AWS Console:**

1. Go to **S3 Console** → **Create bucket**
2. **Bucket name**: `laravel-api-storage-{unique-id}`
3. **Region**: `us-east-1`
4. **Block all public access**: Uncheck (if you need public file access)
5. **Bucket Versioning**: Enable (optional)
6. Create bucket

**Configure CORS** (for file uploads from frontend):
```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
        "AllowedOrigins": ["https://frontend-0-2.vercel.app"],
        "ExposeHeaders": ["ETag"]
    }
]
```

### 4. Create IAM User for Laravel

**Via IAM Console:**

1. Create user: `laravel-api-user`
2. Attach policies:
   - `AmazonS3FullAccess` (or custom S3 policy)
   - `AmazonSESFullAccess` (if using email)
3. Create access key → Save credentials

---

## Environment Configuration

### 1. Create `.env.aws` file

Copy `.env.aws.example` and fill in your AWS values:

```bash
cp .env.aws.example .env.aws
```

**Edit `.env.aws`:**

```env
APP_KEY=base64:... # Generate with: php artisan key:generate --show

# Database (RDS)
DB_HOST=laravel-api-db.xxxxx.us-east-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=erp_db
DB_USERNAME=admin
DB_PASSWORD=your_rds_password

DB_HOST_CLIENT=laravel-api-db.xxxxx.us-east-1.rds.amazonaws.com
DB_DATABASE_CLIENT=espace_client

# Redis (ElastiCache)
REDIS_HOST=laravel-api-cache.xxxxx.cache.amazonaws.com
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# S3
AWS_ACCESS_KEY_ID=your_iam_access_key
AWS_SECRET_ACCESS_KEY=your_iam_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=laravel-api-storage-xxxxx
FILESYSTEM_DISK=s3

# Frontend
FRONTEND_URL=https://frontend-0-2.vercel.app
```

### 2. Test Docker Build Locally

```bash
# Build the AWS-optimized image
docker build -f Dockerfile.aws -t laravel-api:test .

# Test run (with your .env.aws values)
docker run --rm -p 8080:80 --env-file .env.aws laravel-api:test
```

Visit `http://localhost:8080/api` to verify it works.

---

## Deployment

### Step 1: Build and Push to ECR

```bash
# Login to AWS
export AWS_ACCESS_KEY_ID=your_key
export AWS_SECRET_ACCESS_KEY=your_secret
export AWS_DEFAULT_REGION=us-east-1

# Create ECR repository
aws ecr create-repository --repository-name laravel-api --region us-east-1

# Get ECR URI
ECR_URI=$(aws ecr describe-repositories --repository-names laravel-api --region us-east-1 --query 'repositories[0].repositoryUri' --output text)
echo $ECR_URI

# Build and push
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin $ECR_URI
docker build -f Dockerfile.aws -t laravel-api:latest .
docker tag laravel-api:latest $ECR_URI:latest
docker push $ECR_URI:latest
```

### Step 2: Create App Runner Service

**Via AWS Console:**

1. Go to **App Runner Console** → **Create service**
2. **Source**: Container registry → Amazon ECR
3. Select your repository: `laravel-api`
4. Tag: `latest`
5. **Deployment trigger**: Automatic
6. **ECR access role**: Create new or use existing
7. **Service settings**:
   - Service name: `laravel-api`
   - CPU: 2 vCPU
   - Memory: 4 GB
   - Port: 80
8. **Environment variables**: Add all from `.env.aws`
9. **Health check**: Path `/api/health`
10. Create & deploy

**Via AWS CLI:**

```bash
# Create IAM role first
aws iam create-role --role-name AppRunnerECRAccessRole --assume-role-policy-document '{...}'
aws iam attach-role-policy --role-name AppRunnerECRAccessRole --policy-arn arn:aws:iam::aws:policy/service-role/AWSAppRunnerServicePolicyForECRAccess

# Get role ARN
ROLE_ARN=$(aws iam get-role --role-name AppRunnerECRAccessRole --query 'Role.Arn' --output text)

# Create service
aws apprunner create-service \
  --service-name laravel-api \
  --source-configuration file://apprunner-config.json \
  --instance-configuration Cpu=2048,Memory=4096 \
  --region us-east-1
```

### Step 3: Configure Environment Variables in App Runner

In App Runner console → Your service → Configuration → Environment variables:

Add all variables from `.env.aws`, including:
- All DB_* variables
- REDIS_HOST
- AWS_* credentials
- APP_KEY
- RUN_MIGRATIONS=true (first deployment only)

### Step 4: Deploy

App Runner will automatically deploy when you push new images to ECR with the `:latest` tag.

---

## CI/CD Setup

### GitHub Actions (Automated Deployment)

1. **Add GitHub Secrets:**
   - Go to GitHub repo → Settings → Secrets → Actions
   - Add:
     - `AWS_ACCESS_KEY_ID`
     - `AWS_SECRET_ACCESS_KEY`
     - `APP_RUNNER_SERVICE_ARN` (get from App Runner console)

2. **Push code to trigger deployment:**
```bash
git add .
git commit -m "Deploy to AWS"
git push origin main
```

The workflow in `.github/workflows/deploy-api-aws.yml` will:
- Build Docker image
- Push to ECR
- Trigger App Runner deployment

---

## Monitoring

### CloudWatch Logs

```bash
# Get log streams
aws logs describe-log-streams \
  --log-group-name /aws/apprunner/laravel-api/<service-id>/application \
  --region us-east-1

# Tail logs
aws logs tail /aws/apprunner/laravel-api/<service-id>/application \
  --follow --region us-east-1
```

### Metrics

- **AWS Console** → **App Runner** → Your service → **Metrics**
- View: Requests, CPU, Memory, Response time, Errors

### Health Check

Add a health endpoint in Laravel:

```php
// routes/api.php
Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});
```

---

## Troubleshooting

### Issue: App doesn't start

**Check logs:**
```bash
aws logs tail /aws/apprunner/.../application --follow
```

**Common causes:**
- Missing environment variables
- Database connection failure
- Invalid APP_KEY

### Issue: Database connection failed

**Solutions:**
1. Check RDS security group allows connections
2. Verify DB credentials in environment variables
3. Test connection:
```bash
docker run --rm -it mysql:8 mysql -h your-rds-endpoint -u admin -p
```

### Issue: 500 errors

**Check:**
- Storage permissions
- APP_KEY is set
- All required env variables are present
- Logs in CloudWatch

### Issue: Slow performance

**Solutions:**
- Enable Redis for caching/sessions
- Increase App Runner CPU/Memory
- Optimize database queries
- Enable CloudFront CDN

---

## Cost Estimation

**Monthly AWS costs (approximate):**

| Service | Configuration | Cost |
|---------|--------------|------|
| App Runner | 2 vCPU, 4 GB, 100 requests/min | ~$50-80 |
| RDS MySQL | db.t3.small, 20 GB | ~$25-35 |
| ElastiCache Redis | cache.t3.micro | ~$12-15 |
| S3 | 10 GB storage, 1000 requests | ~$1-2 |
| ECR | 5 GB images | ~$0.50 |
| **Total** | | **~$90-135/month** |

**Free tier eligible:**
- RDS: 750 hours/month (db.t3.micro)
- ElastiCache: 750 hours/month (cache.t3.micro)

---

## Next Steps

1. Set up custom domain in App Runner
2. Configure SSL certificate
3. Set up CloudWatch alarms
4. Implement database backups
5. Configure auto-scaling
6. Set up staging environment

---

**Support:**
- [AWS App Runner Docs](https://docs.aws.amazon.com/apprunner/)
- [Laravel Deployment](https://laravel.com/docs/deployment)

**Last updated:** January 16, 2026
