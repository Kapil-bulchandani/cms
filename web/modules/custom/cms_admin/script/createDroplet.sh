#!/bin/bash
# Get parameters
while getopts "size:region:name:" opt; do
  case $opt in
    size) size="$OPTARG"
    ;;
    region) region="$OPTARG"
    ;;
    name) name="$OPTARG"
    ;;
    \?) echo "Invalid option -$OPTARG" >&2
    ;;
  esac
done

# Create a droplet
doctl compute droplet create --image 130435855 --size "$size" --region "$region" "$name" --tag-name cms:env --wait --format 'ID, Name, PublicIPv4'

# Import ssh key
doctl compute ssh-key import my-ssh-key --public-key-file ~/.ssh/id_rsa.pub

# Get droplet IP Address
droplet_ip=$(doctl compute droplet list example.com --format PublicIPv4 --no-header)

# Wait for the ssh to be available
until ssh -o "StrictHostKeyChecking=no" root@"$droplet_ip" "echo 2>&1" >/dev/null; do
  slep 1
done

# SSH into droplet and run command
# ssh -o "StrictHostKeyChecking=no" root@"$droplet_ip"
