doctl compute droplet create test --size s-1vcpu-1gb --image ubuntu-18-04-x64 --region fra1 --ssh-keys $1

sleep 20

doctl compute droplet list
