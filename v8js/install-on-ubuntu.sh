# Guys/gals... This took >4 hours to get right.
#
# Here are some troubleshooting checklist in case you're getting errors:
#
# - Make sure you have no other v8 libraries lying around your system. Get rid of those first!
# - Run apt-get update and upgrade before running this. Obviously...
# - I got v8js-2.0.0 working against 6.4.388.18. Never got it to work against v8-7.x...
# - Don't even try apt-get install libv8-7.2. Lost hours, couldn't get it to work... Just compile yourself...

# Update & Upgrade packages
apt-get update
apt-get upgrade

# Install required dependencies
apt-get install build-essential curl git python libglib2.0-dev

cd /tmp

# Install depot_tools first (needed for source checkout)
git clone https://chromium.googlesource.com/chromium/tools/depot_tools.git
export PATH=`pwd`/depot_tools:"$PATH"

# Download v8
fetch v8
cd v8

# I needed this to make it work with PHP >=7.0
git checkout 6.4.388.18
gclient sync

# Setup GN
tools/dev/v8gen.py -vv x64.release -- is_component_build=true

# Build
ninja -C out.gn/x64.release/

# Move libraries to necessary location
cp out.gn/x64.release/lib*.so /usr/lib/
cp out.gn/x64.release/*_blob.bin /usr/lib
cp out.gn/x64.release/icudtl.dat /usr/lib
cp -R include/* /usr/include

cd out.gn/x64.release/obj
ar rcsDT libv8_libplatform.a v8_libplatform/*.o

# Are you getting v8 (library) not found error? Try this before pecl install:
apt-get install patchelf 
for A in /usr/lib/*.so; do patchelf --set-rpath '$ORIGIN' $A;done

# Then let's pull v8js.
cd /tmp
git clone https://github.com/phpv8/v8js.git
cd v8js
# Checkout version 2.1.0
git checkout 2.1.0
phpize
# This flag is important!
./configure LDFLAGS="-lstdc++" --with-v8js=/usr
make clean
make
# Make sure that the tests pass...
make test
make install

# DON'T FORGET TO Add
# extension=v8js.so
# To your php.ini as needed.
# You can check if the module is there by doing php -m | grep v8js