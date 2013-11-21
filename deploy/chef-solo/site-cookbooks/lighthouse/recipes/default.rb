# encoding: utf-8

#############################################
# admin
#############################################
package "vim"
package "htop"

#############################################
# users
#############################################
user "watchman" do
  :create
  password "$1$Cgi1/uGL$zUZsBGlvxDjrX0YpahCvq/"
  shell "/bin/bash"
  home "/home/watchman"
end

directory "/home/watchman" do
  action :create
  owner "watchman"
  group "watchman"
end

cookbook_file "sudo_watchman" do
  path "/etc/sudoers.d/watchman"
  owner "root"
  group "root"
  mode 0440
end

#############################################
# system
#############################################
package "git"

directory "/home/watchman/.ssh" do
  action :create
  owner "watchman"
  group "watchman"
end

cookbook_file "ssh_rsa_privite_key" do
  path "/home/watchman/.ssh/id_rsa"
  owner "watchman"
  group "watchman"
end

cookbook_file "authorized_keys" do
  path "/home/watchman/.ssh/authorized_keys"
  owner "watchman"
  group "watchman"
end

#############################################
# php
#############################################
package "make"

php_pear "mongo" do
  action :install
end
