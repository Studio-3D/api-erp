
#!/bin/bash

sudo yum install -y certbot python3-certbot-nginx

sudo certbot --nginx \
  --non-interactive \
  --agree-tos \
  --email studio3d.deploy@gmail.com \
  -d api-dev.immogestion.online

sudo systemctl enable certbot-renew.timer
