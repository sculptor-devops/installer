doctl compute droplet create sculptor.ubuntu18 --size s-1vcpu-1gb --image ubuntu-18-04-x64 --region fra1 --ssh-keys $1

# doctl compute droplet create sculptor.ubuntu20 --size s-1vcpu-1gb --image ubuntu-20-04-x64 --region fra1 --ssh-keys $1

sleep 20

doctl compute droplet list
