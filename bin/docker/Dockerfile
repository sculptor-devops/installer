FROM ubuntu:18.04
MAINTAINER Alessandro Cappellozza <alessandro.cappellozza@gmail.com>

ADD installer /root/installer
ADD setup.sh /root/setup.sh
ADD installer.yml /root/installer.yml

RUN cd /root && sh /root/setup.sh
