php ../../installer app:build --build-version=dev
cp /home/alessandro/dev/scuptor/installer/builds/installer .
sudo docker build . --no-cache
