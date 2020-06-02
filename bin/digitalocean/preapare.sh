sudo snap install doctl

doctl auth init

sudo snap connect doctl:ssh-keys

doctl compute ssh-key list
