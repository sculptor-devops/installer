php ../../installer app:build --build-version=dev

scp ../../builds/installer root@$1:/root

scp ../setup_dev.sh root@$1:/root

ssh root@$1

# doctl compute ssh $2 --ssh-command "sh setup_dev.sh"

# doctl compute ssh $2 --ssh-command "./installer run"

# doctl compute droplet delete $2
