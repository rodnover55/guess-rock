# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.synced_folder ".", "/home/vagrant/project"
  config.vm.network :private_network, ip: "10.2.2.15"
  config.vm.provider "virtualbox" do |vb|
    vb.memory = "1024"
  end
  config.vm.provision "shell", inline: <<-SHELL
    (which docker && which docker-compose) || sudo apt-get update
    which curl || sudo apt-get install -y curl
    which docker || curl https://get.docker.com/ | sudo bash 
    which docker-compose || curl -L https://github.com/docker/compose/releases/download/1.5.2/docker-compose-`uname -s`-`uname -m` | sudo tee /usr/local/bin/docker-compose > /dev/null
    sudo chmod +x /usr/local/bin/docker-compose
    cd /home/vagrant/project
   docker-compose up -d
  SHELL
end
