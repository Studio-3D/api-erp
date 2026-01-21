# Quick AWS Deployment Steps

## Step 1: Create IAM User (Do this in AWS Console)

1. In AWS Console search bar, type **IAM** and click **IAM**
2. Click **Users** (left sidebar)
3. Click **Create user** button
4. **User name**: `immogestion-deploy`
5. Click **Next**
6. Select **Attach policies directly**
7. Search and select these policies:
   - `AmazonEC2ContainerRegistryFullAccess`
   - `AWSAppRunnerFullAccess`
   - `IAMFullAccess`
8. Click **Next** → **Create user**
9. Click on the user you just created
10. Go to **Security credentials** tab
11. Click **Create access key**
12. Choose **Command Line Interface (CLI)**
13. Check "I understand" → Click **Next**
14. Click **Create access key**
15. **IMPORTANT**: Copy both keys now!
    - Access key ID: `AKIA...`
    - Secret access key: `wJalrXUtnFEMI...` (you won't see this again!)

## Step 2: Set Environment Variables (Do this in Terminal)

Open PowerShell or Git Bash and run:

```bash
# Windows PowerShell
$env:AWS_ACCESS_KEY_ID="paste_your_access_key_here"
$env:AWS_SECRET_ACCESS_KEY="paste_your_secret_key_here"
$env:AWS_DEFAULT_REGION="eu-north-1"

# Verify it worked
echo $env:AWS_ACCESS_KEY_ID
```

OR in Git Bash:
```bash
export AWS_ACCESS_KEY_ID="paste_your_access_key_here"
export AWS_SECRET_ACCESS_KEY="paste_your_secret_key_here"
export AWS_DEFAULT_REGION="eu-north-1"

# Verify
echo $AWS_ACCESS_KEY_ID
```

## Step 3: Run Deployment

```bash
cd /c/xampp/htdocs/dashboard/api_0.1
bash aws-deploy.sh
```

This will:
- Create ECR repository
- Build Docker image
- Push to AWS
- Create App Runner service
- Deploy your API

---

**After deployment completes, you'll get your API URL like:**
`https://xxxxxxxx.eu-north-1.awsapprunner.com`

Update your Vercel frontend to use this URL!
