Vagrant.configure("2") do |config|
  config.vm.box = "trusty"
  config.vm.box_url = "https://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-i386-vagrant-disk1.box"
  config.vm.provision :shell, :path => "bootstrap.sh"
  config.vm.network :forwarded_port, host: 8080, guest: 80
  config.vm.host_name = "bot.local"
  config.vm.synced_folder ".", "/vagrant", :mount_options => ['dmode=774','fmode=775']
  config.vm.synced_folder "www/", "/var/www/html/"
end
