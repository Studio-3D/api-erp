# GitHub Actions Auto-Deployment Setup

## ✅ GitHub Actions workflow created!

Your API will now automatically deploy to AWS ECS whenever you push to GitHub.

## 📋 One-Time Setup Required

### Step 1: Add GitHub Secrets

Go to your GitHub repository:
1. Click **Settings** → **Secrets and variables** → **Actions**
2. Click **New repository secret**
3. Add these two secrets:

**Secret 1:**
- Name: `AWS_ACCESS_KEY_ID`
- Value: `<your-aws-access-key-id>`

**Secret 2:**
- Name: `AWS_SECRET_ACCESS_KEY`
- Value: `<your-aws-secret-access-key>`

### Step 2: Push the workflow file to GitHub

```bash
cd /c/xampp/htdocs/dashboard/api_0.1
git add .github/workflows/deploy-to-aws.yml
git commit -m "Add GitHub Actions auto-deployment"
git push
```

## 🚀 How It Works

From now on, whenever you:
```bash
git add .
git commit -m "your changes"
git push
```

GitHub Actions will automatically:
1. ✅ Build your Docker image
2. ✅ Push to AWS ECR
3. ✅ Update your ECS service
4. ✅ Wait for deployment to complete
5. ✅ Notify you if successful

## 📊 Monitor Deployments

- Go to your GitHub repo → **Actions** tab
- You'll see all deployments and their status
- Click on any deployment to see detailed logs

## 🔧 Manual Trigger

You can also manually trigger deployment from GitHub:
- Go to **Actions** tab
- Click **Deploy to AWS ECS** workflow
- Click **Run workflow**

## ⚠️ Important Notes

- The workflow triggers on pushes to `main` or `master` branch
- First deployment after setup will take 3-5 minutes
- Subsequent deployments are faster (uses cached layers)
- You can view deployment progress in GitHub Actions tab

## 🎯 Next Steps

1. Add the GitHub Secrets (see Step 1 above)
2. Push the workflow file (see Step 2 above)
3. Make any code change and push
4. Watch the magic happen! ✨

Your deployment is now fully automated!
