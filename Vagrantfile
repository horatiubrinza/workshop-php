# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.box = "ubuntu/trusty64"

    # Forward ports to Apache and MySQL
    config.vm.network "forwarded_port", guest: 80, host: 8888
    config.vm.network "forwarded_port", guest: 3306, host: 8889

	config.vm.provision "shell", path: "provision.sh"

    config.vm.provider :virtualbox do |vb|
        # changes the VM's name
        vb.customize ["modifyvm", :id, "--name", "workshop-php"]

        # cpus option specifies the maximum nr of CPUs that the virtual machine can have
        vb.customize ["modifyvm", :id, "--cpus", "2"]

        vb.customize ["modifyvm", :id, "--memory", "1024"]

        vb.customize ["modifyvm", :id, "--ioapic", "on"]
        vb.customize ["modifyvm", :id, "--cpuexecutioncap", "100"]
    end
end
